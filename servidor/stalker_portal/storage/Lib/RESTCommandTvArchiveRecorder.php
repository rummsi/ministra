<?php

namespace Ministra\Storage\Lib;

use ErrorException;
class RESTCommandTvArchiveRecorder extends \Ministra\Storage\Lib\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Storage\Lib\TvArchiveRecorder();
    }
    public function create(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $task = $request->getData('task');
        if (empty($task)) {
            throw new \ErrorException('Empty task');
        }
        return $this->manager->start($task);
    }
    public function delete(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $ch_ids = $request->getIdentifiers();
        if (empty($ch_ids[0])) {
            throw new \ErrorException('Empty ch_id');
        }
        return $this->manager->stop($ch_ids[0]);
    }
}
