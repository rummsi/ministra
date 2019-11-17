<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\Itv;
class RESTCommandMonitoringLinks extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $manager;
    private $allowed_fields;
    public function __construct()
    {
        $this->manager = \Ministra\Lib\Itv::getInstance();
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        if (empty($request) || \strpos($request->getAccept(), 'text/channel-monitoring-id-url') === false) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Unsupported Accept header, use text/channel-monitoring-id-url');
        }
        $this->setManager($request);
        return $this->manager->getLinksForMonitoring(@$_GET['status']);
    }
    private function setManager(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $type = $request->getData('type');
        if (empty($type)) {
            $type = empty($_GET['type']) ? 'itv' : $_GET['type'];
        }
        $base_class = \ucfirst($type);
        if (\class_exists($base_class)) {
            $this->manager = $base_class::getInstance();
        }
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $this->setManager($request);
        $put = $request->getPut();
        if (empty($put)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        $allowed_to_update_fields = \array_fill_keys(['status', 'link_id'], true);
        $data = \array_intersect_key($put, $allowed_to_update_fields);
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Update data is empty');
        }
        $ids = $request->getIdentifiers();
        if (empty($ids)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty link id');
        }
        $link_id = $ids[0];
        $manager_class = \get_class($this->manager);
        return $manager_class::setChannelLinkStatus($link_id, (int) $data['status']);
    }
}
