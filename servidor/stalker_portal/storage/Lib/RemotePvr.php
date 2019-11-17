<?php

namespace Ministra\Storage\Lib;

class RemotePvr extends \Ministra\Storage\Lib\Storage
{
    public function __construct()
    {
        parent::__construct();
    }
    public function checkMedia($name)
    {
        $result = [];
        $result['series'] = [];
        $result['series_file'] = [];
        $result['files'] = [];
        if (\is_file(RECORDS_DIR . $name)) {
            $result['files'][] = ['name' => $name, 'md5' => ''];
        } else {
            throw new \Ministra\Storage\Lib\IOException('File ' . RECORDS_DIR . $name . ' not exist on ' . $this->storage_name);
        }
        return $result;
    }
    public function createLink($media_file, $media_id)
    {
        $this->user->checkHome();
        \preg_match("/([\\S\\s]+)\\.(" . $this->media_ext_str . ')$/i', $media_file, $arr);
        $ext = $arr[2];
        $from = RECORDS_DIR . $media_file;
        $to = NFS_HOME_PATH . $this->user->getMac() . '/' . $media_id . '.' . $ext;
        $link_result = @\symlink($from, $to);
        if (!$link_result) {
            throw new \Ministra\Storage\Lib\IOException('Could not create link ' . $from . ' to ' . $to . ' on ' . $this->storage_name);
        }
        if (!\is_readable($to)) {
            throw new \Ministra\Storage\Lib\IOException('File ' . $to . ' is not readable on ' . $this->storage_name);
        }
        return true;
    }
}
