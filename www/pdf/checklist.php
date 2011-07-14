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

var $border = 0;				// Layout debugging
var $height = 3.3;				// Line spacing

function Header()
{
	$logo  = 'logo.png';
	$title = 'Checklist';
	$date  = strftime ("%d %B %Y");

	$this->Image ($logo, 5, 5, 17);
	$this->SetFont ('Arial', 'B', 20);
	$this->SetXY (23, 9);
	$this->Write (0, $title);
	$this->SetFont ('Arial', '', 10);
	$this->Cell (90, 0, $date, 0, 0, 'R');

	$this->Image ($logo, 153, 5, 17);
	$this->SetFont ('Arial', 'B', 20);
	$this->SetXY (171, 9);
	$this->Write (0, $title);
	$this->SetFont ('Arial', '', 10);
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

function new_column()
{
	$this->SetCol ($this->col + 1);
	$this->SetY ($this->y0);
}

function add_route ($panel, $colour, $grade, $key)
{
	$h = $this->height;
	$b = $this->border;

	$this->SetFont ('Times', '', 9);
	$this->Cell (5, $h, $panel, $b, 0, 'R');
	$this->SetFont ('WingDing', '', 9);
	$this->SetTextColor (220, 220, 220);
	$this->Cell (4, $h, "R", $b);
	$this->SetFont ('Times', '', 9);
	$this->SetTextColor (0, 0, 0);
	$this->Cell (19, $h, $colour, $b);
	$this->Cell ( 7, $h, $grade, $b);
	$this->Cell ( 8, $h, $key, $b);
	$this->Ln();
	$y = $this->GetY();
	if ($y > 200)
		$this->new_column();
}

function print_grade ($str, $count)
{
	$y = $this->GetY();
	if ($y > 180)
		$this->new_column();

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "$str ");
	$this->SetFont ('Times', '', 6);
	$this->Write (3, "($count)");
	$this->SetFont ('Times', '', 9);
	$this->Ln(4);
}

function add_stats ($panels, $routes, $auto, $top, $lead, $height, $last)
{
	$h = $this->height;
	$b = $this->border;

	$this->SetY (145);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Key:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$this->Cell (3);
	$this->Cell (5, $h, "L ", $b, 0, 'R');
	$this->Cell (0, $h, "Lead Climb", $b, 1);
	$this->Cell (3);
	$this->Cell (5, $h, "N ", $b, 0, 'R');
	$this->Cell (0, $h, "New Route", $b, 1);
	$this->Cell (3);
	$this->Cell (5, $h, "! ", $b, 0, 'R');
	$this->Cell (0, $h, "Read the Route Notes", $b, 1);
	$this->Ln(3);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Glossary:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$this->Cell (3);
	$this->Cell (10, $h, "Arete:", $b);
	$this->Cell (0, $h, "Corner / Edge", $b, 1);
	$this->Cell (3);
	$this->Cell (10, $h, "Tufa: ", $b);
	$this->Cell (0, $h, "Long wibbly bits", $b, 1);
	$this->Ln(3);

	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Stats:");
	$this->Ln(3);
	$this->SetFont ('Times', '', 9);

	$height = sprintf ("%.0f", $height);
	$this->Cell (3);
	$this->Cell (0, $h, "$routes Routes ($panels Panels)", $b, 1);
	$this->Cell (3);
	$this->Cell (0, $h, "$auto Auto-Belays", $b, 1);
	$this->Cell (3);
	$this->Cell (0, $h, "$lead Leads", $b, 1);
	$this->Cell (3);
	$this->Cell (0, $h, "$top Top Ropes", $b, 1);
	$this->Ln(2);
	$this->Cell (3);
	$this->Cell (0, $h, "Total Route Height: {$height}m", $b, 1);
	$this->Cell (3);
	$this->Cell (0, $h, "Last Route Set: $last", $b, 1);

	$this->Ln(3);
	$this->SetTextColor (0, 0, 255);
	$this->SetFont ('Times', 'U', 9);
	$this->Cell (3);
	$this->Write ($h, 'http://craggy.russon.org', 'http://craggy.russon.org');
	$this->SetTextColor (0);
	$this->SetFont ('Times', '', 9);

	$this->Image ('rss.png', 287, 200, 5, 0, '', 'http://craggy.russon.org');

	$this->SetXY (101, 200);
	$this->SetFont ('Times', 'B', 10);
	$this->Write (3, "Key overleaf...");
	$this->SetFont ('Times', '', 9);
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
	$auto   = 0;
	$top    = 0;
	$lead   = 0;
	$height = 0;
	$last   = 0;

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
	$last = trim (strftime ("%e %b %Y", strtotime ($last)));

	$checklist = array (3 => array(), 4 => array(), 5 => array(), 6 => array(), 7 => array());
	while ($row = array_shift ($list)) {

		$gb = checklist_grade_block ($row['grade']);
		$checklist[$gb][] = $row;
	}

	//$pdf = new PDF ('P', 'mm', 'A5');
	$pdf = new PDF ('L', 'mm', 'A4');
	$pdf->SetDisplayMode ('fullwidth', 'single');
	//$pdf->SetTopMargin (0);
	$pdf->SetLeftMargin (5);
	$pdf->SetRightMargin (5);

	$pdf->AddPage();
	$pdf->Ln();
	$pdf->y0 = $pdf->GetY();
	$pdf->SetDrawColor (180);
	$pdf->SetLineWidth (0.1);
	$pdf->Line (51, 15, 51, 202);
	$pdf->Line (98, 15, 98, 202);

	$pdf->Line (199, 15, 199, 202);
	$pdf->Line (247, 15, 247, 202);

	/*
	// Draw central dividing line
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

		if (($gb == 6) || ($gb == 8))
			$pdf->new_column();

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

