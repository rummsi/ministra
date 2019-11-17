<?php

namespace Ministra\OldAdmin;

use Exception;
use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Itv;
use Ministra\Lib\KaraokeMaster;
use Ministra\Lib\Video;
use Ministra\Lib\VideoMaster;
if (!\function_exists('Ministra\\OldAdmin\\get_data')) {
    function get_data()
    {
        $get = @$_GET['get'];
        $data = @$_POST['data'];
        $arr = [];
        if ($data) {
            switch ($get) {
                case 'del_tv_logo':
                    if (!\Ministra\Lib\Admin::isEditAllowed('add_itv')) {
                        \header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                        echo \_('Action "edit" denied for page "add_itv"');
                        exit;
                    }
                    return \Ministra\Lib\Itv::delLogoById((int) $_GET['id']);
                    break;
                case 'vclub_info':
                    $media_id = (int) $data;
                    $video = \Ministra\Lib\Video::getById($media_id);
                    $path = $video['path'];
                    $rtsp_url = $video['rtsp_url'];
                    if (!empty($rtsp_url)) {
                        $result['data'] = [];
                        return $result;
                    }
                    $master = new \Ministra\Lib\VideoMaster();
                    $good_storages = $master->getAllGoodStoragesForMediaFromNet($media_id, 0, true);
                    foreach ($good_storages as $name => $data) {
                        $arr[] = ['storage_name' => $name, 'path' => $path, 'series' => \count($data['series']), 'files' => $data['files'], 'tv_series' => $data['tv_series'], 'for_moderator' => $data['for_moderator']];
                    }
                    $result['data'] = $arr;
                    return $result;
                    break;
                case 'startmd5sum':
                    $resp = [];
                    if (\Ministra\Lib\Admin::isPageActionAllowed('add_video')) {
                        $master = new \Ministra\Lib\VideoMaster();
                        try {
                            $master->startMD5Sum($data['storage_name'], $data['media_name']);
                        } catch (\Exception $exception) {
                            $resp['error'] = $exception->getMessage();
                        }
                        return $resp;
                    }
                    $resp['error'] = 'У Вас нет прав на это действие';
                    return $resp;
                    break;
                case 'karaoke_info':
                    $media_id = (int) $data;
                    $master = new \Ministra\Lib\KaraokeMaster();
                    $good_storages = $master->getAllGoodStoragesForMediaFromNet($media_id, 0, true);
                    if (\count($good_storages) > 0) {
                        set_karaoke_status($media_id, 1);
                    } else {
                        set_karaoke_status($media_id, 0);
                    }
                    foreach ($good_storages as $name => $data) {
                        $arr[] = ['storage_name' => $name, 'file' => $media_id . '.mpg'];
                    }
                    $result['data'] = $arr;
                    return $result;
                    break;
                case 'chk_name':
                    return $result['data'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('video')->where(['name' => $data])->get()->counter();
                    break;
                case 'org_name_chk':
                    return $result['data'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('permitted_video')->where(['o_name' => $data['o_name'], 'year' => $data['year']])->get()->counter();
                    break;
                case 'get_cat_genres':
                    $category_alias = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->where(['id' => $data])->get()->first('category_alias');
                    $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->where(['category_alias' => $category_alias])->orderby('title')->get()->all();
                    $genres = \array_map(function ($genre) {
                        return ['id' => $genre['id'], 'title' => \_($genre['title'])];
                    }, $genres);
                    return ['data' => $genres];
                    break;
            }
        }
    }
}
