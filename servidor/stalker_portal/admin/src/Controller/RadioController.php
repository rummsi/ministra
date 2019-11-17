<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Constraints as Assert;
class RadioController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $radio;
    protected $allStatus = array();
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        $attribute = $this->getDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allMonitoringStatus'] = [['id' => 1, 'title' => $this->setLocalization('monitoring off')], ['id' => 2, 'title' => $this->setLocalization('errors occurred')], ['id' => 3, 'title' => $this->setLocalization('no errors')]];
        $this->app['allStatus'] = [['id' => 1, 'title' => $this->setLocalization('Unpublished')], ['id' => 2, 'title' => $this->setLocalization('Published')]];
        $allVolumeVal = [];
        for ($i = -20; $i <= 20; ++$i) {
            if ($i == 0) {
                $i = '-0';
            }
            \array_push($allVolumeVal, ['val' => "{$i}", 'title' => $i * 5]);
        }
        $this->app['allVolumeVal'] = $allVolumeVal;
        $this->getRadioFilters();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => false], ['name' => 'number', 'title' => $this->setLocalization('Order'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'cmd', 'title' => $this->setLocalization('URL'), 'checked' => true], ['name' => 'volume_correction', 'title' => $this->setLocalization('Volume'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'monitoring_status', 'title' => $this->setLocalization('Monitoring status'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    private function getRadioFilters()
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            if (\array_key_exists('status_id', $this->data['filters']) && $this->data['filters']['status_id'] != 0) {
                $return['status'] = $this->data['filters']['status_id'] - 1;
            }
            if (\array_key_exists('monitoring_status', $this->data['filters']) && $this->data['filters']['monitoring_status'] != 0) {
                if ((int) $this->data['filters']['monitoring_status'] == 1) {
                    $return['enable_monitoring'] = 0;
                } else {
                    $return['enable_monitoring'] = 1;
                    $return['monitoring_status'] = (int) ($this->data['filters']['monitoring_status'] - 2);
                }
            }
            if (\array_key_exists('volume_correction', $this->data['filters']) && (string) $this->data['filters']['volume_correction'] !== '0') {
                $val = \trim($this->data['filters']['volume_correction']);
                $return['volume_correction'] = (int) $val;
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $return;
    }
    public function add_radio()
    {
        $form = $this->buildRadioForm();
        if ($this->saveRadioData($form)) {
            return $this->app->redirect('index');
        }
        $this->app['form'] = $form->createView();
        $this->app['radioEdit'] = false;
        $this->app['breadcrumbs']->addItem($this->setLocalization('Add radio'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildRadioForm(&$data = array(), $edit = false)
    {
        if (empty($data['number'])) {
            $data['number'] = $this->db->getFirstFreeNumber('radio');
        }
        $builder = $this->app['form.factory'];
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('number', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank(), new \Symfony\Component\Validator\Constraints\Regex(['pattern' => '/^\\d+$/'])], 'required' => true])->add('name', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank()], 'required' => true])->add('cmd', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank(), new \Symfony\Component\Validator\Constraints\Regex(['pattern' => '/^(\\w+\\s)?\\w+\\:\\/\\/.+$/i'])]])->add('volume_correction', 'choice', ['choices' => \array_combine(\range(-20, 20, 1), \range(-100, 100, 5)), 'constraints' => [new \Symfony\Component\Validator\Constraints\Range(['min' => -20, 'max' => 20])], 'required' => true, 'data' => empty($data['volume_correction']) ? 0 : $data['volume_correction']])->add('enable_monitoring', 'checkbox', ['required' => false])->add('save', 'submit');
        return $form->getForm();
    }
    private function saveRadioData(&$form, $edit = false)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            $action = isset($this->radio) && $edit ? 'updateRadio' : 'insertRadio';
            $param = ['number' => $data['number']];
            if (!empty($data['id'])) {
                $param['id<>'] = $data['id'];
            }
            $radio_num = $this->db->searchOneRadioParam($param);
            $param = ['name' => $data['name']];
            if (!empty($data['id'])) {
                $param['id<>'] = $data['id'];
            }
            $radio_name = $this->db->searchOneRadioParam($param);
            $data['volume_correction'] = !empty($data['volume_correction']) ? (int) \str_replace('%', '', $data['volume_correction']) : 0;
            if (!empty($radio_num) || !empty($radio_name)) {
                if (!empty($radio_num)) {
                    $form->get('number')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('This number is not unique')));
                }
                if (!empty($radio_name)) {
                    $form->get('name')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('This name is not unique')));
                }
                return false;
            }
            if ($form->isValid()) {
                $data = \array_intersect_key($data, \array_flip($this->getFieldFromArray($this->db->getTableFields('radio'), 'Field')));
                $param = [];
                $param[] = $data;
                if ($edit && !empty($data['id'])) {
                    $param[] = $data['id'];
                    unset($param[0]['id']);
                    if ($radio_num == $data['number']) {
                        unset($param[0]['number']);
                    }
                    if ($radio_name == $data['name']) {
                        unset($param[0]['name']);
                    }
                }
                $result = \call_user_func_array([$this->db, $action], $param);
                if (\is_numeric($result)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function edit_radio()
    {
        if ($this->method == 'POST' && !empty($this->postData['form']['id'])) {
            $id = $this->postData['form']['id'];
        } else {
            if ($this->method == 'GET' && !empty($this->data['id'])) {
                $id = $this->data['id'];
            } else {
                return $this->app->redirect('add-radio');
            }
        }
        $query_param = ['select' => ['*'], 'where' => [], 'like' => [], 'order' => []];
        $query_param['where']['radio.id'] = $id;
        $query_param['order'] = 'radio.id';
        $radio = $this->db->getRadioList($query_param);
        $this->radio = \is_array($radio) && \count($radio) > 0 ? $radio[0] : [];
        \settype($this->radio['enable_monitoring'], 'bool');
        $form = $this->buildRadioForm($this->radio);
        if ($this->saveRadioData($form, true)) {
            return $this->app->redirect('index');
        }
        $this->app['form'] = $form->createView();
        $this->app['radioEdit'] = true;
        $this->app['radioID'] = $id;
        $this->app['radioName'] = $this->radio['name'];
        $this->app['breadcrumbs']->addItem($this->setLocalization('Edit radio') . ': "' . $this->radio['name'] . '"');
        return $this->app['twig']->render($this->getTemplateName('Radio::add_radio'));
    }
    public function toggle_radio()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['radioid']) || !isset($this->postData['radiostatus'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['radioid'];
        $error = $this->setLocalization('Failed');
        if (($result = $this->db->toggleRadioStatus($this->postData['radioid'], (int) (!$this->postData['radiostatus']))) && \is_numeric($result)) {
            $error = '';
            $data = \array_merge_recursive($data, $this->radio_list_json(true));
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function radio_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        if (!empty($query_param['select'])) {
            $query_param['select'][] = 'monitoring_status_updated';
            $query_param['select'][] = 'enable_monitoring';
        }
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filter = $this->getRadioFilters();
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        if (!empty($param['radioid'])) {
            $query_param['where']['id'] = $param['radioid'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsRadioList();
        $response['recordsFiltered'] = $this->db->getTotalRowsRadioList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (!empty($query_param['order']) && \array_key_exists('monitoring_status', $query_param['order'])) {
            $tmp = ['enable_monitoring' => $query_param['order']['monitoring_status'], 'monitoring_status' => $query_param['order']['monitoring_status'], 'monitoring_status_updated' => 'DESC'];
            unset($query_param['order']['monitoring_status']);
            $query_param['order'] = \array_merge($query_param['order'], $tmp);
        }
        $response['data'] = $this->db->getRadioList($query_param);
        $this->db->setSQLDebug(0);
        if (\is_array($response['data'])) {
            \reset($response['data']);
            while (list($key, $row) = \each($response['data'])) {
                if ($monitoring_status = $this->getMonitoringStatus($row)) {
                    $response['data'][$key]['RowOrder'] = 'dTRow_' . $row['id'];
                    $response['data'][$key]['monitoring_status'] = $monitoring_status;
                } else {
                    unset($response['data'][$key]);
                }
            }
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getMonitoringStatus($row)
    {
        $return = '';
        if (!$row['enable_monitoring']) {
            $return .= $this->setLocalization('monitoring off');
        } else {
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $row['monitoring_status_updated']);
            $datetime = $datetime instanceof \DateTime && (int) $datetime->getTimestamp() > 0 ? $datetime->getTimestamp() : 0;
            $diff = \time() - $datetime;
            $msg_str = '';
            $msg_dgt = 0;
            switch (1) {
                case $datetime === 0:
                    $msg_str = \_('Date unknown');
                    break;
                case $diff > 86400:
                    $msg_str = \_('{{dgt}} day(s) ago');
                    $msg_dgt = \round($diff / 86400);
                    break;
                case $diff > 3600:
                    $msg_str = \_('{{dgt}} hour(s) ago');
                    $msg_dgt = \round($diff / 3600);
                    break;
                case $diff > 60:
                    $msg_str = \_('{{dgt}} minutes ago');
                    $msg_dgt = \round($diff / 60);
                    break;
                case $diff < 60:
                    $msg_str = \_('less than a minute ago');
                    break;
            }
            $return .= $this->setLocalization($msg_str, '', 0, ['{{dgt}}' => $msg_dgt]);
            $return .= '<br><span style="color: ';
            if ($row['monitoring_status'] == 1) {
                $return .= 'green;">' . $this->setLocalization('no errors');
            } else {
                $return .= 'red;">' . $this->setLocalization('errors occurred');
            }
            $return .= '</span>';
        }
        return $return;
    }
    public function remove_radio()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['radioid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['radioid'];
        $error = $this->setLocalization('Failed');
        if ($this->db->deleteRadioById($this->postData['radioid'])) {
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function radio_check_name()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['param'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_name';
        $error = $this->setLocalization('Name already used');
        if ($this->db->searchOneRadioParam(['name' => \trim($this->postData['param']), 'id<>' => \trim($this->postData['radioid'])])) {
            $data['chk_rezult'] = $this->setLocalization('Name already used');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Name is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function radio_check_number()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['param'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_number';
        if (\is_numeric($this->postData['param'])) {
            $error = $this->setLocalization('Number is not unique');
            if ($this->db->searchOneRadioParam(['number' => \trim($this->postData['param']), 'id<>' => \trim($this->postData['radioid'])])) {
                $data['chk_rezult'] = $this->setLocalization('Number is not unique');
            } else {
                $data['chk_rezult'] = $this->setLocalization('Number is unique');
                $error = '';
            }
        } else {
            $error = $data['chk_rezult'] = $this->setLocalization('This field can contain only numbers');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
