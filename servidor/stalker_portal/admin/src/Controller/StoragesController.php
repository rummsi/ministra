<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\KaraokeMaster;
use Ministra\Lib\TvArchive;
use Ministra\Lib\VideoMaster;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class StoragesController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    private $allServerStatus = array();
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->allServerStatus = [['id' => 1, 'title' => $this->setLocalization('Unpublished')], ['id' => 2, 'title' => $this->setLocalization('Published')]];
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/storages-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function storages_list()
    {
        $attribute = $this->getListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $dvr_type = $this->db->getEnumValues('storages', 'dvr_type');
        $this->app['dvrType'] = \array_combine(\array_values($dvr_type), \array_map('ucfirst', \str_replace('_dvr', ' DVR', $dvr_type)));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getListDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'storage_name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'storage_ip', 'title' => $this->setLocalization('IP'), 'checked' => true], ['name' => 'nfs_home_path', 'title' => $this->setLocalization('Home directory'), 'checked' => true], ['name' => 'max_online', 'title' => $this->setLocalization('Maximum users'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operation'), 'checked' => true]];
    }
    public function storages_video_search()
    {
        $attribute = $this->getSearchDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['dropdownStorages'] = \array_map(function ($val) {
            return ['name' => $val['storage_name'], 'title' => $val['storage_name'], 'checked' => false];
        }, $this->db->getListList(['where' => ['status' => 1]]));
        $this->app['dropdownQuality'] = [['name' => 'HD', 'title' => 'HD', 'checked' => false], ['name' => 'SD', 'title' => 'SD', 'checked' => false]];
        $this->app['dropdownStatus'] = \array_map(function ($val) {
            $val['name'] = $val['id'];
            $val['checked'] = false;
            return $val;
        }, $this->allServerStatus);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getSearchDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'path', 'title' => $this->setLocalization('Catalogue'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'hd', 'title' => $this->setLocalization('Video quality'), 'checked' => true], ['name' => 'on_storages', 'title' => $this->setLocalization('Storage quantity'), 'checked' => true], ['name' => 'count', 'title' => $this->setLocalization('All views'), 'checked' => true], ['name' => 'month_counter', 'title' => $this->setLocalization('Views per month'), 'checked' => true], ['name' => 'last_played', 'title' => $this->setLocalization('Last view'), 'checked' => true], ['name' => 'accessed', 'title' => $this->setLocalization('Status'), 'checked' => true]];
    }
    public function storages_logs()
    {
        $attribute = $this->getLogsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getLogsDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => false], ['name' => 'added', 'title' => $this->setLocalization('Time'), 'checked' => true], ['name' => 'log_txt', 'title' => $this->setLocalization('Message'), 'checked' => true]];
    }
    public function reset_cache()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Error');
        if ($this->postData['id'] != 'all') {
            $result = $this->db->getListList(['select' => ['storage_name'], 'where' => ['id' => $this->postData['id']]]);
            $names = ['storage_name' => $result[0]['storage_name']];
        } else {
            $names = [];
        }
        $result = $this->db->updateStorageCache(['changed' => '0000-00-00 00:00:00'], $names);
        if (\is_numeric($result)) {
            $data['msg'] = $this->setLocalization('A cache has been reset') . (!empty($names) ? ' ' . $this->setLocalization('for') . ' ' . \implode(', ', $names) : ' ' . $this->setLocalization('for all servers'));
            $error = '';
            if (!empty($names) && $this->postData['id'] != 'all') {
                $data['id'] = $this->postData['id'];
                $data = \array_merge_recursive($data, $this->storages_list_json(true));
                $data['action'] = 'updateTableRow';
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function storages_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => ''];
        $filds_for_select = $this->getListFields();
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getListTotalRows();
        $response['recordsFiltered'] = $this->db->getListTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (!empty($param['id']) && \is_numeric($param['id'])) {
            $query_param['where']['S.`id`'] = $param['id'];
        }
        $tv_archive = new \Ministra\Lib\TvArchive();
        $response['data'] = \array_map(function ($row) use($tv_archive) {
            $tasks = $tv_archive->getAllTasks($row['storage_name']);
            $row['tasks'] = (int) (!empty($tasks));
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getListList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getListFields()
    {
        return ['id' => 'S.`id` as `id`', 'storage_name' => 'S.`storage_name` as `storage_name`', 'storage_ip' => 'S.`storage_ip` as `storage_ip`', 'nfs_home_path' => 'S.`nfs_home_path` as `nfs_home_path`', 'max_online' => 'S.`max_online` as `max_online`', 'status' => 'S.`status` as `status`'];
    }
    public function refresh_cache()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        \ob_start();
        \ob_implicit_flush(true);
        \header('Content-Type: application/json');
        \ob_flush();
        \sleep(1);
        $data = [];
        $data['action'] = 'updateTableData';
        $updated_video = 0;
        $updated_karaoke = 0;
        $not_custom_video = $this->db->getNoCustomVideo();
        $data['msg'] = '';
        $_SERVER['TARGET'] = 'ADM';
        foreach ($not_custom_video as $row) {
            \set_time_limit(30);
            \ob_start();
            \ob_implicit_flush(false);
            $master = new \Ministra\Lib\VideoMaster();
            $master->getAllGoodStoragesForMediaFromNet($row, true, true);
            \ob_end_clean();
            unset($master);
            ++$updated_video;
        }
        $not_custom_karaoke = $this->db->getNoCustomKaraoke();
        foreach ($not_custom_karaoke as $row) {
            \set_time_limit(30);
            \ob_start();
            \ob_implicit_flush(false);
            $master = new \Ministra\Lib\KaraokeMaster();
            $master->getAllGoodStoragesForMediaFromNet($row, true, true);
            \ob_end_clean();
            unset($master);
            ++$updated_karaoke;
        }
        \ob_end_clean();
        $error = '';
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_storage()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'showAdModalBox';
        $result = $this->db->getListList(['select' => ['*'], 'where' => ['id' => $this->postData['id']]]);
        $data['storage'] = $result[0];
        $data['storage']['title'] = $this->setLocalization('Edit storage');
        $error = '';
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_storage()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['form'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $storage = [$this->postData['form']];
        $error = $this->setLocalization('Failed');
        if (!empty($storage[0]['storage_name']) && !empty($storage[0]['storage_ip']) && !empty($storage[0]['apache_port'])) {
            $storage[0]['for_records'] = !empty($storage[0]['dvr_type']);
            foreach (['wowza_dvr', 'flussonic_dvr', 'nimble_dvr'] as $dvr_fields) {
                $storage[0][$dvr_fields] = (int) ($storage[0]['dvr_type'] == $dvr_fields);
            }
            if (!$this::checkStorageMayChanging($storage[0])) {
                $db_param = ['where' => \array_key_exists('id', $storage[0]) ? ['id' => $storage[0]['id']] : ['storage_name' => $storage[0]['storage_name']]];
                $old_data = $this->db->getListList($db_param);
                $data['additional'] = $this->getAdditionalAJAXDataForStorage($old_data[0]);
            }
            if (empty($this->postData['form']['id'])) {
                $operation = 'insertStorages';
            } else {
                $operation = 'updateStorages';
                $data['id'] = $storage['id'] = $this->postData['form']['id'];
            }
            unset($storage[0]['id']);
            $result = \call_user_func_array([$this->db, $operation], $storage);
            if (\is_numeric($result)) {
                $error = '';
                $data['msg'] = $this->setLocalization('Saved');
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                if ($operation == 'updateStorages') {
                    $this->postData['id'] = $this->postData['form']['id'];
                    $data = \array_merge_recursive($data, $this->storages_list_json(true));
                    $data['action'] = 'updateTableRow';
                    $data['msg'] = $this->setLocalization('Changed');
                }
            }
        } else {
            $error = $data['msg'] = $this->setLocalization('Fill in the required fields');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public static function checkStorageMayChanging($params = array())
    {
        if (\array_key_exists('id', $params) || \array_key_exists('storage_name', $params)) {
            $db = false;
            $modelName = 'Ministra\\Admin\\Model\\StoragesModel';
            if (\class_exists($modelName)) {
                $db = new $modelName();
                if (!$db instanceof $modelName) {
                    return;
                }
            }
            $db_param = ['where' => \array_key_exists('id', $params) ? ['id' => $params['id']] : ['storage_name' => $params['storage_name']]];
            $storage = $db->getListList($db_param);
            if (!empty($storage)) {
                foreach (['id', 'storage_name', 'status', 'dvr_type'] as $key) {
                    if (\array_key_exists($key, $params)) {
                        \settype($storage[0][$key], \gettype($params[$key]));
                        if (!empty($storage[0][$key]) && (empty($params[$key]) || $params[$key] != $storage[0][$key])) {
                            $tv_archive = new \Ministra\Lib\TvArchive();
                            return empty($tv_archive->getAllTasks($storage[0]['storage_name']));
                        }
                    }
                }
            }
            return true;
        }
    }
    private function getAdditionalAJAXDataForStorage($data)
    {
        $storage_name = !empty($data['storage_name']) ? $data['storage_name'] : 'undefined';
        return ['modal_data' => ['title' => $this->setLocalization('Attention please') . '!', 'body' => ['info' => $this->setLocalization('Storage "{strg}" has linked channels, check please channels settings', '', $storage_name, ['{strg}' => $storage_name]), 'link' => ['title' => $this->setLocalization('List of linked channels'), 'src' => $this->workURL . '/tv-channels/iptv-list?filters[storage]=' . $storage_name]], 'buttons' => ['cancel' => ['title' => $this->setLocalization('Close')]]]];
    }
    public function toggle_storages_status()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || !\array_key_exists('status', $this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $check_may_changing = $this::checkStorageMayChanging(['id' => $this->postData['id'], 'status' => (int) $this->postData['status'] == 1 ? 0 : 1]);
        $result = $this->db->updateStorages(['status' => (int) (!(bool) $this->postData['status'])], $this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data = \array_merge_recursive($data, $this->storages_list_json(true));
            $data['action'] = 'updateTableRow';
            $data['msg'] = $this->setLocalization('Changed');
        }
        if (!$check_may_changing) {
            $data['additional'] = $this->getAdditionalAJAXDataForStorage($data['data'][0]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function remove_storage()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $old_data = $this->db->getListList(['where' => ['id' => $this->postData['id']]]);
        $tv_archive = new \Ministra\Lib\TvArchive();
        $tasks = $tv_archive->getAllTasks($old_data[0]['storage_name']);
        if (!empty($tasks)) {
            $data['additional'] = $this->getAdditionalAJAXDataForStorage($old_data[0]);
        }
        $result = $this->db->deleteStorages($this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data['msg'] = $this->setLocalization('Deleted') . ' ' . (!empty($result) ? $result : '');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function storages_video_search_json()
    {
        $param = !empty($this->data) ? $this->data : $this->postData;
        if (!$this->isAjax && empty($param['textview'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = $this->getSearchFields();
        $error = $this->setLocalization('Error');
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $like_filter = $having = $query_param['having'] = [];
        $filter = $this->getSearchFilters($like_filter, $having);
        if (empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = $like_filter;
        } elseif (!empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = \array_merge($query_param['like'], $like_filter);
        }
        if (empty($query_param['having']) && !empty($having)) {
            $query_param['having'] = $having;
        } elseif (!empty($query_param['having']) && !empty($having)) {
            $query_param['having'] = \array_merge($query_param['having'], $having);
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $query_param['select'] = \array_values($filds_for_select);
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($query_param['like']['count(`storage_name`)'])) {
            unset($query_param['like']['count(`storage_name`)']);
        }
        $response['recordsTotal'] = $this->db->getTotalRowsVideoList($query_param['select']);
        $response['recordsFiltered'] = $this->db->getTotalRowsVideoList($query_param['select'], $query_param['where'], $query_param['like'], $query_param['having']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getVideoList($query_param);
        $response['data'] = \array_map(function ($row) {
            $last = \strtotime($row['last_played']);
            $row['last_played'] = $last <= 0 || $last === false ? '' : $last;
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if (!empty($param['textview'])) {
            \header('Content-Type: text/plain; charset=utf-8');
            $i = 1;
            foreach ($response['data'] as $row) {
                echo $i . "\t" . $row['path'] . "\t" . $row['on_storages'] . "\r\n";
                ++$i;
            }
            exit;
        }
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getSearchFields()
    {
        return ['id' => '`video`.`id` as `id`', 'path' => '`video`.`path` as `path`', 'name' => '`video`.`name` as `name` ', 'hd' => "if(`video`.`hd` = 1, 'HD', 'SD') as `hd` ", 'on_storages' => 'count(`storage_name`) as `on_storages`', 'count' => '`video`.`count` as `count` ', 'month_counter' => '(`video`.`count_first_0_5` + `video`.`count_second_0_5`) as `month_counter`', 'last_played' => 'cast(`video`.`last_played` as char) as `last_played` ', 'accessed' => '`video`.`accessed` as `accessed`', 'tasks' => '(select count(*) from `moderator_tasks` where `moderator_tasks`.`ended` = 0 and `moderator_tasks`.`media_id`= `video`.`id`) as `tasks`', 'storages' => 'GROUP_CONCAT(`storage_name`) as `storages`'];
    }
    private function getSearchFilters(&$like_filter, &$having)
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            $on_storages = \array_key_exists('on_storages', $this->data['filters']) ? $this->data['filters']['on_storages'] : [];
            $not_on_storages = \array_key_exists('not_on_storages', $this->data['filters']) ? $this->data['filters']['not_on_storages'] : [];
            $search = \array_search('all', $on_storages);
            if ($search !== false) {
                unset($on_storages[$search]);
            }
            $search = \array_search('all', $not_on_storages);
            if ($search !== false) {
                unset($not_on_storages[$search]);
            }
            if (!empty($this->data['filters']['status'][0]) && $this->data['filters']['status'][0] != 'all') {
                if ($this->data['filters']['status'][0] == 2) {
                    $return['video.accessed'] = 1;
                } else {
                    $return['video.accessed'] = 0;
                }
            }
            if (\array_key_exists('quality', $this->data['filters']) && \strtolower($this->data['filters']['quality'][0]) != 'all') {
                $return['video.hd'] = (int) (\strtolower($this->data['filters']['quality'][0]) == 'hd');
            }
            if (isset($this->data['filters']['total_storages']) && $this->data['filters']['total_storages'] != '') {
                $having['`on_storages`'] = (int) $this->data['filters']['total_storages'];
            }
            foreach ($on_storages as $value) {
                $having["`storages` like '%" . $value . "%' and '1'"] = '1';
            }
            foreach ($not_on_storages as $value) {
                $having["`storages` not like '%" . $value . "%' and '1'"] = '1';
            }
        }
        return $return;
    }
    public function storages_logs_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => ''];
        $filds_for_select = $this->getLogsFields();
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getLogsTotalRows();
        $response['recordsFiltered'] = $this->db->getLogsTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getLogsList($query_param);
        $response['data'] = \array_map(function ($row) {
            $row['added'] = (int) \strtotime($row['added']);
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getLogsFields()
    {
        return ['id' => 'M_L.`id` as `id`', 'added' => 'CAST(M_L.`added` AS CHAR) as `added`', 'log_txt' => 'M_L.`log_txt` as `log_txt`'];
    }
    public function check_linked_channels()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkLinkedChannelsResult';
        $result = $this->db->getListList(['select' => ['*'], 'where' => ['id' => $this->postData['id']]]);
        if (!empty($result) && \is_array($result)) {
            $result = $result[0];
            if (!empty($this->postData['storage_name']) && $result['storage_name'] == $this->postData['storage_name']) {
                if ((string) $this->postData['dvr_type'] != (string) $result['dvr_type']) {
                    $tv_archive = new \Ministra\Lib\TvArchive();
                    $tasks = $tv_archive->getAllTasks($this->postData['storage_name']);
                    $data['msg'] = '';
                    $data['msg_list'] = [];
                    if (!empty($tasks)) {
                        $data['msg'] .= $this->setLocalization('This server has connected TV channels for recording a TV archive');
                        foreach ($tasks as $row) {
                            $data['msg_list'][$row['id'] . '_task'] = $this->setLocalization('Archive of channel with id - {chnl}', '', false, ['{chnl}' => $row['ch_id']]);
                        }
                    }
                    if ((string) $result['dvr_type'] == 'stalker_dvr') {
                        $nPVR = $this->db->getRecFilesByStorageName(['where' => ['storage_name' => $this->postData['storage_name']], 'groupby' => 'uid,ch_id']);
                        if (!empty($nPVR)) {
                            $data['msg'] .= !empty($data['msg']) ? ' ' . $this->setLocalization('and') . ' ' : $this->setLocalization('This server') . ' ';
                            $data['msg'] .= $this->setLocalization('is used for nPVR recording');
                            foreach ($nPVR as $row) {
                                $data['msg_list'][$row['id'] . '_npvr'] = $this->setLocalization('Records of channel with id - {tsk} , user id - {usr}', '', false, ['{tsk}' => $row['ch_id'], '{usr}' => $row['uid']]);
                            }
                        }
                    }
                    if (!empty($data['msg'])) {
                        $data['msg'] = $this->setLocalization('Attention') . '! ' . $data['msg'];
                    }
                }
            } else {
                $error = $this->setLocalization('You cannot change the name of storage');
            }
        } else {
            $error = $this->setLocalization('Storage not found');
        }
        $data['storage'] = $result;
        $data['storage']['title'] = $this->setLocalization('Edit storage');
        $error = '';
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
