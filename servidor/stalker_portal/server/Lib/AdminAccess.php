<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class AdminAccess
{
    const ACCESS_VIEW = 'view';
    const ACCESS_CREATE = 'create';
    const ACCESS_DELETE = 'delete';
    const ACCESS_EDIT = 'edit';
    const ACCESS_PAGE_ACTION = 'page_action';
    const ACCESS_CONTEXT_ACTION = 'context_action';
    private $admin;
    public function __construct(\Ministra\Lib\Admin $admin)
    {
        $this->admin = $admin;
    }
    public static function convertPostParamsToAccessMap($post_data)
    {
        $fields = ['view', 'create', 'edit', 'delete', 'page_action', 'context_action'];
        $map = [];
        foreach ($fields as $field) {
            if (!isset($post_data[$field])) {
                continue;
            }
            foreach ($post_data[$field] as $page => $val) {
                if (!isset($map[$page])) {
                    $map[$page] = \array_fill_keys($fields, 0);
                    $map[$page]['page'] = $page;
                }
                $map[$page][$field] = $val;
            }
        }
        return \array_values($map);
    }
    public function check($page, $action = 'view')
    {
        if ($this->admin->getLogin() == 'admin') {
            return true;
        }
        return (bool) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('acl')->where(['gid' => $this->admin->getGID(), 'page' => $page, 'acl.' . $action => 1])->get()->first();
    }
}
