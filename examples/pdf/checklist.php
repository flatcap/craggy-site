<?php

set_include_path ('/usr/share/php/fpdf');
require ('fpdf.php');

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

class PDF extends FPDF
{
var $col = 0;					// Current column
var $y0;					// Ordinate of column start

function Header()
{
	$this->Image ('logo.png', 5, 5, 17);
	$this->SetFont ('Arial', 'B', 20);
	$this->SetXY (23, 9);
	$this->Write (0, 'Checklist');
	$this->SetFont ('Arial', '', 10);
	$date = strftime ("%d %B %Y");
	$this->Cell (90, 0, $date, 0, 0, 'R');

	$this->Image ('logo.png', 153, 5, 17);
	$this->SetFont ('Arial', 'B', 20);
	$this->SetXY (171, 9);
	$this->Write (0, 'Checklist');
	$this->SetFont ('Arial', '', 10);
	$date = strftime ("%d %B %Y");
	$this->Cell (0, 0, $date, 0, 0, 'R');

	$this->Ln (6);
}

function AcceptPageBreak()
{
	return false;
}

function SetCol ($col)
{
	// Set position at a given column
	$this->col = $col;
	$x = 5 + ($col * 48);
	if ($col > 2)
		$x += 5;
	$this->SetLeftMargin ($x);
	$this->SetX ($x);
	//printf ("x = %s\n", $x);
}

function add_route ($panel, $colour, $grade, $key)
{
	$border = 0;
	$height = 3.3;
	$this->SetFont ('Times', '', 9);
	$this->Cell (5, $height, $panel, $border, 0, 'R');
	$this->SetFont ('WingDing', '', 9);
	$this->SetTextColor (220, 220, 220);
	$this->Cell (4, $height, "R", $border);
	$this->SetFont ('Times', '', 9);
	$this->SetTextColor (0, 0, 0);
	$this->Cell (19, $height, $colour, $border);
	$this->Cell ( 7, $height, $grade, $border);
	$this->Cell ( 8, $height, $key, $border);
	$this->Ln();
	$y = $this->GetY();
	if ($y > 200) {
		$this->SetCol ($this->col + 1);
		$this->SetY ($this->y0);
	}
}

function print_grade ($str, $count)
{
	$y = $this->GetY();
	if ($y > 180) {
		$this->SetCol ($this->col + 1);
		$this->SetY ($this->y0);
	}
	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "$str ");
	$this->SetFont ('Times', '', 6);
	$this->Write (3, "($count)");
	$this->SetFont ('Times', '', 9);
	$this->Ln(4);
}

function add_stats ($panels, $routes, $auto, $top, $lead, $wheight, $last)
{
	$border = 0;
	$height = 3.3;
	$this->SetY (145);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Key:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$this->Cell (8, $height, "L ", $border, 0, 'R');
	$this->Cell (0, $height, "Lead Climb", $border, 1);
	$this->Cell (8, $height, "N ", $border, 0, 'R');
	$this->Cell (0, $height, "New Route", $border, 1);
	$this->Cell (8, $height, "! ", $border, 0, 'R');
	$this->Cell (0, $height, "Read the Route Notes", $border, 1);
	$this->Ln(3);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Glossary:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$this->Cell (3);
	$this->Cell (10, $height, "Arete:", $border);
	$this->Cell (0, $height, "Corner / Edge", $border, 1);
	$this->Cell (3);
	$this->Cell (10, $height, "Tufa: ", $border);
	$this->Cell (0, $height, "Long wibbly bits", $border, 1);
	$this->Ln(3);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Stats:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$this->Cell (3);
	$this->Cell (0, $height, "$routes Routes ($panels Panels)", $border, 1);
	$this->Cell (3);
	$this->Cell (0, $height, "Auto-Belay: $auto", $border, 1);
	$this->Cell (3);
	$this->Cell (0, $height, "Top Rope: $top", $border, 1);
	$this->Cell (3);
	$this->Cell (0, $height, "Lead: $lead", $border, 1);
	$this->Ln(2);
	$this->Cell (3);
	$this->Cell (0, $height, "Total Route Height: {$wheight}m", $border, 1);
	$this->Cell (3);
	$this->Cell (0, $height, "Last Route Set: $last", $border, 1);

	$this->Ln(3);
	$this->SetTextColor (0, 0, 255);
	$this->SetFont ('Times', 'U', 9);
	$this->Write ($height, 'http://craggy.russon.org', 'http://craggy.russon.org');
	$this->SetTextColor (0);
	$this->SetFont ('Times', '', 9);

	$this->Image ('rss.png', 287, 200, 5, 0, '', 'http://craggy.russon.org');
}

}

