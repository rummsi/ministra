<?php

namespace Ministra\Admin\Model;

use Ministra\Lib\RemotePvr;
use Ministra\Lib\StbGroup;
class UsersModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getTotalRowsUresList($where = array(), $like = array(), $in = array())
    {
        if (!empty($this->reseller_id)) {
            $where['reseller_id'] = $this->reseller_id;
        }
        $this->mysqlInstance->count()->from('users')->where($where);
        if (!empty($in)) {
            list($field, $data) = \each($in);
            $this->mysqlInstance->in($field, $data);
        }
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getUsersList($param, $report = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('users');
        if (!empty($this->reseller_id)) {
            $param['where']['users.reseller_id'] = $this->reseller_id;
        }
        if (!$report) {
            $this->mysqlInstance->join('tariff_plan', 'users.tariff_plan_id', 'tariff_plan.id', 'LEFT');
            $this->mysqlInstance->join('reseller', 'users.reseller_id', 'reseller.id', 'LEFT');
            $this->mysqlInstance->join('stb_in_group', 'stb_in_group.uid', 'users.id', 'LEFT');
            $this->mysqlInstance->join('stb_groups', 'stb_groups.id', 'stb_in_group.stb_group_id', 'LEFT');
        } else {
            $this->mysqlInstance->join('(SELECT @rank := 0) r', '1', '1', 'INNER');
        }
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['in'])) {
            list($field, $data) = \each($param['in']);
            $this->mysqlInstance->in($field, $data);
        }
        $order = isset($param['order']) ? \array_merge($param['order'], ['users.id' => 'asc']) : ['users.id' => 'asc'];
        $this->mysqlInstance->orderby($order);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getUsersByIds(array $ids)
    {
        return $this->mysqlInstance->from('users')->in('id', $ids)->get()->all();
    }
    public function toggleUserStatus($id, $status)
    {
        $where = ['id' => $id];
        if (!empty($this->reseller_id)) {
            $where['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->update('users', ['status' => $status, 'last_change_status' => 'NOW()'], $where)->total_rows();
    }
    public function toggleStatus(array $ids, $status)
    {
        $where = empty($this->reseller_id) ? [] : ['reseller_id' => $this->reseller_id];
        return $this->mysqlInstance->in('id', $ids)->update('users', ['status' => $status, 'last_change_status' => 'NOW()'], $where)->total_rows();
    }
    public function deleteUserById($id)
    {
        $where = ['id' => $id];
        if (!empty($this->reseller_id)) {
            $where['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->delete('users', $where)->total_rows();
    }
    public function deleteUsersById($ids)
    {
        return $this->mysqlInstance->in('id', $ids)->delete('users', [])->total_rows();
    }
    public function updateUserById($data, $id)
    {
        $where = ['id' => $id];
        if (!empty($this->reseller_id)) {
            $where['reseller_id'] = $this->reseller_id;
        }
        if (\array_key_exists('last_change_status', $data) && empty($data['last_change_status'])) {
            $data['last_change_status'] = 'NOW()';
        }
        if (\array_key_exists('id', $data) && $data['id'] == $id) {
            unset($data['id']);
        }
        if (\array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }
        return $this->mysqlInstance->update('users', $data, $where)->total_rows();
    }
    public function insertUsers($data)
    {
        $data['created'] = 'NOW()';
        if (!empty($this->reseller_id)) {
            $data['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->insert('users', $data)->insert_id();
    }
    public function deleteUserFavItv($id)
    {
        return $this->mysqlInstance->delete('fav_itv', ['uid' => $id])->total_rows();
    }
    public function deleteUsersItvFavs($ids)
    {
        return $this->mysqlInstance->in('id', $ids)->delete('fav_itv', [])->total_rows();
    }
    public function getUserFavItv($id)
    {
        return $this->mysqlInstance->from('fav_itv')->where(['uid' => $id])->get()->first('fav_ch');
    }
    public function updateUserFavItv($data, $id)
    {
        return $this->mysqlInstance->update('fav_itv', $data, ['uid' => $id])->total_rows();
    }
    public function deleteUserFavVclub($id)
    {
        return $this->mysqlInstance->delete('fav_vclub', ['uid' => $id])->total_rows();
    }
    public function deleteUsersVclubFavs($ids)
    {
        return $this->mysqlInstance->in('id', $ids)->delete('fav_vclub', [])->total_rows();
    }
    public function deleteUserFavMedia($id)
    {
        return $this->mysqlInstance->delete('media_favorites', ['uid' => $id])->total_rows();
    }
    public function deleteUsersMediaFavs($ids)
    {
        return $this->mysqlInstance->in('id', $ids)->delete('media_favorites', [])->total_rows();
    }
    public function deleteUserTokens($id)
    {
        return $this->mysqlInstance->delete('access_tokens', ['uid' => $id])->total_rows();
    }
    public function deleteTokensForUsers($ids)
    {
        return $this->mysqlInstance->in('id', $ids)->delete('access_tokens', [])->total_rows();
    }
    public function getAllTariffPlans()
    {
        return $this->mysqlInstance->select('id, name, user_default, days_to_expires')->from('tariff_plan')->orderby('name')->get()->all();
    }
    public function getSubChannelsDB($id)
    {
        return $this->mysqlInstance->from('itv_subscription')->where(['uid' => $id])->get()->first('sub_ch');
    }
    public function insertSubChannelsDB($params)
    {
        return $this->mysqlInstance->insert('itv_subscription', $params)->total_rows();
    }
    public function updateSubChannelsDB($params, $id)
    {
        return $this->mysqlInstance->update('itv_subscription', $params, ['uid' => $id])->total_rows();
    }
    public function getCostSubChannelsDB($channels = array())
    {
        return empty($channels) ? 0 : $this->mysqlInstance->select('SUM(cost) as total_cost')->from('itv')->in('id', $channels)->get()->first('total_cost');
    }
    public function getTotalRowsConsoleGroup($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getConsoleGroup($params, true);
    }
    public function getConsoleGroup($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('stb_groups as Sg')->join('reseller as R', 'Sg.reseller_id', 'R.id', 'LEFT');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getConsoleGroupList($param = array())
    {
        if (!empty($this->reseller_id)) {
            $param['where']['reseller_id'] = $this->reseller_id;
        }
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('stb_in_group')->join('stb_groups', 'stb_in_group.stb_group_id', 'stb_groups.id', 'LEFT');
        if (\array_key_exists('where', $param)) {
            $this->mysqlInstance->where($param['where']);
        }
        if (\array_key_exists('like', $param)) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (\array_key_exists('order', $param)) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTotalRowsConsoleGroupList($where = array(), $like = array())
    {
        $this->mysqlInstance->count()->from('stb_in_group')->where($where);
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function insertConsoleGroup($param)
    {
        if (!empty($this->reseller_id)) {
            $param['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->insert('stb_groups', $param)->insert_id();
    }
    public function updateConsoleGroup($data, $param)
    {
        if (!empty($this->reseller_id)) {
            $param['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->update('stb_groups', $data, $param)->total_rows();
    }
    public function deleteConsoleGroup($param)
    {
        if (!empty($this->reseller_id)) {
            $param['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->delete('stb_groups', $param)->total_rows();
    }
    public function checkLogin($params)
    {
        if (!\is_array($params)) {
            $params = ['login' => $params];
        }
        return $this->mysqlInstance->count()->from('users')->where($params)->get()->counter();
    }
    public function checkConsoleName($params = array())
    {
        return $this->mysqlInstance->count()->from('stb_groups')->where($params)->get()->counter();
    }
    public function deleteConsoleItem($param)
    {
        return $this->mysqlInstance->delete('stb_in_group', $param)->total_rows();
    }
    public function insertConsoleItem($param)
    {
        return $this->mysqlInstance->insert('stb_in_group', $param)->insert_id();
    }
    public function getTotalRowsLogList($where = array(), $like = array())
    {
        $params = ['where' => $where];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getLogList($params, true);
    }
    public function getLogList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('user_log');
        if (!empty($this->reseller_id)) {
            $param['where']['reseller_id'] = $this->reseller_id;
        }
        if (!empty($this->reseller_id) || $counter === false) {
            $this->mysqlInstance->join('users', 'user_log.uid', 'users.id', 'LEFT');
        }
        $this->mysqlInstance->select(['user_log.id as id', 'users.mac as user_mac', 'user_log.type as type']);
        if (\array_key_exists('where', $param)) {
            $this->mysqlInstance->where($param['where']);
        }
        $this->mysqlInstance->where(['CONCAT(`action`, `type`, `param`) <>' => 'stop0']);
        if (\array_key_exists('like', $param)) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (\array_key_exists('order', $param)) {
            $this->mysqlInstance->orderby($param['order']);
        }
        $this->mysqlInstance->orderby('user_log.id', 'DESC');
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : false);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getITV($param, $all = false)
    {
        return $this->mysqlInstance->from('itv')->where($param)->get()->{$all ? 'all' : 'first'}();
    }
    public function getVideo($param)
    {
        return $this->mysqlInstance->from('video')->where($param)->get()->first();
    }
    public function getRecord($table, $param)
    {
        return $this->mysqlInstance->from($table)->where($param)->get()->first();
    }
    public function getTarifPlanByUserID($id)
    {
        $where = ['U.id' => $id];
        if (!empty($this->reseller_id)) {
            $where['reseller_id'] = $this->reseller_id;
        }
        return $this->mysqlInstance->select(['P_P . *', 'S_P.id as services_package_id', 'S_P.`name` as `name`', 'S_P.`type` as `type`', 'S_P.`external_id` as external_id', 'S_P.description as description', 'S_P.service_type as service_type', 'if(P_P.optional = 1, not isnull(U_P_S.id), 1) as `subscribed`'])->from('users as U')->join('tariff_plan as T_P', 'T_P.id', 'if(U.tariff_plan_id <> 0,  U.tariff_plan_id, (select id FROM tariff_plan where user_default = 1))', 'LEFT')->join('package_in_plan as P_P', 'T_P.id', 'P_P.plan_id', 'LEFT')->join('services_package as S_P', 'S_P.id', 'P_P.package_id', 'INNER')->join('user_package_subscription as U_P_S', 'U.id', 'U_P_S.user_id and P_P.package_id = U_P_S.package_id', 'LEFT')->where($where)->orderby('P_P.optional, S_P.external_id')->get()->all();
    }
    public function getPackagesInPlan($id)
    {
        $query = $this->mysqlInstance->select(['package_in_plan.package_id', 'package_in_plan.optional', 'services_package.id', 'services_package.name', 'services_package.description'])->from('package_in_plan')->join('services_package', 'package_in_plan.package_id', 'services_package.id', 'left')->where(['plan_id' => $id]);
        return $query->get()->all();
    }
    public function getOptionalPackagesForUser($userId, array $packageId)
    {
        $query = $this->mysqlInstance->from('user_package_subscription')->where(['user_id' => $userId])->in('user_id', $packageId);
        return $query->get()->all();
    }
    public function getReseller($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('reseller as R');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updateResellerMemberByID($table_name, $id, $target_id)
    {
        return $this->mysqlInstance->update($table_name, ['reseller_id' => $target_id], ['id' => $id])->total_rows();
    }
    public function getFilterSet($params)
    {
        return $this->mysqlInstance->from('filter_set')->where($params)->get()->all();
    }
    public function insertFilterSet($params)
    {
        return $this->mysqlInstance->insert('filter_set', $params)->insert_id();
    }
    public function updateFilterSet($id, $params)
    {
        return $this->mysqlInstance->update('filter_set', $params, ['id' => $id])->total_rows();
    }
    public function getTotalRowsUsersFilters($where = array(), $like = array())
    {
        $params = ['where' => $where];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getUsersFiltersList($params, true);
    }
    public function getUsersFiltersList($param, $counter = false)
    {
        $where = [];
        if (!empty($this->admin_login) && $this->admin_login != 'admin') {
            $where = ['admin_id' => $this->admin_id, 'for_all' => 1];
        }
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('filter_set as F_S')->join('administrators as A', 'F_S.admin_id', 'A.id', 'LEFT')->join('reseller as R', 'A.reseller_id', 'R.id', 'LEFT');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($where)) {
            $this->mysqlInstance->where($where, ' OR ');
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], ' OR ');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function toggleFilterFavorite($id, $status)
    {
        return $this->mysqlInstance->update('filter_set', ['favorites' => $status], ['id' => $id])->total_rows();
    }
    public function deleteFilter($id)
    {
        $where = ['id' => $id];
        if (!empty($this->admin_login) && $this->admin_login != 'admin') {
            $where['for_all = 1 OR admin_id'] = $this->admin_id;
        }
        return $this->mysqlInstance->delete('filter_set', $where)->total_rows();
    }
    public function getTVChannelNames($param)
    {
        return $this->mysqlInstance->from('itv')->like(['name' => "%{$param}%"], ' OR ')->orderby('name')->get()->all('name');
    }
    public function getMovieNames($param)
    {
        return $this->mysqlInstance->from('video')->like(['name' => "%{$param}%"], ' OR ')->orderby('name')->get()->all('name');
    }
    public function getStbFirmwareVersion($param)
    {
        return $this->mysqlInstance->from('users')->like(['version' => "%{$param}%"], ' OR ')->orderby('id', ' DESC ')->get()->all('version');
    }
    public function getTracertStats($id)
    {
        return $this->mysqlInstance->from('diagnostic_info')->where(['uid' => $id])->get()->first();
    }
    public function getSupportInfoByLang($lang)
    {
        return $this->mysqlInstance->from('support_info')->where(['lang' => (string) $lang])->get()->first();
    }
    public function insertSupportInfo($params)
    {
        return $this->mysqlInstance->insert('support_info', $params)->total_rows();
    }
    public function updateSupportInfo($where, $params)
    {
        return $this->mysqlInstance->update('support_info', $params, $where)->total_rows();
    }
    public function getGroupsList()
    {
        return $this->mysqlInstance->from('stb_groups')->get()->all();
    }
    public function removeByIds(array $ids)
    {
        $result = $this->deleteUsersById($ids);
        if ($result) {
            $this->deleteUsersItvFavs($ids);
            $this->deleteUsersVclubFavs($ids);
            $this->deleteUsersMediaFavs($ids);
            $this->deleteTokensForUsers($ids);
            $this->removeUsersRecs($ids);
            $this->removeFromStbGroups($ids);
        }
        return $result;
    }
    private function removeUsersRecs(array $ids)
    {
        foreach ($ids as $id) {
            \Ministra\Lib\RemotePvr::delAllUserRecs($id);
        }
    }
    private function removeFromStbGroups(array $ids)
    {
        $stb_groups = new \Ministra\Lib\StbGroup();
        return $stb_groups->removeMembersByIds($ids);
    }
    public function changeReseller($resellerId, array $ids)
    {
        return $this->mysqlInstance->in('id', $ids)->update('users', ['reseller_id' => $resellerId])->total_rows();
    }
    public function changeTariffPlan($tariffId, array $ids)
    {
        return $this->mysqlInstance->in('id', $ids)->update('users', ['tariff_plan_id' => $tariffId])->total_rows();
    }
}
