<?php

namespace Ministra\Lib\DVR;

class FlussonicDVR extends \Ministra\Lib\DVR\BaseDVR
{
    const ERROR_MSG_NOT_VALID = 'Address (command) for Flussonic is not valid';
    protected $ip;
    protected $valid;
    private $type;
    private $channel;
    private $subtype;
    public function __construct($stream, $ip)
    {
        parent::__construct($stream, $ip);
        $match = [];
        $this->valid = \preg_match('/:\\/\\/([^\\/]*)\\/([^\\/]*)\\/(.*)(mpegts|m3u8)$/', $stream, $match) === 1;
        if ($this->valid) {
            $this->channel = $match[2];
            $this->subtype = $match[3];
            $this->type = $match[4] == 'mpegts' ? 'ts' : $match[4];
        }
    }
    public function ErrorMessage()
    {
        return $this->isValid() ? self::ERROR_MSG_NOT_VALID : null;
    }
    public function isValid()
    {
        return $this->valid;
    }
    public function getArchiveLink($abs, $duration)
    {
        $tpl = 'http://%s/%s/archive-%d-%d.%s';
        return \sprintf($tpl, $this->ip, $this->channel, $abs, $duration, $this->type);
    }
    public function getDownloadLinkTs($abs, $duration)
    {
        $tpl = 'http://%s/%s/archive-%d-%d.ts';
        return \sprintf($tpl, $this->ip, $this->channel, $abs, $duration);
    }
    public function getTimeshiftLink($abs)
    {
        $tpl = 'http://%s/%s/timeshift_abs-%d.%s';
        return \sprintf($tpl, $this->ip, $this->channel, $abs, $this->type);
    }
    public function getType()
    {
        return $this->type;
    }
    public function getRewindingHlsLink($abs, $now = 'now')
    {
        $tpl = 'http://%s/%s/%s-%s-%s.m3u8';
        return \sprintf($tpl, $this->ip, $this->channel, \trim($this->subtype, '.'), $abs, $now);
    }
}
