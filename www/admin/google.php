<?php

include "../db.php";
include "../utils.php";

function login()
{
    include "../conf.php";

    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Http_Client');
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');

    $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
    $http = Zend_Gdata_ClientLogin::getHttpClient($google_user,$google_pass,$service);
    $client = new Zend_Gdata_Spreadsheets($http);

    return $client;
}

function getSpreadsheetId($client, $ss)
{
    $feed = $client->getSpreadsheetFeed();

    foreach($feed->entries as $entry)
    {
        if ($entry->title->text == $ss)
            return array_pop(explode("/",$entry->id->text));
    }

    return FALSE;
}

function getWorksheetId($client,$ss_id,$ws)
{
    $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
    $query->setSpreadsheetKey($ss_id);
    $feed = $client->getWorksheetFeed($query);

    foreach($feed->entries as $entry)
    {
        if ($entry->title->text == $ws)
            return array_pop(explode("/",$entry->id->text));
    }

    return FALSE;
}

function findRows($client, $spreadsheet, $worksheet, $search=FALSE)
{
    $query = new Zend_Gdata_Spreadsheets_ListQuery();

    $ss_id = getSpreadsheetId($client, $spreadsheet);
    $query->setSpreadsheetKey($ss_id);

    $ws_id = getWorksheetId($client,$ss_id,$worksheet);
    $query->setWorksheetId($ws_id);

    if ($search) $query->setSpreadsheetQuery($search);

    $feed = $client->getListFeed($query);

    return $feed;
}

function getRows($client, $spreadsheet, $worksheet, $search=FALSE)
{
    $rows = array();

    $feed = findRows($client, $spreadsheet, $worksheet, $search);
    if ($feed->entries)
    {
        foreach($feed->entries as $entry)
        {
            $row = array();

            $customRow = $entry->getCustom();
            foreach ($customRow as $customCol)
            {
                $row[$customCol->getColumnName()] = $customCol->getText();
            }

            $rows[] = $row;
        }
    }

    return $rows;
}

function google_main ($routes)
{
    $db_routes = array();
    foreach ($routes as $id => $r) {

        $row = array();

        $panel = "";
        if (array_key_exists('panel', $r))
            $panel = parse_panel($r['panel'], "id");
        $row['panel'] = $panel;

        $colour = "";
        if (array_key_exists('colour', $r))
            $colour = parse_colour2($r['colour'], "id");
        $row['colour'] = $colour;

        $grade = "";
        if (array_key_exists('grade', $r))
            $grade = parse_grade($r['grade'], "id");
        $row['grade'] = $grade;

        $notes = "";
        if (array_key_exists('notes', $r))
            $notes = $r['notes'];
        $row['notes'] = $notes;

        $setter = "";
        if (array_key_exists('setter', $r))
            $setter = parse_setter ($r['setter'], "id");
        $row['setter'] = $setter;

        $date_set = "";
        if (array_key_exists("dateset", $r))
            $date_set = db_date($r['dateset']);
        $row['date_set'] = $date_set;

        array_push ($db_routes, $row);
    }

    db_truncate_route();
    db_route_add2($db_routes);
    db_set_last_update();
}


date_default_timezone_set("UTC");

try {
    $client = login();

    $routes = array();

    $step = 50;
    $end = 90;
    for ($count = 0; $count < $end; $count += $step) {
        $search = sprintf ("panel>=%d AND panel<%d", $count, $count+$step);
        $rows = getRows($client, "routes", "Routes", $search);
        foreach ($rows as $r) {
            $r['dateset'] = implode ("/", array_reverse (explode ("/", $r['dateset'])));
            array_push ($routes, $r);
        }
    }

    db_get_database();

    $g_climbtypes = db_select ("climb_type");
    $g_colours    = db_select ("colour");
    $g_grades     = db_select ("grade");
    $g_panels     = db_select ("panel");
    $g_setters    = db_select ("setter");

    google_main($routes);

    printf ("Imported %d routes\n", count ($routes));

    exit (0);
} catch (Exception $e) {
    printf ("Something went wrong\n");
    exit (1);
}

