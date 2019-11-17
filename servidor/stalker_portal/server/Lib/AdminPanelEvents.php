<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class AdminPanelEvents extends \Ministra\Lib\SysEvent
{
    private $postData = array();
    private $user_list = array();
    public function __construct($postData = array())
    {
        parent::__construct();
        $this->postData = $postData;
    }
    public function setPostData($postData)
    {
        $this->postData = $postData;
        return $this;
    }
    public function get_userlist_all()
    {
        if (\array_key_exists('event', $this->postData) && $this->postData['event'] == 'send_msg' || $this->postData['event'] == 'send_msg_with_video') {
            $this->user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b49b5de1f8caa1ded0e5d2b1848b3a8a();
        } else {
            $this->user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b03d7ef7b2ba1705d9a43de730650d5f();
        }
        return $this;
    }
    public function get_userlist_by_user_list()
    {
        if (\array_key_exists('user_list_type', $this->postData) && $this->postData['user_list_type'] == 'by_user_list' && !empty($this->postData['file_name'])) {
            $file_name = \sys_get_temp_dir() . '/' . $this->postData['file_name'];
            if (\is_writable($file_name)) {
                $file_data = \explode(';', \file_get_contents($file_name));
                foreach ($file_data as $mac) {
                    $uid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($mac);
                    if ($uid) {
                        $this->user_list[] = $uid;
                    }
                }
            }
            @\unlink($file_name);
        }
        return $this;
    }
    public function get_userlist_by_group()
    {
        if (\array_key_exists('group_id', $this->postData) && (int) $this->postData['group_id'] > 0) {
            $this->user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('stb_in_group')->where(['stb_group_id' => $this->postData['group_id']])->get()->all('uid');
        }
        return $this;
    }
    public function get_userlist_by_pattern()
    {
        if (!empty($this->postData['pattern'])) {
            $param = [];
            if ($this->postData['pattern'] == 'MAG100') {
                $param['hd'] = 0;
            } else {
                $param['stb_type'] = $this->postData['pattern'];
            }
            $this->user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db($param);
        }
        return $this;
    }
    public function get_userlist_by_filter()
    {
        if (!empty($this->postData['filter_set'])) {
            $filter_set = \Ministra\Lib\Filters::getInstance();
            $filter_set->setResellerID(\array_key_exists('reseller', $this->postData) ? $this->postData['reseller'] : 0);
            $filter_set->initData('users', 'id');
            $curr_filter_set = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('filter_set')->where(['id' => $this->postData['filter_set']])->get()->first();
            if (!empty($curr_filter_set) && ($unserialize_data = @\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($curr_filter_set['filter_set']))) {
                $filter_data = [];
                foreach ($unserialize_data as $row) {
                    $filter_data[$row[0]] = $row;
                }
                $filters_with_cond = \array_values(\array_filter(\array_map(function ($row) use($filter_data) {
                    if (\array_key_exists($row['text_id'], $filter_data)) {
                        $filters_with_cond = [];
                        $parsed_values = [];
                        switch ($row['type']) {
                            case 'DATETIME':
                                $parsed_values = \explode('|', (string) $filter_data[$row['text_id']][2]);
                                $filters_with_cond[] = [$row['method'], '>=', !empty($parsed_values[0]) ? $parsed_values[0] : 0];
                                $filters_with_cond[] = [$row['method'], '<=', !empty($parsed_values[1]) ? $parsed_values[1] : (empty($parsed_values[0]) ? \time() : 0)];
                                break;
                            case 'NUMBER' && \strpos($filter_data[$row['text_id']][2], '|') !== false:
                            case 'VALUES_SET' && \strpos($filter_data[$row['text_id']][2], '|') !== false:
                                $parsed_values = \explode('|', (string) $filter_data[$row['text_id']][2]);
                                if ($row['text_id'] == 'status' || $row['text_id'] == 'state') {
                                    $parsed_values = \array_map(function ($filter_val) {
                                        return (int) ($filter_val - 1 > 0);
                                    }, $parsed_values);
                                }
                                $filters_with_cond[] = [$row['method'], 'in', $parsed_values];
                                break;
                            default:
                                $filters_with_cond[] = [$row['method'], $filter_data[$row['text_id']][1], $filter_data[$row['text_id']][2]];
                                break;
                        }
                        return $filters_with_cond;
                    }
                }, $filter_set->getFilters())));
                if (\is_array($filters_with_cond) && !empty($filters_with_cond)) {
                    $filters_with_cond = \count($filters_with_cond) > 1 ? \call_user_func_array('array_merge', \array_values($filters_with_cond)) : \current($filters_with_cond);
                }
                $filtered_arr = \array_map(function ($filter) use($filter_set) {
                    $filter_set->initData('users', 'id');
                    \call_user_func_array([$filter_set, 'setFilters'], $filter);
                    return $filter_set->getData();
                }, $filters_with_cond);
                $filtered_merge = \array_unique(\call_user_func_array('array_merge', \array_values($filtered_arr)));
                \array_unshift($filtered_arr, $filtered_merge);
                $this->user_list = \array_unique(\array_values(\call_user_func_array('array_intersect', \array_values($filtered_arr))));
            }
        }
        return $this;
    }
    public function get_userlist_single()
    {
        $ids = [];
        if (\array_key_exists('mac', $this->postData) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::M07ba21bb659416ecab21793947436d6c($this->postData['mac'])) {
            $ids[] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($this->postData['mac']);
        } elseif (\array_key_exists('id', $this->postData) && \is_numeric($this->postData['id'])) {
            $ids[] = $this->postData['id'];
        }
        $this->user_list = $ids;
        return $this;
    }
    public function get_userlist_selected()
    {
        $ids = \array_key_exists('selected_ids', $this->postData) ? \explode(',', $this->postData['selected_ids']) : [];
        $this->user_list = $ids;
        return $this;
    }
    public function cleanAndSetUsers()
    {
        $all_users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->get()->all('id');
        if (!empty($all_users)) {
            $this->user_list = \array_intersect($this->user_list, $all_users);
        }
        $this->setUserListById($this->user_list);
        return $this;
    }
    public function set_event_send_msg()
    {
        if (\array_key_exists('msg', $this->postData) && \array_key_exists('header', $this->postData)) {
            if (!empty($this->postData['reboot_after_ok'])) {
                $this->sendMsgAndReboot($this->postData['msg'], $this->postData['header']);
            } else {
                $this->sendMsg($this->postData['msg'], $this->postData['header']);
            }
            return true;
        }
        return false;
    }
    public function set_event_reboot()
    {
        $this->sendReboot();
        return true;
    }
    public function set_event_reload_portal()
    {
        $this->sendReloadPortal();
        return true;
    }
    public function set_event_update_channels()
    {
        $this->sendUpdateChannels();
        return true;
    }
    public function set_event_play_channel()
    {
        if (\array_key_exists('channel', $this->postData)) {
            $this->sendPlayChannel($this->postData['channel']);
            return true;
        }
        return false;
    }
    public function set_event_play_radio_channel()
    {
        if (\array_key_exists('channel', $this->postData)) {
            $this->sendPlayRadioChannel($this->postData['channel']);
            return true;
        }
        return false;
    }
    public function set_event_mount_all_storages()
    {
        $this->sendMountAllStorages();
        return true;
    }
    public function set_event_cut_off()
    {
        if (!\is_array($this->user_list)) {
            $this->user_list = [$this->user_list];
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->where(['id in (' . \implode(',', $this->user_list) . ') and 1' => 1])->update('users', ['status' => 1, 'last_change_status' => 'NOW()']);
        $this->sendCutOff();
        return true;
    }
    public function set_event_update_image()
    {
        $this->sendUpdateImage();
        return true;
    }
    public function set_event_send_msg_with_video()
    {
        if (\array_key_exists('msg', $this->postData) && \array_key_exists('header', $this->postData) && !empty($this->postData['video_url'])) {
            $this->sendMsgWithVideo($this->postData['msg'], $this->postData['video_url'], $this->postData['header']);
            return true;
        }
        return false;
    }
    public function getUserList()
    {
        return $this->user_list;
    }
}
