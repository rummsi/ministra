<?php

namespace Ministra\Lib;

use Exception;
use Imagick;
use ImagickException;
use Leafo\ScssPhp\Compiler;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Theme
{
    private $alias = '';
    private $name = '';
    private $version = '';
    private $variables = array();
    private $original_variables = array();
    private $is_default = false;
    private $default_theme = '';
    private $package_info = null;
    private $screen_width = 1920;
    private $screen_height = 1080;
    private $last_updated = 0;
    private $screen_map = array(480 => '720x480', 576 => '720x576', 720 => '1280x720', 1080 => '1920x1080');
    public function __construct($alias, $version = '')
    {
        $this->alias = $alias;
        $this->version = $version;
        $this->init();
    }
    private function init()
    {
        $where = ['type' => 'theme', 'alias' => $this->alias];
        if (!empty($this->version)) {
            $where['current_version'] = $this->version;
        }
        $theme = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('launcher_apps')->where($where)->get()->first();
        if (!$theme) {
            throw new \Ministra\Lib\ThemeNotFound('Theme not found');
        }
        $this->name = $theme['name'];
        $this->last_updated = \strtotime($theme['updated']);
        $this->setVersion($theme['current_version']);
        $customization = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('themes')->where(['alias' => $this->alias])->get()->first();
        $this->default_theme = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_launcher_template');
        $this->is_default = $this->default_theme == 'smart_launcher:' . $this->alias;
        if ($customization) {
            if ($variables = \json_decode($customization['variables'], true)) {
                $this->variables = $variables;
            }
            $customization_updated_time = \strtotime($customization['updated']);
            $customization_added_time = \strtotime($customization['updated']);
            $this->last_updated = \max($this->last_updated, $customization_updated_time, $customization_added_time);
        }
        $theme_path = $this->getPackagePath();
        if ($theme_path) {
            $variables_path = $theme_path . '/src/vars.json';
            if (\is_readable($variables_path)) {
                if ($variables = \json_decode(\file_get_contents($variables_path), true)) {
                    $this->original_variables = $variables;
                }
            }
        }
    }
    private function getPackagePath()
    {
        $info = $this->getPackageInfo();
        if (!empty($info) && $info['app_path'] && $info['installed']) {
            return $info['app_path'];
        }
        return false;
    }
    private function getPackageInfo()
    {
        if ($this->package_info) {
            $info = $this->package_info;
        } else {
            try {
                $info = \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getAppInfoByUrl($this->alias);
            } catch (\Exception $e) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47($e->getMessage());
            }
        }
        if (!empty($info)) {
            $this->package_info = $info;
        }
        return isset($info) ? $info : false;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setAsDefault()
    {
        $this->is_default = true;
        $this->save();
    }
    private function save()
    {
        $customization = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('themes')->where(['alias' => $this->alias])->get()->first();
        if ($this->is_default && $this->default_theme != 'smart_launcher:' . $this->alias) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('settings', ['default_launcher_template' => 'smart_launcher:' . $this->alias]);
        }
        if ($customization) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('themes', ['variables' => \json_encode($this->variables), 'updated' => 'NOW()'], ['id' => $customization['id']]);
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('themes', ['alias' => $this->alias, 'variables' => \json_encode($this->variables), 'added' => 'NOW()']);
        }
    }
    public function saveBackgroundImage($file, $width, $height)
    {
        if (!\in_array($height, [480, 576, 720, 1080])) {
            throw new \Ministra\Lib\ThemeImageException('Not supported resolution');
        }
        $ext = \image_type_to_extension(\exif_imagetype($file), false);
        if (!$ext) {
            $ext = 'jpg';
        }
        try {
            $image = new \Imagick($file);
        } catch (\ImagickException $e) {
            throw new \Ministra\Lib\ThemeImageException('Imagick error: ' . $e->getMessage());
        }
        $resize = $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
        if (!$resize) {
            throw new \Ministra\Lib\ThemeImageException('Unable to resize image');
        }
        $save_path = join_paths(PROJECT_PATH, '../misc/themes', $this->alias, $height);
        if (!\is_dir($save_path)) {
            \umask(0);
            \mkdir($save_path, 0777, true);
        }
        $filename = 'bg_' . \md5_file($file) . '.' . $ext;
        $write = $image->writeImage($save_path . '/' . $filename);
        if (!$write) {
            throw new \Ministra\Lib\ThemeImageException('Unable to save re-sized image');
        }
        if (!isset($this->variables['bodyBgFilename']) || !\is_array($this->variables['bodyBgFilename'])) {
            $this->variables['bodyBgFilename'] = [];
        }
        $this->variables['bodyBgFilename'][$height] = $filename;
        $this->setVariable('bodyBgFilename', $this->variables['bodyBgFilename']);
        $image->destroy();
        if ($height == 1080) {
            $this->saveBackgroundImage($file, 1280, 720);
        } elseif ($height == 576) {
            $this->saveBackgroundImage($file, 720, 480);
        }
        $this->save();
        if (\is_file($file)) {
            \unlink($file);
        }
        return true;
    }
    public function setVariable($name, $value, $flush = false)
    {
        $this->variables[$name] = $value;
        if ($flush) {
            $this->save();
        }
    }
    public function saveLogo($file, $align = 'left')
    {
        $ext = \image_type_to_extension(\exif_imagetype($file), false);
        if (!$ext) {
            $ext = 'jpg';
        }
        $filename = 'logo_' . \md5_file($file) . '.' . $ext;
        foreach ([480, 576, 720, 1080] as $height) {
            $save_path = join_paths(PROJECT_PATH, '../misc/themes', $this->alias, $height);
            if (!\is_dir($save_path)) {
                \umask(0);
                \mkdir($save_path, 0777, true);
            }
            if (!\copy($file, $save_path . '/' . $filename)) {
                throw new \Ministra\Lib\ThemeImageException('Unable to save logo image');
            }
        }
        $this->setVariable('logoAlign', $align);
        $this->setVariable('logoFilename', $filename);
        $this->save();
        if (\is_file($file)) {
            \unlink($file);
        }
        return true;
    }
    public function reset()
    {
        $this->setVariables([]);
    }
    public function resetBackgroundImage($width, $height)
    {
        if (isset($this->variables['bodyBgFilename'][$height])) {
            unset($this->variables['bodyBgFilename'][$height]);
            $this->setVariable('bodyBgFilename', $this->variables['bodyBgFilename'], true);
        }
    }
    public function resetParam($name)
    {
        if (isset($this->variables[$name])) {
            unset($this->variables[$name]);
            $this->setVariable($name, '', true);
        }
    }
    public function getRgbVariables()
    {
        $converted_variables = [];
        $variables = $this->getVariables();
        foreach ($variables as $name => $value) {
            if ($value['type'] == 'color') {
                $converted_variables[$name] = ['type' => 'color', 'value' => $this->colorToRgba($value)];
            } else {
                $converted_variables[$name] = $value;
            }
        }
        return $converted_variables;
    }
    public function getVariables()
    {
        $original_variables = $this->original_variables;
        if (!empty($original_variables['bodyBgFilename']['value'])) {
            $bg_image = $this->getOriginalBackgroundImageUrl($this->screen_width, $this->screen_height);
            if ($bg_image) {
                $original_variables['bodyBgFilename']['value'] = $bg_image;
            }
        }
        $custom_variables = $this->variables;
        if (!empty($this->variables['bodyBgFilename'])) {
            $bg_image = $this->getCustomBackgroundImageUrl($this->screen_width, $this->screen_height);
            if ($bg_image) {
                $custom_variables['bodyBgFilename'] = ['type' => 'string', 'value' => $bg_image];
            } else {
                unset($custom_variables['bodyBgFilename']);
            }
        } elseif (isset($this->variables['bodyBgFilename']) && !$this->variables['bodyBgFilename']) {
            unset($custom_variables['bodyBgFilename']);
        }
        if (!empty($this->variables['logoFilename'])) {
            $logo_image = $this->getCustomLogoImageUrl($this->screen_width, $this->screen_height);
            if ($logo_image) {
                $custom_variables['logoFilename'] = ['type' => 'string', 'value' => $logo_image];
            } else {
                unset($custom_variables['logoFilename']);
            }
        } elseif (isset($this->variables['logoFilename']) && !$this->variables['logoFilename']) {
            unset($custom_variables['logoFilename']);
        }
        if (!empty($this->variables['logoAlign'])) {
            $custom_variables['logoAlign'] = ['type' => 'string', 'value' => $this->variables['logoAlign']];
        } elseif (isset($this->variables['logoAlign']) && !$this->variables['logoAlign']) {
            unset($custom_variables['logoAlign']);
        }
        $merged = \array_merge($original_variables, $custom_variables);
        return $merged;
    }
    public function setVariables($variables)
    {
        $this->variables = $variables;
        $this->save();
    }
    public function getOriginalBackgroundImageUrl($width = 1920, $height = 1080)
    {
        $resolution = $this->pickImageResolution($width, $height);
        $theme = $this->getPackageInfo();
        $theme_path = $this->getPackagePath();
        $image_folder = join_paths($theme_path, 'img', $resolution);
        if (\is_dir($image_folder) && isset($this->original_variables['bodyBgFilename']['value']) && \is_readable(join_paths($image_folder, $this->original_variables['bodyBgFilename']['value']))) {
            return \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getLauncherRootWebPath() . '/' . join_paths($this->alias, $theme['current_version'], '/img/', $resolution, $this->original_variables['bodyBgFilename']['value']);
        }
    }
    private function pickImageResolution($width, $height)
    {
        if ($height > 720) {
            return 1080;
        } elseif ($height > 576) {
            return 720;
        } elseif ($height > 480) {
            return 576;
        }
        return 480;
    }
    public function getCustomBackgroundImageUrl($width = 1920, $height = 1080)
    {
        $resolution = $this->pickImageResolution($width, $height);
        $customization_path = join_paths($this->getCustomizationPath(), $resolution);
        if (\is_dir($customization_path) && !empty($this->variables['bodyBgFilename']) && !empty($this->variables['bodyBgFilename'][$height]) && \is_readable(join_paths($customization_path, $this->variables['bodyBgFilename'][$height]))) {
            return \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getProtocol() . \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getHost() . '/' . join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url'), 'misc/themes', $this->alias, $resolution, $this->variables['bodyBgFilename'][$height]);
        }
    }
    private function getCustomizationPath()
    {
        $themes_path = join_paths(PROJECT_PATH, '/../misc/themes/', $this->alias) . '/';
        if (!\is_dir($themes_path)) {
            \umask(0);
            \mkdir($themes_path, 0777, true);
        }
        return \realpath($themes_path);
    }
    public function getCustomLogoImageUrl($width = 1920, $height = 1080)
    {
        $resolution = $this->pickImageResolution($width, $height);
        $customization_path = join_paths($this->getCustomizationPath(), $resolution);
        if (\is_dir($customization_path) && !empty($this->variables['logoFilename']) && \is_readable(join_paths($customization_path, $this->variables['logoFilename']))) {
            return \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getProtocol() . \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getHost() . '/' . join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url'), 'misc/themes', $this->alias, $resolution, $this->variables['logoFilename']);
        }
    }
    private function colorToRgba($raw)
    {
        return 'rgba(' . $raw['r'] . ', ' . $raw['g'] . ', ' . $raw['b'] . ', ' . (isset($raw['o']) ? $raw['o'] : 1) . ')';
    }
    public function getRgbaHexVariables()
    {
        $converted_variables = [];
        $variables = $this->getVariables();
        foreach ($variables as $name => $value) {
            if ($value['type'] == 'color') {
                $converted_variables[$name] = ['type' => 'color', 'value' => $this->colorToRgbaHex($value)];
            } else {
                $converted_variables[$name] = $value;
            }
        }
        return $converted_variables;
    }
    private function colorToRgbaHex($raw)
    {
        return '0x' . \sprintf('%02X%02X%02X%02X', $raw['r'], $raw['g'], $raw['b'], (isset($raw['o']) ? $raw['o'] : 1) * 255);
    }
    public function getArgbHexVariables()
    {
        $converted_variables = [];
        $variables = $this->getVariables();
        foreach ($variables as $name => $value) {
            if ($value['type'] == 'color') {
                $converted_variables[$name] = ['type' => 'color', 'value' => $this->colorToArgbHex($value)];
            } else {
                $converted_variables[$name] = $value;
            }
        }
        return $converted_variables;
    }
    private function colorToArgbHex($raw)
    {
        return '0x' . \sprintf('%02X%02X%02X%02X', (isset($raw['o']) ? $raw['o'] : 1) * 255, $raw['r'], $raw['g'], $raw['b']);
    }
    public function getVersion()
    {
        return $this->version;
    }
    public function setVersion($version)
    {
        $this->version = $version;
    }
    public function generateThemeCSS()
    {
        if (!empty($this->getThemeCSSRoot())) {
            $this->deleteThemeCompiledCSS();
        }
        try {
            foreach ($this->screen_map as $height => $resolution) {
                $this->setScreenHeight($height);
                $this->getCssUrl();
            }
        } catch (\Exception $exception) {
        }
    }
    public function getThemeCSSRoot($version = '')
    {
        if (empty($version) && empty($this->version)) {
            return '';
        }
        $save_path = join_paths(PROJECT_PATH, '../misc/themes', $this->alias, 'build', !empty($version) ? $version : $this->version);
        return \is_dir($save_path) ? $save_path : '';
    }
    public function deleteThemeCompiledCSS()
    {
        $save_path = $this->getThemeCSSRoot();
        if (\is_dir($save_path)) {
            delTree($save_path);
        }
    }
    public function setScreenHeight($screen_height)
    {
        if (!isset($this->screen_map[$screen_height])) {
            throw new \Ministra\Lib\ThemeNotFound('Not supported resolution');
        }
        $this->setResolution($this->screen_map[$screen_height]);
    }
    public function setResolution($resolution)
    {
        if (\strpos($resolution, 'x')) {
            $separator = 'x';
        } elseif (\strpos($resolution, 'X')) {
            $separator = 'X';
        } elseif (\strpos($resolution, '*')) {
            $separator = '*';
        }
        if (isset($separator)) {
            list($this->screen_width, $this->screen_height) = \explode($separator, $resolution);
        }
    }
    public function getCssUrl()
    {
        $theme = $this->getPackageInfo();
        $save_path = join_paths(PROJECT_PATH, '/../misc/themes/', $this->alias, '/build/', $this->version);
        if (!\is_dir($save_path)) {
            $theme_path = $this->getPackagePath();
            self::copyRecursive($theme_path, $save_path);
            $this->checkThemeBase($save_path);
        }
        if (!\is_readable($save_path . '/src/' . $this->screen_height . '.scss')) {
            return \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getLauncherRootWebPath() . '/' . join_paths($this->alias, $this->version, $this->screen_height . '.css');
        }
        $scss = new \Leafo\ScssPhp\Compiler();
        $css_file = $save_path . '/' . $this->screen_height . '-' . $this->last_updated . '.css';
        if (!\is_readable($css_file)) {
            $variables = $this->convertVariables();
            \file_put_contents($save_path . '/src/vars.scss', $variables);
            \chdir($save_path . '/src/');
            $css = $scss->compile(\file_get_contents($save_path . '/src/' . $this->screen_height . '.scss'));
            $this->clearByMask($save_path, $this->screen_height . '-');
            \file_put_contents($css_file, $css);
        }
        return \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getProtocol() . \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getHost() . '/' . join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url'), 'misc/themes/', $this->alias, '/build/', $this->version, $this->screen_height . '-' . $this->last_updated . '.css');
    }
    private static function copyRecursive($src, $dst)
    {
        $dir = \opendir($src);
        \mkdir($dst, 0777, true);
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
    public function checkThemeBase($theme_path)
    {
        $node_path = join_paths($theme_path, 'node_modules');
        if (!\is_readable($node_path)) {
            \mkdir($node_path, 0777, true);
        }
        $node_path = \realpath($node_path);
        $theme_info = $this->getPackageInfo();
        $info = \Ministra\Lib\SmartLauncherAppsManager::getNpmInfo($theme_info);
        if (empty($node_path) || $info['name'] !== $this->name || empty($info['dependencies'])) {
            return;
        }
        foreach ($info['dependencies'] as $dependency_alias => $dependency_ver) {
            $dependency_info = \Ministra\Lib\SmartLauncherAppsManager::getInstance()->getAppInfoByUrl($dependency_alias);
            $dependency_path = join_paths($node_path, $dependency_alias);
            if (empty($dependency_info['app_path']) || empty($dependency_info['installed']) || \is_readable($dependency_path)) {
                continue;
            }
            \symlink($dependency_info['app_path'], $dependency_path);
        }
    }
    private function convertVariables()
    {
        $scss_variables = '';
        $variables = $this->getVariables();
        if (isset($variables['logoFilename'])) {
            $parts = \parse_url($variables['logoFilename']['value']);
            $variables['logoFilename']['value'] = \str_repeat('../', 9) . $parts['path'];
        }
        if (isset($variables['bodyBgFilename'])) {
            $parts = \parse_url($variables['bodyBgFilename']['value']);
            $variables['bodyBgFilename']['value'] = \str_repeat('../', 9) . $parts['path'];
        }
        foreach ($variables as $name => $value) {
            $scss_variables .= '$' . $name . ': ' . $this->convertValueToScss($value) . ";\n";
        }
        return $scss_variables;
    }
    private function convertValueToScss($value)
    {
        if ($value['type'] == 'string') {
            return "'" . $value['value'] . "'";
        } elseif ($value['type'] == 'color') {
            return $this->colorToRgba($value);
        }
        return $value['value'];
    }
    private function clearByMask($path, $mask)
    {
        if (!\is_dir($path)) {
            return;
        }
        $files = \array_diff(\scandir($path), ['.', '..']);
        foreach ($files as $file) {
            if (\strpos($file, $mask) === 0) {
                \unlink($path . '/' . $file);
            }
        }
    }
    private function getCustomizedVariables()
    {
        return $this->variables;
    }
}
