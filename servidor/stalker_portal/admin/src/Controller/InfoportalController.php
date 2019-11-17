<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class InfoportalController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $allServices = array();
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->allServices = [['id' => 'main', 'title' => $this->setLocalization('Emergency services')], ['id' => 'help', 'title' => $this->setLocalization('Reference services')], ['id' => 'other', 'title' => $this->setLocalization('Other services')]];
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/phone-book');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function phone_book()
    {
        if (!empty($this->data['filters']['service']) && !\in_array($this->data['filters']['service'], $this->getFieldFromArray($this->allServices, 'id'))) {
            return $this->app->redirect($this->app['action_alias']);
        }
        $this->app['allServices'] = $this->allServices;
        $attribute = $this->getPhoneBoockDropdownAttribute();
        $attribute_filter = false;
        if (empty($this->data['filters']['service'])) {
            if (empty($this->data['filters'])) {
                $this->data['filters'] = ['service' => 'main'];
            } else {
                $this->data['filters']['service'] = 'main';
            }
        } else {
            $attribute_filter = "-filters-{$this->data['filters']['service']}";
        }
        \call_user_func_array([$this, 'checkDropdownAttribute'], [&$attribute, $attribute_filter]);
        $this->app['filters'] = $this->data['filters'];
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getPhoneBoockDropdownAttribute()
    {
        return [['name' => 'num', 'title' => $this->setLocalization('Order'), 'checked' => true], ['name' => 'title', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'number', 'title' => $this->setLocalization('Phone number'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function humor()
    {
        $attribute = $this->getHumorDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getHumorDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('Order'), 'checked' => true], ['name' => 'added', 'title' => $this->setLocalization('Date'), 'checked' => true], ['name' => 'anec_body', 'title' => $this->setLocalization('Text'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function save_phone_book_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $item = [$this->postData];
        $error = $this->setLocalization('error');
        if (\count($item) != 0 && !empty($item[0]['num']) && (int) $item[0]['num'] > 0) {
            if (empty($this->postData['id'])) {
                $operation = 'insertPhoneBoock';
                $available = !(bool) $this->db->getTotalRowsPhoneBoockList($this->postData['phoneboocksource'], ['num' => $this->postData['num']]);
            } else {
                $operation = 'updatePhoneBoock';
                $available = !(bool) $this->db->getTotalRowsPhoneBoockList($this->postData['phoneboocksource'], ['id<>' => $this->postData['id'], 'num' => $this->postData['num']]);
                $data['id'] = $item['id'] = $this->postData['id'];
            }
            unset($item[0]['id'], $item[0]['phoneboocksource']);
            if ($available) {
                $result = \call_user_func_array([$this->db, $operation], [$this->postData['phoneboocksource'], $item]);
                if (\is_numeric($result)) {
                    $error = '';
                    if ($result === 0) {
                        $data['nothing_to_do'] = true;
                    }
                    if ($operation == 'updatePhoneBoock') {
                        $data = \array_merge_recursive($data, $this->phone_book_list_json(true));
                        $data['action'] = 'updateTableRow';
                    } else {
                        $data['msg'] = $this->setLocalization('Added');
                    }
                }
            } else {
                $error = $this->setLocalization('This number is already in use') . '. ';
                $error .= $this->setLocalization('Closest free number') . ' - ' . $this->db->getFirstFreeNumber($this->postData['phoneboocksource']);
                $data['msg'] = $error;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function phone_book_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'setPhoneBookModal'];
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $like_filter = [];
        $filters = $this->getInfoportalFilters($like_filter);
        $table_prefix = !empty($filters['service']) ? $filters['service'] : 'main';
        $table_prefix = !empty($this->postData['phoneboocksource']) ? $this->postData['phoneboocksource'] : $table_prefix;
        unset($filters['service']);
        if (empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = $like_filter;
        } elseif (!empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = \array_merge($query_param['like'], $like_filter);
        }
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = '*';
        } else {
            $query_param['select'][] = 'id';
        }
        $response['recordsTotal'] = $this->db->getTotalRowsPhoneBoockList($table_prefix);
        $response['recordsFiltered'] = $this->db->getTotalRowsPhoneBoockList($table_prefix, $query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (\array_key_exists('id', $param)) {
            $query_param['where']['id'] = $param['id'];
        }
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getPhoneBoockList($table_prefix, $query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getInfoportalFilters(&$like_filter)
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            if (\array_key_exists('service', $this->data['filters']) && !empty($this->data['filters']['service']) && \in_array($this->data['filters']['service'], $this->getFieldFromArray($this->allServices, 'id'))) {
                $return['service'] = $this->data['filters']['service'];
            } else {
                $return['service'] = 'main';
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $return;
    }
    public function remove_phone_book_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = '';
        $result = $this->db->deletePhoneBoock($this->postData['phoneboocksource'], ['id' => $this->postData['id']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function save_humor_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $item = [$this->postData];
        $error = 'error';
        if (empty($this->postData['id'])) {
            $operation = 'insertHumor';
            $item[0]['added'] = 'NOW()';
        } else {
            $operation = 'updateHumor';
            $data['id'] = $item['id'] = $this->postData['id'];
        }
        unset($item[0]['id']);
        $result = \call_user_func_array([$this->db, $operation], $item);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            if ($operation == 'updateHumor') {
                $data = \array_merge_recursive($data, $this->humor_list_json(true));
                $data['action'] = 'updateTableRow';
            } else {
                $data['msg'] = $this->setLocalization('Added');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function humor_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'setHumorModal'];
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = '*';
        }
        if (\array_key_exists('added', $query_param['where'])) {
            $tmp = $query_param['where']['added'];
            unset($query_param['where']['added']);
            $query_param['where']['CAST(`added` as CHAR)'] = $tmp;
        }
        if (\array_key_exists('added', $query_param['like'])) {
            $tmp = $query_param['like']['added'];
            unset($query_param['like']['added']);
            $query_param['like']['CAST(`added` as CHAR)'] = $tmp;
        }
        $response['recordsTotal'] = $this->db->getTotalRowsHumorList();
        $response['recordsFiltered'] = $this->db->getTotalRowsHumorList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (\array_key_exists('id', $param)) {
            $query_param['where']['id'] = $param['id'];
        }
        $response['data'] = $this->db->getHumorList($query_param);
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
    public function remove_humor_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = '';
        $result = $this->db->deleteHumor(['id' => $this->postData['id']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
}
