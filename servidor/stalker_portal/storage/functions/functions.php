<?php

function get_next_file($file)
{
    $filename = \basename($file);
    $filename = \substr($filename, 0, \strpos($filename, '.'));
    $filedate = $filename . ':00:00';
    $filedate = \str_replace('-', ' ', $filedate);
    $filedate = \strtotime($filedate . ' +1 hour');
    return \str_replace($filename, \date('Ymd-H', $filedate), $file);
}
function file_in_current_hour($file)
{
    $filename = \basename($file);
    $filename = \substr($filename, 0, \strpos($filename, '.'));
    \_log('file: ' . $filename . ' - ' . \date('Ymd-H') . ' ' . (\date('Ymd-H') == $filename));
    return $filename == \date('Ymd-H');
}
function get_content_length($queue)
{
    $length = 0;
    foreach ($queue as $item) {
        $length += $item['size'];
    }
    return $length;
}
if (!\function_exists('_log')) {
    function _log($message)
    {
    }
}
if (!\function_exists('getPythonInterpreterName')) {
    function getPythonInterpreterName()
    {
        \exec('which python', $output, $exitCode);
        return $exitCode === 0 ? 'python' : 'python3';
    }
}
