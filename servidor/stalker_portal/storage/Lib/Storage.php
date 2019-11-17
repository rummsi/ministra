<?php

namespace Ministra\Storage\Lib;

abstract class Storage
{
    protected $media_ext_arr = array('mpg', 'mpeg', 'avi', 'ts', 'mkv', 'mp4', 'mov', 'm2ts', 'flv', 'm4v');
    protected $media_ext_str = '';
    protected $storage_name = '';
    protected $user;
    public function __construct()
    {
        $this->media_ext_str = \implode('|', $this->media_ext_arr);
        if (\defined('STORAGE_NAME')) {
            $this->storage_name = STORAGE_NAME;
        } else {
            $this->storage_name = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR'];
        }
        $this->user = \Ministra\Storage\Lib\User::getInstance();
        $this->user->setStorageName($this->storage_name);
    }
}
