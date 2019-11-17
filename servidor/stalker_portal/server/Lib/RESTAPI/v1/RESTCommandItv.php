<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Itv;
class RESTCommandItv extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $manager;
    private $allowed_fields;
    public function __construct()
    {
        $this->manager = \Ministra\Lib\Itv::getInstance();
        $this->allowed_fields = \array_fill_keys(['id', 'name', 'number', 'base_ch', 'hd', 'url', 'enable_monitoring', 'descr'], true);
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $itv_list = $this->manager->getByIds($request->getIdentifiers());
        $allowed_fields = $this->allowed_fields;
        $itv_list = \array_map(function ($item) use($allowed_fields) {
            $item['url'] = $item['monitoring_url'];
            $item = \array_intersect_key($item, $allowed_fields);
            return $item;
        }, $itv_list);
        return $itv_list;
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $data['modified'] = \date('Y-m-d H:i:s');
        $data['base_ch'] = 1;
        $data['cmd'] = $url = $data['url'];
        unset($data['url']);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('ch_links', ['ch_id' => $data['id']]);
        $link = ['ch_id' => $data['id'], 'url' => $url, 'status' => $data['status']];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_links', $link);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('itv', $data)->insert_id();
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) != 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier count failed');
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('ch_links', ['ch_id' => $identifiers[0]]);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('itv', ['id' => $identifiers[0]]);
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $put = $request->getPut();
        if (empty($put)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        $allowed_to_update_fields = \array_fill_keys(['monitoring_status'], true);
        $data = \array_intersect_key($put, $allowed_to_update_fields);
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Update data is empty');
        }
        $ids = $request->getIdentifiers();
        if (empty($ids)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty channel id');
        }
        $channel_id = (int) $ids[0];
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', $data, ['id' => $channel_id]);
    }
}
