<?php

namespace Ministra\Admin\Lib\Middleware;

use Ministra\Admin\Lib\Authentication\AccessMap;
use Moust\Silex\Cache\CacheInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\Translator;
class SideBar
{
    protected $cache;
    private $user;
    private $accessMap;
    private $sideBar = array();
    private $isCached = false;
    protected $locale;
    private $hash;
    public function __construct(\Symfony\Component\Security\Core\User\UserInterface $user, \Ministra\Admin\Lib\Authentication\AccessMap $accessMap, \Moust\Silex\Cache\CacheInterface $cache, \Symfony\Component\Translation\Translator $locale)
    {
        $this->setUser($user);
        $this->setAccessMap($accessMap);
        $this->cache = $cache;
        $this->locale = $locale;
    }
    public function setUser($user)
    {
        $this->user = $user;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setAccessMap($accessMap)
    {
        $this->accessMap = $accessMap;
    }
    public function getAccessMap()
    {
        return $this->accessMap;
    }
    private function setSideBar()
    {
        if (!\is_array($this->sideBar)) {
            throw new \InvalidArgumentException('Invalid sidebar data');
        }
        $this->buildSideBar();
        $this->cache->store($this->getCacheKey(), $this->sideBar);
        $this->isCached = true;
    }
    public function getSideBar()
    {
        return $this->sideBar ?: $this->getCached();
    }
    private function getCacheKey()
    {
        $key = $this->user->getRoles();
        $key = \reset($key);
        return $key . '_' . $this->locale->getLocale() . '_' . ($this->user->getUserGroupId() ?: '') . '_' . $this->hash;
    }
    public function isCached()
    {
        if ($this->getCached()) {
            return true;
        }
        return false;
    }
    public function clearCache()
    {
        $this->cache->delete($this->getCacheKey());
        $this->isCached = false;
    }
    public function getCached()
    {
        return $this->cache->fetch($this->getCacheKey());
    }
    private function buildSideBar()
    {
        $accessMap = $this->accessMap->getAccessMap($this->user->getRoles());
        $user = $this->user->getUsername();
        $this->sideBar = \array_filter(\array_map(function ($controller_item) use($accessMap, $user) {
            if (\array_key_exists($controller_item['alias'], $accessMap) && (int) $accessMap[$controller_item['alias']]['is_ajax'] == 0 && $accessMap[$controller_item['alias']]['only_top_admin'] == 0 && (\bindec($accessMap[$controller_item['alias']]['access']) > 0 || $user === 'admin')) {
                $translate = \trim($this->locale->trans($controller_item['name']));
                $controller_item['name'] = !empty($translate) ? $translate : $controller_item['name'];
                if (!empty($controller_item['action'])) {
                    $controller_item_alias = $controller_item['alias'];
                    $controller_item['action'] = \array_filter(\array_map(function ($action_item) use($accessMap, $user, $controller_item_alias) {
                        $action_item_alias = $controller_item_alias . '/' . $action_item['alias'];
                        if (\array_key_exists($action_item_alias, $accessMap) && (int) $accessMap[$action_item_alias]['is_ajax'] == 0 && $accessMap[$action_item_alias]['only_top_admin'] == 0 && \bindec($accessMap[$action_item_alias]['access']) > 0 || $user === 'admin') {
                            $translate = \trim($this->locale->trans($action_item['name']));
                            $action_item['name'] = !empty($translate) ? $translate : $action_item['name'];
                            return $action_item;
                        }
                    }, $controller_item['action']));
                }
                return $controller_item;
            }
        }, $this->sideBar));
    }
    public function loadMenu($menuPath)
    {
        if (\is_file($menuPath)) {
            $sideBarTemplate = \file_get_contents($menuPath);
            if (false !== $sideBarTemplate) {
                $this->sideBar = \json_decode(\str_replace(['_(', ')'], '', $sideBarTemplate), true);
            }
            $this->hash = \md5($sideBarTemplate);
        }
        $this->setSideBar();
    }
}
