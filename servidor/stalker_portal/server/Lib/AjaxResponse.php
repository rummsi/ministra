<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
abstract class AjaxResponse
{
    const MAX_PAGE_ITEMS = 14;
    protected $db;
    protected $stb;
    protected $page = 0;
    protected $load_last_page = false;
    protected $cur_page = 0;
    protected $selected_item = 0;
    protected $response = array('total_items' => 0, 'max_page_items' => self::MAX_PAGE_ITEMS, 'selected_item' => 0, 'cur_page' => 0, 'data' => array());
    protected $abc = array();
    protected $months = array();
    protected $all_title = '';
    protected $no_ch_info = '';
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
        $this->abc = \array_filter(['*', \_('ABC_1l'), \_('ABC_2l'), \_('ABC_3l'), \_('ABC_4l'), \_('ABC_5l'), \_('ABC_6l'), \_('ABC_7l'), \_('ABC_8l'), \_('ABC_9l'), \_('ABC_10l'), \_('ABC_11l'), \_('ABC_12l'), \_('ABC_13l'), \_('ABC_14l'), \_('ABC_15l'), \_('ABC_16l'), \_('ABC_17l'), \_('ABC_18l'), \_('ABC_19l'), \_('ABC_20l'), \_('ABC_21l'), \_('ABC_22l'), \_('ABC_23l'), \_('ABC_24l'), \_('ABC_25l'), \_('ABC_26l'), \_('ABC_27l'), \_('ABC_28l'), \_('ABC_29l'), \_('ABC_30l'), \_('ABC_31l'), \_('ABC_32l'), \_('ABC_33l')], function ($e) {
            return $e != '.';
        });
        $this->months = [\_('january'), \_('february'), \_('march'), \_('april'), \_('may'), \_('june'), \_('july'), \_('august'), \_('september'), \_('october'), \_('november'), \_('december')];
        $this->all_title = \_('All');
        $this->no_ch_info = \_('[No channel info]');
        $this->page = (int) (isset($_REQUEST['p']) ? $_REQUEST['p'] : 0);
        if ($this->page == 0) {
            $this->load_last_page = true;
        }
        if ($this->page > 0) {
            --$this->page;
        }
    }
    protected function setResponseData(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89 $query)
    {
        $query_rows = clone $query;
        $this->setResponse('total_items', $query_rows->nolimit()->nogroupby()->noorderby()->count()->get()->counter());
        $this->setResponse('cur_page', $this->cur_page);
        $this->setResponse('selected_item', $this->selected_item);
        $this->setResponse('data', $query->get()->all());
    }
    protected function getResponse($callback = '')
    {
        if ($callback && \is_callable([$this, $callback])) {
            return \call_user_func([$this, $callback]);
        }
        return $this->response;
    }
    protected function setResponse($key, $value)
    {
        $this->response[$key] = $value;
    }
    protected function getImgUri($id)
    {
        $cover = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('screenshots')->where(['id' => (int) $id])->get()->first();
        if (empty($cover)) {
            return false;
        }
        $dir_name = \ceil($id / 100);
        $dir_path = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('screenshots_url') . $dir_name;
        $ext = \pathinfo($cover['name'], PATHINFO_EXTENSION);
        if (!$ext) {
            $ext = 'jpg';
        }
        $dir_path .= '/' . $id . '.' . $ext;
        return $dir_path;
    }
    protected function setClaimGlobal($media_type)
    {
        $id = (int) $_REQUEST['id'];
        $type = $_REQUEST['real_type'];
        $this->db->insert('media_claims_log', ['media_type' => $media_type, 'media_id' => $id, 'type' => $type, 'uid' => $this->stb->id, 'added' => 'NOW()']);
        $total_media_claims = $this->db->from('media_claims')->where(['media_type' => $media_type, 'media_id' => $id])->get()->first();
        $sound_counter = 0;
        $video_counter = 0;
        $no_epg_counter = 0;
        $wrong_epg_counter = 0;
        if ($type == 'video') {
            ++$video_counter;
        } elseif ($type == 'sound') {
            ++$sound_counter;
        } elseif ($type == 'no_epg') {
            ++$no_epg_counter;
        } elseif ($type == 'wrong_epg') {
            ++$wrong_epg_counter;
        }
        if (!empty($total_media_claims)) {
            $this->db->update('media_claims', ['sound_counter' => $total_media_claims['sound_counter'] + $sound_counter, 'video_counter' => $total_media_claims['video_counter'] + $video_counter, 'no_epg' => $total_media_claims['no_epg'] + $no_epg_counter, 'wrong_epg' => $total_media_claims['wrong_epg'] + $wrong_epg_counter], ['media_type' => $media_type, 'media_id' => $id]);
        } else {
            $this->db->insert('media_claims', ['sound_counter' => $sound_counter, 'video_counter' => $video_counter, 'no_epg' => $no_epg_counter, 'wrong_epg' => $wrong_epg_counter, 'media_type' => $media_type, 'media_id' => $id]);
        }
        $total_daily_claims = $this->db->from('daily_media_claims')->where(['date' => 'CURDATE()'])->get()->first();
        $media_name = 'undefined';
        if ($media_type == 'itv') {
            $media = \Ministra\Lib\Itv::getById($id);
            if (!empty($media['name'])) {
                $media_name = $media['name'];
            }
        } elseif ($media_type == 'vclub') {
            $media = \Ministra\Lib\Video::getById($id);
            if (!empty($media['name'])) {
                $media_name = $media['name'];
            }
        } elseif ($media_type == 'karaoke') {
            $media = \Ministra\Lib\Karaoke::getById($id);
            if (!empty($media['name'])) {
                $media_name = $media['name'];
            }
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('administrator_email')) {
            $message = \sprintf(\_('New claim on %s - %s (%s, id: %s). From %s'), $media_type, $type, $media_name, $id, $this->stb->mac);
            \mail(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('administrator_email'), 'New claim on ' . $media_type . ' - ' . $type, $message, "Content-type: text/html; charset=UTF-8\r\n");
        }
        if (!empty($total_daily_claims)) {
            return $this->db->update('daily_media_claims', [$media_type . '_sound' => $total_daily_claims[$media_type . '_sound'] + $sound_counter, $media_type . '_video' => $total_daily_claims[$media_type . '_video'] + $video_counter, 'no_epg' => $total_daily_claims['no_epg'] + $no_epg_counter, 'wrong_epg' => $total_daily_claims['wrong_epg'] + $wrong_epg_counter], ['date' => 'CURDATE()'])->result();
        }
        return $this->db->insert('daily_media_claims', [$media_type . '_sound' => $sound_counter, $media_type . '_video' => $video_counter, 'no_epg' => $no_epg_counter, 'wrong_epg' => $wrong_epg_counter, 'date' => 'CURDATE()'])->insert_id();
    }
}
