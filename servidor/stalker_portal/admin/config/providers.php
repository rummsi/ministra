<?php

if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
use Ministra\Admin\AppServiceProvider;
use Ministra\Admin\CacheServiceProvider;
use Ministra\Admin\Lib\Authentication\AccessVoter;
use Ministra\Admin\Lib\Authentication\User\UserProvider;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use nymo\Silex\Provider\BreadCrumbServiceProvider;
use nymo\Twig\Extension\BreadCrumbExtension;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Translation\Loader\PoFileLoader;
use W6\Service\Provider\ImagineServiceProvider;
$app->register(new \Ministra\Admin\AppServiceProvider());
$app['debug'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('admin_panel_debug', \false);
$app->register(new \Silex\Provider\HttpCacheServiceProvider(), ['http_cache.cache_dir' => __DIR__ . '/../resources/cache/http']);
if ($app['debug']) {
    require __DIR__ . '/dev.php';
}
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('admin_panel_debug_log', \false)) {
    $app->register(new \Silex\Provider\MonologServiceProvider(), ['monolog.logfile' => \join_paths(__DIR__, '..', 'logs', 'development_' . \date('Y-m-d') . '.log')]);
}
$app->register(new \Silex\Provider\DoctrineServiceProvider(), ['db.options' => ['driver' => 'pdo_mysql', 'host' => $app['db.host'], 'dbname' => $app['db.dbname'], 'user' => $app['db.user'], 'port' => $app['db.port'], 'password' => $app['db.password'], 'charset' => 'utf8', 'collate' => 'utf8_general_ci', 'defaultTableOptions' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci']]]);
$app->register(new \W6\Service\Provider\ImagineServiceProvider());
$app->register(new \nymo\Silex\Provider\BreadCrumbServiceProvider());
$app->register(new \Silex\Provider\RoutingServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Ministra\Admin\CacheServiceProvider());
$app->register(new \Silex\Provider\SecurityServiceProvider(), ['security.firewalls' => ['login' => ['pattern' => '^/login$'], 'secured' => ['pattern' => '^.*$', 'form' => ['login_path' => '/login', 'check_path' => '/login-check', 'require_previous_session' => \false], 'logout' => ['logout_path' => '/logout', 'invalidate_session' => \true], 'users' => function () use($app) {
    return new \Ministra\Admin\Lib\Authentication\User\UserProvider($app['db']);
}]]]);
$app->extend('security.voters', function () use($app) {
    return [new \Ministra\Admin\Lib\Authentication\AccessVoter($app[\Psr\Container\ContainerInterface::class]), new \Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter(new \Symfony\Component\Security\Core\Role\RoleHierarchy($app['security.role_hierarchy'])), new \Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter($app['security.trust_resolver'])];
});
$app['security.default_encoder'] = function () {
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('md5', \false, 0);
};
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.options' => ['cache' => isset($app['twig.options.cache']) && \is_dir($app['twig.options.cache']) && \is_writable($app['twig.options.cache']) ? $app['twig.options.cache'] : \false, 'strict_variables' => \true, 'auto_reload' => \true], 'twig.path' => __DIR__ . '/../resources/views']);
$app->register(new \Silex\Provider\TranslationServiceProvider(), ['locale' => $app['language']]);
$app->extend('translator', function ($translator, \Silex\Application $app) {
    $translator->addLoader('po', new \Symfony\Component\Translation\Loader\PoFileLoader());
    $translator->addResource('po', __DIR__ . "/../../server/locale/{$app['language']}/LC_MESSAGES/stb.po", $app['language'], 'messages');
    return $translator;
});
$app->extend('twig', function (\Twig\Environment $twig, \Silex\Application $app) {
    $theme = $app['themes'];
    $curr_theme = 'default';
    if (!empty($curr_theme) && \array_key_exists($curr_theme, $theme) && \is_dir($theme[$curr_theme])) {
        $curr_theme = $theme[$curr_theme];
    } else {
        $curr_theme = $theme['default'];
    }
    $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($curr_theme));
    $twig->addExtension(new \nymo\Twig\Extension\BreadCrumbExtension($app));
    $twig->addExtension(new \Twig_Extension_Optimizer());
    return $twig;
});
