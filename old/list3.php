function list_6a()
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "difficulty", "height");
    $where   = array ("grade_num >= 400", "grade_num < 500", "climb_type <> 'Lead'");
    $order   = "panel, grade_num, colour";

    return db_select($table, $columns, $where, $order);
}

function list_age()
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
    $where   = NULL;
    $order   = "date_set, panel, grade_num, colour";

    return db_select ($table, $columns, $where, $order);
}

function list_grade()
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
    $where   = NULL;
    $order   = "grade_num, panel, colour";

    return db_select ($table, $columns, $where, $order);
}

function list_panel()
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
    $where   = NULL;
    $order   = "panel, grade_num, colour";

    return db_select ($table, $columns, $where, $order);
}

function list_setter()
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
    $where   = NULL;
    $order   = "setter, panel, grade, colour";

    return db_select ($table, $columns, $where, $order);
}


