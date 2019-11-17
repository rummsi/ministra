<?php

namespace Ministra\Storage\Lib;

use ErrorException;
class RESTCommandRemotePvr extends \Ministra\Storage\Lib\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Storage\Lib\RemotePvr();
    }
    public function get(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (empty($identifiers[0])) {
            throw new \ErrorException('Empty media_name');
        }
        return $this->manager->checkMedia($identifiers[0]);
    }
    public function create(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $media_name = $request->getData('media_name');
        $media_id = $request->getData('media_id');
        if (empty($media_name)) {
            throw new \ErrorException('Empty media_name');
        }
        if (empty($media_id)) {
            throw new \ErrorException('Empty media_id');
        }
        return $this->manager->createLink($media_name, $media_id);
    }
}
