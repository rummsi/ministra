<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Advertising
{
    public function __construct()
    {
    }
    public function getMainMini()
    {
        $ad = $this->getMain();
        if (\array_key_exists('text', $ad)) {
            unset($ad['text']);
        }
        return $ad;
    }
    public function getMain()
    {
        $ad = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('main_page_ad')->get()->all();
        if (\count($ad) > 0) {
            return $ad[0];
        }
    }
    public function setMain($title = '', $text = '', $video_id = 0)
    {
        $rows = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('main_page_ad')->get()->counter();
        if ($rows > 0) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('main_page_ad', ['title' => $title, 'text' => $text, 'video_id' => (int) $video_id], []);
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('main_page_ad', ['title' => $title, 'text' => $text, 'video_id' => (int) $video_id]);
        }
    }
    public function delMain()
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from main_page_ad');
    }
}
