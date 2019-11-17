<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class AdminsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/admins-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function admins_list()
    {
        $attribute = $this->getAdminsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allAdminGroups'] = $this->db->getAdminGropsList(['select' => ['A_G.id as id', 'A_G.name as name']]);
        if (empty($this->app['reseller'])) {
            $resellers = [['id' => '-', 'name' => '']];
            $this->app['allResellers'] = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
        }
        $all_groups = $this->db->getAllFromTable('admin_groups ');
        if (!empty($all_groups) && \is_array($all_groups)) {
            $this->app['allGroups'] = $all_groups;
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getAdminsDropdownAttribute()
    {
        $return = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Login'), 'checked' => true], ['name' => 'group_name', 'title' => $this->setLocalization('Group'), 'checked' => true]];
        if (empty($this->app['reseller'])) {
            $return[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        $return[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $return;
    }
    public function admins_groups()
    {
        if (empty($this->app['reseller'])) {
            $resellers = [['id' => '-', 'name' => '']];
            $this->app['allResellers'] = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
        }
        $all_groups = $this->db->getAllFromTable('admin_groups ');
        if (!empty($all_groups) && \is_array($all_groups)) {
            $this->app['allGroups'] = $all_groups;
        }
        $attribute = $this->getAdminGroupsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getAdminGroupsDropdownAttribute()
    {
        $return = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'admin_count', 'title' => $this->setLocalization('Admins in group'), 'checked' => true]];
        if (empty($this->app['reseller'])) {
            $return[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        $return[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $return;
    }
    public function admins_groups_permissions()
    {
        $gid = !empty($this->data['id']) ? $this->data['id'] : false;
        if ($gid === false) {
            return $this->app->redirect('admins-groups');
        }
        $permissionMap = $this->db->getAdminGroupPermissions($gid);
        $permissionMap = $this->getJoinedNameArray($permissionMap, 'controller_name', 'action_name');
        $baseMap = $this->db->getAdminGroupPermissions();
        $baseMap = $this->getJoinedNameArray($baseMap, 'controller_name', 'action_name');
        $permissionMap = \array_map(function ($val) {
            \settype($val['is_ajax'], 'int');
            \settype($val['view_access'], 'int');
            \settype($val['edit_access'], 'int');
            \settype($val['action_access'], 'int');
            return $val;
        }, $this->infliction_array($baseMap, $permissionMap));
        $group_name = $this->db->getAdminGropsList(['select' => 'A_G.*', 'where' => ['A_G.id' => $gid], 'like' => '', 'order' => ['name' => 'ASC'], 'limit' => ['limit' => 1, 'offset' => '']]);
        $this->app['adminGropName'] = $group_name[0]['name'];
        $this->app['adminGropID'] = $this->data['id'];
        $permissionMap = $this->setLocalization($permissionMap, 'description');
        $this->app['permissionMap'] = $permissionMap;
        $this->app['breadcrumbs']->addItem($this->setLocalization('Groups'), $this->app['controller_alias'] . '/admins-groups');
        $this->app['breadcrumbs']->addItem($this->setLocalization('permissions for group administrators ') . ": '{$group_name[0]['name']}'");
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getJoinedNameArray($input = array(), $field1 = '', $field2 = '')
    {
        $output = [];
        foreach ($input as $row) {
            if (\array_key_exists($field1, $row) && \array_key_exists($field2, $row)) {
                $new_key = \trim($row[$field1] . '-' . $row[$field2], '-');
                $output[$new_key] = $row;
            }
        }
        return $output;
    }
    public function resellers_list()
    {
        $attribute = $this->getResellerDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $resellers = [['id' => '-', 'name' => '']];
        $this->app['allResellers'] = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
        $this->app['allow_resellers_ip_ranges'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getResellerDropdownAttribute()
    {
        $return = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Name'), 'checked' => true], ['name' => 'created', 'title' => $this->setLocalization('Created'), 'checked' => true], ['name' => 'modified', 'title' => $this->setLocalization('Modified'), 'checked' => true], ['name' => 'admins_count', 'title' => $this->setLocalization('Admins of reseller'), 'checked' => true], ['name' => 'users_count', 'title' => $this->setLocalization('Users of reseller'), 'checked' => true], ['name' => 'max_users', 'title' => $this->setLocalization('Maximum number of users'), 'checked' => true]];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
            $return[] = ['name' => 'ip_ranges', 'title' => $this->setLocalization('Reseller IP-ranges'), 'checked' => true];
        }
        $return[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $return;
    }
    public function check_admins_login()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['login'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'adm_login';
        $error = $this->setLocalization('Login is already used');
        if (\preg_match('/^[A-Za-z0-9_]+$/i', $this->postData['login'])) {
            $adminsList = $this->db->getAdminsList(['where' => ['login' => $this->postData['login'], 'A.id<>' => $this->postData['id']], 'order' => ['login' => 'ASC']]);
            if ($adminsList) {
                $data['chk_rezult'] = $this->setLocalization('Login is already used');
            } else {
                $data['chk_rezult'] = $this->setLocalization('Login is available');
                $error = '';
            }
        } else {
            $error = $data['chk_rezult'] = $this->setLocalization('Used illegal characters');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_admin()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $item = [$this->postData];
        $error = $this->setLocalization('Failed');
        if (!empty($this->postData['login']) && $this->postData['login'] == 'admin') {
            unset($item[0]['login'], $item[0]['gid']);
            $error = $this->setLocalization('Account "admin" is not editable. You may change only password.');
        }
        if (empty($item[0]['id']) && !empty($item[0]['login']) && !empty($item[0]['gid']) && !empty($item[0]['pass'])) {
            $operation = 'insertAdmin';
        } else {
            $operation = 'updateAdmin';
            $item['id'] = $this->postData['id'];
        }
        $new_pass = false;
        if (empty($item[0]['pass']) || $item[0]['pass'] != $item[0]['re_pass']) {
            unset($item[0]['pass']);
        } else {
            $new_pass = $item[0]['pass'];
            $item[0]['pass'] = \md5($item[0]['pass']);
        }
        unset($item[0]['id'], $item[0]['re_pass']);
        $result = $need_authorization = false;
        if ((!empty($item[0]['login']) && \preg_match('/^[A-Za-z0-9_]+$/i', $item[0]['login']) || $operation != 'insertAdmin') && !empty($item[0])) {
            if (!empty($item[0]['login']) && $item[0]['login'] != $this->admin->getUsername() && !empty($item['id']) && $item['id'] == $this->admin->getId()) {
                $data['msg'] = $error = $this->setLocalization('You can not change your own login');
            } elseif (($result = \call_user_func_array([$this->db, $operation], [$item])) && \is_numeric($result)) {
                $error = '';
                if ($operation == 'updateAdmin') {
                    if ($new_pass && $item['id'] == $this->admin->getId()) {
                        if (\Ministra\Lib\Admin::checkAuthorization($this->admin->getUsername(), $new_pass)) {
                            $data['msg'] = $this->setLocalization('Your password has been changed');
                        } else {
                            $data['msg'] = $error = $this->setLocalization('Need authorization');
                            $need_authorization = true;
                        }
                    }
                    $data = \array_merge_recursive($data, $this->admins_list_json(true));
                    $data['id'] = $item['id'];
                    $data['action'] = 'updateTableRow';
                    $data['msg'] = $this->setLocalization('Changed');
                } else {
                    $data['msg'] = $this->setLocalization('Added');
                }
                $this->cleanSideBars();
            } elseif (!empty($this->postData['login']) && $this->postData['login'] == 'admin') {
                $data['msg'] = $error;
            } else {
                $data['nothing_to_do'] = true;
            }
        } else {
            $data['msg'] = $error = $this->setLocalization('Not all required fields are filled');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : ($need_authorization ? 403 : 500), ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function admins_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        if (!$local_uses) {
            $response['action'] = 'setAdminsModal';
        }
        $filds_for_select = $this->getAdminsFields();
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'A_G.`id` as `gid`';
            if (empty($this->app['reseller'])) {
                $query_param['select'][] = 'R.`id` as `reseller_id`';
            }
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($param['id'])) {
            $query_param['where']['A.`id`'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getAdminsTotalRows();
        $response['recordsFiltered'] = $this->db->getAdminsTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getAdminsList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getAdminsFields()
    {
        $return = ['id' => 'A.`id` as `id`', 'login' => 'A.`login` as `login`', 'group_name' => 'A_G.`name` as `group_name`', 'gid' => 'A_G.`id` as `gid`'];
        if (empty($this->app['reseller'])) {
            $return['reseller_id'] = 'R.`id` as `reseller_id`';
            $return['reseller_name'] = 'R.`name` as `reseller_name`';
        }
        return $return;
    }
    private function cleanSideBars()
    {
        foreach (\glob($this->baseDir . '/resources/cache/sidebar/*bar') as $file) {
            if (\is_file($file)) {
                \unlink($file);
            }
        }
    }
    public function remove_admin()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = '';
        $result = $this->db->deleteAdmin(['id' => $this->postData['id']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $this->cleanSideBars();
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_admins_group_name()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['name'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'adm_name';
        $error = $this->setLocalization('Group name is already used');
        if ($this->db->getAdminGropsList(['select' => ['A_G.*'], 'where' => ['A_G.name' => $this->postData['name'], 'A_G.id<>' => $this->postData['id']], 'order' => ['A_G.name' => 'ASC']])) {
            $data['chk_rezult'] = $this->setLocalization('Group name is already used');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Group name is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_admins_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $item = [$this->postData];
        $error = $this->setLocalization('error');
        if (empty($this->postData['id'])) {
            $operation = 'insertAdminsGroup';
        } else {
            $operation = 'updateAdminsGroup';
            $item['id'] = $this->postData['id'];
        }
        unset($item[0]['id']);
        $result = \call_user_func_array([$this->db, $operation], [$item]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            if ($operation == 'updateAdminsGroup') {
                $data = \array_merge_recursive($data, $this->admins_groups_list_json(true));
                $data['id'] = $item['id'];
                $data['action'] = 'updateTableRow';
                $data['msg'] = $this->setLocalization('Changed');
            } else {
                $data['msg'] = $this->setLocalization('Added');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function admins_groups_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        if (!$local_uses) {
            $response['action'] = 'setAdminsGroupsModal';
        }
        $filds_for_select = $this->getAdminGroupsFields();
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } elseif (empty($this->app['reseller'])) {
            $query_param['select'][] = 'R.`id` as `reseller_id`';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($param['id'])) {
            $query_param['where']['A_G.`id`'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getAdminGropsTotalRows();
        $response['recordsFiltered'] = $this->db->getAdminGropsTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (empty($this->app['reseller'])) {
            $response['data'] = \array_map(function ($row) {
                if (empty($row['reseller_name'])) {
                    $row['reseller_name'] = '';
                }
                if (empty($row['reseller_id'])) {
                    $row['reseller_id'] = '-';
                }
                $row['RowOrder'] = 'dTRow_' . $row['id'];
                return $row;
            }, $this->db->getAdminGropsList($query_param));
        } else {
            $response['data'] = $this->db->getAdminGropsList($query_param);
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getAdminGroupsFields()
    {
        $return = ['id' => 'A_G.`id` as `id`', 'name' => 'A_G.`name` as `name`', 'admin_count' => 'COUNT(A.id) as `admin_count`'];
        if (empty($this->app['reseller'])) {
            $return['reseller_id'] = 'R.`id` as `reseller_id`';
            $return['reseller_name'] = 'R.`name` as `reseller_name`';
        }
        return $return;
    }
    public function remove_admins_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Error');
        $admin_count = $this->db->getAdminsTotalRows(['gid' => $data['id']]);
        if (empty($admin_count)) {
            $this->db->deleteAdminsGroup(['id' => $this->postData['id']]);
            $this->db->deleteAdminGroupPermissions($this->postData['id']);
            $error = '';
        } else {
            $error = $data['msg'] = $this->setLocalization('{admin_count} administrators to be moved to another group before deleting', '', false, ['{admin_count}' => $admin_count]);
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_admins_group_permissions()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['data'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $postData = \json_decode($this->postData['data'], true);
        $data = [];
        $data['action'] = 'managePermissions';
        $data['msg'] = $this->setLocalization('Failed');
        $error = 'Ошибка';
        $adminGropID = $postData['adminGropID'];
        unset($postData['adminGropID']);
        $baseMap = $this->db->getAdminGroupPermissions();
        $baseMap = $this->getJoinedNameArray($baseMap, 'controller_name', 'action_name');
        $baseMap = \array_map(function ($val) {
            unset($val['id']);
            return $val;
        }, $baseMap);
        $other = [];
        foreach ($postData as $controller => $row) {
            foreach ($row as $action => $permissions) {
                $baseKey = \trim($controller . '-' . ($action != 'index' ? $action : ''), '-');
                if (\array_key_exists($baseKey, $baseMap)) {
                    $baseMap[$baseKey]['view_access'] = $permissions['view_access'];
                    $baseMap[$baseKey]['edit_access'] = $permissions['edit_access'];
                    $baseMap[$baseKey]['action_access'] = $permissions['action_access'];
                    $baseMap[$baseKey]['group_id'] = $adminGropID;
                } else {
                    $other[] = $baseKey;
                }
            }
        }
        $this->db->deleteAdminGroupPermissions($adminGropID);
        if ($this->db->setAdminGroupPermissions(\array_values($baseMap))) {
            $error = '';
            $data['msg'] = $this->setLocalization('Saved');
            $this->cleanSideBars();
        }
        if (!empty($other)) {
            $error = $this->setLocalization('Error');
            $data['msg'] = $this->setLocalization('Permissions was not set') . ': ' . \implode(', ', $other);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function resellers_check_name()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['name'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'reseller_name';
        $error = $this->setLocalization('That name is already in use');
        $where = ['R.name' => $this->postData['name']];
        if (!empty($this->postData['id'])) {
            $where['R.id<>'] = $this->postData['id'];
        }
        $result = $this->db->getResellersList(['select' => ['R.*'], 'where' => $where, 'order' => ['R.name' => 'ASC']], 'COUNT');
        if ((int) $result) {
            $data['chk_rezult'] = $this->setLocalization('That name is already in use');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Name is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function resellers_save()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        if (\array_key_exists('ip_ranges', $this->postData)) {
            $ip_ranges = $this->postData['ip_ranges'];
            unset($this->postData['ip_ranges']);
        } else {
            $ip_ranges = '';
        }
        $item = [$this->postData];
        $error = $this->setLocalization('Failed');
        if (empty($this->postData['id'])) {
            $operation = 'insertReseller';
        } else {
            $operation = 'updateReseller';
            $item['id'] = $this->postData['id'];
        }
        unset($item[0]['id']);
        $result = \call_user_func_array([$this->db, $operation], [$item]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
                $reseller_id = $operation == 'updateReseller' ? $item['id'] : $result;
                if (empty($ip_ranges)) {
                    $this->db->deleteResellerIPRange(['reseller_id' => $reseller_id]);
                } else {
                    $prepared = $this->prepareIPRange($ip_ranges);
                    \array_walk($prepared, function (&$row) use($reseller_id) {
                        $row['reseller_id'] = $reseller_id;
                        return $row;
                    });
                    $this->db->updateResellerIPRange($prepared);
                }
            }
            if ($operation == 'updateReseller') {
                $data = \array_merge_recursive($data, $this->resellers_list_json(true));
                $data['id'] = $item['id'];
                $data['action'] = 'updateTableRow';
                $data['msg'] = $this->setLocalization('Changed');
            } else {
                $data['msg'] = $this->setLocalization('Added');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function prepareIPRange($ip_range)
    {
        $return = [];
        if (\is_array($ip_range)) {
            foreach ($ip_range as $range) {
                $return[] = \call_user_func_array([$this, 'rangeConvert'], [$range]);
            }
        } else {
            return $this->prepareIPRange(\explode(',', $ip_range));
        }
        return $return;
    }
    public function resellers_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        if (!$local_uses) {
            $response['action'] = 'setResellerModal';
        }
        $filds_for_select = $this->getResellerFields();
        $error = 'Error';
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (!empty($param['id']) && \is_numeric($param['id'])) {
            $query_param['where']['R.`id`'] = $param['id'];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = '(select count(*) from administrators as A where A.reseller_id = R.id) as `admins_count`';
            $query_param['select'][] = '(select count(*) from users as U where U.reseller_id = R.id) as `users_count`';
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
                $query_param['select'][] = "(SELECT GROUP_CONCAT(ip_range SEPARATOR ', ') FROM resellers_ips_ranges as R_I_R WHERE R_I_R.reseller_id = R.id) as `ip_ranges`";
            }
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($query_param['like'][$filds_for_select['admins_count']])) {
            unset($query_param['like'][$filds_for_select['admins_count']]);
        }
        if (!empty($query_param['like'][$filds_for_select['users_count']])) {
            unset($query_param['like'][$filds_for_select['users_count']]);
        }
        $response['recordsTotal'] = $this->db->getResellersTotalRows();
        $response['recordsFiltered'] = $this->db->getResellersTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        foreach (['users_count', 'admins_count', 'ip_ranges'] as $delete_field) {
            if (($search = \array_search($delete_field, $query_param['select'])) !== false) {
                unset($query_param['select'][$search]);
            }
        }
        if (empty($param['id']) && empty($query_param['like']) && $query_param['limit']['offset'] == '0') {
            $response['data'][] = ['id' => '-', 'name' => $this->setLocalization('(No reseller)'), 'created' => null, 'modified' => null, 'admins_count' => $this->db->getResellerMember('administrators', null), 'users_count' => $this->db->getResellerMember('users', null), 'max_users' => '&#8734;', 'ip_ranges' => ''];
        }
        if (!empty($query_param['order'][$filds_for_select['admins_count']])) {
            $tmp = $query_param['order'][$filds_for_select['admins_count']];
            $query_param['order'] = ['admins_count' => $tmp];
        }
        if (!empty($query_param['order'][$filds_for_select['users_count']])) {
            $tmp = $query_param['order'][$filds_for_select['users_count']];
            $query_param['order'] = ['users_count' => $tmp];
        }
        $response['data'] = \array_merge($response['data'], $this->db->getResellersList($query_param));
        $response['data'] = \array_map(function ($row) {
            $row['created'] = $row['created'] ? (int) \strtotime($row['created']) : '';
            $row['modified'] = $row['modified'] ? (int) \strtotime($row['modified']) : '';
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getResellerFields()
    {
        $return = ['id' => 'R.`id` as `id`', 'name' => 'R.`name` as `name`', 'created' => 'CAST(R.`created` as CHAR) as `created`', 'modified' => 'CAST(R.`modified` as CHAR) as `modified`', 'admins_count' => '(select count(*) from administrators as A where A.reseller_id = R.id) as `admins_count`', 'users_count' => '(select count(*) from users as U where U.reseller_id = R.id) as `users_count`', 'max_users' => 'R.`max_users` as `max_users`'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
            $return['use_ip_ranges'] = 'R.`use_ip_ranges` as `use_ip_ranges`';
            $return['ip_ranges'] = "(SELECT GROUP_CONCAT(ip_range SEPARATOR ', ') FROM resellers_ips_ranges as R_I_R WHERE R_I_R.reseller_id = R.id) as `ip_ranges`";
        }
        return $return;
    }
    public function resellers_delete()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = '';
        $count_members = $this->db->getResellerMember('administrators', $this->postData['id']) + $this->db->getResellerMember('users', $this->postData['id']);
        if (empty($count_members)) {
            $this->db->deleteReseller(['id' => $this->postData['id']]);
            $this->db->deleteResellerIPRange(['reseller_id' => $this->postData['id']]);
            $data['msg'] = $this->setLocalization('Deleted');
        } else {
            $error = $data['msg'] = $this->setLocalization('Found members of this reseller. Deleting not possible.');
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_ip_range_intersection()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $error = $this->setLocalization('Failed');
        $ip_ranges = \array_key_exists('ip_ranges', $this->postData) ? $this->postData['ip_ranges'] : '';
        $reseller_id = \array_key_exists('reseller_id', $this->postData) ? $this->postData['reseller_id'] : null;
        if (!empty($ip_ranges)) {
            $ip_ranges = \str_replace(' ', '', $ip_ranges);
            $ip_ranges = \explode(',', $ip_ranges);
            $error = [];
            foreach ($ip_ranges as $range) {
                $error = \array_merge($error, $this->validate($range));
                $private_net = $this->check_private_range($range);
                if ($private_net === false && $this->check_intersect_with_db($range, $reseller_id)) {
                    $error[] = $this->setLocalization('Range "{rng}" have intersect with exists range.', null, null, ['{rng}' => $range]);
                } elseif ($private_net === null) {
                    $error[] = $this->setLocalization('Range "{rng}" is subnet of private network?', null, null, ['{rng}' => $range]);
                }
            }
        }
        $error = \trim(\implode(' ', $error));
        if (empty($error)) {
            $data['msg'] = $this->setLocalization('All ip-ranges is valid.');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    private function validate($range)
    {
        $delimeter = \strpos($range, '/') !== false ? '/' : '-';
        $errors = [];
        $exploded = \explode($delimeter, $range);
        $exploded = \array_map('trim', $exploded);
        $begin_range = $exploded[0];
        $end_range = $exploded[\count($exploded) - 1];
        $begin_valid = (bool) \filter_var($begin_range, FILTER_VALIDATE_IP);
        if (!$begin_valid) {
            $errors[] = $this->setLocalization('The begin of the range ({rng}) is not a valid IP-address.', null, null, ['{rng}' => $range]);
        }
        if (!empty($end_range)) {
            if ($delimeter == '-') {
                $end_valid = (bool) \filter_var($end_range, FILTER_VALIDATE_IP);
            } elseif ($end_range > 32) {
                $errors[] = $this->setLocalization('({rng}) is not a valid CIDR format.', null, null, ['{rng}' => $range]);
                $end_valid = false;
            } else {
                $end_valid = $begin_valid;
            }
        } else {
            $end_valid = $begin_valid;
        }
        if (!$end_valid) {
            $errors[] = $this->setLocalization('The end of the range ({rng}) is not a valid IP-address.', null, null, ['{rng}' => $range]);
        }
        return $errors;
    }
    private function check_private_range($range)
    {
        $private_network_ranges = [['calculated_range_begin' => 167772160, 'calculated_range_end' => 184549375], ['calculated_range_begin' => 2886729728, 'calculated_range_end' => 2887778303], ['calculated_range_begin' => 3232235520, 'calculated_range_end' => 3232301055]];
        $errors = [];
        $calculated = $this->rangeConvert($range);
        $begin_is_private = $end_is_private = false;
        foreach ($private_network_ranges as $network_range) {
            $begin_is_private = $network_range['calculated_range_begin'] <= $calculated['calculated_range_begin'] && $calculated['calculated_range_begin'] <= $network_range['calculated_range_end'];
            $end_is_private = $network_range['calculated_range_begin'] <= $calculated['calculated_range_end'] && $calculated['calculated_range_end'] <= $network_range['calculated_range_end'];
            $more_then_private = $calculated['calculated_range_begin'] <= $network_range['calculated_range_begin'] && $network_range['calculated_range_end'] <= $calculated['calculated_range_end'];
            if ($begin_is_private || $end_is_private || $more_then_private) {
                return $begin_is_private === $end_is_private && !$more_then_private ? true : null;
            }
        }
        return false;
    }
    private function rangeConvert($range)
    {
        $return = ['ip_range' => $range];
        if (\strpos($range, '/') !== false) {
            $range = $this->cidrToRangeConvert($range);
        } else {
            $range = \explode('-', $range);
        }
        if (\count($range) != 2) {
            $range[1] = $range[0];
        }
        $range = \array_map('trim', $range);
        $range = \array_map('ip2long', $range);
        $return['calculated_range_begin'] = $range[0];
        $return['calculated_range_end'] = $range[1];
        return $return;
    }
    private function cidrToRangeConvert($cidr)
    {
        $range = [];
        $cidr = \explode('/', $cidr);
        if ((int) $cidr[1] > 32) {
            $cidr[1] = 0;
        }
        $range[0] = \long2ip(\ip2long($cidr[0]) & -1 << 32 - (int) $cidr[1]);
        $range[1] = \long2ip(\ip2long($range[0]) + \pow(2, 32 - (int) $cidr[1]) - 1);
        return $range;
    }
    private function check_intersect_with_db($range, $reseller_id = null)
    {
        $error = [];
        $converted = $this->rangeConvert($range);
        $cond = ["calculated_range_begin <= {$converted['calculated_range_begin']} AND {$converted['calculated_range_begin']} <= calculated_range_end AND 1=" => 1, "calculated_range_begin <= {$converted['calculated_range_end']} AND {$converted['calculated_range_end']} <= calculated_range_end AND 1=" => 1, "{$converted['calculated_range_begin']} <= calculated_range_begin AND calculated_range_end <= {$converted['calculated_range_end']} AND 1=" => 1];
        $intersect_ranges = $this->db->getResellerIPRange(['where' => $cond], null, ' OR ');
        if (!empty($intersect_ranges) && !empty($reseller_id)) {
            $intersect_ranges = \array_filter($intersect_ranges, function ($row) use($reseller_id) {
                return $row['reseller_id'] !== $reseller_id;
            });
        }
        return !empty($intersect_ranges);
    }
    public function move_users_to_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $source_id = $this->postData['source_id'] !== '-' ? $this->postData['source_id'] : null;
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = '';
        $count_members = $this->db->getResellerMember('users', $source_id);
        if (!empty($count_members) && $source_id != $target_id) {
            $this->db->updateResellerMember('users', $source_id, $target_id);
            $data['msg'] = $this->setLocalization('Moved');
        } else {
            $error = $data['msg'] = $this->setLocalization('Not found members for moving. Nothing to do');
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_admin_to_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $admin_id = $this->postData['id'];
        $source_id = $this->postData['source_id'] !== '-' ? $this->postData['source_id'] : null;
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = '';
        if (!empty($target_id)) {
            $count_reseller = $this->db->getResellersList(['select' => ['*'], 'where' => ['id' => $target_id], 'like' => [], 'order' => []], true);
        } else {
            $count_reseller = 1;
        }
        if (!empty($count_reseller) && $source_id !== $target_id) {
            $this->db->updateResellerMemberByID('administrators', $admin_id, $target_id);
            $data['nothing_to_do'] = true;
            $data = \array_merge_recursive($data, $this->admins_list_json(true));
            $this->cleanSideBars();
        } else {
            if (empty($count_reseller)) {
                $error = $data['msg'] = $this->setLocalization('Not found reseller for moving');
            } else {
                $error = $data['msg'] = $this->setLocalization('Nothing to do');
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_admin_group_to_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $admin_id = $this->postData['id'];
        $source_id = $this->postData['source_id'] !== '-' ? $this->postData['source_id'] : null;
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = '';
        if (!empty($target_id)) {
            $count_reseller = $this->db->getResellersList(['select' => ['*'], 'where' => ['id' => $target_id], 'like' => [], 'order' => []], true);
        } else {
            $count_reseller = 1;
        }
        if (!empty($count_reseller) && $source_id !== $target_id) {
            $this->db->updateResellerMemberByID('admin_groups', $admin_id, $target_id);
            $data = \array_merge_recursive($data, $this->admins_groups_list_json(true));
            $data['msg'] = $this->setLocalization('Moved');
            $data['id'] = $admin_id;
            $data['action'] = 'updateTableRow';
            $this->cleanSideBars();
        } else {
            $error = $data['msg'] = empty($count_reseller) ? $this->setLocalization('Not found reseller for moving') : $this->setLocalization('Nothing to do');
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_admin_to_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $admin_id = $this->postData['id'];
        $source_id = $this->postData['source_id'] !== '-' ? $this->postData['source_id'] : null;
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = '';
        if (!empty($target_id)) {
            $count_admins = $this->db->getAdminGropsTotalRows(['A_G.id' => $target_id]);
        } else {
            $count_admins = 1;
        }
        if (!empty($count_admins) && $source_id !== $target_id) {
            $result = $this->db->updateAdmin(['id' => $admin_id, 0 => ['gid' => $target_id]]);
            if (\is_numeric($result)) {
                $error = '';
                $data['nothing_to_do'] = true;
                $data = \array_merge_recursive($data, $this->admins_list_json(true));
                $this->cleanSideBars();
            }
        } else {
            if (empty($count_admins)) {
                $error = $data['msg'] = $this->setLocalization('Not found admin-group for moving');
            } else {
                $error = $data['msg'] = $this->setLocalization('Nothing to do');
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_all_admin_to_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $source_id = $this->postData['source_id'] !== '-' ? $this->postData['source_id'] : null;
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = '';
        if (!empty($target_id)) {
            $count_admins = $this->db->getAdminGropsTotalRows(['A_G.id' => $target_id]);
        } else {
            $count_admins = 1;
        }
        if (!empty($count_admins) && $source_id !== $target_id) {
            $result = $this->db->updateAdmin(['gid' => $source_id, 0 => ['gid' => $target_id]]);
            if (\is_numeric($result)) {
                $error = '';
                $data['msg'] = $this->setLocalization('Moved');
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                $this->cleanSideBars();
            }
        } else {
            if (empty($count_admins)) {
                $error = $data['msg'] = $this->setLocalization('Not found admin-group for moving');
            } else {
                $error = $data['msg'] = $this->setLocalization('Nothing to do');
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
