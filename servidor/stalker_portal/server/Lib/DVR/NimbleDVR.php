<?php

namespace Ministra\Lib\DVR;

class NimbleDVR extends \Ministra\Lib\DVR\BaseDVR
{
    private $path;
    private $type;
    public function __construct($stream, $ip)
    {
        parent::__construct($stream, $ip);
        $match = [];
        $this->valid = \preg_match('/:\\/\\/([^\\/]*)\\/(.*)\\/playlist_dvr\\.m3u8$/', $stream, $match) === 1;
        if ($this->valid) {
            $this->path = $match[2];
            $this->type = 'm3u8';
        }
    }
    public function getArchiveLink($abs, $duration = 'now')
    {
        $tpl = 'http://%s/%s/playlist_dvr_range-%s-%s.m3u8';
        return \sprintf($tpl, $this->ip, $this->path, $abs, $duration);
    }
}
