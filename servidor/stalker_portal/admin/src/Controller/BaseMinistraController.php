<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
class BaseMinistraController
{
    protected $app;
    protected $request;
    protected $baseDir;
    protected $baseHost;
    protected $workHost;
    protected $relativePath;
    protected $workURL;
    protected $refferer;
    protected $Uri;
    protected $method;
    protected $isAjax;
    protected $data;
    protected $postData;
    protected $db;
    protected $admin;
    protected $session;
    protected $access_level = 0;
    protected $access_levels = array(0 => 'denied', 1 => 'view', 2 => 'edit', 3 => 'edit', 4 => 'action', 5 => 'all', 6 => 'all', 7 => 'all', 8 => 'all');
    protected $sidebar_cache_time;
    protected $language_codes_en = array();
    protected $redirect = false;
    public function __construct(\Silex\Application $app, $modelName = '')
    {
        $this->app = $app;
        $this->request = $app['request_stack']->getCurrentRequest();
        $this->admin = $this->app['admin'];
        $this->setAjaxFlag();
        $this->getPathInfo();
        $this->getData();
        $this->setRequestMethod();
        $this->checkLastLocation();
        if ($this->redirect) {
            $this->app->redirect(!empty($this->redirect) ? $this->redirect : $this->workURL);
            return;
        }
        $this->baseDir = \rtrim(\str_replace(['src', 'Controller'], '', __DIR__), '//');
        if (!empty($modelName) && $modelName === static::class) {
            $modelName = \explode('\\', $modelName);
            $modelName = \str_replace('Controller', '', $modelName[\count($modelName) - 1]) . 'Model';
            $modelName = "\\Ministra\\Admin\\Model\\{$modelName}";
        }
        $modelName = \class_exists($modelName) ? $modelName : 'Ministra\\Admin\\Model\\BaseMinistraModel';
        if (\class_exists($modelName)) {
            $this->db = new $modelName($this->admin);
            if (!$this->db instanceof $modelName) {
                $this->db = null;
            }
        }
        $this->db->setReseller($this->app->offsetExists('reseller') ? $this->app['reseller'] : null);
        $this->db->setAdmin($this->app->offsetExists('user_id') ? $this->app['user_id'] : null, $this->app->offsetExists('userlogin') ? $this->app['userlogin'] : null);
        $this->app['certificate_server_health_check'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('certificate_server_health_check', true);
        if (!$this->isAjax) {
            $update = $this->db->getAllFromTable('updates', 'id');
            if (!empty($update)) {
                $this->app['new_version'] = \end($update);
            } else {
                $this->app['new_version'] = false;
            }
            if (\is_file($this->baseDir . '/../c/version.js')) {
                $tmp = \file_get_contents($this->baseDir . '/../c/version.js');
                if (\preg_match('/[\\d\\.]+/i', $tmp, $ver) && !empty($ver)) {
                    $this->app['current_version'] = $ver[0];
                } else {
                    $this->app['current_version'] = false;
                }
            }
            if ($this->admin) {
                $this->setBreadcrumbs();
            }
            $this->app['session']->set('cached_lang', $this->app['language']);
        }
        if (isset($this->data['set-dropdown-attribute']) && \method_exists($this, 'set_dropdown_attribute')) {
            \call_user_func([$this, 'set_dropdown_attribute']);
            exit;
        }
        if (\exec('npm -v') !== '2.15.11') {
            $this->app['npmVersionError'] = 1;
        }
        if ($this->admin) {
            $this->setAccessLevel();
        }
    }
    private function setAjaxFlag()
    {
        $this->isAjax = $this->request->isXmlHttpRequest();
    }
    private function getPathInfo()
    {
        $this->workURL = $this->app['workURL'];
        $this->baseHost = $this->app['baseHost'];
        $this->workHost = $this->app['workHost'];
        $this->refferer = $this->app['refferer'];
        if (!$this->isAjax) {
            $ext_path = (!empty($tmp[0]) ? \implode('', \array_map('ucfirst', \explode('-', $tmp[0]))) : 'Index') . '/' . (!empty($tmp[1]) ? \str_replace('-', '_', $tmp[1]) : 'index') . '/';
            $this->app['assetic_ext_min_name'] = (!empty($tmp[0]) ? \str_replace('-', '_', $tmp[0]) : 'index') . '_' . (!empty($tmp[1]) ? \str_replace('-', '_', $tmp[1]) : 'index');
            $this->app['assetic_path_to_source'] = $this->baseDir . '/../server/adm/';
            if ($this->app['stalker_env'] == 'min') {
                $this->app['assetic_base_web_path'] = $this->workURL . '/min/';
                $this->app['assetic_base_js_path'] = $this->app['twig_theme'] . '/js/';
                $this->app['assetic_base_css_path'] = $this->app['twig_theme'] . '/css/';
                $this->app['assetic_ext_web_path'] = $ext_path;
            } else {
                $this->app['assetic_base_web_path'] = \rtrim($this->workURL, '/') . '/';
                $this->app['assetic_base_js_path'] = 'js/dev/';
                $this->app['assetic_base_css_path'] = 'css/dev/';
                $this->app['assetic_ext_web_path'] = $ext_path;
            }
        }
    }
    private function getData()
    {
        $this->data = $this->request->query->all();
        $this->postData = $this->request->request->all();
        if (!empty($this->postData['group_key']) && \is_string($this->postData[$this->postData['group_key']]) && ($parsed_json = \json_decode($this->postData[$this->postData['group_key']], true)) && \json_last_error() == JSON_ERROR_NONE) {
            $this->postData[$this->postData['group_key']] = $parsed_json;
        }
    }
    private function setRequestMethod()
    {
        $this->method = $this->request->getMethod();
    }
    protected function checkLastLocation()
    {
        $token = $this->app['security.token_storage']->getToken();
        if (!$this->app->offsetExists('userlogin')) {
            $this->app['userlogin'] = '';
        }
        if (empty($this->app['userlogin']) && $token && ($user = $token->getUser())) {
            $this->app['userlogin'] = $user->getUsername();
        }
        if (!$this->isAjax) {
            if ($this->app['controller_alias'] != 'login' && $this->app['controller_alias'] != 'logout' && $this->app['action_alias'] != 'auth-user-logout') {
                if (!empty($this->app['userlogin'])) {
                    $location_path = $this->app['controller_alias'];
                    if (!empty($this->app['action_alias'])) {
                        $location_path .= '/' . $this->app['action_alias'];
                    }
                    if (!empty($this->data)) {
                        $location_path .= '?' . \urldecode($this->request->getQueryString());
                    }
                    $last_location = $this->request->cookies->get('last_location');
                    if (($parsed_json = \json_decode($last_location, true)) && $parsed_json && \json_last_error() == JSON_ERROR_NONE) {
                        $last_location_array = $parsed_json;
                    } else {
                        $last_location_array = [];
                    }
                    $last_location_array[\md5($this->app['userlogin'])] = \trim($this->workURL, '/') . "/{$location_path}";
                    $cookie_all = $this->request->cookies->all();
                    $cookie_all['last_location'] = $last_location_array;
                    while (\count($last_location_array) != 0 && \strlen(\json_encode($cookie_all)) > 4000) {
                        \array_shift($last_location_array);
                        $cookie_all['last_location'] = $last_location_array;
                    }
                    \setcookie('last_location', '', \time() - 3600);
                    \setcookie('last_location', \json_encode($last_location_array), \time() + 60 * 60 * 24, '/');
                }
            } else {
                $refferer = \explode('/', $this->refferer);
                $refferer = \end($refferer);
                if ($refferer == 'login' && !empty($this->app['userlogin'])) {
                    $last_location = $this->request->cookies->get('last_location');
                    if (($parsed_json = \json_decode($last_location, true)) && $parsed_json && \json_last_error() == JSON_ERROR_NONE) {
                        $last_location_array = $parsed_json;
                    } else {
                        $last_location_array = [];
                    }
                    if (\array_key_exists(\md5($this->app['userlogin']), $last_location_array)) {
                        $this->redirect = $last_location_array[\md5($this->app['userlogin'])];
                    }
                }
            }
        }
        return false;
    }
    protected function setBreadcrumbs()
    {
        $side_bar = $this->app['side_bar'];
        while (list($key, $row) = \each($side_bar)) {
            $controller = \str_replace('_', '-', $row['alias']);
            if ($this->app['controller_alias'] == $controller) {
                $this->app['breadcrumbs']->addItem($row['name'], $this->workURL . "/{$controller}");
                while (list($key_a, $row_a) = \each($row['action'])) {
                    $action = \str_replace('_', '-', $row_a['alias']);
                    if ($this->app['controller_alias'] == $controller && $this->app['action_alias'] == $action) {
                        $this->app['breadcrumbs']->addItem($row_a['name'], $this->workURL . "/{$controller}/{$action}");
                        break;
                    }
                }
                break;
            }
        }
    }
    private function setAccessLevel()
    {
        $this->setControllerAccessMap();
        $controller_alias = !empty($this->app['controller_alias']) ? $this->app['controller_alias'] : 'index';
        if (\array_key_exists($controller_alias, $this->app['controllerAccessMap']) && $this->app['controllerAccessMap'][$controller_alias]['access']) {
            if ($this->app['action_alias'] == '' || $this->app['action_alias'] == 'index') {
                $this->access_level = $this->app['controllerAccessMap'][$controller_alias]['access'];
                return;
            } elseif (\array_key_exists($this->app['action_alias'], $this->app['controllerAccessMap'][$controller_alias]['action'])) {
                $parent_access = $this->getParentActionAccess();
                $this->access_level = $parent_access !== false ? $parent_access : $this->app['controllerAccessMap'][$controller_alias]['action'][$this->app['action_alias']]['access'];
                return;
            }
        }
        $this->access_level = 0;
    }
    private function setControllerAccessMap()
    {
        if (!$this->app->offsetExists('controllerAccessMap') || $this->app->offsetExists('controllerAccessMap') && empty($this->app['controllerAccessMap'])) {
            $is_admin = !empty($this->app['userlogin']) && $this->app['userlogin'] == 'admin';
            $gid = $is_admin ? '' : $this->admin->getUserGroupId();
            $map = [];
            $tmp_map = $this->db->getControllerAccess($gid, $this->app['reseller']);
            foreach ($tmp_map as $row) {
                if (!\array_key_exists($row['controller_name'], $map)) {
                    $map[$row['controller_name']]['access'] = !$is_admin ? $this->getDecFromBin($row) : '8';
                    if ($map[$row['controller_name']]['access'] == 0) {
                        continue;
                    }
                    $map[$row['controller_name']]['action'] = [];
                }
                if (!empty($row['action_name']) && $row['action_name'] != 'index' || $row['controller_name'] != 'index') {
                    $map[$row['controller_name']]['action'][$row['action_name']]['access'] = !$is_admin ? $this->getDecFromBin($row) : '8';
                }
            }
            $this->app['controllerAccessMap'] = $map;
        }
    }
    private function getDecFromBin($row)
    {
        return \bindec($row['action_access'] . $row['edit_access'] . $row['view_access']);
    }
    protected function getParentActionAccess()
    {
        $return = false;
        if ($this->app['userlogin'] !== 'admin' && $this->isAjax && \preg_match('/-json$/', $this->app['action_alias'])) {
            $action_alias = \preg_replace(['/-composition/i', '/-datatable\\d/i', '/-version/'], '', $this->app['action_alias'], 1);
            $parent_1 = \str_replace('-json', '', $action_alias);
            $parent_2 = \str_replace('-list-json', '', $action_alias);
            $parent_access = 0;
            if ($parent_1 == $this->app['controller_alias'] || $parent_2 == $this->app['controller_alias']) {
                $parent_access = $this->app['controllerAccessMap'][$this->app['controller_alias']]['access'];
            } elseif (\array_key_exists($parent_1, $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'])) {
                $parent_access = $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'][$parent_1]['access'];
            } elseif (\array_key_exists($parent_2, $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'])) {
                $parent_access = $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'][$parent_2]['access'];
            } elseif (\array_key_exists($action_alias, $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'])) {
                $parent_access = $this->app['controllerAccessMap'][$this->app['controller_alias']]['action'][$action_alias]['access'];
            }
            $return = (int) ($parent_access > 0);
        }
        return $return;
    }
    public function groupPostAction($method, $post_key)
    {
        if (\method_exists($this, $method)) {
            $parsed_json = false;
            $data = ['group' => []];
            $error = false;
            $group_action = false;
            if (!empty($this->postData['group_action'])) {
                $group_action = $this->postData['group_action'];
            }
            if (!empty($this->postData[$post_key]) && \is_string($this->postData[$post_key]) && ($parsed_json = \json_decode($this->postData[$post_key], true)) && \json_last_error() == JSON_ERROR_NONE) {
                if (\is_array($parsed_json)) {
                    \reset($parsed_json);
                }
                while (list($num, $postdata) = \each($parsed_json)) {
                    $this->postData[$post_key] = $postdata;
                    $data['group'][$num] = $this->{$method}();
                    $error = $error || !empty($data['group'][$num]['error']);
                }
            }
            $response = $this->generateAjaxResponse($data, $error);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        $this->app->abort(501, $this->setLocalization('Unrecognized group operation'));
    }
    protected function generateAjaxResponse($data = array(), $error = '')
    {
        $response = [];
        if (!empty($this->postData['for_validator'])) {
            $error = \trim($error);
            $response['valid'] = empty($error) && !empty($data);
            $response['message'] = \array_key_exists('chk_rezult', $data) ? \trim($data['chk_rezult']) : $error;
        } else {
            if (empty($error) && !empty($data)) {
                $response['success'] = true;
                $response['error'] = false;
            } else {
                $response['success'] = false;
                $response['error'] = $error;
            }
            $response = \array_merge($response, $data);
        }
        return $response;
    }
    public function setLocalization($source = array(), $fieldname = '', $number = false, $params = array(), $locale = null)
    {
        if (!empty($source)) {
            if (!\is_array($source)) {
                if ($number === false) {
                    $translate = $this->app['translator']->trans($source, $params, $locale);
                } else {
                    $translate = $this->app['translator']->transChoice($source, $number, $params, $locale);
                }
                return !empty($translate) ? $translate : $source;
            } elseif (\array_key_exists($fieldname, $source)) {
                $source[$fieldname] = $this->setLocalization((string) $source[$fieldname], $fieldname, $number, $params, $locale);
            } else {
                while (list($key, $row) = \each($source)) {
                    $source[$key] = $this->setLocalization($row, $fieldname, $number, $params, $locale);
                }
            }
        }
        return $source;
    }
    protected function getTemplateName($method_name, $extend = '')
    {
        $method_name = \explode('::', \str_replace([__NAMESPACE__, '\\'], '', $method_name));
        $method_name[] = \end($method_name);
        $method_name[0] = \str_replace('Controller', '', $method_name[0]);
        return $this->app['twig_theme'] . '/' . \implode('/', $method_name) . $extend . '.twig';
    }
    protected function getCoverFolder($id)
    {
        $dir_name = \ceil($id / 100);
        $dir_path = \realpath(PROJECT_PATH . '/../' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('screenshots_path', 'screenshots/')) . '/' . $dir_name;
        if (!\is_dir($dir_path)) {
            \umask(0);
            if (!\mkdir($dir_path, 0777)) {
                return -1;
            }
            return $dir_path;
        }
        return $dir_path;
    }
    protected function transliterate($st)
    {
        $st = \trim($st);
        $replace = ['а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'g', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'G', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' => 'E', 'ё' => 'yo', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ь' => '', 'ю' => 'yu', 'я' => 'ya', 'Ё' => 'Yo', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ь' => '', 'Ю' => 'Yu', 'Я' => 'Ya', ' ' => '_', '!' => '', '?' => '', ',' => '', '.' => '', '"' => '', '\'' => '', '\\' => '', '/' => '', ';' => '', ':' => '', '«' => '', '»' => '', '`' => '', '-' => '-', '—' => '-'];
        $st = \strtr($st, $replace);
        $st = \preg_replace('/[^a-z0-9_-]/i', '', $st);
        return $st;
    }
    protected function prepareDataTableParams($params = array(), $drop_columns = array())
    {
        $query_param = ['select' => [], 'like' => [], 'order' => [], 'limit' => ['offset' => 0, 'limit' => false]];
        if (empty($params) || !\is_array($params) || !\array_key_exists('columns', $params)) {
            return $query_param;
        }
        if (\array_key_exists('length', $params)) {
            $query_param['limit']['limit'] = (int) $params['length'];
        } else {
            $query_param['limit']['limit'] = false;
        }
        if (\array_key_exists('start', $params)) {
            $query_param['limit']['offset'] = (int) $params['start'];
        } else {
            $query_param['limit']['offset'] = null;
        }
        if (!empty($params['order'])) {
            foreach ($params['order'] as $val) {
                $column = $params['columns'][(int) $val['column']];
                $direct = $val['dir'];
                $col_name = !empty($column['name']) ? $column['name'] : (!empty($column['data']) ? $column['data'] : false);
                if ($col_name === false || \in_array($col_name, $drop_columns)) {
                    continue;
                }
                if ($column['orderable']) {
                    $query_param['order'][$col_name] = $direct;
                }
            }
        }
        if (!empty($params['columns'])) {
            foreach ($params['columns'] as $key => $column) {
                $col_name = !empty($column['name']) ? $column['name'] : (!empty($column['data']) ? $column['data'] : false);
                if ($col_name === false || \in_array($col_name, $drop_columns)) {
                    continue;
                }
                $query_param['select'][] = $col_name;
                if (!\array_key_exists('visible', $column) || $column['visible'] != 'false') {
                    \settype($params['search']['value'], 'string');
                    if (!empty($column['searchable']) && $column['searchable'] == 'true' && (!empty($params['search']['value']) || $params['search']['value'] === '0') && $params['search']['value'] != 'false') {
                        $query_param['like'][$col_name] = '%' . \addslashes($params['search']['value']) . '%';
                    }
                }
            }
        }
        return $this->prepareParamForSQLQuery($query_param);
    }
    protected function prepareParamForSQLQuery($query_param)
    {
        $handled = [];
        $link = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->z8b5ab0519df29195de5f168802f3fa51();
        foreach ($query_param as $field => $value) {
            $field = \preg_replace('/(--[^\\r\\n]*)|(\\/\\*[\\w\\W]*?(?=\\*\\/)\\*\\/)|[\'\\"\\\\;\\/\\.\\[\\]\\{\\}\\|\\%\\0\\t\\r\\n\\s]/', '', $field);
            if (\is_array($value)) {
                $value = $this->prepareParamForSQLQuery($value);
            }
            $handled[$field] = $value;
        }
        return $handled;
    }
    protected function cleanQueryParams(&$data, $filds_for_delete = array(), $fields_for_replace = array(), $order_no_replace = false)
    {
        \reset($data);
        while (list($key, $block) = \each($data)) {
            if ($order_no_replace !== false && $key == 'order') {
                continue;
            }
            foreach ($filds_for_delete as $field) {
                if (\array_key_exists($field, $block)) {
                    $new_name = \str_replace(" as `{$field}`", '', $fields_for_replace[$field]);
                    if (\array_key_exists($field, $fields_for_replace) && !\is_numeric($new_name)) {
                        $data[$key][$new_name] = $data[$key][$field];
                    }
                    unset($data[$key][$field]);
                } elseif (($search = \array_search($field, $block)) !== false && \array_search($fields_for_replace[$field], $block) === false) {
                    if (\array_key_exists($field, $fields_for_replace)) {
                        $data[$key][] = $fields_for_replace[$field];
                    }
                    unset($data[$key][$search]);
                }
            }
        }
    }
    protected function orderByDeletedParams(&$data, $param)
    {
        foreach ($param as $field => $direct) {
            $direct = \strtoupper($direct) == 'ASC' ? 1 : -1;
            \usort($data, function ($a, $b) use($field, $direct) {
                return ($a[$field] >= $b[$field] ? -1 : 1) * $direct;
            });
        }
    }
    protected function checkDisallowFields(&$data, $fields = array())
    {
        $return = [];
        while (list($key, $block) = \each($data)) {
            foreach ($fields as $field) {
                if (\array_key_exists($field, $block)) {
                    $return[$key][$field] = $block[$field];
                    unset($data[$key][$field]);
                } elseif (($search = \array_search($field, $block)) !== false) {
                    $return[$key][$field] = $block[$search];
                    unset($data[$key][$search]);
                }
            }
        }
        return $return;
    }
    protected function infliction_array($dest = array(), $source = array())
    {
        if (\is_array($dest)) {
            while (list($d_key, $d_row) = \each($dest)) {
                if (\is_array($source)) {
                    if (\array_key_exists($d_key, $source)) {
                        $dest[$d_key] = $this->infliction_array($d_row, $source[$d_key]);
                    } else {
                        continue;
                    }
                } else {
                    return $dest;
                }
            }
        } elseif (!\is_array($source)) {
            return $source;
        }
        return $dest;
    }
    protected function checkDropdownAttribute(&$attribute, $filters = '')
    {
        $param = [];
        $param['admin_id'] = $this->admin->getId();
        $param['controller_name'] = $this->app['controller_alias'];
        $param['action_name'] = (empty($this->app['action_alias']) ? 'index' : $this->app['action_alias']) . $filters;
        $attribute['all'] = ['name' => 'all', 'title' => $this->setLocalization('All'), 'checked' => (bool) \array_sum($this->getFieldFromArray($attribute, 'checked'))];
        $base_attribute = $this->db->getDropdownAttribute($param);
        if (empty($base_attribute)) {
            return $attribute;
        }
        $dropdown_attributes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($base_attribute['dropdown_attributes']);
        foreach ($dropdown_attributes as $key => $value) {
            \reset($attribute);
            while (list($num, $row) = \each($attribute)) {
                if ($row['name'] === $key && $num !== 'all') {
                    $attribute[$num]['checked'] = $value == 'true';
                    $attribute['all']['checked'] = $attribute['all']['checked'] && $attribute[$num]['checked'];
                    break;
                }
            }
        }
    }
    public function getFieldFromArray($array, $field)
    {
        $return_array = [];
        if (\is_array($array) && !empty($array)) {
            $tmp = \array_values($array);
            if (!empty($tmp) && \is_array($tmp[0]) && \array_key_exists($field, $tmp[0])) {
                foreach ($array as $key => $value) {
                    $return_array[] = $value[$field];
                }
            }
        }
        return $return_array;
    }
    protected function getUCArray($array = array(), $field = '')
    {
        \reset($array);
        while (list($key, $row) = \each($array)) {
            if (!empty($field)) {
                $row[$field] = $this->mb_ucfirst($row[$field]);
            } else {
                $row = $this->mb_ucfirst($row);
            }
            $array[$key] = $row;
        }
        return $array;
    }
    protected function mb_ucfirst($str)
    {
        $fc = \mb_strtoupper(\mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8');
        return $fc . \mb_substr($str, 1, \mb_strlen($str), 'UTF-8');
    }
    protected function getLanguageCodesEN($code = false)
    {
        if (empty($this->language_codes_en)) {
            $this->language_codes_en = $this->db->getAllFromTable('languages', 'name');
            $this->language_codes_en = $this->setLocalization(\array_combine($this->getFieldFromArray($this->language_codes_en, 'iso_639_code'), $this->getFieldFromArray($this->language_codes_en, 'name')));
        }
        return $code !== false ? \is_array($this->language_codes_en) && \array_key_exists($code, $this->language_codes_en) ? $this->language_codes_en[$code] : '' : $this->language_codes_en;
    }
    protected function groupMessageList($id, $result, $msg_tmpl)
    {
        if ($result !== 0) {
            if (\is_numeric($result)) {
                return ['status' => $msg_tmpl['success']['status'], 'msg' => $this->setLocalization($msg_tmpl['success']['msg'], '', $id, ['{updid}' => $id])];
            } elseif (\array_key_exists('error', $msg_tmpl)) {
                return ['status' => $msg_tmpl['error']['status'], 'msg' => $this->setLocalization($msg_tmpl['error']['msg'], '', $id, ['{updid}' => $id])];
            }
            return $this->groupMessageList($id, 0, $msg_tmpl);
        }
        return ['status' => $msg_tmpl['failed']['status'], 'msg' => $this->setLocalization($msg_tmpl['failed']['msg'], '', $id, ['{updid}' => $id])];
    }
    protected function setSQLDebug($flag = 0)
    {
        if ($this->db) {
            $this->db->setSQLDebug($flag);
        }
    }
}
