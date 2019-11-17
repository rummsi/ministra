<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\KaraokeMaster;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class KaraokeController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        $allProtocols = [['id' => 'http', 'title' => 'HTTP'], ['id' => 'custom', 'title' => 'Custom URL']];
        if ($this->db->getTotalRowsKaraokeList(['protocol' => 'nfs'])) {
            \array_unshift($allProtocols, ['id' => 'nfs', 'title' => 'NFS']);
        }
        $this->app['allProtocols'] = $allProtocols;
        $this->app['allStatus'] = [['id' => 1, 'title' => $this->setLocalization('Unpublished')], ['id' => 2, 'title' => $this->setLocalization('Published')]];
        $attribute = $this->getDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $like_filter = [];
        $this->getKaraokeFilters($like_filter);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'singer', 'title' => $this->setLocalization('Artist'), 'checked' => true], ['name' => 'added', 'title' => $this->setLocalization('Added'), 'checked' => true], ['name' => 'protocol', 'title' => $this->setLocalization('Protocol'), 'checked' => true], ['name' => 'rtsp_url', 'title' => $this->setLocalization('URL'), 'checked' => true], ['name' => 'media_claims', 'title' => $this->setLocalization('Complaints'), 'checked' => true], ['name' => 'done', 'title' => $this->setLocalization('Tasks'), 'checked' => true], ['name' => 'accessed', 'title' => $this->setLocalization('Conditions'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    private function getKaraokeFilters(&$like_filter)
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            if (\array_key_exists('status', $this->data['filters']) && $this->data['filters']['status'] != 0) {
                $return['`karaoke`.`accessed`'] = $this->data['filters']['status'] - 1;
            }
            if (\array_key_exists('protocol', $this->data['filters']) && !empty($this->data['filters']['protocol'])) {
                $return['`karaoke`.`protocol`'] = $this->data['filters']['protocol'];
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $return;
    }
    public function save_karaoke()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'manageKaraoke';
        $karaoke = [$this->postData];
        $error = $this->setLocalization('error');
        if (empty($this->postData['id'])) {
            $operation = 'insertKaraoke';
            $karaoke[0]['added'] = 'NOW()';
            $karaoke[0]['add_by'] = $this->admin->getId();
            if ($karaoke[0]['protocol'] == 'custom') {
                $karaoke[0]['status'] = 1;
            }
        } else {
            $operation = 'updateKaraoke';
            $data['id'] = $this->postData['karaokeid'] = $karaoke['id'] = $this->postData['id'];
            $data['action'] = 'updateTableRow';
        }
        unset($karaoke[0]['id']);
        if (!empty($this->postData['protocol']) && $this->postData['protocol'] != 'custom' || !empty($this->postData['rtsp_url']) && \preg_match('/^(\\w+\\s)?\\w+\\:\\/\\/.*$/i', $this->postData['rtsp_url'])) {
            $result = \call_user_func_array([$this->db, $operation], $karaoke);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                if ($operation != 'insertKaraoke') {
                    $data = \array_merge_recursive($data, $this->karaoke_list_json(true));
                }
            }
        } else {
            $data['msg'] = $this->setLocalization('Invalid format links');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function karaoke_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        if (!$local_uses) {
            $response['action'] = 'setKaraokeModal';
        }
        $filds_for_select = ['id' => '`karaoke`.`id` as `id`', 'name' => '`karaoke`.`name` as `name`', 'singer' => '`karaoke`.`singer` as `singer`', 'added' => 'CAST(`karaoke`.`added` AS CHAR) as `added`', 'protocol' => '`karaoke`.`protocol` as `protocol`', 'rtsp_url' => '`karaoke`.`rtsp_url` as `rtsp_url`', 'media_claims' => "CONCAT_WS(' / ', if(`media_claims`.`sound_counter`, `media_claims`.`sound_counter`, 0), if(`media_claims`.`video_counter`, `media_claims`.`video_counter`, 0)) as `media_claims`", 'done' => '`karaoke`.`done` as `done`', 'accessed' => '`karaoke`.`accessed` as `accessed`', 'status' => '`karaoke`.`status` as `status`'];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $like_filter = [];
        $filter = $this->getKaraokeFilters($like_filter);
        if (empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = $like_filter;
        } elseif (!empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = \array_merge($query_param['like'], $like_filter);
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'karaoke.id as id';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (\array_key_exists('accessed', $query_param['order']) && !\array_key_exists('status', $query_param['order'])) {
            $query_param['order']['status'] = 'DESC';
        }
        if (!\array_key_exists('status', $query_param['select'])) {
            $query_param['select'][] = '`karaoke`.`status` as `status`';
        }
        if (\array_key_exists('karaokeid', $param)) {
            $query_param['where']['karaoke.id'] = $param['karaokeid'];
        }
        if (empty($query_param['order'])) {
            $query_param['order']['added'] = 'DESC';
        }
        $response['recordsTotal'] = $this->db->getTotalRowsKaraokeList();
        $response['recordsFiltered'] = $this->db->getTotalRowsKaraokeList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getKaraokeList($query_param);
        $response['data'] = \array_map(function ($row) {
            $row['added'] = (int) \strtotime($row['added']);
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function remove_karaoke()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['karaokeid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['karaokeid'];
        $error = $this->setLocalization('Failed');
        if ($this->db->deleteKaraoke(['id' => $this->postData['karaokeid']])) {
            $error = '';
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function toggle_karaoke_done()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['karaokeid']) || !\array_key_exists('done', $this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['karaokeid'];
        $this->db->updateKaraoke(['done' => (int) (!(bool) $this->postData['done']), 'done_time' => 'NOW()'], $this->postData['karaokeid']);
        $data = \array_merge_recursive($data, $this->karaoke_list_json(true));
        $error = '';
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function toggle_karaoke_accessed()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['karaokeid']) || !\array_key_exists('accessed', $this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['karaokeid'];
        $error = $this->setLocalization('Failed');
        $goodStorages = [];
        $media_id = (int) $this->postData['karaokeid'];
        if (empty($_SERVER['TARGET'])) {
            $_SERVER['TARGET'] = 'ADM';
        }
        $where = ['karaoke.id' => $this->postData['karaokeid']];
        $item = $this->db->getKaraokeList(['select' => ['*', 'karaoke.id as id'], 'where' => $where]);
        \ob_start();
        if (($master = new \Ministra\Lib\KaraokeMaster()) && $item[0]['protocol'] != 'custom') {
            $goodStorages = $master->getAllGoodStoragesForMediaFromNet($media_id, 0, true);
            $this->db->updateKaraoke(['status' => (int) (\count($goodStorages) > 0)], $this->postData['karaokeid']);
        }
        \ob_end_clean();
        if (!empty($goodStorages) || $item[0]['protocol'] == 'custom' || (bool) $this->postData['accessed']) {
            if ($item[0]['protocol'] == 'custom' && empty($item[0]['rtsp_url'])) {
                $error = $this->setLocalization('You can not publishing record with protocol - "custom", and with empty field - URL');
            } else {
                $this->db->updateKaraoke(['accessed' => (int) (!(bool) $this->postData['accessed']), 'added' => 'NOW()'], $this->postData['karaokeid']);
                $data = \array_merge_recursive($data, $this->karaoke_list_json(true));
                $error = '';
            }
        } else {
            $error = $this->setLocalization('File unavailable and cannot be published');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function check_karaoke_source()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['karaokeid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['data' => [], 'base_info' => $this->setLocalization('Information not available')];
        $error = $this->setLocalization('Error');
        $media_id = (int) $this->postData['karaokeid'];
        $karaoke_data = $this->karaoke_list_json(true);
        $data = \array_merge_recursive($data, $karaoke_data);
        $data['action'] = 'checkSourceKaraoke';
        if (!empty($data['data']) && $data['data'][0]['protocol'] != 'custom') {
            if (empty($_SERVER['TARGET'])) {
                $_SERVER['TARGET'] = 'ADM';
            }
            \ob_start();
            if ($master = new \Ministra\Lib\KaraokeMaster()) {
                $good_storages = $master->getAllGoodStoragesForMediaFromNet($media_id, 0, true);
                $data['data'][0]['status'] = (int) (\count($good_storages) > 0);
                $this->db->updateKaraoke(['status' => $data['data'][0]['status']], $media_id);
                $data['base_info'] = [];
                $file = $media_id . '.mpg';
                $error = '';
                foreach ($good_storages as $name => $val) {
                    $data['base_info'][] = ['storage_name' => $name, 'file' => $file];
                }
            }
            \ob_end_clean();
        } else {
            $data['msg'] = $data['base_info'];
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
}
