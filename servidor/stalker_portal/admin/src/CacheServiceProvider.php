<?php

namespace Ministra\Admin;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Moust\Silex\Provider\CacheServiceProvider as MCacheServiceProvider;
use Pimple\Container;
class CacheServiceProvider extends \Moust\Silex\Provider\CacheServiceProvider
{
    public function register(\Pimple\Container $app)
    {
        $options = $app->offsetExists('memcache.options') ? $app['memcache.options'] : ['memcache.options' => ['host' => 'localhost', 'default_timeout' => 1800]];
        if (null !== ($instance = $this->checkMemcacheConnect($options['memcache.options']['host']))) {
            $app['caches.options'] = ['memcache' => ['driver' => \get_class($instance) === 'Memcache' ? 'memcache' : 'memcached', 'memcache' => function () use($instance) {
                return $instance;
            }]];
        } else {
            $app['caches.options'] = ['filesystem' => ['driver' => 'file', 'cache_dir' => __DIR__ . '/../resources/cache/admin']];
        }
        return parent::register($app);
    }
    private function checkMemcacheConnect($hosts)
    {
        if (!\class_exists('Memcache') && !\class_exists('Memcached')) {
            return null;
        }
        $memcache = \class_exists('Memcached') ? new \Memcached() : new \Memcache();
        if (!\is_array($hosts)) {
            $hosts = [$hosts];
        }
        $countDisabled = 0;
        foreach ($hosts as $host) {
            if (!$memcache->addServer($host, 11211)) {
                ++$countDisabled;
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47('Could not connect to memcached. Host: ' . $host);
            }
            if (!$memcache->set('test', '123')) {
                ++$countDisabled;
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47('Could not connect to memcached. Host: ' . $host);
            }
        }
        return $countDisabled === \count($hosts) ? null : $memcache;
    }
}
