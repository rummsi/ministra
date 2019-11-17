<?php

require __DIR__ . '/../common.php';
require __DIR__ . '/../Lib/funcs/functions.php';
echo \get_last5min_play($argv[1]);
