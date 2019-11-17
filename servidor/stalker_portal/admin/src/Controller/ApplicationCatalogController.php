<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\AppsManager;
use Ministra\Lib\GitHub;
use Ministra\Lib\GitHubError;
use Ministra\Lib\Npm;
use Ministra\Lib\SmartLauncherAppsManager;
use Ministra\Lib\SmartLauncherAppsManagerConflictException;
use Ministra\Lib\SmartLauncherAppsManagerException;
use Ministra\Lib\Theme;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class ApplicationCatalogController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $apps_list;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/application-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function application_list()
    {
        $tos = $this->db->getTOS('stalker_apps');
        if (empty($tos)) {
            return $this->app['twig']->render($this->getTemplateName('ApplicationCatalog::index'));
        } elseif (empty($tos[0]['accepted'])) {
            $this->app['tos'] = $tos[0];
            $this->app['tos_alias'] = 'stalker_apps';
            return $this->app['twig']->render($this->getTemplateName('ApplicationCatalog::tos'));
        }
        $attribute = $this->getApplicationListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getApplicationListDropdownAttribute()
    {
        $attribute = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Application'), 'checked' => true], ['name' => 'url', 'title' => $this->setLocalization('URL'), 'checked' => true], ['name' => 'current_version', 'title' => $this->setLocalization('Current version'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function smart_application_list()
    {
        $tos = $this->db->getTOS('launcher_apps');
        if (empty($tos)) {
            return $this->app['twig']->render($this->getTemplateName('ApplicationCatalog::index'));
        } elseif (empty($tos[0]['accepted'])) {
            $this->app['tos'] = $tos[0];
            $this->app['tos_alias'] = 'launcher_apps';
            return $this->app['twig']->render($this->getTemplateName('ApplicationCatalog::tos'));
        }
        $attribute = $this->getSmartApplicationListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allType'] = [['id' => 1, 'title' => $this->setLocalization('Application')], ['id' => 2, 'title' => $this->setLocalization('System')]];
        $this->app['allCategory'] = [['id' => 'media', 'title' => $this->setLocalization('Media')], ['id' => 'apps', 'title' => $this->setLocalization('Application')], ['id' => 'games', 'title' => $this->setLocalization('Games')], ['id' => 'notification', 'title' => $this->setLocalization('Notification')]];
        $this->app['allInstalled'] = [['id' => 1, 'title' => $this->setLocalization('Not installed')], ['id' => 2, 'title' => $this->setLocalization('Installed')]];
        $this->app['allStatus'] = [['id' => 1, 'title' => $this->setLocalization('Off')], ['id' => 2, 'title' => $this->setLocalization('On')]];
        $this->app['allCompatibility'] = [['id' => 1, 'title' => $this->setLocalization('Incompatible')], ['id' => 2, 'title' => $this->setLocalization('Compatible')]];
        $this->getSmartApplicationFilters();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getSmartApplicationListDropdownAttribute()
    {
        $attribute = [['name' => 'icon', 'title' => $this->setLocalization('Logo'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Application'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Type'), 'checked' => true], ['name' => 'category', 'title' => $this->setLocalization('Category'), 'checked' => true], ['name' => 'current_version', 'title' => $this->setLocalization('Current version'), 'checked' => true], ['name' => 'available_version', 'title' => $this->setLocalization('Actual version'), 'checked' => true], ['name' => 'author', 'title' => $this->setLocalization('Author'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'description', 'title' => $this->setLocalization('Description'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    private function getSmartApplicationFilters()
    {
        $return = [];
        if (empty($this->data['filters'])) {
            $this->data['filters'] = [];
        }
        if (!\array_key_exists('type', $this->data['filters'])) {
            $this->data['filters']['type'] = '1';
        }
        if ((string) $this->data['filters']['type'] != '0') {
            $return['`L_A`.`type`' . ($this->data['filters']['type'] == 1 ? '=' : '<>')] = 'app';
        }
        if (\array_key_exists('category', $this->data['filters']) && (string) $this->data['filters']['category'] != '0') {
            $return['`L_A`.`category`'] = $this->data['filters']['category'];
        }
        if (\array_key_exists('installed', $this->data['filters']) && (string) $this->data['filters']['installed'] != '0') {
            $return['installed'] = $this->data['filters']['installed'] - 1;
        }
        if (\array_key_exists('status', $this->data['filters']) && (string) $this->data['filters']['status'] != '0') {
            $return['`L_A`.`status`'] = $this->data['filters']['status'] - 1;
        }
        if (\array_key_exists('conflicts', $this->data['filters']) && (string) $this->data['filters']['conflicts'] != '0') {
        }
        $this->app['filters'] = $this->data['filters'];
        return $return;
    }
    public function accept_tos()
    {
        if ($this->app['userlogin'] === 'admin' && !empty($this->postData['accepted']) && !empty($this->postData['tos_alias'])) {
            $this->db->setAcceptedTOS($this->postData['tos_alias']);
        }
        if (!empty($this->postData['tos_alias'])) {
            $redirect_path = '/application-catalog' . ($this->postData['tos_alias'] == 'launcher_apps' ? '/smart-application-list' : '/application-list');
        } else {
            $redirect_path = '/';
        }
        return $this->app->redirect($this->workURL . $redirect_path);
    }
    public function application_detail()
    {
        if (empty($this->data['id'])) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/application-list');
        }
        $attribute = $this->getApplicationDetailDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['app_info'] = $this->application_version_list_json(true);
        $this->app['breadcrumbs']->addItem($this->setLocalization('Ministra applications'), 'application-catalog/application-list');
        $this->app['breadcrumbs']->addItem(!empty($this->app['app_info']['info']['name']) ? $this->app['app_info']['info']['name'] : $this->setLocalization('Undefined'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getApplicationDetailDropdownAttribute()
    {
        $attribute = [['name' => 'version', 'title' => $this->setLocalization('Application version'), 'checked' => true], ['name' => 'published', 'title' => $this->setLocalization('Release date'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function application_version_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'manageList', 'info' => []];
        $id = false;
        $version = !empty($this->postData['version']) ? $this->postData['version'] : false;
        if (!empty($this->data['id'])) {
            $id = $this->data['id'];
        }
        if (!empty($this->postData['id'])) {
            $id = $this->postData['id'];
            $response['action'] = 'createOptionForm';
        }
        try {
            $apps_list = new \Ministra\Lib\AppsManager();
            $app = $apps_list->getAppInfo($id);
        } catch (\Exception $e) {
            $response['msg'] = $error = $this->setLocalization('Failed to get the list of versions of this applications') . '. ' . $e->getMessage();
            $app = false;
        }
        if ($app !== false) {
            $response['data'] = \array_values(\array_filter(\array_map(function ($row) use($version) {
                if ($version === false || $version == $row['version']) {
                    $row['published'] = (int) \strtotime($row['published']);
                    $row['published'] = $row['published'] < 0 ? 0 : $row['published'];
                    $row['RowOrder'] = 'dTRow_' . \str_replace('.', '_', $row['version']);
                    return $row;
                }
            }, $app['versions'])));
            $response['recordsTotal'] = \count($response['data']);
            $response['recordsFiltered'] = \count($response['data']);
            unset($app['versions']);
            $response['info'] = $app;
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function smart_application_detail()
    {
        if (empty($this->data['id'])) {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/smart-application-list');
        }
        $attribute = $this->getSmartApplicationDetailDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['app_info'] = $this->smart_application_version_list_json(true);
        $this->app['breadcrumbs']->addItem($this->setLocalization('Applications of Smart Launcher'), 'application-catalog/smart-application-list');
        $this->app['breadcrumbs']->addItem(!empty($this->app['app_info']['info']['name']) ? $this->app['app_info']['info']['name'] : $this->setLocalization('Undefined'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getSmartApplicationDetailDropdownAttribute()
    {
        $attribute = [['name' => 'version', 'title' => $this->setLocalization('Available versions'), 'checked' => true], ['name' => 'published', 'title' => $this->setLocalization('Date'), 'checked' => true], ['name' => 'conflicts', 'title' => $this->setLocalization('Compatibility'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function smart_application_version_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'manageList', 'info' => []];
        $id = false;
        $version = !empty($this->postData['version']) ? $this->postData['version'] : false;
        if (!empty($this->data['id'])) {
            $id = $this->data['id'];
        }
        if (!empty($this->postData['id'])) {
            $id = $this->postData['id'];
            $response['action'] = 'createOptionForm';
        }
        $apps_list = null;
        try {
            $apps_list = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            $app = $apps_list->getAppInfo($id);
            $app['versions'] = $apps_list->getAppVersions($id);
            $app['conflicts'] = $apps_list->getConflicts($id, $version);
        } catch (\Exception $e) {
            $response['msg'] = $error = $this->setLocalization('Failed to get the list of versions of this applications') . '. ' . $e->getMessage();
            $app = false;
        }
        if ($app !== false) {
            $id = $app['id'];
            $response['data'] = \array_values(\array_filter(\array_map(function ($row) use($version, $apps_list, $id) {
                if ($version === false || $version == $row['version']) {
                    $row['published'] = $row['published'] < 0 ? 0 : $row['published'];
                    try {
                        $row['conflicts'] = $apps_list->getConflicts($id, $row['version']);
                    } catch (\Exception $e) {
                        $row['error'] = $e->getMessage();
                    }
                    return $row;
                }
            }, $app['versions'])));
            $response['recordsTotal'] = \count($response['data']);
            $response['recordsFiltered'] = \count($response['data']);
            unset($app['versions']);
            $response['info'] = $app;
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function tos()
    {
        if (!empty($this->postData['tos_alias'])) {
            $redirect_path = '/application-catalog' . ($this->postData['tos_alias'] == 'launcher_apps' ? '/smart-application-list' : '/application-list');
        } else {
            $redirect_path = '/';
        }
        return $this->app->redirect($this->workURL . $redirect_path);
    }
    public function smart_application_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => ''];
        $filds_for_select = $this->getSmartApplicationFields();
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $filter = $this->getSmartApplicationFilters();
        $installed = null;
        if (\array_key_exists('installed', $filter)) {
            $installed = (bool) $filter['installed'];
            unset($filter['installed']);
        }
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $get_conflicts = false;
        if (!empty($param['id'])) {
            $query_param['where'] = ['L_A.`id`' => $param['id']];
            $response['action'] = 'buildModalByAlias';
            $get_conflicts = true;
            if (\array_key_exists('curr_row', $param) && !\array_key_exists('alias', $param)) {
                $response['curr_row'] = $param['curr_row'];
                $response['action'] = 'oneRowRender';
            }
        }
        if (!empty($query_param['like'])) {
            if (\array_key_exists('description', $query_param['like'])) {
                $query_param['like']['localization'] = $query_param['like']['description'];
            } elseif (\array_key_exists('name', $query_param['like'])) {
                $query_param['like']['localization'] = $query_param['like']['name'];
            }
        }
        if (!\in_array('L_A.`id` as `id`', $query_param['select'])) {
            $query_param['select'][] = 'L_A.`id` as `id`';
        }
        if (!\in_array('L_A.`alias` as `alias`', $query_param['select'])) {
            $query_param['select'][] = 'L_A.`alias` as `alias`';
        }
        $query_param['in'] = [];
        if ($installed !== null) {
            $apps_manager = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            $apps_types = $this->db->getEnumValues('launcher_apps', 'type');
            $apps_types[] = '';
            $installed_app = [];
            foreach ($apps_types as $a_type) {
                $installed_app = \array_merge($installed_app, $apps_manager->getInstalledApps($a_type));
            }
            $query_param['in'][] = 'id';
            $query_param['in'][] = $this->getFieldFromArray($installed_app, 'id');
            $query_param['in'][] = !$installed;
        }
        $response['recordsTotal'] = $this->db->getTotalRowsSmartApplicationList();
        $response['recordsFiltered'] = $this->db->getTotalRowsSmartApplicationList($query_param['where'], $query_param['like'], $query_param['in']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $base_obj = $this->db->getSmartApplicationList($query_param, false);
        if ($get_conflicts || $installed !== null) {
            if (!isset($apps_manager)) {
                $apps_manager = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            }
            while (list(, $row) = \each($base_obj)) {
                try {
                    $info = $apps_manager->getAppInfo($row['id']);
                    if ($installed !== null && $installed !== $info['installed']) {
                        continue;
                    }
                    $row['name'] = $info['name'];
                    $row['description'] = $info['description'];
                    $row['available_version'] = $info['available_version'];
                    $row['conflicts'] = $row['available_version_conflicts'] = [];
                    if ($get_conflicts) {
                        if (!empty($row['current_version']) && !isset($param['curr_row'])) {
                            $row['conflicts'] = $apps_manager->getConflicts($row['id'], $row['current_version']);
                        }
                        if (!empty($row['available_version']) && (empty($row['current_version']) || $row['current_version'] != $row['available_version']) && !isset($param['curr_row'])) {
                            $row['available_version_conflicts'] = $apps_manager->getConflicts($row['id'], $row['available_version']);
                        }
                    }
                    $row['icon'] = !empty($info['icon']) ? $info['icon'] : $this->getIconByType($row['type']);
                    $row['backgroundColor'] = $info['backgroundColor'];
                    \settype($row['status'], 'int');
                    $row['installed'] = $info['installed'];
                    $row['rerendered'] = true;
                    if (\count($response['data']) <= 50) {
                        $response['data'][] = $row;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } else {
            $response['data'] = $base_obj;
        }
        if (!empty($response['data'])) {
            $response['data'] = \array_map(function ($row) {
                $row['RowOrder'] = 'dTRow_' . $row['id'];
                return $row;
            }, $response['data']);
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getSmartApplicationFields()
    {
        $attribute = ['id' => 'L_A.`id` as `id`', 'icon' => '"" as `icon`', 'name' => 'L_A.`name` as `name`', 'type' => 'L_A.`type` as `type`', 'category' => 'L_A.`category` as `category`', 'current_version' => 'L_A.`current_version` as `current_version`', 'available_version' => '"" as `available_version`', 'alias' => 'L_A.`alias` as `alias`', 'conflicts' => '"" as `conflicts`', 'author' => 'L_A.`author` as `author`', 'status' => 'L_A.`status` as `status`', 'localization' => 'L_A.`localization` as `localization`', 'description' => '"" as `description`'];
        return $attribute;
    }
    private function getIconByType($type)
    {
        switch ($type) {
            case 'core':
                return 'img/Core_icon2.png';
                break;
            case 'launcher':
                return 'img/Launcher_icon2.png';
                break;
            case 'osd':
                return 'img/OSD_icon2.png';
                break;
            case 'plugin':
                return 'img/Plugin_icon2.png';
                break;
            case 'system':
                return 'img/System_icon2.png';
                break;
            case 'theme':
                return 'img/Theme_icon2.png';
                break;
            default:
                return 'img/no_image.png';
                break;
        }
    }
    public function application_get_data_from_repo()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps']['url'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'buildSaveForm';
        $response['data'] = [];
        $response['msg'] = '';
        try {
            $repo = new \Ministra\Lib\GitHub($this->postData['apps']['url']);
            $response['data'] = $repo->getFileContent('package.json');
            if (!\array_key_exists('repository', $response['data'])) {
                $response['data']['repository']['url'] = $this->postData['apps']['url'];
            }
        } catch (\Ministra\Lib\GitHubError $e) {
            $response['msg'] = $this->setLocalization($e->getMessage());
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_get_data_from_repo()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps']['url']) && empty($this->postData['alias'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = !empty($this->postData['alias']) ? 'buildModalByAlias' : 'buildSaveForm';
        $response['data'] = [];
        $error = '';
        try {
            $search_str = !empty($this->postData['apps']['url']) ? $this->postData['apps']['url'] : $this->postData['alias'];
            if (\strpos($search_str, '://') === false) {
                $repo = new \Ministra\Lib\Npm();
                $response['data'] = $repo->info($search_str, !empty($this->postData['version']) ? $this->postData['version'] : null);
                $response['data']['name'] = isset($response['data']['config']['type']) && $response['data']['config']['type'] == 'app' && isset($response['data']['config']['name']) ? $response['data']['config']['name'] : $response['data']['name'];
                if (!empty($response['data'])) {
                    $response['data']['repository']['url'] = $search_str;
                } else {
                    $response['msg'] = $error = $this->setLocalization('No data about this apps');
                }
            } else {
                $response['msg'] = $error = $this->setLocalization('Invalid package name');
            }
        } catch (\Exception $e) {
            $response['msg'] = $this->setLocalization($e->getMessage());
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_add()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'updateTableData';
        $postData = $this->postData['apps'];
        if (!empty($postData['url'])) {
            $app = $this->db->getApplication(['url' => $postData['url']]);
            if (empty($app) && $this->db->insertApplication($postData)) {
                $error = '';
                $response['msg'] = $this->setLocalization('Installed');
            } else {
                $response['msg'] = $error = $this->setLocalization('Perhaps the application is already installed. You can update it if the new version is available or uninstall and install again');
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('URL of application is not defined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_add()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'manageList';
        $postData = $this->postData['apps'];
        if (!empty($postData['url'])) {
            $app = $this->db->getSmartApplication(['url' => $postData['url']]);
            if (empty($app) && $this->db->insertSmartApplication($postData)) {
                $response['msg'] = $error = '';
            } else {
                $response['msg'] = $error = $this->setLocalization('Perhaps the application is already installed. You can update it if the new version is available or uninstall and install again');
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Package name is not defined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_version_save_option()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'changeStatus';
        $postData = $this->postData['apps'];
        if (!empty($postData['id'])) {
            $app_id = $postData['id'];
            unset($postData['id']);
            $option = \json_encode($postData);
            $result = $this->db->updateApplication(['options' => $option], $app_id);
            if (\is_numeric($result)) {
                $response['msg'] = $error = '';
                if ($result === 0) {
                    $response['nothing_to_do'] = true;
                }
            } else {
                $response['msg'] = $error = $this->setLocalization('Failed to update the parameters of application launch');
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Application is undefined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_version_save_option()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['apps'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'manageList';
        $postData = $this->postData['apps'];
        if (!empty($postData['id'])) {
            $app_id = $postData['id'];
            unset($postData['id']);
            $option = \json_encode($postData);
            $result = $this->db->updateSmartApplication(['options' => $option], $app_id);
            if (\is_numeric($result)) {
                $response['msg'] = $error = '';
                if ($result === 0) {
                    $response['nothing_to_do'] = true;
                }
            } else {
                $response['msg'] = $error = $this->setLocalization('Failed to update the parameters of application launch');
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Application is undefined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_version_install()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'changeStatus';
        if (!empty($this->postData['id'])) {
            \ignore_user_abort(true);
            \set_time_limit(0);
            try {
                $apps = new \Ministra\Lib\AppsManager();
                if (empty($this->postData['version'])) {
                    $result = $apps->installApp($this->postData['id']);
                } else {
                    $result = $apps->updateApp($this->postData['id'], $this->postData['version']);
                }
                if ($result !== false) {
                    $response['msg'] = $error = '';
                    $response['installed'] = 1;
                } else {
                    $response['msg'] = $error = $this->setLocalization('Error of installing the application');
                }
            } catch (\PharException $e) {
                $response['msg'] = $this->setLocalization($e->getMessage());
            } catch (\Exception $e) {
                $response['msg'] = $this->setLocalization($e->getMessage());
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Application is undefined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_version_install()
    {
        $id = !empty($this->postData['id']) && \is_numeric($this->postData['id']) ? $this->postData['id'] : (!empty($this->data['id']) && \is_numeric($this->data['id']) ? $this->data['id'] : false);
        if ($id !== false) {
            $response['id'] = $id;
        }
        $response = [];
        $curr_row = !empty($this->postData['curr_row']) ? $this->postData['curr_row'] : (!empty($this->data['curr_row']) ? $this->data['curr_row'] : false);
        if ($curr_row !== false) {
            $response['curr_row'] = \strpos($curr_row, '#') === false ? '#' . $curr_row : $curr_row;
        }
        if (!empty($this->postData['info'])) {
            $response['action'] = 'resetAllWarning';
            if ($id !== false) {
                $response['url_id'] = 'install_app_' . $id;
                $response['modal_message'] = $this->setLocalization('Do you really want install this application?');
            }
            $response['button_message'] = $this->setLocalization('Install');
            $error = '';
        } else {
            $response['action'] = 'manageList';
            if ($id !== false) {
                \ignore_user_abort(true);
                \set_time_limit(0);
                try {
                    $data['msg'] = $this->setLocalization('Installed');
                    $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                    if (empty($this->postData['version'])) {
                        $response['action'] = '';
                        $this->beginNotifications();
                        $apps->setNotificationCallback(function ($msg) {
                            \error_reporting(-1);
                            \ini_set('display_errors', 'On');
                            \ini_set('output_buffering', 'Off');
                            \ini_set('output_handler', '');
                            \ini_set('implicit_flush', 'On');
                            \ob_implicit_flush(true);
                            while (\ob_get_level()) {
                                \ob_end_clean();
                            }
                            \ob_start();
                            echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
                            ';
                            echo '<script  type="text/javascript"> window.parent.deliver("setModalMessage","' . $msg . '"); </script>
                            ';
                            \ob_flush();
                        });
                    }
                    if (empty($this->postData['version'])) {
                        $result = $apps->installApp($id);
                    } else {
                        $result = $apps->updateApp($id, $this->postData['version']);
                    }
                    if ($result !== false) {
                        $response['msg'] = $error = '';
                        $response['installed'] = 1;
                    } else {
                        $response['msg'] = $error = $this->setLocalization('Error of installing the application');
                    }
                } catch (\PharException $e) {
                    $error = $response['msg'] = $this->setLocalization($e->getMessage());
                } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                    $error = $response['msg'] = $this->setLocalization($e->getMessage());
                } catch (\Ministra\Lib\SmartLauncherAppsManagerConflictException $e) {
                    $response['msg'] = $this->setLocalization($e->getMessage());
                    foreach ($e->getConflicts() as $row) {
                        $response['msg'] .= '<br>' . (!empty($row['target']) ? " {$row['target']} with " : '') . " {$row['alias']} {$row['current_version']}" . PHP_EOL;
                    }
                    $error = $response['msg'];
                } catch (\Exception $e) {
                    $response['msg'] = $this->setLocalization($e->getMessage());
                }
            } else {
                $response['msg'] = $error = $this->setLocalization('Application is undefined');
            }
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        if (empty($this->postData['info']) && empty($this->postData['version'])) {
            $this->endNotification($response, $error, 'setModalMessage', 'manageList');
            exit;
        }
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function beginNotifications()
    {
        \ignore_user_abort(true);
        \set_time_limit(0);
        \error_reporting(-1);
        \ini_set('display_errors', 'On');
        \ini_set('output_buffering', 'Off');
        \ini_set('output_handler', '');
        \ini_set('zlib.output_compression', 'Off');
        \ini_set('implicit_flush', 'On');
        \ob_implicit_flush(true);
        while (\ob_get_level()) {
            \ob_end_clean();
        }
        \ob_start();
        \header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        \header('X-Accel-Buffering: no');
        \header('Content-Type: text/html; charset=utf-8');
        \ob_flush();
        echo '<!DOCTYPE html>
                            <head></head>
                            <body>
                            ';
        $sended = 0;
        $send_str = '<br/>
                ';
        $send_str_len = \mb_strlen($send_str);
        while (($sended += $send_str_len) <= 1024) {
            echo $send_str;
        }
        \ob_flush();
    }
    private function endNotification($response, $error, $msg_func, $act_func)
    {
        \error_reporting(-1);
        \ini_set('display_errors', 'On');
        \ini_set('output_buffering', 'Off');
        \ini_set('output_handler', '');
        \ini_set('implicit_flush', 'On');
        \ob_implicit_flush(true);
        while (\ob_get_level()) {
            \ob_end_clean();
        }
        \ob_start();
        echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
            ';
        if (empty($error)) {
            echo '<script type="text/javascript"> window.parent.deliver("' . $msg_func . '","' . $this->setLocalization('Done') . '"); </script>
                ';
            echo '<script type="text/javascript"> window.parent.deliver("' . $act_func . '", ' . $response . '); </script>
                ';
        } else {
            echo '<script type="text/javascript"> window.parent.deliver("' . $msg_func . '","' . $this->setLocalization('Error') . '! ' . $error . '"); </script>
                ';
            echo '<script type="text/javascript"> window.parent.deliver("' . $act_func . 'Error", ' . $response . '); </script>
                ';
        }
        echo '</body>
            </html>';
        \ob_end_flush();
    }
    public function application_version_delete()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'changeStatus';
        if (!empty($this->postData['id'])) {
            \ignore_user_abort(true);
            \set_time_limit(0);
            $app_db = $this->db->getApplication(['id' => $this->postData['id']]);
            try {
                $apps = new \Ministra\Lib\AppsManager();
                $apps->deleteApp($this->postData['id'], $this->postData['version']);
                $error = '';
                $response['msg'] = $this->setLocalization('Deleted');
                if ($app_db[0]['current_version'] == $this->postData['version']) {
                    $response['installed'] = 0;
                }
                $response['id'] = $this->postData['id'];
            } catch (\Exception $e) {
                $response['msg'] = $error = $this->setLocalization('Error of uninstalling the application.');
                $response['msg'] .= ' ' . $this->setLocalization($e->getMessage());
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Application is undefined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_version_delete()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'manageList';
        if (!empty($this->postData['id'])) {
            \ignore_user_abort(true);
            \set_time_limit(0);
            $app_db = $this->db->getSmartApplication(['id' => $this->postData['id']]);
            try {
                $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                $apps->deleteApp($this->postData['id'], $this->postData['version']);
                $response['msg'] = $error = '';
                if ($app_db[0]['current_version'] == $this->postData['version']) {
                    $response['installed'] = 0;
                }
            } catch (\Exception $e) {
                $response['msg'] = $error = $this->setLocalization('Error of uninstalling the application.');
                $response['msg'] .= ' ' . $this->setLocalization($e->getMessage());
            }
        } else {
            $response['msg'] = $error = $this->setLocalization('Application is undefined');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_toggle_state()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'changeStatus';
        $response['field'] = 'app_status';
        $postData = $this->postData;
        $response['id'] = $id = $postData['id'];
        $key = '';
        if (\array_key_exists('status', $postData)) {
            $postData['status'] = !empty($postData['status']) && $postData['status'] != 'false' && $postData['status'] !== false ? 1 : 0;
            $key = 'status';
        }
        if (\array_key_exists('autoupdate', $postData)) {
            $postData['autoupdate'] = !empty($postData['autoupdate']) && $postData['autoupdate'] != 'false' && $postData['autoupdate'] !== false ? 1 : 0;
            $response['field'] = 'app_autoupdate';
            $key = 'autoupdate';
        }
        unset($postData['id']);
        $result = $this->db->updateApplication($postData, $id);
        if (\is_numeric($result)) {
            $response['msg'] = $error = '';
            if (!empty($postData['current_version'])) {
                $response['msg'] = $this->setLocalization('Activated. Current version') . ' ' . $postData['current_version'];
            } else {
                $response = \array_merge_recursive($response, $this->application_list_json(true));
                $response['action'] = 'updateTableRow';
            }
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $response['installed'] = !empty($postData[$key]) && $postData[$key] != 'false' && $postData[$key] !== false ? 1 : 0;
        } else {
            $response['msg'] = $error = $this->setLocalization('Failed to activated of application.');
            if (!empty($postData['current_version'])) {
                $response['msg'] = $error .= $this->setLocalization('Version') . ' ' . $postData['current_version'];
            }
            $response['installed'] = (int) (!(!empty($postData[$key]) && $postData[$key] != 'false' && $postData[$key] !== false ? 1 : 0));
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Failed');
        $apps_list = null;
        try {
            $apps_list = new \Ministra\Lib\AppsManager();
            if (!$local_uses) {
                $response['data'] = \array_map(function ($row) {
                    $row['RowOrder'] = 'dTRow_' . $row['id'];
                    return $row;
                }, $apps_list->getList());
                $error = '';
            } else {
                $param = !empty($this->data) ? $this->data : $this->postData;
                $id = !empty($param['id']) ? $param['id'] : null;
                $app = $apps_list->getAppInfo($id);
                if (!empty($app)) {
                    unset($app['versions']);
                    $app['RowOrder'] = 'dTRow_' . $app['id'];
                    $error = '';
                    $response['data'][] = $app;
                } else {
                    $response['msg'] = $error = $this->setLocalization('Application is not defined');
                }
            }
        } catch (\Exception $e) {
            $response['msg'] = $error = $this->setLocalization('Failed to get the list of applications');
        }
        $response['recordsTotal'] = $response['recordsFiltered'] = \count($response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function smart_application_toggle_state()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'changeStatus';
        $response['field'] = 'app_status';
        $response['conflicts'] = false;
        $postData = $this->postData;
        $response['id'] = $id = $postData['id'];
        $key = '';
        $error = '';
        if (\array_key_exists('curr_row', $postData)) {
            $response['curr_row'] = $postData['curr_row'];
            unset($postData['curr_row']);
        }
        if (\array_key_exists('status', $postData)) {
            $postData['status'] = !empty($postData['status']) && $postData['status'] != 'false' && $postData['status'] !== false ? 1 : 0;
            $key = 'status';
        }
        if (\array_key_exists('autoupdate', $postData)) {
            $postData['autoupdate'] = !empty($postData['autoupdate']) && $postData['autoupdate'] != 'false' && $postData['autoupdate'] !== false ? 1 : 0;
            $response['field'] = 'app_autoupdate';
            $key = 'autoupdate';
        }
        $app = $this->db->getSmartApplication(['id' => $id]);
        if ($postData['status'] && !empty($app) && $app[0]['type'] == 'launcher') {
            $active_launchers = $this->db->getSmartApplication(['id<>' => $id, 'status' => 1, 'type' => 'launcher']);
            if (!empty($active_launchers)) {
                $response['msg'] = $error = $this->setLocalization('Disable {ln} and then enable this launcher', '', $active_launchers[0]['url'], ['{ln}' => $active_launchers[0]['url']]);
            }
        }
        try {
            $apps_list = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            $conflicts = $apps_list->getConflicts($id, !empty($postData['current_version']) ? $postData['current_version'] : null);
            $response['conflicts'] = !empty($conflicts);
        } catch (\Exception $e) {
        }
        unset($postData['id']);
        if (empty($error)) {
            if (!$response['conflicts'] || !$postData['status']) {
                $result = $this->db->updateSmartApplication($postData, $id);
                if (\is_numeric($result)) {
                    $response['msg'] = $error = '';
                    if (!empty($postData['current_version'])) {
                        $response['msg'] = $this->setLocalization('Activated. Current version') . ' ' . $postData['current_version'];
                    }
                    if ($result === 0) {
                        $response['nothing_to_do'] = true;
                    }
                    $response['installed'] = !empty($postData[$key]) && $postData[$key] != 'false' && $postData[$key] !== false ? 1 : 0;
                    if (!empty($app) && $app[0]['type'] == 'theme') {
                        $theme = new \Ministra\Lib\Theme($app[0]['alias']);
                        if ($postData['status'] == 0) {
                            $theme->deleteThemeCompiledCSS();
                        } else {
                            $theme->generateThemeCSS();
                        }
                        if (!empty($postData['current_version']) && $postData['current_version'] != $app[0]['current_version']) {
                            $theme->setVersion($app[0]['current_version']);
                            $theme->deleteThemeCompiledCSS();
                        }
                    }
                } else {
                    $response['msg'] = $error = $this->setLocalization('Failed to activated of application.');
                    if (!empty($postData['current_version'])) {
                        $response['msg'] = $error .= $this->setLocalization('Version') . ' ' . $postData['current_version'];
                    }
                    $response['installed'] = (int) (!(!empty($postData[$key]) && $postData[$key] != 'false' && $postData[$key] !== false ? 1 : 0));
                }
            } else {
                $response['msg'] = $error = $this->setLocalization('This application version has conflicts');
            }
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function application_delete()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'deleteTableRow';
        $response['id'] = $this->postData['id'];
        if ($this->db->deleteApplication($this->postData)) {
            $error = '';
            $response['msg'] = $this->setLocalization('Application has been deleted');
        } else {
            $response['msg'] = $error = $this->setLocalization('Failed to delete application.');
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_delete()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $response['action'] = 'manageList';
        try {
            $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            $apps->deleteApp($this->postData['id']);
            $response['msg'] = $error = '';
            $response['msg'] = $this->setLocalization('Application has been deleted');
        } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
            $response['msg'] = $error = $this->setLocalization('Failed to delete application.') . $e->getMessage();
        }
        $response = $this->generateAjaxResponse($response);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_reset_all()
    {
        $response = ['action' => 'manageList'];
        $error = $this->setLocalization('Failed');
        $response = [];
        if (!empty($this->postData['info'])) {
            $response['action'] = 'resetAllWarning';
            $response['data'] = $this->db->getSmartApplication(['manual_install' => 1]);
            $error = '';
        } else {
            try {
                $response = ['action' => ''];
                $this->beginNotifications();
                $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                $apps->setNotificationCallback(function ($msg) {
                    \error_reporting(-1);
                    \ini_set('display_errors', 'On');
                    \ini_set('output_buffering', 'Off');
                    \ini_set('output_handler', '');
                    \ini_set('implicit_flush', 'On');
                    \ob_implicit_flush(true);
                    while (\ob_get_level()) {
                        \ob_end_clean();
                    }
                    \ob_start();
                    echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
                    ';
                    echo '<script  type="text/javascript"> window.parent.deliver("setModalMessage","' . $msg . '"); </script>
                    ';
                    \ob_flush();
                });
                if ($apps->resetApps()) {
                    $error = '';
                }
            } catch (\Ministra\Lib\SmartLauncherAppsManagerConflictException $e) {
                $response['msg'] = $error = $e->getMessage();
                foreach ($e->getConflicts() as $row) {
                    $error .= '<br>' . (!empty($row['target']) ? "{$row['target']} with " : '') . " {$row['alias']} {$row['current_version']}";
                }
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $response['msg'] = $error = $e->getMessage();
            }
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        if (empty($this->postData['info'])) {
            $this->endNotification($response, $error, 'setModalMessage', 'manageList');
            exit;
        }
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_download_list()
    {
        $response = ['action' => ''];
        $error = $this->setLocalization('Failed');
        try {
            $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
            \header('Set-Cookie: fileDownload=true; path=/');
            \header('Cache-Control: max-age=60, must-revalidate');
            \header('Content-type: text/json');
            \header('Content-Disposition: attachment; filename="stalker-apps-snapshot-' . \DateTime::createFromFormat('U', \time())->format('Y_m_d_H_i') . '.json"');
            echo $apps->getSnapshot();
            exit;
        } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
            $response['msg'] = $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_upload_list()
    {
        $response = ['action' => ''];
        $error = $this->setLocalization('Failed');
        if (empty($this->data['install_path']) || empty($this->data['install_file'])) {
            try {
                $storage = new \Upload\Storage\FileSystem('/tmp', true);
                $file = new \Upload\File('files', $storage);
                $file->upload();
                $response['install_path'] = $file->getPath();
                $response['install_file'] = $file->getNameWithExtension();
                $response['msg'] = $this->setLocalization('Loaded');
                $error = '';
                $response['action'] = 'setUploadMessage';
            } catch (\Exception $e) {
                $response['msg'] = $error = $e->getMessage();
                if (!empty($file)) {
                    $error .= ' ' . $file->getErrors();
                }
                $response['msg'] = $error;
            }
        } else {
            try {
                $json_str = \file_get_contents($this->data['install_path'] . '/' . $this->data['install_file']);
                @\unlink($this->data['install_path'] . '/' . $this->data['install_file']);
                \ignore_user_abort(true);
                \set_time_limit(0);
                $this->beginNotifications();
                $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                $apps->setNotificationCallback(function ($msg) {
                    \error_reporting(-1);
                    \ini_set('display_errors', 'On');
                    \ini_set('output_buffering', 'Off');
                    \ini_set('output_handler', '');
                    \ini_set('implicit_flush', 'On');
                    \ob_implicit_flush(true);
                    while (\ob_get_level()) {
                        \ob_end_clean();
                    }
                    \ob_start();
                    echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
                    ';
                    echo '<script  type="text/javascript"> window.parent.deliver("setUploadMessage","' . $msg . '"); </script>
                    ';
                    \ob_flush();
                });
                $apps->restoreFromSnapshot($json_str);
                $error = '';
                $response['action'] = '';
                $response['msg'] = $this->setLocalization('Loaded');
            } catch (\Ministra\Lib\SmartLauncherAppsManagerConflictException $e) {
                $response['msg'] = $error = $e->getMessage();
                foreach ($e->getConflicts() as $row) {
                    $error .= '<br>' . (!empty($row['target']) ? "{$row['target']} with " : '') . " {$row['alias']} {$row['current_version']}";
                }
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $response['msg'] = $error = $e->getMessage();
            }
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        if (!empty($this->data['install_path']) && !empty($this->data['install_file'])) {
            $this->endNotification($response, $error, 'setUploadMessage', 'manageList');
            exit;
        }
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_update()
    {
        $response = ['action' => 'manageList'];
        $error = $this->setLocalization('Failed');
        $id = !empty($this->postData['id']) && \is_numeric($this->postData['id']) ? $this->postData['id'] : (!empty($this->data['id']) && \is_numeric($this->data['id']) ? $this->data['id'] : false);
        if ($id !== false) {
            $response['id'] = $id;
        }
        $response = [];
        $curr_row = !empty($this->postData['curr_row']) ? $this->postData['curr_row'] : (!empty($this->data['curr_row']) ? $this->data['curr_row'] : false);
        if ($curr_row !== false) {
            $response['curr_row'] = \strpos($curr_row, '#') === false ? '#' . $curr_row : $curr_row;
        }
        if (!empty($this->postData['info'])) {
            $response['action'] = 'resetAllWarning';
            if ($id !== false) {
                $response['url_id'] = 'update_app_' . $id;
                $response['modal_message'] = $this->setLocalization('Do you really want update this application?');
            } else {
                $response['url_id'] = 'update_all_apps';
                $response['modal_message'] = $this->setLocalization('Do you really want update all application?');
            }
            $response['button_message'] = $this->setLocalization('Update');
            $error = '';
        } else {
            try {
                $data['msg'] = $this->setLocalization('Updated');
                $response['action'] = '';
                $this->beginNotifications();
                $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                $apps->setNotificationCallback(function ($msg) {
                    \error_reporting(-1);
                    \ini_set('display_errors', 'On');
                    \ini_set('output_buffering', 'Off');
                    \ini_set('output_handler', '');
                    \ini_set('implicit_flush', 'On');
                    \ob_implicit_flush(true);
                    while (\ob_get_level()) {
                        \ob_end_clean();
                    }
                    \ob_start();
                    echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
                        ';
                    echo '<script  type="text/javascript"> window.parent.deliver("setModalMessage","' . $msg . '"); </script>
                        ';
                    \ob_flush();
                });
                $param = [];
                $func = 'updateApps';
                if ($id !== false) {
                    $func = 'updateApp';
                    $param[] = $id;
                }
                $error = '';
                \call_user_func_array([$apps, $func], $param);
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $error = $e->getMessage();
            }
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        if (empty($this->postData['info'])) {
            $this->endNotification($response, $error, 'setModalMessage', 'manageList');
            exit;
        }
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function smart_application_check_update()
    {
        $response = ['action' => 'manageList'];
        $error = $this->setLocalization('Failed');
        $id = !empty($this->postData['id']) && \is_numeric($this->postData['id']) ? $this->postData['id'] : (!empty($this->data['id']) && \is_numeric($this->data['id']) ? $this->data['id'] : false);
        if ($id !== false) {
            $response['id'] = $id;
        }
        $curr_row = !empty($this->postData['curr_row']) ? $this->postData['curr_row'] : (!empty($this->data['curr_row']) ? $this->data['curr_row'] : false);
        if ($curr_row !== false) {
            $response['curr_row'] = \strpos($curr_row, '#') === false ? '#' . $curr_row : $curr_row;
        }
        if (!empty($this->postData['info'])) {
            $response['action'] = 'resetAllWarning';
            if ($id !== false) {
                $response['url_id'] = 'check_update_app_' . $id;
                $response['modal_message'] = $this->setLocalization('Do you really want checking update for this application?');
            } else {
                $response['url_id'] = 'check_update_apps';
                $response['modal_message'] = $this->setLocalization('Do you really want checking updates for all application?');
            }
            $response['button_message'] = $this->setLocalization('Check');
            $error = '';
        } else {
            try {
                $data['msg'] = $this->setLocalization('Checked');
                $response['action'] = '';
                $this->beginNotifications();
                $apps = new \Ministra\Lib\SmartLauncherAppsManager($this->app['language']);
                $apps->setNotificationCallback(function ($msg) {
                    \error_reporting(-1);
                    \ini_set('display_errors', 'On');
                    \ini_set('output_buffering', 'Off');
                    \ini_set('output_handler', '');
                    \ini_set('implicit_flush', 'On');
                    \ob_implicit_flush(true);
                    while (\ob_get_level()) {
                        \ob_end_clean();
                    }
                    \ob_start();
                    echo '<script type="text/javascript"> var x = ' . \microtime(true) . ';</script>
                        ';
                    echo '<script  type="text/javascript"> window.parent.deliver("setModalMessage","' . $msg . '"); </script>
                        ';
                    \ob_flush();
                });
                $param = [];
                $func = 'updateAllAppsInfo';
                if ($id !== false) {
                    $func = 'getAppInfo';
                    $param[] = $id;
                    $param[] = true;
                }
                $error = '';
                \call_user_func_array([$apps, $func], $param);
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $error = $e->getMessage();
            }
        }
        $response = $this->generateAjaxResponse($response);
        $response = \json_encode($response);
        if (empty($this->postData['info'])) {
            $this->endNotification($response, $error, 'setModalMessage', 'manageList');
            exit;
        }
        return new \Symfony\Component\HttpFoundation\Response($response, empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
