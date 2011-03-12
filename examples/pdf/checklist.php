<?php

set_include_path ('/usr/share/php/fpdf');
require ('fpdf.php');

class PDF extends FPDF
{
var $col = 0;					// Current column
var $y0;					// Ordinate of column start

function Header()
{
	$this->Image ('logo.png', 4, 4, 20);
	$this->SetFont ('Arial', 'B', 20);
	$this->Cell (16, 0);
	$this->Cell (0, 0, 'Checklist', 0, 0, 'L');
	$this->SetFont ('Arial', '', 10);
	$date = strftime ("%d %B %Y");
	$this->Cell (5, 0, $date, 0, 0, 'R');
	$this->Ln (6);
}

function SetCol ($col)
{
	// Set position at a given column
	$this->col = $col;
	$x = 5 + ($col * 50);
	$this->SetLeftMargin ($x);
	$this->SetX ($x);
	//printf ("x = %s\n", $x);
}

function AcceptPageBreak()
{
	// Method accepting or not automatic page break
	if ($this->col < 2) {
		$this->SetCol ($this->col + 1);	// Go to next column
		$this->SetY ($this->y0);	// Set ordinate to top
		return false;			// Keep on page
	} else {
		$this->SetCol (0);		// Go back to first column
		return true;			// Page break
	}
}

function add_route ($panel, $colour, $grade, $key)
{
	$border = 0;
	$this->SetFont ('Times', '', 9);
	$this->Cell (5, 3, $panel, $border, 0, 'R');
	$this->SetFont ('WingDing', '', 9);
	$this->SetTextColor (220, 220, 220);
	$this->Cell (4, 3, "R", $border);
	$this->SetFont ('Times', '', 9);
	$this->SetTextColor (0, 0, 0);
	$this->Cell (20, 3, $colour, $border);
	$this->Cell ( 8, 3, $grade, $border);
	$this->Ln();
}

function PrintChapter ($file)
{
	// Add chapter
	//$this->AddPage();
	//$this->Ln();
	$this->y0 = $this->GetY();

	$f = fopen ($file, 'r');
	$txt = fread ($f, filesize ($file));
	fclose ($f);

	$lines = explode ("\n", $txt);
	foreach ($lines as $l) {
		$parts = explode ("\t", $l);
		$count = count ($parts);
		if ($count < 3)
			continue;
		$panel  = $parts[0];
		$colour = $parts[1];
		$grade  = $parts[2];
		$key    = ($count > 3) ? $parts[3] : "";
		$this->add_route ($panel, $colour, $grade, $key);
	}

	$this->SetCol (0);			// Go back to first column
}

}

date_default_timezone_set ('UTC');

$pdf = new PDF ('P', 'mm', 'A5');
//$pdf = new PDF ('L', 'mm', 'A4');
//$pdf->SetDisplayMode ('fullpage', 'single');
//$pdf->SetTopMargin (0);
//$pdf->SetRightMargin (5);

$pdf->AddPage();
$pdf->Ln();
$pdf->SetDrawColor (0);
$pdf->SetLineWidth (0.2);
$pdf->Line (48, 17, 48, 186);
$pdf->Line (98, 17, 98, 186);

$pdf->SetLineWidth (0.2);
$pdf->SetTitle ('Craggy Checklist');
$pdf->SetCreator ('Richard Russon');
$pdf->SetAuthor ('Richard Russon');
$pdf->SetSubject ('Craggy Routes');
$pdf->AddFont('WingDing','','wingding.php');
$pdf->SetCol (0);
$pdf->PrintChapter ('data.txt');

$pdf->Output();

