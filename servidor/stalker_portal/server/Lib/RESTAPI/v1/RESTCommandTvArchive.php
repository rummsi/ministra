<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\TvArchive;
class RESTCommandTvArchive extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Lib\TvArchive();
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $ids = $request->getIdentifiers();
        if (empty($ids[0])) {
            throw new \ErrorException('Empty storage name');
        }
        return $this->manager->getAllTasks($ids[0], true);
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $ids = $request->getIdentifiers();
        if (empty($ids[0]) || (int) $ids[0] == 0) {
            throw new \ErrorException('Empty channel id');
        }
        $data = $request->getData();
        if (\array_key_exists('start_time', $data)) {
            $this->manager->updateStartTime((int) $ids[0], $data['start_time']);
        }
        if (\array_key_exists('end_time', $data)) {
            $this->manager->updateEndTime((int) $ids[0], $data['end_time']);
        }
        return true;
    }
}
