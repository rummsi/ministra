<?php

namespace Ministra\Lib;

class Cityinfo extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Cityinfo
{
    public static $instance = null;
    public function __construct()
    {
        parent::__construct();
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getOrderedList()
    {
        $result = $this->getData();
        $result = $result->orderby('num');
        $this->setResponseData($result);
        return $this->getResponse('prepareData');
    }
    private function getData()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $part = $_REQUEST['part'];
        if ($part == 'main') {
            $table = 'main_city_info';
        } elseif ($part == 'help') {
            $table = 'help_city_info';
        } else {
            $table = 'other_city_info';
        }
        return $this->db->from($table)->limit(self::MAX_PAGE_ITEMS, $offset);
    }
    public function prepareData()
    {
        return $this->response;
    }
}
