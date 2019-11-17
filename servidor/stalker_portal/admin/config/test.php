<?php

if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
$app['db.host'] = \getenv('MYSQL_HOST');
$app['db.port'] = \getenv('MYSQL_PORT');
$app['db.user'] = \getenv('MYSQL_USER');
$app['db.password'] = \getenv('MYSQL_PASSWORD');
$app['db.dbname'] = \getenv('MYSQL_DB_NAME');
$app['memcache.options'] = ['memcache.options' => ['host' => \getenv('MEMCACHE_HOST'), 'default_timeout' => \getenv('MEMCACHE_TIMEOUT')]];
