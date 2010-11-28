function html_table_header ($columns)
{
    $output = "";
    $output .= "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";

    foreach ($columns as $name) {
        $split = explode("_", $name);
        $n = array_pop($split);
        $n = ucfirst ($n);
        $output .= sprintf ("<th>%s</th>", $n);
    }

    $output .= "</tr>";

    return $output;
}

