<?php

date_default_timezone_set('UTC');

//sleep (2);

echo '<br>';
echo 'hello: ' . date ('H:i:s') . '<br>';
echo '<br>';

$post_data = file_get_contents('php://input');
echo htmlentities($post_data);
echo '<br>';

$xml = simplexml_load_string ($post_data);

echo '<pre>';
echo htmlentities (print_r ($xml, true));
echo '</pre>';

echo 'Count = ' . $xml->count() . '<br>';
echo 'Name = ' . $xml->name . '<br>';
echo 'Message = ' . $xml->message . '<br>';

