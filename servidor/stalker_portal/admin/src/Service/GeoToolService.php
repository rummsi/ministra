<?php

namespace Ministra\Admin\Service;

class GeoToolService
{
    private $service;
    public function __construct($service)
    {
        $this->service = $service;
    }
    public function getLinkToService($ip)
    {
        if (!$this->isValidIp($ip, true)) {
            return null;
        }
        $services = $this->services();
        if (!\array_key_exists($this->service, $services)) {
            return null;
        }
        return \sprintf($services[$this->service], $ip);
    }
    private function services()
    {
        return ['geoiptool' => 'https://geoiptool.com/?ip=%s', 'geoip' => 'https://geoip.tools/?q=%s', 'ipapi' => 'https://ipapi.com/ip_api.php?ip=%s', 'ip-api' => 'http://ip-api.com/#%s'];
    }
    private function isValidIp($ip, $noRes = false)
    {
        if ($noRes) {
            return \filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        }
        return \filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
