<?php

\session_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\Itv;
use Ministra\Lib\KinopoiskException;
use Ministra\Lib\Logger;
use Ministra\Lib\Module;
use Ministra\Lib\Radio;
use Ministra\Lib\VClubinfo;
use Ministra\Lib\Video;
\Ministra\Lib\Admin::checkAuth();
\ob_start();
$response = [];
if ($_GET['get'] == 'kinopoisk_info' || $_GET['get'] == 'kinopoisk_rating' || $_GET['get'] == 'kinopoisk_info_by_id') {
    try {
        if ($_GET['get'] == 'kinopoisk_info') {
            $response['result'] = \Ministra\Lib\VClubinfo::getInfoByName($_GET['oname']);
        } elseif ($_GET['get'] == 'kinopoisk_rating') {
            $response['result'] = \Ministra\Lib\VClubinfo::getRatingByName($_GET['oname']);
        } elseif ($_GET['get'] == 'kinopoisk_info_by_id') {
            $response['result'] = \Ministra\Lib\VClubinfo::getInfoById($_GET['kinopoisk_id']);
        }
    } catch (\Ministra\Lib\KinopoiskException $e) {
        echo $e->getMessage();
        $logger = new \Ministra\Lib\Logger();
        $logger->setPrefix('vclubinfo_');
        $logger->error(\sprintf("[%s] - %s - \"%s\"\n", \date('r'), $e->getMessage(), \base64_encode($e->getResponse())));
    }
} elseif ($_GET['get'] == 'tv_services') {
    $response['result'] = \Ministra\Lib\Itv::getServices();
} elseif ($_GET['get'] == 'video_services') {
    $response['result'] = \Ministra\Lib\Video::getServices();
} elseif ($_GET['get'] == 'radio_services') {
    $response['result'] = \Ministra\Lib\Radio::getServices();
} elseif ($_GET['get'] == 'module_services') {
    $response['result'] = \Ministra\Lib\Module::getServices();
} elseif ($_GET['get'] == 'option_services') {
    $option_services = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('option_services', []);
    $response['result'] = \array_map(function ($item) {
        return ['id' => $item, 'name' => $item];
    }, $option_services);
}
$output = \ob_get_contents();
\ob_end_clean();
if ($output) {
    $response['output'] = $output;
}
echo \json_encode($response);
