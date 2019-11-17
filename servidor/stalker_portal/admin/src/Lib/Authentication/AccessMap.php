<?php

namespace Ministra\Admin\Lib\Authentication;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ministra\Admin\Lib\Authentication\User\User as AdminAuthUser;
use Ministra\Admin\Lib\Authentication\User\UserProvider;
use Ministra\Admin\Lib\Authentication\User\UserProvider as AdminAuthUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
class AccessMap
{
    private $accessMap = array();
    protected $map = array();
    private $conn;
    private $user;
    public function __construct(\Doctrine\DBAL\Connection $connection, \Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->setConnection($connection);
        $this->setUser($user);
        $this->setAccessMap();
    }
    public function getMap()
    {
        return $this->map;
    }
    public function getAccessMap($role = null)
    {
        $role = \is_string($role) ? $role : (\is_array($role) ? \reset($role) : null);
        return \array_key_exists($role, $this->accessMap) ? $this->accessMap[$role] : $this->accessMap;
    }
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user = null)
    {
        $this->user = $user;
    }
    public function setConnection(\Doctrine\DBAL\Connection $conn)
    {
        $this->conn = $conn;
    }
    public function setAccessMap()
    {
        if (\count($this->map) > 0) {
            return;
        }
        $query = $this->conn->createQueryBuilder();
        $query->select('A_G_A_A.*')->addSelect('IF(ISNULL(A_G_A_A.group_id), NULL, ' . '(SELECT name from admin_groups WHERE id=A_G_A_A.group_id)) as group_name')->addSelect('concat_ws(A_G_A_A.id, "_", A_G_A_A.controller_name, A_G_A_A.action_name) as group_str')->from('adm_grp_action_access', 'A_G_A_A')->where('A_G_A_A.blocked<>1 OR (A_G_A_A.hidden = 1 and A_G_A_A.blocked<>1)')->addOrderBy('A_G_A_A.controller_name', 'ASC')->addOrderBy('A_G_A_A.action_name', 'ASC');
        $results = $query->execute()->fetchAll();
        $userRoles = $this->user ? $this->user->getRoles() : [];
        foreach ($results as $row) {
            $roles = [null !== $row['group_name'] ? \Ministra\Admin\Lib\Authentication\User\UserProvider::getAdaptedRole($row['group_name']) : 'ROLE_ADMIN'];
            if ($row['hidden'] == 1) {
                $roles = \array_merge($roles, $userRoles);
            }
            foreach ($roles as $role) {
                $path = $row['controller_name'] . ($row['action_name'] ? '/' . $row['action_name'] : '');
                if (!\array_key_exists($role, $this->accessMap)) {
                    $this->accessMap[$role] = [];
                }
                if (!\array_key_exists($row['controller_name'], $this->accessMap[$role])) {
                    $this->accessMap[$role][$row['controller_name']] = ['access' => '000', 'is_ajax' => 0, 'only_top_admin' => (int) $row['only_top_admin'], 'controller' => $row['controller_name'], 'action' => $row['action_name'], 'manual_entered' => true];
                } elseif (!empty($this->accessMap[$role][$row['controller_name']]['manual_entered'])) {
                    $this->accessMap[$role][$row['controller_name']]['access'] |= $row['action_access'] . $row['edit_access'] . $row['view_access'];
                    $this->accessMap[$role][$row['controller_name']]['only_top_admin'] |= (int) $row['only_top_admin'];
                }
                if (\in_array($role, $userRoles)) {
                    $this->accessMap[$role][$path] = ['access' => $role === \Ministra\Admin\Lib\Authentication\User\UserProvider::ROLE_ADMIN ? '111' : $row['action_access'] . $row['edit_access'] . $row['view_access'], 'is_ajax' => $row['is_ajax'], 'only_top_admin' => $row['only_top_admin']];
                }
            }
        }
    }
}
