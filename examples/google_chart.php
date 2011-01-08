<?php

function chart_line_chart()
{
    return 'cht=lc';
}

function chart_title ($title)
{
    return 'chtt=' . urlencode($title);
}

function chart_size ($x, $y)
{
    return "chs={$x}x{$y}";
}

function chart_data($values)
{
    $enc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $len = strlen ($enc) - 1;
    $max = 50;//max($values);
    $out = 's:';

    foreach ($values as $num) {
        if (($num >= 0) && ($num <= $len))
            $out .= $enc[$len * ($num / $max)];
        else
            $out .= '_';
    }

    return "chd=$out";
}

function chart_colour ($rgb)
{
    return "chco=$rgb";
}

function chart_line_style ($thickness, $line, $blank)
{
    return "chls=$thickness,$line,$blank";
}


function chart_main()
{
    $base = 'http://chart.apis.google.com/chart';
    //$data = array (41,80,13,85,15,0,23,0,0,0);      // Age in months (0-9)
    $data = array (7,14,25,24,28,48,40,23,23,9,12,3,1,6,1,1,3,0,2,0,0);  // Grades

    $url = array();

    $url[] = chart_line_chart();
    //$url[] = chart_title ('Grades');
    $url[] = chart_size (800, 300);
    $url[] = chart_data ($data);
    $url[] = chart_colour ('4684ee');
    $url[] = chart_line_style (3, 1, 0);

    // Label axes
    $url[] = 'chxt=x,y,r';
    $url[] = 'chxl=0:|3|3%2B|4|4%2B|5|5%2B|6a|6a%2B|6b|6b%2B|6c|6c%2B|7a|7a%2B|7b|7b%2B|7c|7c%2B|8a|8a%2B||1:|0|10|20|30|40|50|2:|0|10|20|30|40|50';

    // Make the labels more visible
    $url[] = 'chxs=0,000000,14,-1,l|1,000000,14,-1,lt|2,000000,14,1,lt';

    // Overlay dotted grid
    $url[] = 'chg=5,20,2,5';

    //$url[] = 'chxtc=1,-800';
    //$url[] = 'chxr=0,0,48,10';

    return "$base?" . implode ($url, '&');
}


$url = chart_main();

$output  = '<body>';
$output .= "<div style='margin:50px'><img src='$url'></div>";

echo $output;

