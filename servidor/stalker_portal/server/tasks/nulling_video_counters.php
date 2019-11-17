<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$day = \date('j');
if ($day <= 15) {
    $field_name = 'count_first_0_5';
} else {
    $field_name = 'count_second_0_5';
}
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video', [$field_name => 0]);
echo 1;
