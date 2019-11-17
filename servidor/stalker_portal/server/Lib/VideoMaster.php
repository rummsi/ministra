<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class VideoMaster extends \Ministra\Lib\Master implements \Ministra\Lib\StbApi\VideoMaster
{
    public function __construct()
    {
        $this->media_type = 'vclub';
        $this->db_table = 'video';
        parent::__construct();
    }
    public function getStoragesForVideo()
    {
        $video_id = (int) $_REQUEST['video_id'];
        $good_storages = $this->getAllGoodStoragesForMediaFromNet($video_id, 0);
        $good_storages = $this->sortByLoad($good_storages);
        return \array_keys($good_storages);
    }
    protected function getMediaName()
    {
        if (!empty($this->media_params) && !empty($this->media_params['path'])) {
            return $this->media_params['path'];
        }
        return '';
    }
    protected function getMediaPath($file_name, $file_id)
    {
        if ($file_id) {
            $file = \Ministra\Lib\Video::getFileById($file_id);
            if (!empty($file)) {
                $movie = \Ministra\Lib\Video::getById($file['video_id']);
                if ($movie['is_series'] && $file['series_id']) {
                    $episode = \Ministra\Lib\Video::getEpisodeById($file['series_id']);
                    $season = \Ministra\Lib\Video::getSeasonById($episode['season_id']);
                    $path = '/s' . \str_pad($season['season_number'], 2, '0', STR_PAD_LEFT) . '/e' . \str_pad($episode['series_number'], 2, '0', STR_PAD_LEFT) . '/';
                } else {
                    $path = '/';
                }
                return $this->media_name . $path . $file['file_name'];
            }
        }
        return $this->media_name . '/' . $file_name;
    }
    protected function setStatus($status, $file_id = 0)
    {
        if ($file_id) {
            $this->db->update('video_series_files', ['status' => $status], ['video_id' => $this->media_id, 'id' => $file_id, 'protocol<>' => 'custom']);
        }
        $files = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select(['status', 'protocol'])->from('video_series_files')->where(['video_id' => $this->media_id, 'file_type' => 'video'])->get()->all('status');
        $has_files = \count($files);
        if ($file_id) {
            if (\is_array($files)) {
                $has_files = \count(\array_filter($files));
            }
            $this->db->update('video', ['status' => (int) (bool) $has_files], ['id' => $this->media_id]);
        } elseif (!$has_files) {
            $this->db->update('video', ['status' => $status], ['id' => $this->media_id]);
        }
    }
}
