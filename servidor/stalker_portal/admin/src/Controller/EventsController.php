<?php

namespace Ministra\Admin\Controller;

use Cron as Cron;
use Ministra\Lib\AdminPanelEvents;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\CronExpression;
use Ministra\Lib\CronForm;
use Ministra\Lib\Filters;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class EventsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $repeatingInterval;
    protected $monthNames;
    protected $dayNames;
    protected $sendedStatus = array();
    protected $receivingStatus = array();
    protected $scheduleType = array();
    protected $scheduleState = array();
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->sendedStatus = [['id' => 1, 'title' => $this->setLocalization('Not delivered')], ['id' => 2, 'title' => $this->setLocalization('Delivered')]];
        $this->receivingStatus = [['id' => 1, 'title' => $this->setLocalization('Not received')], ['id' => 2, 'title' => $this->setLocalization('Received')]];
        $this->scheduleType = [['id' => 1, 'title' => $this->setLocalization('One-time event')], ['id' => 2, 'title' => $this->setLocalization('For a period')]];
        $this->scheduleState = [['id' => 2, 'title' => $this->setLocalization('Scheduled')], ['id' => 1, 'title' => $this->setLocalization('Stopped')]];
        $this->repeatingInterval = [['id' => 1, 'title' => $this->setLocalization('Year')], ['id' => 2, 'title' => $this->setLocalization('Month')], ['id' => 3, 'title' => $this->setLocalization('Week')], ['id' => 4, 'title' => $this->setLocalization('Day')]];
        $this->monthNames = [['id' => 1, 'title' => $this->setLocalization('January')], ['id' => 2, 'title' => $this->setLocalization('February')], ['id' => 3, 'title' => $this->setLocalization('March')], ['id' => 4, 'title' => $this->setLocalization('April')], ['id' => 5, 'title' => $this->setLocalization('May')], ['id' => 6, 'title' => $this->setLocalization('June')], ['id' => 7, 'title' => $this->setLocalization('July')], ['id' => 8, 'title' => $this->setLocalization('August')], ['id' => 9, 'title' => $this->setLocalization('September')], ['id' => 10, 'title' => $this->setLocalization('October')], ['id' => 11, 'title' => $this->setLocalization('November')], ['id' => 12, 'title' => $this->setLocalization('December')]];
        $this->dayNames = [['id' => 1, 'title' => $this->setLocalization('Mon')], ['id' => 2, 'title' => $this->setLocalization('Tue')], ['id' => 3, 'title' => $this->setLocalization('Wed')], ['id' => 4, 'title' => $this->setLocalization('Thu')], ['id' => 5, 'title' => $this->setLocalization('Fri')], ['id' => 6, 'title' => $this->setLocalization('Sat')], ['id' => 7, 'title' => $this->setLocalization('Sun')]];
        $this->app['defTTL'] = ['send_msg' => 7 * 24 * 3600, 'send_msg_with_video' => 7 * 24 * 3600, 'other' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2];
    }
    public function index()
    {
        if (empty($this->app['action_alias']) || $this->app['action_alias'] == 'index') {
            return $this->app->redirect($this->workURL . '/' . $this->app['controller_alias'] . '/events');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function events()
    {
        if (isset($this->data['uid'])) {
            $param['id'] = $this->data['uid'];
            $currentUser = $this->db->getUser($param);
            $this->app['currentUser'] = ['login' => $currentUser['login'], 'mac' => $currentUser['mac'], 'uid' => $currentUser['id']];
            $label = $currentUser['login'] ?: $currentUser['mac'];
            $this->app['breadcrumbs']->addItem($this->setLocalization('Users events') . ' (' . $label . ')');
        }
        $this->app['formEvent'] = $this->getFormEvent();
        $this->app['allEvent'] = \array_merge($this->getFormEvent(), $this->getHiddenEvent());
        $this->app['sendedStatus'] = $this->sendedStatus;
        $this->app['receivingStatus'] = $this->receivingStatus;
        $this->app['consoleGroup'] = $this->db->getConsoleGroup();
        $this->app['allFilters'] = $this->getAllFilters();
        $this->app['messagesTemplates'] = $this->db->getAllFromTable('messages_templates', 'title');
        $attribute = $this->getEventsListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        $this->getEventsFilters();
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getFormEvent($without_mount = false)
    {
        $return = [['id' => 'send_msg', 'title' => $this->setLocalization('Sending a message')], ['id' => 'reboot', 'title' => $this->setLocalization('Reboot')], ['id' => 'reload_portal', 'title' => $this->setLocalization('Restart the portal')], ['id' => 'update_channels', 'title' => $this->setLocalization('Update channel list')], ['id' => 'play_channel', 'title' => $this->setLocalization('Playback channel')], ['id' => 'play_radio_channel', 'title' => $this->setLocalization('Playback radio channel')]];
        if (!$without_mount) {
            $return[] = ['id' => 'mount_all_storages', 'title' => $this->setLocalization('Mount all storages')];
        }
        $return[] = ['id' => 'cut_off', 'title' => $this->setLocalization('Switch off')];
        $return[] = ['id' => 'update_image', 'title' => $this->setLocalization('Image update')];
        return $return;
    }
    private function getHiddenEvent()
    {
        return [['id' => 'update_epg', 'title' => $this->setLocalization('EPG update')], ['id' => 'update_subscription', 'title' => $this->setLocalization('Subscribe update')], ['id' => 'update_modules', 'title' => $this->setLocalization('Modules update')], ['id' => 'cut_on', 'title' => $this->setLocalization('Switch on')], ['id' => 'show_menu', 'title' => $this->setLocalization('Show menu')], ['id' => 'additional_services_status', 'title' => $this->setLocalization('Status additional service')]];
    }
    private function getAllFilters()
    {
        $filter_set = \Ministra\Lib\Filters::getInstance();
        $filter_set->setResellerID($this->app['reseller']);
        $filter_set->initData('users', 'id');
        $self = $this;
        $all_title = $this->setLocalization('All');
        return \array_map(function ($row) use($filter_set, $self, $all_title) {
            if (($filter_set_data = @\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($row['filter_set'])) !== false) {
                $row['filter_set'] = '';
                foreach ($filter_set_data as $data_row) {
                    $filter_set_filter = $filter_set->getFilters([$data_row[0]]);
                    if ($filter_set_filter[0]['type'] != 'DATETIME' && $filter_set_filter[0]['type'] != 'STRING') {
                        \array_unshift($filter_set_filter[0]['values_set'], ['value' => '0', 'title' => $all_title]);
                        $data_array = \explode('|', $data_row[2]);
                    } else {
                        $data_array = [$data_row[2]];
                    }
                    $row_filter_set = $self->setLocalization($filter_set_filter[0]['title']) . ': ';
                    foreach ($data_array as $data_val) {
                        if (!empty($filter_set_filter[0]['values_set']) && \is_array($filter_set_filter[0]['values_set'])) {
                            foreach ($filter_set_filter[0]['values_set'] as $filter_row) {
                                if ((string) $data_val == $filter_row['value']) {
                                    $row_filter_set .= $self->setLocalization($filter_row['title']) . ', ';
                                    break;
                                }
                            }
                        } else {
                            $row_filter_set .= $data_val . ', ';
                        }
                    }
                    $row['filter_set'] .= \trim($row_filter_set, ', ') . '; ';
                }
            }
            \settype($row['favorites'], 'int');
            \settype($row['for_all'], 'int');
            return $row;
        }, $this->db->getAllFromTable('filter_set', 'id'));
    }
    private function getEventsListDropdownAttribute()
    {
        $attribute = [['name' => 'events_id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'addtime', 'title' => $this->setLocalization('Added'), 'checked' => true], ['name' => 'eventtime', 'title' => $this->setLocalization('Expiration date'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Login'), 'checked' => true], ['name' => 'event', 'title' => $this->setLocalization('Event'), 'checked' => true], ['name' => 'sended', 'title' => $this->setLocalization('Delivery status'), 'checked' => true], ['name' => 'ended', 'title' => $this->setLocalization('Receipt status'), 'checked' => true]];
        return $attribute;
    }
    private function getEventsFilters()
    {
        $return = [];
        if (!\array_key_exists('filters', $this->data)) {
            $this->data['filters'] = [];
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['event'])) {
            $return['event'] = $this->data['filters']['event'];
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['sended'])) {
            $return['sended'] = (int) $this->data['filters']['sended'] - 1;
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['ended'])) {
            $return['ended'] = (int) $this->data['filters']['ended'] - 1;
        }
        if (!empty($this->data['uid'])) {
            $return['uid'] = $this->data['uid'];
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['date_from'])) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['date_from']);
            $date->modify('midnight');
            $this->data['filters']['interval_from'] = $return['UNIX_TIMESTAMP(`date_begin`) >='] = $date->getTimestamp();
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['date_to'])) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['date_to']);
            $date->modify('1 second ago tomorrow');
            $this->data['filters']['interval_to'] = $return['UNIX_TIMESTAMP(`date_end`) > 0 AND UNIX_TIMESTAMP(`date_end`) <='] = $date->getTimestamp();
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['type']) && (int) $this->data['filters']['type']) {
            $return['periodic'] = (int) $this->data['filters']['type'] - 1;
        }
        if (!empty($this->data['filters']) && !empty($this->data['filters']['state']) && (int) $this->data['filters']['state']) {
            $return['state'] = (int) $this->data['filters']['state'] - 1;
        }
        $this->app['filters'] = !empty($this->data['filters']) ? $this->data['filters'] : [];
        return $return;
    }
    public function message_templates()
    {
        $attribute = $this->getMessagesTemplatesDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allAdmins'] = $this->db->getAllFromTable('administrators', 'login');
        if (!empty($this->data['filters'])) {
            $this->app['filters'] = $this->data['filters'];
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getMessagesTemplatesDropdownAttribute()
    {
        $attribute = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'title', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Author'), 'checked' => true], ['name' => 'created', 'title' => $this->setLocalization('Created'), 'checked' => true], ['name' => 'edited', 'title' => $this->setLocalization('Edited'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function event_scheduler()
    {
        $this->app['scheduleType'] = $this->scheduleType;
        $this->app['scheduleState'] = $this->scheduleState;
        $this->app['consoleGroup'] = $this->db->getConsoleGroup();
        $this->app['formEvent'] = $this->getFormEvent(true);
        $this->app['allFilters'] = $this->getAllFilters();
        $this->app['repeatingInterval'] = $this->repeatingInterval;
        $this->app['monthNames'] = $this->monthNames;
        $this->app['dayNames'] = $this->dayNames;
        $this->app['messagesTemplates'] = $this->db->getAllFromTable('messages_templates', 'title');
        $attribute = $this->getSchedulerDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        if (!empty($this->data['filters'])) {
            $this->app['filters'] = $this->data['filters'];
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getSchedulerDropdownAttribute()
    {
        $attribute = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'event_trans', 'title' => $this->setLocalization('Event'), 'checked' => true], ['name' => 'post_function', 'title' => $this->setLocalization('Post-function'), 'checked' => true], ['name' => 'recipient', 'title' => $this->setLocalization('Recipient'), 'checked' => true], ['name' => 'periodic', 'title' => $this->setLocalization('Type'), 'checked' => true], ['name' => 'date_begin', 'title' => $this->setLocalization('Begin'), 'checked' => true], ['name' => 'date_end', 'title' => $this->setLocalization('End'), 'checked' => true], ['name' => 'schedule', 'title' => $this->setLocalization('Schedule'), 'checked' => true], ['name' => 'next_run', 'title' => $this->setLocalization('Next run'), 'checked' => true], ['name' => 'last_run', 'title' => $this->setLocalization('Last run'), 'checked' => true], ['name' => 'state', 'title' => $this->setLocalization('State'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    public function events_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $filds_for_select = ['events_id' => 'events.`id` as `events_id`', 'addtime' => 'CAST(events.`addtime` AS CHAR) as `addtime`', 'eventtime' => 'CAST(events.`eventtime` AS CHAR) as `eventtime`', 'mac' => 'users.`mac` as `mac`', 'login' => 'users.`login` as `login`', 'event' => 'events.`event` as `event`', 'msg' => 'events.`msg` as `msg`', 'sended' => 'events.`sended` as `sended`', 'ended' => 'events.`ended` as `ended`', 'uid' => 'events.`uid` as `uid`', 'name' => 'users.`fname` as `name`', 'post_function' => 'events.`post_function` as `post_function`', 'param1' => 'events.`param1` as `param1`'];
        $error = '';
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filter = $this->getEventsFilters();
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'uid';
        }
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        $response['recordsTotal'] = $this->db->getTotalRowsEventsList();
        $response['recordsFiltered'] = $this->db->getTotalRowsEventsList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['events_id'];
            return $row;
        }, $this->db->getEventsList($query_param));
        $allevents = $this->getFormEvent();
        $allevents = \array_combine($this->getFieldFromArray($allevents, 'id'), $this->getFieldFromArray($allevents, 'title'));
        $hiddenevents = $this->getHiddenEvent();
        $hiddenevents = \array_combine($this->getFieldFromArray($hiddenevents, 'id'), $this->getFieldFromArray($hiddenevents, 'title'));
        $events = \array_merge($allevents, $hiddenevents);
        $self = $this;
        $response['data'] = \array_map(function ($row) use($events, $self) {
            $row['event'] = $events[$row['event']];
            $row['addtime'] = (int) \strtotime($row['addtime']);
            if ($row['addtime'] < 0) {
                $row['addtime'] = 0;
            }
            $row['eventtime'] = (int) \strtotime($row['eventtime']);
            if ($row['eventtime'] < 0) {
                $row['eventtime'] = 0;
            }
            if (!empty($row['post_function'])) {
                $row['post_function'] = $self->setLocalization(\str_replace('_', ' ', \ucfirst($row['post_function'])));
            }
            if (!empty($row['param1']) && \strpos($row['param1'], '://') === false) {
                $row['param1'] = $self->setLocalization($row['param1']);
            }
            return $row;
        }, $response['data']);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    public function add_event()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['user_list_type']) || empty($this->postData['event'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Event creating error');
        $_SERVER['TARGET'] = 'ADM';
        $post_data = $this->postData;
        if (\array_key_exists('user_id', $post_data)) {
            $post_data['id'] = $post_data['user_id'];
        }
        $event = new \Ministra\Lib\AdminPanelEvents($post_data);
        $event->setTtl($this->postData['ttl']);
        if (!empty($this->postData['add_post_function']) && !empty($this->postData['post_function']) && !empty($this->postData['param1'])) {
            $event->setPostFunctionParam($this->postData['post_function'], $this->postData['param1']);
        }
        $get_list_func_name = 'get_userlist_' . \str_replace('to_', '', $this->postData['user_list_type']);
        $set_event_func_name = 'set_event_' . \str_replace('to_', '', $this->postData['event']);
        if ($event->{$get_list_func_name}()->cleanAndSetUsers()->{$set_event_func_name}()) {
            $count = \count($event->getUserList());
            $data['msg'] = $this->setLocalization('Event created to users', '', $count, ['%cnt%' => $count]);
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function upload_list_addresses()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($_FILES)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'addAddressList';
        $data['msg'] = $this->setLocalization('Added');
        $data['fname'] = '';
        $error = $this->setLocalization('The file does not contain valid MAC-addresses.');
        list($key, $tmp) = \each($_FILES);
        $file_data = \file_get_contents($tmp['tmp_name']);
        $list = [];
        \preg_match_all('/([0-9a-fA-F]{2}:){5}([0-9a-fA-F]{2})/', $file_data, $list);
        if (!empty($list) && !empty($list[0])) {
            $file_name = \tempnam(\sys_get_temp_dir(), 'MAC');
            $data['fname'] = \basename($file_name);
            $file_data = \implode(';', $list[0]);
            \file_put_contents($file_name, $file_data);
            $data['msg'] .= \count($list[0]) . ' ' . $this->setLocalization('addresses');
            $error = '';
        } else {
            $data['msg'] = $error;
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function clean_events()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['uid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $result = $this->postData['uid'] == 'all' ? $this->db->deleteAllEvents() : $this->db->deleteEventsByUID($this->postData['uid']);
        if (!\is_bool($result)) {
            $error = '';
            if (\is_numeric($result)) {
                $data['msg'] = $this->setLocalization('Deleted {cnt} events', '', $result, ['{cnt}' => $result]);
            } else {
                $data['msg'] = $this->setLocalization('Deleted all events');
            }
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function save_message_template()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['msg_tpl'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Not enough data');
        $tpl_data['params'] = $this->postData['msg_tpl'];
        $tpl_data['params']['author'] = $tpl_data['params']['admin_id'];
        if (!empty($this->postData['msg_tpl']['id'])) {
            $operation = 'update';
            $tpl_data['id'] = $this->postData['msg_tpl']['id'];
        } else {
            $operation = 'insert';
            $tpl_data['params']['created'] = 'NOW()';
        }
        unset($tpl_data['params']['id'], $tpl_data['params']['admin_id']);
        $return_id = \call_user_func_array([$this->db, $operation . 'MsgTemplate'], $tpl_data);
        if ($return_id !== false) {
            if ($return_id == 0) {
                $data['msg'] = $this->setLocalization('Nothing to do');
                $data['nothing_to_do'] = true;
            } else {
                if ($operation != 'insert' || !empty($this->postData['action'])) {
                    if (!empty($tpl_data['id'])) {
                        $this->postData['id'] = $tpl_data['id'];
                    }
                    $data = \array_merge_recursive($data, $this->message_templates_list_json(true));
                    $data['action'] = empty($this->postData['action']) ? 'updateTableRow' : $this->postData['action'];
                    if (!empty($tpl_data['id'])) {
                        $data['id'] = $tpl_data['id'];
                    }
                }
            }
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function message_templates_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filds_for_select = $this->getMsgTemplatesFields();
        if (!empty($query_param['select'])) {
            $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        } else {
            $query_param['select'] = $filds_for_select;
        }
        if (!empty($this->data['filters']['admin_id'])) {
            $query_param['where']['M_T.author'] = $this->data['filters']['admin_id'];
        }
        if (!empty($this->postData['id'])) {
            $query_param['where'] = ['M_T.id' => $this->postData['id']];
            $response['action'] = 'fillModalForm';
        } elseif (!empty($this->postData['action'])) {
            $response['action'] = $this->postData['action'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsMsgTemplates();
        $response['recordsFiltered'] = $this->db->getTotalRowsMsgTemplates($query_param['where'], $query_param['like']);
        $response['data'] = \array_map(function ($row) {
            $row['created'] = (int) \strtotime($row['created']) * 1000;
            $row['edited'] = (int) \strtotime($row['edited']) * 1000;
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getMsgTemplates($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getMsgTemplatesFields()
    {
        return ['id' => 'M_T.`id` as `id`', 'login' => 'A.`login` as `login`', 'admin_id' => 'A.`id` as `admin_id`', 'title' => 'M_T.title as `title`', 'header' => 'M_T.header as `header`', 'body' => 'M_T.body as `body`', 'created' => 'M_T.created as `created`', 'edited' => 'M_T.edited as `edited`', 'url' => 'M_T.url as `url`'];
    }
    public function remove_template()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteMsgTemplate($this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function save_schedule_event()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Not enough data');
        $from_db = \array_flip($this->getFieldFromArray($this->db->getTableFields('schedule_events'), 'Field'));
        $form_post = $this->postData;
        $recipient_func = 'getRecipientBy' . \ucfirst(\strtolower(\str_replace(['by_', 'to_'], '', $form_post['user_list_type'])));
        $form_post['recipient'] = $this->{$recipient_func}($form_post);
        $form_post['periodic'] = (int) \str_replace('schedule_type_', '', $form_post['type']) - 1;
        $form_post['state'] = 1;
        $form_post['reboot_after_ok'] = (int) (!empty($form_post['reboot_after_ok']) && (string) $form_post['reboot_after_ok'] != 'false' && (string) $form_post['reboot_after_ok'] != 'off' && (string) $form_post['reboot_after_ok'] != '0');
        if (\array_key_exists('month', $form_post)) {
            $form_post['month'] = (int) $form_post['month'];
        }
        if (\array_key_exists('every_month', $form_post)) {
            $form_post['every_month'] = (int) $form_post['every_month'];
        }
        if (\array_key_exists('every_day', $form_post)) {
            $form_post['every_day'] = (int) $form_post['every_day'];
        }
        if (\array_key_exists('every_hour', $form_post)) {
            $form_post['every_hour'] = (int) $form_post['every_hour'];
        }
        if (\array_key_exists('every_minute', $form_post)) {
            $form_post['every_minute'] = (int) $form_post['every_minute'];
        }
        if (\array_key_exists('date_begin', $form_post)) {
            $date = \DateTime::createFromFormat('d/m/Y', $form_post['date_begin']);
            $form_post['date_begin'] = $date ? $date->format('Y-m-d G:i:s') : 'NOW()';
        }
        if (\array_key_exists('date_end', $form_post)) {
            $date = \DateTime::createFromFormat('d/m/Y', $form_post['date_end']);
            $form_post['date_end'] = $date ? $date->format('Y-m-d G:i:s') : '';
        }
        $form_post['schedule'] = \Ministra\Lib\CronForm::getInstance()->setFormData($form_post)->getExpression();
        $params = [];
        $from_db = \array_combine(\array_keys($from_db), \array_fill(0, \count($from_db), null));
        if (!empty($form_post['id'])) {
            $id = $form_post['id'];
            $operation = 'update';
            $params[] = \array_replace($from_db, \array_intersect_key($form_post, $from_db));
            $params[] = $id;
        } else {
            $operation = 'insert';
            $params[] = \array_replace($from_db, \array_intersect_key($form_post, $from_db));
        }
        unset($params[0]['id']);
        $result = \call_user_func_array([$this->db, $operation . 'ScheduleEvents'], $params);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
                $data['msg'] = $this->setLocalization('Nothing to do');
            } elseif ($operation != 'insert') {
                $this->postData['id'] = $id;
                $data = \array_merge_recursive($data, $this->event_scheduler_list_json(true));
                $data['action'] = 'updateTableRow';
                $data['id'] = $id;
            } else {
                $data['msg'] = '';
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function event_scheduler_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_', 'next_run', 'event_trans']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filter = $this->getEventsFilters();
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $filds_for_select = $this->getScheduleEventsFields();
        if (!empty($query_param['select'])) {
            $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        } else {
            $query_param['select'] = $filds_for_select;
        }
        if (!\array_key_exists('event', $query_param['select'])) {
            $query_param['select']['event'] = $filds_for_select['event'];
        }
        if (!\array_key_exists('last_run', $query_param['select'])) {
            $query_param['select']['last_run'] = $filds_for_select['last_run'];
        }
        if (\array_key_exists('id', $this->postData)) {
            $query_param['where'] = ['S_E.id' => $this->postData['id']];
            $response['action'] = 'fillModalForm';
        }
        if (!empty($query_param['like'])) {
            foreach (['S_E.last_run', 'TIMESTAMP(S_E.date_begin)', 'TIMESTAMP(S_E.date_end)'] as $field_d) {
                if (\array_key_exists($field_d, $query_param['like'])) {
                    $query_param['like']["CAST({$field_d} as CHAR)"] = $query_param['like'][$field_d];
                    unset($query_param['like'][$field_d]);
                }
            }
        }
        $response['recordsTotal'] = $this->db->getTotalRowsScheduleEvents();
        $response['recordsFiltered'] = $this->db->getTotalRowsScheduleEvents($query_param['where'], $query_param['like']);
        $cronTab = new \Ministra\Lib\CronExpression('* * * * *', new \Cron\FieldFactory());
        foreach ($cronTab->getMessageParts() as $key => $val) {
            $cronTab->setMessageParts($key, $this->setLocalization($val));
        }
        $deferred = $this->setLocalization('deferred');
        $unlimited = $this->setLocalization('unlimited');
        $not_run = $this->setLocalization('do not yet running');
        $all_event = \array_merge($this->getFormEvent(), $this->getHiddenEvent());
        $all_event = \array_combine($this->getFieldFromArray($all_event, 'id'), $this->getFieldFromArray($all_event, 'title'));
        $all_recipients = ['to_all' => $this->setLocalization('All'), 'by_group' => $this->setLocalization('Group'), 'to_single' => $this->setLocalization('One'), 'by_filter' => $this->setLocalization('Filter')];
        $response['data'] = \array_map(function ($row) use($cronTab, $deferred, $all_event, $all_recipients, $unlimited, $not_run) {
            $cronTab->setCurrentTime($row['last_run']);
            $row['event_trans'] = $all_event[$row['event']];
            $row['post_function'] = \array_key_exists($row['post_function'], $all_event) ? $all_event[$row['post_function']] : $row['post_function'];
            $row['date_begin'] = (int) \strtotime($row['date_begin']);
            if ($row['date_begin'] < 0) {
                $row['date_begin'] = 0;
            }
            $row['date_end'] = (int) \strtotime($row['date_end']);
            if ($row['date_end'] <= 0) {
                $row['date_end'] = $unlimited;
            }
            $row['last_run'] = (int) \strtotime($row['last_run']);
            if ($row['last_run'] <= 0) {
                $row['last_run'] = $not_run;
            }
            $row['cron_str'] = $row['schedule'];
            $cronTab->setExpression($row['schedule'])->setMessage();
            if (!empty($row['schedule']) && (int) $row['periodic']) {
                $row['next_run'] = $cronTab->getNextRunDate()->getTimestamp();
                $row['schedule'] = \implode(' ', $cronTab->getMessage());
            } else {
                $row['date_end'] = $row['next_run'] = $row['state'] ? $cronTab->getNextRunDate()->getTimestamp() : $deferred;
                $row['schedule'] = $cronTab->getMessageParts('once');
            }
            $recipient = \json_decode($row['recipient'], true);
            list($row['recipient'], $recipient) = \each($recipient);
            $row['user_list_type'] = $row['recipient'];
            $row['recipient'] = $all_recipients[$row['recipient']];
            if (!empty($recipient) && \is_array($recipient)) {
                $row = \array_merge($row, (array) $recipient);
            }
            \settype($row['reboot_after_ok'], 'int');
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getScheduleEvents($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            if (!empty($response['data']) && !empty($this->postData['id'])) {
                $response['data'][0] = \array_merge($response['data'][0], \array_map(function ($row) {
                    return \is_numeric($row) ? \str_pad((string) $row, 2, '0', STR_PAD_LEFT) : $row;
                }, \Ministra\Lib\CronForm::getInstance()->setExpression($response['data'][0]['cron_str'])->getFormData()));
                if (\array_key_exists('interval', $response['data'][0])) {
                    $response['data'][0]['interval'] = \str_replace('0', 'repeating_interval_', $response['data'][0]['interval']);
                }
                $response['data'][0]['type'] = 'schedule_type_' . ((int) $response['data'][0]['periodic'] + 1);
            }
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
        }
        return $response;
    }
    private function getScheduleEventsFields()
    {
        return ['id' => 'S_E.`id` as `id`', 'event' => 'S_E.event as `event`', 'header' => 'S_E.header as `header`', 'msg' => 'S_E.msg as `msg`', 'post_function' => 'S_E.post_function as `post_function`', 'recipient' => 'S_E.recipient as `recipient`', 'periodic' => 'S_E.periodic as `periodic`', 'date_begin' => 'TIMESTAMP(S_E.date_begin) as `date_begin`', 'date_end' => 'TIMESTAMP(S_E.date_end) as `date_end`', 'schedule' => 'S_E.schedule as `schedule`', 'state' => 'S_E.state as `state`', 'reboot_after_ok' => 'S_E.reboot_after_ok as `reboot_after_ok`', 'param1' => 'S_E.param1 as `param1`', 'ttl' => 'S_E.ttl as `ttl`', 'last_run' => 'S_E.last_run as `last_run`', 'channel' => 'S_E.channel as `channel`'];
    }
    public function scheduler_toggle_state()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['id']) || !isset($this->postData['state'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->updateScheduleEvents(['state' => !(int) $this->postData['state']], $this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            } else {
                $data = \array_merge_recursive($data, $this->event_scheduler_list_json(true));
                $data['action'] = 'updateTableRow';
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    public function scheduler_remove()
    {
        if (!$this->isAjax || $this->method != 'POST' || !isset($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Not enough data');
        $result = $this->db->deleteScheduleEvents($this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500);
    }
    private function getRecipientByAll($data)
    {
        return \json_encode([$data['user_list_type'] => '']);
    }
    private function getRecipientByGroup($data)
    {
        return \json_encode([$data['user_list_type'] => ['group_id' => $data['group_id']]]);
    }
    private function getRecipientBySingle($data)
    {
        return \json_encode([$data['user_list_type'] => ['mac' => $data['mac']]]);
    }
    private function getRecipientByFilter($data)
    {
        return \json_encode([$data['user_list_type'] => ['filter_set' => $data['filter_set']]]);
    }
}
