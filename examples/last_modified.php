<?php

date_default_timezone_set('UTC');

// outputs e.g. 'Last modified: March 04 1998 20:43:59.'
echo 'Last modified: ' . date ('F d Y H:i:s.', getlastmod());