function checklist_grade_block($grade)
{
	if ($grade[0] < '6')
		return $grade[0];

	$g = substr($grade, 0, 2);
	switch ($g) {
		case '6a': return 6;
		case '6b': return 7;
		default:   return 8;
	}
}

function checklist_main ()
{
	include 'db_names.php';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'climb_type', 'colour', 'grade', 'grade_seq', 'notes', 'date_set', 'height');

	$list = db_select($table, $columns);

	usort($list, 'cmp_panel');

	process_key ($list);

	$panels = 0;
	$routes = 0;
	$auto = 0;
	$top = 0;
	$lead = 0;
	$height = 0;
	$last = 0;

	$old = 0;
	foreach ($list as $l) {
		if ($l['panel'] != $old) {
			$old = $l['panel'];
			$panels++;
		}
		$routes++;
		switch ($l['climb_type']) {
			case 'Auto-belay':	$auto++;	break;
			case 'Lead':		$lead++;	break;
			case 'Top Rope':
			default:		$top++;		break;
		}
		$height += $l['height'];

		if ($l['date_set'] > $last) {
			$last = $l['date_set'];
		}
	}
	$height /= 100;
	$last = strftime ("%d %b %Y", strtotime ($last));

	$checklist = array (3 => array(), 4 => array(), 5 => array(), 6 => array(), 7 => array());
	while ($row = array_shift ($list)) {

		$gb = checklist_grade_block ($row['grade']);
		$checklist[$gb][] = $row;
	}

	//$pdf = new PDF ('P', 'mm', 'A5');
	$pdf = new PDF ('L', 'mm', 'A4');
	//$pdf->SetDisplayMode ('fullpage', 'single');
	//$pdf->SetTopMargin (0);
	$pdf->SetLeftMargin (5);
	$pdf->SetRightMargin (5);

	$pdf->AddPage();
	$pdf->Ln();
	$pdf->y0 = $pdf->GetY();
	$pdf->SetDrawColor (180);
	$pdf->SetLineWidth (0.2);
	$pdf->Line (51, 15, 51, 202);
	$pdf->Line (98, 15, 98, 202);

	/*
	$pdf->SetLineWidth (0.5);
	$pdf->SetDrawColor (0);
	$pdf->Line (148, 15, 148, 202);
	*/

	$pdf->SetDrawColor (0);
	$pdf->SetLineWidth (0.2);

	$pdf->SetTitle ('Craggy Checklist');
	$pdf->SetCreator ('Richard Russon');
	$pdf->SetAuthor ('Richard Russon');
	$pdf->SetSubject ('Craggy Routes');
	$pdf->AddFont('WingDing','','wingding.php');
	$pdf->SetCol (0);

	$titles = array (3 => 'Grade 3', 4 => 'Grade 4', 5 => 'Grade 5', 6 => 'Grade 6a', 7 => 'Grade 6b', 8 => 'Grade 6c...');
	$columns = array('panel', 'colour', 'grade', 'key');
	foreach ($checklist as $gb => $list) {

		$title = $titles[$gb];
		$count = count ($list);

		if (($gb == 6) || ($gb == 8)) {
			$pdf->SetCol ($pdf->col + 1);
			$pdf->SetY ($pdf->y0);
		}
		$pdf->print_grade ($title, $count);

		foreach ($list as $l) {
			$pdf->add_route ( $l['panel'], $l['colour'], $l['grade'], $l['key']);
		}
		$pdf->Ln(4);
	}

	$pdf->add_stats ($panels, $routes, $auto, $top, $lead, $height, $last);
	$pdf->Output();

}


date_default_timezone_set ('UTC');
checklist_main();

