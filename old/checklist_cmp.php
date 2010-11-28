function checklist_cmp($a, $b)
{
    $b1 = checklist_grade_block ($a['grade']);
    $p1 = $a['panel'];
    $g1 = $a['grade_num'];
    $c1 = $a['colour'];

    $b2 = checklist_grade_block ($b['grade']);
    $p2 = $b['panel'];
    $g2 = $b['grade_num'];
    $c2 = $b['colour'];

    if ($b1 != $b2)
        return ($b1 < $b2) ? -1 : 1;

    if ($p1 != $p2)
        return ($p1 < $p2) ? -1 : 1;

    if ($g1 != $g2)
        return ($g1 < $g2) ? -1 : 1;

    return ($c1 < $c2) ? -1 : 1;
}

