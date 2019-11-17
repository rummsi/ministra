<?php

namespace Ministra\Admin\Controller;

use Ministra\Admin\Interfaces\ActivityType;
use Ministra\Admin\Interfaces\LicenseKeyStatus;
use Ministra\Admin\Service\Statistic\DevicesStatistic;
use Ministra\Admin\Service\Statistic\GroupedStatistic;
use Ministra\Admin\Service\Statistic\LicenseKeysStatistic;
use Ministra\Admin\Service\Statistic\StorageServerStatistic;
use Ministra\Admin\Service\Statistic\StreamServerStatistic;
use Ministra\Admin\Service\Statistic\VideoStatistic;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\C6988008e45ae6ce5c7e4c2c8135278b2;
use Ministra\Lib\NotificationFeed;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Response;
class IndexController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->app['error_local'] = [];
        $this->app['baseHost'] = $this->baseHost;
    }
    public function index()
    {
        $this->app['breadcrumbs']->addItem($this->setLocalization('Dashboard'));
        $datatables = ['devices' => $this->dataDevices(), 'content' => $this->dataContent(), 'licenses' => $this->dataLicenses(), 'storage' => $this->dataActiveStorages(), 'streaming' => $this->dataActiveStreaming()];
        return $this->app['twig']->render($this->getTemplateName(__METHOD__), \compact('datatables'));
    }
    private function dataDevices()
    {
        $devicesStat = new \Ministra\Admin\Service\Statistic\DevicesStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance(), $this->app['reseller']);
        $devicesStat->process($this->getTimeout());
        return [['online' => $devicesStat->countOnline(), 'offline' => $devicesStat->countOffline()]];
    }
    private function getTimeout()
    {
        return \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
    }
    private function dataContent()
    {
        $videoStat = new \Ministra\Admin\Service\Statistic\VideoStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance());
        $videoStat->process();
        $tvStat = new \Ministra\Admin\Service\Statistic\GroupedStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance());
        $tvStat->process('itv');
        $audioAlbumsStat = new \Ministra\Admin\Service\Statistic\GroupedStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance());
        $audioAlbumsStat->process('audio_albums');
        $karaokeAlbumsStat = new \Ministra\Admin\Service\Statistic\GroupedStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance());
        $karaokeAlbumsStat->process('karaoke');
        return [['type' => $this->setLocalization('TV channels'), 'published' => $tvStat->countItemsBy(1), 'total' => $tvStat->countTotal()], ['type' => $this->setLocalization('Films'), 'published' => $videoStat->countFilms(1), 'total' => $videoStat->countTotalFilms()], ['type' => $this->setLocalization('TV series'), 'published' => $videoStat->countSerials(1), 'total' => $videoStat->countTotalSerials()], ['type' => $this->setLocalization('Audio albums'), 'published' => $audioAlbumsStat->countItemsBy(1), 'total' => $audioAlbumsStat->countTotal()], ['type' => $this->setLocalization('Karaoke songs'), 'published' => $karaokeAlbumsStat->countItemsBy(1), 'total' => $karaokeAlbumsStat->countTotal()]];
    }
    private function dataLicenses()
    {
        $keyStat = new \Ministra\Admin\Service\Statistic\LicenseKeysStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance());
        $keyStat->process();
        return [['type' => $this->setLocalization('Standard'), 'activated' => $keyStat->totalStandard(\Ministra\Admin\Interfaces\LicenseKeyStatus::ACTIVATED) + $keyStat->totalStandard(\Ministra\Admin\Interfaces\LicenseKeyStatus::MANUALLY), 'available' => $keyStat->totalStandard(\Ministra\Admin\Interfaces\LicenseKeyStatus::NOT_ACTIVATED), 'reserved' => $keyStat->totalStandard(\Ministra\Admin\Interfaces\LicenseKeyStatus::RESERVED)], ['type' => $this->setLocalization('Advanced'), 'activated' => $keyStat->totalAdvanced(\Ministra\Admin\Interfaces\LicenseKeyStatus::ACTIVATED) + $keyStat->totalAdvanced(\Ministra\Admin\Interfaces\LicenseKeyStatus::MANUALLY), 'available' => $keyStat->totalAdvanced(\Ministra\Admin\Interfaces\LicenseKeyStatus::NOT_ACTIVATED), 'reserved' => $keyStat->totalAdvanced(\Ministra\Admin\Interfaces\LicenseKeyStatus::RESERVED)]];
    }
    private function dataActiveStorages()
    {
        $servers = $this->db->getStorages();
        if (\count($servers) == 0) {
            return [];
        }
        $names = \array_column($servers, 'storage_name');
        $serversStat = new \Ministra\Admin\Service\Statistic\StorageServerStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance(), $this->app['reseller']);
        $serversStat->process($this->getTimeout(), $names);
        $data = [];
        foreach ($servers as $server) {
            $stat = $serversStat->totalSessionsByServer($server['storage_name']);
            $data[] = ['storage' => $server['storage_name'], 'video' => \array_key_exists(\Ministra\Admin\Interfaces\ActivityType::VIDEO, $stat) ? $stat[\Ministra\Admin\Interfaces\ActivityType::VIDEO] : 0, 'tv_archive' => \array_key_exists(\Ministra\Admin\Interfaces\ActivityType::TV_ARCHIVE, $stat) ? $stat[\Ministra\Admin\Interfaces\ActivityType::TV_ARCHIVE] : 0, 'timeshift' => \array_key_exists(\Ministra\Admin\Interfaces\ActivityType::TIMESHIFT, $stat) ? $stat[\Ministra\Admin\Interfaces\ActivityType::TIMESHIFT] : 0, 'loading' => $this->calcPercent($server['max_online'], $stat['total'])];
        }
        return $data;
    }
    private function calcPercent($max, $value)
    {
        return $max > 0 ? \round($value * 100 / $max, 2) . '%' : '-';
    }
    private function dataActiveStreaming()
    {
        $streamingServers = $this->db->getStreamServer();
        if (\count($streamingServers) == 0) {
            return [];
        }
        $ids = \array_column($streamingServers, 'id');
        $streamingStat = new \Ministra\Admin\Service\Statistic\StreamServerStatistic(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance(), $this->app['reseller']);
        $streamingStat->process($this->getTimeout(), $ids);
        $data = [];
        foreach ($streamingServers as $server) {
            $sessions = $streamingStat->totalSessionsByServer($server['id']);
            $data[] = ['server' => $server['name'], 'sessions' => $sessions, 'loading' => $this->calcPercent($server['max_sessions'], $sessions)];
        }
        return $data;
    }
    public function set_dropdown_attribute()
    {
        if (!$this->isAjax || empty($this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'dropdownAttributesAction';
        $error = $this->setLocalization('Failed');
        $aliases = \trim(\str_replace($this->workURL, '', $this->refferer), '/');
        $aliases = \array_pad(\explode('/', $aliases), 2, 'index');
        $aliases[1] = \urldecode($aliases[1]);
        $filters = \explode('?', $aliases[1]);
        $aliases[1] = $filters[0];
        if (\count($filters) > 1 && (!empty($this->data['set-dropdown-attribute']) && $this->data['set-dropdown-attribute'] == 'with-button-filters')) {
            $filters[1] = \explode('&', $filters[1]);
            $filters[1] = $filters[1][0];
            $filters[1] = \str_replace(['=', '_'], '-', $filters[1]);
            $filters[1] = \preg_replace('/(\\[[^\\]]*\\])/i', '', $filters[1]);
            $aliases[1] .= "-{$filters[1]}";
        }
        $param = [];
        $param['controller_name'] = $aliases[0];
        $param['action_name'] = $aliases[1];
        $param['admin_id'] = $this->admin->getId();
        $this->db->deleteDropdownAttribute($param);
        $param['dropdown_attributes'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($this->postData);
        $id = $this->db->insertDropdownAttribute($param);
        if ($id && $id != 0) {
            $error = '';
            $data['nothing_to_do'] = 1;
        }
        $response = $this->generateAjaxResponse($data, $error);
        if (empty($error)) {
            \header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
            \header('Content-Type: application/json; charset=UTF-8');
            echo \json_encode($response);
        } else {
            \header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        }
        exit;
    }
    public function datatable_devices()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $data = ['data' => $this->dataDevices(), 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1];
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
    public function datatable_content()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $data = ['data' => $this->dataContent(), 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1];
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
    public function datatable_licenses()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $data = ['data' => $this->dataLicenses(), 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1];
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
    public function datatable_storages()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $data = ['data' => $this->dataActiveStorages(), 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1];
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
    public function datatable_streaming()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $data = ['data' => $this->dataActiveStreaming(), 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1];
        return new \Symfony\Component\HttpFoundation\JsonResponse($data);
    }
    public function index_datatable4_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['data' => [], 'action' => 'datatableReload', 'datatableID' => 'datatable-4', 'json_action_alias' => 'index-datatable4-list-json'];
        $error = $this->setLocalization('Failed');
        $data['data'] = [];
        $types = ['tv' => 1, 'video' => 2, 'karaoke' => 3, 'audio' => 4, 'radio' => 5];
        $all_sessions = 0;
        foreach ($types as $key => $type) {
            $data['data'][$key] = [];
            $data['data'][$key]['sessions'] = $this->db->getCurActivePlayingType($type);
            $all_sessions += $data['data'][$key]['sessions'];
        }
        $data['data'] = \array_map(function ($row) use($all_sessions) {
            \settype($row['sessions'], 'int');
            $row['percent'] = $all_sessions ? \round($row['sessions'] * 100 / $all_sessions, 0) : 0;
            return $row;
        }, $data['data']);
        $data['data']['all_sessions'] = (int) $all_sessions;
        if ($this->isAjax) {
            $error = '';
            $data = $this->generateAjaxResponse($data);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($data), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $data;
    }
    public function index_datatable5_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['data' => [], 'action' => 'datatableReload', 'datatableID' => 'datatable-5', 'json_action_alias' => 'index-datatable5-list-json'];
        $error = $this->setLocalization('Failed');
        $data['data'] = $this->db->getUsersActivity();
        $reseller = (int) $this->app['reseller'];
        $data['data'] = \array_map(function ($row) use($reseller) {
            \settype($row['time'], 'int');
            $row['users_online'] = @\json_decode($row['users_online'], true);
            $key = empty($reseller) ? 'total' : $reseller;
            $row['users_online'] = \is_array($row['users_online']) && \array_key_exists($key, $row['users_online']) ? (int) $row['users_online'][$key] : 0;
            return [$row['time'], $row['users_online']];
        }, $data['data']);
        if ($this->isAjax) {
            $error = '';
            $data = $this->generateAjaxResponse($data);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($data), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $data;
    }
    public function opinion_check()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'setOpinionModal';
        $error = '';
        $data['remind'] = $this->app['session']->get('remind', false);
        if ($this->admin->isSuperUser() && (\is_null($this->admin->getOpinionFormFlag()) || $this->admin->getOpinionFormFlag() == 'remind')) {
            $data['link'] = $this->app['language'] == 'ru' ? 'https://goo.gl/forms/2bZsWJ06feIas5Aa2' : 'https://goo.gl/forms/AQx9JhtJ9FYaBEJa2';
        } else {
            $data['remind'] = true;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function opinion_set()
    {
        if (!$this->isAjax || empty($this->postData['opinion'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'setOpinionData';
        $data['remind'] = true;
        $data['link'] = $this->app['language'] == 'ru' ? 'https://goo.gl/forms/2bZsWJ06feIas5Aa2' : 'https://goo.gl/forms/AQx9JhtJ9FYaBEJa2';
        $error = '';
        $this->db->getOpinionFormFlag($this->postData['opinion']);
        $this->app['session']->set('remind', true);
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function note_list()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, 'Page not found');
        }
        $error = '';
        $data = [];
        try {
            $feed = new \Ministra\Lib\NotificationFeed();
            $data = $feed->getNotDeletedItems();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse(['data' => $data], $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function note_list_mark_deleted()
    {
        if (!$this->isAjax || empty($this->postData['guid'])) {
            $this->app->abort(404, 'Page not found');
        }
        $error = '';
        $data = [];
        try {
            $feed = new \Ministra\Lib\NotificationFeed();
            $data = $feed->deleteByGuid($this->postData['guid']);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse(['data' => $data], $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function note_list_set_readed()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, 'Page not found');
        }
        $guid = isset($this->postData['feed_item_id']) ? $this->postData['feed_item_id'] : null;
        $data = [];
        $error = '';
        try {
            $feed = new \Ministra\Lib\NotificationFeed();
            $data = $feed->setRedByGuid($guid);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse(['data' => $data], $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function note_list_set_remind()
    {
        if (!$this->isAjax || empty($this->postData['feeditemid'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $error = '';
        try {
            $feed = new \Ministra\Lib\NotificationFeed();
            $item = $feed->getItemByGUId($this->postData['feeditemid']);
            $data = $item ? $item->setDelay(60 * 24) : [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse(['data' => $data], $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_certificate_server_health()
    {
        if (!$this->isAjax || empty($this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = ['health_status' => false, 'time' => \time() * 1000];
        $error = '';
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('certificate_server_health_check', true)) {
            try {
                if (\array_key_exists('check_health_time', $this->postData)) {
                    if (\time() - (int) ($this->postData['check_health_time'] / 1000) > 3600) {
                        $smac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B603f6072f55d27f59fab9253d56b1a36\X7db24d699d9dc4413e7a61bb94cc44d8::a0a3921a25e19d949bd4be9d65f0e1e0()->b2752823b7677523753979de3a5daba5(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\C6988008e45ae6ce5c7e4c2c8135278b2::class);
                        $data['health_status'] = $smac->cb74435d5430ada6b0c9ea665cf24b59();
                        if (!$data['health_status']) {
                            $data['action'] = 'healthServerAlert';
                        }
                    }
                    $data['nothing_to_do'] = true;
                } else {
                    $error = $this->setLocalization('Undefined last health check time');
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        } else {
            $data['health_status'] = true;
            $data['nothing_to_do'] = true;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
