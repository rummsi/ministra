<?php

$user = \posix_getpwuid(\posix_geteuid());
$group = \posix_getpwuid(\posix_getegid());
echo \json_encode(['user' => $user['name'], 'group' => $group['name']]);
