<?php

namespace Ministra\OldAdmin;

use Imagick;
use ImagickException;
use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Karaoke;
use Ministra\Lib\SysEvent;
use Ministra\Lib\Video;
if (!\function_exists('Ministra\\OldAdmin\\get_line')) {
    function get_line($date, $epg_lines, $line_num)
    {
        $epg_line = @\trim($epg_lines[$line_num]);
        \preg_match("/(\\d+):(\\d+)[\\s\t]*([\\S\\s]+)/", $epg_line, $tmp_line);
        if (@$tmp_line[1] && $tmp_line[2] && $tmp_line[3]) {
            $result = [];
            $time = $date . ' ' . $tmp_line[1] . ':' . $tmp_line[2] . ':00';
            $result['time'] = $time;
            $result['name'] = $tmp_line[3];
            $next_line = get_line($date, $epg_lines, $line_num + 1);
            if (!empty($next_line)) {
                $time_to = $next_line['time'];
                $result['time_to'] = $time_to;
                $result['duration'] = \strtotime($time_to) - \strtotime($time);
            } else {
                $result['time_to'] = 0;
                $result['duration'] = 0;
            }
            return $result;
        }
        return false;
    }
    function construct_option($id = 0)
    {
        $opt = '';
        $channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->get()->all();
        foreach ($channels as $arr) {
            if ($id && $id == $arr['id']) {
                $opt .= "<option value='" . $arr['id'] . "' selected>" . $arr['name'] . "</option>\n";
            } else {
                $opt .= "<option value='" . $arr['id'] . "'>" . $arr['name'] . "</option>\n";
            }
        }
        return $opt;
    }
    function construct_YY()
    {
        if (empty($_GET['yy'])) {
            $year = \date('Y');
        } else {
            $year = $_GET['yy'];
        }
        $yy = "<option value='" . $year . "'>" . $year . '</option>';
        $yy .= "<option value='" . ($year + 1) . "'>" . ($year + 1) . '</option>';
        return $yy;
    }
    function construct_MM()
    {
        $month = [1 => \_('January'), 2 => \_('February'), 3 => \_('March'), 4 => \_('April'), 5 => \_('May'), 6 => \_('June'), 7 => \_('July'), 8 => \_('August'), 9 => \_('September'), 10 => \_('October'), 11 => \_('November'), 12 => \_('December')];
        $mm = '';
        if (empty($_GET['mm'])) {
            $mon = \date('n');
        } else {
            $mon = $_GET['mm'];
        }
        for ($i = 1; $i <= 12; ++$i) {
            if ($i == $mon) {
                $mm .= "<option value='" . $i . "' selected>" . $month[$i] . '</option>';
            } else {
                $mm .= "<option value='" . $i . "'>" . $month[$i] . '</option>';
            }
        }
        return $mm;
    }
    function construct_DD()
    {
        if (empty($_GET['dd'])) {
            $day = \date('j');
        } else {
            $day = $_GET['dd'];
        }
        $dd = '';
        for ($i = 1; $i <= 31; ++$i) {
            if ($i == $day) {
                $dd .= "<option value='" . $i . "' selected>" . $i . '</option>';
            } else {
                $dd .= "<option value='" . $i . "'>" . $i . '</option>';
            }
        }
        return $dd;
    }
    function load_epg($id = 0)
    {
        $epg = '';
        if (!$id) {
            return;
        }
        if (@$_GET['yy'] && @$_GET['mm'] && @$_GET['dd']) {
            $time = \mktime(0, 0, 0, $_GET['mm'], $_GET['dd'], $_GET['yy']);
        } else {
            $time = \time();
        }
        $year = \date('Y', $time);
        $month = \date('m', $time);
        $day = \date('d', $time);
        $time_from = $year . '-' . $month . '-' . $day . ' 00:00:00';
        $time_to = $year . '-' . $month . '-' . $day . ' 23:59:59';
        $programs = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('epg')->where(['ch_id' => $id, 'time>=' => $time_from, 'time<=' => $time_to])->orderby('time')->get()->all();
        foreach ($programs as $arr) {
            $epg .= time_mysql2epg($arr['time']) . ' ' . $arr['name'] . "\n";
        }
        return $epg;
    }
    function time_mysql2epg($datetime)
    {
        \preg_match("/(\\d+):(\\d+)/", $datetime, $arr);
        return $arr[0];
    }
    function handle_upload_logo($file, $ch_id)
    {
        if (empty($file)) {
            return true;
        }
        $images = ['image/gif' => 'gif', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!\array_key_exists($file['type'], $images)) {
            return false;
        }
        $ext = $images[$file['type']];
        $path = \realpath(PROJECT_PATH . '/../misc/logos/');
        if (!$path) {
            return false;
        }
        $filename = $ch_id . '.' . $ext;
        $resolutions = ['320' => ['height' => 96, 'width' => 96], '240' => ['height' => 72, 'width' => 72], '160' => ['height' => 48, 'width' => 48], '120' => ['height' => 36, 'width' => 36]];
        \umask(0);
        foreach ($resolutions as $resolution => $dimension) {
            $ico_path = $path . '/' . $resolution;
            if (!\is_dir($ico_path)) {
                \mkdir($ico_path, 0777);
            }
            $fullpath = $ico_path . '/' . $filename;
            try {
                $icon = new \Imagick($file['tmp_name']);
            } catch (\ImagickException $e) {
                return false;
            }
            if (!$icon->resizeImage($dimension['width'], $dimension['height'], \Imagick::FILTER_LANCZOS, 1)) {
                return false;
            }
            if (!$icon->writeImage($fullpath)) {
                return false;
            }
            $icon->destroy();
            \chmod($fullpath, 0666);
        }
        \unlink($file['tmp_name']);
        return $filename;
    }
    function check_number($num)
    {
        $total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->count()->where(['number' => (int) $num])->get()->counter();
        if ($total_items > 0) {
            return 0;
        }
        return 1;
    }
    function get_screen_name($addr)
    {
        \preg_match("/(\\S+)\\s(\\S+):\\/\\/(\\d+).(\\d+).(\\d+).(\\d+):(\\d+)/", $addr, $tmp_arr);
        $img_str = '/iptv/mpg/' . $tmp_arr[6] . '_' . $tmp_arr[7] . '.mpg';
        return $img_str;
    }
    function get_color($channel)
    {
        if (!$channel['enable_monitoring']) {
            return '#5588FF';
        }
        if (\time() - \strtotime($channel['monitoring_status_updated']) > 3600) {
            return '#8B8B8B';
        }
        if ($channel['monitoring_status'] == 1) {
            $disabled_link = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => $channel['id'], 'enable_monitoring' => 1, 'status' => 0])->get()->all();
            if (!empty($disabled_link)) {
                return '#f4c430';
            }
            return 'green';
        }
        return 'red';
    }
    function get_hint($channel)
    {
        if (!$channel['enable_monitoring']) {
            return \_('monitoring off');
        }
        $diff = \time() - \strtotime($channel['monitoring_status_updated']);
        if ($diff > 3600) {
            return \_('more than an hour ago');
        }
        if ($diff < 60) {
            return \_('less than a minute ago');
        }
        return \round($diff / 60) . ' ' . \_('minutes ago');
    }
    function get_genres($tv_genre_id)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_genre')->get()->all();
        $option = '';
        foreach ($genres as $arr) {
            $selected = '';
            if ($tv_genre_id == $arr['id']) {
                $selected = 'selected';
            }
            $option .= "<option value={$arr['id']} {$selected}>" . \_($arr['title']) . "\n";
        }
        return $option;
    }
    function page_bar($MAX_PAGE_ITEMS, $page, $total_pages)
    {
        $page_bar = '';
        for ($i = 1; $i <= $total_pages; ++$i) {
            if ($i - 1 != $page) {
                $page_bar .= ' <a href="?page=' . ($i - 1) . '&search=' . @$_GET['search'] . '&letter=' . @$_GET['letter'] . '&status=' . @$_GET['status'] . '">' . $i . '</a> |';
            } else {
                $page_bar .= '<b> ' . $i . ' </b>|';
            }
        }
        return $page_bar;
    }
    function set_karaoke_accessed($id, $val)
    {
        if (!$id) {
            return;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['accessed' => $val, 'added' => 'NOW()'], ['id' => $id]);
    }
    function set_karaoke_done($id, $val)
    {
        if (!$id) {
            return;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['done' => $val, 'done_time' => 'NOW()'], ['id' => $id]);
    }
    function get_karaoke_accessed($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['id' => $id])->get()->first('accessed');
    }
    function get_done_karaoke($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['id' => $id])->get()->first('done');
    }
    function get_karaoke_accessed_color($id)
    {
        if (get_karaoke_accessed($id)) {
            $color = 'green';
            $accessed = 0;
            $txt = 'on';
        } else {
            $color = 'red';
            $accessed = 1;
            $txt = 'off';
        }
        $letter = @$_GET['letter'];
        $search = @$_GET['search'];
        if (\Ministra\Lib\Admin::isActionAllowed()) {
            return "<a href='add_karaoke.php?accessed={$accessed}&id={$id}&letter=" . @$_GET['letter'] . '&search=' . @$_GET['search'] . '&page=' . @$_GET['page'] . "'><font color='{$color}'>{$txt}</font></a>";
        }
        return "<font color='{$color}'><b>{$txt}</b></font>";
    }
    function get_done_karaoke_color($id)
    {
        if (get_done_karaoke($id)) {
            $color = 'green';
            $done = 0;
            $txt = \_('done');
        } else {
            $color = 'red';
            $done = 1;
            $txt = \_('not done');
        }
        $letter = @$_GET['letter'];
        $search = @$_GET['search'];
        if (\Ministra\Lib\Admin::isActionAllowed()) {
            return "<a href='add_karaoke.php?done={$done}&id={$id}&letter=" . @$_GET['letter'] . '&search=' . @$_GET['search'] . '&page=' . @$_GET['page'] . "'><font color='{$color}'>{$txt}</font></a>";
        }
        return "<font color='{$color}'><b>{$txt}</b></font>";
    }
    function check_file($id)
    {
        $fname = $id . '.mpg';
        $color_status = get_status($id);
        if ($color_status == 1) {
            $color = 'green';
            set_status($id, 1);
        } else {
            if ($color_status == 0) {
                $color = 'red';
                set_status($id, 0);
            } else {
                if ($color_status == 2) {
                    $color = 'blue';
                }
            }
        }
        return "<font id='path_{$id}' color='{$color}'>{$fname}</font>";
    }
    function set_status($id, $val)
    {
        if (!$id) {
            return;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['status' => $val], ['id' => $id]);
    }
    function get_status($id = 0)
    {
        $karaoke = \Ministra\Lib\Karaoke::getById($id);
        if (!empty($karaoke['rtsp_url'])) {
            return 2;
        }
        return $karaoke['status'];
    }
    function get_genres_karaoke($genre_id)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke_genre')->get()->all();
        $option = '';
        foreach ($genres as $arr) {
            $selected = '';
            if ($genre_id == $arr['id']) {
                $selected = 'selected';
            }
            $option .= "<option value={$arr['id']} {$selected}>{$arr['title']}\n";
        }
        return $option;
    }
    function add_video_log($action, $video_id)
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('video_log', ['action' => $action, 'video_id' => $video_id, 'moderator_id' => $_SESSION['uid'], 'actiontime' => 'NOW()']);
    }
    function check_incoming_path($path)
    {
        if (\is_dir(INCOMING_DIR . $path)) {
        } else {
            \umask(0);
            \mkdir(INCOMING_DIR . $path, 0777);
        }
        if (\is_dir(VIDEO_STORAGE_DIR . $path)) {
        } else {
            \umask(0);
            \mkdir(VIDEO_STORAGE_DIR . $path, 0777);
        }
    }
    function del_incoming_path($path)
    {
        return @\rmdir(INCOMING_DIR . $path);
    }
    function check_video_status($id)
    {
        $video = \Ministra\Lib\Video::getById($id);
        if (!empty($video['rtsp_url'])) {
            return 2;
        }
        return $video['status'];
    }
    function count_series($series)
    {
        return \count(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($series));
    }
    function get_path_color($id, $path)
    {
        $color_status = check_video_status($id);
        if ($color_status == 1) {
            $color = 'green';
        } else {
            if ($color_status == 0) {
                $color = 'red';
            } else {
                if ($color_status == 2) {
                    $color = 'blue';
                } else {
                    if ($color_status == 3) {
                        $color = '#f4c430';
                    }
                }
            }
        }
        return "<span id='path_{$id}' style='color:" . $color . "'>{$path}</span>";
    }
    function get_accessed($id)
    {
        $video = \Ministra\Lib\Video::getById($id);
        return $video['accessed'];
    }
    function get_accessed_color($id)
    {
        if (get_accessed($id)) {
            $color = 'green';
            $accessed = 0;
            $txt = 'on';
        } else {
            $color = 'red';
            $accessed = 1;
            $txt = 'off';
        }
        $hint = '';
        $date_on = '';
        $letter = @$_GET['letter'];
        $search = @$_GET['search'];
        if (\Ministra\Lib\Admin::isActionAllowed()) {
            if ($accessed) {
                $class = 'switch_button';
                $video_on_task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_on_tasks')->where(['video_id' => $id])->get()->first();
                if (!empty($video_on_task)) {
                    $color = 'orange';
                    $hint = \sprintf(\_('turn on %s'), $video_on_task['date_on']);
                    $date_on = \date('d-m-Y', \strtotime($video_on_task['date_on']));
                }
            } else {
                $class = '';
            }
            return "<a class='{$class}' title='{$hint}' video-id='{$id}' date-on='{$date_on}' " . "href='add_video.php?accessed={$accessed}&id={$id}&letter=" . @$_GET['letter'] . '&search=' . @$_GET['search'] . '&page=' . @$_GET['page'] . "'><font color='{$color}'>{$txt}</font></a>";
        }
        return "<font color='{$color}'><b>{$txt}</b></font>";
    }
    function get_genres_video()
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('genre')->orderby('title')->get()->all();
        $str = 'var all_genres = [ ';
        foreach ($genres as $arr) {
            $str .= '{ id : ' . $arr['id'] . ", title : '" . \_($arr['title']) . "'},";
        }
        $str = \substr($str, 0, \strlen($str) - 1);
        $str .= ' ]';
        return $str;
    }
    function get_selected_genres()
    {
        if (empty($_GET['id'])) {
            return '';
        }
        $genre_id = [];
        $id = (int) $_GET['id'];
        $video = \Ministra\Lib\Video::getById($id);
        $genre_id[1] = $video['genre_id_1'] ? $video['genre_id_1'] : 0;
        $genre_id[2] = $video['genre_id_2'] ? $video['genre_id_2'] : 0;
        $genre_id[3] = $video['genre_id_3'] ? $video['genre_id_3'] : 0;
        $genre_id[4] = $video['genre_id_4'] ? $video['genre_id_4'] : 0;
        $k = 0;
        for ($i = 1; $i <= 4; ++$i) {
            if ($genre_id[$i] > 0) {
                ++$k;
            }
        }
        $str = 'var sel_genre_id_1 = ' . $genre_id[1] . "\n";
        $str .= 'var sel_genre_id_2 = ' . $genre_id[2] . "\n";
        $str .= 'var sel_genre_id_3 = ' . $genre_id[3] . "\n";
        $str .= 'var sel_genre_id_4 = ' . $genre_id[4] . "\n";
        $str .= 'var total_genres   = ' . $k . "\n";
        return $str;
    }
    function get_categories()
    {
        $categories = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->orderby('num')->get()->all();
        $str = 'var all_categories = [ ';
        foreach ($categories as $arr) {
            $str .= '{ id : ' . $arr['id'] . ", name : '" . \_($arr['category_name']) . "'},";
        }
        $str = \substr($str, 0, \strlen($str) - 1);
        $str .= ' ]';
        return $str;
    }
    function get_selected_cat_genres()
    {
        $cat_genre_id = [];
        if (!empty($_GET['id'])) {
            $video = \Ministra\Lib\Video::getById((int) $_GET['id']);
            $cat_genre_id[1] = $video['cat_genre_id_1'] ? $video['cat_genre_id_1'] : 0;
            $cat_genre_id[2] = $video['cat_genre_id_2'] ? $video['cat_genre_id_2'] : 0;
            $cat_genre_id[3] = $video['cat_genre_id_3'] ? $video['cat_genre_id_3'] : 0;
            $cat_genre_id[4] = $video['cat_genre_id_4'] ? $video['cat_genre_id_4'] : 0;
            $k = 0;
            for ($i = 1; $i <= 4; ++$i) {
                if ($cat_genre_id[$i] > 0) {
                    ++$k;
                }
            }
            $str = 'var sel_cat_genre_id_1 = ' . $cat_genre_id[1] . "\n";
            $str .= 'var sel_cat_genre_id_2 = ' . $cat_genre_id[2] . "\n";
            $str .= 'var sel_cat_genre_id_3 = ' . $cat_genre_id[3] . "\n";
            $str .= 'var sel_cat_genre_id_4 = ' . $cat_genre_id[4] . "\n";
            $str .= 'var total_cat_genres   = ' . $k . "\n";
        } else {
            $str = "var sel_cat_genre_id_1\n";
            $str .= "var sel_cat_genre_id_2\n";
            $str .= "var sel_cat_genre_id_3\n";
            $str .= "var sel_cat_genre_id_4\n";
        }
        return $str;
    }
    function send_button($id)
    {
        $task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderator_tasks')->where(['ended' => 0, 'media_id' => $id])->get()->first();
        if (!empty($task)) {
            return "<a href='msgs.php?task=" . $task['id'] . "'><font color='#CBCB00'>" . \_('task') . "</font></a>&nbsp;&nbsp;\n";
        }
        return "<a href='send_to.php?id=" . $id . "'>" . \_('send') . "</a>&nbsp;&nbsp;\n";
    }
    function get_album_genres($album_id)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_genres.name')->from('audio_genre')->where(['album_id' => $album_id])->join('audio_genres', 'audio_genre.genre_id', 'audio_genres.id', 'LEFT')->orderby('audio_genres.name')->get()->all('name');
        return \array_map(function ($genre) {
            return \_($genre);
        }, $genres);
    }
    function count_album_tracks($album_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['album_id' => $album_id])->count()->get()->counter();
    }
    function get_album_languages($album_id)
    {
        $languages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_languages.name')->from('audio_compositions')->where(['album_id' => $album_id])->join('audio_languages', 'audio_compositions.language_id', 'audio_languages.id', 'LEFT')->orderby('audio_languages.name')->groupby('audio_languages.name')->get()->all('name');
        return $languages;
    }
    function get_video_name($id)
    {
        $video = \Ministra\Lib\Video::getById($id);
        return $video['name'];
    }
    function count_storages($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('storage_cache')->where(['status' => 1, 'media_type' => 'vclub', 'media_id' => $id])->get()->counter();
    }
    function get_total_tasks($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('moderator_tasks')->where(['to_usr' => $uid, 'archived' => 0])->get()->counter();
    }
    function get_open_tasks($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('moderator_tasks')->where(['ended' => 0, 'to_usr' => $uid, 'archived' => 0])->get()->counter();
    }
    function get_closed_tasks($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('moderator_tasks')->where(['ended' => 1, 'to_usr' => $uid, 'archived' => 0])->get()->counter();
    }
    function get_rejected_tasks($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('moderator_tasks')->where(['rejected' => 1, 'to_usr' => $uid, 'archived' => 0])->get()->counter();
    }
    function get_mac_by_id()
    {
        $stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::z55bad8d1e166d966765492584ab3ab41((int) $_GET['id']);
        return $stb['mac'];
    }
    function isset_date()
    {
        if (@$_GET['yy'] && @$_GET['mm'] && @$_GET['dd']) {
            return 1;
        }
        return 0;
    }
    function get_last_time($time)
    {
        $time_ts = datetime2timestamp($time);
        $time_now = \time();
        $time_delta_s = $time_now - $time_ts;
        $str = '';
        $hh = \floor($time_delta_s / 3600);
        $ii = \floor(($time_delta_s - $hh * 3600) / 60);
        if ($ii < 10) {
            $ii = '0' . $ii;
        }
        $ss = $time_delta_s - $hh * 3600 - $ii * 60;
        if ($ss < 10) {
            $ss = '0' . $ss;
        }
        $str = $hh . ':' . $ii . ':' . $ss;
        return $str;
    }
    function get_cur_media($media_id)
    {
        $media = [0 => '--', 1 => \_('TV'), 2 => \_('Video'), 3 => \_('Karaoke'), 4 => \_('Audio'), 5 => \_('Radio'), 6 => \_('My records'), 7 => \_('Records'), 9 => 'ad', 10 => \_('Media browser'), 11 => \_('Tv archive'), 12 => \_('Records'), 14 => \_('TimeShift'), 20 => \_('Infoportal'), 21 => \_('Infoportal'), 22 => \_('Infoportal'), 23 => \_('Infoportal'), 24 => \_('Infoportal'), 25 => \_('Infoportal')];
        if (!empty($media[$media_id])) {
            return $media[$media_id];
        }
        return 'media_id: ' . $media_id;
    }
    function construct_HH()
    {
        if (!@$_GET['hh']) {
            $hour = \date('H');
        } else {
            $hour = @$_GET['hh'];
        }
        for ($i = 0; $i <= 24; ++$i) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            if ($i == $hour) {
                $hour .= "<option value='" . $i . "' selected>" . $i . '</option>';
            } else {
                $hour .= "<option value='" . $i . "'>" . $i . '</option>';
            }
        }
        return $hour;
    }
    function construct_II()
    {
        if (!@$_GET['ii']) {
            $minutes = \date('i');
        } else {
            $minutes = @$_GET['ii'];
        }
        $opt = '';
        for ($i = 0; $i <= 59; ++$i) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            if ($i == $minutes) {
                $opt .= "<option value='" . $i . "' selected>" . $i . "</option>\n";
            } else {
                $opt .= "<option value='" . $i . "'>" . $i . "</option>\n";
            }
        }
        return $opt;
    }
    function construct_time()
    {
        if (@$_GET['yy'] && @$_GET['mm'] && @$_GET['dd'] && @$_GET['hh'] && @$_GET['ii']) {
            $time = $_GET['yy'] . '-' . $_GET['mm'] . '-' . $_GET['dd'] . ' ' . $_GET['hh'] . ':' . $_GET['ii'];
            return $time;
        }
        return 0;
    }
    function parse_param($action, $param)
    {
        if ($action == 'play') {
            $sub_param = \substr($param, 0, 3);
            if ($sub_param == 'rtp') {
                $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['cmd' => $param])->get()->first();
                $name = '[' . \_('Channel') . '] ' . $channel['name'];
            } else {
                if ($sub_param == 'aut') {
                    \preg_match("/(\\d+)\\.[a-z]*\$/", $param, $tmp_arr);
                    $media_id = $tmp_arr[1];
                    $video = \Ministra\Lib\Video::getById($media_id);
                    $name = '[' . \_('Video') . '] ' . $video['name'];
                } else {
                    $name = '';
                }
            }
        } else {
            if ($param == '""') {
                $name = '';
            } else {
                $name = $param;
            }
        }
        return $name;
    }
    function cut_off_user($id)
    {
        $status = get_user_status($id);
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($id);
        if ($status == 1) {
            set_user_status($id, 0);
            $event->sendCutOn();
        } else {
            set_user_status($id, 1);
            $event->sendCutOff();
        }
    }
    function get_user_color($id)
    {
        $status = get_user_status($id);
        $str = '';
        if ($status == 0) {
            $str = '<font color="green">On</font>';
        } else {
            if ($status == 1) {
                $str = '<font color="red">Off</font>';
            } else {
                $str = '<font color="grey">Unknown</font>';
            }
        }
        return $str;
    }
    function get_user_status($id)
    {
        $stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::z55bad8d1e166d966765492584ab3ab41($id);
        return $stb['status'];
    }
    function set_user_status($id, $status)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['status' => $status, 'last_change_status' => 'NOW()'], ['id' => $id]);
    }
    function get_media_name_by_id($id, $type = 'vclub')
    {
        if ($type == 'itv') {
            $table = 'itv';
        } elseif ($type == 'karaoke') {
            $table = 'karaoke';
        } else {
            $table = 'video';
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from($table)->where(['id' => $id])->get()->first('name');
    }
    function get_online_users()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->get()->counter();
    }
    function get_offline_users()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['UNIX_TIMESTAMP(keep_alive)<=' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->get()->counter();
    }
    function set_karaoke_returned($id, $val, $txt)
    {
        if (!$id) {
            return;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['returned' => $val, 'reason' => $txt, 'done' => (int) (!$val)], ['id' => $id]);
    }
    function return_karaoke($id, $returned, $reason)
    {
        if ($returned == 1) {
            $txt = \_('returned');
            $color = 'red';
            $returned = 0;
        } else {
            $txt = \_('return');
            $color = '#CBCB00';
            $returned = 1;
        }
        if (\Ministra\Lib\Admin::isPageActionAllowed()) {
            $str = "<a href='#' ";
            if ($returned == 0) {
                $str .= "title='{$reason}'";
            }
            $str .= " onclick='reason = prompt(\"" . \_('Return reason') . ':"); if(reason){document.location = "last_closed_karaoke.php?returned=' . "{$returned}&id={$id}&uid=" . @$_GET['id'] . "&reason=\"+reason} '><font color='{$color}'>{$txt}</font></a>";
            return $str;
        }
        $str = "<a href='#' ";
        if ($returned == 0) {
            $str .= "title='{$reason}'";
        }
        $str .= " ><font color='{$color}'>{$txt}</font></a>";
        return $str;
    }
    function get_cost_sub_channels()
    {
        $sub_ch = get_sub_channels();
        if (\count($sub_ch) > 0) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('SUM(cost) as total_cost')->from('itv')->in('id', $sub_ch)->get()->first('total_cost');
        }
        return 0;
    }
    function additional_services_btn($id)
    {
        $stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::z55bad8d1e166d966765492584ab3ab41($id);
        $additional_services_on = $stb['additional_services_on'];
        if ($additional_services_on == 0) {
            $color = 'red';
            $txt = \_('Disabled');
            $set = 1;
        } else {
            $color = 'green';
            $txt = \_('Enabled');
            $set = 0;
        }
        return '<a href="profile.php?id=' . $id . '&set_services=' . $set . '" style="color:' . $color . '"><b>' . $txt . '</b></a>';
    }
    function get_moderators()
    {
        $opt = '';
        $moderators = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->get();
        while ($arr = $moderators->next()) {
            $opt .= "<option value={$arr['id']}>{$arr['login']}\n";
        }
        return $opt;
    }
    function get_sended_video()
    {
        $video = \Ministra\Lib\Video::getById((int) $_GET['id']);
        return $video['name'];
    }
    function get_users_count_in_package($package)
    {
        $count = 0;
        $tariff_plans_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('package_in_plan')->where(['optional' => 0, 'package_id' => $package['id']])->get()->all('plan_id');
        $tariff_plans = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->in('id', $tariff_plans_ids)->get()->all();
        foreach ($tariff_plans as $tariff) {
            $count += get_users_count_in_tariff($tariff);
        }
        $count += \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_package_subscription')->where(['package_id' => $package['id']])->count()->get()->counter();
        return $count;
    }
    function get_users_count_in_tariff($tariff)
    {
        $tariff_ids = [$tariff['id']];
        if ($tariff['user_default'] == 1) {
            $tariff_ids[] = 0;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->in('tariff_plan_id', $tariff_ids)->get()->counter();
    }
    function get_open_karaoke($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('karaoke')->where(['add_by' => $uid, 'archived' => 0])->get()->counter();
    }
    function get_closed_karaoke($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('karaoke')->where(['add_by' => $uid, 'archived' => 0, 'accessed' => 1, 'status' => 1, 'done' => 1])->get()->counter();
    }
    function count_rate($sarr)
    {
        if (\is_array($rate = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($sarr))) {
            return \array_sum($rate);
        }
        return 0;
    }
    function get_status_video($id)
    {
        $video = \Ministra\Lib\Video::getById($id);
        return $video['status'];
    }
    function get_status_color($id)
    {
        if (get_status($id)) {
            $color = 'green';
        } else {
            $color = 'red';
        }
        return $color;
    }
    function time_to_hhmm($time)
    {
        if ($time < 0) {
            return '----';
        }
        $hh = \floor($time / 60);
        $mm = $time - $hh * 60;
        if ($hh < 10) {
            $hh = '0' . $hh;
        }
        if ($mm < 10) {
            $mm = '0' . $mm;
        }
        return $hh . ':' . $mm;
    }
    function get_all_channels_opt($sub_ch, $bonus_ch)
    {
        $opt = '';
        $total_arr = \array_merge($sub_ch, $bonus_ch);
        if (\count($total_arr) > 0) {
            $all_sub_str = \implode(',', $total_arr);
            $sql = "select * from itv where base_ch=0 and id not in ({$all_sub_str})";
        } else {
            $sql = 'select * from itv where base_ch=0';
        }
        $channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
        while ($arr = $channels->next()) {
            $opt .= "<option value={$arr['id']}>{$arr['number']}. {$arr['name']}\n";
        }
        return $opt;
    }
    function get_sub_channels_opt($sub_ch)
    {
        $opt = '';
        if (\count($sub_ch) > 0) {
            $channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0])->in('id', $sub_ch)->get();
            while ($arr = $channels->next()) {
                $opt .= "<option value={$arr['id']}>{$arr['number']}. {$arr['name']}\n";
            }
            return $opt;
        }
        return '';
    }
    function get_bonus_channels_opt($bonus_ch)
    {
        global $bonus_ch;
        $opt = '';
        if (\count($bonus_ch) > 0) {
            $channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0])->in('id', $bonus_ch)->get();
            while ($arr = $channels->next()) {
                $opt .= "<option value={$arr['id']}>{$arr['number']}. {$arr['name']}\n";
            }
            return $opt;
        }
        return '';
    }
    function get_service_id_map()
    {
        $arr = [];
        $channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->get();
        while ($channel = $channels->next()) {
            $service_id = $channel['service_id'];
            if (\strlen($service_id) == 5) {
                $arr[$service_id] = $channel['id'];
            } elseif (\strlen($service_id) == 11) {
                $ids = \explode(' ', $service_id);
                foreach ($ids as $id) {
                    $arr[$id] = $channel['id'];
                }
            }
        }
        return $arr;
    }
    function get_subscription_map()
    {
        $arr = [];
        $itv_subscription = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->get();
        while ($item = $itv_subscription->next()) {
            $arr[$item['uid']] = $item['id'];
        }
        return $arr;
    }
    function get_stb_id_map()
    {
        $arr = [];
        $users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->get();
        while ($user = $users->next()) {
            $arr[$user['mac']] = $user['id'];
        }
        return $arr;
    }
    function get_all_ch_bonus()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['bonus_ch' => 1, 'base_ch' => 0])->get()->all('id');
    }
    function get_base_channels()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 1])->get()->all('id');
    }
    function get_all_hd_channels()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['hd' => 1])->get()->all('id');
    }
    function get_all_payed_ch()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0, 'hd' => 0])->not_in('id', [270, 271, 272, 273, 274, 275])->get()->all('id');
    }
    function get_all_payed_ch_discovery()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0, 'hd' => 0])->get()->all('id');
    }
    function get_all_payed_ch_100()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['base_ch' => 0, 'hd' => 0])->not_in('id', [178, 179, 270, 271, 272, 273, 274, 275])->get()->all('id');
    }
    function _log($str)
    {
        global $log;
        $log .= $str . "<br>\n";
    }
    function get_video_color($video)
    {
        $colors = [0 => 'red', 1 => 'green', 2 => '', 3 => '#d8a903'];
        if (!empty($colors[$video['status']])) {
            return $colors[$video['status']];
        }
        return '';
    }
    function parse_param_user_log($action, $param, $type)
    {
        if ($action == 'play') {
            switch ($type) {
                case 1:
                    $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['cmd' => $param, 'status' => 1])->get()->first();
                    if (!empty($channel)) {
                        $ch_name = $channel['name'];
                    } else {
                        $ch_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Channel') . '] ' . $ch_name;
                    break;
                case 2:
                    if (!\preg_match("/(\\d+)\\.[a-z0-9]*/", $param, $tmp_arr)) {
                        $name = '[' . \_('Video') . '] ' . $param;
                        break;
                    }
                    $media_id = $tmp_arr[1];
                    $video = \Ministra\Lib\Video::getById($media_id);
                    if (!empty($video)) {
                        $video_name = $video['name'];
                    } else {
                        $video_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Video') . '] ' . $video_name;
                    break;
                case 3:
                    if (\preg_match("/(\\d+)\\.[a-z]*\$/", $param, $tmp_arr)) {
                        $karaoke_id = $tmp_arr[1];
                        $karaoke = \Ministra\Lib\Karaoke::getById($karaoke_id);
                    }
                    if (!empty($karaoke)) {
                        $karaoke_name = $karaoke['name'];
                    } else {
                        $karaoke_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Karaoke') . '] ' . $karaoke_name;
                    break;
                case 4:
                    \preg_match("/(\\d+).mp3\$/", $param, $tmp_arr);
                    $audio_id = $tmp_arr[1];
                    $audio = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio')->where(['id' => $audio_id])->get()->first();
                    if (!empty($audio)) {
                        $audio_name = $audio['name'];
                    } else {
                        $audio_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Audio') . '] ' . $audio_name;
                    break;
                case 5:
                    $radio = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['cmd' => $param, 'status' => 1])->get()->first();
                    if (empty($radio)) {
                        $ch_name = $radio['name'];
                    } else {
                        $ch_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Radio') . '] ' . $ch_name;
                    break;
                case 6:
                    \preg_match("/\\/(\\d+).mpg/", $param, $tmp_arr);
                    $my_record_id = $tmp_arr[1];
                    $record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('t_start, itv.name')->from('users_rec')->join('itv', 'users_rec.ch_id', 'itv.id', 'INNER')->where(['users_rec.id' => $my_record_id])->get()->first();
                    if (!empty($record)) {
                        $my_record_name = $record['t_start'] . ' ' . $record['name'];
                    } else {
                        $my_record_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('My records') . '] ' . $my_record_name;
                    break;
                case 7:
                    \preg_match("/(\\d+).mpg\$/", $param, $tmp_arr);
                    $shared_record_id = $tmp_arr[1];
                    $record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_records')->where(['id' => $shared_record_id])->get()->first();
                    if (!empty($record)) {
                        $shared_record_name = $record['descr'];
                    } else {
                        $shared_record_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Records') . '] ' . $shared_record_name;
                    break;
                case 8:
                    \preg_match("/(\\d+).mpg\$/", $param, $tmp_arr);
                    $media_id = $tmp_arr[1];
                    $video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_clips')->where(['id' => $media_id])->get()->first();
                    if (!empty($video)) {
                        $video_name = $video['name'];
                    } else {
                        $video_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Clip') . '] ' . $video_name;
                    break;
                case 11:
                    $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['cmd' => $param, 'status' => 1])->get()->first();
                    if (!empty($channel)) {
                        $ch_name = $channel['name'];
                    } else {
                        $ch_name = \htmlspecialchars($param);
                    }
                    $name = '[' . \_('Archive') . '] ' . $ch_name;
                    break;
                default:
                    $name = 'unknown media';
            }
        } else {
            if ($param == '""') {
                $name = '';
            } else {
                $name = \htmlspecialchars($param);
            }
        }
        return $name;
    }
    function add_where(&$where, $str)
    {
        if ($where) {
            $where .= " and {$str}";
        } else {
            $where .= " where {$str}";
        }
    }
    function merge_services($list1, $list2)
    {
        if (empty($list1)) {
            $list1 = [];
        }
        if (empty($list2)) {
            $list2 = [];
        }
        return \array_merge($list1, $list2);
    }
    function get_bonus1()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['bonus_ch' => 1, 'cost!=' => 99])->get()->all('id');
    }
    function get_bonus2()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['cost' => 99])->get()->all('id');
    }
    function parse_param_video_log($action, $param)
    {
        if ($action == 'play()' || $action == 'play_not_to()') {
            $sub_param = \substr($param, 0, 3);
            if ($sub_param == 'rtp') {
                $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['cmd' => $param])->get()->first();
                $name = '[' . \_('Channel') . '] ';
                if (!empty($channel)) {
                    $name .= $channel['name'];
                } else {
                    $name .= 'undefined';
                }
            } else {
                if ($sub_param == 'aut') {
                    \preg_match("/auto \\/media\\/(\\d+)\\.[a-z]*\$/", $param, $tmp_arr);
                    $media_id = $tmp_arr[1];
                    $video = \Ministra\Lib\Video::getById($media_id);
                    $name = '[' . \_('Video') . '] ';
                    if (!empty($video)) {
                        $name .= $video['name'];
                    }
                } else {
                    $name = '';
                }
            }
        } else {
            if ($param == '""') {
                $name = '';
            } else {
                $name = $param;
            }
        }
        return $name;
    }
    function check_number_radio($num)
    {
        $radio = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['number' => (int) $num])->get()->first();
        return empty($radio);
    }
    function handle_upload_logo_itv($file, $album_id)
    {
        if (empty($file)) {
            return true;
        }
        $images = ['image/gif' => 'gif', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        if (!\array_key_exists($file['type'], $images)) {
            return false;
        }
        $ext = $images[$file['type']];
        $path = \realpath(PROJECT_PATH . '/../misc/audio_covers/');
        if (!$path) {
            return false;
        }
        $filename = $album_id . '.' . $ext;
        \umask(0);
        $subfolder = \ceil($album_id / 100);
        $path = $path . '/' . $subfolder;
        if (!\is_dir($path)) {
            \mkdir($path, 0755);
        }
        $fullpath = $path . '/' . $filename;
        try {
            $icon = new \Imagick($file['tmp_name']);
        } catch (\ImagickException $e) {
            return false;
        }
        if (!$icon->resizeImage(240, 240, \Imagick::FILTER_LANCZOS, 1)) {
            return false;
        }
        if (!$icon->writeImage($fullpath)) {
            return false;
        }
        $icon->destroy();
        \chmod($fullpath, 0644);
        \unlink($file['tmp_name']);
        return $filename;
    }
}
