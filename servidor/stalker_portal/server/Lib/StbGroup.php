<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class StbGroup
{
    private $db;
    private $name = '';
    private $id = 0;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
    }
    public function setName($group_name)
    {
        $this->name = $group_name;
        $this->id = (int) $this->db->from('stb_groups')->where(['name' => $group_name])->get()->first('id');
    }
    public function getAll()
    {
        return $this->db->from('stb_groups')->get()->all();
    }
    public function getGroupIdByUid($uid)
    {
        return $this->db->from('stb_in_group')->where(['uid' => (int) $uid])->get()->first('stb_group_id');
    }
    public function getById($groupID)
    {
        return $this->db->from('stb_groups')->where(['id' => (int) $groupID])->get()->first();
    }
    public function add($group_name)
    {
        $group = $this->db->from('stb_groups')->where(['name' => $group_name])->get()->all();
        if (empty($group)) {
            $this->id = $this->db->insert('stb_groups', ['name' => $group_name])->insert_id();
            return $this->id;
        }
        return false;
    }
    public function del($groupID)
    {
        $groupID = (int) $groupID;
        $group = $this->db->from('stb_groups')->where(['id' => $groupID])->get()->all();
        if (!empty($group)) {
            return $this->db->delete('stb_groups', ['id' => $groupID])->result();
        }
        return false;
    }
    public function set($data, $groupID = 0)
    {
        if ($groupID) {
            return $this->db->update('stb_groups', $data, ['id' => $groupID])->result();
        }
        return $this->db->insert('stb_groups', $data)->insert_id();
    }
    public function addMember($data)
    {
        if (!empty($data) && \is_array($data) && $data['uid']) {
            $record = $this->getMemberByUid($data['uid']);
            if (empty($record)) {
                return $this->db->insert('stb_in_group', $data)->insert_id();
            }
        }
        return false;
    }
    public function getMemberByUid($uid)
    {
        return $this->db->from('stb_in_group')->where(['uid' => (int) $uid])->get()->first();
    }
    public function addMembers(array $users, $gid)
    {
        $data = [];
        foreach ($users as $user) {
            $data[] = ['stb_group_id' => $gid, 'uid' => $user['id'], 'mac' => $user['mac']];
        }
        return $this->db->insert('stb_in_group', $data)->total_rows();
    }
    public function setMember($data, $memberID)
    {
        if (!empty($data) && \is_array($data)) {
            $record = $this->getMember($memberID);
            if (!empty($record)) {
                return $this->db->update('stb_in_group', $data, ['id' => $memberID])->result();
            }
        }
        return false;
    }
    public function getMember($memberID)
    {
        return $this->db->from('stb_in_group')->where(['id' => (int) $memberID])->get()->first();
    }
    public function removeMember($memberID)
    {
        $memberID = (int) $memberID;
        if ($memberID > 0) {
            return $this->db->delete('stb_in_group', ['id' => $memberID])->result();
        }
        return false;
    }
    public function removeMembersByIds(array $ids)
    {
        return $this->db->in('uid', $ids)->delete('stb_in_group', [])->total_rows();
    }
    public function getAllMembersByGroupId($groupID)
    {
        return $this->db->from('stb_in_group')->where(['stb_group_id' => (int) $groupID])->get()->all();
    }
    public function getAllMemberUidsByGroupId($groupID)
    {
        return $this->db->from('stb_in_group')->where(['stb_group_id' => (int) $groupID])->get()->all('uid');
    }
}
