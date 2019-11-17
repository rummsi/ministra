<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
if (!isset($argv[1]) || $argv[1] == '--help') {
    echo "Usage: php ./m3u_to_radio.php [M3U FILE]\n";
    exit;
}
$dir = \dirname(__FILE__);
$inputFileName = \realpath($dir . '/' . $argv[1]);
if (!$inputFileName) {
    echo "File {$argv[1]} not found\n";
    exit;
}
$file = \file($inputFileName);
$result = [];
foreach ($file as $line) {
    if (\strpos($line, '#') === 0 && \strpos($line, ',') > 0) {
        list($foo, $name) = \explode(',', $line);
        $name = \trim($name);
    } elseif (\strpos($line, '#') === \false) {
        $url = \trim($line);
        if (isset($name)) {
            echo 'Found ' . $name . ' with url: ' . $url . "\n";
            $result[$name] = $url;
        }
    }
}
$number = $max_number = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(number) as max_number')->from('radio')->get()->first('max_number');
foreach ($result as $name => $url) {
    ++$number;
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('radio', ['number' => $number, 'name' => $name, 'cmd' => 'ifm ' . $url, 'status' => 1]);
}
