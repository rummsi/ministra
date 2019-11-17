<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Downloads implements \Ministra\Lib\StbApi\Downloads
{
    public function getAll()
    {
        return \Ministra\Lib\System::base64_decode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_downloads')->where(['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->get()->first('downloads'));
    }
    public function save()
    {
        $downloads = @$_REQUEST['downloads'];
        if (empty($downloads)) {
            $downloads = '""';
        }
        $downloads = \Ministra\Lib\System::base64_encode($downloads);
        $record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_downloads')->where(['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->get()->first();
        if (empty($record)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('user_downloads', ['downloads' => $downloads, 'uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('user_downloads', ['downloads' => $downloads], ['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->result();
    }
    public function createDownloadLink($type, $media_id, $user_id, $param = '')
    {
        $link_hash = \md5(\microtime(1) . \uniqid());
        $id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('download_links', ['link_hash' => $link_hash, 'uid' => $user_id, 'type' => $type, 'media_id' => $media_id, 'param1' => $param, 'added' => 'NOW()'])->insert_id();
        return 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . \str_replace('/', '', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/')) . '/server/api/get_download_link.php?lid=' . ($id ? $link_hash : '');
    }
}
