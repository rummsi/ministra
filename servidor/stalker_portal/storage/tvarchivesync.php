<?php

require_once __DIR__ . '/common.php';
use Ministra\Storage\Lib\TvArchiveRecorder;
use Ministra\Storage\Lib\TvArchiveTasks;
if (\ASTRA_RECORDER) {
    \exec('which astra', $out, $exitCode);
    if ($exitCode !== 0) {
        throw new \RuntimeException('Astra recorder does not install. Install before usage: https://cesbo.com/en/astra/quick-start/');
    }
}
$archive = new \Ministra\Storage\Lib\TvArchiveTasks();
$archive->setApiUrl(\API_URL . 'tv_archive/' . \STORAGE_NAME);
$tasks = $archive->sync();
if (!\is_array($tasks)) {
    return \false;
}
$recorder = new \Ministra\Storage\Lib\TvArchiveRecorder();
echo $recorder->startAll($tasks);
