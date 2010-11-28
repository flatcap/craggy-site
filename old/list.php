function list_main()
{
    $type = get_url_variable('type');

    $last_update = date ("j M Y", strtotime (db_get_last_update()));

    if (($type != "checklist") && ($type != "csv")) {
        $output  = html_header ("Craggy Routes");
        $output .= "<body>";
        $output .= "<div class='header'>Craggy Routes <span>(Last updated: $last_update)</span></div>\n";
        $output .= html_menu();
        $output .= "<div class='content'>\n";
    }

    $routes = db_get_routes();

    switch ($type) {
        case "6a":    $output .= list_6a($routes);    break;
        case "age":   $output .= list_age($routes);   break;
        case "grade": $output .= list_grade($routes); break;
        case "panel":
        default:      $output .= list_panel($routes); break;
    }

    $output .= "</div>";
    $output .= get_errors();
    $output .= "</body>";
    $output .= "</html>";

    return $output;
}

