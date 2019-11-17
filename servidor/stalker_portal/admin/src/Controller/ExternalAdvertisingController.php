<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Constraints as Assert;
class ExternalAdvertisingController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/company-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function company_list()
    {
        $curr_company_count = $this->db->getCompanyList(null, true);
        if (empty($curr_company_count)) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/company-add');
        }
        if (empty($this->data['filters'])) {
            $this->data['filters'] = [];
        }
        $attribute = $this->getDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getDropdownAttribute()
    {
        $attribute = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'platform', 'title' => $this->setLocalization('Platform'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function register()
    {
        if ($this->method == 'POST' && \array_key_exists('form', $this->postData)) {
            $data = $this->postData['form'];
        } else {
            $data = [];
        }
        $form = $this->buildRegisterForm($data);
        if ($this->saveRegisterData($form)) {
            if (!empty($data['submit_type'])) {
                if ($data['submit_type'] == 'skip') {
                    return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/settings');
                }
                if ($data['submit_type'] == 'save') {
                    try {
                        if ($data['region'] != 'other') {
                            $country_field_name = $this->app['language'] == 'ru' ? 'name' : 'name_en';
                            $countries = $this->db->getAllFromTable('countries');
                            $countries = \array_combine($this->getFieldFromArray($countries, 'iso2'), $this->getFieldFromArray($countries, $country_field_name));
                            $data['region'] = \array_key_exists($data['region'], $countries) ? $countries[$data['region']] : $this->setLocalization('undefined');
                        } else {
                            $data['region'] = $data['other_region'];
                        }
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153::e64dbcc2c6e34bea559d57f13c1f3d49($data['name'], $data['email'], $data['phone'], $data['region'] != 'other' ? $data['region'] : $data['other_region'], $data['num_users'], $data['website']);
                    } catch (\Exception $e) {
                    }
                }
                $this->app['breadcrumbs']->addItem($this->setLocalization('Congratulations!'));
                return $this->app['twig']->render($this->getTemplateName('ExternalAdvertising::register_confirm'));
            }
        }
        $this->app['form'] = $form->createView();
        $this->app['breadcrumbs']->addItem($this->setLocalization('Register'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildRegisterForm(&$data = array())
    {
        $builder = $this->app['form.factory'];
        $regions = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153::h39f19a48ffb12930872b820d9ede2703();
        $country_field_name = $this->app['language'] == 'ru' ? 'name' : 'name_en';
        $countries = $this->db->getAllFromTable('countries');
        $countries = \array_combine($this->getFieldFromArray($countries, 'iso2'), $this->getFieldFromArray($countries, $country_field_name));
        \reset($regions);
        while (list($num, $val) = \each($regions)) {
            $regions[$val] = \array_key_exists($val, $countries) ? $countries[$val] : '';
            if (\is_numeric($num)) {
                unset($regions[$num]);
            }
        }
        $regions = \array_filter($regions);
        if (empty($regions)) {
            $regions = [];
        }
        $regions['other'] = $this->setLocalization('Other');
        $form = $builder->createBuilder('form', $data)->add('submit_type', 'hidden')->add('name', 'text')->add('phone', 'text')->add('email', 'text')->add('num_users', 'text')->add('website', 'text')->add('region', 'choice', ['choices' => $regions, 'data' => empty($data['region']) ? '' : $data['region']])->add('other_region', 'text', ['required' => \array_key_exists('region', $data) && $data['region'] == 'Other'])->add('save', 'submit')->add('skip', 'submit');
        return $form->getForm();
    }
    private function saveRegisterData(&$form)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            if ($form->isValid()) {
                return true;
            }
        }
        return false;
    }
    public function company_add()
    {
        $form = $this->buildCompanyForm();
        if ($this->saveCompanyData($form)) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/company-list');
        }
        $this->app['form'] = $form->createView();
        $this->app['breadcrumbs']->addItem($this->setLocalization('List of campaigns'), $this->app['controller_alias'] . '/company-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Campaign add'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildCompanyForm(&$data = array(), $show = false)
    {
        $this->app['is_show'] = $show;
        $builder = $this->app['form.factory'];
        $sources = $this->db->getSourceList(['select' => ['E_A_S.id as id', 'E_A_S.source as source']]);
        $sources = \array_combine($this->getFieldFromArray($sources, 'id'), $this->getFieldFromArray($sources, 'source'));
        $platforms = ['stb' => 'Set-Top Box', 'ios' => 'iOS', 'android' => 'Android', 'smarttv' => 'SmartTV'];
        $platform_list = ['stb' => ['101' => ['label' => $this->setLocalization('Classic Launcher'), 'count' => 0], '201' => ['label' => $this->setLocalization('Smart Launcher'), 'count' => 0]], 'ios' => ['401' => ['label' => $this->setLocalization('iOS'), 'count' => 0]], 'android' => ['301' => ['label' => $this->setLocalization('Android'), 'count' => 0]], 'smarttv' => ['501' => ['label' => $this->setLocalization('SmartTV'), 'count' => 0]]];
        if (\array_key_exists('status', $data)) {
            \settype($data['status'], 'bool');
        }
        $ad_positions = $this->db->getAllFromTable('ext_adv_positions', 'position_code');
        $parts_labels = [];
        $parts_platform = [];
        foreach ($platforms as $platform => $label) {
            $platform_skip = $platform . '_skip';
            if (!\array_key_exists($platform, $parts_platform)) {
                $parts_platform[$platform] = [];
                $parts_platform[$platform_skip] = [];
            }
            if (!\array_key_exists($platform, $parts_labels)) {
                $parts_labels[$platform] = [];
            }
            $position_code_prefix = $position_code = '';
            foreach ($ad_positions as $row) {
                if (\in_array($row['position_code'], ['205', '203', '103'])) {
                    continue;
                }
                if ($position_code_prefix !== $row['position_code'][0]) {
                    $position_code_prefix = $row['position_code'][0];
                    $position_code = $row['position_code'];
                }
                if ($row['platform'] == $platform) {
                    $parts_labels[$platform][$row['position_code']] = $this->setLocalization($row['label']);
                    $parts_platform[$platform][$row['position_code']] = \array_key_exists($platform, $data) && \array_key_exists($row['position_code'], $data[$platform]) && $data[$platform][$row['position_code']] ? $data[$platform][$row['position_code']] : '';
                    $parts_platform[$platform_skip][$row['position_code']] = \array_key_exists($platform_skip, $data) && \array_key_exists($row['position_code'], $data[$platform_skip]) && $data[$platform_skip][$row['position_code']] ? true : false;
                }
                if (\array_key_exists($platform, $platform_list) && \array_key_exists($position_code, $platform_list[$platform])) {
                    ++$platform_list[$platform][$position_code]['count'];
                    $platform_list[$platform][$position_code]['prefix'] = $position_code_prefix;
                }
            }
            \ksort($parts_platform[$platform]);
        }
        $data = \array_merge($data, $parts_platform);
        $this->app['platform_list'] = $platform_list;
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('name', 'text', ['attr' => ['class' => 'form-control', 'data-validation' => 'required'], 'required' => true])->add('source', 'choice', ['choices' => $sources, 'required' => true, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($sources)])], 'attr' => ['readonly' => $show, 'disabled' => $show, 'class' => 'populate placeholder', 'data-validation' => 'required'], 'data' => empty($data['source']) ? '' : $data['source']])->add('platform', 'choice', ['choices' => $platforms, 'required' => true, 'attr' => ['readonly' => $show, 'disabled' => $show, 'class' => 'populate placeholder', 'data-validation' => 'required'], 'data' => empty($data['platform']) ? 'stb' : $data['platform']])->add('status', 'checkbox', ['label' => ' ', 'required' => false, 'label_attr' => ['class' => 'label-success'], 'attr' => ['readonly' => $show, 'disabled' => $show, 'class' => 'form-control']])->add('save', 'submit');
        $block_val = \array_combine(\range(1, 5), \range(1, 5));
        foreach ($platforms as $p_key => $p_label) {
            $form->add($p_key, 'collection', ['type' => 'choice', 'options' => ['required' => true, 'label' => $parts_labels[$p_key], 'choices' => $block_val, 'label_attr' => ['class' => 'control-label']], 'required' => true, 'allow_add' => true, 'allow_delete' => true, 'prototype' => false])->add($p_key . '_skip', 'collection', ['type' => 'checkbox', 'options' => ['required' => false], 'required' => false, 'allow_add' => true, 'allow_delete' => true, 'prototype' => false]);
        }
        return $form->getForm();
    }
    private function saveCompanyData(&$form)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            if ($form->isValid()) {
                $skip_field = [];
                if (\array_key_exists('form', $this->postData) && !empty($this->postData['form'][$data['platform'] . '_skip'])) {
                    $skip_field = $this->postData['form'][$data['platform'] . '_skip'];
                }
                $get_positions = [];
                foreach (['stb', 'ios', 'android', 'smarttv'] as $platform) {
                    if (\array_key_exists($platform, $data)) {
                        $get_positions += $data[$platform];
                    }
                }
                $get_positions = \array_filter($get_positions);
                if (!empty($data['id'])) {
                    $is_positions = $this->db->getAdPositions($data['id']);
                    if (!empty($is_positions)) {
                        $del_position = [];
                        if (!empty($get_positions)) {
                            while (list($num, $row) = \each($is_positions)) {
                                if (!\array_key_exists($row['position_code'], $get_positions) || $get_positions[$row['position_code']] != $row['blocks'] || !\array_key_exists($row['position_code'], $skip_field) || $skip_field[$row['position_code']] != $row['skip_after']) {
                                    $del_position[] = $row['position_code'];
                                } else {
                                    unset($get_positions[$row['position_code']]);
                                }
                            }
                        } else {
                            $del_position = $this->getFieldFromArray($is_positions, 'position_code');
                        }
                        if (!empty($del_position)) {
                            $this->db->delAdPositions($data['id'], $del_position);
                        }
                    }
                }
                $curr_fields = $this->db->getTableFields('ext_adv_campaigns');
                $curr_fields = $this->getFieldFromArray($curr_fields, 'Field');
                $curr_fields = \array_flip($curr_fields);
                $data = \array_intersect_key($data, $curr_fields);
                $data['updated'] = 'NOW()';
                if (!empty($data['id'])) {
                    $operation = 'update';
                    $id = $data['id'];
                    unset($data['id']);
                    $params = [$data, $id];
                } else {
                    $operation = 'insert';
                    $data['added'] = 'NOW()';
                    $params = [$data];
                }
                $result = \call_user_func_array([$this->db, $operation . 'CompanyData'], $params);
                if (\is_numeric($result)) {
                    if (!empty($get_positions)) {
                        $this->db->addAdPositions($operation == 'update' ? $id : $result, $get_positions, $skip_field);
                    }
                    return true;
                }
            }
        }
        return false;
    }
    public function company_edit()
    {
        if ($this->method == 'POST' && !empty($this->postData['form']['id'])) {
            $data = $this->postData['form'];
        } else {
            if ($this->method == 'GET' && !empty($this->data['id'])) {
                $data = $this->company_list_json(true);
                $data = !empty($data['data']) ? $data['data'][0] : [];
            }
        }
        if (empty($data)) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/company-add');
        }
        $data[$data['platform']] = [];
        $data[$data['platform'] . '_skip'] = [];
        $is_positions = $this->db->getAdPositions($data['id']);
        if (!empty($is_positions)) {
            $data[$data['platform']] = \array_combine($this->getFieldFromArray($is_positions, 'position_code'), $this->getFieldFromArray($is_positions, 'blocks'));
            $data[$data['platform'] . '_skip'] = \array_combine($this->getFieldFromArray($is_positions, 'position_code'), $this->getFieldFromArray($is_positions, 'skip_after'));
        }
        $form = $this->buildCompanyForm($data);
        if ($this->saveCompanyData($form)) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/company-list');
        }
        $this->app['form'] = $form->createView();
        $this->app['breadcrumbs']->addItem($this->setLocalization('List of campaigns'), $this->app['controller_alias'] . '/company-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Campaign edit') . ': "' . $data['name'] . '"');
        return $this->app['twig']->render($this->getTemplateName('ExternalAdvertising::company_add'));
    }
    public function company_list_json($local_use = false)
    {
        if (!$this->isAjax && $local_use === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = $this->getCompanyFields();
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
        if (!empty($param['id'])) {
            $query_param['where']['E_A_C.`id`'] = $param['id'];
        }
        if (!empty($this->app['reseller'])) {
            $query_param['joined'] = $this->getJoinedCompanyTables();
        }
        $response['recordsTotal'] = $this->db->getCompanyRowsList($query_param, 'ALL');
        $response['recordsFiltered'] = $this->db->getCompanyRowsList($query_param);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            \settype($row['status'], 'int');
            return $row;
        }, $this->db->getCompanyList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_use) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getCompanyFields()
    {
        return ['id' => 'E_A_C.`id` as `id`', 'name' => 'E_A_C.`name` as `name`', 'source' => 'E_A_C.`source` as `source`', 'platform' => 'E_A_C.`platform` as `platform`', 'status' => 'E_A_C.`status` as `status`'];
    }
    private function getJoinedCompanyTables()
    {
        return ['ext_adv_sources as E_A_S' => ['left_key' => 'E_A_C.source', 'right_key' => 'E_A_S.id', 'type' => 'LEFT']];
    }
    public function settings()
    {
        if ($this->method == 'POST' && \array_key_exists('form', $this->postData)) {
            $data = $this->postData['form'];
        } else {
            $data = $this->db->getSourceList(['select' => ['E_A_S.id', 'E_A_S.source']]);
            if (!empty($data)) {
                $sources = $this->getFieldFromArray($data, 'source');
                $ids = $this->getFieldFromArray($data, 'id');
                $data = ['source' => !empty($sources) ? \array_combine($ids, $sources) : [''], 'new_source' => ['']];
            } else {
                $data = ['new_source' => ['']];
            }
        }
        $form = $this->buildSettingsForm($data);
        if ($this->saveSettingsData($form)) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/settings');
        }
        $this->app['form'] = $form->createView();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildSettingsForm(&$data = array(), $show = false)
    {
        $this->app['is_show'] = $show;
        $builder = $this->app['form.factory'];
        $form = $builder->createBuilder('form', $data);
        if (!empty($data['source'])) {
            $form->add('source', 'collection', ['entry_type' => 'text', 'entry_options' => ['attr' => ['class' => 'form-control', 'data-validation' => 'number']]]);
        }
        $form->add('new_source', 'collection', ['entry_type' => 'text', 'entry_options' => ['attr' => ['class' => 'form-control', 'data-validation' => 'number', 'data-validation-optional' => 'true']], 'required' => false, 'allow_add' => true])->add('save', 'submit');
        return $form->getForm();
    }
    private function saveSettingsData(&$form)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            if ($form->isValid()) {
                $curr_fields = $this->db->getTableFields('ext_adv_sources');
                $curr_fields = $this->getFieldFromArray($curr_fields, 'Field');
                $curr_fields = \array_flip($curr_fields);
                $old_sources = !empty($data['source']) ? $data['source'] : [];
                $new_sources = !empty($data['new_source']) ? $data['new_source'] : [];
                $data = \array_intersect_key($data, $curr_fields);
                $data['updated'] = 'NOW()';
                $result = 0;
                $params = ['updated' => 'NOW()'];
                foreach ($old_sources as $source_id => $source_val) {
                    if (\is_numeric($result)) {
                        $params['source'] = $source_val;
                        $result += !empty($source_val) ? $this->db->updateSourceData($params, $source_id) : $this->db->deleteSourceData($source_id);
                    } else {
                        $result = false;
                        break;
                    }
                }
                $params['added'] = 'NOW()';
                foreach ($new_sources as $source_val) {
                    if (!empty($source_val)) {
                        $params['source'] = $source_val;
                        if (\is_numeric($result) && \is_numeric($this->db->insertSourceData($params))) {
                            ++$result;
                        } else {
                            $result = false;
                            break;
                        }
                    }
                }
                if (\is_numeric($result) && $result > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    public function tos()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function toggle_company_state()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['id'];
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        $result = $this->db->updateCompanyData(['status' => empty($this->postData['status'])], $this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data = \array_merge_recursive($data, $this->company_list_json(true));
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function delete_company()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteCompanyData($this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
}
