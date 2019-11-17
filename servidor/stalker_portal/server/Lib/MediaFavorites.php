<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class MediaFavorites implements \Ministra\Lib\StbApi\MediaFavorites
{
    public function getAll()
    {
        return \Ministra\Lib\System::base64_decode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_favorites')->where(['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->get()->first('favorites'));
    }
    public function save()
    {
        $favorites = @$_REQUEST['favorites'];
        if (empty($favorites)) {
            $favorites = '""';
        }
        $favorites = \Ministra\Lib\System::base64_encode($favorites);
        $record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_favorites')->where(['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->get()->first();
        if (empty($record)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('media_favorites', ['favorites' => $favorites, 'uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('media_favorites', ['favorites' => $favorites], ['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->result();
    }
}
