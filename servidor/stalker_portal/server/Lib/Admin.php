<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Admin
{
    private static $instance = null;
    private $profile;
    private function __construct()
    {
        $sid = \session_id();
        if (!\headers_sent() && empty($sid)) {
            \session_start();
        }
        if (!empty($_SESSION['uid'])) {
            $this->profile = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['id' => $_SESSION['uid']])->get()->first();
        }
    }
    public static function checkAuthorization($login, $pass)
    {
        $admin = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['login' => $login])->get()->first();
        if (empty($admin)) {
            return false;
        }
        if ($admin['pass'] == \md5($pass)) {
            if (self::$instance == null) {
                self::getInstance();
            }
            $_SESSION['uid'] = $admin['id'];
            $_SESSION['login'] = $admin['login'];
            $_SESSION['pass'] = $admin['pass'];
            return true;
        }
        return false;
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function checkAuth()
    {
        $admin = self::getInstance();
        if (!$admin->isAuthorized()) {
            \header('Location: login.php');
            exit;
        }
    }
    public function isAuthorized()
    {
        if (empty($_SESSION['login']) || empty($_SESSION['pass'])) {
            return false;
        }
        $admin = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('administrators')->where(['login' => $_SESSION['login']])->get()->first();
        if (empty($admin)) {
            return false;
        }
        $is_authorized = $admin['pass'] == $_SESSION['pass'];
        if ($is_authorized) {
            $this->profile = $admin;
        }
        return $is_authorized;
    }
    public static function checkLanguage($language)
    {
        if ($language && self::getInstance()->getAdminLanguage() !== $language && self::getInstance()->getId()) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('administrators', ['language' => $language], ['id' => self::getInstance()->getId()]);
            self::getInstance()->profile['language'] = $language;
        }
    }
    public function getAdminLanguage()
    {
        return $this->profile['language'];
    }
    public function getId()
    {
        return $this->profile['id'];
    }
    public static function isSuperUser()
    {
        if (self::$instance == null) {
            self::getInstance();
        }
        return self::$instance->getLogin() == 'admin';
    }
    public function getLogin()
    {
        return $this->profile['login'];
    }
    public static function isActionAllowed($page = null)
    {
        return self::isAllowed(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION, $page);
    }
    private static function isAllowed($action, $page = null)
    {
        if (self::$instance == null) {
            self::getInstance();
        }
        if ($page === null) {
            $page = self::getCurrentPage();
        }
        $access = new \Ministra\Lib\AdminAccess(self::$instance);
        return $access->check($page, $action);
    }
    private static function getCurrentPage()
    {
        $page = '';
        if (\preg_match("/\\/([^\\/]+)\\./", $_SERVER['PHP_SELF'], $match)) {
            $page = $match[1];
        }
        return $page;
    }
    public static function isPageActionAllowed($page = null)
    {
        return self::isAllowed(\Ministra\Lib\AdminAccess::ACCESS_PAGE_ACTION, $page);
    }
    public static function isCreateAllowed($page = null)
    {
        return self::isAllowed(\Ministra\Lib\AdminAccess::ACCESS_CREATE, $page);
    }
    public static function isEditAllowed($page = null)
    {
        return self::isAllowed(\Ministra\Lib\AdminAccess::ACCESS_EDIT, $page);
    }
    public static function isAccessAllowed($page = null)
    {
        return self::isAllowed(\Ministra\Lib\AdminAccess::ACCESS_VIEW, $page);
    }
    public static function checkAccess($action = 'view', $page = null)
    {
        if ($page === null) {
            $page = self::getCurrentPage();
        }
        if (!self::isAllowed($action, $page)) {
            echo \sprintf(\_('Action "%s" denied for page "%s"'), $action, $page);
            exit;
        }
    }
    public function getGID()
    {
        return $this->profile['gid'];
    }
    public function getResellerID()
    {
        return $this->profile['reseller_id'];
    }
    public function getOpinionFormFlag()
    {
        return $this->profile['opinion_form_flag'];
    }
    public function getTheme()
    {
        return $this->profile['theme'];
    }
}
