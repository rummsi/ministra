<?php

namespace Ministra\Admin\Controller;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b4c75f356ba107b83536139965f5fb66d\f634b2f995cc6b1b3311171fb0680721;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class StatisticsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $taskType = array();
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->taskType = [['id' => 'moderator_tasks', 'title' => $this->setLocalization('Movies')], ['id' => 'karaoke', 'title' => $this->setLocalization('Karaoke')]];
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/stat-video');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function stat_video()
    {
        if (empty($this->data['filters']['stat_to'])) {
            if (empty($this->data['filters'])) {
                $this->data['filters'] = ['stat_to' => 'all'];
            } else {
                $this->data['filters']['stat_to'] = 'all';
            }
            $dropdown_filters = '';
        } else {
            $dropdown_filters = "-filters-{$this->data['filters']['stat_to']}";
        }
        $this->app['filters'] = $this->data['filters'];
        $filter = $this->app['filters']['stat_to'];
        $this->app['allVideoStat'] = [['id' => 'all', 'title' => $this->setLocalization('General')], ['id' => 'daily', 'title' => $this->setLocalization('By days')], ['id' => 'genre', 'title' => $this->setLocalization('By genres')]];
        $attr_func = 'getVideo' . \ucfirst($filter) . 'DropdownAttribute';
        $attribute = $this->{$attr_func}();
        $this->checkDropdownAttribute($attribute, $dropdown_filters);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getBeginEndPeriod()
    {
        $return = ['time_end' => '', 'time_begin' => '', 'target_table' => ''];
        switch (\str_replace('-list-json', '', $this->app['action_alias'])) {
            case 'stat-moderators':
                $return['time_end'] = !empty($this->data['filters']['task_type']) && $this->data['filters']['task_type'] == 'karaoke' ? 'done_time' : 'end_time';
                $return['time_begin'] = !empty($this->data['filters']['task_type']) && $this->data['filters']['task_type'] == 'karaoke' ? 'done_time' : 'end_time';
                $return['target_table'] = '';
                break;
            case 'stat-video':
                if (empty($this->data['filters']['stat_to']) || $this->data['filters']['stat_to'] != 'genre') {
                    $return['time_end'] = $return['time_begin'] = empty($this->data['filters']['stat_to']) || $this->data['filters']['stat_to'] != 'daily' ? 'last_played' : 'date';
                    $return['target_table'] = empty($this->data['filters']['stat_to']) || $this->data['filters']['stat_to'] != 'daily' ? 'video' : 'daily_played_video';
                }
                break;
            case 'stat-tv':
                $return['time_end'] = $return['time_begin'] = 'playtime';
                $return['target_table'] = 'played_itv';
                break;
            case 'stat-tv-archive':
                $return['time_end'] = $return['time_begin'] = 'playtime';
                $return['target_table'] = 'played_tv_archive';
                break;
            case 'stat-timeshift':
                $return['time_end'] = $return['time_begin'] = 'playtime';
                $return['target_table'] = 'played_timeshift';
                break;
            case 'stat-abonents':
                if (empty($this->data['filters']['abon_to']) || $this->data['filters']['abon_to'] == 'tv') {
                    $return['time_end'] = $return['time_begin'] = 'played_itv.playtime';
                    $return['target_table'] = 'played_itv';
                } elseif ($this->data['filters']['abon_to'] == 'video') {
                    $return['time_end'] = $return['time_begin'] = 'played_video.playtime';
                    $return['target_table'] = 'played_video';
                } else {
                    $return['time_end'] = $return['time_begin'] = 'readed';
                    $return['target_table'] = 'readed_anec';
                }
                break;
            case 'stat-abonents-unactive':
                $return['time_end'] = $return['time_begin'] = '`users`.`time_last_play_tv`';
                $return['target_table'] = 'users';
                break;
            case 'stat-claims':
                $return['time_end'] = $return['time_begin'] = 'date';
                $return['target_table'] = 'daily_media_claims';
                break;
        }
        return $return;
    }
    public function stat_tv()
    {
        $attribute = $this->getTvDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['filters'] = \array_key_exists('filters', $this->data) ? $this->data['filters'] : [];
        $this->app['allTVLocale'] = $this->db->getTVLocale();
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getTvDropdownAttribute()
    {
        return [['name' => 'itv_id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'number', 'title' => $this->setLocalization('Number'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true]];
    }
    public function stat_tv_archive()
    {
        $attribute = $this->getTvArchiveDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getTvArchiveDropdownAttribute()
    {
        return [['name' => 'ch_id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true], ['name' => 'total_duration', 'title' => $this->setLocalization('Entire time of views, sec'), 'checked' => true]];
    }
    public function stat_timeshift()
    {
        $attribute = $this->getTimeShiftDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getTimeShiftDropdownAttribute()
    {
        return [['name' => 'ch_id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true], ['name' => 'total_duration', 'title' => $this->setLocalization('Entire time of views, sec'), 'checked' => true]];
    }
    public function stat_moderators()
    {
        $task_report_state = [0 => ['id' => '1', 'title' => $this->setLocalization('Open')], 1 => ['id' => '2', 'title' => $this->setLocalization('Done')], 2 => ['id' => '3', 'title' => $this->setLocalization('Rejected')], 3 => ['id' => '4', 'title' => $this->setLocalization('Expired')], 4 => ['id' => '5', 'title' => $this->setLocalization('Archive')]];
        $this->app['allTaskState'] = $task_report_state;
        unset($task_report_state[0], $task_report_state[3], $task_report_state[4]);
        $this->app['taskType'] = $this->taskType;
        $this->app['taskState'] = $task_report_state;
        $this->app['videoQuality'] = [0 => ['id' => '1', 'title' => 'SD'], 1 => ['id' => '2', 'title' => 'HD']];
        $this->app['taskAdmin'] = $this->db->getAdmins();
        if (empty($this->data['filters']['task_type'])) {
            if (empty($this->data['filters'])) {
                $this->data['filters'] = ['task_type' => 'moderator_tasks'];
            } else {
                $this->data['filters']['task_type'] = 'moderator_tasks';
            }
            $dropdown_filters = '';
        } else {
            $dropdown_filters = "-filters-{$this->data['filters']['task_type']}";
        }
        $this->app['task_type_title'] = $this->getTaskTitle($this->data['filters']['task_type']);
        $this->app['task_type'] = $this->data['filters']['task_type'];
        $this->app['taskStateColor'] = ['primary', 'success', 'warning', 'danger', 'default'];
        $attribute = $this->getModeratorsDropdownAttribute();
        $this->checkDropdownAttribute($attribute, $dropdown_filters);
        $this->app['dropdownAttribute'] = $attribute;
        if ($this->data['filters']['task_type'] == 'moderator_tasks') {
            $this->app['allVideoDuration'] = ['hd_time' => -1, 'sd_time' => -1];
        }
        $allArhivedate = $this->db->getArhiveIDs(($this->data['filters']['task_type'] == 'moderator_tasks' ? 'tasks' : 'karaoke') . '_archive');
        $this->app['allArhivedate'] = \array_reverse($allArhivedate);
        $this->app['filters'] = $this->data['filters'];
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function stat_users_devices()
    {
        $attribute = $this->getStatUsersDevicesDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        if (isset($this->data['user_id'])) {
            $title = $this->setLocalization('Devices of user') . "ID {$this->data['user_id']}";
            $this->app['breadcrumbs']->addItem($title);
            $this->app['current_user_id'] = $this->data['user_id'];
        }
        if (empty($this->app['reseller'])) {
            $usersResellers = \array_map(function ($row) {
                return ['id' => $row['id'], 'title' => $row['name']];
            }, $this->db->getAllFromTable('reseller'));
            $this->app['usersResellers'] = $usersResellers;
        }
        $unknown_translate = $this->setLocalization('unknown');
        $allPlatforms = \array_map(function ($row) use($unknown_translate) {
            return ['id' => $row['platform'] ?: 'unknown', 'title' => $row['platform'] ?: $unknown_translate];
        }, $this->db->getAllFromTable('users_devices_statistic', 'platform', 'platform'));
        $allModels = \array_map(function ($row) use($unknown_translate) {
            return ['id' => $row['model'] ?: 'unknown', 'title' => $row['model'] ?: $unknown_translate];
        }, $this->db->getAllFromTable('users_devices_statistic', 'model', 'model'));
        $licenseKeyTypes = [['id' => 1, 'title' => 'Operator\'s key'], ['id' => 2, 'title' => 'User own key'], ['id' => 3, 'title' => 'Without key']];
        $this->app['allPlatforms'] = $allPlatforms;
        $this->app['allModels'] = $allModels;
        $this->app['licenseKeyTypes'] = $this->setLocalization($licenseKeyTypes, 'title');
        $this->app['ownKeyTranslate'] = ['key' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b4c75f356ba107b83536139965f5fb66d\f634b2f995cc6b1b3311171fb0680721::O70e4fca7a98352ad7cc936bf191bc1e4, 'translate' => $this->setLocalization(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b4c75f356ba107b83536139965f5fb66d\f634b2f995cc6b1b3311171fb0680721::O70e4fca7a98352ad7cc936bf191bc1e4)];
        $this->app['filters'] = $this->data['filters'];
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function stat_users_devices_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $filters = \array_diff_key($filters, ['stat_to' => '', 'no_active_abonent' => '', 'abon_to' => '', 'task_type' => '']);
        $filds_for_select = $this->getUsersDevicesFields();
        $error = '';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($param['user_id'])) {
            $query_param['where']['users_devices_statistic.`user_id`'] = $param['user_id'];
        }
        $response['recordsTotal'] = $this->db->getUsersDevicesTotalRows(!empty($param['user_id']) ? ['users_devices_statistic.`user_id`' => $param['user_id']] : []);
        $response['recordsFiltered'] = $this->db->getUsersDevicesTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getUsersDevicesList($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getUsersDevicesFields()
    {
        $return = ['id' => '`users_devices_statistic`.`id` as `id`', 'user_id' => '`users_devices_statistic`.`user_id` as `user_id`', 'user_name' => '`users`.`fname` as `user_name`', 'user_login' => '`users`.`login` as `user_login`', 'mac' => '`users_devices_statistic`.`mac` as `mac`', 'platform' => '`users_devices_statistic`.`platform` as `platform`', 'model' => '`users_devices_statistic`.`model` as `model`', 'license_key' => '`users_devices_statistic`.`license_key` as `license_key`', 'added' => '`users_devices_statistic`.`added` as `added`'];
        if (empty($this->app['reseller'])) {
            $return['reseller_id'] = '`users`.`reseller_id` as `reseller_id`';
            $return['reseller_name'] = '`reseller`.`name` as `reseller_name`';
        }
        return $return;
    }
    private function getStatUsersDevicesDropdownAttribute()
    {
        $return = [['name' => 'id', 'title' => $this->setLocalization('Record ID'), 'checked' => false], ['name' => 'user_id', 'title' => $this->setLocalization('User ID'), 'checked' => true], ['name' => 'user_name', 'title' => $this->setLocalization('User name'), 'checked' => true], ['name' => 'user_login', 'title' => $this->setLocalization('User login'), 'checked' => true]];
        if (empty($this->app['reseller'])) {
            $return[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        $return[] = ['name' => 'mac', 'title' => $this->setLocalization('Device MAC'), 'checked' => true];
        $return[] = ['name' => 'platform', 'title' => $this->setLocalization('Platform'), 'checked' => true];
        $return[] = ['name' => 'model', 'title' => $this->setLocalization('Model'), 'checked' => true];
        $return[] = ['name' => 'license_key', 'title' => $this->setLocalization('License key'), 'checked' => true];
        $return[] = ['name' => 'added', 'title' => $this->setLocalization('Date added'), 'checked' => true];
        if (empty($this->data['user_id'])) {
            $return[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        }
        return $return;
    }
    private function getTaskTitle($param)
    {
        foreach ($this->taskType as $row) {
            if ($row['id'] == $param) {
                return $row['title'];
            }
        }
        return '';
    }
    private function getModeratorsDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('Order'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Type'), 'checked' => false], ['name' => 'start_time', 'title' => $this->setLocalization('Created'), 'checked' => true], ['name' => 'end_time', 'title' => $this->setLocalization('Completed'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'video_quality', 'title' => $this->setLocalization('Quality'), 'checked' => true], ['name' => 'duration', 'title' => $this->setLocalization('Length, min'), 'checked' => true], ['name' => 'to_user_name', 'title' => $this->setLocalization('Moderator'), 'checked' => true], ['name' => 'state', 'title' => $this->setLocalization('State'), 'checked' => true]];
    }
    public function stat_abonents()
    {
        if (empty($this->data['filters']['abon_to'])) {
            if (empty($this->data['filters'])) {
                $this->data['filters'] = ['abon_to' => 'tv'];
            } else {
                $this->data['filters']['abon_to'] = 'tv';
            }
            $dropdown_filters = '';
        } else {
            $dropdown_filters = "-filters-{$this->data['filters']['abon_to']}";
        }
        $this->app['filters'] = $this->data['filters'];
        $filter = $this->app['filters']['abon_to'];
        $this->app['allAbonentStat'] = [['id' => 'tv', 'title' => $this->setLocalization('TV')], ['id' => 'video', 'title' => $this->setLocalization('Movies')], ['id' => 'anec', 'title' => $this->setLocalization('Humor')]];
        $attr_func = 'getAbonent' . \ucfirst($filter) . 'DropdownAttribute';
        $attribute = $this->{$attr_func}();
        $this->checkDropdownAttribute($attribute, $dropdown_filters);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function stat_abonents_unactive()
    {
        if (empty($this->data['filters']['no_active_abonent'])) {
            if (empty($this->data['filters'])) {
                $this->data['filters'] = ['no_active_abonent' => 'tv'];
            } else {
                $this->data['filters']['no_active_abonent'] = 'tv';
            }
            $dropdown_filters = '';
        } else {
            $dropdown_filters = "-filters-{$this->data['filters']['no_active_abonent']}";
        }
        $this->app['filters'] = $this->data['filters'];
        $filter = $this->app['filters']['no_active_abonent'];
        $this->app['allNoActiveAbonentStat'] = [['id' => 'tv', 'title' => $this->setLocalization('TV')], ['id' => 'video', 'title' => $this->setLocalization('Movies')]];
        $attr_func = 'getNoActiveAbonent' . \ucfirst($filter) . 'DropdownAttribute';
        $attribute = $this->{$attr_func}();
        $this->checkDropdownAttribute($attribute, $dropdown_filters);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function stat_claims()
    {
        $attribute = $this->getClaimsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getClaimsDropdownAttribute()
    {
        return [['name' => 'date', 'title' => $this->setLocalization('Date'), 'checked' => true], ['name' => 'vclub_sound', 'title' => $this->setLocalization('Video-club sound'), 'checked' => true], ['name' => 'vclub_video', 'title' => $this->setLocalization('Video-club video'), 'checked' => true], ['name' => 'itv_sound', 'title' => $this->setLocalization('TV sound'), 'checked' => true], ['name' => 'itv_video', 'title' => $this->setLocalization('TV video'), 'checked' => true], ['name' => 'karaoke_sound', 'title' => $this->setLocalization('Karaoke sound'), 'checked' => true], ['name' => 'karaoke_video', 'title' => $this->setLocalization('Karaoke video'), 'checked' => true], ['name' => 'no_epg', 'title' => $this->setLocalization('No EPG'), 'checked' => true], ['name' => 'wrong_epg', 'title' => $this->setLocalization('EPG does not match'), 'checked' => true]];
    }
    public function stat_claims_logs()
    {
        $attribute = $this->getClaimsLogsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $date_fields = $this->getBeginEndPeriod();
        $this->app['minDatepickerDate'] = $this->db->getMinDateFromTable($date_fields['target_table'], $date_fields['time_begin']);
        $this->app['breadcrumbs']->addItem($this->setLocalization('Complaints statistics'), $this->workURL . '/' . $this->app['controller_alias'] . '/stat-claims');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Complaints log'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getClaimsLogsDropdownAttribute()
    {
        return [['name' => 'media_type', 'title' => $this->setLocalization('Category'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Object of complaint'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Type'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('Author'), 'checked' => true], ['name' => 'added', 'title' => $this->setLocalization('Date'), 'checked' => true]];
    }
    public function stat_video_list_json($param = array())
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $func_alias = \ucfirst(!empty($filters['stat_to']) && $filters['stat_to'] != 'main' ? $filters['stat_to'] : 'all');
        $filds_for_select = $this->{"getVideo{$func_alias}Fields"}();
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getVideoStatTotalRows($func_alias);
        $response['recordsFiltered'] = $this->db->getVideoStatTotalRows($func_alias, $query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->{"getVideoStat{$func_alias}List"}($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $datetime = new \DateTime();
        while (list($num, $row) = \each($response['data'])) {
            if ($func_alias == 'Genre') {
                $response['data'][$num]['total_movies'] = $row['total_movies'] ? $row['total_movies'] : 0;
                $response['data'][$num]['played_movies'] = $row['played_movies'] ? $row['played_movies'] : 0;
                $response['data'][$num]['title'] = $this->mb_ucfirst($this->setLocalization($row['title']));
            } else {
                $datekey = \array_key_exists('date', $row) ? 'date' : 'last_played';
                $timestamp = 0;
                if (!empty($row[$datekey])) {
                    $date_arr = \explode(' ', $row[$datekey]);
                    \call_user_func_array([$datetime, 'setDate'], \explode('-', $date_arr[0]));
                    if (!empty($date_arr[1])) {
                        \call_user_func_array([$datetime, 'setTime'], \explode(':', $date_arr[1]));
                    } else {
                        $datetime->setTime(0, 0, 0);
                    }
                    $timestamp = $datetime->getTimestamp();
                }
                $response['data'][$num][$datekey] = $datetime instanceof \DateTime && (int) $timestamp > 0 ? $timestamp : 0;
            }
        }
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getStatisticsFilters(&$like_filter)
    {
        $return = [];
        if (!empty($this->data['filters'])) {
            if (!empty($this->data['filters']['stat_to'])) {
                $return['stat_to'] = $this->data['filters']['stat_to'];
            } else {
                $return['stat_to'] = 'main';
            }
            if (!empty($this->data['filters']['no_active_abonent'])) {
                $return['no_active_abonent'] = $this->data['filters']['no_active_abonent'];
            } else {
                $return['no_active_abonent'] = 'tv';
            }
            if (!empty($this->data['filters']['abon_to'])) {
                $return['abon_to'] = $this->data['filters']['abon_to'];
            } else {
                $return['abon_to'] = 'tv';
            }
            if (!empty($this->data['filters']['user_locale'])) {
                $return['user_locale'] = $this->data['filters']['user_locale'];
            }
            if (\array_key_exists('task_type', $this->data['filters'])) {
                $return['task_type'] = $this->data['filters']['task_type'];
            } else {
                $return['task_type'] = 'moderator_tasks';
            }
            if (\array_key_exists('state', $this->data['filters']) && !empty($this->data['filters']['state'])) {
                $state = (int) $this->data['filters']['state'];
                if ($state != 5) {
                    if ($return['task_type'] == 'karaoke') {
                        $return['if(done=0 and archived=0 and (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(added))>864000, 3, done)='] = (int) $this->data['filters']['state'] - 1;
                    } else {
                        $return['if(ended=0 and archived=0 and (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(start_time))>864000, 3, ended + rejected)='] = (int) $this->data['filters']['state'] - 1;
                    }
                } else {
                    $return['`archived`<>'] = 0;
                }
            }
            if (\array_key_exists('video_quality', $this->data['filters']) && !empty($this->data['filters']['video_quality']) && $return['task_type'] == 'moderator_tasks') {
                $return['`hd`'] = (int) $this->data['filters']['video_quality'] - 1;
            }
            if (\array_key_exists('to_user', $this->data['filters']) && !empty($this->data['filters']['to_user'])) {
                $return['A.`id`'] = $this->data['filters']['to_user'];
            }
            if (\array_key_exists('archived', $this->data['filters']) && !empty($this->data['filters']['archived'])) {
                $return['`archived`'] = $this->data['filters']['archived'];
            }
            \extract($this->getBeginEndPeriod());
            if (\array_key_exists('interval_from', $this->data['filters']) && $this->data['filters']['interval_from'] != 0 && !empty($time_begin)) {
                $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['interval_from']);
                $return["UNIX_TIMESTAMP({$time_begin})>="] = $date->getTimestamp();
            }
            if (\array_key_exists('interval_to', $this->data['filters']) && $this->data['filters']['interval_to'] != 0 && !empty($time_end)) {
                $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['interval_to']);
                $return["UNIX_TIMESTAMP({$time_end})<="] = $date->getTimestamp();
            }
            if (\array_key_exists('reseller', $this->data['filters']) && !empty($this->data['filters']['reseller'])) {
                $return['reseller_id'] = (int) $this->data['filters']['reseller'];
            }
            if (\array_key_exists('platform', $this->data['filters']) && !empty($this->data['filters']['platform'])) {
                $return['`platform`'] = $this->data['filters']['platform'] != 'unknown' ? $this->data['filters']['platform'] : '';
            }
            if (\array_key_exists('model', $this->data['filters']) && !empty($this->data['filters']['model'])) {
                $return['`model`'] = $this->data['filters']['model'] != 'unknown' ? $this->data['filters']['model'] : '';
            }
            if (\array_key_exists('key_type', $this->data['filters']) && !empty($this->data['filters']['key_type'])) {
                $key_type = (int) $this->data['filters']['key_type'];
                switch ($key_type) {
                    case 1:
                        $return['`license_key` AND `license_key`<>'] = 'user own key';
                        break;
                    case 2:
                        $return['`license_key`'] = 'user own key';
                        break;
                    case 3:
                        $return['`license_key`'] = '';
                        break;
                }
            }
            $this->app['filters'] = $this->data['filters'];
        } else {
            $this->app['filters'] = [];
        }
        return $return;
    }
    public function stat_abonents_unactive_list_json($param = array())
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $func_alias = \ucfirst(!empty($filters['no_active_abonent']) ? $filters['no_active_abonent'] : 'tv');
        $filds_for_select = $this->{"getNoActiveAbonent{$func_alias}Fields"}();
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getNoActiveAbonentTotalRows($func_alias);
        $response['recordsFiltered'] = $this->db->getNoActiveAbonentTotalRows($func_alias, $query_param['where'], $query_param['like']);
        if ($func_alias != 'Genre') {
            if (empty($query_param['limit']['limit'])) {
                $query_param['limit']['limit'] = 50;
            } elseif ($query_param['limit']['limit'] == -1) {
                $query_param['limit']['limit'] = false;
            }
        }
        $response['data'] = $this->db->{"getNoActiveAbonent{$func_alias}List"}($query_param);
        $response['data'] = \array_map(function ($row) {
            $row['time_last_play'] = (int) \strtotime($row['time_last_play']);
            $row['time_last_play'] = $row['time_last_play'] <= 0 ? '0000-00-00' : $row['time_last_play'];
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_claims_list_json($param = array())
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $filds_for_select = $this->getFieldFromArray($this->getClaimsDropdownAttribute(), 'name');
        $error = $this->setLocalization('Error');
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (($search = \array_search('date', $query_param['select'])) !== false) {
            $query_param['select'][$search] = 'CAST(`date` as CHAR) as `date`';
        }
        if (!empty($query_param['like']) && \array_key_exists('date', $query_param['like'])) {
            $query_param['like']['CAST(`date` as CHAR)'] = $query_param['like']['date'];
            unset($query_param['like']['date']);
        }
        $response['recordsTotal'] = $this->db->getDailyClaimsTotalRows();
        $response['recordsFiltered'] = $this->db->getDailyClaimsTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getDailyClaimsList($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_claims_logs_list_json($param = array())
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = $this->getFieldFromArray($this->getClaimsLogsDropdownAttribute(), 'name');
        $error = $this->setLocalization('Error');
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        if (!empty($param['type'])) {
            if (\strpos($param['type'], 'epg') !== false) {
                $param['media_type'] = 'itv';
            } else {
                $tmp = \explode('_', $param['type']);
                $param['media_type'] = $tmp[0];
                $param['type'] = $tmp[1];
            }
        }
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (!empty($param['type'])) {
            $query_param['where']['`type`'] = $param['type'];
        }
        if (!empty($param['media_type'])) {
            $query_param['where']['`media_type`'] = $param['media_type'];
        }
        if (!isset($query_param['like'])) {
            $query_param['like'] = [];
        }
        if (!empty($param['date'])) {
            $query_param['where']['M_C_L.`added` LIKE "' . $param['date'] . '%" AND 1'] = 1;
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (($search = \array_search('name', $query_param['select'])) !== false) {
            $query_param['select'][$search] = 'if(isnull(I.`name`), if(isnull(K.`name`), if(isnull(V.`name`), "undefined", V.`name`), K.`name`),I.`name`) as `name`';
        }
        if (($search = \array_search('added', $query_param['select'])) !== false) {
            $query_param['select'][$search] = 'CAST(M_C_L.`added` as CHAR) as `added`';
        }
        $query_param['select'][] = 'M_C_L.uid';
        if (!empty($query_param['like']) && \array_key_exists('name', $query_param['like'])) {
            $query_param['like']["(I.`name` LIKE '{$query_param['like']['name']}' OR K.`name` LIKE '{$query_param['like']['name']}' OR V.`name` LIKE '{$query_param['like']['name']}') AND '1'"] = 1;
            unset($query_param['like']['name']);
        }
        $response['recordsTotal'] = $this->db->getClaimsLogsTotalRows($query_param['where']);
        $response['recordsFiltered'] = $this->db->getClaimsLogsTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getClaimsLogsList($query_param);
        $response['data'] = \array_map(function ($row) {
            $row['added'] = (int) \strtotime($row['added']);
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_tv_archive_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $filds_for_select = ['itv_id' => 'played_tv_archive.ch_id as `ch_id`', 'name' => 'itv.`name` as `name`', 'counter' => 'count(`played_tv_archive`.ch_id) as `counter`', 'total_duration' => 'SUM(played_tv_archive.length) as `total_duration`'];
        $error = $this->setLocalization('Error');
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (!empty($query_param['like']) && \array_key_exists('counter', $query_param['like'])) {
            unset($query_param['like']['counter']);
        }
        if (!empty($query_param['like']) && \array_key_exists('total_duration', $query_param['like'])) {
            unset($query_param['like']['total_duration']);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getTvArchiveTotalRows();
        $response['recordsFiltered'] = $this->db->getTvArchiveTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getTvArchiveList($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_timeshift_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $filds_for_select = ['itv_id' => 'played_timeshift.ch_id as `ch_id`', 'name' => 'itv.`name` as `name`', 'counter' => 'count(`played_timeshift`.ch_id) as `counter`', 'total_duration' => 'SUM(played_timeshift.length) as `total_duration`'];
        $error = $this->setLocalization('Error');
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (!empty($query_param['like']) && \array_key_exists('counter', $query_param['like'])) {
            unset($query_param['like']['counter']);
        }
        if (!empty($query_param['like']) && \array_key_exists('total_duration', $query_param['like'])) {
            unset($query_param['like']['total_duration']);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getTimeShiftTotalRows();
        $response['recordsFiltered'] = $this->db->getTimeShiftTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getTimeShiftList($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_abonents_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $func_alias = \ucfirst(!empty($filters['abon_to']) ? $filters['abon_to'] : 'tv');
        $filds_for_select = $this->{"getAbonent{$func_alias}Fields"}();
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (!empty($query_param['like']) && \array_key_exists('count(`played_itv`.`id`)', $query_param['like'])) {
            unset($query_param['like']['count(`played_itv`.`id`)']);
        }
        $response['recordsTotal'] = $this->db->getAbonentStatTotalRows($func_alias);
        $response['recordsFiltered'] = $this->db->getAbonentStatTotalRows($func_alias, $query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->{"getAbonentStat{$func_alias}List"}($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($func_alias == 'Anec') {
            $response['data'] = \array_map(function ($row) {
                $row['readed'] = (int) \strtotime($row['readed']);
            }, $response['data']);
        }
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_tv_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $like_filter = [];
        $filters = $this->getStatisticsFilters($like_filter);
        $filds_for_select = ['itv_id' => 'played_itv.itv_id as `itv_id`', 'number' => 'itv.number as `number`', 'name' => 'itv.`name` as `name`', 'counter' => 'count(`played_itv`.id) as `counter`'];
        $error = $this->setLocalization('Error');
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : $param;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        unset($filters['stat_to'], $filters['no_active_abonent'], $filters['abon_to'], $filters['task_type']);
        $query_param['where'] = \array_merge($query_param['where'], $filters);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (!empty($query_param['like']) && \array_key_exists('counter', $query_param['like'])) {
            unset($query_param['like']['counter']);
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select, true);
        $response['recordsTotal'] = $this->db->getTvTotalRows();
        $response['recordsFiltered'] = $this->db->getTvTotalRows($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = $this->db->getTvList($query_param);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function stat_moderators_list_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'table' => 'moderator_tasks'];
        $error = 'Error';
        $param = empty($param) ? !empty($this->data) ? $this->data : $this->postData : [];
        $like_filter = [];
        $filter = $this->getStatisticsFilters($like_filter);
        if (!empty($filter['task_type'])) {
            $response['table'] = $filter['task_type'];
        }
        if (!empty($param['task_type'])) {
            $response['table'] = $param['task_type'];
        }
        unset($filter['task_type'], $filter['stat_to'], $filter['no_active_abonent'], $filter['abon_to']);
        $func = 'getFieldsReport' . \ucfirst($response['table']);
        $filds_for_select = $this->{$func}($response['table']);
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = $like_filter;
        } elseif (!empty($query_param['like']) && !empty($like_filter)) {
            $query_param['like'] = \array_merge($query_param['like'], $like_filter);
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $query_param['where']['A.id is not '] = null;
        if ($response['table'] == 'karaoke') {
            $query_param['where']['done'] = 1;
        } else {
            $query_param['where']['ended'] = 1;
        }
        $prefix = \implode('_', \array_map(function ($val) {
            return \strtoupper(\substr($val, 0, 1));
        }, \explode('_', $response['table'])));
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'A.`id` as `user_id`';
            $query_param['select'][] = '(archived<>0) as `archived`';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $func = 'getJoinedReport' . \ucfirst($response['table']);
        $query_param['joined'] = $this->{$func}();
        $func = 'getGropByReport' . \ucfirst($response['table']);
        $query_param['groupby'] = $this->{$func}();
        $query_param['from'] = "{$response['table']} as {$prefix}";
        $query_param['groupby'][] = "{$prefix}.`id`";
        $response['recordsTotal'] = $this->db->getModeratorsStatRowsList($query_param, true);
        $response['recordsFiltered'] = $this->db->getModeratorsStatRowsList($query_param);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['videotime'] = $this->getVideoTime($query_param);
        $response['data'] = \array_map(function ($val) {
            $val['state'] = (int) $val['state'];
            $val['start_time'] = (int) \strtotime($val['start_time']);
            $val['end_time'] = (int) \strtotime($val['end_time']);
            return $val;
        }, $this->db->getModeratorsStatList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getVideoTime($params)
    {
        if (\strpos($params['from'], 'moderator_tasks') !== false) {
            $return['hd_time'] = -1;
            $return['sd_time'] = -1;
            unset($params['select'], $params['joined']['`moderators_history` as M_H'], $params['groupby']);
            $params['select'][] = 'sum(V.`time`) as `summtime`';
            $params['where']['ended'] = 1;
            $params['where']['rejected'] = 0;
            $params['limit'] = [];
            if (!empty($this->data['filters']['video_quality']) && $this->data['filters']['video_quality'] == 2) {
                $result = $this->db->getModeratorsStatList($params);
                $return['hd_time'] = $result[0]['summtime'];
            }
            if (!empty($this->data['filters']['video_quality']) && $this->data['filters']['video_quality'] == 1) {
                $result = $this->db->getModeratorsStatList($params);
                $return['sd_time'] = $result[0]['summtime'];
            }
            if (empty($this->data['filters']['video_quality'])) {
                $params['where']['`hd`'] = 0;
                $result = $this->db->getModeratorsStatList($params);
                $return['sd_time'] = $result[0]['summtime'];
                $params['where']['`hd`'] = 1;
                $result = $this->db->getModeratorsStatList($params);
                $return['hd_time'] = $result[0]['summtime'];
            }
            return $return;
        }
        return -1;
    }
    public function stat_claims_clean()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['media_type'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Error');
        if ($this->postData['media_type'] == 'all') {
            $this->db->truncateTable('daily_media_claims');
            $this->db->truncateTable('media_claims');
            $this->db->truncateTable('media_claims_log');
            $error = '';
            $data['msg'] = $this->setLocalization('Cleared all');
        } else {
            $query_params = ['select' => ['id', 'date'], 'like' => [], 'order' => []];
            if ($this->postData['media_type'] == 'epg') {
                $query_params['where'] = ['no_epg <> 0 OR wrong_epg<>' => 0];
            } else {
                $query_params['where'] = [$this->postData['media_type'] . '_sound <> 0 OR ' . $this->postData['media_type'] . '_video<>' => 0];
            }
            $date = $this->db->getDailyClaimsList($query_params);
            if (!empty($date) && \is_array($date)) {
                $query_params['select'] = ['M_C_L.id as `id`', 'M_C_L.media_id as `media_id`'];
                if ($this->postData['media_type'] == 'epg') {
                    $query_params['where'] = ['M_C_L.media_type' => 'itv', "M_C_L.`type` = 'no_epg' OR M_C_L.`type` = " => 'wrong_epg'];
                } else {
                    $query_params['where'] = ['media_type' => $this->postData['media_type'], "(M_C_L.`type` = 'sound' OR M_C_L.`type` = 'video') AND '1'=" => '1'];
                }
                $like = \array_map(function ($row) {
                    return "{$row}%";
                }, $this->getFieldFromArray($date, 'date'));
                $like_ctr = '';
                for ($i = 0; $i <= \count($like) - 2; ++$i) {
                    $like_ctr .= ' M_C_L.`added` LIKE "' . $like[$i] . '" OR ';
                }
                $like_ctr .= ' M_C_L.`added` ';
                $query_params['like'][$like_ctr] = $like[\count($like) - 1];
                $log = $this->db->getClaimsLogsList($query_params);
                if ($this->postData['media_type'] == 'epg') {
                    $new_values = ['no_epg' => 0, 'wrong_epg' => 0];
                } else {
                    $new_values = [$this->postData['media_type'] . '_sound' => 0, $this->postData['media_type'] . '_video' => 0];
                }
                if ($this->db->updateDailyClaims($new_values, ['id' => $this->getFieldFromArray($date, 'id')])) {
                    $this->db->cleanDailyClaims();
                }
                if ($this->postData['media_type'] != 'epg') {
                    $new_values = ['sound_counter' => 0, 'video_counter' => 0];
                }
                if ($this->db->updateMediaClaims($new_values, ['media_id' => $this->getFieldFromArray($log, 'media_id')], ['media_type' => $this->postData['media_type'] == 'epg' ? 'itv' : $this->postData['media_type']])) {
                    $this->db->cleanMediaClaims();
                }
                $this->db->deleteClaimsLogs(['id' => $this->getFieldFromArray($log, 'id')]);
                $error = '';
                $data['msg'] = $this->setLocalization('Cleared');
            } else {
                $data['msg'] = $this->setLocalization('Nothing in this category');
                $error = '';
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    private function getVideoAllDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'count', 'title' => $this->setLocalization('Views lifetime'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views last month'), 'checked' => true], ['name' => 'last_played', 'title' => $this->setLocalization('Last viewed date'), 'checked' => true], ['name' => 'count_storages', 'title' => $this->setLocalization('Number of copies'), 'checked' => true]];
    }
    private function getVideoDailyDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'date', 'title' => $this->setLocalization('Day'), 'checked' => true], ['name' => 'count', 'title' => $this->setLocalization('By day'), 'checked' => true]];
    }
    private function getVideoGenreDropdownAttribute()
    {
        return [['name' => 'title', 'title' => $this->setLocalization('Genre'), 'checked' => true], ['name' => 'played_movies', 'title' => $this->setLocalization('Overall movies'), 'checked' => true], ['name' => 'total_movies', 'title' => $this->setLocalization('Views quantity'), 'checked' => true], ['name' => 'ratio', 'title' => $this->setLocalization('Genre popularity') . ', %', 'checked' => true]];
    }
    private function getVideoAllFields()
    {
        return ['id' => '`video`.`id` as `id`', 'name' => '`video`.`name` as `name`', 'count' => '`video`.`count` as `count`', 'counter' => '(`video`.count_second_0_5 + `video`.count_first_0_5) as `counter`', 'last_played' => 'CAST(`video`.`last_played` as CHAR) as `last_played`', 'count_storages' => "(select count(*) from `storage_cache` as S_C where S_C.`status` = 1 and S_C.`media_type` = 'vclub' and S_C.`media_id` = `video`.`id`) as `count_storages`"];
    }
    private function getVideoDailyFields()
    {
        return ['id' => '`daily_played_video`.`id` as `id`', 'date' => 'CAST(`daily_played_video`.`date` as CHAR) as `date`', 'count' => '`daily_played_video`.`count` as `count`'];
    }
    private function getVideoGenreFields()
    {
        return ['title' => '`title` as `title`', 'played_movies' => '`played_movies` as `played_movies`', 'total_movies' => '`total_movies` as `total_movies`', 'ratio' => 'IF(`played_movies` AND `played_movies`<>0, ROUND(( IF(total_movies AND total_movies<>0, total_movies, 0 )/ played_movies) * 100, 2), 0.00 ) as `ratio`'];
    }
    private function getNoActiveAbonentTvDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'time_last_play', 'title' => $this->setLocalization('Last view TV'), 'checked' => true]];
    }
    private function getNoActiveAbonentVideoDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'time_last_play', 'title' => $this->setLocalization('Last view movie'), 'checked' => true]];
    }
    private function getNoActiveAbonentTvFields()
    {
        return ['id' => '`users`.`id` as `id`', 'mac' => '`users`.`mac` as `mac`', 'time_last_play' => 'CAST(`users`.`time_last_play_tv` as CHAR) as `time_last_play`'];
    }
    private function getNoActiveAbonentVideoFields()
    {
        return ['id' => '`users`.`id` as `id`', 'mac' => '`users`.`mac` as `mac`', 'time_last_play' => 'CAST(`users`.`time_last_play_video` as CHAR) as `time_last_play`'];
    }
    private function getAbonentTvDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true]];
    }
    private function getAbonentVideoDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true]];
    }
    private function getAbonentAnecDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'counter', 'title' => $this->setLocalization('Views quantity'), 'checked' => true], ['name' => 'readed', 'title' => $this->setLocalization('Last view'), 'checked' => true]];
    }
    private function getAbonentTvFields()
    {
        return ['id' => '`users`.`id` as `id`', 'mac' => '`users`.`mac` as `mac`', 'counter' => 'count(`played_itv`.`id`) as `counter`'];
    }
    private function getAbonentVideoFields()
    {
        return ['id' => '`users`.`id` as `id`', 'mac' => '`users`.`mac` as `mac`', 'counter' => 'count(`played_video`.`id`) as `counter`'];
    }
    private function getAbonentAnecFields()
    {
        return ['id' => '`readed_anec`.`id` as `id`', 'mac' => '`readed_anec`.`mac` as `mac`', 'counter' => 'count(`readed_anec`.`mac`) as `counter`', 'readed' => 'CAST(max(readed) as CHAR) as `readed`'];
    }
    private function getFieldsReportModerator_tasks($table = '')
    {
        return ['user_id' => 'A.`id` as `user_id`', 'id' => 'M_T.`id` as `id`', 'type' => "'{$this->getTaskTitle($table)}'as `type`", 'name' => 'V.`name` as `name`', 'to_user_name' => 'A.`login` as `to_user_name`', 'start_time' => 'CAST(M_T.`start_time` as CHAR ) as `start_time`', 'state' => 'if(ended=0 and archived=0 and (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(start_time))>864000, 3, M_T.`ended` + M_T.rejected) as `state`', 'end_time' => 'CAST(M_T.`end_time` as CHAR ) as `end_time`', 'video_quality' => "if(V.hd = 0, 'SD', 'HD') as `video_quality`", 'duration' => 'CAST(V.`time` as UNSIGNED) as `duration`', 'archived' => '(archived<>0) as `archived`'];
    }
    private function getFieldsReportKaraoke($table = '')
    {
        return ['user_id' => 'A.`id` as `user_id`', 'id' => 'K.`id` as `id`', 'type' => "'{$this->getTaskTitle($table)}'as `type`", 'name' => "concat_ws(' - ', K.`singer`, K.`name`) as `name`", 'to_user_name' => 'A.`login` as `to_user_name`', 'start_time' => 'CAST(K.`added` as CHAR ) as `start_time`', 'state' => 'if(K.done=0 and K.archived=0 and (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(K.added))>864000, 3, K.done) as `state`', 'end_time' => 'CAST(K.`done_time` as CHAR ) as `end_time`', 'video_quality' => "'-' as `video_quality`", 'duration' => "'-' as `duration`", 'archived' => '(archived<>0) as `archived`'];
    }
    private function getJoinedReportModerator_tasks()
    {
        return ['`administrators` as A' => ['left_key' => 'M_T.`to_usr`', 'right_key' => 'A.`id`', 'type' => 'LEFT'], '`video` as V' => ['left_key' => 'M_T.`media_id`', 'right_key' => 'V.`id`', 'type' => 'INNER'], '`moderators_history` as M_H' => ['left_key' => 'M_T.`id`', 'right_key' => 'M_H.`task_id` and M_T.`to_usr` = M_H.`to_usr`', 'type' => 'LEFT']];
    }
    private function getJoinedReportKaraoke()
    {
        return ['`administrators` as A' => ['left_key' => 'K.`add_by`', 'right_key' => 'A.`id`', 'type' => 'LEFT']];
    }
    private function getGropByReportModerator_tasks()
    {
        return [];
    }
    private function getGropByReportKaraoke()
    {
        return [];
    }
}
