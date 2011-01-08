<?php

$self = $_SERVER['PHP_SELF'];
$dir  = dirname ($self);

echo "<a href='$dir/work.php'>work</a>";
echo '<br>';
echo "<a href='$dir/user/dave'>dave</a>";
echo '<br>';
echo "<a href='$dir/user/mike'>mike</a>";
echo '<br>';
echo 'work.php';
echo '<br>';
echo '<pre>';

//var_dump ($_SERVER);
var_dump ($_GET);

