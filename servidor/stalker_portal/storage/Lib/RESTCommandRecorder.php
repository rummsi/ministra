<?php

namespace Ministra\Storage\Lib;

use ErrorException;
class RESTCommandRecorder extends \Ministra\Storage\Lib\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Storage\Lib\Recorder();
    }
    public function create(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $url = $request->getData('url');
        $rec_id = (int) $request->getData('rec_id');
        $start_delay = (int) $request->getData('start_delay');
        $duration = (int) $request->getData('duration');
        if (empty($url)) {
            throw new \ErrorException('Empty url');
        }
        if (empty($rec_id)) {
            throw new \ErrorException('Empty rec_id');
        }
        if (empty($duration)) {
            throw new \ErrorException('Empty recording duration');
        }
        if ($start_delay < 0) {
            $start_delay = 0;
        }
        return $this->manager->start($url, $rec_id, $start_delay, $duration);
    }
    public function update(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (empty($identifiers[0])) {
            throw new \ErrorException('Empty rec_id');
        }
        $rec_id = (int) $identifiers[0];
        $stop_time = (int) $request->getData('stop_time');
        if ($stop_time) {
            return $this->manager->updateStopTime($rec_id, $stop_time);
        }
        return $this->manager->stop($rec_id);
    }
    public function delete(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $files = $request->getIdentifiers();
        if (empty($files[0])) {
            throw new \ErrorException('Empty filename');
        }
        return $this->manager->delete($files[0]);
    }
}
