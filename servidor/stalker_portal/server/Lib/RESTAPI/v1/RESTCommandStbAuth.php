<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class RESTCommandStbAuth extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $stb_list = $request->getConvertedIdentifiers();
        if (empty($stb_list)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty stb list');
        }
        foreach ($stb_list as $uid) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['access_token' => \strtoupper(\md5(\microtime(1) . \uniqid()))], ['id' => $uid]);
            if ($request->getData('reset_device_id')) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['mac' => '', 'device_id' => '', 'device_id2' => ''], ['id' => $uid]);
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('access_tokens', ['token' => 'invalid_' . \md5(\microtime(1) . \uniqid()), 'refresh_token' => 'invalid_' . \md5(\microtime(1) . \uniqid())], ['uid' => $uid]);
        }
        return true;
    }
}
