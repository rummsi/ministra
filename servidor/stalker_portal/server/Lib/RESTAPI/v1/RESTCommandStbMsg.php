<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\SysEvent;
class RESTCommandStbMsg extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $stb_list = $request->getConvertedIdentifiers();
        if (empty($stb_list)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty stb list');
        }
        $msg = $request->getData('msg');
        if (empty($msg)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty msg');
        }
        $event = new \Ministra\Lib\SysEvent();
        $ttl = (int) $request->getData('ttl');
        if (!empty($ttl)) {
            $event->setTtl($ttl);
        }
        $auto_hide_timeout = (int) $request->getData('auto_hide_timeout');
        if ($auto_hide_timeout) {
            $event->setAutoHideTimeout($auto_hide_timeout);
        }
        $event->setUserListById($stb_list);
        $event->sendMsg($msg);
        return true;
    }
}
