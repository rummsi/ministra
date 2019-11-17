<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\StreamRecorder;
class RESTCommandStreamRecorder extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $manager;
    public function __construct()
    {
        $this->manager = new \Ministra\Lib\StreamRecorder();
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (empty($identifiers)) {
            return $this->manager->getTasks();
        }
        return $this->manager->getRecordingInfo($identifiers[0]);
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $put = $request->getPut();
        if (empty($put)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        if (empty($put['action'])) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Action param is empty');
        }
        $identifiers = $request->getIdentifiers();
        if (empty($identifiers)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty identifiers');
        }
        if ($put['action'] == 'started') {
            foreach ($identifiers as $identifier) {
                $this->manager->setStarted((int) $identifier);
            }
            return true;
        } elseif ($put['action'] == 'ended') {
            foreach ($identifiers as $identifier) {
                $this->manager->setEnded((int) $identifier);
            }
            return true;
        }
        throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Action is wrong');
    }
}
