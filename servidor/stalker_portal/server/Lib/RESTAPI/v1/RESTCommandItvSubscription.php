<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\ItvSubscription;
class RESTCommandItvSubscription extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $allowed_fields;
    public function __construct()
    {
        $this->allowed_fields = \array_fill_keys(['ls', 'mac', 'sub_ch', 'additional_services_on'], true);
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $list = \Ministra\Lib\ItvSubscription::getByUids($request->getConvertedIdentifiers());
        return $this->formatList($list);
    }
    private function formatList($list)
    {
        $allowed_fields = $this->allowed_fields;
        $list = \array_map(function ($item) use($allowed_fields) {
            $item = \array_intersect_key($item, $allowed_fields);
            return $item;
        }, $list);
        return $list;
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $put = $request->getPut();
        if (empty($put)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        $allowed_to_update_fields = \array_fill_keys(['sub_ch', 'additional_services_on'], true);
        $data = \array_intersect_key($put, $allowed_to_update_fields);
        $stb_data = \array_intersect_key($put, ['additional_services_on' => true]);
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Update data is empty');
        }
        unset($data['additional_services_on']);
        if (!empty($stb_data)) {
            $uids = $request->getConvertedIdentifiers();
            foreach ($uids as $uid) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::c8fbd03665e7195573037a3cd22aca1e($uid, (int) $stb_data['additional_services_on']);
            }
        }
        if (!empty($data)) {
            $list = \Ministra\Lib\ItvSubscription::updateByUids($request->getConvertedIdentifiers(), $data);
            if (empty($list)) {
                return false;
            }
        }
        return $this->formatList(\Ministra\Lib\ItvSubscription::getByUids($request->getConvertedIdentifiers()));
    }
}
