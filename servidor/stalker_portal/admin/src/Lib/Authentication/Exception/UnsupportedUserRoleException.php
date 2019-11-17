<?php

namespace Ministra\Admin\Lib\Authentication\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
class UnsupportedUserRoleException extends \Symfony\Component\Security\Core\Exception\AuthenticationException
{
    public function getMessageKey()
    {
        return 'User role not found.';
    }
}
