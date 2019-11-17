<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\FlussonicTvArchive;
use Ministra\Lib\Itv;
use Ministra\Lib\RemotePvr;
use Ministra\Lib\StreamServer;
use Ministra\Lib\TvArchive;
use Ministra\Lib\WowzaTvArchive;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (@$_GET['del'] && !empty($_GET['id'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    $tv_archive = new \Ministra\Lib\TvArchive();
    $tv_archive->deleteTasks((int) $_GET['id']);
    $ch_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => (int) $_GET['id']])->get()->all();
    foreach ($ch_links as $ch_link) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching(['ch_links.id=' . (int) $ch_link['id']])->delete('ch_links', ['id' => (int) $ch_link['id']]);
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching(['itv.id=' . (int) $_GET['id']])->delete('itv', ['id' => (int) $_GET['id']]);
    if (!empty($ch_links)) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('deleted_channels', ['ch_id' => (int) $_GET['id'], 'deleted' => 'NOW()']);
    }
    \header('Location: add_itv.php');
    exit;
}
if (isset($_GET['status']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching(['itv.id=' . (int) @$_GET['id']])->update('itv', ['status' => (int) @$_GET['status'], 'modified' => 'NOW()'], ['id' => (int) @$_GET['id']]);
    \header('Location: add_itv.php');
    exit;
}
if (isset($_GET['shift']) && isset($_GET['from_num'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $direction = (int) $_GET['shift'];
    $from_num = (int) $_GET['from_num'];
    $channel_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['number>=' => $from_num])->get()->all('id');
    if ($direction > 0) {
        $direction = '+' . $direction;
    }
    if (!empty($channel_ids)) {
        $query = 'update itv set number=number' . $direction . ' where id in (' . \implode(', ', $channel_ids) . ')';
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query);
    }
    \header('Location: add_itv.php');
    exit;
}
if (@$_GET['restart_all_archives']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_PAGE_ACTION);
    $tv_archive = new \Ministra\Lib\TvArchive();
    $result = \true;
    $current_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('ch_id, storage_name')->from('tv_archive')->get()->all();
    $new_tasks = [];
    foreach ($current_tasks as $task) {
        $new_tasks[$task['ch_id']][] = $task['storage_name'];
    }
    foreach (\array_keys($new_tasks) as $channel) {
        $tv_archive->deleteTasks($channel);
        $result = $tv_archive->createTasks($channel, $new_tasks[$channel]) && $result;
    }
    if (!$result) {
        $error = \_('TV Archive has NOT been restarted correctly.');
    } else {
        $error = \_('TV Archive has been restarted.');
    }
}
if (!$error) {
    if (@$_POST['censored'] == 'on') {
        $censored = 1;
    } else {
        $censored = 0;
    }
    if (@$_POST['use_http_tmp_link'] == 'on') {
        $use_http_tmp_link = 1;
    } else {
        $use_http_tmp_link = 0;
    }
    if (@$_POST['wowza_tmp_link'] == 'on') {
        $wowza_tmp_link = 1;
    } else {
        $wowza_tmp_link = 0;
    }
    if (@$_POST['nginx_secure_link'] == 'on') {
        $nginx_secure_link = 1;
    } else {
        $nginx_secure_link = 0;
    }
    if (@$_POST['wowza_dvr'] == 'on') {
        $wowza_dvr = 1;
    } else {
        $wowza_dvr = 0;
    }
    if (@$_POST['flussonic_dvr'] == 'on') {
        $flussonic_dvr = 1;
    } else {
        $flussonic_dvr = 0;
    }
    if (@$_POST['wowza_dvr'] == 'on') {
        $wowza_dvr = 1;
    } else {
        $wowza_dvr = 0;
    }
    if (@$_POST['enable_tv_archive'] == 'on') {
        $enable_tv_archive = 1;
    } else {
        $enable_tv_archive = 0;
    }
    $storage_names = empty($_POST['storage_names']) ? [] : $_POST['storage_names'];
    $allow_pvr = @(int) $_POST['allow_pvr'];
    if (empty($storage_names)) {
        $enable_tv_archive = 0;
        $flussonic_dvr = 0;
        $wowza_dvr = 0;
    }
    $pvr_storage_names = empty($_POST['pvr_storage_names']) ? [] : $_POST['pvr_storage_names'];
    if (empty($pvr_storage_names)) {
        $allow_pvr = 0;
    }
    $enable_monitoring = @(int) $_POST['enable_monitoring'];
    $allow_local_pvr = @(int) $_POST['allow_local_pvr'];
    $allow_local_timeshift = @(int) $_POST['allow_local_timeshift'];
    $enable_wowza_load_balancing = @(int) $_POST['enable_wowza_load_balancing'];
    if (@$_POST['base_ch'] == 'on') {
        $base_ch = 1;
    } else {
        $base_ch = 0;
    }
    if (@$_POST['bonus_ch'] == 'on') {
        $bonus_ch = 1;
    } else {
        $bonus_ch = 0;
    }
    if (@$_POST['hd'] == 'on') {
        $hd = 1;
    } else {
        $hd = 0;
    }
    if (@$_POST['number'] && !\Ministra\OldAdmin\check_number($_POST['number']) && !@$_GET['update']) {
        $error = \sprintf(\_('Error: channel with number "%s" is already in use') . ' <a href="#form">#</a>', (int) $_POST['number']);
    }
    $urls = empty($_POST['cmd']) ? [] : $_POST['cmd'];
    $links = [];
    foreach ($urls as $key => $value) {
        if (empty($value)) {
            continue;
        }
        $links[] = ['url' => $value, 'priority' => \array_key_exists($key, $_POST['priority']) ? (int) $_POST['priority'][$key] : 0, 'use_http_tmp_link' => !empty($_POST['use_http_tmp_link']) && \array_key_exists($key, $_POST['use_http_tmp_link']) ? (int) $_POST['use_http_tmp_link'][$key] : 0, 'wowza_tmp_link' => !empty($_POST['wowza_tmp_link']) && \array_key_exists($key, $_POST['wowza_tmp_link']) ? (int) $_POST['wowza_tmp_link'][$key] : 0, 'flussonic_tmp_link' => !empty($_POST['flussonic_tmp_link']) && \array_key_exists($key, $_POST['flussonic_tmp_link']) ? (int) $_POST['flussonic_tmp_link'][$key] : 0, 'xtream_codes_support' => !empty($_POST['xtream_codes_support']) && \array_key_exists($key, $_POST['xtream_codes_support']) && (\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so')) ? (int) $_POST['xtream_codes_support'][$key] : 0, 'nginx_secure_link' => !empty($_POST['nginx_secure_link']) && \array_key_exists($key, $_POST['nginx_secure_link']) ? (int) $_POST['nginx_secure_link'][$key] : 0, 'user_agent_filter' => \array_key_exists($key, $_POST['user_agent_filter']) ? $_POST['user_agent_filter'][$key] : '', 'monitoring_url' => \array_key_exists($key, $_POST['monitoring_url']) ? $_POST['monitoring_url'][$key] : '', 'use_load_balancing' => !empty($_POST['stream_server']) && \array_key_exists($key, $_POST['stream_server']) && !empty($_POST['use_load_balancing']) && \array_key_exists($key, $_POST['use_load_balancing']) ? (int) $_POST['use_load_balancing'][$key] : 0, 'enable_monitoring' => !empty($_POST['enable_monitoring']) && \array_key_exists($key, $_POST['enable_monitoring']) ? (int) $_POST['enable_monitoring'][$key] : 0, 'enable_balancer_monitoring' => !empty($_POST['enable_balancer_monitoring']) && \array_key_exists($key, $_POST['enable_balancer_monitoring']) ? (int) $_POST['enable_balancer_monitoring'][$key] : 0, 'stream_servers' => !empty($_POST['stream_server']) && \array_key_exists($key, $_POST['stream_server']) ? $_POST['stream_server'][$key] : []];
    }
    $mc_cmd = @$_POST['mc_cmd'];
    if (empty($mc_cmd)) {
        foreach ($links as $link) {
            if ((\strpos($link['url'], 'rtp://') !== \false || \strpos($link['url'], 'udp://') !== \false || \strpos($link['url'], 'http://') !== \false) && \preg_match("/(\\S+:\\/\\/\\S+)/", $link['url'], $match)) {
                $mc_cmd = $match[1];
                break;
            }
        }
    }
    if (@$_GET['save'] && !$error && !empty($_POST)) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
        if (@$_GET['name'] && @$_POST['tv_genre_id'] > 0) {
            $ch_id = (int) @$_GET['id'];
            $channel = \Ministra\Lib\Itv::getChannelById($ch_id);
            if (!empty($channel) && $channel['enable_tv_archive'] != $enable_tv_archive || $channel['wowza_dvr'] != $wowza_dvr || $channel['flussonic_dvr'] != $flussonic_dvr) {
                if ($channel['enable_tv_archive']) {
                    if ($channel['flussonic_dvr']) {
                        $archive = new \Ministra\Lib\FlussonicTvArchive();
                    } elseif ($channel['wowza_dvr']) {
                        $archive = new \Ministra\Lib\WowzaTvArchive();
                    } else {
                        $archive = new \Ministra\Lib\TvArchive();
                    }
                    $archive->deleteTasks($ch_id);
                }
            }
            $ch_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('itv', ['name' => $_POST['name'], 'number' => $_POST['number'], 'use_http_tmp_link' => $use_http_tmp_link, 'wowza_tmp_link' => $wowza_tmp_link, 'nginx_secure_link' => $nginx_secure_link, 'wowza_dvr' => $wowza_dvr, 'flussonic_dvr' => $flussonic_dvr, 'censored' => $censored, 'base_ch' => $base_ch, 'bonus_ch' => $bonus_ch, 'hd' => $hd, 'cost' => @$_POST['cost'], 'cmd' => !empty($_POST['cmd'][0]) ? $_POST['cmd'][0] : '', 'cmd_1' => @$_POST['cmd_1'], 'cmd_2' => @$_POST['cmd_2'], 'cmd_3' => @$_POST['cmd_3'], 'mc_cmd' => $mc_cmd, 'enable_wowza_load_balancing' => $enable_wowza_load_balancing, 'enable_tv_archive' => $enable_tv_archive, 'allow_pvr' => $allow_pvr, 'allow_local_pvr' => $allow_local_pvr, 'allow_local_timeshift' => $allow_local_timeshift, 'enable_monitoring' => $enable_monitoring, 'descr' => @$_POST['descr'], 'tv_genre_id' => @$_POST['tv_genre_id'], 'status' => 1, 'xmltv_id' => @$_POST['xmltv_id'], 'service_id' => \trim($_POST['service_id']), 'volume_correction' => (int) $_POST['volume_correction'], 'correct_time' => (int) $_POST['correct_time'], 'modified' => 'NOW()', 'added' => 'NOW()', 'tv_archive_duration' => $_POST['tv_archive_duration']])->insert_id();
            foreach ($links as $link) {
                $link['ch_id'] = $ch_id;
                $links_on_server = $link['stream_servers'];
                unset($link['stream_servers']);
                $link_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_links', $link)->insert_id();
                if ($link_id && $links_on_server) {
                    foreach ($links_on_server as $streamer_id) {
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_link_on_streamer', ['link_id' => $link_id, 'streamer_id' => $streamer_id]);
                    }
                }
            }
            if ($enable_tv_archive) {
                if (!empty($_POST['flussonic_dvr'])) {
                    $archive = new \Ministra\Lib\FlussonicTvArchive();
                } elseif (!empty($_POST['wowza_dvr'])) {
                    $archive = new \Ministra\Lib\WowzaTvArchive();
                } else {
                    $archive = new \Ministra\Lib\TvArchive();
                }
                $archive->createTasks($ch_id, $storage_names);
            }
            if ($allow_pvr) {
                \Ministra\Lib\RemotePvr::setAllowedStoragesForChannel($ch_id, $pvr_storage_names);
            }
            if (!empty($_FILES['logo'])) {
                if ($logo = \Ministra\OldAdmin\handle_upload_logo_itv($_FILES['logo'], $ch_id)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['logo' => $logo], ['id' => $ch_id]);
                } else {
                    $error = \_('Error: could not save logo') . ' <a href="#form">#</a>';
                }
            }
            \header('Location: add_itv.php');
            exit;
        }
        $error = \_('Error: all fields are required') . ' <a href="#form">#</a>';
    }
    if (@$_GET['update'] && !$error && !empty($_POST)) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        if (@$_GET['name'] && @$_POST['tv_genre_id'] > 0) {
            $ch_id = (int) @$_GET['id'];
            $channel = \Ministra\Lib\Itv::getChannelById($ch_id);
            if (!empty($channel) && ($channel['enable_tv_archive'] != $enable_tv_archive || $channel['wowza_dvr'] != $wowza_dvr || $channel['flussonic_dvr'] != $flussonic_dvr || $channel['tv_archive_duration'] != $_POST['tv_archive_duration'])) {
                if ($channel['enable_tv_archive']) {
                    if ($channel['flussonic_dvr']) {
                        $archive = new \Ministra\Lib\FlussonicTvArchive();
                    } elseif ($channel['wowza_dvr']) {
                        $archive = new \Ministra\Lib\WowzaTvArchive();
                    } else {
                        $archive = new \Ministra\Lib\TvArchive();
                    }
                    $archive->deleteTasks($ch_id);
                }
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['name' => $_POST['name'], 'cmd' => !empty($_POST['cmd'][0]) ? $_POST['cmd'][0] : '', 'cmd_1' => @$_POST['cmd_1'], 'cmd_2' => @$_POST['cmd_2'], 'cmd_3' => @$_POST['cmd_3'], 'mc_cmd' => $mc_cmd, 'enable_wowza_load_balancing' => $enable_wowza_load_balancing, 'enable_tv_archive' => $enable_tv_archive, 'allow_pvr' => $allow_pvr, 'allow_local_pvr' => $allow_local_pvr, 'allow_local_timeshift' => $allow_local_timeshift, 'enable_monitoring' => $enable_monitoring, 'wowza_tmp_link' => $wowza_tmp_link, 'nginx_secure_link' => $nginx_secure_link, 'wowza_dvr' => $wowza_dvr, 'flussonic_dvr' => $flussonic_dvr, 'use_http_tmp_link' => $use_http_tmp_link, 'censored' => $censored, 'base_ch' => $base_ch, 'bonus_ch' => $bonus_ch, 'hd' => $hd, 'cost' => $_POST['cost'], 'number' => $_POST['number'], 'descr' => $_POST['descr'], 'tv_genre_id' => $_POST['tv_genre_id'], 'xmltv_id' => $_POST['xmltv_id'], 'service_id' => \trim($_POST['service_id']), 'volume_correction' => (int) $_POST['volume_correction'], 'correct_time' => (int) $_POST['correct_time'], 'modified' => 'NOW()', 'tv_archive_duration' => $_POST['tv_archive_duration']], ['id' => (int) @$_GET['id']]);
            \Ministra\Lib\Itv::invalidateCacheForChannel((int) @$_GET['id']);
            if (!$enable_monitoring) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['monitoring_status' => 1], ['id' => (int) @$_GET['id']]);
            }
            $urls = $_POST['cmd'];
            $priorities = $_POST['priority'];
            $current_urls = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => (int) $_GET['id']])->get()->all('url');
            $current_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => (int) $_GET['id']])->get()->all();
            $urls_str = "'" . \implode("','", $urls) . "'";
            $need_to_delete_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('select * from ch_links where ch_id=' . (int) $_GET['id'] . ' and url not in (' . $urls_str . ')')->all('id');
            if ($need_to_delete_links) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from ch_links where id in (' . \implode(',', $need_to_delete_links) . ')');
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from ch_link_on_streamer where link_id in (' . \implode(',', $need_to_delete_links) . ')');
            }
            foreach ($links as $link) {
                $link['ch_id'] = (int) $_GET['id'];
                $links_on_server = $link['stream_servers'];
                unset($link['stream_servers']);
                if (!\in_array($link['url'], $current_urls)) {
                    $link_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_links', $link)->insert_id();
                    if ($link_id && $links_on_server) {
                        foreach ($links_on_server as $streamer_id) {
                            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_link_on_streamer', ['link_id' => $link_id, 'streamer_id' => $streamer_id]);
                        }
                    }
                } else {
                    $link_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => (int) $_GET['id'], 'url' => $link['url']])->get()->first('id');
                    if (!$link['enable_monitoring']) {
                        $link['status'] = 1;
                    }
                    if ($link_id) {
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching(['ch_links.id=' . (int) $link_id])->update('ch_links', $link, ['id' => (int) $link_id]);
                        $on_streamers = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_link_on_streamer')->where(['link_id' => $link_id])->get()->all('streamer_id');
                        if ($on_streamers) {
                            $need_to_delete = \array_diff($on_streamers, $links_on_server);
                            $need_to_insert = \array_diff($links_on_server, $on_streamers);
                            if ($need_to_delete) {
                                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("delete from ch_link_on_streamer where link_id={$link_id} and streamer_id in (" . \implode(',', $need_to_delete) . ')');
                            }
                            if ($need_to_insert) {
                                foreach ($need_to_insert as $streamer_id) {
                                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_link_on_streamer', ['link_id' => $link_id, 'streamer_id' => $streamer_id]);
                                }
                            }
                        } else {
                            foreach ($links_on_server as $streamer_id) {
                                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('ch_link_on_streamer', ['link_id' => $link_id, 'streamer_id' => $streamer_id]);
                            }
                        }
                    }
                }
            }
            if ($enable_tv_archive) {
                if (!empty($_POST['flussonic_dvr'])) {
                    $archive = new \Ministra\Lib\FlussonicTvArchive();
                } elseif (!empty($_POST['wowza_dvr'])) {
                    $archive = new \Ministra\Lib\WowzaTvArchive();
                } else {
                    $archive = new \Ministra\Lib\TvArchive();
                }
                $archive->createTasks($ch_id, $storage_names);
            }
            if ($allow_pvr) {
                \Ministra\Lib\RemotePvr::setAllowedStoragesForChannel($ch_id, $pvr_storage_names);
            }
            if (!empty($_FILES['logo']['name'])) {
                if ($logo = \Ministra\OldAdmin\handle_upload_logo_itv($_FILES['logo'], $ch_id)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['logo' => $logo], ['id' => $ch_id]);
                } else {
                    $error = \_('Error: could not save logo') . ' <a href="#form">#</a>';
                }
            }
            if (empty($error)) {
                \header('Location: add_itv.php?edit=1&id=' . (int) @$_GET['id'] . '#form');
                exit;
            }
        } else {
            $error = \_('Error: all fields are required') . ' <a href="#form">#</a>';
        }
    }
}
$tv_archive = new \Ministra\Lib\TvArchive();
$storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['status' => 1, 'for_records' => 1])->where([' stream_server_type' => \null, 'stream_server_type' => ''], 'OR ')->get()->all();
$stream_servers = \Ministra\Lib\StreamServer::getAll();
$selected_storages = $selected_pvr_storages = [];
if (!empty($_GET['id'])) {
    $tasks = \Ministra\Lib\TvArchive::getTasksByChannelId((int) $_GET['id']);
    if (!empty($tasks)) {
        $selected_storages = \array_map(function ($storage) {
            return $storage['storage_name'];
        }, $tasks);
    }
    $selected_pvr_storages = \array_keys(\Ministra\Lib\RemotePvr::getStoragesForChannel((int) $_GET['id']));
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        td {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: #000000;
        }

        .list {
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
        }

        a {
            color: #0000FF;
            font-weight: bold;
            text-decoration: none;
        }

        a:link, a:visited {
            color: #5588FF;
            font-weight: bold;
        }

        a:hover {
            color: #0000FF;
            font-weight: bold;
            text-decoration: underline;
        }

        .shift_ch {
            cursor: pointer
        }

        .last_modified {
            border-left-color: #F00;
        }
    </style>
    <title>
        <?php 
echo \_('IPTV channels');
?>
    </title>
    <script type="text/javascript" src="js.js"></script>
    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../adm/js/jquery.tmpl.min.js"></script>
    <script type="text/javascript" src="../adm/js/jquery.cookies.2.2.0.js"></script>

    <script id="link_item_tmpl" type="text/x-jquery-tmpl">
    <div id="link_${idx}" class="link" link-id="${idx}">
        <input name="cmd[${idx}]" size="50" type="text" value="${url}"
               style="border-style:solid;border-color: {{if status==1}}#66A566{{else}}#F88787{{/if}}"><br>
        <table>
            <tr>
                <td><?php 
echo \_('priority');
?>:</td>
                <td><input type="text" name="priority[${idx}]" size="3" value="${priority}"></td>
            </tr>
            <tr>
                <td><?php 
echo \_('filter');
?>:</td>
                <td>
                    <input type="text" name="user_agent_filter[${idx}]" value="${user_agent_filter}">
                </td>
            </tr>
            <tr>
                <td><?php 
echo \_('Temporary HTTP URL');
?>:</td>
                <td>
                    <input type="checkbox" class="use_http_tmp_link" name="use_http_tmp_link[${idx}]" value="1" {{if use_http_tmp_link==="1"}}checked{{/if}}>
                </td>
            </tr>
            <tr style="display:{{if use_http_tmp_link==1}} {{else}}none{{/if}}; background-color:#f8f8f8">
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td>&nbsp;&nbsp;<?php 
echo \_('WOWZA support');
?>:</td>
                            <td width="40%"><input type="checkbox" name="wowza_tmp_link[${idx}]" value="1" {{if wowza_tmp_link==="1"}}checked{{/if}}></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<?php 
echo \_('Flussonic support');
?>:</td>
                            <td width="40%"><input type="checkbox" name="flussonic_tmp_link[${idx}]" value="1" {{if flussonic_tmp_link==="1"}}checked{{/if}}></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<?php 
echo \_('Xtream-Codes support');
?>:</td>
                            <td width="40%"><input type="checkbox" name="xtream_codes_support[${idx}]" value="1" <?php 
if (\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so')) {
    ?>{{if xtream_codes_support==="1"}}checked{{/if}}><?php 
} else {
    ?>readonly="readonly"><br><span style="color: red; display: none;"><?php 
    echo \_('For enabling Xtream-Codes Support you need enable mcrypt php-extension');
    ?></span><?php 
}
?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;<?php 
echo \_('NGINX secure link');
?>:</td>
                            <td><input type="checkbox" name="nginx_secure_link[${idx}]" value="1" {{if nginx_secure_link==="1"}}checked{{/if}}></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php 
echo \_('Enable monitoring');
?>:
                    <input type="checkbox" class="enable_monitoring"  name="enable_monitoring[${idx}]" value="1" {{if enable_monitoring==="1"}}checked{{/if}}>
                    <span style="display:{{if use_load_balancing==1 && enable_monitoring==1}} {{else}}none{{/if}}">&nbsp;&nbsp;<?php 
echo \_('Balancer monitoring');
?>:<input type="checkbox" name="enable_balancer_monitoring[${idx}]" value="1" {{if enable_balancer_monitoring==="1"}}checked{{/if}}></span>
                </td>
            </tr>
            <tr class="monitoring_url_block" style="display:{{if enable_monitoring==1}} {{else}}none{{/if}}; background-color:#f8f8f8">
                <td colspan="2">
                    <?php 
echo \_('Channel URL for monitoring');
?>:<br>
                    <input type="text" size="48" name="monitoring_url[${idx}]" value="${monitoring_url}">
                </td>
            </tr>
            <tr>
                <td>load balancing:</td>
                <td>
                    <input type="checkbox" class="use_load_balancing" name="use_load_balancing[${idx}]" value="1" {{if use_load_balancing==="1"}}checked{{/if}}>
                </td>
            </tr>
            <tr style="display:{{if use_load_balancing==1}} {{else}}none{{/if}}; background-color:#f8f8f8">
                <td colspan="2">
                    <table width="100%">
                        {{each(i, stream_server) stream_servers}}
                        <tr>
                            <td width="50%" style="color: {{if stream_server.monitoring_status==1 && stream_server.selected==1 && enable_monitoring==1 && enable_balancer_monitoring==1}}#079107{{else stream_server.monitoring_status==0 && stream_server.selected==1 && enable_monitoring==1 && enable_balancer_monitoring==1}}#D31919{{/if}}">${stream_server.name}:</td>
                            <td width="50%">
                                <input type="checkbox" class="stream_server" name="stream_server[${idx}][]" value="${stream_server.id}" {{if stream_server.selected==1}}checked{{/if}}/>
                            </td>
                        </tr>
                        {{/each}}
                    </table>
                </td>
            </tr>
        </table>

        <button href="javascript://" class="add_ch_url"><?php 
echo \_('add new link');
?></button>
        {{if idx>0}}
        <button href="javascript://" class="del_ch_url"><?php 
echo \_('delete');
?></button>
        {{/if}}
        <hr>
    </div>

    </script>

    <script type="text/javascript">

    jQuery.fn.sortElements = (function () {

      var sort = [].sort;

      return function (comparator, getSortable) {

        getSortable = getSortable || function () {return this;};

        var placements = this.map(function () {

          var sortElement = getSortable.call(this),
            parentNode = sortElement.parentNode,

            // Since the element itself will change position, we have
            // to have some way of storing its original position in
            // the DOM. The easiest way is to have a 'flag' node:
            nextSibling = parentNode.insertBefore(
              document.createTextNode(''),
              sortElement.nextSibling
            );

          return function () {

            if (parentNode === this) {
              throw new Error(
                'You can\'t sort elements if any one is a descendant of another.'
              );
            }

            // Insert before flag:
            parentNode.insertBefore(this, nextSibling);
            // Remove flag:
            parentNode.removeChild(nextSibling);

          };

        });

        return sort.call(this, comparator).each(function (i) {
          placements[i].call(getSortable.call(this));
        });
      };

    })();

    $(function () {

        <?php 
if (!(\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so'))) {
    ?>
      $('input[type="checkbox"][name*="xtream_codes_support"]').live('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).prop('checked', false).removeAttr('checked');
        $(this).next().next().show();
        return false;
      });
        <?php 
}
?>

      $('.add_ch_url').live('click', function (event) {

        var idx = $('.links_block>div').length;

        var link = {
          'url': '',
          'priority': 0,
          'status': 1,
          'use_http_tmp_link': 0,
          'wowza_tmp_link': 0,
          'flussonic_tmp_link': 0,
          'xtream_codes_support': 0,
          'nginx_secure_link': 0,
          'user_agent_filter': '',
          'idx': idx,
          'monitoring_url': '',
          'use_load_balancing': 0,
          'enable_monitoring': 0,
          'enable_balancer_monitoring': 0
        };

        $('#link_item_tmpl').tmpl(link).appendTo('.links_block');

        return false;
      });

      $('.del_ch_url').live('click', function (event) {
        if (confirm("<?php 
echo \htmlspecialchars(\_('Do you really want to delete this record?'), \ENT_QUOTES);
?>")) {
          $(this).closest('div').remove();
        }
        return false;
      });

      if (!links) {
        links = [];
      }

      if (links.length == 0) {
        links = [{
          'url': '',
          'priority': 0,
          'status': 1,
          'use_http_tmp_link': 0,
          'wowza_tmp_link': 0,
          'flussonic_tmp_link': 0,
          'xtream_codes_support': 0,
          'nginx_secure_link': 0,
          'user_agent_filter': '',
          'monitoring_url': '',
          'use_load_balancing': 0,
          'enable_monitoring': 0,
          'enable_balancer_monitoring': 0
        }];
      }

      links = links.map(function (link, idx) {
        link['idx'] = idx;
        return link;
      });

      $('#link_item_tmpl').tmpl(links).appendTo('.links_block');

      $('.use_http_tmp_link').live('click', function (event) {
        if ($(this).attr('checked')) {
          $(this).parent().parent().next().show();
        } else {
          $(this).parent().parent().next().hide();
        }
      });

      $('.use_load_balancing').live('click', function (event) {
        if ($(this).attr('checked')) {

          $(this).parent().parent().next().show();

          if ($('#link_' + $(this).parents('.link').attr('link-id')).find('.enable_monitoring').attr('checked')) {
            $(this).parent().parent().prev().prev().children().children('span').show();
          }
        } else {
          $(this).parent().parent().next().hide();
          $(this).parent().parent().prev().prev().children().children('span').hide();
        }
      });

      $('.enable_monitoring').live('click', function (event) {
        if ($(this).attr('checked')) {
          $(this).parent().parent().next().show();
          if ($('#link_' + $(this).parents('.link').attr('link-id')).find('.use_load_balancing').attr('checked')) {
            $(this).next().show();
          }
        } else {
          $(this).parent().parent().next().hide();
          $(this).next().hide();
        }
      });

      var table = $('.item_list');

      $('.item_list th')
        .each(function () {

          var th = $(this),
            thIndex = th.index(),
            inverse = false;

          if (thIndex == 1) {
            inverse = true;
          }

          th.click(function (eventObject) {

            //console.log(eventObject);

            if (eventObject.hasOwnProperty('inverse')) {
              inverse = !eventObject.inverse;
            }

            table.find('td.list').filter(function () {

              return $(this).index() === thIndex;

            }).sortElements(function (a, b) {

              // todo: sort numbers
              if (th.hasClass('number_row')) {
                return parseInt($.text([a]), 10) > parseInt($.text([b]), 10) ?
                  inverse ? -1 : 1
                  : inverse ? 1 : -1;
              } else {
                return $.text([a]) > $.text([b]) ?
                  inverse ? -1 : 1
                  : inverse ? 1 : -1;
              }

            }, function () {

              // parentNode is the element we want to move
              return this.parentNode;

            });

            inverse = !inverse;

            $.cookies.set('sort_by_row', $(this).index());
            $.cookies.set('sort_inverse', inverse);

            if (sort_by_row == $(this).index() && inverse == true) {
              $('.shift_ch').show();
            } else {
              $('.shift_ch').hide();
            }

            $('.order').remove();

            th.append(' <span class="order">' + (inverse ? '&darr;' : '&uarr;') + '</span>');
          });
        });

      $('.add_btn').click(function () {
        var new_position = $('.itv_form').offset();
        window.scrollTo(new_position.left, new_position.top);
        return false;
      });

      var sort_by_row = $.cookies.get('sort_by_row');
      var sort_inverse = $.cookies.get('sort_inverse');

      if (sort_by_row !== null) {
        $('.item_list th:eq(' + sort_by_row + ')').trigger(jQuery.Event('click', { inverse: $.cookies.get('sort_inverse') }));
      } else {
        sort_by_row = 1;
        sort_inverse = true;
      }

      if (sort_by_row == 1 && sort_inverse == true) {
        $('.shift_ch').show();
      } else {
        $('.shift_ch').hide();
      }

      $('.add_btn').click(function () {
        $('#form_').get(0).reset();
        document.location.href = 'add_itv.php#form';
      });

      $('.shift_ch').click(function () {

        var direction = $(this).attr('data-direction');
        var from_num = $(this).parent().parent().find('.number').html();

        var number_error = $('.number').toArray().some(function (dom_obj) {
          return $(dom_obj).html() == parseInt(from_num, 10) + parseInt(direction, 10);
        });

        if (direction < 0 && number_error) {
          alert('<?php 
echo \htmlspecialchars(\_('Channel with same number already exists!'), \ENT_QUOTES);
?>');
        } else {
          if (confirm('<?php 
echo \htmlspecialchars(\_('Shift channel list?'), \ENT_QUOTES);
?>')) {
            window.location = 'add_itv.php?shift=' + direction + '&from_num=' + from_num;
          }
        }
      });

      $('#enable_tv_archive').click(function () {
        if ($(this).attr('checked')) {
          $(this).next().show();
          $(this).next().next().show();
          $(this).next().next().next().show();
        } else {
          $(this).next().hide();
          $(this).next().next().hide();
          $(this).next().next().next().hide();
        }
      });

      $('.flussonic_dvr').click(function () {
        if ($(this).attr('checked')) {
          $('.flussonic_stream_server').removeAttr('disabled');
          $('.generic_stream_server').attr('disabled', 'disabled');
          $('.wowza_stream_server').attr('disabled', 'disabled');
          $('.wowza_dvr').attr('disabled', 'disabled');
        } else {
          $('.flussonic_stream_server').attr('disabled', 'disabled');
          $('.generic_stream_server').removeAttr('disabled');
          $('.wowza_dvr').removeAttr('disabled');
        }
      });

      $('.wowza_dvr').click(function () {
        if ($(this).attr('checked')) {
          $('.wowza_stream_server').removeAttr('disabled');
          $('.generic_stream_server').attr('disabled', 'disabled');
          $('.flussonic_stream_server').attr('disabled', 'disabled');
          $('.flussonic_dvr').attr('disabled', 'disabled');
        } else {
          $('.wowza_stream_server').attr('disabled', 'disabled');
          $('.generic_stream_server').removeAttr('disabled');
          $('.flussonic_dvr').removeAttr('disabled');
        }
      });
    });
    </script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('IPTV channels');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a> | <a href="#"
                                                                      class="add_btn"><?php 
echo \_('Add');
?></a> | <a
                    href="?restart_all_archives=1"><?php 
echo \_('Restart all TV archives');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <strong>
                    <?php 
echo $error;
?>
                </strong>
            </font>
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td align="center">

            <?php 
$last_modified_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['modified!=' => ''])->orderby('modified', 'DESC')->limit(1, 0)->get()->first('id');
$all_channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("select itv.*, tv_genre.title as genres_name, media_claims.media_type, media_claims.media_id, media_claims.sound_counter, media_claims.video_counter, media_claims.no_epg, media_claims.wrong_epg from itv left join media_claims on itv.id=media_claims.media_id and media_claims.media_type='itv' left join tv_genre on itv.tv_genre_id=tv_genre.id group by itv.id order by number");
echo "<center><table class='list item_list' cellpadding='3' cellspacing='0'>";
echo '<tr>';
echo "<th class='list number_row'><b>id</b></th>";
echo "<th class='list number_row'><b>" . \_('Number') . "</b> <span class='order'>&darr;</span></th>";
echo "<th class='list'><b>" . \_('Service code') . '</b></th>';
echo "<th class='list'><b>" . \_('Name') . '</b></th>';
echo "<th class='list'><b>" . \_('URL') . '</b></th>';
echo "<th class='list'><b>" . \_('xmltv id') . '</b></th>';
echo "<th class='list'><b>" . \_('Genre') . '</b></th>';
echo "<th class='list'><b>" . \_('Archive') . '</b></th>';
echo "<th class='list'><b>" . \_('Volume correction') . '</b></th>';
echo "<th class='list'><b>" . \_('Claims about<br>audio/video/epg') . "</b></th>\n";
echo "<th class='list'><b>&nbsp;</b></th>";
echo '</tr>';
while ($arr = $all_channels->next()) {
    echo '<tr ';
    if ($arr['bonus_ch'] == 1) {
        echo 'bgcolor="#ffffec"';
    } else {
        if ($arr['base_ch'] == 1) {
        } else {
            if (\strlen($arr['service_id']) < 5) {
                echo 'bgcolor="#f7f7f7"';
            } else {
                echo 'bgcolor="#ffecec"';
            }
        }
    }
    echo '>';
    echo "<td class='list";
    if ($last_modified_id == $arr['id']) {
        echo ' last_modified';
    }
    echo "' height='36'>" . $arr['id'] . '</td>';
    echo "<td class='list'><span class='number'>" . $arr['number'] . "</span> <div style='float:right'><span class='shift_ch' data-direction='1'>&darr;</span> <span class='shift_ch' data-direction='-1'>&uarr;</span></div></td>";
    echo "<td class='list'>" . $arr['service_id'] . '</td>';
    echo "<td class='list'>";
    echo '<table cellpadding="0" cellspacing="0">';
    echo '<tr>';
    echo '<td>';
    if ($ch_logo = \Ministra\Lib\Itv::getLogoUriById((int) $arr['id'], 120)) {
        echo '<img src ="' . $ch_logo . '"/ >';
    }
    echo '</td>';
    echo '<td>';
    echo '<b style="color:' . \Ministra\OldAdmin\get_color($arr) . '" title="' . \Ministra\OldAdmin\get_hint($arr) . '">' . $arr['name'] . '</b>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</td>';
    echo "<td class='list'>" . $arr['cmd'] . '</td>';
    echo "<td class='list'>" . $arr['xmltv_id'] . '</td>';
    echo "<td class='list'>" . (!empty($arr['genres_name']) ? \_($arr['genres_name']) : '----') . '</td>';
    echo "<td class='list' align='center'>" . ($arr['enable_tv_archive'] == 1 ? '&bull;' : '') . '</td>';
    echo "<td class='list'>" . $arr['volume_correction'] . '</td>';
    echo "<td class='list' align='center'>\n";
    if (\Ministra\Lib\Admin::isActionAllowed()) {
        echo "<a href='#' onclick='if(confirm(\"" . \_('Do you really want to reset claims counter?') . '")){document.location="claims.php?reset=1&media_id=' . $arr['media_id'] . '&media_type=' . $arr['media_type'] . "\"}'>";
    }
    echo "<span style='color:red;font-weight:bold'>" . $arr['video_counter'] . ' / ' . $arr['sound_counter'] . ' / ' . $arr['no_epg'], ' / ' . $arr['wrong_epg'] . '</span>';
    if (\Ministra\Lib\Admin::isActionAllowed()) {
        echo '</a>';
    }
    echo "</td>\n";
    echo "<td class='list' nowrap><a href='?edit=1&id=" . $arr['id'] . "#form'>edit</a>&nbsp;&nbsp;";
    echo "<a href='#' onclick='if(confirm(\"" . \htmlspecialchars(\_('Do you really want to delete this record?'), \ENT_QUOTES) . '")){document.location="add_itv.php?del=1&id=' . $arr['id'] . '&letter=' . @$_GET['letter'] . '&search=' . @$_GET['search'] . "\"}'>del</a>&nbsp;&nbsp;\n";
    if ($arr['status']) {
        echo "<a href='?status=0&id=" . $arr['id'] . "'><font color='Green'>on</font></a>&nbsp;&nbsp;";
    } else {
        echo "<a href='?status=1&id=" . $arr['id'] . "'><font color='Red'>off</font></a>&nbsp;&nbsp;";
    }
    echo "<a href='add_epg.php?id=" . $arr['id'] . "'>EPG</a>&nbsp;&nbsp;</td>";
    echo '</tr>';
}
echo '</table></center>';
if (@$_GET['edit']) {
    $arr = \Ministra\Lib\Itv::getById((int) @$_GET['id']);
    if (!empty($arr)) {
        $name = $arr['name'];
        $number = $arr['number'];
        $cmd = $arr['cmd'];
        $mc_cmd = $arr['mc_cmd'];
        $tv_genre_id = $arr['tv_genre_id'];
        $descr = $arr['descr'];
        $status = $arr['status'];
        $status = $arr['status'];
        $censored = $arr['censored'];
        $base_ch = $arr['base_ch'];
        $bonus_ch = $arr['bonus_ch'];
        $cost = $arr['cost'];
        $hd = $arr['hd'];
        $xmltv_id = $arr['xmltv_id'];
        $service_id = $arr['service_id'];
        $volume_correction = $arr['volume_correction'];
        $correct_time = $arr['correct_time'];
        $use_http_tmp_link = $arr['use_http_tmp_link'];
        $wowza_tmp_link = $arr['wowza_tmp_link'];
        $wowza_dvr = $arr['wowza_dvr'];
        $flussonic_dvr = $arr['flussonic_dvr'];
        $enable_tv_archive = $arr['enable_tv_archive'];
        $tv_archive_duration = $arr['tv_archive_duration'];
        $allow_pvr = $arr['allow_pvr'];
        $allow_local_pvr = $arr['allow_local_pvr'];
        $allow_local_timeshift = $arr['allow_local_timeshift'];
        $enable_monitoring = $arr['enable_monitoring'];
        $monitoring_url = $arr['monitoring_url'];
        $enable_wowza_load_balancing = $arr['enable_wowza_load_balancing'];
        $logo = $arr['logo'];
        if ($use_http_tmp_link) {
            $checked_http_tmp_link = 'checked';
        }
        if ($wowza_tmp_link) {
            $checked_wowza_tmp_link = 'checked';
        }
        if ($wowza_dvr) {
            $checked_wowza_dvr = 'checked';
        }
        if ($flussonic_dvr) {
            $checked_flussonic_dvr = 'checked';
        }
        if ($enable_tv_archive) {
            $checked_enable_tv_archive = 'checked';
        }
        if ($allow_pvr) {
            $checked_allow_pvr = 'checked';
        }
        if ($allow_local_pvr) {
            $checked_allow_local_pvr = 'checked';
        } else {
            $checked_allow_local_pvr = '';
        }
        if ($allow_local_timeshift) {
            $checked_allow_local_timeshift = 'checked';
        } else {
            $checked_allow_local_timeshift = '';
        }
        if ($enable_monitoring) {
            $checked_enable_monitoring = 'checked';
        }
        if ($enable_wowza_load_balancing) {
            $checked_wowza_load_balancing = 'checked';
        }
        if ($censored) {
            $checked = 'checked';
        }
        if ($base_ch) {
            $checked_base = 'checked';
        }
        if ($bonus_ch) {
            $checked_bonus = 'checked';
        }
        if ($hd) {
            $checked_hd = 'checked';
        }
        $stream_servers = \Ministra\Lib\StreamServer::getAll();
        $links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => (int) $arr['id']])->orderby('priority')->get()->all();
        $links = \array_map(function ($link) use($stream_servers) {
            $streamers_map = \Ministra\Lib\StreamServer::getStreamersIdMapForLink($link['id']);
            $link['stream_servers'] = \array_map(function ($server) use($streamers_map) {
                if (!empty($streamers_map[$server['id']])) {
                    $server['selected'] = 1;
                    $server['monitoring_status'] = $streamers_map[$server['id']]['monitoring_status'];
                } else {
                    $server['selected'] = 0;
                    $server['monitoring_status'] = 0;
                }
                return $server;
            }, $stream_servers);
            $link['xtream_codes_support'] = \extension_loaded('mcrypt') || \extension_loaded('mcrypt.so') ? $link['xtream_codes_support'] : 0;
            return $link;
        }, $links);
    }
} elseif (!empty($_POST)) {
    $name = @$_POST['name'];
    $number = @$_POST['number'];
    $cmd = @$_POST['cmd'];
    $mc_cmd = @$_POST['mc_cmd'];
    $tv_genre_id = @$_POST['tv_genre_id'];
    $descr = @$_POST['descr'];
    $cost = @$_POST['cost'];
    $xmltv_id = @$_POST['xmltv_id'];
    $service_id = @$_POST['service_id'];
    $volume_correction = @$_POST['volume_correction'];
    $correct_time = @$_POST['correct_time'];
    $monitoring_url = @$_POST['monitoring_url'];
    $tv_archive_duration = @$_POST['tv_archive_duration'];
    if (@$_POST['use_http_tmp_link']) {
        $checked_http_tmp_link = 'checked';
    }
    if (@$_POST['wowza_tmp_link']) {
        $checked_wowza_tmp_link = 'checked';
    }
    if (@$_POST['wowza_dvr']) {
        $checked_wowza_dvr = 'checked';
    }
    if (@$_POST['flussonic_dvr']) {
        $checked_flussonic_dvr = 'checked';
    }
    if (@$_POST['enable_tv_archive']) {
        $checked_enable_tv_archive = 'checked';
    }
    if (@$_POST['allow_local_pvr']) {
        $checked_allow_local_pvr = 'checked';
    } else {
        $checked_allow_local_pvr = '';
    }
    if (@$_POST['allow_local_timeshift']) {
        $checked_allow_local_timeshift = 'checked';
    } else {
        $checked_allow_local_timeshift = '';
    }
    if (@$_POST['enable_monitoring']) {
        $checked_enable_monitoring = 'checked';
    }
    if (@$_POST['enable_wowza_load_balancing']) {
        $checked_wowza_load_balancing = 'checked';
    }
    if (@$_POST['censored']) {
        $checked = 'checked';
    }
    if (@$_POST['base_ch']) {
        $checked_base = 'checked';
    }
    if (@$_POST['bonus_ch']) {
        $checked_bonus = 'checked';
    }
    if (@$_POST['hd']) {
        $checked_hd = 'checked';
    }
}
?>
            <script type="text/javascript">
            function save() {
              var form_ = document.getElementById('form_');
              var cmd = '';
              var name = document.getElementById('name').value;
              if (document.getElementById('cmd')) {
                cmd = document.getElementById('cmd').value;
              }
              var id = document.getElementById('id').value;
              //descr = document.getElementById('descr').value

              var action = 'add_itv.php?name=' + name + '&cmd=' + cmd + '&id=' + id;
              //alert(action)
              if (document.getElementById('action').value == 'edit') {
                action += '&update=1';
              } else {
                action += '&save=1';
              }

              //alert(action)
              form_.setAttribute('action', action);
              form_.setAttribute('method', 'POST');
              //document.location=action
              form_.submit();
            }

            function popup(src) {
              window.open(src, 'win_' + src, 'width=300,height=200,toolbar=0,location=0,directories=0,menubar=0,scrollbars=0,resizable=1,status=0,fullscreen=0');
            }

            function delete_logo(id) {
              var req = new Subsys_JsHttpRequest_Js();

              req.onreadystatechange = function () {
                if (req.readyState == 4) {

                  if (req.responseJS) {

                    var resp = req.responseJS.data;
                    if (req.responseJS) {
                      //set_cat_genres(resp)
                      document.getElementById('logo_block').innerHTML = '';
                    } else {
                      alert('<?php 
echo \htmlspecialchars(\_('Error deleting a logo'), \ENT_QUOTES);
?>');
                    }
                  } else {
                    alert('<?php 
echo \htmlspecialchars(\_('Error deleting a logo'), \ENT_QUOTES);
?>');
                  }
                }
              };

              req.caching = false;

              req.open('POST', 'load.php?get=del_tv_logo&id=' + id, true);
              req.send({ data: 'bar' });
            }
            </script>
            <br>
            <script type="text/javascript">
            var links = <?php 
echo empty($links) ? '[]' : \json_encode($links);
?>;
            var stream_servers = <?php 
echo empty($stream_servers) ? '[]' : \json_encode($stream_servers);
?>;
            </script>

            <a name="form"></a>
            <table align="center" class='list'>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        <form id="form_" class="itv_form" method="POST" enctype="multipart/form-data">
                            <table align="center">
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Number');
?>:
                                    </td>
                                    <td>
                                        <input type="text" name="number" id="number" value="<?php 
echo @$number;
?>"
                                               maxlength="5">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Name');
?>:
                                    </td>
                                    <td>
                                        <input type="text" name="name" id="name"
                                               value="<?php 
echo @\htmlspecialchars($name);
?>">
                                        <input type="hidden" id="id" value="<?php 
echo @$_GET['id'];
?>">
                                        <input type="hidden" id="action"
                                               value="<?php 
if (@$_GET['edit'] || @$_GET['update']) {
    echo 'edit';
}
?>">
                                    </td>
                                </tr>

                                <!--<tr>
           <td align="right" valign="top">
            <?php 
?>:
           </td>
           <td>
            <input name="use_http_tmp_link" id="use_http_tmp_link" type="checkbox" <?php 
?> onchange="this.checked ? document.getElementById('wowza_tmp_link_tr').style.display = '' : document.getElementById('wowza_tmp_link_tr').style.display = 'none'" >
            <span id="wowza_tmp_link_tr" style="display: <?php 
?>">
                <?php 
?>:
                <input name="wowza_tmp_link" id="wowza_tmp_link" type="checkbox" <?php 
?> >
            </span>
           </td>
        </tr>-->
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Age restriction');
?>:
                                    </td>
                                    <td>
                                        <input name="censored" id="censored" type="checkbox" <?php 
echo @$checked;
?> >
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        HD:
                                    </td>
                                    <td>
                                        <input name="hd" id="hd" type="checkbox" <?php 
echo @$checked_hd;
?> >
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Base channel');
?>:
                                    </td>
                                    <td>
                                        <input name="base_ch" id="base_ch"
                                               type="checkbox" <?php 
echo @$checked_base;
?> >
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Bonus channel');
?>:
                                    </td>
                                    <td>
                                        <input name="bonus_ch" id="bonus_ch"
                                               type="checkbox" <?php 
echo @$checked_bonus;
?> >
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Price');
?>:
                                    </td>
                                    <td>
                                        <input name="cost" id="cost" type="text" value="<?php 
echo @$cost;
?>" size="5"
                                               maxlength="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Genre');
?>:
                                    </td>
                                    <td>
                                        <select name="tv_genre_id">
                                            <option value="0">-----------
                                                <?php 
echo \Ministra\OldAdmin\get_genres($tv_genre_id);
?>
                                        </select>
                                    </td>
                                </tr>

                                <?php 
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tv_quality_filter')) {
    ?>
                                    <tr>
                                        <td align="right">
                                            URL (HQ):
                                        </td>
                                        <td>
                                            <input id="cmd_1" name="cmd_1" size="50" type="text"
                                                   value="<?php 
    echo @$arr['cmd_1'];
    ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            URL (Medium):
                                        </td>
                                        <td>
                                            <input id="cmd_2" name="cmd_2" size="50" type="text"
                                                   value="<?php 
    echo @$arr['cmd_2'];
    ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">
                                            URL (Low):
                                        </td>
                                        <td>
                                            <input id="cmd_3" name="cmd_3" size="50" type="text"
                                                   value="<?php 
    echo @$arr['cmd_3'];
    ?>">
                                        </td>
                                    </tr>
                                <?php 
} else {
    ?>
                                    <tr>
                                        <td align="right" valign="top">
                                            <?php 
    echo \_('Links');
    ?>:
                                        </td>
                                        <td class="links_block">

                                        </td>
                                    </tr>
                                <?php 
}
?>

                                <tr style="display: none;">
                                    <td align="right" valign="top">
                                        WOWZA load balancing:
                                    </td>
                                    <td>
                                        <input name="enable_wowza_load_balancing" id="enable_wowza_load_balancing"
                                               value="1" type="checkbox" <?php 
echo @$checked_wowza_load_balancing;
?> >
                                    </td>
                                </tr>

                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('URL for recording (multicast)');
?>:
                                    </td>
                                    <td>
                                        <input id="mc_cmd" name="mc_cmd" size="50" type="text"
                                               value="<?php 
echo @$mc_cmd;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Enable TV archive');
?>:
                                    </td>
                                    <td>
                                        <input name="enable_tv_archive" id="enable_tv_archive" class="enable_tv_archive"
                                               type="checkbox" <?php 
echo @$checked_enable_tv_archive;
?> >
                                        <span class="flussonic_dvr_block"
                                              style="display: <?php 
echo @$checked_enable_tv_archive ? '' : 'none';
?>"> <?php 
echo \_('Flussonic DVR');
?><input
                                                    type="checkbox" class="flussonic_dvr"
                                                    name="flussonic_dvr" <?php 
echo @$checked_flussonic_dvr;
?>></span>
                                        <span class="wowza_dvr_block"
                                              style="display: <?php 
echo @$checked_enable_tv_archive ? '' : 'none';
?>"> <?php 
echo \_('Wowza DVR');
?><input
                                                    type="checkbox" class="wowza_dvr"
                                                    name="wowza_dvr" <?php 
echo @$checked_wowza_dvr;
?>></span>
                                        <span id="storage_name"
                                              style="display: <?php 
echo @$checked_enable_tv_archive ? '' : 'none';
?>">
                <table width="100%" style="background-color:#f8f8f8">
                    <?php 
foreach ($storages as $storage) {
    ?>
                        <tr>
                        <td width="50%"><?php 
    echo $storage['storage_name'];
    ?>:</td>
                        <td width="50%">
                            <input type="checkbox"
                                   class="stream_server <?php 
    if ($storage['flussonic_dvr']) {
        echo 'flussonic_stream_server';
    } elseif ($storage['wowza_dvr']) {
        echo 'wowza_stream_server';
    } else {
        echo 'generic_stream_server';
    }
    ?>"
                                   name="storage_names[]"
                                   value="<?php 
    echo $storage['storage_name'];
    ?>"
                                   <?php 
    echo \in_array($storage['storage_name'], $selected_storages) ? 'checked' : '';
    ?>
                                <?php 
    echo $storage['flussonic_dvr'] && !isset($checked_flussonic_dvr) || $storage['wowza_dvr'] && !isset($checked_wowza_dvr) || !$storage['flussonic_dvr'] && !$storage['wowza_dvr'] && isset($checked_flussonic_dvr) ? 'disabled' : '';
    ?>
                                />
                        </td>
                    </tr>
                    <?php 
}
?>
                </table>
            </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('TV archive duration, h');
?>:
                                    </td>
                                    <td>
                                        <input name="tv_archive_duration" id="tv_archive_duration" type="text"
                                               value="<?php 
echo isset($_GET['id']) ? @$tv_archive_duration : \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_parts_number', 168);
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Allow nPVR');
?>:
                                    </td>
                                    <td>
                                        <input name="allow_pvr" id="allow_pvr" type="checkbox"
                                               value="1" <?php 
echo @$checked_allow_pvr;
?>
                                               onchange="this.checked ? document.getElementById('pvr_storage_name').style.display = '' : document.getElementById('pvr_storage_name').style.display = 'none'">

                                        <span id="pvr_storage_name"
                                              style="display: <?php 
echo @$checked_allow_pvr ? '' : 'none';
?>">
                <table width="100%" style="background-color:#f8f8f8">
                    <?php 
foreach ($storages as $storage) {
    if ($storage['flussonic_dvr'] || $storage['wowza_dvr']) {
        continue;
    }
    ?>
                        <tr>
                        <td width="50%"><?php 
    echo $storage['storage_name'];
    ?>:</td>
                        <td width="50%">
                            <input type="checkbox" class="stream_server" name="pvr_storage_names[]"
                                   value="<?php 
    echo $storage['storage_name'];
    ?>" <?php 
    echo \in_array($storage['storage_name'], $selected_pvr_storages) ? 'checked' : '';
    ?>/>
                        </td>
                    </tr>
                        <?php 
}
?>
                </table>
            </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Allow USB TimeShift');
?>:
                                    </td>
                                    <td>
                                        <input name="allow_local_timeshift" id="allow_local_timeshift" type="checkbox"
                                               value="1" <?php 
echo isset($checked_allow_local_timeshift) ? $checked_allow_local_timeshift : 'checked';
?> >
                                        <span style="color: #747474">(<?php 
echo \_('only for rtp and ffrt solutions');
?>)</span>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Allow USB PVR');
?>:
                                    </td>
                                    <td>
                                        <input name="allow_local_pvr" id="allow_local_pvr" type="checkbox"
                                               value="1" <?php 
echo isset($checked_allow_local_pvr) ? $checked_allow_local_pvr : 'checked';
?> >
                                    </td>
                                </tr>
                                <!--<tr>
           <td align="right">
            <?php 
?>:
           </td>
           <td>
            <input id="enable_monitoring" name="enable_monitoring" type="checkbox" value="1" <?php 
?> onchange="this.checked ? document.getElementById('monitoring_url_tr').style.display = '' : document.getElementById('monitoring_url_tr').style.display = 'none'">
           </td>
        </tr>
        <tr id="monitoring_url_tr" style="display:<?php 
?>">
           <td align="right">
            <?php 
?>:
           </td>
           <td>
            <input id="monitoring_url" name="monitoring_url" size="50" type="text" value="<?php 
?>"> * <?php 
?>
           </td>
        </tr>-->
                                <tr>
                                    <td align="right">
                                        xmltv id:
                                    </td>
                                    <td>
                                        <input id="xmltv_id" name="xmltv_id" size="50" type="text"
                                               value="<?php 
echo @$xmltv_id;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('EPG correction') . ', ' . \_('min');
?>:
                                    </td>
                                    <td>
                                        <input id="correct_time" name="correct_time" size="50" type="text"
                                               value="<?php 
echo @$correct_time;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Service code');
?>:
                                    </td>
                                    <td>
                                        <input id="service_id" name="service_id" size="50" type="text"
                                               value="<?php 
echo @$service_id;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Volume correction');
?> (-20...20):
                                    </td>
                                    <td>
                                        <input id="volume_correction" name="volume_correction" size="50" type="text"
                                               value="<?php 
echo @$volume_correction;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Comments');
?>:
                                    </td>
                                    <td>
                                        <textarea id="descr" name="descr" cols="39"
                                                  rows="5"><?php 
echo @$descr;
?></textarea>
                                    </td>
                                </tr>
                                <?php 
if (!empty($logo)) {
    ?>
                                    <tr>
                                        <td align="right"></td>
                                        <td valign="top" id="logo_block">
                                            <img src="<?php 
    echo \Ministra\Lib\Itv::getLogoUriById((int) $_GET['id']) . '?' . \time();
    ?>"
                                                 style="float: left;"/><a href="javascript://"
                                                                          onclick="delete_logo(<?php 
    echo (int) $_GET['id'];
    ?>); return false;"
                                                                          style="float: left;">[x]</a>
                                        </td>
                                    </tr>
                                <?php 
}
?>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Logo');
?>:
                                    </td>
                                    <td>
                                        <input type="file" name="logo" id="logo"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="button"
                                               value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>"
                                               onclick="save()">&nbsp;
                                        <input type="button"
                                               value="<?php 
echo \htmlspecialchars(\_('Cancel'), \ENT_QUOTES);
?>"
                                               onclick="document.location='add_itv.php'">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

