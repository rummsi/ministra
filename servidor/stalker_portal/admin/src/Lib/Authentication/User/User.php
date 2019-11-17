<?php

namespace Ministra\Admin\Lib\Authentication\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
final class User implements \Symfony\Component\Security\Core\User\AdvancedUserInterface
{
    const STATUS_BLOCKED = 0;
    const ROLE_ADMIN = 'ROLE_ADMIN';
    private $id;
    private $username;
    private $password;
    private $gid;
    private $group;
    private $reseller_id;
    private $language;
    private $theme = '';
    private $enabled;
    private $accountNonExpired;
    private $credentialsNonExpired;
    private $accountNonLocked;
    private $roles;
    public function __construct($user, array $roles = array(), $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if (!$user || !$user['username']) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }
        foreach ($user as $prop_name => $prop) {
            if (\property_exists($this, $prop_name)) {
                $this->{$prop_name} = $prop;
            }
        }
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
    }
    public function __toString()
    {
        return $this->getUsername();
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getUserGroupId()
    {
        return $this->gid;
    }
    public function getGID()
    {
        return $this->getUserGroup();
    }
    public function getUserGroup()
    {
        return $this->group;
    }
    public function getResellerId()
    {
        return $this->reseller_id;
    }
    public function getLanguage()
    {
        return $this->language;
    }
    public function getUserTheme()
    {
        return $this->theme;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getSalt()
    {
        return '';
    }
    public function getId()
    {
        return $this->id;
    }
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }
    public function isEnabled()
    {
        return $this->enabled;
    }
    public function eraseCredentials()
    {
    }
    public function getLogin()
    {
        return $this->getUsername();
    }
}
