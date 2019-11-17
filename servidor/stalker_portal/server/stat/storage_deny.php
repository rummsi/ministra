<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$in_param = $argv[1];
$counter = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storage_deny')->where(['name' => $in_param])->get()->first('counter');
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('storage_deny', ['counter' => 0], ['name' => $in_param]);
echo $counter;
