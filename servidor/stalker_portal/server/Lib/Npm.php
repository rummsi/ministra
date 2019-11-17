<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class Npm
{
    private static $instance = null;
    private $app_path;
    private $plugins_path;
    public function __construct()
    {
        $this->app_path = join_paths(PROJECT_PATH, '/../../', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('launcher_apps_path', 'stalker_launcher_apps/'));
        $this->plugins_path = join_paths($this->app_path, 'plugins');
        $registry = \exec('npm get registry');
        if ($registry != \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('npm_registry', 'http://registry.npmjs.org/')) {
            \system('npm cache clean 2>/dev/null');
            \system('npm set registry ' . \escapeshellarg(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('npm_registry', 'http://registry.npmjs.org/')) . ' 2>/dev/null');
        }
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function install($package, $version = null)
    {
        \ob_start();
        if (!\is_null($version)) {
            $package .= '@' . $version;
        }
        \system('cd ' . $this->app_path . '; npm install ' . \escapeshellarg($package) . ' --production 2>/dev/null');
        $plain = \trim(\ob_get_contents());
        \ob_clean();
        $this->relocatePackages();
        return !empty($plain);
    }
    private function relocatePackages($path = null, $package_order = null)
    {
        if (\is_null($path)) {
            $path = $this->app_path;
        }
        if (\is_null($package_order)) {
            $package_order = [];
        }
        $packages_path = \realpath(join_paths($path, 'node_modules'));
        if (!$packages_path) {
            return;
        }
        $scanned_directory = \array_diff(\scandir($packages_path), ['..', '.', '.bin']);
        $scanned_directory = \array_merge($package_order, \array_diff($scanned_directory, $package_order));
        foreach ($scanned_directory as $dir) {
            $full_path = join_paths($packages_path, $dir);
            if (\is_dir($full_path)) {
                $package_json_path = join_paths($full_path, 'package.json');
                if (\is_readable($package_json_path)) {
                    $info = \file_get_contents($package_json_path);
                    $info = \json_decode($info, true);
                    if (empty($info)) {
                        continue;
                    }
                }
                if (!isset($info['version'])) {
                    continue;
                }
                try {
                    $version = new \Ministra\Lib\SemVer($info['version']);
                    $ver = $version->getVersion();
                } catch (\Ministra\Lib\SemVerException $e) {
                    throw new \Ministra\Lib\NodeException($e->getMessage());
                }
                $package_order = isset($info['dependencies']) ? \array_keys($info['dependencies']) : null;
                $this->relocatePackages($full_path, $package_order);
                \umask(0);
                $target_path = join_paths(isset($info['config']['type']) && $info['config']['type'] == 'plugin' ? $this->plugins_path : $this->app_path, $dir, $ver);
                if ($dir == 'magcore-theme-base' && !\is_dir($target_path)) {
                    \mkdir($target_path, 0777, true);
                    self::copyRecursive($full_path, $target_path);
                } else {
                    if (!\is_dir($target_path)) {
                        \mkdir($target_path, 0777, true);
                        \rename($full_path, $target_path);
                    } else {
                        self::delTree($full_path);
                    }
                }
                \Ministra\Lib\SmartLauncherAppsManager::getInstance()->addApplication($dir, true, false, $ver, true);
            }
        }
    }
    private static function copyRecursive($src, $dst)
    {
        $dir = \opendir($src);
        if (!\is_dir($dst)) {
            \mkdir($dst);
        }
        while (false !== ($file = \readdir($dir))) {
            if ($file != '.' && $file != '..') {
                if (\is_dir($src . '/' . $file)) {
                    self::copyRecursive($src . '/' . $file, $dst . '/' . $file);
                } else {
                    \copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        \closedir($dir);
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
    public function update($package)
    {
        \ob_start();
        \system('cd ' . $this->app_path . '; npm update ' . \escapeshellarg($package) . ' --depth 0 2>/dev/null');
        $plain = \trim(\ob_get_contents());
        \ob_clean();
        $this->relocatePackages();
        return !empty($plain);
    }
    public function getLatestVersion($packageName)
    {
        \ob_start();
        $packageName .= '@latest';
        $version = \system('npm view ' . \escapeshellarg($packageName) . ' version 2>/dev/null');
        \ob_end_clean();
        return $version;
    }
    public function info($package, $version = null)
    {
        \ob_start();
        if (!\is_null($version)) {
            $package .= '@' . $version;
        }
        \system('npm view ' . \escapeshellarg($package) . ' --json 2>/dev/null');
        $plain = \trim(\ob_get_contents());
        \ob_clean();
        $info = \json_decode($plain, true);
        if (empty($info)) {
            return false;
        }
        return $info;
    }
}
