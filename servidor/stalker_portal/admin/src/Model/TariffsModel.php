<?php

namespace Ministra\Admin\Model;

class TariffsModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getTotalRowsTariffsList($where = array(), $like = array())
    {
        $this->mysqlInstance->count()->from('services_package')->where($where);
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getTariffsList($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('services_package');
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
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : null);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getUserCountForPackage($package_id)
    {
        return $this->mysqlInstance->query("SELECT SUM(\n                                        (SELECT COUNT(*)\n                                        FROM users\n                                        WHERE (tariff_plan_id = T_P.id) || \n                                              IF(T_P.user_default, tariff_plan_id = 0, 0))\n                                              ) AS user_cont\n                                    FROM\n                                        package_in_plan AS P_P\n                                        LEFT JOIN tariff_plan AS T_P ON P_P.plan_id = T_P.id\n                                    WHERE\n                                        optional = 0 AND package_id = {$package_id}")->first('user_cont');
    }
    public function getUserCountForSubscription($package_id)
    {
        return $this->mysqlInstance->from('user_package_subscription')->where(['package_id' => $package_id])->count()->get()->counter();
    }
    public function deletePackageById($package_id)
    {
        $this->mysqlInstance->delete('package_in_plan', ['package_id' => $package_id]);
        return $this->mysqlInstance->delete('services_package', ['id' => $package_id])->total_rows();
    }
    public function deleteServicesById($package_id)
    {
        return $this->mysqlInstance->delete('service_in_package', ['package_id' => $package_id])->total_rows();
    }
    public function getPackageById($params, $order = null)
    {
        $where = [];
        if (\is_numeric($params)) {
            $where = ['package_id' => $params];
        } elseif (\is_array($params)) {
            $where = $params;
        }
        $this->mysqlInstance->from('service_in_package')->where($where);
        if (!empty($order)) {
            $this->mysqlInstance->orderby($order);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function updatePackage($param, $id)
    {
        return $this->mysqlInstance->update('services_package', $param, ['id' => $id])->total_rows() || 1;
    }
    public function insertPackage($param)
    {
        return $this->mysqlInstance->insert('services_package', $param)->insert_id();
    }
    public function insertServices($param)
    {
        return $this->mysqlInstance->insert('service_in_package', $param)->insert_id();
    }
    public function getTariffPlansList($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('tariff_plan');
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
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : null);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTotalRowsTariffPlansList($where = array(), $like = array())
    {
        $this->mysqlInstance->count()->from('tariff_plan')->where($where);
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function deletePlanById($id)
    {
        return $this->mysqlInstance->delete('package_in_plan', ['plan_id' => $id])->total_rows();
    }
    public function deleteTariffById($id)
    {
        return $this->mysqlInstance->delete('tariff_plan', ['id' => $id])->total_rows();
    }
    public function getOptionalForPlan($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('package_in_plan')->join('services_package', 'package_in_plan.package_id', 'services_package.id', 'LEFT');
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
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : null);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function updatePlan($param, $id)
    {
        return $this->mysqlInstance->update('tariff_plan', $param, ['id' => $id])->total_rows();
    }
    public function insertPlan($param)
    {
        return $this->mysqlInstance->insert('tariff_plan', $param)->insert_id();
    }
    public function insertPackageInPlan($param)
    {
        return $this->mysqlInstance->insert('package_in_plan', $param)->insert_id();
    }
    public function deletePackageInPlanById($id)
    {
        return $this->mysqlInstance->delete('package_in_plan', ['plan_id' => $id])->total_rows();
    }
    public function getTariffsNotifications($tariff_id, $noti_id = false)
    {
        $where = ['tariff_id' => $tariff_id];
        if ($noti_id !== false) {
            $where['id'] = $noti_id;
        }
        return $this->mysqlInstance->from('tariffs_notifications')->where($where)->get()->all();
    }
    public function deleteTariffsNotifications($tariff_id, $noti_id = false)
    {
        $where = ['tariff_id' => $tariff_id];
        if ($noti_id !== false) {
            $where['id'] = $noti_id;
        }
        return $this->mysqlInstance->delete('tariffs_notifications', $where)->total_rows();
    }
    public function insertTariffsNotifications($param)
    {
        return $this->mysqlInstance->insert('tariffs_notifications', $param)->insert_id();
    }
    public function getUserDefaultPlan()
    {
        return $this->mysqlInstance->from('tariff_plan')->where(['user_default' => 1])->get()->first('id');
    }
    public function getSubscribeLogList($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['U.reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->from('package_subscribe_log as P_S_L')->join('users as U', 'P_S_L.user_id', 'U.id', 'LEFT')->join('administrators as A', 'P_S_L.initiator_id', 'A.id', 'LEFT')->join('services_package as S_P', 'P_S_L.package_id', 'S_P.id', 'LEFT')->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : null);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTotalRowsSubscribeLogList($where = array(), $like = array(), $user_id = false)
    {
        if ($user_id !== false && !\array_key_exists('user_id', $where)) {
            $where['user_id'] = $user_id;
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['U.reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->count()->from('package_subscribe_log as P_S_L')->join('services_package as S_P', 'P_S_L.package_id', 'S_P.id', 'LEFT')->join('users as U', 'P_S_L.user_id', 'U.id', 'LEFT')->join('administrators as A', 'P_S_L.initiator_id', 'A.id', 'LEFT')->where($where);
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getUser($param)
    {
        return $this->mysqlInstance->from('users')->where($param, 'OR')->get()->first();
    }
    public function getArchiveStatus($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('itv');
        if (\array_key_exists('where', $param)) {
            $this->mysqlInstance->where($param['where']);
        }
        if (\array_key_exists('like', $param)) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (\array_key_exists('order', $param)) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['in'])) {
            \call_user_func_array([$this->mysqlInstance, 'in'], $param['in']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : null);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getStorages()
    {
        return $this->mysqlInstance->from('storages')->where(['status' => 1, 'for_records' => 1, '`dvr_type` IS NOT' => null, '`dvr_type` <>' => ''])->where([' stream_server_type' => null, 'stream_server_type' => ''], 'OR ')->get()->all();
    }
    public function updateTVChannel($param, $where)
    {
        if (empty($where)) {
            $where = ['id' => null];
        }
        if (\is_numeric($where)) {
            $where = ['id' => $where];
        }
        return $this->mysqlInstance->update('itv', $param, $where)->total_rows();
    }
    public function totalItemsByName($table, $name, $id)
    {
        $where = ['name' => $name];
        if ($id) {
            $where['id<>'] = $id;
        }
        return $this->mysqlInstance->count()->from($table)->where($where)->get()->counter();
    }
}
