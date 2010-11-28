function db_get_table($table, $order = "")
{
    if (!empty ($order))
        $order = "order by $table.$order";

    $result = mysql_query("select * from $table $order;");

    $list = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $list[$row['id']] = $row;
    }

    mysql_free_result($result);
    return $list;
}

