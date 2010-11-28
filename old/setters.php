    // Setters stats ----------------------------------------

    /*
    $query = "select setter.name from route left join setter on route.setter = setter.id order by name";
    $result = mysql_query($query);

    $setters = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $name = $row['name'];
        if (array_key_exists($name, $setters))
            $setters[$name]++;
        else
            $setters[$name] = 1;
    }
    mysql_free_result($result);

    $count = count ($setters) - 1;
    $output .= "<dl><dt>$count Setters</dt>";
    foreach ($setters as $setter => $count) {
        if (empty ($setter))
            continue;
        $output .= "<dd>{$setter}: {$count}</dd>";
    }
    $output .= "</dl>";
    */


