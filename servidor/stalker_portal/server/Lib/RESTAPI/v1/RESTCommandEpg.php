<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class RESTCommandEpg extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function __construct()
    {
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, uri, etag, updated, id_prefix, status')->from('epg_setting')->get()->all();
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $res = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('epg_setting', $data)->insert_id();
        return $res;
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) != 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier count failed');
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('epg_setting', ['id' => $identifiers[0]]);
    }
}
