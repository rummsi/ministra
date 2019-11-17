<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$mac = $argv[1];
$ch = $argv[2];
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('last_id', ['last_id' => $ch], ['ident' => $mac]);
