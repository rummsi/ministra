<?php

namespace Ministra\Storage\Lib;

class User
{
    private static $instance = null;
    private $mac;
    private $storage_name;
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct()
    {
        $this->mac = !empty($_SERVER['HTTP_X_FROM']) ? $_SERVER['HTTP_X_FROM'] : '';
    }
    public function getMac()
    {
        return $this->mac;
    }
    public function checkHome()
    {
        if (empty($this->mac)) {
            return false;
        }
        $home = NFS_HOME_PATH . $this->mac;
        $mac = $this->mac;
        $dir_to_clean = \array_filter(\array_map(function ($func) use($mac, $home) {
            $path = NFS_HOME_PATH . $func($mac);
            return $home !== $path && \is_dir($path) ? $path : '';
        }, ['strtolower', 'strtoupper']));
        $dir_to_clean[] = $home;
        foreach ($dir_to_clean as $dir) {
            if ($dir !== $home) {
                \exec("rm -rf {$dir}", $out, $exitCode);
            } else {
                $files = \scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        \unlink($dir . '/' . $file);
                    }
                }
            }
            \exec("ls -la {$dir}", $out, $exitCode);
        }
        if (!\is_dir($home)) {
            \umask(0);
            if (!\mkdir($home, 0777)) {
                throw new \Ministra\Storage\Lib\IOException('Could not create directory ' . $home . ' on ' . $this->storage_name);
            }
        }
        return true;
    }
    public function setStorageName($storage_name)
    {
        $this->storage_name = $storage_name;
    }
}
