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
	$this->Cell (0, 0, $date, 0, 0, 'R');
	$this->Ln (6);
}

function SetCol ($col)
{
	// Set position at a given column
	$this->col = $col;
	$x = 5 + ($col * 48);
	$this->SetLeftMargin ($x);
	$this->SetX ($x);
	//printf ("x = %s\n", $x);
}

function AcceptPageBreak()
{
	return false;
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
	$this->SetFont ('Times', 'B', 9);
	$this->Write (3, "$str ");
	$this->SetFont ('Times', '', 6);
	$this->Write (3, "($count)");
	$this->SetFont ('Times', '', 9);
	$this->Ln(4);
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
	$columns = array ('id', 'panel', 'climb_type', 'colour', 'grade', 'grade_seq', 'notes', 'date_set');

	$list = db_select($table, $columns);

	usort($list, 'cmp_panel');

	process_key ($list);

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

	$pdf->Output();

}


date_default_timezone_set ('UTC');
checklist_main();

