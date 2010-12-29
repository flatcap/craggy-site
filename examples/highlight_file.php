<?php

/* Add this to .htaccess
# make php source viewable as myfile.phps (same for html/htmls)
RewriteEngine on
RewriteRule ^(.*)/([^/\.]+\.php)s$ /craggy_test/phpsource.php?file=$1/$2 [L]
RewriteRule ^(.*)/([^/\.]+\.html)s$ /craggy_test/phpsource.php?file=$1/$2 [L]
*/

echo "<body style='background: #000; color: #fff;'>";

//ini_set('highlight.comment', '#CCCCCC; font-weight: bold;');

ini_set('highlight.string',  'yellow');
ini_set('highlight.comment', '#00ff00');
ini_set('highlight.keyword', 'cyan');
ini_set('highlight.default', 'white');
ini_set('highlight.html',    'white');

highlight_file($_GET['file']);

