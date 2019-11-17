<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class NotificationFeed
{
    private $feed_url = 'https://not.ministra.com/feed';
    public function getCount($only_not_read = true)
    {
        $items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('notification_feed')->where(['delay_finished_time<=' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d)])->count();
        if ($only_not_read) {
            $items->where(['`read`' => 0]);
        }
        return (int) $items->get()->counter();
    }
    public function getItems($only_not_read = true)
    {
        $items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('notification_feed')->where(['delay_finished_time<=' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d)])->orderby('pub_date DESC, guid', 'DESC');
        if ($only_not_read) {
            $items->where(['`read`' => 0]);
        }
        $items = $items->get()->all();
        $items = \array_map(function ($item) {
            return new \Ministra\Lib\NotificationFeedItem($item);
        }, $items);
        return $items;
    }
    public function getNotDeletedItems()
    {
        $items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('title, description, category, pub_date, `read`, link, guid')->from('notification_feed')->where(['deleted' => 0])->orderby('pub_date DESC, guid', 'DESC')->get()->all();
        return $items;
    }
    public function deleteByGuid($guid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('notification_feed', ['deleted' => 1], ['guid' => $guid])->result();
    }
    public function sync()
    {
        $language = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['login' => 'admin'])->get()->first('language');
        if (!$language) {
            $language = 'en';
        }
        $feed_url = $this->feed_url . (\strpos($this->feed_url, '?') ? '&' : '?') . 'lang=' . $language;
        $content = \file_get_contents($feed_url);
        if (!$content) {
            return false;
        }
        $feed = \simplexml_load_string($content);
        if (!$feed) {
            return false;
        }
        $result = true;
        foreach ($feed->channel->item as $item) {
            $item_arr = ['title' => (string) $item->title, 'description' => (string) $item->description, 'link' => (string) $item->link, 'category' => (string) $item->category, 'pub_date' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \strtotime((string) $item->pubDate)), 'guid' => (string) $item->guid];
            $notification = new \Ministra\Lib\NotificationFeedItem($item_arr);
            $result = $notification->sync() && $result;
        }
        return $result;
    }
    public function setRedByGuid($guid = null)
    {
        $where = $guid ? ['guid' => $guid] : [];
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('notification_feed', ['`read`' => 1], $where)->result();
    }
    public function getItemByGUId($guid)
    {
        $item = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('notification_feed')->where(['guid' => $guid])->get()->first();
        if (!$item) {
            return false;
        }
        return new \Ministra\Lib\NotificationFeedItem($item);
    }
}
