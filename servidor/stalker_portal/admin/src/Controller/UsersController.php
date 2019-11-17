<?php

namespace Ministra\Admin\Controller;

use Ministra\Admin\Service\GeoToolService;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Filters;
use Ministra\Lib\RemotePvr;
use Ministra\Lib\StbGroup;
use Ministra\Lib\SysEvent;
use Ministra\Lib\User;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Validator\Constraints as Assert;
class UsersController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    protected $user;
    protected $tariff_expires_time;
    private $watchdog = 0;
    private $userFields = array('users.id as `id`', 'users.mac', '`ip`', '`country`', '`login`', '`ls`', '`fname`', 'users.reseller_id', '`theme`', '`status`', 'tariff_plan.id as `tariff_plan_id`', 'tariff_plan.name as `tariff_plan_name`', 'stb_groups.id as `group_id`', 'stb_groups.name as `group_name`', "DATE_FORMAT(last_change_status,'%d.%m.%Y') as `last_change_status`", 'concat (users.fname) as `fname`', 'UNIX_TIMESTAMP(`keep_alive`) as `last_active`', '`expire_billing_date` as `expire_billing_date`', 'users.`created` as `created`', '`account_balance`', '`now_playing_type`', "IF(now_playing_type = 2 and storage_name, CONCAT('[', storage_name, ']', now_playing_content), now_playing_content) as `now_playing_content`");
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->watchdog = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
        $this->userFields[] = "((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`keep_alive`)) <= {$this->watchdog}) as `state`";
        if (empty($this->app['reseller'])) {
            $this->userFields[] = 'reseller.name as `reseller_name`';
        }
        $this->app['defTTL'] = ['send_msg' => 7 * 24 * 3600, 'send_msg_with_video' => 7 * 24 * 3600, 'other' => $this->watchdog];
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/users-list');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function users_list()
    {
        $users_filter = [];
        if (!empty($this->data['filters'])) {
            $users_filter = $this->data['filters'];
        }
        if (!empty($this->data['filter_set'])) {
            $curr_filter_set = $this->db->getFilterSet(['id' => $this->data['filter_set']]);
            if (!empty($curr_filter_set) && \count($curr_filter_set) > 0 && !empty($curr_filter_set[0]['filter_set'])) {
                $curr_filter_set[0]['filter_set'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($curr_filter_set[0]['filter_set']);
                if (!empty($curr_filter_set[0]['filter_set'])) {
                    $curr_filter_set[0]['filter_set'] = \array_combine($this->getFieldFromArray($curr_filter_set[0]['filter_set'], 0), $this->getFieldFromArray($curr_filter_set[0]['filter_set'], 2));
                    $users_filter = \array_replace($curr_filter_set[0]['filter_set'], $users_filter);
                }
                $this->app['filter_set'] = $curr_filter_set[0];
            }
        }
        if (!\array_key_exists('state', $users_filter)) {
            $users_filter['state'] = '0';
        }
        if (!\array_key_exists('status', $users_filter)) {
            $users_filter['status'] = '0';
        }
        if (!\array_key_exists('stbmodel', $users_filter)) {
            $users_filter['stbmodel'] = '0';
        }
        $filter_set = \Ministra\Lib\Filters::getInstance();
        $filter_set->setResellerID($this->app['reseller']);
        $filter_set->initData('users', 'id');
        $self = $this;
        $users_filter = \array_filter($users_filter, function ($val) {
            return $val !== 'without';
        });
        if (!empty($users_filter)) {
            $filters = \array_map(function ($row) use($users_filter) {
                $row['title'] = $this->setLocalization($row['title']);
                if (\array_key_exists($row['text_id'], $users_filter)) {
                    $row['value'] = $users_filter[$row['text_id']];
                }
                return $row;
            }, $filter_set->getFilters(\array_keys($users_filter)));
        } else {
            $filters = [];
        }
        if (!empty($this->app['filters'])) {
            $users_filter = \array_merge($this->app['filters'], $users_filter);
        }
        if (!empty($filters)) {
            $this->app['filters_set'] = \array_map(function ($row) {
                if (\is_array($row['values_set'])) {
                    $row['values_set'] = \array_map(function ($row_in) {
                        $row_in['title'] = $this->setLocalization($row_in['title']);
                        return $row_in;
                    }, $row['values_set']);
                }
                return $row;
            }, \array_combine($this->getFieldFromArray($filters, 'text_id'), \array_values($filters)));
        } else {
            $this->app['filters_set'] = [];
        }
        \reset($users_filter);
        while (list($text_id, $row) = \each($users_filter)) {
            if (\array_key_exists($text_id, $this->app['filters_set']) && $this->app['filters_set'][$text_id]['type'] != 'STRING') {
                $value = \explode('|', $row);
                if ($this->app['filters_set'][$text_id]['type'] == 'DATETIME') {
                    $users_filter[$text_id] = ['from' => !empty($value[0]) ? $value[0] : '', 'to' => !empty($value[1]) ? $value[1] : ''];
                } else {
                    $users_filter[$text_id] = $value;
                }
            }
        }
        $this->app['filters'] = $users_filter;
        $filters_template = \array_filter(\array_map(function ($row) use($users_filter) {
            if ((int) $row['default'] || $row['type'] != 'STRING' && $row['type'] != 'DATETIME' && $row['values_set'] === false) {
                return false;
            }
            $row['title'] = $this->setLocalization($row['title']);
            $row['name'] = $row['text_id'];
            $row['checked'] = \array_key_exists($row['text_id'], $users_filter);
            return $row;
        }, $filter_set->getFilters()));
        if (!empty($filters_template)) {
            $this->app['filters_template'] = \array_combine($this->getFieldFromArray($filters_template, 'text_id'), \array_values($filters_template));
        }
        $this->app['allStatus'] = [['id' => 1, 'title' => $this->setLocalization('on')], ['id' => 2, 'title' => $this->setLocalization('off')]];
        $this->app['allState'] = [['id' => 2, 'title' => 'Offline'], ['id' => 1, 'title' => 'Online']];
        $this->app['consoleGroup'] = $this->db->getConsoleGroup(['select' => $this->getUsersGroupsConsolesListFields()]);
        $this->app['formEvent'] = $this->getFormEvent();
        $this->app['allEvent'] = \array_merge($this->getFormEvent(), $this->getHiddenEvent());
        $attribute = $this->getUsersListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', 'false')) {
            $this->app['enableBilling'] = true;
        }
        $this->app['hide_media_info'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_media_info_for_offline_stb', false);
        $this->app['mediaTypeName'] = $this->setLocalization([0 => '--', 1 => 'TV', 2 => 'Video', 3 => 'Karaoke', 4 => 'Audio', 5 => 'Radio', 6 => 'My records', 7 => 'Records', 8 => 'Video Clips', 9 => 'ad', 10 => 'Media browser', 11 => 'Tv archive', 12 => 'Records', 14 => 'TimeShift', 20 => 'Infoportal', 21 => 'Infoportal', 22 => 'Infoportal', 23 => 'Infoportal', 24 => 'Infoportal', 25 => 'Infoportal']);
        if (empty($this->app['reseller'])) {
            $resellers = [['id' => '-', 'name' => '']];
            $this->app['allResellers'] = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
        }
        $reseller_info = $this->db->getReseller(['where' => ['id' => $this->app['reseller']]]);
        $reseller_users = $this->db->getTotalRowsUresList();
        if (!empty($reseller_info[0]['max_users'])) {
            $this->app['resellerUserLimit'] = (int) $reseller_info[0]['max_users'] - (int) $reseller_users > 0;
        } else {
            $this->app['resellerUserLimit'] = true;
        }
        if (!empty($curr_filter_set)) {
            $this->app['breadcrumbs']->addItem($this->setLocalization('Used filter') . ' "' . $curr_filter_set[0]['title'] . '"');
        }
        $this->app['messagesTemplates'] = $this->db->getAllFromTable('messages_templates', 'title');
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        $groups = $this->db->getGroupsList();
        $tariffs = $this->db->getAllTariffPlans();
        $enable_tariff_plans = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', 'false');
        return $this->app['twig']->render($this->getTemplateName('Users::users_list'), \compact('groups', 'tariffs', 'enable_tariff_plans'));
    }
    private function getUsersGroupsConsolesListFields()
    {
        return ['id' => 'Sg.`id` as `id`', 'name' => 'Sg.name as `name`', 'users_count' => '(select count(*) from stb_in_group as Si where Si.stb_group_id = Sg.id) as `users_count`', 'reseller_id' => 'R.id as `reseller_id`', 'reseller_name' => 'R.name as `reseller_name`'];
    }
    private function getFormEvent()
    {
        return [['id' => 'send_msg', 'title' => $this->setLocalization('Sending a message')], ['id' => 'reboot', 'title' => $this->setLocalization('Reboot')], ['id' => 'reload_portal', 'title' => $this->setLocalization('Restart the portal')], ['id' => 'update_channels', 'title' => $this->setLocalization('Update channel list')], ['id' => 'play_channel', 'title' => $this->setLocalization('Playback channel')], ['id' => 'play_radio_channel', 'title' => $this->setLocalization('Playback radio channel')], ['id' => 'mount_all_storages', 'title' => $this->setLocalization('Mount all storages')], ['id' => 'cut_off', 'title' => $this->setLocalization('Switch off')], ['id' => 'update_image', 'title' => $this->setLocalization('Image update')]];
    }
    private function getHiddenEvent()
    {
        return [['id' => 'update_epg', 'title' => $this->setLocalization('EPG update')], ['id' => 'update_subscription', 'title' => $this->setLocalization('Subscribe update')], ['id' => 'update_modules', 'title' => $this->setLocalization('Modules update')], ['id' => 'cut_on', 'title' => $this->setLocalization('Switch on')], ['id' => 'show_menu', 'title' => $this->setLocalization('Show menu')], ['id' => 'additional_services_status', 'title' => $this->setLocalization('Status additional service')]];
    }
    private function getUsersListDropdownAttribute()
    {
        $attribute = [['name' => 'mac', 'title' => 'MAC', 'checked' => true], ['name' => 'ip', 'title' => 'IP', 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Login'), 'checked' => true]];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', 'false')) {
            $attribute[] = ['name' => 'tariff_plan_name', 'title' => $this->setLocalization('Tariff'), 'checked' => true];
        }
        $attribute[] = ['name' => 'group_name', 'title' => $this->setLocalization('Group'), 'checked' => true];
        $attribute[] = ['name' => 'ls', 'title' => $this->setLocalization('Account'), 'checked' => true];
        $attribute[] = ['name' => 'fname', 'title' => $this->setLocalization('Name'), 'checked' => true];
        $attribute[] = ['name' => 'created', 'title' => $this->setLocalization('Created'), 'checked' => false];
        $attribute[] = ['name' => 'now_playing_type', 'title' => $this->setLocalization('Type'), 'checked' => false];
        $attribute[] = ['name' => 'now_playing_content', 'title' => $this->setLocalization('Media'), 'checked' => false];
        $attribute[] = ['name' => 'last_change_status', 'title' => $this->setLocalization('Last modified'), 'checked' => true];
        $attribute[] = ['name' => 'state', 'title' => $this->setLocalization('State'), 'checked' => true];
        $attribute[] = ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true];
        if (empty($this->app['reseller'])) {
            $attribute[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', 'false')) {
            $attribute[] = ['name' => 'expire_billing_date', 'title' => $this->setLocalization('Expire billing date'), 'checked' => true];
        }
        $attribute[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $attribute;
    }
    public function users_consoles_groups()
    {
        if (empty($this->app['reseller'])) {
            $resellers = [['id' => '-', 'name' => $this->setLocalization('Empty')]];
            $this->app['allResellers'] = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
        }
        $attribute = $this->getUsersConsolesGroupsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getUsersConsolesGroupsDropdownAttribute()
    {
        $attribute = [['name' => 'name', 'title' => $this->setLocalization('Name'), 'checked' => true], ['name' => 'users_count', 'title' => $this->setLocalization('Quantity of users'), 'checked' => true]];
        if (empty($this->app['reseller'])) {
            $attribute[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        $attribute[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $attribute;
    }
    public function users_consoles_logs()
    {
        $title = $this->setLocalization('Logs');
        if (isset($this->data['id'])) {
            $id = $this->data['id'];
            $user = $this->db->getUsersByIds([$id]);
            $title = $this->setLocalization('Log of user') . ' ' . (isset($user[0]['login']) ? '(' . $user[0]['login'] . ')' : '');
            $this->app['breadcrumbs']->addItem($title);
        } elseif (isset($this->data['mac'])) {
            $title = $this->setLocalization('Log of user') . " ({$this->data['mac']})";
            $this->app['breadcrumbs']->addItem($title);
        }
        $attribute = $this->getUsersConsolesLogsDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__), ['title' => $title]);
    }
    private function getUsersConsolesLogsDropdownAttribute()
    {
        $attribute = [['name' => 'time', 'title' => $this->setLocalization('Time'), 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('Mac'), 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Login'), 'checked' => true], ['name' => 'action', 'title' => $this->setLocalization('Actions'), 'checked' => true], ['name' => 'object', 'title' => $this->setLocalization('Object'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Type'), 'checked' => true]];
        return $attribute;
    }
    public function users_consoles_report()
    {
        $attribute = $this->getUsersConsolesReportDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['now_time'] = \strftime('%d.%m.%Y') . ' ' . \strftime('%T');
        $this->app['breadcrumbs']->addItem($this->setLocalization('STB statuses report') . ' ' . $this->app['now_time']);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getUsersConsolesReportDropdownAttribute()
    {
        $attribute = [['name' => 'rank', 'title' => '#', 'checked' => true], ['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'status', 'title' => $this->setLocalization('Status'), 'checked' => true], ['name' => 'last_change_status', 'title' => $this->setLocalization('Time'), 'checked' => true]];
        return $attribute;
    }
    public function add_users()
    {
        $this->app['userEdit'] = false;
        $reseller_info = $this->db->getReseller(['where' => ['id' => $this->app['reseller']]]);
        $users_total = $this->db->getTotalRowsUresList();
        if (!empty($reseller_info[0]['max_users'])) {
            $this->app['resellerUserLimit'] = (int) $reseller_info[0]['max_users'] - (int) $users_total > 0;
        } else {
            $this->app['resellerUserLimit'] = true;
        }
        $this->app['tariffPlanFlag'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false);
        $this->app['tariffPlanSubscriptionFlag'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tv_subscription_for_tariff_plans', false);
        if ($this->app['resellerUserLimit']) {
            $data = [];
            if ($this->app['tariffPlanFlag'] == false) {
                $data['activation_code_auto_issue'] = true;
            }
            $form = $this->buildUserForm($data);
            if ($this->saveUsersData($form)) {
                return $this->app->redirect('users-list');
            }
            $this->app['form'] = $form->createView();
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', 'false')) {
                $this->app['enableBilling'] = true;
            }
        }
        $this->app['breadcrumbs']->addItem($this->setLocalization('Users list'), $this->app['controller_alias'] . '/users-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Add user'));
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function buildUserForm(&$data = array(), $edit = false)
    {
        $builder = $this->app['form.factory'];
        $additional_services = ['0' => $this->setLocalization('off'), '1' => $this->setLocalization('on')];
        $status = ['1' => $this->setLocalization('status off'), '0' => $this->setLocalization('status on')];
        $stb_groups = new \Ministra\Lib\StbGroup();
        $all_groups = $stb_groups->getAll();
        $group_keys = $this->getFieldFromArray($all_groups, 'id');
        $group_names = $this->getFieldFromArray($all_groups, 'name');
        if (\is_array($group_keys) && \is_array($group_names) && \count($group_keys) == \count($group_names) && \count($group_keys) > 0) {
            $all_groups = \array_combine($group_keys, $group_names);
        } else {
            $all_groups = [];
        }
        if (!empty($data['id'])) {
            $tmp = $stb_groups->getMemberByUid($data['id']);
            if (!empty($tmp)) {
                $data['group_id'] = $tmp['stb_group_id'];
            }
            $tmp = $this->db->getUserFavItv($data['id']);
            if (!empty($tmp)) {
                $tmp = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($tmp));
                $data['fav_itv'] = \is_array($tmp) ? \count($tmp) : 0;
                $data['fav_itv_on'] = $data['fav_itv'] ? 1 : 0;
            } else {
                $data['fav_itv'] = 0;
                $data['fav_itv_on'] = 0;
            }
            $data['version'] = \str_replace('; ', ';', $data['version']);
            $data['version'] = \str_replace(';', ";\r\n", $data['version']);
        }
        if (empty($this->app['reseller'])) {
            $resellers = [['id' => '-', 'name' => $this->setLocalization('Empty')]];
            $resellers = \array_merge($resellers, $this->db->getAllFromTable('reseller'));
            $resellers = \array_combine($this->getFieldFromArray($resellers, 'id'), $this->getFieldFromArray($resellers, 'name'));
            $this->app['allResellers'] = $resellers;
            if (empty($data['reseller_id'])) {
                $data['reseller_id'] = '-';
            }
        }
        $all_themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
        $themes = [];
        $themes[''] = 'Default';
        foreach ($all_themes as $theme_id => $theme) {
            if (\strpos($theme['name'], 'Ministra 5x - ') !== false) {
                $theme['name'] = 'Smart Launcher - ' . \ucfirst(\str_replace('Ministra 5x - ', '', $theme['name']));
            }
            $themes[$theme_id] = $theme['name'];
        }
        $tariff_expires_time = [];
        if ($this->app['tariffPlanFlag']) {
            $tariff_plans = [];
            $default_id = false;
            foreach ($this->db->getAllTariffPlans() as $num => $row) {
                if ((int) $row['user_default']) {
                    $tariff_plans = [$row['id'] => $row['name']] + $tariff_plans;
                    $default_id = $row['id'];
                } else {
                    $tariff_plans[$row['id']] = $row['name'];
                }
                $tariff_expires_time[$row['id']] = 1 * $row['days_to_expires'];
            }
            if (!empty($tariff_plans) && !\array_key_exists(0, $tariff_plans)) {
                $tariff_plans[0] = '---';
            }
            if (\is_array($data) && \array_key_exists('tariff_plan_id', $data) && (int) $data['tariff_plan_id'] == 0) {
                if (!empty($default_id)) {
                    \settype($default_id, 'int');
                    if (\array_key_exists($default_id, $tariff_plans)) {
                        $data['tariff_plan_id'] = $default_id;
                        $data['tariff_plan_name'] = $tariff_plans[$default_id];
                    }
                }
            }
        }
        $form = $builder->createBuilder('form', $data)->add('id', 'hidden')->add('fname', 'text', ['required' => false])->add('login', 'text', ['required' => false])->add('password', 'password', ['required' => false])->add('phone', 'text', ['required' => false])->add('ls', 'text', ['required' => false])->add('group_id', 'choice', ['choices' => $all_groups, 'data' => !empty($data['group_id']) ? $data['group_id'] : null, 'required' => false])->add('mac', 'text', $edit ? ['required' => false, 'read_only' => true] : ['required' => false])->add('status', 'choice', ['choices' => $status, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($status)])], 'required' => !empty($status)])->add('theme', 'choice', ['choices' => $themes, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($themes)])], 'required' => !empty($themes)])->add('comment', 'textarea', ['required' => false])->add('save', 'submit');
        if (!empty($data['id'])) {
            $form->add('ip', 'text', ['required' => false, 'read_only' => true, 'disabled' => true])->add('parent_password', 'text', ['required' => false, 'read_only' => true, 'disabled' => true])->add('settings_password', 'text', ['required' => false, 'read_only' => true, 'disabled' => true])->add('fav_itv', 'text', ['required' => false, 'read_only' => true, 'disabled' => true])->add('version', 'textarea', ['required' => false, 'read_only' => true, 'disabled' => true])->add('account_balance', 'text', ['required' => false, 'read_only' => true, 'disabled' => true])->add('video_out', 'text', ['required' => false, 'read_only' => true, 'disabled' => true]);
        }
        if (\array_key_exists('tariff-and-service-control', $tariff_and_service_control = $this->app['controllerAccessMap']['users']['action'])) {
            $this->app['tariff_and_service_control'] = $this->app['controllerAccessMap']['users']['action']['tariff-and-service-control']['access'];
        } else {
            $this->app['tariff_and_service_control'] = 0;
        }
        if ($this->app['tariffPlanFlag']) {
            if (!isset($tariff_plans)) {
                $tariff_plans = [];
            }
            $this->app['allTariffPlans'] = $tariff_plans;
            $form->add('tariff_plan_id', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, ['choices' => $tariff_plans, 'choice_attr' => function ($val, $key, $index) use($tariff_expires_time) {
                return ['data-expirestime' => \array_key_exists($val, $tariff_expires_time) ? $tariff_expires_time[$val] : 0];
            }, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($tariff_plans)])], 'required' => !empty($tariff_plans)])->add('tariff_id_instead_expired', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, ['choices' => $tariff_plans, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($tariff_plans)])], 'required' => !empty($tariff_plans)])->add('tariff_expired_date', 'text', ['required' => false]);
        } else {
            $this->app['additionalServices'] = $additional_services;
            $form->add('additional_services_on', 'choice', ['choices' => $additional_services, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($additional_services)])], 'required' => !empty($additional_services)])->add('activation_code_auto_issue', 'checkbox', ['label' => ' ', 'required' => false, 'label_attr' => ['class' => 'label-success'], 'attr' => ['class' => 'form-control']]);
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', 'false')) {
            if (\array_key_exists('billing-date-control', $tariff_and_service_control = $this->app['controllerAccessMap']['users']['action'])) {
                $this->app['billing_date_control'] = $this->app['controllerAccessMap']['users']['action']['billing-date-control']['access'];
            } else {
                $this->app['billing_date_control'] = 0;
            }
            $form->add('expire_billing_date', 'text', ['required' => false]);
        }
        if (empty($this->app['reseller'])) {
            if (\array_key_exists('user-reseller-control', $tariff_and_service_control = $this->app['controllerAccessMap']['users']['action'])) {
                $this->app['user_reseller_control'] = $this->app['controllerAccessMap']['users']['action']['user-reseller-control']['access'];
            } else {
                $this->app['user_reseller_control'] = 0;
            }
            $form->add('reseller_id', 'choice', ['choices' => $resellers, 'constraints' => [new \Symfony\Component\Validator\Constraints\Choice(['choices' => \array_keys($resellers)])], 'required' => !empty($resellers)]);
        }
        return $form->getForm();
    }
    private function saveUsersData(&$form, $edit = false)
    {
        if (!empty($this->method) && $this->method == 'POST') {
            $form->handleRequest($this->request);
            $data = $this->request->get('form');
            $action = isset($this->user) ? 'updateUserById' : 'insertUsers';
            if (\array_key_exists('password', $data) && $edit && empty($data['password'])) {
                unset($data['password']);
            }
            if ($form->isValid()) {
                if (!empty($data['login'])) {
                    if (\array_key_exists('id', $data)) {
                        $this->postData['id'] = $data['id'];
                    }
                    $this->postData['login'] = $data['login'];
                    $check_login = $this->check_login(true);
                    if (!$check_login['chk_rezult']) {
                        $form->get('login')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('Error') . '! ' . $this->setLocalization('Login already used') . '!'));
                        return false;
                    }
                }
                if (!empty($data['mac'])) {
                    $check_params = ['where' => ['users.mac' => $data['mac']]];
                    if (!empty($data['id'])) {
                        $check_params['where']['users.id<>'] = $data['id'];
                    }
                    $check = $this->db->getUsersList($check_params);
                    if (!empty($check)) {
                        $form->get('mac')->addError(new \Symfony\Component\Form\FormError($this->setLocalization('Error: STB with such MAC address already exists') . '!'));
                        return false;
                    }
                }
                $data['activation_code_auto_issue'] = (int) (\array_key_exists('activation_code_auto_issue', $data) && ('on' === $data['activation_code_auto_issue'] || (int) $data['activation_code_auto_issue']));
                $stb_group_id = $data['group_id'] ? $data['group_id'] : false;
                $curr_fields = $this->db->getTableFields('users');
                $curr_fields = $this->getFieldFromArray($curr_fields, 'Field');
                $curr_fields = \array_flip($curr_fields);
                $data = \array_intersect_key($data, $curr_fields);
                $match = [];
                if (!empty($data['expire_billing_date']) && \preg_match("/(0[1-9]|[12][0-9]|3[01])([- \\/\\.])(0[1-9]|1[012])[- \\/\\.](19|20)\\d\\d/im", $data['expire_billing_date'], $match)) {
                    $data['expire_billing_date'] = \implode('-', \array_reverse(\explode($match[2], $data['expire_billing_date'])));
                } else {
                    $data['expire_billing_date'] = 0;
                }
                if (!empty($data['tariff_expired_date']) && \preg_match("/(0[1-9]|[12][0-9]|3[01])([- \\/\\.])(0[1-9]|1[012])[- \\/\\.](19|20)\\d\\d/im", $data['tariff_expired_date'], $match)) {
                    $data['tariff_expired_date'] = \implode('-', \array_reverse(\explode($match[2], $data['tariff_expired_date'])));
                } else {
                    $data['tariff_expired_date'] = 0;
                }
                if ($data['reseller_id'] == '-') {
                    $data['reseller_id'] = null;
                }
                if (!empty($this->user) && \array_key_exists('status', $this->user) && (int) $this->user['status'] != (int) $data['status']) {
                    $data['last_change_status'] = false;
                    $event = new \Ministra\Lib\SysEvent();
                    $event->setUserListById($data['id']);
                    if ((int) $data['status'] == 0) {
                        $event->sendCutOn();
                    } else {
                        $event->sendCutOff();
                    }
                } else {
                    unset($data['last_change_status']);
                }
                unset($data['version']);
                $result = \call_user_func_array([$this->db, $action], [$data, $data['id']]);
                $id = $action == 'updateUserById' && !empty($data['id']) ? $data['id'] : $result;
                $stb_groups = new \Ministra\Lib\StbGroup();
                $member = $stb_groups->getMemberByUid($id);
                if (!empty($stb_group_id)) {
                    $stb_group_params = ['uid' => $id, 'stb_group_id' => $stb_group_id];
                    if (!empty($data['mac'])) {
                        $stb_group_params['mac'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($data['mac']);
                    }
                    if (empty($member)) {
                        $stb_groups->addMember($stb_group_params);
                    } else {
                        $stb_groups->setMember($stb_group_params, $member['id']);
                    }
                } elseif (!empty($member)) {
                    $stb_groups->removeMember($member['id']);
                }
                if (\array_key_exists('password', $data) && !empty($id)) {
                    $password = \md5(\md5($data['password']) . $id);
                    $this->db->updateUserById(['password' => $password, 'access_token' => ''], $id);
                    $this->db->deleteUserTokens($id);
                }
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans') && isset($data['tariff_plan_id']) && isset($this->user) && \array_key_exists('tariff_plan_id', $this->user) && $data['tariff_plan_id'] != $this->user['tariff_plan_id']) {
                    $event = new \Ministra\Lib\SysEvent();
                    $event->setUserListById([(int) $this->user['id']]);
                    $user = \Ministra\Lib\User::getInstance((int) $this->user['id']);
                    $event->sendMsgAndReboot($user->getLocalizedText('Tariff plan is changed, please restart your STB'));
                }
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
                    $this->changeUserPlanPackages($id, !empty($this->postData['tariff_plan_packages']) ? $this->postData['tariff_plan_packages'] : []);
                }
                return true;
            }
        }
        return false;
    }
    public function check_login($local_uses = false)
    {
        if ((!$this->isAjax || $this->method != 'POST' || !\array_key_exists('login', $this->postData)) && !$local_uses) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'form_login';
        $error = $this->setLocalization('Login already used');
        $params = ['login' => \trim($this->postData['login'])];
        if (!empty($this->postData['id'])) {
            $params['id<>'] = $this->postData['id'];
        }
        if ($this->db->checkLogin($params)) {
            $data['chk_rezult'] = !$local_uses ? $this->setLocalization('Login already used') : false;
        } else {
            $data['chk_rezult'] = !$local_uses ? $this->setLocalization('Login is available') : true;
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        if ($this->isAjax && !$local_uses) {
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $data;
    }
    private function changeUserPlanPackages($user_id, $tariff_plan_packages)
    {
        $users_tarif_plans = \array_map(function ($val) {
            $val['optional'] = (int) $val['optional'];
            $val['subscribed'] = (int) $val['subscribed'];
            return $val;
        }, $this->db->getTarifPlanByUserID($user_id));
        $user = \Ministra\Lib\User::getInstance($user_id);
        foreach ($users_tarif_plans as $row) {
            if (\array_key_exists($row['package_id'], $tariff_plan_packages) && $tariff_plan_packages[$row['package_id']] == 'on') {
                $user->subscribeToPackage($row['package_id'], null, true);
            } else {
                $user->unsubscribeFromPackage($row['package_id'], null, true);
            }
        }
    }
    public function edit_users()
    {
        $query_param = ['select' => ['*'], 'where' => [], 'like' => [], 'order' => []];
        $query_param['select'] = \array_merge($query_param['select'], \array_diff($this->userFields, $query_param['select']));
        if ($this->method == 'POST' && !empty($this->postData['form']['id'])) {
            $query_param['where']['users.id'] = $this->postData['form']['id'];
        } elseif ($this->method == 'GET' && !empty($this->data['id'])) {
            $query_param['where']['users.id'] = $this->data['id'];
        } elseif ($this->method == 'GET' && !empty($this->data['mac'])) {
            $query_param['where']['users.mac'] = $this->data['mac'];
        } else {
            return $this->app->redirect('add-users');
        }
        $query_param['order'] = ['users.id' => 'asc'];
        $user = $this->db->getUsersList($query_param);
        $this->user = \is_array($user) && \count($user) > 0 ? $user[0] : [];
        if (empty($this->user)) {
            return $this->app->redirect('add-users');
        }
        $this->app['tariffPlanFlag'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false);
        $this->app['tariffPlanSubscriptionFlag'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tv_subscription_for_tariff_plans', false);
        if (!empty($this->user['expire_billing_date']) && \preg_match("/(19|20\\d\\d)[- \\/\\.](0[1-9]|1[012])[- \\/\\.](0[1-9]|[12][0-9]|3[01])/im", $this->user['expire_billing_date'], $match)) {
            unset($match[0]);
            $this->user['expire_billing_date'] = \implode('-', \array_reverse($match));
        } elseif ((int) \str_replace(['-', '.'], '', $this->user['expire_billing_date']) == 0) {
            $this->user['expire_billing_date'] = '';
        } else {
            $this->user['expire_billing_date'] = \str_replace('.', '-', $this->user['expire_billing_date']);
        }
        if (!empty($this->user['tariff_expired_date']) && \preg_match("/(19|20\\d\\d)[- \\/\\.](0[1-9]|1[012])[- \\/\\.](0[1-9]|[12][0-9]|3[01])/im", $this->user['tariff_expired_date'], $match)) {
            unset($match[0]);
            $this->user['tariff_expired_date'] = \implode('-', \array_reverse($match));
        } elseif ((int) \str_replace(['-', '.'], '', $this->user['tariff_expired_date']) == 0) {
            $this->user['tariff_expired_date'] = '';
        } else {
            $this->user['tariff_expired_date'] = \str_replace('.', '-', $this->user['tariff_expired_date']);
        }
        $this->user['version'] = \preg_replace("/(\r\n|\n\r|\r|\n|\\s){2,}/i", '$1', \stripcslashes($this->user['version']));
        if (empty($this->user['login']) && !empty($this->user['id'])) {
            $this->user['login'] = $this->user['id'];
        }
        \settype($this->user['activation_code_auto_issue'], 'bool');
        $form = $this->buildUserForm($this->user, true);
        if ($this->saveUsersData($form, true)) {
            return $this->app->redirect('users-list');
        }
        $this->app['form'] = $form->createView();
        $this->app['userEdit'] = true;
        $this->app['userID'] = $this->user['id'];
        $users_tarif_plans = \array_map(function ($val) {
            $val['optional'] = (int) $val['optional'];
            $val['subscribed'] = (int) $val['subscribed'];
            return $val;
        }, $this->db->getTarifPlanByUserID($this->user['id']));
        $this->app['userTPs'] = $users_tarif_plans;
        $this->app['state'] = (int) $this->user['state'];
        $tracert = $this->db->getTracertStats($this->user['id']);
        if (!empty($tracert)) {
            $tmp = \json_decode($tracert['info'], true);
            $tracert['info'] = [];
            while (list($key, $item) = \each($tmp)) {
                list($domain, $stat) = \each($item);
                if (!\array_key_exists($domain, $tracert['info'])) {
                    $tracert['info'][$domain] = \array_map(function ($val) {
                        $val[1] = \trim($val[1], '%');
                        return \array_combine(['ip', 'loss', 'ping'], $val);
                    }, $stat);
                }
            }
            $this->app['tracertStats'] = $tracert;
            $tracert_attr = $this->getUsersTracertStatDropdownAttribute();
            $this->checkDropdownAttribute($tracert_attr);
            $this->app['tracert_attr'] = $tracert_attr;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', 'false')) {
            $this->app['enableBilling'] = true;
        }
        $this->app['resellerUserLimit'] = true;
        $this->app['userName'] = $this->user['mac'];
        $this->app['breadcrumbs']->addItem($this->setLocalization('Users list'), $this->app['controller_alias'] . '/users-list');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Edit user'));
        return $this->app['twig']->render($this->getTemplateName('Users::add_users'));
    }
    private function getUsersTracertStatDropdownAttribute()
    {
        $attribute = [['name' => 'ip', 'title' => $this->setLocalization('IP Address'), 'checked' => true], ['name' => 'loss', 'title' => $this->setLocalization('Loss') . ' %', 'checked' => true], ['name' => 'ping', 'title' => $this->setLocalization('Ping'), 'checked' => true]];
        return $attribute;
    }
    public function users_groups_consoles_list()
    {
        if ($this->method == 'GET' && !empty($this->data['id'])) {
            $id = $this->data['id'];
        } else {
            return $this->app->redirect('users-consoles-groups');
        }
        $tmp = $this->db->getConsoleGroup(['select' => $this->getUsersGroupsConsolesListFields(), 'where' => ['Sg.id' => $id]]);
        $this->app['consoleGroup'] = $tmp[0];
        $this->app['groupid'] = $id;
        $attribute = $this->getUsersGroupsConsolesListDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['breadcrumbs']->addItem($this->setLocalization('User groups'), $this->app['controller_alias'] . '/users-consoles-groups');
        $this->app['breadcrumbs']->addItem($this->setLocalization('STB of group') . " '{$this->app['consoleGroup']['name']}'");
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getUsersGroupsConsolesListDropdownAttribute()
    {
        $attribute = [['name' => 'mac', 'title' => $this->setLocalization('MAC'), 'checked' => true], ['name' => 'uid', 'title' => $this->setLocalization('uid'), 'checked' => true]];
        if (!empty($this->app['reseller'])) {
            $attribute[] = ['name' => 'reseller_name', 'title' => $this->setLocalization('Reseller'), 'checked' => true];
        }
        $attribute[] = ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true];
        return $attribute;
    }
    public function user_tariff_plan()
    {
        $planId = (int) $this->postData['plan_id'];
        $userId = (int) $this->postData['user_id'];
        $regular = $optional = [];
        foreach ($this->db->getPackagesInPlan($planId) as $package) {
            if ($package['id'] == 0) {
                continue;
            }
            if ($package['optional'] == 1) {
                $optional[$package['id']] = $package;
                $optional[$package['id']]['subscribed'] = 0;
            } else {
                $regular[$package['id']] = $package;
                $regular[$package['id']]['subscribed'] = 1;
            }
        }
        foreach ($this->db->getOptionalPackagesForUser($userId, \array_keys($optional)) as $package) {
            $optional[$package['package_id']]['subscribed'] = 1;
        }
        $packages = \array_merge($regular, $optional);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($packages), 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_filter_list()
    {
        $dropdownAttribute = $this->getUsersFiltersDropdownAttribute();
        $this->checkDropdownAttribute($dropdownAttribute);
        $allAdmins = $this->db->getAllFromTable('administrators', 'login');
        if (!empty($this->data['filters'])) {
            $this->app['filters'] = $this->data['filters'];
        }
        $this->app['consoleGroup'] = $this->db->getConsoleGroup(['select' => $this->getUsersGroupsConsolesListFields()]);
        $this->app['formEvent'] = $this->getFormEvent();
        $allEvent = \array_merge($this->getFormEvent(), $this->getHiddenEvent());
        $filter_set = \Ministra\Lib\Filters::getInstance();
        $filter_set->setResellerID($this->app['reseller']);
        $filter_set->initData('users', 'id');
        $self = $this;
        $all_title = $this->setLocalization('All');
        $this->app['allFilters'] = \array_map(function ($row) use($filter_set, $all_title) {
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
                    $row_filter_set = $this->setLocalization($filter_set_filter[0]['title']) . ': ';
                    foreach ($data_array as $data_val) {
                        if (!empty($filter_set_filter[0]['values_set']) && \is_array($filter_set_filter[0]['values_set'])) {
                            foreach ($filter_set_filter[0]['values_set'] as $filter_row) {
                                if ((string) $data_val == $filter_row['value']) {
                                    $row_filter_set .= $this->setLocalization($filter_row['title']) . ', ';
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
        }, $this->db->getAllFromTable('filter_set', 'title'));
        $favList = $this->getFavoritesOptions();
        $this->app['messagesTemplates'] = $this->db->getAllFromTable('messages_templates', 'title');
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        return $this->app['twig']->render($this->getTemplateName(__METHOD__), \compact('dropdownAttribute', 'allAdmins', 'allEvent', 'favList'));
    }
    private function getUsersFiltersDropdownAttribute()
    {
        $attribute = [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'login', 'title' => $this->setLocalization('Author'), 'checked' => true], ['name' => 'title', 'title' => $this->setLocalization('Title'), 'checked' => true], ['name' => 'filter_set', 'title' => $this->setLocalization('Filter conditions'), 'checked' => true], ['name' => 'for_all', 'title' => $this->setLocalization('Visibility'), 'checked' => true], ['name' => 'favorites', 'title' => $this->setLocalization('Favorites'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
        return $attribute;
    }
    private function getFavoritesOptions()
    {
        return [['id' => 1, 'title' => $this->setLocalization('Favorites')], ['id' => 2, 'title' => $this->setLocalization('Not in favorites')]];
    }
    public function support_info()
    {
        $this->app['support_langs'] = \array_map(function ($row) {
            return \substr($row, 0, 2);
        }, $this->app['allowed_locales']);
        $def_lang_info = $this->db->getSupportInfoByLang($this->app['language']);
        if (empty($def_lang_info)) {
            $def_lang_info = ['lang' => $this->app['language'], 'content' => ''];
        }
        $this->app['def_lang_info'] = $def_lang_info;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function toggle_status()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['ids']) || !isset($this->postData['userstatus'])) {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $status = $this->postData['userstatus'] == 1 ? 0 : 1;
        $result = $this->db->toggleStatus($ids, $status);
        if (\is_numeric($result) && $result > 0) {
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($ids);
            if ($status == 1) {
                $event->sendCutOn();
            } else {
                $event->sendCutOff();
            }
            return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'message' => $this->setLocalization('Status changed for user(s)', '', $result)]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => false, 'message' => $this->setLocalization('Status changing error')]);
    }
    public function toggle_user()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid']) || !isset($this->postData['userstatus'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['userid'];
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        $result = $this->db->toggleUserStatus($this->postData['userid'], (int) (!$this->postData['userstatus']));
        if (\is_numeric($result)) {
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $error = '';
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($this->postData['userid']);
            if ($this->postData['userstatus'] == 1) {
                $event->sendCutOn();
            } else {
                $event->sendCutOff();
            }
            $this->postData['id'] = $this->postData['userid'];
            $data = \array_merge_recursive($data, $this->users_list_json(true));
            $data['msg'] = $this->setLocalization('Status changed for user(s)', '', 1);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $param = empty($this->data) ? $this->postData : $this->data;
        $query_param = $this->prepareDataTableParams($param, ['operations', '_']);
        $query_param = $this->correctUsersListQueryParams($query_param);
        $filter = $this->getUsersFilters();
        $query_param['in'] = [];
        if (!empty($this->app['filters']) || !empty($this->data['filter_set'])) {
            $filter_set = \Ministra\Lib\Filters::getInstance();
            $filter_set->setResellerID($this->app['reseller']);
            $filter_set->initData('users', 'id');
            $app_filter = [];
            if (!empty($this->app['filters'])) {
                $app_filter = $this->app['filters'];
            } elseif (!empty($this->data['filter_set'])) {
                $data_filter = $this->db->getFilterSet(['id' => $this->data['filter_set']]);
                if (!empty($data_filter[0]) && \array_key_exists('filter_set', $data_filter[0])) {
                    $data_filter = @\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($data_filter[0]['filter_set']);
                    if (\is_array($data_filter) && \count($data_filter) > 0) {
                        $app_filter = \array_combine($this->getFieldFromArray($data_filter, 0), $this->getFieldFromArray($data_filter, 2));
                    }
                }
            }
            $app_filter = \array_filter($app_filter, function ($val) {
                return $val != 'without';
            });
            $all_filters = $filter_set->getFilters();
            $filtered_users = $filters_with_cond = [];
            $greatest = false;
            $cond = '=';
            \reset($all_filters);
            while (list($key, $row) = \each($all_filters)) {
                if (\array_key_exists($row['text_id'], $app_filter)) {
                    if ($row['type'] == 'DATETIME') {
                        if (\is_string($app_filter[$row['text_id']])) {
                            $tmp = \explode('|', $app_filter[$row['text_id']]);
                            $app_filter[$row['text_id']] = ['from' => !empty($tmp[0]) ? $tmp[0] : 0, 'to' => !empty($tmp[1]) ? $tmp[1] : (empty($tmp[0]) ? \time() : 0)];
                        }
                        $filters_with_cond[] = [$row['method'], '>=', $app_filter[$row['text_id']]['from']];
                        if (!empty($app_filter[$row['text_id']]['to'])) {
                            $filters_with_cond[] = [$row['method'], '<=', $app_filter[$row['text_id']]['to']];
                        }
                        continue;
                    } elseif ($row['type'] == 'STRING') {
                        $cond = '*=';
                    } else {
                        $cond = '=';
                        if (\is_string($app_filter[$row['text_id']])) {
                            $tmp = \explode('|', $app_filter[$row['text_id']]);
                            if (empty($tmp) || \is_array($tmp) && (\array_search('0', $tmp, true) !== false || \array_search('', $tmp, true) !== false)) {
                                continue;
                            }
                            $filtered_users[$row['text_id']] = [];
                            foreach ($tmp as $value) {
                                $filter_set->initData('users', 'id');
                                if ($row['text_id'] == 'status' || $row['text_id'] == 'state') {
                                    if ((int) $value) {
                                        $value = (int) ($value - 1 > 0);
                                    } else {
                                        continue;
                                    }
                                }
                                $filter_set->setFilters($row['method'], $cond, $value);
                                $filtered_users[$row['text_id']] = \array_unique(\array_merge($filtered_users[$row['text_id']], $filter_set->getData()));
                            }
                            if ($greatest === false || \count($filtered_users[$row['text_id']]) > \count($filtered_users[$greatest])) {
                                $greatest = $row['text_id'];
                            }
                        }
                        continue;
                    }
                    if (empty($app_filter[$row['text_id']]) || \is_numeric($app_filter[$row['text_id']]) && (int) $app_filter[$row['text_id']] == 0) {
                        continue;
                    }
                    $value = $row['text_id'] == 'status' || $row['text_id'] == 'state' ? (int) ($app_filter[$row['text_id']] - 1 > 0) : $app_filter[$row['text_id']];
                    $filters_with_cond[] = [$row['method'], $cond, $value];
                }
            }
            $filter_set->initData('users', 'id');
            $filter_set->setFilters($filters_with_cond);
            $last = \uniqid();
            $filtered_users[$last] = $filter_set->getData();
            if ($greatest === false || \count($filtered_users[$last]) > \count($filtered_users[$greatest])) {
                $greatest = $last;
            }
            $result = $filtered_users[$greatest];
            unset($filtered_users[$greatest]);
            foreach ($filtered_users as $value) {
                $result = \array_intersect($result, $value);
            }
            $query_param['in'] = ['users.id' => $result];
        }
        $query_param['where'] = \array_merge($query_param['where'], $filter);
        $query_param['select'] = $this->userFields;
        if ($pos = \array_search('reseller_name', $query_param['select'])) {
            unset($query_param['select'][$pos]);
        }
        if (!empty($param['id'])) {
            $query_param['where']['users.id'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsUresList();
        $response['recordsFiltered'] = $this->db->getTotalRowsUresList($query_param['where'], $query_param['like'], $query_param['in']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (!empty($query_param['order'])) {
            if (!empty($query_param['order']['state'])) {
                $query_param['order']['`keep_alive`'] = $query_param['order']['state'];
                unset($query_param['order']['state']);
            } elseif (!empty($query_param['order']['last_change_status'])) {
                $query_param['order']['unix_timestamp(last_change_status)'] = $query_param['order']['last_change_status'];
                unset($query_param['order']['last_change_status']);
            } elseif (!empty($query_param['order']['ls'])) {
                $direct = \strtoupper($query_param['order']['ls']);
                $order = ['ls=0' => $direct == 'ASC' ? 'ASC' : 'DESC', '-ls' => $direct == 'ASC' ? 'DESC' : 'ASC', 'ls' => $direct == 'ASC' ? 'ASC' : 'DESC'];
                unset($query_param['order']['ls']);
                $query_param['order'] = \array_merge($query_param['order'], $order);
            } elseif (!empty($query_param['order']['created'])) {
                $query_param['order']['users.created'] = $query_param['order']['created'];
                unset($query_param['order']['created']);
            } elseif (!empty($query_param['order']['reseller_name'])) {
                $query_param['order']['reseller.name'] = $query_param['order']['reseller_name'];
                unset($query_param['order']['reseller_name']);
            }
        }
        $countries = [];
        $country_field_name = $this->app['lang'] == 'ru' ? 'name' : 'name_en';
        foreach ($this->db->getAllFromTable('countries') as $row) {
            $countries[$row['iso2']] = $row[$country_field_name];
        }
        $geoLink = new \Ministra\Admin\Service\GeoToolService(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('geo_ip_lookup_service', ''));
        $response['data'] = \array_map(function ($val) use($countries, $geoLink) {
            $val['last_active'] = (int) $val['last_active'];
            $val['state'] = (int) $val['state'];
            $val['status'] = (int) $val['status'];
            $val['last_change_status'] = (int) \strtotime($val['last_change_status']);
            $val['last_change_status'] = $val['last_change_status'] > 0 ? $val['last_change_status'] : 0;
            $val['expire_billing_date'] = (int) \strtotime($val['expire_billing_date']);
            $val['expire_billing_date'] = $val['expire_billing_date'] > 0 ? $val['expire_billing_date'] : 0;
            $val['created'] = (int) \strtotime($val['created']);
            $val['created'] = $val['created'] > 0 ? $val['created'] : 0;
            $val['reseller_id'] = !empty($val['reseller_id']) ? $val['reseller_id'] : '-';
            $val['reseller_name'] = !empty($val['reseller_name']) ? $val['reseller_name'] : '';
            \settype($val['now_playing_type'], 'int');
            if (!empty($val['country'])) {
                $val['country_name'] = $countries[$val['country']];
                $val['country'] = \strtolower($val['country']);
            } else {
                $val['country_name'] = $val['country'] = '';
            }
            $val['ip_link'] = $geoLink->getLinkToService($val['ip']);
            $val['RowOrder'] = 'dTRow_' . $val['id'];
            return $val;
        }, $this->db->getUsersList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function correctUsersListQueryParams(array $query_param)
    {
        if (($search = \array_search('state', $query_param['select'])) != false) {
            unset($query_param['select'][$search], $query_param['where']['state'], $query_param['like']['state']);
        }
        if (($search = \array_search('created', $query_param['select'])) != false) {
            unset($query_param['select'][$search], $query_param['where']['created'], $query_param['like']['created']);
        }
        if (isset($query_param['like']['mac'])) {
            $query_param['like']['users.mac'] = $query_param['like']['mac'];
            unset($query_param['like']['mac']);
        }
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        return $query_param;
    }
    private function getUsersFilters()
    {
        $return = [];
        if (empty($this->data['filters']) && empty($this->app['filters'])) {
            $this->app['filters'] = ['interval_from' => '', 'interval_to' => ''];
            return $return;
        }
        $now_timestamp = \time() - $this->watchdog;
        $now_time = \date('Y-m-d H:i:s', $now_timestamp);
        if (\array_key_exists('filters', $this->data) && \is_array($this->data['filters'])) {
            if (\array_key_exists('status', $this->data['filters']) && \is_numeric($this->data['filters']['status']) && $this->data['filters']['status'] != 0 && $this->data['filters']['status'] != 'without') {
                $return['status'] = $this->data['filters']['status'] - 1;
            }
            if (\array_key_exists('state', $this->data['filters']) && \is_numeric($this->data['filters']['state']) && $this->data['filters']['state'] != 0 && $this->data['filters']['state'] != 'without') {
                $return['keep_alive' . ((int) $this->data['filters']['state'] - 1 ? '<' : '>')] = "{$now_time}";
            }
            if (\array_key_exists('interval_from', $this->data['filters']) && $this->data['filters']['interval_from'] != 0 && $this->data['filters']['interval_from'] != 'without') {
                $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['interval_from']);
                $return['UNIX_TIMESTAMP(last_active)>='] = $date->getTimestamp();
            }
            if (\array_key_exists('interval_to', $this->data['filters']) && $this->data['filters']['interval_to'] != 0 && $this->data['filters']['interval_to'] != 'without') {
                $date = \DateTime::createFromFormat('d/m/Y', $this->data['filters']['interval_to']);
                $return['UNIX_TIMESTAMP(last_active)<='] = $date->getTimestamp();
            }
            $this->data['filters']['interval_from'] = empty($this->data['filters']['interval_from']) || $this->data['filters']['interval_from'] == 'without' ? '' : $this->data['filters']['interval_from'];
            $this->data['filters']['interval_to'] = empty($this->data['filters']['interval_to']) || $this->data['filters']['interval_to'] == 'without' ? '' : $this->data['filters']['interval_to'];
            if (!empty($this->app['filters'])) {
                $this->app['filters'] = \array_merge($this->app['filters'], $this->data['filters']);
            } else {
                $this->app['filters'] = $this->data['filters'];
            }
        }
        return $return;
    }
    public function remove_users()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $result = $this->db->removeByIds($ids);
        if (\is_numeric($result)) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'message' => $this->setLocalization('User deleted', '', $result, ['%removed%' => $result])]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => false, 'message' => $this->setLocalization('User deleting error')]);
    }
    public function remove_user()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['userid'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteUserById($this->postData['userid']);
        if (\is_numeric($result)) {
            $error = '';
            $this->db->deleteUserFavItv($this->postData['userid']);
            $this->db->deleteUserFavVclub($this->postData['userid']);
            $this->db->deleteUserFavMedia($this->postData['userid']);
            $this->db->deleteUserTokens($this->postData['userid']);
            \Ministra\Lib\RemotePvr::delAllUserRecs($this->postData['userid']);
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            } else {
                $data['action'] = 'removeUser';
                $data['msg'] = $this->setLocalization('User deleted', '', 1);
                $reseller_info = $this->db->getReseller(['where' => ['id' => $this->app['reseller']]]);
                $users_total = $this->db->getTotalRowsUresList();
                if (!empty($reseller_info[0]['max_users'])) {
                    $data['add_button'] = (int) $reseller_info[0]['max_users'] - (int) $users_total > 0;
                } else {
                    $data['add_button'] = true;
                }
            }
            $stb_groups = new \Ministra\Lib\StbGroup();
            $member = $stb_groups->getMemberByUid($this->postData['userid']);
            if (!empty($member['id'])) {
                $stb_groups->removeMember($member['id']);
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function reset_users_parent_password()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'resetUsersParentPassword';
        $error = $this->setLocalization('Failed');
        $data['newpass'] = '0000';
        $result = $this->db->updateUserById(['parent_password' => '0000'], $this->postData['userid']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
                $data['msg'] = $this->setLocalization('Already reset');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function reset_users_settings_password()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'resetUserSettingsPassword';
        $error = $this->setLocalization('Failed');
        $data['newpass'] = '0000';
        $result = $this->db->updateUserById(['settings_password' => '0000'], $this->postData['userid']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
                $data['msg'] = $this->setLocalization('Already reset');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function reset_user_fav_tv()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'resetUserFavTv';
        $error = $this->setLocalization('Failed');
        $result = $this->db->updateUserFavItv(['fav_ch' => ''], $id = $this->postData['userid']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
                $data['msg'] = $this->setLocalization('Already reset');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function add_console_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['name'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $error = $this->setLocalization('Failed');
        $check = $this->db->getConsoleGroup(['where' => ['Sg.name' => $this->postData['name']]], 'COUNT');
        if (empty($check)) {
            $result = $this->db->insertConsoleGroup(['name' => $this->postData['name']]);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function edit_console_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['name']) || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['id'];
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        $check = $this->db->getConsoleGroup(['where' => ['Sg.name' => $this->postData['name'], 'Sg.id<>' => $this->postData['id']]]);
        if (empty($check)) {
            $result = $this->db->updateConsoleGroup(['name' => $this->postData['name']], ['id' => $this->postData['id']]);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                $data = \array_merge_recursive($data, $this->users_consoles_groups_list_json(true));
            }
        } else {
            $data['msg'] = $error = $this->setLocalization('Name already used');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_consoles_groups_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filds_for_select = $this->getUsersGroupsConsolesListFields();
        $query_param['select'] = \array_values($filds_for_select);
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        foreach ($query_param['order'] as $key => $val) {
            if ($search = \array_search($key, $filds_for_select)) {
                $new_key = \str_replace(" as {$search}", '', $key);
                unset($query_param['order'][$key]);
                $query_param['order'][$new_key] = $val;
            }
        }
        if (!isset($query_param['like'])) {
            $query_param['like'] = [];
        }
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (!empty($this->app['reseller'])) {
            $query_param['where']['reseller_id'] = $this->app['reseller'];
        }
        if (!empty($param['id'])) {
            $query_param['where']['Sg.id'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsConsoleGroup();
        $response['recordsFiltered'] = $this->db->getTotalRowsConsoleGroup($query_param['where'], $query_param['like']);
        $allGroups = $this->db->getConsoleGroup($query_param);
        if (\is_array($allGroups)) {
            $empty_reseller = $this->setLocalization('Empty');
            $response['data'] = \array_map(function ($row) use($empty_reseller) {
                if (empty($row['reseller_name'])) {
                    $row['reseller_name'] = $empty_reseller;
                }
                if (empty($row['reseller_id'])) {
                    $row['reseller_id'] = '-';
                }
                $row['operations'] = '';
                $row['RowOrder'] = 'dTRow_' . $row['id'];
                return $row;
            }, $allGroups);
        }
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function remove_console_group()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['consolegroupid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['consolegroupid'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteConsoleGroup(['id' => $this->postData['consolegroupid']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_console_name()
    {
        if (!$this->isAjax || $this->method != 'POST' || !\array_key_exists('name', $this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = 'console_name';
        $params = ['name' => \trim($this->postData['name'])];
        if (!empty($this->postData['id'])) {
            $params['id<>'] = $this->postData['id'];
        }
        $error = $this->setLocalization('Name already used');
        if ($this->db->checkConsoleName($params)) {
            $data['chk_rezult'] = $this->setLocalization('Name already used');
        } else {
            $data['chk_rezult'] = $this->setLocalization('Name is available');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_groups_consoles_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'state', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (!empty($param['id'])) {
            $query_param['where']['stb_group_id'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsConsoleGroupList($query_param['where']);
        $response['recordsFiltered'] = $this->db->getTotalRowsConsoleGroupList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        }
        $query_param['select'][] = 'stb_in_group.id';
        $response['data'] = \array_map(function ($row) {
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getConsoleGroupList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function remove_console_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['consoleid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['consoleid'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteConsoleItem(['id' => $this->postData['consoleid']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function add_console_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['name']) || empty($this->postData['groupid'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'addConsoleItem';
        $error = $this->setLocalization('Failed');
        $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($this->postData['name']);
        if (!empty($mac)) {
            $check_in_group = $this->db->getConsoleGroupList(['where' => ['mac' => $mac], 'order' => 'mac', 'limit' => ['limit' => 1]]);
            $check_in_users = $this->db->getUsersList(['select' => ['*', 'users.id as uid'], 'where' => ['users.mac' => $mac]]);
            if ((\count($check_in_group) == 0 || (int) $check_in_group[0]['stb_group_id'] == 0) && !empty($check_in_users)) {
                $param = ['mac' => $mac, 'uid' => $check_in_users[0]['uid'], 'stb_group_id' => $this->postData['groupid']];
                $result = $this->db->insertConsoleItem($param);
                if (!empty($result)) {
                    $data['stb_in_group_id'] = $result;
                    $data['uid'] = $param['uid'];
                    $data['mac'] = $param['mac'];
                    $error = '';
                }
            } elseif (!empty($check_in_group)) {
                $group_name = $check_in_group[0]['name'];
                $data['msg'] = $error = $this->setLocalization('This user is already connected to the group') . " '{$group_name}'";
            } elseif (empty($check_in_users)) {
                $data['msg'] = $error = $this->setLocalization('User with this MAC-address is not defined');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function check_console_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['mac'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'checkData';
        $data['input_id'] = !empty($this->postData['input_id']) ? $this->postData['input_id'] : 'item_mac';
        $error = $this->setLocalization('Failed');
        $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($this->postData['mac']);
        $check_in_group = $this->db->getConsoleGroupList(['where' => ['mac' => $mac], 'order' => 'mac', 'limit' => ['limit' => 1, 'offset' => 0]]);
        $check_in_users = $this->db->getUsersList(['where' => ['users.mac' => $mac]]);
        if (!empty($this->postData['form_page']) && $this->postData['form_page'] == 'add_user_form') {
            if (!empty($check_in_users)) {
                $data['chk_rezult'] = $this->setLocalization('Error: STB with such MAC address already exists');
            } else {
                $data['chk_rezult'] = $this->setLocalization('MAC-address can be used');
                $error = '';
            }
        } elseif (\count($check_in_group) != 0 && (int) $check_in_group[0]['stb_group_id'] != 0) {
            $group_name = $check_in_group[0]['name'];
            $data['chk_rezult'] = $this->setLocalization('This user is already connected to the group') . " '{$group_name}'";
            $error = $this->setLocalization('This user is already connected to the group') . " '{$group_name}'";
        } elseif (empty($check_in_users)) {
            $data['chk_rezult'] = $error = $this->setLocalization('User with this MAC-address is not defined');
        } else {
            $data['chk_rezult'] = $this->setLocalization('The user can be added to the group');
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_consoles_logs_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(405);
        }
        $query = $this->prepareDataTableParams($this->data, ['operations', '_']);
        $query['where'] = [];
        $query['select'][] = 'uid';
        $query['select'][] = 'param';
        $deletedParams = $this->checkDisallowFields($query, ['object', 'type']);
        $fields = $this->getUserLogFields();
        $this->cleanQueryParams($query, \array_keys($fields), $fields);
        if (isset($this->data['id'])) {
            $query['where']['user_log.`uid`'] = $this->data['id'];
        } elseif (!empty($this->data['mac'])) {
            $query['where']['user_log.`mac`'] = $this->data['mac'];
        }
        $total = $this->db->getTotalRowsLogList($query['where']);
        $filtered = $this->db->getTotalRowsLogList($query['where'], $query['like']);
        if (empty($query['limit']['limit'])) {
            $query['limit']['limit'] = 50;
        }
        $data = $this->db->getLogList($query);
        $this->setLogObjects($data);
        if (isset($deletedParams['order'])) {
            $this->orderByDeletedParams($data, $deletedParams['order']);
        }
        $data = \array_map(function ($row) {
            $row['time'] = (int) \strtotime($row['time']);
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $data);
        return new \Symfony\Component\HttpFoundation\JsonResponse(['data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'draw' => isset($this->data['draw']) ? $this->data['draw'] : 1]);
    }
    private function getUserLogFields()
    {
        return ['time' => 'CAST(user_log.`time` AS CHAR) as `time`', 'mac' => 'user_log.`mac` as `mac`', 'login' => 'login', 'uid' => 'user_log.`uid` as `uid`', 'action' => 'user_log.`action` as `action`', 'param' => 'user_log.`param` as `param`'];
    }
    private function setLogObjects(&$data)
    {
        $logObjectsTypes = [1 => ['type' => 'itv', 'name' => $this->setLocalization('IPTV channels')], 2 => ['type' => 'video', 'name' => $this->setLocalization('Video club')], 3 => ['type' => 'karaoke', 'name' => $this->setLocalization('Karaoke')], 4 => ['type' => 'audio', 'name' => $this->setLocalization('Audio')], 5 => ['type' => 'radio', 'name' => $this->setLocalization('Radio')], 11 => ['type' => 'tv_archive', 'name' => $this->setLocalization('TV Archive')], 12 => ['type' => 'records', 'name' => $this->setLocalization('Records')], 14 => ['type' => 'timeshift', 'name' => $this->setLocalization('Timeshift')], 111 => ['type' => '', 'name' => ''], 'unknown' => ''];
        while (list($key, $row) = \each($data)) {
            if (\strpos($row['param'], '://')) {
                $data[$key]['object'] = '';
            } else {
                $data[$key]['object'] = $row['param'];
                $data[$key]['param'] = '';
            }
            if (\array_key_exists((int) $row['type'], $logObjectsTypes)) {
                $data[$key]['type'] = $logObjectsTypes[(int) $row['type']]['name'];
            }
            if ($row['type'] == 1) {
                $chanel = [];
                if (!empty($row['param'])) {
                    $chanel = $this->db->getRecord('itv', ['cmd' => $row['param']]);
                }
                if (!empty($chanel['name'])) {
                    $data[$key]['object'] = $chanel['name'];
                }
            } elseif ($row['type'] == 2) {
                \preg_match("/auto \\/media\\/(\\d+)\\.[a-z]*\$/", $row['param'], $tmp_arr);
                $media = [];
                if (!empty($tmp_arr[1])) {
                    $media = $this->db->getRecord('video', ['id' => $tmp_arr[1]]);
                }
                if (!empty($media['name'])) {
                    $data[$key]['object'] = $media['name'];
                }
            } elseif (!\array_key_exists($row['type'], $logObjectsTypes)) {
                $data[$key]['type'] = $logObjectsTypes['unknown'];
            }
        }
    }
    public function users_consoles_report_json()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = [];
        $filds_for_select = ['id' => 'users.id as `id`', 'rank' => '@rank:= if(isnull(@rank), 0, @rank+1) as `rank`', 'mac' => 'users.mac as `mac`', 'status' => 'users.status as `status`', 'last_change_status' => 'CAST(users.`last_change_status` AS CHAR) as `last_change_status`'];
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['state', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        } else {
            $query_param['select'][] = 'id';
        }
        $this->cleanQueryParams($query_param, ['id', 'rank', 'mac', 'status', 'last_change_status'], $filds_for_select);
        $query_param['where']['UNIX_TIMESTAMP(last_change_status)>='] = \mktime(0, 0, 0, \date('n'), \date('j'), \date('Y'));
        $response['recordsTotal'] = $this->db->getTotalRowsUresList(['UNIX_TIMESTAMP(last_change_status)>=' => \mktime(0, 0, 0, \date('n'), \date('j'), \date('Y'))]);
        $response['recordsFiltered'] = $this->db->getTotalRowsUresList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        $response['data'] = \array_map(function ($row) {
            $row['last_change_status'] = (int) \strtotime($row['last_change_status']);
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getUsersList($query_param, true));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        if ($this->isAjax) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    public function set_expire_billing_date()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['userid']) || empty($this->postData['setaction'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['userid'];
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        if ($this->postData['setaction'] == 'set' && !empty($this->postData['expire_date'])) {
            $date = $this->postData['expire_date'];
        } elseif ($this->postData['setaction'] == 'unset') {
            $date = 0;
        }
        if (isset($date)) {
            if (!empty($date) && \preg_match("/(0[1-9]|[12][0-9]|3[01])([- \\/\\.])(0[1-9]|1[012])[- \\/\\.](19|20)\\d\\d/im", $date, $match)) {
                $date = \implode('-', \array_reverse(\explode($match[2], $date)));
            }
            $result = $this->db->updateUserById(['expire_billing_date' => $date], $this->postData['userid']);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                $this->postData['id'] = $this->postData['userid'];
                $data = \array_merge_recursive($data, $this->users_list_json(true));
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_user_to_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || empty($this->postData['source_id']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        $data['id'] = $user_id = $this->postData['id'];
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        if (!empty($target_id)) {
            $count_reseller = $this->db->getReseller(['select' => ['*'], 'where' => ['id' => $target_id]], true);
        } else {
            $count_reseller = 1;
        }
        if (!empty($count_reseller)) {
            $result = $this->db->updateResellerMemberByID('users', $user_id, $target_id);
            if (\is_numeric($result)) {
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                $error = '';
                $data = \array_merge_recursive($data, $this->users_list_json(true));
                $data['msg'] = $this->setLocalization('Reseller is assigned to user');
            }
        } else {
            $error = $data['msg'] = $this->setLocalization('Not found reseller for moving');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function move_user_group_to_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['consolegroupid']) || empty($this->postData['target_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $console_group_id = $this->postData['consolegroupid'];
        $data['data'] = [];
        $target_id = $this->postData['target_id'] !== '-' ? $this->postData['target_id'] : null;
        $error = $this->setLocalization('Failed');
        if (!empty($target_id)) {
            $count_reseller = $this->db->getReseller(['select' => ['*'], 'where' => ['id' => $target_id]], true);
        } else {
            $count_reseller = 1;
        }
        if (!empty($count_reseller)) {
            $this->db->updateResellerMemberByID('stb_groups', $console_group_id, $target_id);
            $result = $this->db->updateResellerMemberByID('stb_groups', $console_group_id, $target_id);
            if (\is_numeric($result)) {
                $error = '';
                if ($result === 0) {
                    $data['nothing_to_do'] = true;
                }
                $data['msg'] = $this->setLocalization('Moved');
                $this->postData['id'] = $this->postData['consolegroupid'];
                $data = \array_merge_recursive($data, $this->users_consoles_groups_list_json(true));
            }
        } else {
            $error = $this->setLocalization('Not found reseller for moving');
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_filter()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['text_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'addFilter';
        $error = $this->setLocalization('Not exists');
        $filter_set = \Ministra\Lib\Filters::getInstance();
        $filter_set->setResellerID($this->app['reseller']);
        $filter_set->initData('users', 'id');
        $data['filter'] = $filter_set->getFilters($this->postData['text_id']);
        if (!empty($data['filter'])) {
            $error = '';
            $data['filter']['title'] = $this->setLocalization($data['filter']['title']);
            unset($data['filter']['method']);
            if (!empty($data['filter']['values_set'])) {
                \reset($data['filter']['values_set']);
                while (list($key, $row) = \each($data['filter']['values_set'])) {
                    $data['filter']['values_set'][$key]['title'] = $this->setLocalization($row['title']);
                }
            } elseif ($data['filter']['type'] != 'STRING' && $data['filter']['type'] != 'DATETIME' && empty($data['filter']['values_set'])) {
                $data['msg'] = $error = $this->setLocalization('No data for this filter');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_filter()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['filter_set'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'saveFilterData';
        $error = $this->setLocalization('Not enough data');
        $params = $this->postData['filter_set'];
        if (!empty($params['filter_set'])) {
            $params['filter_set'] = \json_decode(\urldecode($params['filter_set']), true);
        }
        if (!empty($params['title']) && !empty($params['filter_set'])) {
            $filter_set = \Ministra\Lib\Filters::getInstance();
            $filter_set->setResellerID($this->app['reseller']);
            $filter_set->initData('users', 'id');
            $app_filter = $params['filter_set'];
            $all_filters = $filter_set->getFilters();
            $filters_with_cond = \array_filter(\array_map(function ($row) use($app_filter) {
                if (\array_key_exists($row['text_id'], $app_filter) and \trim(\trim($app_filter[$row['text_id']], '|')) != '') {
                    if ($row['type'] == 'STRING') {
                        $cond = '*=';
                    } elseif ($row['type'] == 'DATETIME') {
                        $cond = '>=';
                    } else {
                        $cond = '=';
                    }
                    return [$row['text_id'], $cond, $app_filter[$row['text_id']]];
                }
                return false;
            }, $all_filters));
            $params['filter_set'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($filters_with_cond);
            if (!empty($params['filter_set'])) {
                $current = $this->db->getFilterSet(['id' => $params['id'], 'admin_id' => $params['admin_id']]);
                if (!empty($current)) {
                    $operation = 'update';
                    $filter_data['id'] = $params['id'];
                } else {
                    $operation = 'insert';
                }
                $filter_data['params'] = $params;
                unset($params['id']);
                $filter_data['params']['for_all'] = (int) (\array_key_exists('for_all', $params) && !empty($params['for_all']));
                $return_id = 0;
                $result = \call_user_func_array([$this->db, $operation . 'FilterSet'], $filter_data);
                if (\is_numeric($result)) {
                    $error = '';
                    if ($result === 0) {
                        $data['nothing_to_do'] = true;
                        $data['msg'] = $this->setLocalization('Nothing to do');
                    }
                    if ($operation == 'insert') {
                        $data['return_id'] = $result;
                    }
                }
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function remove_filter()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteFilter($this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function toggle_filter_favorite()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || !\array_key_exists('favorite', $this->postData)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $data['action'] = 'updateTableRow';
        $data['id'] = $this->postData['id'];
        $data['data'] = [];
        $error = $this->setLocalization('Failed');
        $result = $this->db->toggleFilterFavorite($this->postData['id'], (int) $this->postData['favorite'] == 1 ? 0 : 1);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data = \array_merge_recursive($data, $this->users_filter_list_json(true));
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function users_filter_list_json($local_uses = false)
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
        $filds_for_select = $this->getFilterSetFields();
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (isset($this->data['filters']['admin_id']) && $this->data['filters']['admin_id'] > 0) {
            $query_param['where'] = \array_merge($query_param['where'], ['A.id' => $this->data['filters']['admin_id']]);
        }
        if (isset($this->data['filters']['favorites']) && \in_array($this->data['filters']['favorites'], [1, 2])) {
            $query_param['where'] = \array_merge($query_param['where'], ['favorites' => $this->data['filters']['favorites'] == 2 ? 0 : 1]);
        }
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (!empty($param['id'])) {
            $query_param['where']['F_S.id'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsUsersFilters();
        $response['recordsFiltered'] = $this->db->getTotalRowsUsersFilters($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        }
        $filter_set = \Ministra\Lib\Filters::getInstance();
        $filter_set->setResellerID($this->app['reseller']);
        $filter_set->initData('users', 'id');
        $all_title = $this->setLocalization('All');
        $response['data'] = \array_map(function ($row) use($filter_set, $all_title) {
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
                    $row_filter_set = $this->setLocalization($filter_set_filter[0]['title']) . ': ';
                    foreach ($data_array as $data_val) {
                        if (!empty($filter_set_filter[0]['values_set']) && \is_array($filter_set_filter[0]['values_set'])) {
                            foreach ($filter_set_filter[0]['values_set'] as $filter_row) {
                                if ((string) $data_val == $filter_row['value']) {
                                    $row_filter_set .= $this->setLocalization($filter_row['title']) . ', ';
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
            $row['RowOrder'] = 'dTRow_' . $row['id'];
            return $row;
        }, $this->db->getUsersFiltersList($query_param));
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getFilterSetFields()
    {
        return ['id' => 'F_S.`id` as `id`', 'login' => 'A.`login` as `login`', 'title' => 'F_S.title as `title`', 'filter_set' => 'F_S.filter_set as `filter_set`', 'for_all' => 'F_S.for_all as `for_all`', 'favorites' => 'F_S.`favorites` as `favorites`'];
    }
    public function get_autocomplete_watching_tv()
    {
        if (!$this->isAjax || empty($this->data)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $return_array = [];
        if (!empty($this->data['term']) && ($term = \trim($this->data['term'])) && \strlen($term) >= 3) {
            $return_array = \array_filter($this->db->getTVChannelNames($term));
        }
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($return_array), 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_autocomplete_watching_movie()
    {
        if (!$this->isAjax || empty($this->data)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $return_array = [];
        if (!empty($this->data['term']) && ($term = \trim($this->data['term'])) && \strlen($term) >= 3) {
            $return_array = \array_filter($this->db->getTVChannelNames($term));
        }
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($return_array), 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_autocomplete_stbfirmware_version()
    {
        if (!$this->isAjax || empty($this->data)) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $return_array = [];
        if (!empty($this->data['term']) && ($term = \trim($this->data['term'])) && \strlen($term) >= 3) {
            $str_len_offset = \ceil((20 - \strlen($term)) / 2);
            $return_array = \array_filter(\array_map(function ($row) use($term, $str_len_offset) {
                $pos = \strpos($row, $term);
                $begin = $pos !== false ? $pos : 0;
                return \substr($row, $begin, \strlen($term) + $str_len_offset * 2);
            }, $this->db->getStbFirmwareVersion($term)));
        }
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($return_array), 200, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_subscribed_tv()
    {
        if (!$this->isAjax || empty($this->postData['user_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['action' => 'setSubscribedTVModal', 'user_id' => (int) $this->postData['user_id']];
        $error = $this->setLocalization('Error');
        $subscribed_tv_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($this->db->getSubChannelsDB((int) $this->postData['user_id'])));
        if (\is_array($subscribed_tv_ids) && !empty($subscribed_tv_ids)) {
            $subscribed_tv_ids = \implode(', ', $subscribed_tv_ids);
            $data['subscribed_tv'] = \array_map(function ($row) {
                return ['id' => $row['id'], 'name' => $row['name'], 'cost' => $row['cost']];
            }, $this->db->getITV(['base_ch' => 0, 'id IN(' . $subscribed_tv_ids . ') AND 1=' => 1], 'ALL'));
            $data['not_subscribed_tv'] = \array_map(function ($row) {
                return ['id' => $row['id'], 'name' => $row['name'], 'cost' => $row['cost']];
            }, $this->db->getITV(['base_ch' => 0, 'id NOT IN(' . $subscribed_tv_ids . ') AND 1=' => 1], 'ALL'));
            $error = '';
        } else {
            $data['subscribed_tv'] = [];
            $data['not_subscribed_tv'] = \array_map(function ($row) {
                return ['id' => $row['id'], 'name' => $row['name'], 'cost' => $row['cost']];
            }, $this->db->getITV(['base_ch' => 0], 'ALL'));
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_subscribed_tv()
    {
        if (!$this->isAjax || empty($this->postData['user_id'])) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['action' => 'hideSubscribedTVModal'];
        $error = $this->setLocalization('Error');
        $sub_ch = !empty($this->postData['sub_ch']) && \is_array($this->postData['sub_ch']) ? $this->postData['sub_ch'] : [];
        $params = ['sub_ch' => \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($sub_ch)), 'bonus_ch' => '', 'addtime' => 'NOW()'];
        if ($this->db->getSubChannelsDB((int) $this->postData['user_id']) && $this->db->updateSubChannelsDB($params, $this->postData['user_id'])) {
            $error = '';
        } elseif (($params['uid'] = $this->postData['user_id']) && $this->db->insertSubChannelsDB($params)) {
            $error = '';
        } else {
            $error = $this->setLocalization('Write database error');
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function get_support_content()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['action' => 'setSupportContent', 'data' => []];
        $error = $this->setLocalization('Error');
        if (!empty($this->postData['lang'])) {
            $data['data'] = $this->db->getSupportInfoByLang($this->postData['lang']);
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_support_content()
    {
        if (!$this->isAjax) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['action' => ''];
        $error = $this->setLocalization('Error');
        $support_info_data = \array_intersect_key($this->postData, \array_flip($this->getFieldFromArray($this->db->getTableFields('support_info'), 'Field')));
        $db_data = $this->db->getSupportInfoByLang($this->postData['lang']);
        if (empty($db_data)) {
            $operation = 'insert';
            $params = [$support_info_data];
        } else {
            $operation = 'update';
            $params = [['id' => $db_data['id']], $support_info_data];
        }
        $result = \call_user_func_array([$this->db, $operation . 'SupportInfo'], $params);
        if (\is_numeric($result)) {
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $error = '';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function clear_users_group()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $group = new \Ministra\Lib\StbGroup();
        $result = $group->removeMembersByIds($ids);
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'updated' => $result]);
    }
    public function assign_users_group()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $groupId = $this->postData['group'];
        $StbGroup = new \Ministra\Lib\StbGroup();
        $group = $StbGroup->getById($groupId);
        if (!$group) {
            $messageTpl = $this->setLocalization('No Group with ID %s');
            return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => false, 'message' => \sprintf($messageTpl, $groupId)]);
        }
        $users = $this->db->getUsersByIds($ids);
        $result = $StbGroup->addMembers($users, $group['id']);
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'assigned' => $result]);
    }
    public function change_users_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $resellerId = $this->postData['reseller'];
        $reseller = $this->db->getReseller(['where' => ['id' => $resellerId]]);
        if (\count($reseller) < 1) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => false, 'message' => $this->setLocalization('Reseller not found')]);
        }
        $result = $this->db->changeReseller($reseller[0]['id'], $ids);
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'message' => $this->setLocalization('Reseller is assigned to user', '', $result)]);
    }
    public function clear_users_reseller()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $result = $this->db->changeReseller(null, $ids);
        if ($result) {
            return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'message' => $this->setLocalization('Reseller is unassigned from user', '', $result)]);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => false, 'message' => $this->setLocalization('Reseller unassigning error')]);
    }
    public function change_users_tariff()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(405);
        }
        $ids = $this->postData['ids'];
        $tariffId = $this->postData['tariff'];
        $users = $this->db->getUsersByIds($ids);
        $allUsers = [];
        $locales = [];
        foreach ($users as $user) {
            if ($user['tariff_plan_id'] == $tariffId) {
                continue;
            }
            $allUsers[] = $user['id'];
            if (isset($user['mac']) || isset($user['login'])) {
                $locales[$user['locale']][] = $user['id'];
            }
        }
        $changed = $this->db->changeTariffPlan($tariffId, $allUsers);
        $messages = 0;
        foreach ($locales as $locale => $users) {
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($users);
            $message = $this->setLocalization('Tariff plan is changed, please restart your STB', '', false, [], $locale);
            $event->sendMsgAndReboot($message);
            $messages += \count($users);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(['success' => true, 'changed' => $changed, 'messages' => $messages]);
    }
    private function getAddUserFormParam($edit)
    {
        if (!$edit) {
            return ['constraints' => [new \Symfony\Component\Validator\Constraints\NotBlank()], 'required' => true];
        }
        return ['required' => false];
    }
    private function getCostSubChannels($id = 0)
    {
        if ($id == 0) {
            return 0.0;
        }
        $sub_ch = $this->getSubChannels($id);
        return \number_format((float) ($this->db->getCostSubChannelsDB($sub_ch) / 100), 2, '.', ' ');
    }
    private function getSubChannels($id = 0)
    {
        if ($id == 0) {
            return [];
        }
        $sub_ch = $this->db->getSubChannelsDB($id);
        $sub_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($sub_ch));
        if (!\is_array($sub_ch)) {
            return [];
        }
        return $sub_ch;
    }
}
