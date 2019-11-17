<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class ImageAutoUpdate
{
    private static $storage_initialized = false;
    private static $allowed_fields = array('enable', 'require_image_version', 'require_image_date', 'image_version_contains', 'image_description_contains', 'hardware_version_contains', 'update_type', 'stb_type', 'prefix');
    private $id;
    private $settings;
    public function __construct($id)
    {
        self::checkSettingsStorage();
        $this->id = $id;
        $this->settings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('image_update_settings')->where(['id' => $id])->get()->first();
        if (empty($this->settings)) {
            throw new \Exception('Setting not found');
        }
    }
    private static function checkSettingsStorage()
    {
        if (self::$storage_initialized) {
            return false;
        }
        self::$storage_initialized = true;
        $settings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('image_update_settings')->get()->first();
        if (!empty($settings)) {
            return true;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('image_update_settings', ['enable' => 0])->insert_id();
    }
    public static function getById($id)
    {
        return new self($id);
    }
    public static function getAll()
    {
        self::checkSettingsStorage();
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('image_update_settings')->orderby('id')->get()->all();
    }
    public static function getSettingByStbType($stb_type, $user_id = 0)
    {
        $user_groups = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('stb_in_group')->where(['uid' => $user_id, 'stb_group_id!=' => 0])->get()->all('stb_group_id');
        $not_in_groups = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('image_update_settings')->where(['stb_type' => $stb_type, 'enable' => 1, 'stb_group_id' => 0])->get()->all();
        $in_groups = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('image_update_settings')->where(['stb_type' => $stb_type, 'enable' => 1])->in('stb_group_id', $user_groups)->get()->all();
        return \array_merge($not_in_groups, $in_groups);
    }
    public static function create($settings)
    {
        $allowed_fields = \array_fill_keys(self::$allowed_fields, true);
        $settings = \array_intersect_key($settings, $allowed_fields);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('image_update_settings', $settings);
    }
    public function toggle()
    {
        if ($this->isEnabled()) {
            return $this->disable();
        }
        return $this->enable();
    }
    public function isEnabled()
    {
        return (bool) $this->settings['enable'];
    }
    public function disable()
    {
        if (!$this->isEnabled()) {
            return true;
        }
        return $this->setSettings(['enable' => 0]);
    }
    public function setSettings($settings)
    {
        $allowed_fields = \array_fill_keys(self::$allowed_fields, true);
        $settings = \array_intersect_key($settings, $allowed_fields);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('image_update_settings', $settings, ['id' => $this->id]);
    }
    public function enable()
    {
        if ($this->isEnabled()) {
            return true;
        }
        return $this->setSettings(['enable' => 1]);
    }
    public function delete()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('image_update_settings', ['id' => $this->id]);
    }
}
