<?php

namespace Ministra\Admin\Model;

use Ministra\Admin\Lib\Authentication\User\User;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\s5a2673e9096c740f490ea49e71d32c29;
class BaseMinistraModel
{
    protected $mysqlInstance;
    protected $reseller_id;
    protected $admin_id;
    protected $admin_login;
    protected $admin;
    public function __construct(\Ministra\Admin\Lib\Authentication\User\User $user = null)
    {
        $this->mysqlInstance = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->reseller_id = null;
        $this->admin_id = null;
        $this->admin_login = null;
        $this->admin = $user;
        if ($this->admin) {
            $this->admin_id = $this->admin->getId();
            $this->reseller_id = $this->admin->getResellerId();
            $this->admin_login = $this->admin->getUsername();
        }
    }
    public function setReseller($reseller_id)
    {
        $this->reseller_id = $reseller_id;
    }
    public function setAdmin($admin_id, $admin_login)
    {
        $this->admin_id = $admin_id;
        $this->admin_login = $admin_login;
    }
    public function getTableFields($table_name)
    {
        return $this->mysqlInstance->query("DESCRIBE {$table_name}")->all();
    }
    public function getAllFromTable($table_name, $order = 'name', $groupby = '')
    {
        $this->mysqlInstance->from($table_name)->orderby($order);
        if (!empty($groupby)) {
            $this->mysqlInstance->groupby($groupby);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function existsTable($tablename, $temporary = false)
    {
        if (!$temporary) {
            return $this->mysqlInstance->query("SHOW TABLES LIKE '{$tablename}'")->first();
        }
        try {
            $this->mysqlInstance->query("SELECT count(*) FROM {$tablename}")->first();
            return true;
        } catch (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\s5a2673e9096c740f490ea49e71d32c29 $ex) {
            return false;
        }
    }
    public function getCountUnreadedMsgsByUid($uid)
    {
        return $this->mysqlInstance->query("select\n                                              count(moderators_history.id) as counter\n                                            from moderators_history, moderator_tasks\n                                            where moderators_history.task_id = moderator_tasks.id and\n                                                  moderators_history.to_usr={$uid} and\n                                                  moderators_history.readed=0 and\n                                                  moderator_tasks.archived=0 and\n                                                  moderator_tasks.ended=0 and\n                                                  moderator_tasks.rejected=0")->first('counter');
    }
    public function getControllerAccess($uid, $reseller)
    {
        $this->mysqlInstance->where(['blocked<>' => 1]);
        if ($reseller) {
            $this->mysqlInstance->where(['only_top_admin<>' => 1]);
        }
        if (!empty($uid)) {
            $params['group_id'] = $uid;
        } else {
            $params['group_id'] = null;
        }
        $params[' 1=1 OR `hidden`='] = 1;
        return $this->mysqlInstance->from('adm_grp_action_access')->where($params)->orderby(['controller_name' => 'ASC', 'action_name' => 'ASC'])->get()->all();
    }
    public function getDropdownAttribute($param)
    {
        return $this->mysqlInstance->from('admin_dropdown_attributes')->where($param)->get()->first();
    }
    public function getFirstFreeNumber($table, $field = 'number', $offset = 1, $direction = 1)
    {
        if ($direction == 1) {
            $func = 'min';
            $compare = '>=';
            $order = 'ASC';
            $operation = '+';
        } else {
            $func = 'max';
            $compare = '<=';
            $order = 'DESC';
            $operation = '-';
        }
        return $this->mysqlInstance->query("SELECT (`{$table}`.`{$field}` {$operation} 1) as `empty_number`\n                    FROM `{$table}`\n                    WHERE (\n                        SELECT 1 FROM `{$table}` as `st` WHERE `st`.`{$field}` = (`{$table}`.`{$field}` {$operation} 1) AND `st`.`{$field}` {$compare} {$offset} LIMIT 1\n                    ) IS NULL AND `{$table}`.`{$field}` {$compare} {$offset}\n                    ORDER BY `{$table}`.`{$field}` {$order}\n                    LIMIT 1")->first('empty_number');
    }
    public function getEnumValues($table, $field)
    {
        $type = $this->mysqlInstance->query("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")->first('Type');
        \preg_match("/^enum\\(\\'(.*)\\'\\)\$/", $type, $matches);
        $enum = \explode("','", $matches[1]);
        return $enum;
    }
    public function setSQLDebug($flag = 0)
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::$debug = $flag;
    }
}
