function reference()
{
    $a = array();
    $a[1] = "apple";
    $a[2] = "banana";

    $b = array();
    $b[1] = "chess";
    $b[2] = "darts";

    $c = array();
    array_push($c, $a);
    array_push($c, $b);

    $d = array();
    $d[1] = &$a[1];

    $a[1] = "wibble";
    var_dump ($d);
}

