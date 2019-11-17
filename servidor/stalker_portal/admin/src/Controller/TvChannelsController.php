<?php

namespace Ministra\Admin\Controller;

use Imagine\Image\Box;
use Ministra\Admin\Model\TvChannelsModel;
use Ministra\Admin\Service\EpgParser\EpgParserService;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Epg;
use Ministra\Lib\Itv;
use Ministra\Lib\RemotePvr;
use Ministra\Lib\TvArchive;
use Silex\Application;
use Symfony\Component\Form\FormError as FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Constraints as Assert;
class TvChannelsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    const DATETIME_MYSQL = 'Y-m-d H:i:s';
    protected $db;
    private $logoDir;
    private $broadcasting_keys = array('cmd' => array(''), 'user_agent_filter' => array(''), 'priority' => array(''), 'enable_monitoring' => array(false), 'monitoring_status' => array(false), 'enable_balancer_monitoring' => array(''), 'monitoring_url' => array(''), 'use_load_balancing' => array(false), 'stream_server' => array(''));
    private $logoResolutions = array('320' => array('height' => 96, 'width' => 96), '240' => array('height' => 72, 'width' => 72), '160' => array('height' => 48, 'width' => 48), '120' => array('height' => 36, 'width' => 36));
    private $saveFiles;
    private $oneChannel;
    private $streamServers;
    private $streamers_map;
    private $channelLinks;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->logoDir = \str_replace('/admin', '', $this->baseDir) . '/misc/logos';
        $this->app['baseHost'] = $this->baseHost;
        $this->saveFiles = new \Ministra\Admin\Lib\Save($app);
        foreach ($this->getHttpTmpLink() as $row) {
            $this->broadcasting_keys[$row['value']] = $row['value'] == 'use_http_tmp_link' ? [false] : [''];
        }
    }
    private function getHttpTmpLink()
    {
        return [['value' => 'use_http_tmp_link', 'label' => $this->setLocalization('Ministra'), 'check_module' => false], ['value' => 'wowza_tmp_link', 'label' => $this->setLocalization('WOWZA'), 'check_module' => false], ['value' => 'nginx_secure_link', 'label' => $this->setLocalization('NGINX'), 'check_module' => false], ['value' => 'flussonic_tmp_link', 'label' => $this->setLocalization('Flussonic'), 'check_module' => false], ['value' => 'xtream_codes_support', 'label' => $this->setLocalization('Xtream-Codes'), 'check_module' => true], ['value' => 'edgecast_auth_support', 'label' => $this->setLocalization('EdgeCast'), 'check_module' => true], ['value' => 'akamai_auth_support', 'label' => $this->setLocalization('Akamai'), 'check_module' => false], ['value' => 'nimble_auth_support', 'label' => $this->setLocalization('Nimble'), 'check_module' => false], ['value' => 'wowza_securetoken', 'label' => $this->setLocalization('Wowza SecureToken'), 'check_module' => false], ['value' => 'flexcdn_auth_support', 'label' => $this->setLocalization('Flex-CDN'), 'check_module' => false]];
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/iptv-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function iptv_list()
    {
        $this->app['allChannels'] = $this->db->getAllFromTable('itv');
        $this->app['allGenres'] = $this->getAllGenres();
        $tv_archive_type = $this->db->getEnumValues('itv', 'tv_archive_type');
        $tv_archive_type = \array_filter(\array_combine(\array_values($tv_archive_type), \array_map('ucfirst', \str_replace('_dvr', ' DVR', $tv_archive_type))));
        $allArchive = [['id' => 1, 'title' => $this->setLocalization('Yes')], ['id' => 2, 'title' => $this->setLocalization('No')]];
        foreach ($tv_archive_type as $id => $title) {
            $allArchive[] = ['id' => $id, 'title' => $title];
        }
        $this->app['allArchive'] = $allArchive;
        $this->app['allStatus'] = [['id' => 1, 'title' => $this->setLocalization('Published')], ['id' => 2, 'title' => $this->setLocalization('Unpublished')]];
        $this->app['allMonitoringStatus'] = [['id' => 1, 'title' => $this->setLocalization('monitoring off')], ['id' => 2, 'title' => $this->setLocalization('errors occurred')], ['id' => 3, 'title' => $this->setLocalization('no errors')], ['id' => 4, 'title' => $this->setLocalization('there are some problems')]];
        $allLanguages = $this->getLanguageCodesEN();
        \asort($allLanguages);
        \array_walk($allLanguages, function (&$val, $key) {
            $val = ['id' => $key, 'title' => $val];
        });
        $this->app['allLanguages'] = \array_values($allLanguages);
        $this->getIPTVfilters();
        $attribute = $this->getIptvListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getAllGenres()
    {
        $getAllGenres = $this->db->getAllGenres();
        foreach ($this->setLocalization($getAllGenres, 'title') as $key => $row) {
            $getAllGenres[$key]['title'] = $this->mb_ucfirst($row['title']);
        }
        return $getAllGenres;
    }
    private function getIPTVfilters()
    {
        $filters = [];
        if (\array_key_exists('filters', $this->data)) {
            if (\array_key_exists('tv_genre_id', $this->data['filters']) && $this->data['filters']['tv_genre_id'] != 0) {
                $filters['tv_genre_id'] = $this->data['filters']['tv_genre_id'];
            }
            if (\array_key_exists('tv_archive_type', $this->data['filters']) && (string) $this->data['filters']['tv_archive_type'] !== '0') {
                switch ($this->data['filters']['tv_archive_type']) {
                    case 1:
                        $filters['tv_archive_type<>"" AND '] = 1;
                        break;
                    case 2:
                        $filters['tv_archive_type'] = '';
                        break;
                    default:
                        $filters['tv_archive_type'] = $this->data['filters']['tv_archive_type'];
                        break;
                }
            }
            if (\array_key_exists('status_id', $this->data['filters']) && $this->data['filters']['status_id'] != 0) {
                $filters['status'] = (int) ($this->data['filters']['status_id'] == 1);
            }
            if (\array_key_exists('languages', $this->data['filters']) && !\is_numeric($this->data['filters']['languages'])) {
                $filters['languages'] = '%"' . $this->data['filters']['languages'] . '"%';
            }
            if (\array_key_exists('monitoring_status', $this->data['filters']) && $this->data['filters']['monitoring_status'] != 0) {
                $filters['enable_monitoring'] = (int) (!($this->data['filters']['monitoring_status'] == 1));
            }
            if (\array_key_exists('storage', $this->data['filters'])) {
                $tv_archive = new \Ministra\Lib\TvArchive();
                $tasks = $tv_archive->getAllTasks($this->data['filters']['storage']);
                $tv_ids = [];
                if (!empty($tasks)) {
                    $tv_ids = $this->getFieldFromArray($tasks, 'ch_id');
                }
                if (!empty($tv_ids)) {
                    $filters['`itv`.`id` IN(' . \implode(', ', $tv_ids) . ') AND 1'] = 1;
                } else {
                    $filters['`itv`.`id`'] = '';
                }
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $filters;
    }
    private function getIptvListDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => false], ['name' => 'number', 'title' => '№', 'checked' => true], ['name' => 'logo', 'title' => $this->setLocalization('Logo'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'genres_name', 'title' => $this->setLocalization('Genre'), 'checked' => true], ['name' => 'languages', 'title' => $this->setLocalization('Language'), 'checked' => true], ['name' => 'cmd', 'title' => $this->setLocalization('URL'), 'checked' => true], ['name' => 'tv_archive_type', 'title' => $this->setLocalization('Archive'), 'checked' => true], ['name' => 'xmltv_id', 'title' => $this->setLocalization('XMLTV ID'), 'checked' => false], ['name' => 'claims', 'title' => $this->setLocalization('Claims about audio/video/epg'), 'checked' => false], ['name' => 'monitoring_status', 'title' => $this->setLocalization('Monitoring status'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function move_channel()
    {
        if (!empty($this->data['channel_id'])) {
            $this->app['channel_id'] = $this->data['channel_id'];
            $channel = $this->db->getChannelById($this->data['channel_id']);
            $left_num = \floor(($channel['number'] - 50) / 10) * 10;
            $right_num = \floor(($channel['number'] + 50) / 10) * 10;
            if ($left_num <= 0) {
                $left_num = 1;
                $right_num = 100;
            } else {
                if ($right_num >= 99999) {
                    $left_num = 99899;
                    $right_num = 99999;
                }
            }
            $this->app['channel_begin'] = $left_num;
            $this->app['channel_end'] = $right_num;
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function add_channel()
    {
        $this->app['not_found'] = $this->app['session']->get('channel_error');
        if (!empty($this->app['not_found'])) {
            $this->app['session']->remove('channel_error');
        }
        $this->app['enable_tariff_plans'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false);
        $this->app['allGenres'] = $this->getAllGenres();
        $this->app['streamServers'] = $this->db->getAllStreamServer();
        $this->app['channelEdit'] = false;
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        $form = $this->buildForm();
        if ($this->saveChannelData($form)) {
            return $this->app->redirect('iptv-list');
        }
        $this->app['form'] = $form->createView();
        $this->app['breadcrumbs']->addItem($this->setLocalization('Channels list'), $this->app['controller_alias'] . '/iptv-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Add a channel'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildForm($data = array())
    {
        $builder = $this->app['form.factory'];
        $genres = [];
        foreach ($this->app['allGenres'] as $row) {
            $genres[$row['id']] = $row['title'];
        }
        $storages = $this->getStorages();
        $def_name = empty($data['name']) ? '' : $data['name'];
        $def_number = isset($data['number']) ? $data['number'] : $this->db->getFirstFreeNumber('itv');
        if (\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so')) {
            $this->app['mcrypt_enabled'] = true;
        } else {
            $this->app['mcrypt_enabled'] = false;
            if (\array_key_exists('xtream_codes_support', $data) && !empty($data['xtream_codes_support'])) {
                $data['xtream_codes_support'] = \array_fill(0, \count($data['xtream_codes_support']), 0);
            }
        }
        $allLanguages = $this->getLanguageCodesEN();
        if (\is_array($allLanguages)) {
            \asort($allLanguages);
        } else {
            $allLanguages = [];
        }
        $this->app['allLanguages'] = $allLanguages;
        if (!empty($data['languages']) && \is_string($data['languages']) && ($parsed_json = \json_decode($data['languages'], true)) && \json_last_error() == JSON_ERROR_NONE) {
            $data['languages'] = $parsed_json;
        } elseif (!\array_key_exists('languages', $data) || !\is_array($data['languages'])) {
            $data['languages'] = [];
        }
        $this->app['httpTmpLink'] = $this->getHttpTmpLink();
        $tv_archive_type = $this->db->getEnumValues('itv', 'tv_archive_type');
        $tv_archive_type = \array_combine(\array_values($tv_archive_type), \array_map('ucfirst', \str_replace('_dvr', ' DVR', $tv_archive_type)));
        if (\array_key_exists('enable_tv_archive', $data) && (int) $data['enable_tv_archive'] !== 0 && empty($data['tv_archive_type'])) {
            $data['tv_archive_type'] = 'stalker_dvr';
        }
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('number', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\Range(['min' => 1, 'max' => 99999]), new \Symfony\Component\Validator\Constraints\NotBlank()], 'data' => $def_number])->add('name', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank()], 'data' => $def_name])->add('tv_genre_id', 'choice', ['choices' => $genres, 'choice_translation_domain' => false])->add('languages', 'choice', ['choices' => $this->app['allLanguages'], 'multiple' => true, 'required' => false, 'choice_translation_domain' => false])->add('tv_archive_type', 'choice', ['choices' => $tv_archive_type, 'multiple' => false, 'required' => false])->add('pvr_storage_names', 'choice', ['choices' => empty($storages['storage_names']) || !\is_array($storages['storage_names']) ? [] : $storages['storage_names'], 'multiple' => true])->add('storage_names', 'choice', ['choices' => empty($storages['storage_names']) || !\is_array($storages['storage_names']) ? [] : $storages['storage_names'], 'multiple' => true])->add('wowza_storage_names', 'choice', ['choices' => empty($storages['wowza_storage_names']) || !\is_array($storages['wowza_storage_names']) ? [] : $storages['wowza_storage_names'], 'multiple' => true])->add('flussonic_storage_names', 'choice', ['choices' => empty($storages['flussonic_storage_names']) || !\is_array($storages['flussonic_storage_names']) ? [] : $storages['flussonic_storage_names'], 'multiple' => true])->add('nimble_storage_names', 'choice', ['choices' => empty($storages['nimble_storage_names']) || !\is_array($storages['nimble_storage_names']) ? [] : $storages['nimble_storage_names'], 'multiple' => true])->add('volume_correction', 'choice', ['choices' => \array_combine(\range(-20, 20, 1), \range(-100, 100, 5)), 'constraints' => [new \Symfony\Component\Validator\Constraints\Range(['min' => -20, 'max' => 20]), new \Symfony\Component\Validator\Constraints\NotBlank()], 'required' => true, 'data' => empty($data['volume_correction']) ? '0' : $data['volume_correction']])->add('logo', 'hidden')->add('link_id', 'collection', $this->getDefaultOptions())->add('cmd', 'collection', $this->getDefaultOptions())->add('user_agent_filter', 'collection', $this->getDefaultOptions())->add('priority', 'collection', $this->getDefaultOptions());
        foreach ($this->getHttpTmpLink() as $row) {
            $form->add($row['value'], 'collection', $this->getDefaultOptions($row['value'] == 'use_http_tmp_link' ? 'checkbox' : 'hidden'));
        }
        $form->add('enable_monitoring', 'collection', $this->getDefaultOptions('checkbox'))->add('monitoring_status', 'collection', $this->getDefaultOptions())->add('enable_balancer_monitoring', 'collection', $this->getDefaultOptions())->add('monitoring_url', 'collection', $this->getDefaultOptions())->add('use_load_balancing', 'collection', $this->getDefaultOptions('checkbox'))->add('stream_server', 'collection', $this->getDefaultOptions())->add('mc_cmd', 'text', ['constraints' => [new \Symfony\Component\Validator\Constraints\Regex(['pattern' => '/^(http|udp|rtp)\\:\\/\\//'])], 'required' => false])->add('tv_archive_duration', 'text', ['constraints' => new \Symfony\Component\Validator\Constraints\Range(['min' => 0, 'max' => 999])])->add('allow_pvr', 'checkbox', ['required' => false])->add('xmltv_id', 'text', ['required' => false])->add('correct_time', 'text', ['constraints' => new \Symfony\Component\Validator\Constraints\Range(['min' => -720, 'max' => 840])])->add('censored', 'checkbox', ['required' => false])->add('base_ch', 'checkbox', ['required' => false])->add('allow_local_timeshift', 'checkbox', ['required' => false])->add('allow_local_pvr', 'checkbox', ['required' => false])->add('save', 'submit');
        return $form->getForm();
    }
    private function getStorages($id = false)
    {
        $return = ['storage_names' => [], 'wowza_storage_names' => [], 'flussonic_storage_names' => [], 'nimble_storage_names' => []];
        foreach ($this->db->getStorages() as $key => $value) {
            if ($value['flussonic_dvr'] && !$value['wowza_dvr'] && !$value['nimble_dvr'] || $value['dvr_type'] == 'flussonic_dvr') {
                $return['flussonic_storage_names'][$value['storage_name']] = $value['storage_name'];
            } elseif (!$value['flussonic_dvr'] && $value['wowza_dvr'] && !$value['nimble_dvr'] || $value['dvr_type'] == 'wowza_dvr') {
                $return['wowza_storage_names'][$value['storage_name']] = $value['storage_name'];
            } elseif (!$value['flussonic_dvr'] && !$value['wowza_dvr'] && $value['nimble_dvr'] || $value['dvr_type'] == 'nimble_dvr') {
                $return['nimble_storage_names'][$value['storage_name']] = $value['storage_name'];
            } else {
                $return['storage_names'][$value['storage_name']] = $value['storage_name'];
            }
        }
        if ($id !== false) {
            $tasks = $id == false ? [] : \Ministra\Lib\TvArchive::getTasksByChannelId($id);
            if (!empty($tasks)) {
                $return = \array_map(function ($row) use($tasks) {
                    $names = \array_filter(\array_map(function ($task_row) use($row) {
                        if (\in_array($task_row['storage_name'], $row)) {
                            return $task_row['storage_name'];
                        }
                    }, $tasks));
                    return \is_array($names) && !empty($names) ? \array_combine(\array_values($names), $names) : [];
                }, $return);
            } else {
                $return = ['storage_names' => [], 'wowza_storage_names' => [], 'flussonic_storage_names' => [], 'nimble_storage_names' => []];
            }
        }
        return $return;
    }
    private function getDefaultOptions($type = 'hidden', $constraints = false)
    {
        $options = ['type' => $type, 'options' => ['required' => false], 'required' => false, 'allow_add' => true, 'allow_delete' => true, 'prototype' => false];
        if ($type == 'checkbox') {
            $options['options']['empty_data'] = null;
        }
        if ($constraints !== false) {
            $options['options']['constraints'] = $constraints;
        }
        return $options;
    }
    private function saveChannelData(&$form)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $form->getData();
            if (empty($data['id'])) {
                $is_repeating_name = \count($this->db->getFieldFirstVal('name', $data['name']));
                $is_repeating_number = \count($this->db->getFieldFirstVal('number', $data['number']));
                $operation = 'insertITVChannel';
            } elseif (isset($this->oneChannel)) {
                $is_repeating_name = \count($this->db->getITVByParams(['where' => ['id<>' => $data['id'], 'name' => $data['name']]]));
                $is_repeating_number = \count($this->db->getITVByParams(['where' => ['id<>' => $data['id'], 'number' => $data['number']]]));
                $operation = 'updateITVChannel';
            }
            $this->dataPrepare($data);
            if (!empty($data['allow_pvr']) && empty($data['mc_cmd'])) {
                $error_local = [];
                $error_local['mc_cmd'] = $this->setLocalization('This field cannot be empty if enabled TV-archive or nPVR');
                $this->app['error_local'] = $error_local;
                return false;
            } elseif (\array_key_exists('xtream_codes_support', $data) && \array_sum($data['xtream_codes_support']) && empty($this->app['mcrypt_enabled'])) {
                $form->addError(new \Symfony\Component\Form\FormError($this->setLocalization('For enabling Xtream-Codes Support you need enable mcrypt php-extension')));
                return false;
            }
            if ($form->isValid()) {
                if (empty($data['cmd'])) {
                    $error_local['cmd'] = $this->setLocalization('Requires at least one link of broadcast');
                    $this->app['error_local'] = $error_local;
                    return false;
                }
                $data['languages'] = \json_encode(!empty($data['languages']) && \is_array($data['languages']) ? $data['languages'] : []);
                if (!$is_repeating_name && !$is_repeating_number) {
                    $ch_id = $this->db->{$operation}($data);
                } else {
                    $error_local = [];
                    $error_local['name'] = $is_repeating_name ? $this->setLocalization('This name already exists') : '';
                    $error_local['number'] = $is_repeating_number ? $this->setLocalization('This number is already in use') : '';
                    $this->app['error_local'] = $error_local;
                    return false;
                }
                $links_data = $this->getLinks($data);
                if ($operation == 'updateITVChannel') {
                    $this->deleteChannelTasks($data, $this->oneChannel);
                    $this->deleteDBLinks($links_data);
                }
                if (!empty($data['logo'])) {
                    $ext = \explode('.', $data['logo']);
                    $ext = $ext[\count($ext) - 1];
                    $this->saveFiles->renameFile($this->logoDir, $data['logo'], "{$ch_id}.{$ext}");
                    $error = $this->saveFiles->getError();
                    if (empty($error) || \strpos($error[\count($error) - 1], 'rename') === false) {
                        $this->db->updateLogoName($ch_id, "{$ch_id}.{$ext}");
                    }
                }
                $this->setDBLincs($ch_id, $links_data);
                $this->createTasks($ch_id, $data);
                $this->setAllowedStoragesForChannel($ch_id, $data);
                return true;
            }
        }
        return false;
    }
    private function dataPrepare(&$data)
    {
        while (list($key, $row) = \each($data)) {
            if (\is_array($row)) {
                $this->dataPrepare($data[$key]);
            } elseif ($row === 'on') {
                $data[$key] = 1;
            } elseif ($row === 'off') {
                $data[$key] = 0;
            }
        }
    }
    private function getLinks($data)
    {
        $urls = empty($data['cmd']) ? [] : $data['cmd'];
        $links = [];
        foreach ($urls as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $link = ['url' => $value, 'id' => \array_key_exists($key, $data['link_id']) ? (int) $data['link_id'][$key] : 0, 'priority' => \array_key_exists($key, $data['priority']) ? (int) $data['priority'][$key] : 0, 'user_agent_filter' => \array_key_exists($key, $data['user_agent_filter']) ? $data['user_agent_filter'][$key] : '', 'monitoring_url' => \array_key_exists($key, $data['monitoring_url']) ? $data['monitoring_url'][$key] : '', 'use_load_balancing' => !empty($data['stream_server']) && \array_key_exists($key, $data['stream_server']) && !empty($data['use_load_balancing']) && \array_key_exists($key, $data['use_load_balancing']) ? (int) $data['use_load_balancing'][$key] : 0, 'enable_monitoring' => !empty($data['enable_monitoring']) && \array_key_exists($key, $data['enable_monitoring']) ? (int) $data['enable_monitoring'][$key] : 0, 'enable_balancer_monitoring' => !empty($data['enable_balancer_monitoring']) && \array_key_exists($key, $data['enable_balancer_monitoring']) ? (int) $data['enable_balancer_monitoring'][$key] : 0, 'stream_servers' => !empty($data['stream_server']) && \array_key_exists($key, $data['stream_server']) ? \explode(';', $data['stream_server'][$key]) : []];
            foreach ($this->getHttpTmpLink() as $row) {
                $link[$row['value']] = !empty($data[$row['value']]) && \array_key_exists($key, $data[$row['value']]) ? (int) $data[$row['value']][$key] : 0;
            }
            $links[] = $link;
        }
        return $links;
    }
    private function deleteChannelTasks($new_data, $old_data)
    {
        if ($old_data['tv_archive_type'] != $new_data['tv_archive_type'] && $old_data['tv_archive_type']) {
            $archive_class = '\\Ministra\\Lib\\' . \ucfirst(\trim(\str_replace(['_dvr', 'stalker'], '', $old_data['tv_archive_type']))) . 'TvArchive';
            if (\class_exists($archive_class)) {
                $archive = new $archive_class();
                $archive->deleteTasks($old_data['id']);
            }
        }
    }
    private function deleteDBLinks($data, $id = false)
    {
        $ch_id = $id !== false ? $id : $this->oneChannel['id'];
        $ids = $this->getFieldFromArray($data, 'id');
        $db_links = $this->db->getUnnecessaryLinks($ch_id);
        $db_ids = $this->getFieldFromArray($db_links, 'id');
        $need_to_delete = \array_diff($db_ids, $ids);
        if (!empty($need_to_delete)) {
            $this->db->deleteCHLink($need_to_delete);
            $this->db->deleteCHLinkOnStreamer($need_to_delete);
        }
    }
    private function setDBLincs($ch_id, $data)
    {
        $this->channelLinks = $this->db->getChannelLinksById($ch_id);
        $current_urls = $this->getFieldFromArray($this->channelLinks, 'url');
        foreach ($data as $link) {
            $link['ch_id'] = $ch_id;
            $links_on_server = \is_array($link['stream_servers']) && !empty($link['stream_servers']) ? \array_filter($link['stream_servers']) : [];
            unset($link['stream_servers']);
            if (!$link['enable_monitoring']) {
                $link['status'] = 1;
            }
            if (empty($link['id'])) {
                $link['id'] = $this->db->insertCHLink($link);
                foreach ($links_on_server as $streamer_id) {
                    $this->db->insertCHLinkOnStreamer($link['id'], $streamer_id);
                }
            } elseif (!empty($link['id'])) {
                $this->db->updateCHLink($link['id'], $link);
                $streamers_map = $this->db->getStreamersIdMapForLink($link['id']);
                if (\is_array($streamers_map)) {
                    $on_streamers = \array_keys($streamers_map);
                    $need_to_delete = \array_diff($on_streamers, $links_on_server);
                    $links_on_server = \array_diff($links_on_server, $on_streamers);
                    if ($need_to_delete) {
                        $this->db->deleteCHLinkOnStreamerByLinkAndID($link['id'], $need_to_delete);
                    }
                }
                foreach ($links_on_server as $streamer_id) {
                    if (!empty($link['id']) && !empty($streamer_id)) {
                        $this->db->insertCHLinkOnStreamer($link['id'], $streamer_id);
                    }
                }
            }
        }
    }
    private function createTasks($id, $data)
    {
        if (!empty($data['tv_archive_type'])) {
            $storage_names = [];
            $archive_name = \trim(\str_replace(['_dvr', 'stalker'], '', $data['tv_archive_type']));
            $archive_class = '\\Ministra\\Lib\\' . \ucfirst($archive_name) . 'TvArchive';
            if (\class_exists($archive_class)) {
                $archive = new $archive_class();
                $archive_storage = \trim($archive_name . '_storage_names', '_');
                if (!empty($data[$archive_storage])) {
                    $storage_names = $data[$archive_storage];
                }
                $archive->createTasks($id, $storage_names);
            }
        }
    }
    private function setAllowedStoragesForChannel($id, $data)
    {
        if ($data['allow_pvr']) {
            \Ministra\Lib\RemotePvr::setAllowedStoragesForChannel($id, $data['pvr_storage_names']);
        } else {
            \Ministra\Lib\RemotePvr::setAllowedStoragesForChannel($id);
        }
    }
    public function edit_channel()
    {
        if ($this->method == 'GET' && (empty($this->data['id']) || !\is_numeric($this->data['id']))) {
            return $this->app->redirect('add-channel');
        }
        $id = $this->method == 'POST' && !empty($this->postData['form']['id']) ? $this->postData['form']['id'] : $this->data['id'];
        $this->app['allGenres'] = $this->getAllGenres();
        $this->app['channelEdit'] = true;
        $this->oneChannel = $this->db->getChannelById($id);
        if (empty($this->oneChannel)) {
            $this->app['session']->set('channel_error', true);
            return $this->app->redirect('add-channel');
        }
        $this->app['enable_tariff_plans'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false);
        $this->oneChannel = \array_merge($this->oneChannel, $this->getStorages($id));
        $this->oneChannel['pvr_storage_names'] = \array_keys(\Ministra\Lib\RemotePvr::getStoragesForChannel($id));
        \settype($this->oneChannel['allow_pvr'], 'boolean');
        \settype($this->oneChannel['censored'], 'boolean');
        \settype($this->oneChannel['allow_local_timeshift'], 'boolean');
        \settype($this->oneChannel['allow_local_pvr'], 'boolean');
        \settype($this->oneChannel['base_ch'], 'boolean');
        $this->oneChannel['logo'] = $this->getLogoUriById(false, $this->oneChannel);
        $this->setChannelLinks();
        $this->app['streamServers'] = $this->streamServers;
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        $form = $this->buildForm($this->oneChannel);
        if ($this->saveChannelData($form)) {
            return $this->app->redirect('iptv-list');
        }
        $this->app['form'] = $form->createView();
        $this->app['breadcrumbs']->addItem($this->setLocalization('Channels list'), $this->app['controller_alias'] . '/iptv-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Editing the channel') . ": '{$this->oneChannel['name']}'");
        $this->app['editChannelName'] = $this->oneChannel['name'];
        return $this->app['twig']->render($this->getTemplateName('TvChannels::add_channel'));
    }
    private function getLogoUriById($id = false, $row = false, $resolution = 320)
    {
        $channel = $row === false ? $this->db->getChannelById($id) : $row;
        if (empty($channel['logo'])) {
            return '';
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/logos/' . $resolution . '/' . $channel['logo'];
    }
    private function setChannelLinks()
    {
        $this->channelLinks = $this->db->getChannelLinksById($this->oneChannel['id']);
        if (empty($this->channelLinks)) {
            $this->channelLinks = !empty($this->oneChannel['cmd']) ? [$this->oneChannel['cmd']] : [''];
        }
        $this->streamServers = $this->db->getAllStreamServer();
        $broadcasting_keys = $this->broadcasting_keys;
        $broadcasting_keys[] = ['link_id' => [0]];
        while (list($key, $row) = \each($this->channelLinks)) {
            $this->oneChannel['link_id'][$key + 1] = $row['id'];
            foreach ($this->broadcasting_keys as $b_key => $value) {
                if (!\array_key_exists($b_key, $this->oneChannel) || !\is_array($this->oneChannel[$b_key])) {
                    $this->oneChannel[$b_key] = [];
                }
                if (isset($row[$b_key])) {
                    $this->oneChannel[$b_key][$key + 1] = $row[$b_key];
                } else {
                    $this->oneChannel[$b_key][$key + 1] = $value[0];
                }
                \settype($this->oneChannel[$b_key][$key + 1], \gettype($value[0]));
            }
            if (!empty($row['id'])) {
                $this->setLinkStreamServers($key, $row['id']);
            }
        }
    }
    private function setLinkStreamServers($num, $id)
    {
        $this->streamers_map[$num] = $this->db->getStreamersIdMapForLink($id);
        if (!\is_array($this->oneChannel['stream_server'])) {
            $this->oneChannel['stream_server'] = [];
        }
        $server = [];
        \reset($this->streamServers);
        while (list($key, $row) = \each($this->streamServers)) {
            if (!empty($this->streamers_map[$num][$row['id']])) {
                $server[] = $this->streamers_map[$num][$row['id']]['streamer_id'];
            }
        }
        $this->oneChannel['stream_server'][$num + 1] = \implode(';', $server);
    }
    public function epg()
    {
        $attribute = $this->getEpgDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allLanguages'] = $this->getLanguageCodesEN();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getEpgDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'id_prefix', 'title' => $this->setLocalization('Prefix'), 'checked' => true], ['name' => 'uri', 'title' => $this->setLocalization('URL'), 'checked' => true], ['name' => 'etag', 'title' => $this->setLocalization('XMLTV file hash'), 'checked' => true], ['name' => 'updated', 'title' => $this->setLocalization('Update date'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function tv_genres()
    {
        $attribute = $this->getGenresDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getGenresDropdownAttribute()
    {
        return [['name' => 'number', 'title' => $this->setLocalization('Order'), 'checked' => true], ['name' => 'title', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'localized_title', 'title' => $this->setLocalization('Localized title'), 'checked' => true], ['name' => 'censored', 'title' => $this->setLocalization('Age restriction'), 'checked' => true], ['name' => 'tv_channel_count', 'title' => $this->setLocalization('Channels in genre'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operation'), 'checked' => true]];
    }
    public function m3u_import()
    {
        $this->app['allGenres'] = $this->getAllGenres();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function remove_channel()
    {
        if (!$this->isAjax || empty($this->postData['id']) || !\is_numeric($this->postData['id'])) {
            if (empty($this->postData['group_key']) || !\is_array($this->postData[$this->postData['group_key']])) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        $data = ['id' => [], 'action' => 'deleteTableRow', 'data' => [], 'msg_list' => []];
        $ids = \is_array($this->postData['id']) ? $this->postData['id'] : [$this->postData['id']];
        $tv_archive = new \Ministra\Lib\TvArchive();
        foreach ($ids as $id) {
            $data['msg_list'][$id . '_task'] = $this->groupMessageList($id . '_task', $tv_archive->deleteTasks((int) $id) ? 1 : false, $this->DELETE_TSKLNK_MSG_TMPL());
            $ch_links = $this->db->getChannelLinksById($id);
            if (empty($ch_links)) {
                $data['msg_list'][$id . '_links'] = $this->groupMessageList($id . '_links', 1, $this->DELETE_CHLNK_MSG_TMPL());
            } else {
                $links_result = $this->db->deleteCHLink($this->getFieldFromArray($ch_links, 'id'));
                $data['msg_list'][$id . '_links'] = $this->groupMessageList($id . '_links', (int) $links_result === \count($ch_links) ? $links_result : false, $this->DELETE_CHLNK_MSG_TMPL());
            }
            $channel = $this->db->getChannelById($id);
            $result = 0;
            if (!empty($channel)) {
                $this->postData['logo_id'] = $channel['logo'];
                $logo_result = $this->delete_logo(true);
                $data['msg_list'] = \array_merge($data['msg_list'], $logo_result['msg_list']);
                $result = $this->db->removeChannel($id);
            }
            if ($result !== 0) {
                $data['id'][$id] = $result;
            }
            $data['msg_list'][$id . '_channel'] = $this->groupMessageList($id . '_channel', $result, $this->DELETE_MSG_TMPL());
        }
        $result = \count($data['id']);
        $data['id'] = \array_filter($data['id']);
        $error = false;
        if (empty($data['id'])) {
            $error = $result !== \count($data['id']);
            if (!$error) {
                $data['msg'] = $error = $this->setLocalization('Nothing to delete');
            } else {
                $data['msg'] = $this->setLocalization('Some errors found');
            }
        } else {
            $data['id'] = \array_keys($data['id']);
            $msg_str = 'id: ' . \implode(', ', $data['id']);
            $data['msg'] = $this->setLocalization('Channels {updchid} has been deleted', '', $msg_str, ['{updchid}' => $msg_str]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function DELETE_TSKLNK_MSG_TMPL()
    {
        return ['success' => ['status' => true, 'msg' => $this->setLocalization('All tasks for channel id:{updid} has been deleted')], 'failed' => ['status' => false, 'msg' => $this->setLocalization('Deleting tasks for channel id:{updid} ended with an error')]];
    }
    private function DELETE_CHLNK_MSG_TMPL()
    {
        return ['success' => ['status' => true, 'msg' => $this->setLocalization('All links for channel id:{updid} has been deleted')], 'failed' => ['status' => false, 'msg' => $this->setLocalization('Deleting links for channel id:{updid} ended with an error')], 'error' => ['status' => true, 'msg' => $this->setLocalization('Not all links for channel id:{updid} has been deleted')]];
    }
    public function delete_logo($internal_use = false)
    {
        if (!$this->isAjax || empty($this->postData['logo_id']) || !\is_numeric($this->postData['logo_id']) && \strpos($this->postData['logo_id'], 'new') === false) {
            if (!$internal_use) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        if (!$internal_use) {
            $channel = $this->db->getChannelById($this->postData['logo_id']);
        }
        $logo_id = false;
        $error = [];
        $data = $this->postData;
        $data['data'] = 0;
        $data['action'] = 'deleteLogo';
        if (!empty($channel) && \array_key_exists('id', $channel)) {
            $this->db->updateITVChannelLogo($channel['id'], '');
            $logo_id = $channel['logo'];
        } else {
            $logo_id = $this->postData['logo_id'];
        }
        $folders = \array_keys($this->logoResolutions);
        $folders[] = 'original';
        foreach ($folders as $folder) {
            $full_path = "{$this->logoDir}/{$folder}";
            if (\is_dir($full_path)) {
                foreach (\glob($full_path) as $logo_id_link) {
                    $logo_name = $logo_id . '_X_' . $folder . '_logo';
                    if (\is_file("{$logo_id_link}/{$logo_id}")) {
                        if (!@\unlink("{$logo_id_link}/{$logo_id}")) {
                            $error[$logo_name]['status'] = false;
                            $error[$logo_name]['msg'] = $this->setLocalization('Error delete file {lgd}', '', $logo_name, ['{lgd}' => $logo_name]);
                        } else {
                            $error[$logo_name]['status'] = true;
                            $error[$logo_name]['msg'] = $this->setLocalization('File logo {lgd} has been deleted', '', $logo_name, ['{lgd}' => $logo_name]);
                        }
                    }
                }
            }
        }
        $data['msg_list'] = $error;
        $data['data'] = empty(\array_filter($this->getFieldFromArray('status', $error)));
        if (empty($data['data'])) {
            $error = \implode('. ', \array_map(function ($row) {
                if (!$row['status']) {
                    return $row['msg'];
                }
            }, $error));
        } else {
            $error = false;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return !$internal_use ? new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']) : $response;
    }
    private function DELETE_MSG_TMPL()
    {
        return ['success' => ['status' => true, 'msg' => $this->setLocalization('Channel id:{updid} has been deleted')], 'failed' => ['status' => false, 'msg' => $this->setLocalization('Channel id:{updid} not deleted')], 'error' => ['status' => false, 'msg' => $this->setLocalization('Deleting channel id:{updid} ended with an error')]];
    }
    public function disable_channel()
    {
        if (!$this->isAjax || empty($this->postData['id']) || !\is_numeric($this->postData['id'])) {
            if (empty($this->postData['group_key']) || !\is_array($this->postData[$this->postData['group_key']])) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        $ids = \is_array($this->postData['id']) ? $this->postData['id'] : [$this->postData['id']];
        $data = ['id' => [], 'action' => 'updateTableRow', 'data' => [], 'msg_list' => []];
        foreach ($ids as $id) {
            $result = $this->db->changeChannelStatus($id, 0);
            if ($result !== 0) {
                $data['id'][$id] = $result;
            }
            $data['msg_list'][$id] = $this->groupMessageList($id, $result, $this->UPDATE_MSG_TMPL());
        }
        $result = \count($data['id']);
        $data['id'] = \array_filter($data['id']);
        $error = false;
        if (empty($data['id'])) {
            $error = $result !== \count($data['id']);
            if (!$error) {
                $data['msg'] = $error = $this->setLocalization('Nothing to do');
            } else {
                $data['msg'] = $this->setLocalization('Some errors found');
            }
        } else {
            if (\array_key_exists('group_key', $this->postData)) {
                $this->postData[$this->postData['group_key']] = \array_keys($data['id']);
            }
            $data = \array_merge_recursive($data, $this->iptv_list_json(true));
            $data['id'] = \array_keys($data['id']);
            $msg_str = 'id: ' . \implode(', ', $data['id']);
            $data['msg'] = $this->setLocalization('Channels {updchid} has been updated', '', $msg_str, ['{updchid}' => $msg_str]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function UPDATE_MSG_TMPL()
    {
        return ['success' => ['status' => true, 'msg' => $this->setLocalization('Channel id:{updid} updated')], 'failed' => ['status' => false, 'msg' => $this->setLocalization('Channel id:{updid} not updated')], 'error' => ['status' => false, 'msg' => $this->setLocalization('Update for channel id:{updid} ended with an error')]];
    }
    public function iptv_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_', 'claims', 'group_key']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filter = $this->getIPTVfilters();
        if (\array_key_exists('languages', $filter)) {
            $query_param['like']['languages'] = $filter['languages'];
            unset($filter['languages']);
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        if (!empty($param['id'])) {
            if (\is_array($param['id'])) {
                $query_param['in']['id'] = $param['id'];
            } else {
                $query_param['where']['id'] = $param['id'];
            }
        }
        $filds_for_select = $this->getAllChannelsFields();
        $query_param['select'] = \array_values($filds_for_select);
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        foreach ($query_param['order'] as $key => $val) {
            if ($search = \array_search($key, $filds_for_select)) {
                $new_key = \str_replace(" as {$search}", '', $key);
                unset($query_param['order'][$key]);
                $query_param['order'][$new_key] = $val;
            }
        }
        $limit_off = !empty($filter['enable_monitoring']);
        if (!empty($query_param['order']) && \array_key_exists('itv.monitoring_status', $query_param['order'])) {
            $order = $query_param['order']['itv.monitoring_status'];
            $query_param['order'] = ['itv.enable_monitoring' => $order, 'itv.monitoring_status' => $order];
        }
        if (!isset($query_param['like'])) {
            $query_param['like'] = [];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsAllChannels();
        $response['recordsFiltered'] = $this->db->getTotalRowsAllChannels($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit']) && $limit_off) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $allChannels = $this->db->getAllChannels($query_param);
        $allChannels = $this->setLocalization($allChannels, 'genres_name');
        $response['data'] = [];
        if (\is_array($allChannels)) {
            \reset($allChannels);
            while (list($num, $row) = \each($allChannels)) {
                $allChannels[$num]['logo'] = $this->getLogoUriById(false, $row, 120);
                $allChannels[$num]['genres_name'] = $this->mb_ucfirst($allChannels[$num]['genres_name']);
                $allChannels[$num]['status'] = (int) $allChannels[$num]['status'];
                $allChannels[$num]['languages'] = $this->getLanguageStrFromJSON($allChannels[$num]['languages']);
                if (!empty($allChannels[$num]['tv_archive_type'])) {
                    $allChannels[$num]['tv_archive_type'] = \ucfirst(\str_replace('_dvr', ' DVR', $allChannels[$num]['tv_archive_type']));
                }
                \settype($allChannels[$num]['sound_counter'], 'int');
                \settype($allChannels[$num]['video_counter'], 'int');
                \settype($allChannels[$num]['no_epg'], 'int');
                \settype($allChannels[$num]['wrong_epg'], 'int');
                $allChannels[$num]['RowOrder'] = 'dTRow_' . $row['id'];
                if (($monitoring_status = $this->getMonitoringStatus($row)) !== false) {
                    $allChannels[$num]['monitoring_status'] = $monitoring_status;
                    $response['data'][] = $allChannels[$num];
                } else {
                    unset($allChannels[$num]);
                }
            }
        }
        if ($limit_off) {
            $count = \count($response['data']);
            $response['recordsFiltered'] = $count;
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getAllChannelsFields()
    {
        return ['id' => 'itv.id as `id`', 'locked' => 'itv.locked as `locked`', 'number' => 'itv.number as `number`', 'logo' => 'itv.logo as `logo`', 'name' => 'itv.name as `name`', 'languages' => 'itv.languages as `languages`', 'genres_name' => 'tv_genre.title as `genres_name`', 'tv_archive_type' => 'itv.tv_archive_type as `tv_archive_type`', 'cmd' => 'itv.cmd as `cmd`', 'monitoring_status' => 'itv.monitoring_status as `monitoring_status`', 'status' => 'itv.status as `status`', 'media_type' => 'media_claims.media_type', 'media_id' => ' media_claims.media_id', 'sound_counter' => 'media_claims.sound_counter', 'video_counter' => 'media_claims.video_counter', 'no_epg' => 'media_claims.no_epg', 'wrong_epg' => 'media_claims.wrong_epg', 'enable_monitoring' => 'itv.enable_monitoring', 'monitoring_status_updated' => 'itv.monitoring_status_updated', 'xmltv_id' => 'itv.xmltv_id'];
    }
    private function getLanguageStrFromJSON($json = '')
    {
        $lang_str = '';
        if (!empty($json) && (\is_array($json) || \is_string($json) && ($json = \json_decode($json, true)) && \json_last_error() == JSON_ERROR_NONE)) {
            $lang_str = \implode(', ', \array_intersect_key($this->getLanguageCodesEN(), \array_flip($json)));
        } elseif (\json_last_error() != JSON_ERROR_NONE) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47(\json_last_error_msg());
        }
        return $lang_str;
    }
    private function getMonitoringStatus($row)
    {
        $return = '';
        if (!(int) $row['enable_monitoring']) {
            $return .= '<span class="no-wrap monitoring-status no-monitoring">';
            $return .= $this->setLocalization('monitoring off');
            $return .= '</span>';
        } else {
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $row['monitoring_status_updated']);
            $datetime = $datetime instanceof \DateTime && (int) $datetime->getTimestamp() > 0 ? $datetime->getTimestamp() : 0;
            $diff = \time() - $datetime;
            $return .= $this->setLocalization('Last check') . ': ';
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
            $return .= '<br><span class="no-wrap monitoring-status ';
            $disabled_link = $this->db->getChanelDisabledLink($row['id']);
            $status = $this->getFieldFromArray($disabled_link, 'status');
            $total_status = \array_sum($status);
            if ($total_status != 0) {
                if ($total_status != \count($status)) {
                    if (!empty($this->data['filters']) && \array_key_exists('monitoring_status', $this->data['filters']) && (int) $this->data['filters']['monitoring_status'] != 0 && (int) $this->data['filters']['monitoring_status'] != 4) {
                        return false;
                    }
                    $return .= 'gold">' . $this->setLocalization('there are some problems');
                } else {
                    if (!empty($this->data['filters']) && \array_key_exists('monitoring_status', $this->data['filters']) && (int) $this->data['filters']['monitoring_status'] != 0 && (int) $this->data['filters']['monitoring_status'] != 3) {
                        return false;
                    }
                    $return .= 'green">' . $this->setLocalization('no errors');
                }
            } else {
                if (!empty($this->data['filters']) && \array_key_exists('monitoring_status', $this->data['filters']) && (int) $this->data['filters']['monitoring_status'] != 0 && (int) $this->data['filters']['monitoring_status'] != 2) {
                    return false;
                }
                $return .= 'red">' . $this->setLocalization('errors occurred');
            }
            $return .= '</span>';
        }
        return $return;
    }
    public function enable_channel()
    {
        if (!$this->isAjax || empty($this->postData['id']) || !\is_numeric($this->postData['id'])) {
            if (empty($this->postData['group_key']) || !\is_array($this->postData[$this->postData['group_key']])) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        $ids = \is_array($this->postData['id']) ? $this->postData['id'] : [$this->postData['id']];
        $data = ['id' => [], 'action' => 'updateTableRow', 'data' => []];
        foreach ($ids as $id) {
            $result = $this->db->changeChannelStatus($id, 1);
            if ($result !== 0) {
                $data['id'][$id] = $result;
            }
            $data['msg_list'][$id] = $this->groupMessageList($id, $result, $this->UPDATE_MSG_TMPL());
        }
        $result = \count($data['id']);
        $data['id'] = \array_filter($data['id']);
        $error = false;
        if (empty($data['id'])) {
            $error = $result !== \count($data['id']);
            if (!$error) {
                $data['msg'] = $error = $this->setLocalization('Nothing to do');
            } else {
                $data['msg'] = $this->setLocalization('Some errors found');
            }
        } else {
            if (\array_key_exists('group_key', $this->postData)) {
                $this->postData[$this->postData['group_key']] = \array_keys($data['id']);
            }
            $data = \array_merge_recursive($data, $this->iptv_list_json(true));
            $data['id'] = \array_keys($data['id']);
            $msg_str = 'id: ' . \implode(', ', $data['id']);
            $data['msg'] = $this->setLocalization('Channels {updchid} has been updated', '', $msg_str, ['{updchid}' => $msg_str]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function edit_logo()
    {
        $upload_id = !empty($this->data['id']) ? $this->data['id'] : (!empty($this->postData['id']) ? $this->postData['id'] : '');
        if (empty($upload_id) || !\is_numeric($upload_id) && \strpos($upload_id, 'new') === false) {
            $this->app->abort(404, $this->setLocalization('Cannot find channel'));
        } elseif ($upload_id == 'new') {
            $upload_id .= \rand(0, 100000);
        }
        $response = [];
        if (!empty($this->postData['container_id'])) {
            $response['container_id'] = $this->postData['container_id'];
        }
        $error = $this->setLocalization('Error');
        if (!empty($_FILES)) {
            list($fKey, $tmp) = \each($_FILES);
            if (\is_uploaded_file($tmp['tmp_name']) && \preg_match('/jpeg|jpg|png/', $tmp['type'])) {
                $img_path = $this->logoDir;
                \umask(0);
                try {
                    $uploaded = $this->request->files->get($fKey)->getPathname();
                    $ext = \explode('.', $tmp['name']);
                    $ext = \end($ext);
                    if (!\is_dir($img_path . '/original')) {
                        if (!\mkdir($img_path . '/original', 0777, true)) {
                            throw new \Exception(\sprintf(\_('Cannot create directory %s'), $img_path . '/original'));
                        }
                    }
                    $this->app['imagine']->open($uploaded)->save($img_path . "/original/{$upload_id}.{$ext}");
                    foreach ($this->logoResolutions as $res => $param) {
                        if (!\is_dir($img_path . "/{$res}")) {
                            if (!\mkdir($img_path . "/{$res}", 0777, true)) {
                                throw new \Exception(\sprintf(\_('Cannot create directory %s'), $img_path . "/{$res}"));
                            }
                        }
                        $this->app['imagine']->open($uploaded)->resize(new \Imagine\Image\Box($param['width'], $param['height']))->save($img_path . "/{$res}/{$upload_id}.{$ext}");
                        \chmod($img_path . "/{$res}/{$upload_id}.{$ext}", 0644);
                    }
                    $error = '';
                    $logoHost = $this->baseHost . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/') . 'misc/logos';
                    $response['upload_id'] = $upload_id;
                    $response['logo'] = "{$upload_id}.{$ext}";
                    $response['pic'] = $logoHost . "/320/{$upload_id}.{$ext}";
                    $response = $this->generateAjaxResponse($response, $error);
                } catch (\ImagickException $e) {
                    $error = \sprintf(\_('Error save file %s'), $tmp['name']);
                    $response['msg'] = $e->getMessage() . '. ' . $error;
                }
            }
        }
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_channel_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'channelDataPrepare'];
        $error = $this->setLocalization('Error');
        $query_param = [];
        $query_param['select'] = ['itv.id', 'itv.name', 'itv.number', 'itv.logo', 'itv.locked'];
        $begin_val = !empty($this->postData['channel_begin']) ? (int) $this->postData['channel_begin'] : 1;
        $end_val = !empty($this->postData['channel_end']) ? (int) $this->postData['channel_end'] + 1 : $begin_val + 199;
        $query_param['where'] = ['itv.number >=' => $begin_val];
        if (\array_key_exists('oneitem', $this->postData)) {
            $response['oneitem'] = $this->postData['oneitem'];
            $response['action'] = 'appendToEnd';
            $query_param['where']['itv.locked'] = 0;
            if (\array_key_exists('ex_item_ids', $this->postData)) {
                if (\is_string($this->postData['ex_item_ids']) && ($parsed_json = \json_decode($this->postData['ex_item_ids'])) && \json_last_error() == JSON_ERROR_NONE && !empty($parsed_json)) {
                    $query_param['not_in']['itv.id'] = $parsed_json;
                } elseif (\is_array($this->postData['ex_item_ids'])) {
                    $query_param['not_in']['itv.id'] = $this->postData['ex_item_ids'];
                }
            }
        } else {
            $query_param['where']['itv.number < '] = $end_val;
        }
        $query_param['limit'] = ['offset' => 0, 'limit' => $end_val - $begin_val];
        $query_param['order'] = 'number';
        $response['recordsTotal'] = $this->db->getTotalRowsAllChannels();
        $response['recordsFiltered'] = $this->db->getTotalRowsAllChannels($query_param['where']);
        $allChannels = $this->db->getAllChannels($query_param);
        $this->db->setSQLDebug(0);
        if (\array_key_exists('oneitem', $this->postData) && !empty($allChannels)) {
            $begin_val = $end_val = $allChannels[0]['number'];
        }
        if (\is_array($allChannels)) {
            while (list($num, $row) = \each($allChannels)) {
                $allChannels[$num]['logo'] = $this->getLogoUriById(false, $row, 120);
                $allChannels[$num]['locked'] = (bool) $allChannels[$num]['locked'];
                \settype($allChannels[$num]['number'], 'int');
            }
            if (!empty($allChannels[0]) && (int) $allChannels[0]['number'] == 0) {
                unset($allChannels[0]);
            }
        } else {
            $allChannels = [];
        }
        $response['data'] = $this->fillEmptyRows($allChannels, $begin_val, $end_val);
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function fillEmptyRows($input_array = array(), $begin = 1, $end = 201)
    {
        $result = [];
        $empty_row = ['logo' => '', 'name' => '', 'id' => '', 'number' => 0, 'empty' => true, 'locked' => false];
        \reset($input_array);
        $begin_val = $begin;
        $count = \count($input_array);
        if ($count < $end) {
            $input_array[$count] = $empty_row;
            $input_array[$count]['number'] = $end;
        }
        while (list($key, $row) = \each($input_array)) {
            while ($begin_val < $row['number']) {
                $empty_row['number'] = $begin_val;
                $result[] = $empty_row;
                ++$begin_val;
            }
            if (!\array_key_exists('empty', $row)) {
                $row['empty'] = false;
                $result[] = $row;
            }
            ++$begin_val;
        }
        \reset($result);
        return $result;
    }
    public function move_channel_move_number($local_uses = false)
    {
        if (!$this->isAjax && !$local_uses || $this->isAjax && empty($this->postData['start_num'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'action' => '', 'nothing_to_do' => true];
        $error = $this->setLocalization('Out of range. The shift is not possible.');
        $start_num = $this->postData['start_num'];
        $direction = $this->isAjax && !empty($this->postData['direction']) ? $this->postData['direction'] : 1;
        if ($direction == 1) {
            $free_num = $this->db->getFirstFreeNumber('itv', 'number', $start_num, $direction);
        } else {
            $free_num = $this->db->getLastChannelNumber() + 1;
        }
        if ($free_num > 0 && $free_num < 99999) {
            $error = '';
            $params = ['select' => ['`itv`.`id` as `id`', '`itv`.`number` as `number`', '`itv`.`locked` as `locked`'], 'where' => ['`itv`.`number`>=' => \min($start_num, $free_num), '`itv`.`number`<=' => \max($start_num, $free_num)], 'order' => ['number' => 'ASC']];
            $channels = $this->db->getAllChannels($params);
            $params['set'] = ['`number` = `number` + ' . $direction . ', `modified`' => 'NOW()'];
            $response['result'] = $this->db->updateChannelGroup($params);
            $response['result_back'] = 0;
            if (\is_numeric($response['result']) && $response['result'] != 0) {
                $set_locked = ['`number` = `number` - ' . $direction . ', `modified`' => 'NOW()'];
                $set_before_locked = ['`number` = `number` + ' . $direction . ', `modified`' => 'NOW()'];
                $locked = \array_filter(\array_map(function ($row) {
                    return $row['locked'] ? $row : false;
                }, $channels));
                $locked_num = \array_combine($this->getFieldFromArray($locked, 'id'), $this->getFieldFromArray($locked, 'number'));
                $moved = \array_filter(\array_map(function ($row) {
                    return !$row['locked'] ? $row : false;
                }, $channels));
                $moved_num = \array_combine($this->getFieldFromArray($moved, 'id'), $this->getFieldFromArray($moved, 'number'));
                $moved_num = \array_map(function ($num) use($direction) {
                    $num += $direction;
                    return $num;
                }, $moved_num);
                $response['result_back'] = $this->db->updateChannelGroup(['set' => $set_locked, 'in' => ['id' => \array_keys($locked_num)]]);
                $duplicate = \array_intersect($moved_num, $locked_num);
                while (!empty($duplicate)) {
                    $this->db->updateChannelGroup(['set' => $set_before_locked, 'in' => ['id' => \array_keys($duplicate)]]);
                    \reset($moved_num);
                    while (list($id, $num) = \each($moved_num)) {
                        if (\array_key_exists($id, $duplicate)) {
                            $moved_num[$id] += $direction;
                        }
                    }
                    $duplicate = \array_intersect($moved_num, $locked_num);
                }
            }
        }
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function move_apply()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $senddata = ['action' => 'manageChannel'];
        if (empty($this->postData['data'])) {
            $senddata['error'] = $this->setLocalization('No moved items, nothing to do');
        } else {
            $senddata['error'] = '';
            foreach ($this->postData['data'] as $row) {
                if (empty($row['id'])) {
                    continue;
                }
                $result = $this->db->updateChannelNum($row);
                if (!\is_numeric($result)) {
                    $senddata['error'] = $this->setLocalization('Failed to save, update the channel list');
                }
            }
        }
        $response = $this->generateAjaxResponse($senddata, $senddata['error']);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($senddata['error']) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function toogle_lock_channel()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['action' => 'manageChannel', 'nothing_to_do' => true];
        if (empty($this->postData['data'])) {
            $error = $this->setLocalization('Nothing to do');
        } else {
            $error = '';
            foreach ($this->postData['data'] as $row) {
                if (empty($row['id'])) {
                    continue;
                }
                $row['locked'] = empty($row['locked']) || $row['locked'] == 'false' || $row['locked'] == '0' ? 0 : 1;
                if (!$this->db->updateChannelLockedStatus($row)) {
                    $error = $this->setLocalization('Failed to save, update the channel list');
                }
            }
        }
        $response = $this->generateAjaxResponse($response, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_channel_epg_item()
    {
        if (!$this->isAjax || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $ch_id = !empty($this->data['id']) ? $this->data['id'] : $this->postData['id'];
        $date = !empty($this->postData['epg_date']) ? $this->postData['epg_date'] : \strftime('%d-%m-%Y');
        $epg = !empty($this->postData['epg_body']) ? $this->postData['epg_body'] : '';
        $sendData = ['action' => 'saveEPGSuccess'];
        $parser = new \Ministra\Admin\Service\EpgParser\EpgParserService($epg, $date);
        $from = $parser->getToday();
        $last = $parser->getLast();
        $to = $parser->getTill();
        $sendData['deleted'] = $this->db->deleteEPGForChannel($ch_id, $from->format(self::DATETIME_MYSQL), $to->format(self::DATETIME_MYSQL));
        $newEpg = $parser->getEpg();
        if (\count($newEpg)) {
            $next = $this->db->findFirstAfterTime($ch_id, $to->getTimestamp());
            $end = $next ? \DateTime::createFromFormat(self::DATETIME_MYSQL, $next['time']) : null;
            $last->calcEnd($end);
            $items = [];
            foreach ($newEpg as $item) {
                $items[] = ['ch_id' => $ch_id, 'name' => $item->getProgram(), 'time' => $item->getDate()->format(self::DATETIME_MYSQL), 'time_to' => $item->getEnd()->format(self::DATETIME_MYSQL), 'duration' => $item->getDuration(), 'real_id' => $ch_id . '_' . $item->getDate()->getTimestamp()];
            }
            $sendData['inserted'] = $this->db->insertEPGForChannel($items);
        }
        $response = $this->generateAjaxResponse($sendData, '');
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_channel_epg_item()
    {
        if (!$this->isAjax || empty($this->data['id']) && empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $ch_id = !empty($this->data['id']) ? $this->data['id'] : $this->postData['id'];
        $date = !empty($this->postData['epg_date']) ? $this->postData['epg_date'] : \strftime('%d-%m-%Y');
        $senddata = ['action' => 'showModalBox', 'ch_id' => $ch_id, 'epg_date' => $date, 'epg_body' => ''];
        $erorr = '';
        $date = \implode('-', \array_reverse(\explode('-', $date)));
        $epg_data = $this->db->getEPGForChannel($ch_id, $date . ' 00:00:00', $date . ' 23:59:59');
        if (!empty($epg_data)) {
            $tmp = [''];
            \reset($epg_data);
            while (list($key, $row) = \each($epg_data)) {
                \preg_match("/(\\d+):(\\d+)/", $row['time'], $tmp);
                $senddata['epg_body'] .= $tmp[0] . ' ' . $row['name'] . "\n";
            }
        }
        $response = $this->generateAjaxResponse($senddata, $erorr);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_epg_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = $check_param = [];
        $data['action'] = 'manageEPG';
        \array_walk($this->postData, function (&$val) {
            $val = \trim($val);
        });
        $item = [$this->postData];
        if (empty($this->postData['id'])) {
            $operation = 'insertEPG';
        } else {
            $operation = 'updateEPG';
            $data['id'] = $item['id'] = $this->postData['id'];
            $data['action'] = 'updateTableRow';
        }
        $cond = false;
        if (!empty($this->postData['id_prefix'])) {
            $check_param['id_prefix'] = $this->postData['id_prefix'];
            $cond = ' OR ';
        } else {
            if (!empty($this->postData['id'])) {
                $check_param['id<>'] = $this->postData['id'];
                $cond = ' AND ';
            }
        }
        $check_param['uri'] = $this->postData['uri'];
        $check = $this->db->searchOneEPGParam($check_param, $cond);
        \array_walk($check, function (&$val) {
            $val = \trim($val);
        });
        unset($item[0]['id']);
        $error = ' ';
        if (empty($check)) {
            $result = \call_user_func_array([$this->db, $operation], $item);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                } else {
                    $data = \array_merge_recursive($data, $this->epg_list_json(true));
                }
            }
        } elseif ($item[0]['id_prefix'] == $check['id_prefix']) {
            $error .= $this->setLocalization('Prefix is busy');
        } elseif ($item[0]['uri'] == $check['uri']) {
            $error .= $this->setLocalization('URL is busy');
        } else {
            $error .= $this->setLocalization('Failed');
        }
        $data['msg'] = $error;
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function epg_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        if (!$local_uses) {
            $response['action'] = 'setEPGModal';
        }
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = '*';
        }
        if (!isset($query_param['like'])) {
            $query_param['like'] = [];
        } elseif (\array_key_exists('updated', $query_param['like'])) {
            $query_param['like']['CAST(`updated` as CHAR)'] = $query_param['like']['updated'];
            unset($query_param['like']['updated']);
        }
        $response['recordsTotal'] = $this->db->getTotalRowsEPGList();
        $response['recordsFiltered'] = $this->db->getTotalRowsEPGList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (\array_key_exists('id', $param)) {
            $query_param['where']['id'] = $param['id'];
        }
        $EPGList = $this->db->getEPGList($query_param);
        $response['data'] = \array_map(function ($val) {
            $val['status'] = (int) $val['status'];
            $val['updated'] = (int) \strtotime($val['updated']);
            $val['RowOrder'] = 'dTRow_' . $val['id'];
            return $val;
        }, $EPGList);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function remove_epg_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        if ($this->db->deleteEPG(['id' => $this->postData['id']])) {
            $error = '';
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function toggle_epg_item_status()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || !\array_key_exists('status', $this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['id'];
        $this->db->updateEPG(['status' => (int) (!(bool) $this->postData['status'])], $this->postData['id']);
        $error = '';
        $data = \array_merge_recursive($data, $this->epg_list_json(true));
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function epg_check_uri()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['param'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_uri';
        $error = $this->setLocalization('URL is busy');
        if ($this->db->searchOneEPGParam(['uri' => \trim($this->postData['param']), 'id<>' => \trim($this->postData['epgid'])])) {
            $data['chk_rezult'] = $this->setLocalization('URL is busy');
        } else {
            $data['chk_rezult'] = $this->setLocalization('URL is free');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function update_epg()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'manageEPG';
        $data['id'] = $this->postData['id'];
        $error = '';
        $epg = new \Ministra\Lib\Epg();
        $data['msg'] = \nl2br($epg->updateEpg(!empty($this->postData['force'])));
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function restart_all_archives()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'restartArchive';
        $data['error'] = $error = '';
        $tv_archive = new \Ministra\Lib\TvArchive();
        $result = true;
        $current_tasks = $this->db->getCurrentTasks();
        $new_tasks = [];
        foreach ($current_tasks as $task) {
            $new_tasks[$task['ch_id']][] = $task['storage_name'];
        }
        foreach (\array_keys($new_tasks) as $channel) {
            if ($this->db->checkChannelParams($channel)) {
                $tv_archive->deleteTasks($channel);
                $result = $tv_archive->createTasks($channel, $new_tasks[$channel]) && $result;
            } else {
                $result = false;
                if (empty($data['error'])) {
                    $data['error'] = $this->setLocalization('Some channels not enough params.');
                }
            }
        }
        if (!$result) {
            $data['error'] .= ' ' . $this->setLocalization('TV Archive has NOT been restarted correctly.');
        } else {
            $data['msg'] = $this->setLocalization('TV Archive has been restarted.');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function add_tv_genres()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['title'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Failed');
        $this->postData['title'] = \trim($this->postData['title']);
        $check = $this->db->getTvGenresList(['where' => ['title' => $this->postData['title']], 'order' => ['title' => 'ASC']]);
        if (empty($check)) {
            if ($this->db->insertTvGenres(['title' => $this->postData['title'], 'censored' => !empty($this->postData['censored'])])) {
                $error = '';
                $data['msg'] = $this->setLocalization('inserted');
            } else {
                $data['msg'] = $error = $this->setLocalization('Nothing to do');
            }
        } else {
            $data['msg'] = $error = $this->setLocalization('Name already used');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function edit_tv_genres()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['title']) || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $error = $this->setLocalization('Failed');
        $check = $this->db->getTvGenresList(['select' => ['*'], 'where' => [' BINARY title' => $this->postData['title'], 'id<>' => $this->postData['id']], 'order' => ['title' => 'ASC'], 'like' => []]);
        if (empty($check)) {
            $result = $this->db->updateTvGenres(['title' => $this->postData['title'], 'censored' => !empty($this->postData['censored'])], ['id' => $this->postData['id']]);
            if ($result) {
                $error = '';
                $data['id'] = $this->postData['id'];
                $data['title'] = $this->postData['title'];
                $data = \array_merge_recursive($data, $this->tv_genres_list_json(true));
                $data['msg'] = $this->setLocalization('updated');
            } elseif (\is_numeric($result)) {
                $error = '';
                $data['msg'] = $this->setLocalization('Nothing to do');
                $data['nothing_to_do'] = true;
            }
        } else {
            $data['msg'] = $error = $this->setLocalization('Name already used');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function tv_genres_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_', 'localized_title', 'tv_channel_count', 'RowOrder']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (!empty($param['id'])) {
            $query_param['where']['id'] = $param['id'];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = ['*'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsTvGenresList();
        $response['recordsFiltered'] = $this->db->getTotalRowsTvGenresList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (!\in_array('id', $query_param['select'])) {
            $query_param['select'][] = 'id';
        }
        $query_param['select'][] = '(select count(*) from itv where itv.tv_genre_id = tv_genre.id) as tv_channel_count';
        $self = $this;
        $query_param['order']['number'] = 'ASC';
        $response['data'] = \array_map(function ($row) use($self) {
            $row['localized_title'] = $self->setLocalization($row['title']);
            $row['censored'] = (int) $row['censored'];
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getTvGenresList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function remove_tv_genres()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['genresid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['genresid'];
        $error = $this->setLocalization('Failed');
        $count_itv = $this->db->getTotalRowsAllChannels(['tv_genre_id' => $this->postData['genresid']]);
        if (empty($count_itv)) {
            if ($this->db->deleteTvGenres(['id' => $this->postData['genresid']])) {
                $data['msg'] = $this->setLocalization('Deleted');
            } else {
                $data['msg'] = $error = $this->setLocalization('Nothing to do');
            }
            $error = '';
        } else {
            $error = $data['msg'] = $this->setLocalization('Found channels of this genre. Deleting not possible');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_tv_genres_name()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['title'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'tv_genres_title';
        $error = $this->setLocalization('Name already used');
        $this->postData['title'] = \trim($this->postData['title']);
        if ($this->db->getTvGenresList(['select' => ['*'], 'where' => [' BINARY title' => $this->postData['title'], 'id<>' => $this->postData['id']], 'order' => ['title' => 'ASC'], 'like' => []])) {
            $data['chk_rezult'] = $this->setLocalization('Name already used');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Name is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function tv_genres_reorder()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $matches = [];
        $data = [];
        $data['action'] = 'reorder';
        $error = $this->setLocalization('Error');
        if (\preg_match("/(\\d+)/i", $this->postData['id'], $matches)) {
            $params = ['select' => ['id' => 'tv_genre.id as `id`', 'number' => 'tv_genre.number as `number`'], 'where' => [], 'like' => [], 'order' => ['number' => 'DESC']];
            $curr_pos = $this->postData['fromPosition'];
            $new_pos = $this->postData['toPosition'];
            $params['where']['tv_genre.number'] = $curr_pos;
            $curr_genre = $this->db->getTvGenresList($params);
            $params['where'] = [];
            $params['where']['tv_genre.number<='] = $new_pos;
            $target_genre = $this->db->getTvGenresList($params);
            if ($this->db->updateTvGenres($target_genre[0], ['id' => $curr_genre[0]['id']]) && $this->db->updateTvGenres($curr_genre[0], ['id' => $target_genre[0]['id']])) {
                $error = '';
                $data['msg'] = $this->setLocalization('Done');
            } else {
                $data['msg'] = $error;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_m3u_data()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'loadM3UData';
        $data['data'] = ['channels' => [], 'last_channel_number' => 0, 'free_number_exists' => 1];
        $error = $this->setLocalization('Upload failed');
        $storage = new \Upload\Storage\FileSystem('/tmp', true);
        $file = new \Upload\File('files', $storage);
        try {
            $file->upload();
            $m3u_data = $this->parseM3UFile($file->getPath() . '/' . $file->getNameWithExtension());
            @\unlink($file->getPath() . '/' . $file->getNameWithExtension());
            $data['data']['last_channel_number'] = (int) $this->db->getLastChannelNumber();
            if ($data['data']['last_channel_number'] + \count($m3u_data) > 99999) {
                $data['data']['free_number_exists'] = (int) ($this->db->getAllChannels([], 'COUNT') + \count($m3u_data) <= 99999);
            }
            $data['data']['channels'] = \array_values(\array_map(function ($row) {
                return ['name' => !empty($row['name']) ? !\mb_check_encoding($row['name'], 'UTF-8') ? \mb_convert_encoding($row['name'], 'UTF-8', ['CP1251']) : $row['name'] : '', 'genre' => !empty($row['title']) ? !\mb_check_encoding($row['title'], 'UTF-8') ? \mb_convert_encoding($row['title'], 'UTF-8', ['CP1251']) : $row['title'] : '', 'cmd' => !empty($row['cmd']) ? $row['cmd'] : '', 'logo' => !empty($row['logo']) ? $row['logo'] : '', 'xmltv_id' => !empty($row['id']) ? $row['id'] : ''];
            }, $m3u_data));
            $error = '';
        } catch (\Exception $e) {
            $data['msg'] = $error = $file->getErrors();
        }
        $response = $this->generateAjaxResponse($data, $error);
        $json_string = \json_encode($response);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            $error = $this->setLocalization('Error m3u parse. Check the file encoding. Required UTF-8 encoding.');
            $json_string = \json_encode(['msg' => $error, 'error' => $error, 'action' => 'loadM3UData']);
        } elseif (empty($data['data']['channels'])) {
            $error = $this->setLocalization('Error m3u parse. File is empty or non in m3u-format');
            $json_string = \json_encode(['msg' => $error, 'error' => $error, 'action' => 'loadM3UData']);
        }
        return new \Symfony\Component\HttpFoundation\Response($json_string, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function parseM3UFile($file)
    {
        $str = @\file_get_contents($file);
        $data = [];
        if ($str !== false && \strpos($str, '#EXTM3U') !== false) {
            if (\substr($str, 0, 3) === "﻿") {
                $str = \substr($str, 3);
            }
            $lines = \explode("\n", $str);
            while (list($num, $line) = \each($lines)) {
                $line = \trim($line);
                if ($line === '' || \strtoupper(\substr($line, 0, 7)) === '#EXTM3U') {
                    continue;
                }
                if (\strtoupper(\substr($line, 0, 8)) === '#EXTINF:') {
                    $tmp = \substr($line, 8);
                    $tmp = \explode(',', $tmp, 2);
                    $data[$num]['name'] = \end($tmp);
                    $data[$num] = \array_merge($this->parseInfoRow($tmp[0]), $data[$num]);
                    while (list(, $line) = \each($lines)) {
                        $line = \trim($line);
                        if ($line !== '') {
                            $data[$num]['cmd'] = \trim($line);
                            break;
                        }
                    }
                } else {
                    if (\substr($line, 0, 1) === '#') {
                        $tmp = \trim(\substr($line, 1));
                        if ($tmp !== '') {
                            $data[$num]['name'] = $tmp;
                            $data[$num] = \array_merge($this->parseInfoRow($tmp), $data[$num]);
                        }
                        while (list(, $line) = \each($lines)) {
                            $line = \trim($line);
                            if ($line !== '') {
                                $data[$num]['cmd'] = \trim($line);
                                break;
                            }
                        }
                    } else {
                        while (list(, $line) = \each($lines)) {
                            $line = \trim($line);
                            if ($line !== '') {
                                $data[$num]['cmd'] = \trim($line);
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
    private function parseInfoRow($row)
    {
        $result = [];
        $row = \preg_replace('/^(#[^\\s]+)\\s?/', '', $row);
        \preg_match_all('/([^=]+)=(\\"[^\\"]+\\")\\s?/', $row, $tmp);
        if (\count($tmp) == 3 && \count($tmp[1]) == \count($tmp[2])) {
            $result = \array_combine(\array_map(function ($row) {
                $tmp = \explode('-', $row);
                return \strtolower(\end($tmp));
            }, $tmp[1]), \array_map(function ($row) {
                return \trim($row, "\"'");
            }, $tmp[2]));
        }
        return $result;
    }
    public function save_m3u_item()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'saveM3UItem';
        $error = $this->setLocalization('Error');
        $item_data = $this->postData;
        $data['item_id'] = $item_data['item_id'];
        $is_repeating_name = \count($this->db->getFieldFirstVal('name', $item_data['name']));
        $is_repeating_number = \count($this->db->getFieldFirstVal('number', $item_data['number']));
        if (!$is_repeating_name && !$is_repeating_number) {
            $this->dataPrepare($item_data);
            if (empty($item_data['cmd'])) {
                $data['msg'] = $error = $this->setLocalization('Requires at least one link of broadcast');
            }
            if (empty($item_data['name'])) {
                $data['msg'] = $error = $this->setLocalization('Field "Channel name" cannot be empty');
            }
            if (empty($item_data['number'])) {
                $data['msg'] = $error = $this->setLocalization('Field "Channel number" cannot be empty');
            } else {
                $item_data['cmd'] = [0 => $item_data['cmd']];
                $item_data['priority'] = [0 => 0];
                $item_data['user_agent_filter'] = [0 => ''];
                $item_data['monitoring_url'] = [0 => ''];
                $ch_id = $this->db->insertITVChannel($item_data);
                if (\is_numeric($ch_id) && $ch_id != 0) {
                    $this->setDBLincs($ch_id, $this->getLinks($item_data));
                    $this->createTasks($ch_id, $item_data);
                    if (!empty($item_data['logo'])) {
                        $ext = \explode('.', $item_data['logo']);
                        $ext = \strtolower(\end($ext));
                        $save_image_flag = false;
                        if (\preg_match('/^(http|https)?:\\/\\//', $item_data['logo'])) {
                            $img_path = $this->logoDir;
                            if (!\preg_match('/(jpg|jpeg|png)/', $ext)) {
                                $ext = 'png';
                            }
                            try {
                                if (!\is_dir($img_path . '/original')) {
                                    if (!\mkdir($img_path . '/original', 0777, true)) {
                                        throw new \Exception(\sprintf(\_('Cannot create directory %s'), $img_path . '/original'));
                                    }
                                }
                                $handle = \fopen($item_data['logo'], 'r');
                                $image = $this->app['imagine']->read($handle);
                                $image->save($img_path . "/original/{$ch_id}.{$ext}");
                                foreach ($this->logoResolutions as $res => $param) {
                                    if (!\is_dir($img_path . "/{$res}")) {
                                        if (!\mkdir($img_path . "/{$res}", 0777, true)) {
                                            throw new \Exception(\sprintf(\_('Cannot create directory %s'), $img_path . "/{$res}"));
                                        }
                                    }
                                    $image->resize(new \Imagine\Image\Box($param['width'], $param['height']))->save($img_path . "/{$res}/{$ch_id}.{$ext}");
                                    \chmod($img_path . "/{$res}/{$ch_id}.{$ext}", 0644);
                                }
                                $save_image_flag = true;
                            } catch (\Exception $e) {
                                $data['msg'] = $e->getMessage();
                            }
                        } else {
                            $this->saveFiles->renameFile($this->logoDir, $item_data['logo'], "{$ch_id}.{$ext}");
                            $error = $this->saveFiles->getError();
                            $save_image_flag = empty($error) || \strpos($error[\count($error) - 1], 'rename') === false;
                        }
                        if ($save_image_flag) {
                            $this->db->updateLogoName($ch_id, "{$ch_id}.{$ext}");
                        }
                    }
                }
                $error = '';
            }
        } else {
            if ($is_repeating_number) {
                $data['msg'] = $error = $this->setLocalization('Number "%number%" is already in use', '', '', ['%number%' => $item_data['number']]);
            } elseif ($is_repeating_name) {
                $data['msg'] = $error = $this->setLocalization('Name "%name%" already exists', '', '', ['%name%' => $item_data['name']]);
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function epg_check_prefix()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_id_prefix';
        $error = $this->setLocalization('Prefix already used');
        $params = ['id_prefix' => $this->postData['prefix']];
        if (!empty($this->postData['epg_id'])) {
            $params['id<>'] = $this->postData['epg_id'];
        }
        $result = $this->db->searchOneEPGParam($params);
        if (!empty($this->postData['prefix']) && !empty($result)) {
            $data['chk_rezult'] = $this->setLocalization('Prefix already used');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Prefix is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function itv_reset_claims()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['id' => $this->postData['media_id'], 'action' => 'updateTableRow', 'data' => []];
        $error = $this->setLocalization('Failed');
        $result = $this->db->resetMediaClaims($this->postData['media_id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            } else {
                $this->postData['id'] = $this->postData['media_id'];
                $data = \array_merge_recursive($data, $this->iptv_list_json(true));
            }
        } else {
            $data['msg'] = $error;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function change_channel_genre()
    {
        if (!$this->isAjax || empty($this->postData['id']) || !\is_numeric($this->postData['id'])) {
            if (empty($this->postData['group_key']) || !\is_array($this->postData[$this->postData['group_key']])) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        $data = ['id' => [], 'action' => 'updateTableRow', 'data' => [], 'msg_list' => []];
        if (!empty($this->postData['tv_genre_id'])) {
            if (!empty($this->db->getTvGenresList(['where' => ['id' => $this->postData['tv_genre_id']]]))) {
                $ids = \is_array($this->postData['id']) ? $this->postData['id'] : [$this->postData['id']];
                foreach ($ids as $id) {
                    $result = $this->db->changeITVGenre($id, $this->postData['tv_genre_id']);
                    if ($result !== 0) {
                        $data['id'][$id] = $result;
                    }
                    $data['msg_list'][$id] = $this->groupMessageList($id, $result, $this->UPDATE_MSG_TMPL());
                }
                $result = \count($data['id']);
                $data['id'] = \array_filter($data['id']);
                $error = false;
                if (empty($data['id'])) {
                    $error = $result !== \count($data['id']);
                    if (!$error) {
                        $data['msg'] = $error = $this->setLocalization('Nothing to do');
                    } else {
                        $data['msg'] = $this->setLocalization('Some errors found');
                    }
                } else {
                    if (\array_key_exists('group_key', $this->postData)) {
                        $this->postData[$this->postData['group_key']] = \array_keys($data['id']);
                    }
                    $data = \array_merge_recursive($data, $this->iptv_list_json(true));
                    $data['id'] = \array_keys($data['id']);
                    $msg_str = 'id: ' . \implode(', ', $data['id']);
                    $data['msg'] = $this->setLocalization('Channels {updchid} has been updated', '', $msg_str, ['{updchid}' => $msg_str]);
                }
            } else {
                $data['msg'] = $error = $this->setLocalization('Undefined tv-genre');
            }
        } else {
            $data['msg'] = $error = $this->setLocalization('TV-genres for channel cannot be empty');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function cmd_autodetect_lang()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $error = $this->setLocalization('No languages detected');
        $data = ['action' => 'autodetectLangResult', 'languages' => [], 'data' => []];
        if (!empty($this->postData['link'])) {
            $url = '';
            if (!empty($this->postData['ch_id'])) {
                $link = $this->postData['link'];
                $links_map = \array_values(\array_filter(\array_map(function ($row) use($link) {
                    return \in_array($link, $row) ? $row : false;
                }, $this->db->getChannelLinksById($this->postData['ch_id']))));
                if (!empty($links_map)) {
                    try {
                        $tmp = \Ministra\Lib\Itv::getInstance()->getRealChannelByChannelId($this->postData['ch_id'], $links_map[0]['id']);
                        if (!empty($tmp['cmd'])) {
                            $url = $tmp['cmd'];
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            if (empty($url)) {
                $url = $this->postData['link'];
            }
            if (!empty($url)) {
                if (\preg_match("/(\\S+)?(\\s+)?(\\b\\S+:\\/\\/\\S+)/", $url, $match)) {
                    $url = $match[3];
                }
                try {
                    $video = \FFMpeg\FFProbe::create();
                    $lang_iso = $this->db->getAllFromTable('languages');
                    $lang_iso = \array_combine($this->getFieldFromArray($lang_iso, 'iso_639_3_code'), \array_values($lang_iso));
                    $records = $video->streams($url);
                    if (!empty($records)) {
                        foreach ($records as $rec) {
                            $tags = $rec->get('tags');
                            if (!empty($tags['language']) && \is_string($tags['language']) && \strlen($tags['language']) == 3 && \array_key_exists($tags['language'], $lang_iso)) {
                                $data['languages'][] = $lang_iso[$tags['language']]['iso_639_code'];
                            }
                        }
                        $data['languages'] = \array_filter(\array_unique($data['languages']));
                    }
                } catch (\Exception $e) {
                    if (\class_exists('\\FFMpeg\\FFProbe') && !empty($video)) {
                        $error = $this->setLocalization('Failed') . '. ' . $e->getMessage();
                    } else {
                        $error = $this->setLocalization('Failed') . '. ' . $this->setLocalization('Unable to load FFProbe library. Please install "ffmpeg" or other package with this library(eg "libav-tools")');
                    }
                }
            }
        }
        if (!empty($data['languages'])) {
            $error = '';
        } else {
            $data['msg'] = $error;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function change_channel_language()
    {
        if (!$this->isAjax || empty($this->postData['id']) || !\is_numeric($this->postData['id'])) {
            if (empty($this->postData['group_key']) || !\is_array($this->postData[$this->postData['group_key']])) {
                $this->app->abort(404, $this->setLocalization('Cannot find channel'));
            }
        }
        $data = ['id' => [], 'action' => 'updateTableRow', 'data' => [], 'msg_list' => []];
        $languages = !empty($this->postData['languages']) ? \is_array($this->postData['languages']) ? \array_filter($this->postData['languages']) : [$this->postData['languages']] : [];
        $languages = \array_intersect($languages, \array_flip($this->getLanguageCodesEN()));
        $ids = \is_array($this->postData['id']) ? $this->postData['id'] : [$this->postData['id']];
        $languages = \json_encode($languages);
        foreach ($ids as $id) {
            $result = $this->db->changeITVLanguages($id, $languages);
            if ($result !== 0) {
                $data['id'][$id] = $result;
            }
            $data['msg_list'][$id] = $this->groupMessageList($id, $result, $this->UPDATE_MSG_TMPL());
        }
        $result = \count($data['id']);
        $data['id'] = \array_filter($data['id']);
        $error = false;
        if (empty($data['id'])) {
            $error = $result !== \count($data['id']);
            if (!$error) {
                $data['msg'] = $error = $this->setLocalization('Nothing to do');
            } else {
                $data['msg'] = $this->setLocalization('Some errors found');
            }
        } else {
            if (\array_key_exists('group_key', $this->postData)) {
                $this->postData[$this->postData['group_key']] = \array_keys($data['id']);
            }
            $data = \array_merge_recursive($data, $this->iptv_list_json(true));
            $data['id'] = \array_keys($data['id']);
            $msg_str = 'id: ' . \implode(', ', $data['id']);
            $data['msg'] = $this->setLocalization('Channels {updchid} has been updated', '', $msg_str, ['{updchid}' => $msg_str]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_channel_name()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $id = isset($this->postData['id']) ? $this->postData['id'] : null;
        $channels = $this->db->totalChannelsByField('name', $this->postData['name'], $id);
        if ($channels > 0) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => false, 'message' => $this->setLocalization('Name already used')]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => true, 'message' => $this->setLocalization('Name is available')]);
    }
    public function check_channel_number()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $id = isset($this->postData['id']) ? $this->postData['id'] : null;
        $channels = $this->db->totalChannelsByField('number', $this->postData['number'], $id);
        if ($channels > 0) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => false, 'message' => $this->setLocalization('Number already used')]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => true, 'message' => $this->setLocalization('Number is available')]);
    }
}
