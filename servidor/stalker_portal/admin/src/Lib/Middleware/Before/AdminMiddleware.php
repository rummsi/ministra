<?php

namespace Ministra\Admin\Lib\Middleware\Before;

use Doctrine\DBAL\Query\QueryBuilder;
use Ministra\Admin\Container\ContainerInterface;
use Ministra\Admin\Lib\Authentication\User\User;
use Ministra\Admin\Lib\Middleware\AbstractMiddleware;
use Ministra\Admin\Lib\Middleware\SideBar;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\NotificationFeed;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class AdminMiddleware extends \Ministra\Admin\Lib\Middleware\AbstractMiddleware
{
    const ROOT_BREADCRUMB = 'Ministra TV platform';
    private $menuPath;
    private $user_info = array('id' => 0, 'login' => '', 'reseller_id' => 0, 'access_level' => 0, 'group_id' => 0, 'group_name' => '', 'language' => 'en', 'theme' => 'default', 'task_count' => 0, 'notification_count' => 0);
    private $user;
    private $isAuthorize;
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, \Ministra\Admin\Container\ContainerInterface $container, $menuPath = null)
    {
        $this->setRequest($request);
        $this->setContainer($container);
        $this->menuPath = $menuPath ?: __DIR__ . '/../../../../json_menu/menu.json';
    }
    protected function prepareContainer()
    {
        $this->setUser();
        $this->setAppVars($this->getRequest()->isXmlHttpRequest());
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->prepareSidebar();
        }
    }
    public function setAppVars($isAjax)
    {
        $this->container->set('COOKIE', $this->request->cookies);
        $tmp = \explode('/', \trim($this->request->getPathInfo(), '/'));
        $this->container->set('controller_alias', $tmp[0]);
        $this->container->set('action_alias', \count($tmp) == 2 ? $tmp[1] : '');
        $env_var = \substr((string) \getenv('STALKER_ENV'), 0, 3);
        $this->container->set('stalker_env', $env_var ?: 'min');
        $this->container->set('baseHost', $this->request->getSchemeAndHttpHost());
        $this->container->set('workHost', $this->container->get('baseHost') . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/'));
        $this->container->set('relativePath', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/'));
        $uri = $this->container->get('request_stack')->getCurrentRequest()->getUri();
        $controller = $this->container->has('controller_alias') ? '/' . $this->container->get('controller_alias') : '';
        $action = $this->container->has('action_alias') ? '/' . $this->container->get('action_alias') : '';
        $action = $action === '/' ? '' : $action;
        $controller = $controller === '/' ? '' : $controller;
        $workUrl = \explode('?', \str_replace([$action, $controller], '', $uri));
        $this->container->set('workURL', $workUrl[0]);
        $this->container->set('refferer', $this->request->server->get('HTTP_REFERER'));
        if (null !== $this->user) {
            $this->container->set('userlogin', $this->user->getUsername());
            $this->container->set('user_id', $this->user->getId());
            $this->container->set('reseller', $this->user->getResellerId());
        }
        $this->container->set('baseDir', $this->request->getBasePath());
        $this->container->set('admin', $this->getUser());
        $this->container->set('twig_theme', $this->setUserTheme());
        $this->container->set('user_info', $this->getUserInfoForTemplate());
        $this->container->set('datatable_lang_file', './plugins/datatables/lang/' . \str_replace('utf8', 'json', $this->container->get('used_locale')));
        $this->container->get('breadcrumbs')->addItem(self::ROOT_BREADCRUMB, $workUrl[0]);
    }
    protected function prepareSidebar()
    {
        if (!$this->getUser()) {
            return;
        }
        $sidebar = new \Ministra\Admin\Lib\Middleware\SideBar($this->getUser(), $this->container->get('access_map'), $this->container->get('cache'), $this->container->get('translator'));
        $sidebar->loadMenu($this->menuPath);
        $this->container->set('side_bar', $sidebar->getSideBar());
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setUser(\Ministra\Admin\Lib\Authentication\User\User $user = null)
    {
        if (null === $user) {
            $token = $this->container->get('security.token_storage')->getToken();
            if ($token instanceof \Symfony\Component\Security\Core\Authentication\Token\TokenInterface) {
                $user = $token->getUser();
                $user = $user && \in_array(\Symfony\Component\Security\Core\User\UserInterface::class, \class_implements($user)) ? $user : null;
            }
        }
        $this->user = $user;
    }
    protected function getUserInfoForTemplate()
    {
        if (!$this->user || !(\is_object($this->user) && \in_array(\Symfony\Component\Security\Core\User\UserInterface::class, \class_implements($this->user)))) {
            return [];
        }
        $this->user_info['id'] = $this->user->getId();
        $this->user_info['login'] = $this->user->getUsername();
        $this->user_info['reseller_id'] = $this->user->getResellerId();
        $this->user_info['group_id'] = $this->user->getUserGroupId();
        $this->user_info['group_name'] = $this->user->getUserGroup();
        $this->user_info['language'] = $this->user->getLanguage();
        $this->user_info['theme'] = $this->user->getUserTheme();
        $query = $this->container->get('db')->createQueryBuilder();
        $query->select('COUNT(*) as task_count')->from('moderators_history', 'M_H')->leftJoin('M_H', 'moderator_tasks', 'M_T', 'M_H.task_id = M_T.id')->where('M_H.to_usr = :to_usr')->setParameters(['to_usr' => $this->user->getId()])->andWhere('M_H.readed = 0')->andWhere('M_T.archived = 0')->andWhere('M_T.ended = 0')->andWhere('M_T.rejected = 0')->setMaxResults(1);
        $result = $query->execute()->fetch();
        $this->user_info['task_count'] = \array_key_exists('task_count', $result) ? $result['task_count'] : 0;
        if ($this->user_info['login'] == 'admin') {
            $feed = new \Ministra\Lib\NotificationFeed();
            $this->user_info['notification_count'] = $feed->getCount();
        }
        return $this->user_info;
    }
    public function setUserTheme()
    {
        $twig_theme = null !== $this->user ? $this->user->getUserTheme() : '';
        if ($this->container->has('themes') && \array_key_exists($twig_theme, $this->container->get('themes')) && \is_dir($this->container->get('themes')[$twig_theme])) {
            $twig_theme = $this->container['themes'][$twig_theme];
        } else {
            $twig_theme = 'default';
        }
        return $twig_theme;
    }
    protected function setIsAuthorize($isAuthorize)
    {
        $this->isAuthorize = (bool) $isAuthorize;
    }
    public function isAuthorize()
    {
        if (\is_null($this->isAuthorize)) {
            return $this->isAuthorize = $this->user && $this->container->get('security.authorization_checker')->isGranted(['request' => $this->request], $this->container->get('access_map'));
        }
        return $this->isAuthorize;
    }
    public function process(\Symfony\Component\HttpFoundation\Request $request, \Psr\Container\ContainerInterface $container)
    {
        $this->setRequest($request);
        $this->setUser();
        $this->setContainer($container);
        $this->prepareContainer();
        if (!$this->getUser()) {
            return null;
        }
        if (!$this->isAuthorize()) {
            return static::getForbidden($this->getRequest());
        }
        return null;
    }
}
