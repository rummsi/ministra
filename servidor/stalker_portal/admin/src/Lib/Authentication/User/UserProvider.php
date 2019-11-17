<?php

namespace Ministra\Admin\Lib\Authentication\User;

use Doctrine\DBAL\Connection;
use Ministra\Admin\Lib\Authentication\Exception\UnsupportedUserRoleException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
class UserProvider implements \Symfony\Component\Security\Core\User\UserProviderInterface
{
    private $conn;
    const ROLE_PREFIX = 'ROLE_';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const SUPER_USER_NAME = 'admin';
    public function __construct(\Doctrine\DBAL\Connection $conn)
    {
        $this->conn = $conn;
    }
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        if (!$user instanceof \Ministra\Admin\Lib\Authentication\User\User) {
            throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }
    public function loadUserByUsername($username)
    {
        $query = $this->conn->createQueryBuilder()->select('*')->addSelect('A.login as username')->addSelect('A.pass as password')->from('administrators', 'A')->where('A.login = :username')->setParameters(['username' => $username])->setMaxResults(1);
        $user = $query->execute()->fetch();
        if (empty($user)) {
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException(\sprintf(\_('Username "%s" does not exist.'), $username));
        }
        $user['group'] = $this->getUserGroupById($user['gid']);
        $roles = [$this->getAdaptedRole($user['group'])];
        return new \Ministra\Admin\Lib\Authentication\User\User($user, $roles, true, true, true, true);
    }
    public function supportsClass($class)
    {
        return $class === 'Symfony\\Component\\Security\\Core\\User\\User';
    }
    public static function transliterate($st)
    {
        $st = \trim($st);
        $replace = ['а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'g', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ж' => 'G', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Ы' => 'I', 'Э' => 'E', 'ё' => 'yo', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ь' => '', 'ю' => 'yu', 'я' => 'ya', 'Ё' => 'Yo', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ь' => '', 'Ю' => 'Yu', 'Я' => 'Ya', ' ' => '_', '!' => '', '?' => '', ',' => '', '.' => '', '"' => '', '\'' => '', '\\' => '', '/' => '', ';' => '', ':' => '', '«' => '', '»' => '', '`' => '', '-' => '-', '—' => '-'];
        $st = \strtr($st, $replace);
        $st = \preg_replace('/[^a-z0-9_-]/i', '', $st);
        return $st;
    }
    private function getUserGroupById($gid)
    {
        if (0 === (int) $gid) {
            return 'admin';
        }
        $query = $this->conn->createQueryBuilder()->select('name')->from('admin_groups', 'A_G')->where('A_G.id = :gid')->setParameters(['gid' => $gid]);
        $group = $query->execute()->fetch();
        return $group['name'] != 'admin' ? $group['name'] : $group['name'] . '_' . $gid;
    }
    public static function getAdaptedRole($base_role_name)
    {
        if (!$base_role_name) {
            throw new \Ministra\Admin\Lib\Authentication\Exception\UnsupportedUserRoleException(\sprintf(\_('User role "%s" does not exists'), (string) $base_role_name));
        }
        $base_role_name = self::transliterate($base_role_name);
        $base_role_name = \preg_replace("/\\s\\-/i", '_', $base_role_name);
        $base_role_name = \preg_replace("/[^\\w]/i", '', $base_role_name);
        return self::ROLE_PREFIX . \strtoupper($base_role_name);
    }
}
