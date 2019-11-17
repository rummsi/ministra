<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class SmartLauncherAppsManager
{
    private static $instance;
    private $lang;
    private $callback;
    private $protocol = '';
    private $host;
    private $launcher_root_web_path;
    private $launcher_root_system_path;
    public function __construct($lang = null)
    {
        $this->lang = $lang ? $lang : 'en';
        $this->setProtocol();
        $this->setHost();
        $this->setLauncherRootWebPath();
        $this->setLauncherRootSystemPath();
        self::$instance = $this;
    }
    public static function getLauncherUrl()
    {
        $core = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['type' => 'core', 'status' => 1])->get()->first();
        if (empty($core)) {
            return false;
        }
        if (!empty($core['config'])) {
            $core['config'] = \json_decode($core['config'], true);
        }
        $url = join_paths($core['alias'], $core['current_version'], isset($core['config']['uris']['app']) ? $core['config']['uris']['app'] : 'app');
        return self::getInstance()->getLauncherRootWebPath() . $url . '/';
    }
    public function getLauncherRootWebPath()
    {
        return $this->launcher_root_web_path;
    }
    public function setLauncherRootWebPath()
    {
        $this->launcher_root_web_path = $this->getProtocol() . join_paths($this->getHost(), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_path', 'stalker_launcher_apps/'));
    }
    public static function getInstance($lang = null)
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        return new self($lang);
    }
    public static function getLauncherProfileUrl()
    {
        $url = join_paths(self::getInstance()->getHost(), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('portal_url', '/stalker_portal/'), 'server/api/launcher_profile.php');
        return self::getInstance()->getProtocol() . $url;
    }
    public function getHost()
    {
        return $this->host;
    }
    public function setHost()
    {
        if (\array_key_exists('HTTP_HOST', $_SERVER)) {
            $this->host = \strpos($_SERVER['HTTP_HOST'], ':') > 0 ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
        }
    }
    public function getProtocol()
    {
        return $this->protocol;
    }
    public function setProtocol()
    {
        if (\array_key_exists('SERVER_PORT', $_SERVER) || \array_key_exists('HTTPS', $_SERVER)) {
            $this->protocol = 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ? 's' : '') . '://';
        }
    }
    public function setNotificationCallback($callback)
    {
        if (!\is_callable($callback)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Not valid callback');
        }
        $this->callback = $callback;
    }
    public function getAppInfoByUrl($url, $force_npm = false)
    {
        $app = $original_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['url' => $url])->get()->first();
        if (empty($app)) {
            return;
        }
        return $this->getAppInfo($app['id'], $force_npm);
    }
    public function getAppInfo($app_id, $force_npm = false)
    {
        $app = $original_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('App not found, id=' . $app_id);
        }
        if (empty($app['alias']) || $force_npm) {
            $this->sendToCallback('Getting info for ' . $app['url'] . '...');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->del($app_id . '_launcher_app_info');
            $info = self::getNpmInfo($app);
            if (empty($info)) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to get info for ' . $app['url']);
            }
            $app['type'] = isset($info['config']['type']) ? $info['config']['type'] : null;
            $app['alias'] = $info['name'];
            $app['name'] = $app['type'] == 'app' && isset($info['config']['name']) ? $info['config']['name'] : $info['name'];
            $app['description'] = isset($info['config']['description']) ? $info['config']['description'] : (isset($info['description']) ? $info['description'] : '');
            $app['available_version'] = isset($info['version']) ? $info['version'] : '';
            $app['author'] = isset($info['author']) ? $info['author'] : '';
            $app['category'] = isset($info['config']['category']) ? $info['config']['category'] : null;
            $app['is_unique'] = isset($info['config']['unique']) && $info['config']['unique'] ? 1 : 0;
            $update_data = [];
            if (!$original_app['alias'] && $app['alias']) {
                $update_data['alias'] = $app['alias'];
            }
            if (!$original_app['name'] && $app['name']) {
                $update_data['name'] = $app['name'];
            }
            if (!$original_app['description'] && $app['description']) {
                $update_data['description'] = $app['description'];
            }
            if (!$original_app['author'] && $app['author'] || $original_app['author'] != $app['author']) {
                $update_data['author'] = $app['author'];
            }
            $update_data['category'] = $app['category'];
            $update_data['available_version'] = $app['available_version'];
            if (!empty($update_data)) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('launcher_apps', $update_data, ['id' => $app_id]);
            }
        } else {
            $app['is_unique'] = (int) $app['is_unique'];
        }
        unset($app['options']);
        $app['icon'] = '';
        $app['icon_big'] = '';
        $app['backgroundColor'] = '';
        if ($app['config']) {
            $app['config'] = \json_decode($app['config'], true);
        }
        if ($app['current_version']) {
            $app_path = join_paths($this->getLauncherRootSystemPath(), $app['type'] == 'plugin' ? 'plugins' : '', $app['url'], $app['current_version']);
            $app['app_path'] = $app_path;
            $app['installed'] = $app_path && \is_dir($app_path);
            if ($app['installed'] && isset($app['config']['uris']['icons']['720']['logoNormal']) && !empty($_SERVER['HTTP_HOST'])) {
                $icon_path = \realpath(join_paths($app_path, isset($app['config']['uris']['app']) ? $app['config']['uris']['app'] : 'app', $app['config']['uris']['icons']['720']['logoNormal']));
                $app['icon'] = $icon_path && \is_readable($icon_path) ? $this->getLauncherRootWebPath() . '/' . join_paths($app['alias'], $app['current_version'], isset($app['config']['uris']['app']) ? $app['config']['uris']['app'] : 'app', $app['config']['uris']['icons']['720']['logoNormal']) : '';
                $icon_big_path = \realpath(join_paths($app_path, isset($app['config']['uris']['app']) ? $app['config']['uris']['app'] : 'app', $app['config']['uris']['icons']['1080']['logoNormal']));
                $app['icon_big'] = $icon_big_path && \is_readable($icon_big_path) ? $this->getLauncherRootWebPath() . '/' . join_paths($app['alias'], $app['current_version'], isset($app['config']['uris']['app']) ? $app['config']['uris']['app'] : 'app', $app['config']['uris']['icons']['1080']['logoNormal']) : '';
                if ($app['icon'] || $app['icon_big']) {
                    $app['backgroundColor'] = isset($app['config']['colors']['splashBackground']) ? $app['config']['colors']['splashBackground'] : '';
                }
            }
        } else {
            $app['installed'] = false;
        }
        if ($app['localization'] && ($localization = \json_decode($app['localization'], true))) {
            if (!empty($localization[$this->lang]['name'])) {
                $app['name'] = $localization[$this->lang]['name'];
            }
            if (!empty($localization[$this->lang]['description'])) {
                $app['description'] = $localization[$this->lang]['description'];
            }
        }
        return $app;
    }
    public function sendToCallback($msg)
    {
        if (\is_null($this->callback)) {
            return;
        }
        \call_user_func($this->callback, $msg);
    }
    public static function getNpmInfo($app, $version = null)
    {
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $key = $version ? $app['id'] . '_' . $version . '_launcher_app_info' : $app['id'] . '_launcher_app_info';
        $cached_info = $cache->get($key);
        if (empty($cached_info)) {
            $npm = \Ministra\Lib\Npm::getInstance();
            $info = $npm->info($app['url'], $version);
        } else {
            $info = $cached_info;
        }
        if (empty($info)) {
            return;
        }
        if (empty($cached_info)) {
            $cache->set($key, $info, 0, 0);
        }
        return $info;
    }
    public function getLauncherRootSystemPath()
    {
        return $this->launcher_root_system_path;
    }
    public function setLauncherRootSystemPath()
    {
        $this->launcher_root_system_path = \realpath(join_paths(PROJECT_PATH, '/../../', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_path', 'stalker_launcher_apps/')));
    }
    public function updateAllAppsInfo()
    {
        $this->resetAppsCache();
        $apps = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->get()->all();
        foreach ($apps as $app) {
            try {
                $this->getAppInfo($app['id'], true);
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $this->sendToCallback('Error: ' . $e->getMessage());
            }
        }
    }
    public function resetAppsCache()
    {
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $apps = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->get()->all();
        foreach ($apps as $app) {
            $this->sendToCallback('Cleaning cache ' . $app['url'] . '...');
            $info = self::getNpmInfo($app);
            if (isset($info['versions'])) {
                if (!\is_array($info['versions'])) {
                    $info['versions'] = [$info['versions']];
                }
                foreach ($info['versions'] as $version) {
                    $cache->del($app['id'] . '_' . $version . '_launcher_app_info');
                }
            }
            $cache->del($app['id'] . '_launcher_app_info');
        }
    }
    public function getAppVersions($app_id)
    {
        $app = $original_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('App not found, id=' . $app_id);
        }
        $info = self::getNpmInfo($app);
        if (empty($info)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to get info for ' . $app['url']);
        }
        $versions = [];
        if (isset($info['versions']) && \is_string($info['versions'])) {
            $info['versions'] = [$info['versions']];
        }
        $option_values = \json_decode($app['options'], true);
        if (empty($option_values)) {
            $option_values = [];
        }
        if (isset($info['versions']) && \is_array($info['versions'])) {
            if (\array_key_exists('time', $info)) {
                unset($info['time']['modified'], $info['time']['created']);
            } else {
                $info['time'] = \array_combine($info['versions'], \array_pad([], \count($info['versions']), 0));
            }
            foreach ($info['time'] as $ver => $time) {
                $version = ['version' => $ver, 'published' => \strtotime($time), 'installed' => \is_dir(join_paths($this->getLauncherRootSystemPath(), $app['type'] == 'plugin' ? 'plugins' : '', $app['alias'], $ver)), 'current' => $ver == $app['current_version']];
                $info = self::getNpmInfo($app, $ver);
                $option_list = isset($info['config']['options']) ? $info['config']['options'] : [];
                if (isset($option_list['name'])) {
                    $option_list = [$option_list];
                }
                $option_list = \array_map(function ($option) use($option_values) {
                    if (isset($option_values[$option['name']])) {
                        $option['value'] = $option_values[$option['name']];
                    } elseif (!isset($option['value'])) {
                        $option['value'] = null;
                    }
                    if (isset($option['info'])) {
                        $option['desc'] = $option['info'];
                    }
                    return $option;
                }, $option_list);
                $version['options'] = $option_list;
                $versions[] = $version;
            }
        }
        return $versions;
    }
    public function deleteApp($app_id, $version = null)
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->del($app_id . '_launcher_app_info');
        $app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app['alias']) || empty($app['current_version'])) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Nothing to delete');
        }
        if ($version === null) {
            $version = '';
        }
        $path = join_paths($this->getLauncherRootSystemPath(), $app['type'] == 'plugin' ? 'plugins' : '', $app['alias'], $version);
        if (\is_dir($path)) {
            self::delTree($path);
        }
        if ($app['type'] == 'theme') {
            try {
                $theme = new \Ministra\Lib\Theme($app['alias']);
                if (!empty($version)) {
                    $theme->setVersion($version);
                }
                $theme->deleteThemeCompiledCSS();
            } catch (\Exception $exception) {
            }
        }
        if ($version && $version == $app['current_version']) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('launcher_apps', ['current_version' => ''], ['id' => $app_id]);
        } elseif (!$version) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('launcher_apps', ['id' => $app_id]);
        }
        return true;
    }
    private static function delTree($dir)
    {
        if (!\is_dir($dir)) {
            return false;
        }
        $files = \array_diff(\scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            \is_dir("{$dir}/{$file}") ? self::delTree("{$dir}/{$file}") : \unlink("{$dir}/{$file}");
        }
        return \rmdir($dir);
    }
    public function startAutoUpdate()
    {
        $need_to_update = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['status' => 1, 'autoupdate' => 1])->get()->all();
        foreach ($need_to_update as $app) {
            $this->updateApp($app['id']);
        }
    }
    public function updateApp($app_id, $version = null)
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->del($app_id . '_launcher_app_info');
        return $this->installApp($app_id, $version);
    }
    public function installApp($app_id, $version = null, $skip_info_check = false, $fake_install = false)
    {
        $app = $original_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('App not found, id=' . $app_id);
        }
        $npm = \Ministra\Lib\Npm::getInstance();
        if (!$skip_info_check) {
            $info = $npm->info($app['url'], $version);
            if (empty($info)) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to get info for ' . $app['url']);
            }
            if ($app['current_version'] == $info['version']) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Nothing to install');
            }
            $version = $info['version'];
            $conflicts = $this->getConflicts($app_id, $info['version']);
            if (!empty($conflicts)) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerConflictException('Conflicts detected', $conflicts);
            }
        }
        $this->sendToCallback('Installing ' . $app['url'] . '...');
        if (!$fake_install) {
            $result = $npm->install($app['url'], $version);
            if (empty($result)) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to install application ' . $app['url'] . '@' . $version);
            }
        } else {
            $result = true;
        }
        $update_data = ['current_version' => isset($info['version']) ? $info['version'] : ''];
        $update_data['type'] = isset($info['config']['type']) ? $info['config']['type'] : null;
        if (empty($app['alias'])) {
            $update_data['alias'] = !empty($info['name']) ? $info['name'] : $app['url'];
        }
        if (empty($app['name'])) {
            $update_data['name'] = $update_data['type'] == 'app' && isset($info['config']['name']) ? $info['config']['name'] : (!empty($info['name']) ? $info['name'] : $app['url']);
        }
        if (empty($app['description'])) {
            $update_data['description'] = isset($info['config']['description']) ? $info['config']['description'] : (isset($info['description']) ? $info['description'] : '');
        }
        $update_data['author'] = isset($info['author']) ? $info['author'] : '';
        $update_data['category'] = isset($info['config']['category']) ? $info['config']['category'] : null;
        $update_data['is_unique'] = isset($info['config']['unique']) && $info['config']['unique'] ? 1 : 0;
        $update_data['status'] = 1;
        if (!empty($info['config'])) {
            $update_data['config'] = \json_encode($info['config']);
        }
        if ($version) {
            $update_data['updated'] = 'NOW()';
        }
        if (!empty($update_data['type']) && $update_data['type'] == 'launcher') {
            $default = \trim(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('default_launcher_apps_launcher', ''));
            $current = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['type' => 'launcher', 'status' => 1])->get()->first();
            if (empty($default)) {
                $update_data['status'] = (int) empty($current);
            } else {
                if (!empty($current)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('launcher_apps', ['status' => 0], ['type' => 'launcher', 'url<>' => $default]);
                }
                $update_data['status'] = (int) ($update_data['alias'] == $default);
            }
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('launcher_apps', $update_data, ['id' => $app_id]);
        if (!empty($update_data['type']) && $update_data['type'] == 'theme') {
            try {
                $theme = new \Ministra\Lib\Theme(!empty($update_data['alias']) ? $update_data['alias'] : (!empty($app['alias']) ? $app['alias'] : ''));
                if (!empty($app['current_version'])) {
                    $current_version = $theme->getVersion();
                    $theme->setVersion($app['current_version']);
                    $theme->deleteThemeCompiledCSS();
                    $theme->setVersion($current_version);
                }
                if (!empty($version)) {
                    $theme->deleteThemeCompiledCSS();
                    $theme->setVersion($version);
                }
                $theme->generateThemeCSS();
            } catch (\Exception $exception) {
            }
        }
        $localization = $this->getAppLocalization($app_id, isset($info['version']) ? $info['version'] : null);
        if (!empty($localization)) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('launcher_apps', ['localization' => \json_encode($localization)], ['id' => $app_id]);
        }
        return $result;
    }
    public function getConflicts($app_id, $version = null)
    {
        $app = $original_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('App not found, id=' . $app_id);
        }
        $info = self::getNpmInfo($app, $version);
        if (empty($info)) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to get info for ' . $app['url']);
        }
        $dependencies = isset($info['dependencies']) ? $info['dependencies'] : [];
        $conflicts = [];
        foreach ($dependencies as $package => $version_expression) {
            $dep_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['alias' => $package])->get()->first();
            $range = new \Ministra\Lib\SemVerExpression($version_expression);
            if ($package == 'magcore-app-auth-stalker') {
                $sap_path = \realpath(join_paths(PROJECT_PATH, '/../deploy/src/sap/'));
                $sap_versions = \array_diff(\scandir($sap_path), ['.', '..']);
                if (empty($dep_app)) {
                    $dep_app = ['id' => 0, 'url' => $package];
                }
                $dep_info = self::getNpmInfo($dep_app);
                if (isset($dep_info['config']['apiVersion']) && \array_search($dep_info['config']['apiVersion'], $sap_versions) !== false) {
                    $version_expression = $dep_info['config']['apiVersion'];
                    $dep_range = new \Ministra\Lib\SemVerExpression($version_expression);
                } else {
                    $dep_range = $range;
                }
                $suitable_sap = null;
                foreach ($sap_versions as $sap_version) {
                    if ($dep_range->satisfiedBy(new \Ministra\Lib\SemVer($sap_version))) {
                        $suitable_sap = $sap_version;
                        break;
                    }
                }
                if (empty($suitable_sap)) {
                    $conflicts[] = ['alias' => $package, 'current_version' => $version_expression, 'target' => $app['url']];
                }
            } elseif ($package == 'magcore-theme') {
                $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['type' => 'theme', 'status' => 1])->get()->all();
                if (empty($dep_app) || empty($dep_app['current_version'])) {
                    continue;
                }
                foreach ($themes as $theme) {
                    $theme_info = self::getNpmInfo($theme);
                    $theme_dependencies = isset($theme_info['dependencies']) ? $theme_info['dependencies'] : [];
                    if (!isset($theme_dependencies['magcore-theme'])) {
                        continue;
                    }
                    if (!$range->satisfiedBy(new \Ministra\Lib\SemVer($dep_app['current_version']))) {
                        $conflicts[] = ['alias' => $theme['url'] . ' - ' . $package, 'current_version' => $dep_app['current_version'], 'target' => $app['url']];
                    }
                }
            }
            if (empty($dep_app) || empty($dep_app['current_version']) || !$dep_app['is_unique']) {
                continue;
            }
            if (!$range->satisfiedBy(new \Ministra\Lib\SemVer($dep_app['current_version']))) {
                $conflicts[] = ['alias' => $package, 'current_version' => $dep_app['current_version'], 'target' => $app['url']];
            }
        }
        return $conflicts;
    }
    private function getAppLocalization($app_id, $version = null)
    {
        $app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (!$version) {
            $version = $app['current_version'];
        }
        if (empty($app) || empty($app['alias'])) {
            return false;
        }
        $path = join_paths($this->getLauncherRootSystemPath(), $app['alias'], $version) . '/';
        $app_localizations = [];
        $config = \json_decode($app['config'], true);
        $entry = isset($config['uris']['app']) ? $config['uris']['app'] : 'app/';
        $dir_path = join_paths($path, $entry, 'lang') . '/';
        if (!\is_dir($dir_path)) {
            return false;
        }
        $scanned_directory = \array_diff(\scandir($dir_path), ['..', '.']);
        $languages = \array_map(function ($file) {
            return \str_replace('.json', '', $file);
        }, $scanned_directory);
        $languages = \array_merge([$this->lang, 'en'], \array_diff($languages, [$this->lang, 'en']));
        foreach ($languages as $lang) {
            if (\is_readable($dir_path . $lang . '.json')) {
                $localization = \json_decode(\file_get_contents($dir_path . $lang . '.json'), true);
                if (!empty($localization['data'][''])) {
                    $localization = $localization['data'][''];
                    if (!empty($localization[$app['name']])) {
                        $app_localizations[$lang]['name'] = $localization[$app['name']];
                    }
                    if (!empty($localization[$app['description']])) {
                        $app_localizations[$lang]['description'] = $localization[$app['description']];
                    }
                }
            }
        }
        return $app_localizations;
    }
    public function updateApps()
    {
        $system_apps = $this->getSystemApps();
        $apps = $this->getInstalledApps('app');
        $themes = $this->getInstalledApps('theme');
        $installed_apps = \array_merge($system_apps, $themes, $apps);
        $need_to_update = \array_values(\array_filter($installed_apps, function ($app) {
            return $app['current_version'] != $app['available_version'];
        }));
        foreach ($need_to_update as $app) {
            $this->sendToCallback('Updating package ' . $app['alias'] . '...');
            try {
                $this->updateApp($app['id']);
            } catch (\Ministra\Lib\SmartLauncherAppsManagerException $e) {
                $this->sendToCallback('Error: ' . $e->getMessage());
            }
        }
    }
    public function getSystemApps()
    {
        $apps = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->not_in('type', ['app', 'theme'])->get()->all();
        $root_path = $this->getLauncherRootSystemPath();
        return \array_values(\array_filter($apps, function ($app) use($root_path) {
            return !empty($app['alias']) && $app['status'] == 1 && \is_dir(join_paths($root_path, $app['type'] == 'plugin' ? 'plugins' : '', $app['alias'], $app['current_version']));
        }));
    }
    public function getInstalledApps($type = 'app')
    {
        $apps = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['type' => $type])->get()->all();
        $metapackage_info = \json_decode(\file_get_contents(join_paths($this->getLauncherRootSystemPath(), 'package.json')), true);
        $app_names = \array_map(function ($app) {
            return $app['alias'];
        }, $apps);
        if ($metapackage_info && isset($metapackage_info['order'])) {
            $order = $metapackage_info['order'];
            $ordered_apps = [];
            foreach ($order as $alias) {
                if (($idx = \array_search($alias, $app_names)) !== false) {
                    $ordered_apps[] = $apps[$idx];
                    \array_splice($apps, $idx, 1);
                    \array_splice($app_names, $idx, 1);
                }
            }
            $apps = \array_merge($ordered_apps, \array_values($apps));
        }
        $root_path = $this->getLauncherRootSystemPath();
        return \array_values(\array_filter($apps, function ($app) use($root_path) {
            return !empty($app['alias']) && $app['status'] == 1 && \is_dir(join_paths($root_path, $app['type'] == 'plugin' ? 'plugins' : '', $app['alias'], $app['current_version']));
        }));
    }
    public function getFullAppDependencies($app_id)
    {
        $app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['id' => $app_id])->get()->first();
        if (empty($app)) {
            return false;
        }
        $info = \file_get_contents(join_paths($this->getLauncherRootSystemPath(), $app['type'] == 'plugin' ? 'plugins' : '', $app['alias'], $app['current_version'], 'package.json'));
        if (!$info) {
            return false;
        }
        $info = \json_decode($info, true);
        if (!$info) {
            return false;
        }
        $full_dependencies = [];
        $dependencies = isset($info['dependencies']) ? $info['dependencies'] : [];
        foreach ($dependencies as $package => $version_expression) {
            $dep_app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['alias' => $package])->get()->first();
            if (empty($dep_app) || empty($dep_app['current_version'])) {
                continue;
            }
            $range = new \Ministra\Lib\SemVerExpression($version_expression);
            if ($range->satisfiedBy(new \Ministra\Lib\SemVer($dep_app['current_version']))) {
                $full_dependencies[$package] = $dep_app['current_version'];
            } elseif (!$dep_app['is_unique']) {
                $dep_app_path = join_paths($this->getLauncherRootSystemPath(), $dep_app['type'] == 'plugin' ? 'plugins' : '', $dep_app['alias']);
                if (!$dep_app_path) {
                    throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to find app path ' . $dep_app['alias'] . ' path');
                }
                $files = \array_diff(\scandir($dep_app_path), ['.', '..']);
                $max_version = null;
                foreach ($files as $file) {
                    if (\is_dir($dep_app_path . '/' . $file)) {
                        $semver = new \Ministra\Lib\SemVer($file);
                        if ($range->satisfiedBy($semver)) {
                            if (\is_null($max_version)) {
                                $max_version = $semver->getVersion();
                            } else {
                                if (\Ministra\Lib\SemVer::gt($semver->getVersion(), $max_version)) {
                                    $max_version = $semver->getVersion();
                                }
                            }
                        }
                    }
                }
                if (\is_null($max_version)) {
                    throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unresolved dependency ' . $dep_app['alias'] . ' for ' . $app['alias']);
                }
                $full_dependencies[$package] = $max_version;
            } else {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unresolved dependency ' . $dep_app['alias'] . ' for ' . $app['alias']);
            }
        }
        return $full_dependencies;
    }
    public function initApps()
    {
        $apps = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->count()->get()->counter();
        if ($apps == 0 || !\file_exists(__DIR__ . '/../../../stalker_launcher_apps/package.json')) {
            return $this->resetApps();
        }
        return false;
    }
    public function resetApps($metapackage = null)
    {
        $orig_metapackage_name = $orig_metapackage = $metapackage;
        if (\strpos($orig_metapackage, '@')) {
            list($orig_metapackage_name, $ver) = \explode('@', $orig_metapackage);
        }
        if (\is_null($metapackage)) {
            $metapackage = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_base_metapackage', '');
        }
        if (empty($metapackage)) {
            return false;
        }
        if (!\strpos($metapackage, '@')) {
            $stalker_version = \file_get_contents(__DIR__ . '/../../c/version.js');
            $start = \strpos($stalker_version, "'") + 1;
            $end = \strrpos($stalker_version, "'");
            $stalker_version = \substr($stalker_version, $start, $end - $start);
            $metapackage_name = $metapackage;
            $metapackage = $metapackage_name . '@' . $stalker_version;
        } else {
            list($metapackage_name, $stalker_version) = \explode('@', $metapackage);
        }
        if ($stalker_version) {
            $exploded_version = \explode('-', $stalker_version);
            $stalker_version = $exploded_version[0];
        }
        $npm = \Ministra\Lib\Npm::getInstance();
        if (\is_null($orig_metapackage)) {
            $latest_version = $npm->getLatestVersion($metapackage_name);
            if (!$latest_version) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('A metapackage not found for release ' . $stalker_version);
            }
            $stalker_version = $latest_version;
        }
        $apps_path = $this->getLauncherRootSystemPath();
        $this->sendToCallback('Removing apps...');
        if ($apps_path) {
            $ignore = ['.', '..'];
            if ($orig_metapackage) {
                $ignore[] = $orig_metapackage_name;
            }
            $files = \array_diff(\scandir($apps_path), $ignore);
            foreach ($files as $file) {
                $this->sendToCallback('Removing package ' . $file . '...');
                self::delTree($apps_path . '/' . $file);
                if (\strpos($file, 'theme') !== false) {
                    try {
                        $theme = new \Ministra\Lib\Theme($file);
                        $theme->deleteThemeCompiledCSS();
                    } catch (\Exception $e) {
                    }
                }
            }
            if (\is_file($apps_path . '/package.json')) {
                \unlink($apps_path . '/package.json');
            }
        }
        $this->resetAppsCache();
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->truncate('launcher_apps');
        $this->sendToCallback('Installing metapackage ' . $metapackage_name . (\is_null($orig_metapackage) ? '@' . $stalker_version : '') . '...');
        $result = $this->addApplication($metapackage_name, true, !\is_null($orig_metapackage), \is_null($orig_metapackage) ? $stalker_version : null);
        $source = join_paths($apps_path, $metapackage_name, \is_null($orig_metapackage) ? $stalker_version : '', 'package.json');
        $dest = join_paths($apps_path, 'package.json');
        \copy($source, $dest);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('launcher_apps', ['url' => $metapackage_name]);
        $this->syncApps();
        return (bool) $result;
    }
    public function syncApps()
    {
        $repos = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_extra_metapackages', []);
        $npm = new \Ministra\Lib\Npm();
        foreach ($repos as $repo) {
            $info = $npm->info($repo);
            if (!$info) {
                continue;
            }
            $apps = isset($info['dependencies']) ? $info['dependencies'] : [];
            if (\is_string($apps)) {
                $apps = [$apps];
            }
            foreach ($apps as $app => $ver) {
                $this->addApplication($app);
            }
        }
    }
    public function addApplication($url, $autoinstall = false, $skip_info_check = false, $version = null, $fake_install = false)
    {
        $app = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where(['url' => $url])->get()->first();
        if (!empty($app)) {
            return false;
        }
        $app_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('launcher_apps', ['url' => $url, 'added' => 'NOW()'])->insert_id();
        if ($autoinstall) {
            $this->installApp($app_id, $version, $skip_info_check, $fake_install);
        } else {
            $this->getAppInfo($app_id, true);
        }
        return $app_id;
    }
    public function getSnapshot()
    {
        $snapshot = ['name' => 'mag-apps-snapshot', 'version' => '0.0.1', 'dependencies' => []];
        $system_apps = $this->getSystemApps();
        $apps = $this->getInstalledApps('app');
        $themes = $this->getInstalledApps('theme');
        $dependencies = \array_merge($system_apps, $themes, $apps);
        foreach ($dependencies as $dependency) {
            $snapshot['dependencies'][$dependency['url']] = $dependency['current_version'];
        }
        return \json_encode($snapshot, 192);
    }
    public function restoreFromSnapshot($json)
    {
        $package = \json_decode($json, true);
        if (!$package) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to decode JSON file');
        }
        if (empty($package['name']) || empty($package['version']) || empty($package['dependencies'])) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Required fields in JSON file are missing.');
        }
        $apps_path = $this->getLauncherRootSystemPath();
        if (!$apps_path) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to get launcher apps path');
        }
        $app_dir = join_paths($apps_path, $package['name']);
        if (!\is_dir($app_dir)) {
            \umask(0);
            $mkdir = \mkdir($app_dir, 0777);
            if (!$mkdir) {
                throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to create metapackage folder');
            }
        }
        $file_result = \file_put_contents(join_paths($app_dir, 'package.json'), $json);
        if (!$file_result) {
            throw new \Ministra\Lib\SmartLauncherAppsManagerException('Unable to create package.json in metapackage folder');
        }
        return $this->resetApps($package['name']);
    }
}
