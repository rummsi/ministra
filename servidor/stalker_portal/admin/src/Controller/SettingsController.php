<?php

namespace Ministra\Admin\Controller;

use Ministra\Admin\Lib\Theme;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response as Response;
class SettingsController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    private $theme_preset = array('id' => '', 'name' => '', 'previews' => '', 'type' => '', 'default' => false, 'devices' => array('pc', 'laptop', 'phone', 'tablet'), 'original_bg' => array(1080 => '', 720 => '', 576 => '', 480 => ''), 'bg' => array(1080 => '', 720 => '', 576 => '', 480 => ''), 'logo' => array(1080 => '', 720 => '', 576 => '', 480 => '', 'align' => 'left'));
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        if (empty($this->app['action_alias'])) {
            return $this->app->redirect($this->app['controller_alias'] . '/themes');
        }
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function themes()
    {
        $current = $this->db->getCurrentTheme();
        $this->app['current_theme'] = ['id' => $current];
        $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
        $theme_arr = [];
        if (\is_array($themes)) {
            $launcher_transparent_preview = $this->workURL . '/img/launcherpreview/launcher@3x.png';
            \reset($themes);
            while (list($key, $row) = \each($themes)) {
                $theme_arr[$key] = \array_replace($this->theme_preset, $row);
                if ($theme_arr[$key]['type'] !== 'classic') {
                    $theme_arr[$key]['preview'] = $launcher_transparent_preview;
                    $launcher_theme = new \Ministra\Admin\Lib\Theme($theme_arr[$key]['alias']);
                    \reset($theme_arr[$key]['bg']);
                    while (list($res, $path) = \each($theme_arr[$key]['bg'])) {
                        if (!($theme_arr[$key]['bg'][$res] = $launcher_theme->getCustomBackgroundImageUrl(null, $res))) {
                            $theme_arr[$key]['bg'][$res] = $launcher_theme->getOriginalBackgroundImageUrl(null, $res);
                        }
                        $theme_arr[$key]['logo'][$res] = $launcher_theme->getCustomLogoImageUrl(null, $res);
                        if (!isset($theme_arr[$key]['bg'][$res])) {
                            $theme_arr[$key]['bg'][$res] = '';
                        }
                        if (!isset($theme_arr[$key]['logo'][$res])) {
                            $theme_arr[$key]['logo'][$res] = '';
                        }
                    }
                    $theme_arr[$key]['logo']['align'] = $launcher_theme->getThemeVar('logoAlign', 'left');
                } else {
                    $theme_arr[$key]['devices'] = ['pc'];
                }
                $theme_arr[$key]['type_name'] = \ucwords(\str_replace('_', ' ', $theme_arr[$key]['type']));
                if (\strpos($theme_arr[$key]['name'], 'Ministra 5x - ') !== false) {
                    $theme_arr[$key]['name'] = \ucfirst(\str_replace('Ministra 5x - ', '', $theme_arr[$key]['name']));
                }
            }
        }
        \krsort($theme_arr);
        $this->app['allData'] = $theme_arr;
        $attribute = $this->getDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getDropdownAttribute()
    {
        return [['name' => 'preview', 'title' => $this->setLocalization('Preview'), 'checked' => true], ['name' => 'type', 'title' => $this->setLocalization('Type'), 'checked' => true], ['name' => 'name', 'title' => $this->setLocalization('Name'), 'checked' => true], ['name' => 'devices', 'title' => $this->setLocalization('Supported devices'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function themes_edit()
    {
        if ($this->method == 'POST' && !empty($this->postData['id'])) {
            $id = $this->postData['id'];
        } else {
            if ($this->method == 'GET' && !empty($this->data['id'])) {
                $id = $this->data['id'];
            } else {
                $id = false;
            }
        }
        $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
        if ($id && \array_key_exists($id, $themes)) {
            $theme = \array_replace($this->theme_preset, $themes[$id]);
            $launcher_theme = new \Ministra\Admin\Lib\Theme($theme['alias']);
            if (\array_key_exists('logoAlign', $this->postData)) {
                $launcher_theme->setVariable('logoAlign', $this->postData['logoAlign'], true);
                if ($this->postData['logoAlign'] != $launcher_theme->getThemeVar('logoAlign', 'left')) {
                    $launcher_theme->resetParam('logoAlign');
                }
            }
            if (\array_key_exists('to_default', $this->postData)) {
                if (\array_key_exists('all', $this->postData['to_default'])) {
                    $launcher_theme->reset();
                } else {
                    if (\array_key_exists('background_image', $this->postData['to_default'])) {
                        foreach ($this->postData['to_default']['background_image'] as $height) {
                            $launcher_theme->resetBackgroundImage(null, $height);
                        }
                    }
                    if (\array_key_exists('logo_image', $this->postData['to_default'])) {
                        $launcher_theme->resetParam('logoAlign');
                        $launcher_theme->resetParam('logoFilename');
                    }
                }
            }
            \reset($theme['bg']);
            while (list($res, $path) = \each($theme['bg'])) {
                $theme['original_bg'][$res] = $launcher_theme->getOriginalBackgroundImageUrl(null, $res);
                $theme['bg'][$res] = $launcher_theme->getCustomBackgroundImageUrl(null, $res);
                if (empty($theme['bg'][$res])) {
                    $theme['bg'][$res] = $theme['original_bg'][$res];
                }
                $theme['logo'][$res] = $launcher_theme->getCustomLogoImageUrl(null, $res);
            }
            $theme['logo']['align'] = $launcher_theme->getThemeVar('logoAlign', 'left');
            if (\strpos($theme['name'], 'Ministra 5x - ') !== false) {
                $theme['name'] = 'Smart Launcher - ' . \ucfirst(\str_replace('Ministra 5x - ', '', $theme['name']));
            } else {
                $theme['name'] = 'Classic - ' . \ucfirst($theme['name']);
            }
            $this->app['theme_name'] = $theme['name'];
            $this->app['launcher_theme'] = $theme;
        } else {
            $this->app['theme_name'] = $this->setLocalization('Undefined');
        }
        $this->app['breadcrumbs']->addItem($this->setLocalization('Appearance'), $this->app['controller_alias'] . '/themes');
        $this->app['breadcrumbs']->addItem($this->setLocalization('Edit theme') . ' "' . $this->app['theme_name'] . '"');
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function common()
    {
        $attribute = $this->getCommonDropdownAttribute();
        $this->checkDropdownAttribute($attribute);
        $this->app['dropdownAttribute'] = $attribute;
        $this->app['allowed_stb'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::F2b8f2900b54f7c71fdeae713c917b860('allowed_stb_types', false, true);
        $this->app['stbGroups'] = $this->db->getAllFromTable('stb_groups');
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    private function getCommonDropdownAttribute()
    {
        return [['name' => 'id', 'title' => $this->setLocalization('ID'), 'checked' => true], ['name' => 'stb_type', 'title' => $this->setLocalization('STB model'), 'checked' => true], ['name' => 'require_image_version', 'title' => $this->setLocalization('STB API version'), 'checked' => true], ['name' => 'require_image_date', 'title' => $this->setLocalization('Image date'), 'checked' => true], ['name' => 'update_type', 'title' => $this->setLocalization('Update type'), 'checked' => true], ['name' => 'prefix', 'title' => $this->setLocalization('Prefix'), 'checked' => true], ['name' => 'image_description_contains', 'title' => $this->setLocalization('Required image description'), 'checked' => true], ['name' => 'image_version_contains', 'title' => $this->setLocalization('Required STB API version'), 'checked' => true], ['name' => 'hardware_version_contains', 'title' => $this->setLocalization('Required hardware version'), 'checked' => true], ['name' => 'enable', 'title' => $this->setLocalization('Automatic update'), 'checked' => true], ['name' => 'stb_group_name', 'title' => $this->setLocalization('User groups'), 'checked' => true], ['name' => 'operations', 'title' => $this->setLocalization('Operations'), 'checked' => true]];
    }
    public function set_current_theme()
    {
        if (!$this->isAjax || $this->method != 'POST') {
            $this->app->abort(404, 'Page not found');
        }
        $data = ['theme' => ['default' => $this->setLocalization('not changed'), 'default_launcher' => $this->setLocalization('not changed')]];
        $error = $this->setLocalization('There is no such skin');
        $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
        if (!empty($themes) && (!empty($this->postData['default']) || !empty($this->postData['default_launcher']))) {
            \reset($themes);
            $result = false;
            while (list($id, $theme) = \each($themes)) {
                if (empty($this->postData['default']) && empty($this->postData['default_launcher'])) {
                    $error = '';
                    break;
                }
                if ($theme['id'] == $this->postData['default'] && (($result = $this->db->setCurrentTheme($this->postData['default'])) || \is_numeric($result))) {
                    if ($result !== 0) {
                        $data['theme']['default'] = $theme['name'];
                        if (\strpos($data['theme']['default'], 'Ministra 5x - ') !== false) {
                            $data['theme']['default'] = 'Smart Launcher - ' . \ucfirst(\str_replace('Ministra 5x - ', '', $data['theme']['default']));
                        } else {
                            $data['theme']['default'] = 'Classic - ' . \ucfirst($data['theme']['default']);
                        }
                    }
                    $this->postData['default'] = null;
                }
                if ($theme['alias'] == $this->postData['default_launcher']) {
                    if (!$theme['default_launcher']) {
                        $launcher_theme = new \Ministra\Admin\Lib\Theme($this->postData['default_launcher']);
                        $launcher_theme->setAsDefault();
                        $data['theme']['default_launcher'] = $theme['name'];
                        if (\strpos($data['theme']['default_launcher'], 'Ministra 5x - ') !== false) {
                            $data['theme']['default_launcher'] = 'Smart Launcher - ' . \ucfirst(\str_replace('Ministra 5x - ', '', $data['theme']['default_launcher']));
                        }
                    }
                    $this->postData['default_launcher'] = null;
                }
            }
            if (empty($this->postData['default']) && empty($this->postData['default_launcher'])) {
                $error = '';
            }
            $data['msg'] = $this->setLocalization('Current TV-theme - "{thmnm}", another platform - "{lthmnm}"', '', true, ['{thmnm}' => $data['theme']['default'], '{lthmnm}' => $data['theme']['default_launcher']]);
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function save_common_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $item = [$this->postData];
        if (empty($this->postData['id'])) {
            $operation = 'insertCommon';
        } else {
            $operation = 'updateCommon';
            $data['id'] = $item['id'] = $this->postData['id'];
        }
        unset($item[0]['id']);
        $error = $this->setLocalization('Failed');
        $result = \call_user_func_array([$this->db, $operation], $item);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            if ($operation == 'updateCommon') {
                $data = \array_merge_recursive($data, $this->common_list_json(true));
                $data['action'] = 'updateTableRow';
                $data['msg'] = $this->setLocalization('Changed');
            } else {
                $data['msg'] = $this->setLocalization('Added');
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function common_list_json($local_uses = false)
    {
        if (!$this->isAjax && $local_uses === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $response = ['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0, 'action' => 'setCommonModal'];
        $error = $this->setLocalization('Error');
        $param = !empty($this->data) ? $this->data : $this->postData;
        $query_param = $this->prepareDataTableParams($param, ['operations', 'RowOrder', '_']);
        if (!isset($query_param['where'])) {
            $query_param['where'] = [];
        }
        $filds_for_select = $this->getCommonFields();
        $this->cleanQueryParams($query_param, \array_keys($filds_for_select), $filds_for_select);
        if (empty($query_param['select'])) {
            $query_param['select'] = \array_values($filds_for_select);
        }
        if (!empty($param['id'])) {
            $query_param['where']['I_U_S.id'] = $param['id'];
        }
        $response['recordsTotal'] = $this->db->getTotalRowsCommonList();
        $response['recordsFiltered'] = $this->db->getTotalRowsCommonList($query_param['where'], $query_param['like']);
        if (empty($query_param['limit']['limit'])) {
            $query_param['limit']['limit'] = 50;
        } elseif ($query_param['limit']['limit'] == -1) {
            $query_param['limit']['limit'] = false;
        }
        if (empty($query_param['order'])) {
            $query_param['order']['id'] = 'asc';
        }
        $commonList = $this->db->getCommonList($query_param);
        $convert = $this->method == 'GET' || $local_uses;
        $response['data'] = \array_map(function ($val) use($convert) {
            $val['enable'] = (int) $val['enable'];
            if ($convert) {
                $val['require_image_date'] = (int) \strtotime($val['require_image_date']);
                if ($val['require_image_date'] < 0) {
                    $val['require_image_date'] = 0;
                }
            }
            $val['RowOrder'] = 'dTRow_' . $val['id'];
            return $val;
        }, $commonList);
        $response['draw'] = !empty($this->data['draw']) ? $this->data['draw'] : 1;
        $error = '';
        if ($this->isAjax && !$local_uses) {
            $response = $this->generateAjaxResponse($response);
            return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
        }
        return $response;
    }
    private function getCommonFields()
    {
        return ['id' => 'I_U_S.id as `id`', 'stb_type' => 'I_U_S.stb_type as `stb_type`', 'require_image_version' => 'I_U_S.require_image_version as `require_image_version`', 'require_image_date' => 'I_U_S.require_image_date as `require_image_date`', 'update_type' => 'I_U_S.update_type as `update_type`', 'prefix' => 'I_U_S.prefix as `prefix`', 'image_description_contains' => 'I_U_S.image_description_contains as `image_description_contains`', 'image_version_contains' => 'I_U_S.image_version_contains as `image_version_contains`', 'hardware_version_contains' => 'I_U_S.hardware_version_contains as `hardware_version_contains`', 'enable' => 'I_U_S.enable as `enable`', 'stb_group_id' => 'S_G.id as `stb_group_id`', 'stb_group_name' => 'S_G.name as `stb_group_name`'];
    }
    public function remove_common_item()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id'])) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'deleteTableRow';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->deleteCommon(['id' => $this->postData['id']]);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data['msg'] = $this->setLocalization('Deleted');
        }
        $response = $this->generateAjaxResponse($data);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function toggle_common_item_status()
    {
        if (!$this->isAjax || $this->method != 'POST' || empty($this->postData['id']) || !\array_key_exists('enable', $this->postData)) {
            $this->app->abort(404, 'Page not found');
        }
        $data = [];
        $data['action'] = 'updateTableData';
        $data['id'] = $this->postData['id'];
        $error = $this->setLocalization('Failed');
        $result = $this->db->updateCommon(['enable' => (int) (!(bool) $this->postData['enable'])], $this->postData['id']);
        if (\is_numeric($result)) {
            $error = '';
            if ($result === 0) {
                $data['nothing_to_do'] = true;
            }
            $data = \array_merge_recursive($data, $this->common_list_json(true));
            $data['msg'] = $this->setLocalization('Changed');
            $data['action'] = 'updateTableRow';
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function upload_theme_img()
    {
        $id = false;
        $size = false;
        if ($this->method == 'POST' && !empty($this->postData['id'])) {
            $id = $this->postData['id'];
        }
        if ($this->method == 'POST' && !empty($this->postData['size'])) {
            $size = $this->postData['size'];
        }
        if (!$this->isAjax || $id === false || $size === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = [];
        $error = $this->setLocalization('Failed');
        if (!empty($_FILES)) {
            list($fKey, $tmp) = \each($_FILES);
            if (\is_uploaded_file($tmp['tmp_name']) && \preg_match('/jpeg|jpg|png/', $tmp['type'])) {
                $uploaded = $this->request->files->get($fKey)->getPathname();
                try {
                    $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
                    if ($id && \array_key_exists($id, $themes)) {
                        $theme = \array_replace($this->theme_preset, $themes[$id]);
                        $launcher_theme = new \Ministra\Admin\Lib\Theme($theme['alias']);
                        if (\is_numeric($size)) {
                            $launcher_theme->saveBackgroundImage($uploaded, null, $size);
                        } elseif ($size == 'logo') {
                            $position = \array_key_exists('logoAlign', $this->postData) && !empty($this->postData['logoAlign']) ? $this->postData['logoAlign'] : null;
                            $launcher_theme->saveLogo($uploaded, $position);
                        }
                        \reset($theme['bg']);
                        while (list($res, $path) = \each($theme['bg'])) {
                            $theme['original_bg'][$res] = $launcher_theme->getOriginalBackgroundImageUrl(null, $res);
                            $theme['bg'][$res] = $launcher_theme->getCustomBackgroundImageUrl(null, $res);
                            if (empty($theme['bg'][$res])) {
                                $theme['bg'][$res] = $theme['original_bg'][$res];
                            }
                            $theme['logo'][$res] = $launcher_theme->getCustomLogoImageUrl(null, $res);
                        }
                        $theme['logo']['align'] = $launcher_theme->getThemeVar('logoAlign', 'left');
                        $data['theme'] = $theme;
                        $error = '';
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
    public function themes_reset_to_default()
    {
        $id = false;
        if ($this->method == 'POST' && !empty($this->postData['id'])) {
            $id = $this->postData['id'];
        }
        if (!$this->isAjax || $id === false) {
            $this->app->abort(404, $this->setLocalization('Page not found'));
        }
        $data = ['data' => [], 'action' => 'updateTableRow', 'RowOrder' => $id];
        $error = $this->setLocalization('Do not completed');
        try {
            $themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
            if ($id && \array_key_exists($id, $themes)) {
                $launcher_transparent_preview = $this->workURL . '/img/launcherpreview/launcher@3x.png';
                $theme = \array_replace($this->theme_preset, $themes[$id]);
                $launcher_theme = new \Ministra\Admin\Lib\Theme($theme['alias']);
                $launcher_theme->reset();
                $theme['logo']['align'] = $launcher_theme->getThemeVar('logoAlign', 'left');
                $theme['preview'] = $launcher_transparent_preview;
                \reset($theme['bg']);
                while (list($res, $path) = \each($theme['bg'])) {
                    $theme['original_bg'][$res] = $launcher_theme->getOriginalBackgroundImageUrl(null, $res);
                    $theme['logo'][$res] = $launcher_theme->getCustomLogoImageUrl(null, $res);
                }
                $theme['type_name'] = \ucwords(\str_replace('_', ' ', $theme['type']));
                $theme['RowOrder'] = $theme['alias'];
                $data['data'][0] = $theme;
                $data['RowOrder'] = $theme['alias'];
                $error = '';
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $response = $this->generateAjaxResponse($data, $error);
        return new \Symfony\Component\HttpFoundation\Response(\json_encode($response), empty($error) ? 200 : 500, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
