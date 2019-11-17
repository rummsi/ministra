<?php

namespace Ministra\Admin\Controller;

use Ministra\Admin\Lib\Itv;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\Module;
use Ministra\Lib\Radio;
use Ministra\Lib\RemotePvr;
use Ministra\Lib\Video;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Constraints as Assert;
class TariffsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $allServiceTypes = array();
    private $allPackageTypes = array(array('id' => 'tv', 'title' => 'tv'), array('id' => 'video', 'title' => 'video'), array('id' => 'radio', 'title' => 'radio'), array('id' => 'module', 'title' => 'module'), array('id' => 'option', 'title' => 'option'));
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->allServiceTypes = [['id' => 'periodic', 'title' => $this->setLocalization('permanent')], ['id' => 'single', 'title' => $this->setLocalization('once-only')]];
    }
    public function index()
    {
        if (empty($this->app['action_alias']) || $this->app['action_alias'] == 'index') {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/tariff-plans');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function service_packages()
    {
        $attribute = $this->getServicePackagesDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allPackageTypes'] = $this->setLocalization($this->allPackageTypes, 'title');
        $this->app['allServices'] = [['id' => '2', 'title' => $this->setLocalization('complete')], ['id' => '1', 'title' => $this->setLocalization('Optional')]];
        $this->getTariffsFilters();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getServicePackagesDropdownAttribute()
    {
        return [['name' => 'external_id', 'title' => $this->setLocalization('External ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Package'), 'checked' => true], ['name' => 'users_count', 'title' => $this->setLocalization('Users'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Service'), 'checked' => true], ['name' => 'all_services', 'title' => $this->setLocalization('Access'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    private function getTariffsFilters()
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            if (!empty($this->data['filters']['type'])) {
                $return['type'] = $this->data['filters']['type'];
            }
            if (!empty($this->data['filters']['all_services'])) {
                $return['all_services'] = (int) $this->data['filters']['all_services'] - 1;
            }
            if (!empty($this->data['filters']['state'])) {
                $return['P_S_L.`set_state`'] = (int) $this->data['filters']['state'] - 1;
            }
            if (!empty($this->data['filters']['initiator'])) {
                $return['P_S_L.`initiator`'] = $this->data['filters']['initiator'];
            }
            if (!empty($this->data['filters']['package'])) {
                $return['S_P.`id`'] = (int) $this->data['filters']['package'];
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $return;
    }
    public function add_service_package()
    {
        $data = \array_key_exists('form', $this->postData) ? $this->postData['form'] : [];
        $form = $this->buildServicePackageForm($data);
        if ($this->saveServicePackageData($form)) {
            return $this->app->redirect($this->workURL . '/tariffs/service-packages');
        }
        $this->app['form'] = $form->createView();
        $this->app['servicePackageEdit'] = false;
        $this->app['breadcrumbs']->addItem($this->setLocalization('Service packages'), $this->app['controller_alias'] . '/service-packages');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Add package'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildServicePackageForm(&$data = array(), $edit = false)
    {
        $default_additional_service_options = ['tv' => ['enable_tv_archive' => ['label' => $this->setLocalization('TV archive'), 'values' => [['opt_val' => '', 'opt_label' => $this->setLocalization('Not specified')], ['opt_val' => 1, 'opt_label' => $this->setLocalization('Enabled')], ['opt_val' => 0, 'opt_label' => $this->setLocalization('Disabled')]], 'settings_link' => '<a id=setting_link_${id} target=blank href=' . $this->request->getBaseUrl() . '/tv-channels/edit-channel?id=${id}>' . $this->setLocalization('Settings') . ' ${name}</a>', 'data_name' => 'service_status', 'data_val' => 'status']]];
        $builder = $this->app['form.factory'];
        if (\array_key_exists('all_services', $data)) {
            \settype($data['all_services'], 'bool');
        } else {
            $data['all_services'] = false;
        }
        $all_services = $services = $service_options = [];
        if (!empty($data['id']) && ($package = $this->db->getPackageById($data['id'], ['service_in_package.id' => 'ASC']))) {
            $services = $this->getFieldFromArray($package, 'service_id');
            $service_options = [$data['type'] => \array_combine($services, $this->getFieldFromArray($package, 'options'))];
        }
        $data['service_options_json'] = \json_encode($service_options);
        $data['services'] = $services;
        $data['services_json'] = \json_encode($services);
        if (empty($data['service_type'])) {
            $data['service_type'] = 'periodic';
        }
        if (empty($data['type'])) {
            $data['type'] = 'tv';
        }
        $func = 'get_' . $data['type'] . '_services';
        $all_services = $this->{$func}();
        $choice_attr = [];
        if (\array_key_exists($data['type'], $default_additional_service_options)) {
            $data_key_option = $default_additional_service_options[$data['type']];
            $choice_attr = function ($allChoices, $currentChoiceKey) use($all_services, $data_key_option) {
                $return = [];
                foreach ($data_key_option as $option_row) {
                    $return["data-{$option_row['data_name']}"] = $all_services[$allChoices][$option_row['data_val']];
                }
                return $return;
            };
        }
        $all_services = \array_combine($this->getFieldFromArray($all_services, 'id'), $this->getFieldFromArray($all_services, 'name'));
        $allPackageTypes = \array_combine($this->getFieldFromArray($this->allPackageTypes, 'id'), $this->getFieldFromArray($this->allPackageTypes, 'title'));
        $allServiceTypes = \array_combine($this->getFieldFromArray($this->allServiceTypes, 'id'), $this->getFieldFromArray($this->allServiceTypes, 'title'));
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('external_id', 'text')->add('name', 'text', ['constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(), 'required' => true])->add('description', 'textarea', ['required' => false])->add('type', 'choice', ['choices' => $allPackageTypes, 'constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank()], 'required' => true])->add('service_type', 'choice', ['choices' => $allServiceTypes, 'required' => false, 'attr' => ['disabled' => !empty($data['type']) && $data['type'] !== 'video']])->add('price', 'text', ['required' => false, 'attr' => ['disabled' => !empty($data['type']) && $data['type'] !== 'video' || !empty($data['service_type']) && $data['service_type'] !== 'single']])->add('rent_duration', 'text', ['required' => false, 'attr' => ['disabled' => !empty($data['type']) && $data['type'] == 'video' || !empty($data['service_type']) && $data['service_type'] !== 'single']])->add('all_services', 'checkbox', ['required' => false, 'attr' => ['disabled' => empty($data['type']) || $data['type'] == 'module']])->add('services', 'choice', ['choices' => $all_services, 'multiple' => true, 'required' => false, 'attr' => ['disabled' => $data['all_services']], 'choice_attr' => $choice_attr])->add('services_json', 'hidden')->add('service_options_json', 'hidden')->add('save', 'submit');
        $this->app['default_additional_service_options'] = \json_encode($default_additional_service_options);
        $this->app['dvr_storages'] = $this->getStorages();
        $tv_archive_type = $this->db->getEnumValues('itv', 'tv_archive_type');
        $tv_archive_type = \array_filter(\array_combine(\array_values($tv_archive_type), \array_map('ucfirst', \str_replace('_dvr', ' DVR', $tv_archive_type))));
        $this->app['tv_archive_type'] = $tv_archive_type;
        return $form->getForm();
    }
    private function getStorages()
    {
        $storages = $this->db->getStorages();
        $return = \array_fill_keys(['storage_names', 'flussonic_storage_names', 'wowza_storage_names', 'nimble_storage_names'], []);
        if (!empty($storages)) {
            $storage_names = $this->getFieldFromArray($storages, 'storage_name');
            $dvr_types = \array_combine($storage_names, $this->getFieldFromArray($storages, 'dvr_type'));
            $st_tmp = [];
            $return['storage_names'] = \array_combine($storage_names, $storage_names);
            foreach (['flussonic', 'wowza', 'nimble'] as $storage_type) {
                $st_tmp = \array_combine($storage_names, $this->getFieldFromArray($storages, $storage_type . '_dvr'));
                \array_walk($st_tmp, function (&$item, $key) use($dvr_types, $storage_type) {
                    if ($item || \str_replace(['_', 'dvr'], '', $dvr_types[$key]) == $storage_type) {
                        $item = $key;
                        return $key;
                    }
                    $item = false;
                    return $item;
                });
                $return["{$storage_type}_storage_names"] = \array_filter($st_tmp);
                $return['storage_names'] = \array_diff($return['storage_names'], $return["{$storage_type}_storage_names"]);
            }
        }
        return $return;
    }
    private function saveServicePackageData(&$form, $edit = false)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            $action = $edit ? 'updatePackage' : 'insertPackage';
            $package_external_id = $this->db->getTariffsList(['where' => ['external_id' => $data['external_id'], 'id<>' => $edit ? $data['id'] : '']]);
            $data['all_services'] = !empty($data['all_services']) ? (int) $data['all_services'] : 0;
            if (empty($data['service_type'])) {
                $data['service_type'] = 'periodic';
            }
            if ($edit && (!empty($package_external_id) && $package_external_id[0]['id'] != $data['id']) || !$edit && !empty($package_external_id)) {
                $form->get('external_id')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('ID already used')));
                return false;
            }
            if ($form->isValid()) {
                if (($parsed_json = \json_decode($data['services_json'], true)) && $parsed_json && \json_last_error() == JSON_ERROR_NONE) {
                    $data['services'] = $parsed_json;
                } else {
                    $data['services'] = [];
                }
                $param[] = \array_intersect_key($data, \array_flip($this->getFieldFromArray($this->db->getTableFields('services_package'), 'Field')));
                if ($edit && !empty($data['id'])) {
                    $param[] = $data['id'];
                    unset($param[0]['id']);
                    if ($package_external_id == $data['external_id']) {
                        unset($param[0]['external_id']);
                    }
                    $this->db->deleteServicesById($data['id']);
                }
                if ($return_val = \call_user_func_array([$this->db, $action], $param)) {
                    if (!empty($data['services'])) {
                        $service_options = \json_decode($data['service_options_json'], true);
                        foreach ($data['services'] as $service) {
                            $this->db->insertServices(['service_id' => $service, 'package_id' => $action == 'updatePackage' ? $data['id'] : $return_val, 'type' => $data['type'], 'options' => \array_key_exists($data['type'], $service_options) && \array_key_exists($service, $service_options[$data['type']]) ? $service_options[$data['type']][$service] : '{}']);
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }
    public function edit_service_package()
    {
        if ($this->method == 'POST' && !empty($this->postData['form']['id'])) {
            $id = $this->postData['form']['id'];
        } else {
            if ($this->method == 'GET' && !empty($this->data['id'])) {
                $id = $this->data['id'];
            } else {
                return $this->app->redirect('add-service-package');
            }
        }
        $package = $this->db->getTariffsList(['where' => ['services_package.id' => $id]]);
        $package = \is_array($package) && \count($package) > 0 ? $package[0] : [];
        $form = $this->buildServicePackageForm($package);
        if ($this->saveServicePackageData($form, true)) {
            return $this->app->redirect($this->workURL . '/tariffs/service-packages');
        }
        $this->app['form'] = $form->createView();
        $this->app['servicePackageEdit'] = true;
        $this->app['packageName'] = $package['name'];
        $this->app['breadcrumbs']->addItem($this->setLocalization('Service packages'), $this->app['controller_alias'] . '/service-packages');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Edit package') . ': "' . $package['name'] . '"');
        return $this->app['twig']->render($this->getTemplateName('Tariffs::add_service_package'));
    }
    public function tariff_plans()
    {
        $attribute = $this->getTariffPlansDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['dayLimits'] = [['id' => 'no_limit', 'title' => $this->setLocalization('No expiration')], ['id' => 'limit', 'title' => $this->setLocalization('With time limit')]];
        $this->app['filters'] = isset($this->data['filters']) ? $this->data['filters'] : [];
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getTariffPlansDropdownAttribute()
    {
        return [['name' => 'external_id', 'title' => $this->setLocalization('External ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Tariff name'), 'checked' => true], ['name' => 'days_to_expires', 'title' => $this->setLocalization('Validity period, days'), 'checked' => true], ['name' => 'users_count', 'title' => $this->setLocalization('Users'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function add_tariff_plans()
    {
        $form = $this->buildTariffPlanForm();
        if ($this->saveTariffPlanData($form)) {
            return $this->app->redirect($this->workURL . '/tariffs/tariff-plans');
        }
        $this->app['userDefault'] = $this->getDefaultPlan();
        $this->app['form'] = $form->createView();
        $this->app['servicePlanEdit'] = false;
        $this->app['breadcrumbs']->addItem($this->setLocalization('Tariff plans'), $this->app['controller_alias'] . '/tariff-plans');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Add tariff plan'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildTariffPlanForm(&$data = array(), $edit = false)
    {
        $builder = $this->app['form.factory'];
        if (\array_key_exists('user_default', $data)) {
            $val = $data['user_default'];
            \settype($data['user_default'], 'bool');
        } else {
            $val = false;
        }
        $tmp = $this->db->getTariffsList(['select' => ['id', 'name'], 'order' => ['id' => '']]);
        $all_packeges = \array_combine($this->getFieldFromArray($tmp, 'id'), $this->getFieldFromArray($tmp, 'name'));
        if (!empty($data['packages'])) {
            $data['packages_optional'] = \array_combine($this->getFieldFromArray($data['packages'], 'id'), $this->getFieldFromArray($data['packages'], 'optional'));
            $data['packages'] = $this->getFieldFromArray($data['packages'], 'id');
        } else {
            $data['packages'] = $data['packages_optional'] = [];
        }
        $data['packages_optional'] = \json_encode($data['packages_optional']);
        if (empty($data['notification_delay_in_hours'])) {
            $data['notification_delay_in_hours'] = [''];
        }
        if (empty($data['template_id'])) {
            $data['template_id'] = [''];
        }
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('external_id', 'text')->add('name', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank()], 'required' => true])->add('user_default', 'checkbox', ['required' => true, 'value' => $val])->add('packages', 'choice', ['choices' => $all_packeges, 'multiple' => true, 'required' => false])->add('packages_optional', 'hidden', ['required' => false])->add('days_to_expires', 'text')->add('save', 'submit');
        $notification_delay_label = ['' => '', 3 => '3 ' . $this->setLocalization('hours'), 24 => '24 ' . $this->setLocalization('hours'), 72 => '3 ' . $this->setLocalization('days'), 168 => '7 ' . $this->setLocalization('days'), 336 => '14 ' . $this->setLocalization('days'), 720 => '30 ' . $this->setLocalization('days')];
        $tmp = $this->db->getAllFromTable('messages_templates', 'created');
        $templates = \array_combine($this->getFieldFromArray($tmp, 'id'), $this->getFieldFromArray($tmp, 'title'));
        $templates = ['' => ''] + $templates;
        $this->app['notification_delay_labels'] = $notification_delay_label;
        $this->app['notification_templates'] = $templates;
        $form->add('notification_delay_in_hours', 'collection', ['type' => 'choice', 'options' => ['required' => true, 'choices' => $notification_delay_label, 'expanded' => false, 'multiple' => false], 'required' => true, 'allow_add' => true, 'allow_delete' => true, 'prototype' => true])->add('template_id', 'collection', ['type' => 'choice', 'options' => ['required' => true, 'choices' => $templates, 'expanded' => false, 'multiple' => false], 'required' => true, 'allow_add' => true, 'allow_delete' => true, 'prototype' => true]);
        return $form->getForm();
    }
    private function saveTariffPlanData(&$form, $edit = false)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            $action = $edit ? 'updatePlan' : 'insertPlan';
            $data['external_id'] = \trim($data['external_id']);
            if (!empty($data['external_id'])) {
                $param = ['where' => ['external_id' => $data['external_id']]];
                if (!empty($data['id'])) {
                    $param['where']['id<>'] = \trim($data['id']);
                }
                $plan = $this->db->getTariffPlansList($param);
                if (!empty($plan)) {
                    $form->get('external_id')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('ID already used')));
                    return false;
                }
            }
            if ($form->isValid()) {
                $param = [];
                $param[] = \array_intersect_key($data, \array_flip($this->getFieldFromArray($this->db->getTableFields('tariff_plan'), 'Field')));
                if ($edit && !empty($data['id'])) {
                    $param[] = $data['id'];
                    unset($param[0]['id']);
                }
                $return_val = \call_user_func_array([$this->db, $action], $param);
                if (\is_numeric($return_val)) {
                    $this->db->deletePackageInPlanById($data['id']);
                    if (!empty($data['packages_optional'])) {
                        $packages_optional = \json_decode($data['packages_optional']);
                        foreach ($packages_optional as $package => $option) {
                            $this->db->insertPackageInPlan(['plan_id' => $action == 'updatePlan' ? $data['id'] : $return_val, 'package_id' => $package, 'optional' => $option]);
                        }
                    }
                    $this->db->deleteTariffsNotifications($data['id']);
                    if (!empty($data['notification_delay_in_hours']) && \is_array($data['notification_delay_in_hours'])) {
                        $params = ['tariff_id' => $action == 'updatePlan' ? $data['id'] : $return_val];
                        foreach ($data['notification_delay_in_hours'] as $delay_key => $delay_val) {
                            if (!empty($delay_val)) {
                                $params['notification_delay_in_hours'] = $delay_val;
                                if (\array_key_exists($delay_key, $data['template_id'])) {
                                    $params['template_id'] = $data['template_id'][$delay_key];
                                }
                                $this->db->insertTariffsNotifications($params);
                            }
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }
    private function getDefaultPlan($curr_id = false)
    {
        $default_plan = $this->db->getUserDefaultPlan();
        if (!empty($default_plan) && $default_plan != $curr_id) {
            return true;
        }
        return false;
    }
    public function edit_tariff_plan()
    {
        if ($this->method == 'POST' && !empty($this->postData['form']['id'])) {
            $id = $this->postData['form']['id'];
        } else {
            if ($this->method == 'GET' && !empty($this->data['id'])) {
                $id = $this->data['id'];
            } else {
                return $this->app->redirect('add-service-package');
            }
        }
        $query_param = ['select' => ['*'], 'where' => [], 'like' => [], 'order' => []];
        $query_param['where']['tariff_plan.id'] = $id;
        $query_param['order'] = 'tariff_plan.id';
        $plan = $this->db->getTariffPlansList($query_param);
        $plan = \is_array($plan) && \count($plan) > 0 ? $plan[0] : [];
        $plan['days_to_expires'] = (int) $plan['days_to_expires'] ? $plan['days_to_expires'] : '';
        $plan['packages'] = $this->db->getOptionalForPlan(['select' => ['package_id as id', 'name', 'optional'], 'where' => ['plan_id' => $id], 'order' => ['package_in_plan.id' => '']]);
        $notifications = $this->db->getTariffsNotifications($id);
        if (!empty($notifications) && \is_array($notifications)) {
            $plan['notification_delay_in_hours'] = $this->getFieldFromArray($notifications, 'notification_delay_in_hours');
            $plan['template_id'] = $this->getFieldFromArray($notifications, 'template_id');
        }
        $form = $this->buildTariffPlanForm($plan);
        if ($this->saveTariffPlanData($form, true)) {
            return $this->app->redirect($this->workURL . '/tariffs/tariff-plans');
        }
        $this->app['userDefault'] = $this->getDefaultPlan($plan['id']);
        $this->app['form'] = $form->createView();
        $this->app['servicePlanEdit'] = true;
        $this->app['planName'] = $plan['name'];
        $this->app['breadcrumbs']->addItem($this->setLocalization('Tariff plans'), $this->app['controller_alias'] . '/tariff-plans');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Edit tariff plan') . ': "' . $plan['name'] . '"');
        return $this->app['twig']->render($this->getTemplateName('Tariffs::add_tariff_plans'));
    }
    public function subscribe_log()
    {
        $attribute = $this->getLogsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allInitiatorRoles'] = [['id' => 'user', 'title' => $this->setLocalization('User')], ['id' => 'admin', 'title' => $this->setLocalization('Administrator')], ['id' => 'api', 'title' => $this->setLocalization('API')]];
        $this->app['allPackageStates'] = [['id' => '1', 'title' => $this->setLocalization('off')], ['id' => '2', 'title' => $this->setLocalization('on')]];
        $this->app['allPackageNames'] = $this->db->getTariffsList(['select' => ['id', 'name as title'], 'where' => [], 'like' => [], 'order' => ['id' => 'ASC']]);
        if (!empty($this->data['user_id'])) {
            $currentUser = $this->db->getUser(['id' => (int) $this->data['user_id']]);
            $this->app['currentUser'] = ['name' => $currentUser['fname'], 'mac' => $currentUser['mac'], 'uid' => $currentUser['id']];
            $this->app['breadcrumbs']->addItem($this->setLocalization('Log of user') . ' ' . " {$this->app['currentUser']['name']} ({$this->app['currentUser']['mac']})");
        }
        $this->getTariffsFilters();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getLogsDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('User'), 'checked' => true], ['name' => 'package', 'title' => $this->setLocalization('Package name'), 'checked' => true], ['name' => 'state', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'initiator_name', 'title' => $this->setLocalization('Initiator'), 'checked' => true], ['name' => 'initiator', 'title' => $this->setLocalization('Initiator role'), 'checked' => true], ['name' => 'modified', 'title' => $this->setLocalization('Modified'), 'checked' => true]];
    }
    public function service_packages_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = ['id' => 'services_package.`id` as `id`', 'external_id' => 'services_package.`external_id` as `external_id`', 'name' => 'services_package.`name` as `name`', 'users_count' => '0 as `users_count`', 'type' => 'services_package.`type` as `type`', 'all_services' => 'services_package.`all_services` as `all_services`'];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'id';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $filter = $this->getTariffsFilters();
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $response['recordsTotal'] = $this->db->getTotalRowsTariffsList();
        $response['recordsFiltered'] = $this->db->getTotalRowsTariffsList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getTariffsList($query_param));
        $this->setUserCount($response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function setUserCount(&$data)
    {
        \reset($data);
        while (list($key, $row) = \each($data)) {
            $data[$key]['users_count'] = (int) $this->db->getUserCountForPackage($row['id']);
            $data[$key]['users_count'] += (int) $this->db->getUserCountForSubscription($row['id']);
        }
    }
    public function remove_service_package()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['packageid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['packageid'];
        $error = $this->setLocalization('Failed');
        $package_result = $this->db->deletePackageById($this->postData['packageid']);
        $services_result = $this->db->deleteServicesById($this->postData['packageid']);
        if (\is_numeric($package_result) && \is_numeric($services_result)) {
            $data['msg'] = $this->setLocalization('{pckg_rslt} packages and {srvcs_rslt} their services has been removed', '', [$package_result, $services_result], ['{pckg_rslt}' => $package_result, '{srvcs_rslt}' => $services_result]);
            $error = '';
            if ($package_result === 0 && $services_result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_services()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['type'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $package = [];
        if (!empty($this->postData['package_id'])) {
            $param = \array_intersect_key($this->postData, \array_flip($this->getFieldFromArray($this->db->getTableFields('service_in_package'), 'Field')));
            $package = $this->db->getPackageById($param);
            if (!empty($package)) {
                $package = $this->getFieldFromArray($package, 'service_id');
            }
        }
        $data = [];
        $data['action'] = 'updateService';
        $data['type'] = $this->postData['type'];
        $func = 'get_' . $this->postData['type'] . '_services';
        \ob_start();
        $data['services'] = $this->{$func}();
        if (!empty($data['services']) && \is_array($data['services'])) {
            $data['services'] = \array_values($data['services']);
            \reset($data['services']);
            while (list($key, $row) = \each($data['services'])) {
                \settype($data['services'][$key]['id'], 'string');
                $data['services'][$key]['selected'] = \in_array($data['services'][$key]['id'], $package);
            }
        }
        \ob_end_clean();
        $error = '';
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_external_id()
    {
        if (!$this->isAjax || $this->method != 'POST' || !\array_key_exists('externalid', $this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_external_id';
        if (\strlen(\trim((string) $this->postData['externalid'])) != 0) {
            $error = $this->setLocalization('ID already used');
            $param = ['where' => ['external_id' => \trim($this->postData['externalid'])], 'order' => ['id' => '']];
            if (!empty($this->postData['selfid'])) {
                $param['where']['id<>'] = \trim($this->postData['selfid']);
            }
            $method = 'getTariff' . (!empty($this->postData['plans']) ? 'Plan' : '') . 'sList';
            $result = $this->db->{$method}($param);
            if (!empty($result)) {
                $data['chk_rezult'] = $this->setLocalization('ID already used');
            } else {
                $data['chk_rezult'] = $this->setLocalization('ID is available');
                $error = '';
            }
        } else {
            $data['nothing_to_do'] = true;
            $data['chk_rezult'] = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function tariff_plans_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = ['id' => 'tariff_plan.`id` as `id`', 'external_id' => 'tariff_plan.`external_id` as `external_id`', 'name' => 'tariff_plan.`name` as `name`', 'days_to_expires' => 'tariff_plan.`days_to_expires`', 'users_count' => '(SELECT COUNT(*) FROM users WHERE (users.tariff_plan_id = tariff_plan.id) || IF(tariff_plan.user_default, tariff_plan_id = 0, 0)) AS users_count', 'user_default' => 'tariff_plan.`user_default` as `user_default`'];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'id';
            $query_param['select'][] = 'tariff_plan.`user_default` as `user_default`';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $dayLimits = isset($param['filters']['days']) ? $param['filters']['days'] : null;
        if ($dayLimits) {
            if ($dayLimits == 'no_limit') {
                $query_param['where']['days_to_expires ='] = 0;
            } elseif ($dayLimits == 'limit') {
                $query_param['where']['days_to_expires <>'] = 0;
            }
        }
        $response['recordsTotal'] = $this->db->getTotalRowsTariffPlansList();
        $response['recordsFiltered'] = $this->db->getTotalRowsTariffPlansList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['days_to_expires'] = $row['days_to_expires'] ?: '&#8734;';
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getTariffPlansList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        $this->app['filters'] = isset($param['filters']) ? $param['filters'] : [];
        return $response;
    }
    public function remove_tariff_plan()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['planid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['planid'];
        $error = $this->setLocalization('Failed');
        $tariff_result = $this->db->deleteTariffById($this->postData['planid']);
        $plans_result = $this->db->deletePlanById($this->postData['planid']);
        if (\is_numeric($tariff_result) && \is_numeric($plans_result)) {
            $data['msg'] = $this->setLocalization('{trff_rslt} tariff plans and {plns_rslt} their packages has been removed', '', [$tariff_result, $plans_result], ['{trff_rslt}' => $tariff_result, '{plns_rslt}' => $plans_result]);
            $error = '';
            if ($tariff_result === 0 && $plans_result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function subscribe_log_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = ['id' => 'P_S_L.`id` as `id`', 'mac' => 'CAST(U.`mac` AS CHAR) as `mac`', 'package' => 'S_P.`name` as `package`', 'state' => 'P_S_L.`set_state` as `state`', 'initiator_name' => 'IF(P_S_L.`initiator` = "admin", A.login, IF(P_S_L.`initiator` = "user" AND U.`login` <> "" AND NOT ISNULL(U.`login`) , U.`login`, P_S_L.`initiator`)) as `initiator_name`', 'initiator' => 'P_S_L.`initiator` as `initiator`', 'modified' => 'CAST(P_S_L.`modified` as CHAR) as `modified`'];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $filter = $this->getTariffsFilters();
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $user_id = false;
        if (!empty($this->data['user_id'])) {
            $query_param['where']['user_id'] = $user_id = (int) $this->data['user_id'];
        }
        $query_param['select'][] = 'P_S_L.`user_id` as `user_id`';
        $response['recordsTotal'] = $this->db->getTotalRowsSubscribeLogList([], [], $user_id);
        $response['recordsFiltered'] = $this->db->getTotalRowsSubscribeLogList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $self = $this;
        $response['data'] = \array_map(function ($row) use($self) {
            if ($row['initiator'] != 'admin' || $row['initiator_name'] == 'user') {
                $row['initiator_name'] = $self->setLocalization($row['initiator_name']);
            }
            $row['state'] = (int) $row['state'];
            $row['initiator'] = $self->setLocalization($row['initiator']);
            $row['modified'] = (int) \strtotime($row['modified']);
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getSubscribeLogList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function set_tvarchive_settings()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['form']['channel_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['id'] = $this->postData['form']['channel_id'];
        $error = $this->setLocalization('Failed');
        $curr_fields = $this->db->getTableFields('itv');
        $curr_fields = $this->getFieldFromArray($curr_fields, 'Field');
        $curr_fields = \array_flip($curr_fields);
        $db_data = \array_intersect_key($this->postData['form'], $curr_fields);
        \array_walk($db_data, function ($val) {
            return \is_string($val) ? \trim($val) : $val;
        });
        $result = $this->db->updateTVChannel($db_data, $this->postData['form']['channel_id']);
        if (\is_numeric($result)) {
            $data['msg'] = $this->setLocalization('Done');
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $archive_data = $this->postData['form'];
            $archive_data['id'] = $this->postData['form']['channel_id'];
            $storages_error = [];
            \ob_start();
            try {
                $this->deleteChannelTasks($archive_data);
                $this->createTasks($archive_data);
                $this->setAllowedStoragesForChannel($archive_data);
            } catch (\Exception $e) {
                $storages_error[] = $e->getMessage();
            }
            $storages_error[] = \ob_get_contents();
            \ob_end_clean();
            if (!empty($storages_error)) {
                $error = \implode('. ', \array_map(function ($row) {
                    return \strtok($row, "\n");
                }, $storages_error));
                $data['msg'] = $this->setLocalization('Found faulty storage') . '. ' . $this->setLocalization('Tv archive setting is not possible');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function deleteChannelTasks($data)
    {
        if ($data['tv_archive_type']) {
            $archive_class = '\\' . \ucfirst(\trim(\str_replace(['_dvr', 'stalker'], '', $data['tv_archive_type']))) . 'TvArchive';
            if (\class_exists($archive_class)) {
                $archive = new $archive_class();
                $archive->deleteTasks($data['id']);
            }
        }
    }
    private function createTasks($data)
    {
        if (!empty($data['tv_archive_type'])) {
            $storage_names = [];
            $archive_name = \trim(\str_replace(['_dvr', 'stalker'], '', $data['tv_archive_type']));
            $archive_class = '\\' . \ucfirst($archive_name) . 'TvArchive';
            if (\class_exists($archive_class)) {
                $archive = new $archive_class();
                $archive_storage = \trim($archive_name . '_storage_names', '_');
                if (!empty($data[$archive_storage])) {
                    $storage_names = $data[$archive_storage];
                }
                $archive->createTasks($data['id'], $storage_names);
            }
        }
    }
    private function setAllowedStoragesForChannel($data)
    {
        if ($data['allow_pvr']) {
            \Ministra\Lib\RemotePvr::setAllowedStoragesForChannel($data['id'], $data['pvr_storage_names']);
        }
    }
    public function check_tariff_name()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $id = isset($this->postData['id']) ? $this->postData['id'] : null;
        $tariffs = $this->db->totalItemsByName('tariff_plan', $this->postData['name'], $id);
        if ($tariffs > 0) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => false, 'message' => $this->setLocalization('Name already used')]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => true, 'message' => $this->setLocalization('Name is available')]);
    }
    public function check_package_name()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $id = isset($this->postData['id']) ? $this->postData['id'] : null;
        $packages = $this->db->totalItemsByName('services_package', $this->postData['name'], $id);
        if ($packages > 0) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => false, 'message' => $this->setLocalization('Name already used')]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => true, 'message' => $this->setLocalization('Name is available')]);
    }
    public function get_video_services()
    {
        return \Ministra\Lib\Video::getServices();
    }
    public function get_radio_services()
    {
        return \Ministra\Lib\Radio::getServices();
    }
    public function get_module_services()
    {
        return \Ministra\Lib\Module::getServices();
    }
    public function get_option_services()
    {
        $option_services = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('option_services', []);
        $result = \array_map(function ($item) {
            return ['id' => $item, 'name' => $item];
        }, $option_services);
        return $result;
    }
    public function get_tv_services()
    {
        $services = \Ministra\Admin\Lib\Itv::getServices();
        $services = \array_combine($this->getFieldFromArray($services, 'id'), $services);
        $params = ['select' => ['id', 'IF(`tv_archive_type` <> "" AND `mc_cmd` <> "", 1, 0) as `status`'], 'in' => ['id', \array_keys($services), false]];
        $tv_archive_statuses = $this->db->getArchiveStatus($params);
        $tv_archive_statuses = \array_combine($this->getFieldFromArray($tv_archive_statuses, 'id'), $tv_archive_statuses);
        return \array_map(function ($row) use($tv_archive_statuses) {
            $row['status'] = (int) (\array_key_exists($row['id'], $tv_archive_statuses) ? $tv_archive_statuses[$row['id']]['status'] : 0);
            return $row;
        }, $services);
    }
}
