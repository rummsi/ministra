<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class RESTCommandStbModules extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $stb_list = $request->getConvertedIdentifiers();
        if (empty($stb_list)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty stb list');
        }
        if (\count($stb_list) != 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $uid = $stb_list[0];
        return ['disabled' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::d7814074e003e3e6aea2e49c0c79a49d($uid), 'restricted' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::b80c972bba3c212fc8653a645f5e00f0d($uid)];
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $stb_list = $request->getConvertedIdentifiers();
        if (empty($stb_list)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Empty stb list');
        }
        $uids = $stb_list;
        $data = $request->getPut();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        if (!\array_key_exists('disabled', $data) && !\array_key_exists('restricted', $data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Update data is empty');
        }
        if (\array_key_exists('disabled', $data)) {
            foreach ($uids as $uid) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::y44523f7c558f25ad658a8322c149de6b($uid, $data['disabled']);
            }
        }
        if (\array_key_exists('restricted', $data)) {
            foreach ($uids as $uid) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::fc12573167fa18c43958ffa7bb4c608b($uid, $data['restricted']);
            }
        }
        return ['disabled' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::d7814074e003e3e6aea2e49c0c79a49d($uids[0]), 'restricted' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::b80c972bba3c212fc8653a645f5e00f0d($uids[0])];
    }
}
