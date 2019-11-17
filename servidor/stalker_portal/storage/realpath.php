<?php

if (!isset($argv[1])) {
    echo \dirname(__FILE__);
    exit;
}
if (!\is_file($argv[1]) && !\is_dir($argv[1])) {
    echo '';
    exit;
}
$file = \is_file($argv[1]) ? \dirname($argv[1]) : $argv[1];
echo \realpath($file);
