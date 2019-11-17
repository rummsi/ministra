<?php

namespace Ministra\Storage\Lib;

use ErrorException;
class RESTCommandVclubMd5Checker extends \Ministra\Storage\Lib\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Storage\Lib\Vclub();
    }
    public function create(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $media_name = $request->getData('media_name');
        if (empty($media_name)) {
            throw new \ErrorException('Empty media_name');
        }
        return $this->manager->startMD5Sum($media_name);
    }
}
