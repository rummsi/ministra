<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class NotificationFeedItem
{
    private $id;
    private $title;
    private $description;
    private $link;
    private $category;
    private $pub_date;
    private $guid;
    private $read;
    public function __construct($item)
    {
        if (isset($item['id'])) {
            $this->id = (int) $item['id'];
        }
        if (isset($item['title'])) {
            $this->title = $item['title'];
        }
        if (isset($item['description'])) {
            $this->description = $item['description'];
        }
        if (isset($item['link'])) {
            $this->link = $item['link'];
        }
        if (isset($item['category'])) {
            $this->category = $item['category'];
        }
        if (isset($item['pub_date'])) {
            $this->pub_date = $item['pub_date'];
        }
        if (isset($item['guid'])) {
            $this->guid = $item['guid'];
        }
        if (isset($item['read'])) {
            $this->read = $item['read'] == 1 ? true : false;
        }
    }
    public function sync()
    {
        if ($this->id) {
            $db_item = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('notification_feed')->where(['id' => $this->id])->get()->first();
        } elseif ($this->guid) {
            $db_item = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('notification_feed')->where(['guid' => $this->guid])->get()->first();
        } else {
            return false;
        }
        if (empty($db_item)) {
            $this->read = 0;
            $this->id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('notification_feed', ['title' => $this->title, 'description' => $this->description, 'link' => $this->link, 'category' => $this->category, 'pub_date' => $this->pub_date, 'guid' => $this->guid, 'read' => $this->read, 'added' => 'NOW()'])->insert_id();
            return $this->id ? true : false;
        } elseif ($db_item['title'] != $this->title || $db_item['description'] != $this->description || $db_item['link'] != $this->link || $db_item['category'] != $this->category || $db_item['pub_date'] != $this->pub_date || $db_item['guid'] != $this->guid) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('notification_feed', ['title' => $this->title, 'description' => $this->description, 'link' => $this->link, 'category' => $this->category, 'pub_date' => $this->pub_date, 'guid' => $this->guid, 'added' => 'NOW()'], ['id' => $db_item['id']])->result();
        }
        return false;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getLink()
    {
        return $this->link;
    }
    public function getCategory()
    {
        return $this->category;
    }
    public function getPubDate()
    {
        return $this->pub_date;
    }
    public function getGUId()
    {
        return $this->guid;
    }
    public function getRead()
    {
        return $this->read;
    }
    public function setRead($read = 0)
    {
        $this->read = $read;
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('notification_feed', ['`read`' => $this->read], ['id' => $this->id])->result();
    }
    public function setDelay($minutes)
    {
        $this->read = 0;
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('notification_feed', ['delay_finished_time' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() + $minutes * 60), '`read`' => 0], ['id' => $this->id])->result();
    }
}
