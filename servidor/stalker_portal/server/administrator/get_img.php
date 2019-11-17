<?php

\session_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
\Ministra\Lib\Admin::checkAuth();
$image_url = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_info_provider', 'kinopoisk') == 'kinopoisk' ? 'kinopoisk.ru/' : 'image.tmdb.org/';
if ((\strpos($_GET['url'], 'http://') === 0 || \strpos($_GET['url'], 'https://') === 0) && \strpos($_GET['url'], $image_url)) {
    echo \file_get_contents($_GET['url']);
}
