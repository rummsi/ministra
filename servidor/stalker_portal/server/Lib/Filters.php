<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
class Filters
{
    const FILTERS_TABLE = 'filters';
    private static $instance = null;
    private $db;
    private $filters;
    private $reseller = -1;
    private $fromTable;
    private $field = null;
    private $watchdog = 0;
    private $data = array();
    private $compare_cond = array('>', '<', '>=', '<=', '=', '<>');
    private $like_cond = array('in', '^=', '*=', '$=');
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->getDBFilters();
        $this->watchdog = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
    }
    private function getDBFilters()
    {
        $this->filters = [];
        if (\class_exists('Ministra\\Lib\\S642b6461e59cef199375bfb377c17a39\\L18e6d54d6202a6e70c8e428830aa4c89') && $this->db instanceof \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89 && $this->db) {
            foreach ($this->db->from(self::FILTERS_TABLE)->get()->all() as $row) {
                $filter_text_id = $row['method'];
                $filter_text_id = \preg_replace('/([a-z]+)([A-Z]+)/', '$1_$2', $filter_text_id);
                $filter_text_id = \str_replace('get_Users_By_', '', $filter_text_id);
                $row['text_id'] = \strtolower($filter_text_id);
                if (!empty($row['values_set']) && \method_exists($this, $row['values_set'])) {
                    $row['values_set'] = \call_user_func([$this, $row['values_set']]);
                } else {
                    $row['values_set'] = false;
                }
                $this->filters[$row['id']] = $row;
            }
            $this->cleanDataSet();
        }
    }
    private function cleanDataSet()
    {
        $this->db->reset();
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function custom_array_intersect()
    {
        $args = \func_get_args();
        if (\count($args) > 0 && \is_array($args[0])) {
            $result = $args[0];
            for ($i = 0; $i < \count($args) - 1; ++$i) {
                if (\is_array($args[$i + 1])) {
                    $result = self::custom_array_intersect_simple($result, $args[$i + 1]);
                }
            }
            return $result;
        }
        return false;
    }
    private static function custom_array_intersect_simple($a = array(), $b = array())
    {
        $result = [];
        $length_a = \count($a);
        $length_b = \count($b);
        \sort($a);
        \sort($b);
        for ($i = 0, $j = 0; $i < $length_a && $j < $length_b; null) {
            if ($a[$i] < $b[$j] && ++$i) {
                continue;
            }
            if ($a[$i] > $b[$j] && ++$j) {
                continue;
            }
            $result[] = $a[$i];
            if (isset($a[$next = $i + 1]) && $a[$next] != $a[$i]) {
                ++$j;
            }
            ++$i;
        }
        return $result;
    }
    public function initData($table_name, $field_name = null)
    {
        $this->fromTable = $table_name;
        $this->field = $field_name;
        $this->getAllUsers();
    }
    private function getAllUsers()
    {
        $this->fromTable();
        if (!empty($this->reseller)) {
            $this->db->where(['reseller_id' => $this->reseller]);
        }
        $this->data = $this->db->get()->all($this->field);
        $this->cleanDataSet();
    }
    public function setResellerID($id = 0)
    {
        $this->reseller = $id;
        $this->getDBFilters();
    }
    public function getData()
    {
        if ($this->reseller == -1) {
            return ['error' => 'Data is empty, because  not set reseller\'s id. If ' . 'system of resellers is not used set the reseller\'s id at "0" ' . 'or use method "setResellerID()" without parameters'];
        }
        return $this->data;
    }
    public function getFilters($filter_id = array())
    {
        $return_filter = [];
        if (\defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50400) {
            $dbt = \debug_backtrace(0, 2);
        } else {
            $dbt = \debug_backtrace(false);
        }
        $caller = isset($dbt[1]['function']) ? $dbt[1]['function'] : null;
        if (!empty($filter_id)) {
            if (\is_array($filter_id)) {
                foreach ($filter_id as $filter) {
                    $return_filter[] = $this->getFilters($filter);
                }
            } else {
                $field = \is_numeric($filter_id) ? 'id' : (\strpos($filter_id, 'getUsersBy') !== false ? 'method' : 'text_id');
                foreach ($this->filters as $item) {
                    if (\array_key_exists($field, $item) && $item[$field] == $filter_id) {
                        return $item;
                    }
                }
            }
        } elseif ($caller !== __FUNCTION__) {
            return $this->filters;
        }
        return \array_filter($return_filter);
    }
    public function setFilters($filter, $cond = null, $cond_value = null)
    {
        if (\is_array($filter)) {
            foreach ($filter as $row) {
                \call_user_func_array([$this, 'setFilters'], $row);
            }
        } elseif (!\is_null($cond) && !\is_null($cond_value)) {
            if (\is_numeric($filter) && \array_key_exists($filter, $this->filters) && \method_exists($this, $this->filters[$filter]['method'])) {
                $this->{$this->filters[$filter]['method']}($cond, $cond_value);
            } else {
                if (\is_string($filter)) {
                    if (\method_exists($this, $filter)) {
                        $this->{$filter}($cond, $cond_value);
                    } else {
                        $filter_unserialize = @\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($filter);
                        if ($filter_unserialize === false) {
                            throw new \Ministra\Lib\FiltersException('Failed unserialize filter: ' . \json_encode($filter));
                        }
                        $this->setFilters($filter_unserialize);
                    }
                }
            }
        }
    }
    private function getFilterIdByMethodName($method_name = '')
    {
        if (!empty($method_name)) {
            foreach ($this->filters as $row) {
                if (\in_array($method_name, $row)) {
                    return (int) $row['id'];
                }
            }
        }
        return false;
    }
    private function getUsersByStatus($cond, $cond_value)
    {
        $this->setNumericFilter('status', $cond, $cond_value);
    }
    private function setNumericFilter($field, $cond, $cond_value)
    {
        if (\in_array($cond, $this->like_cond)) {
            $cond_value = $this->setLikeCondVal($cond, $cond_value);
            if (\strtolower(\trim($cond)) == 'in') {
                $this->db->in($field, $cond_value);
            } else {
                $this->db->like([$field => $cond_value]);
            }
        } elseif (\in_array($cond, $this->compare_cond)) {
            $this->db->where(["{$field} {$cond} " => $cond_value]);
        }
        $this->applyFilter();
    }
    private function setLikeCondVal($cond, $cond_val)
    {
        switch ($cond) {
            case 'in':
                return !\is_array($cond_val) ? \explode('|', $cond_val) : $cond_val;
                break;
            case '^=':
                return "{$cond_val}%";
                break;
            case '*=':
                return "%{$cond_val}%";
                break;
            case '$=':
                return "%{$cond_val}";
                break;
        }
        return '';
    }
    private function applyFilter()
    {
        $this->fromTable();
        $this->data = self::custom_array_intersect_simple($this->data, $this->db->get()->all($this->field));
        $this->cleanDataSet();
    }
    private function fromTable()
    {
        if (empty($this->field)) {
            $this->db->select("{$this->fromTable}.*");
        } else {
            $this->db->select("{$this->fromTable}.{$this->field}");
        }
        $this->db->from($this->fromTable);
    }
    private function getUsersStatusSet()
    {
        return [['value' => 2, 'title' => 'off'], ['value' => 1, 'title' => 'on']];
    }
    private function getUsersByState($cond, $cond_value)
    {
        $this->setTimeStampFilter('keep_alive', $cond_value ? '<' : '>', \time() - $this->watchdog);
    }
    private function setTimeStampFilter($field, $cond, $cond_value)
    {
        $this->db->where(["unix_timestamp({$field}) {$cond}" => \is_numeric($cond_value) ? $cond_value : \strtotime($cond_value)]);
        $this->applyFilter();
    }
    private function getUsersStateSet()
    {
        return [['value' => 2, 'title' => 'offline'], ['value' => 1, 'title' => 'online']];
    }
    private function getUsersByPlayingType($cond, $cond_value)
    {
        $this->setNumericFilter('now_playing_type', $cond, $cond_value);
    }
    private function getUsersPlayingTypeSet()
    {
        return [['value' => 1, 'title' => 'TV'], ['value' => 2, 'title' => 'Video'], ['value' => 3, 'title' => 'Karaoke'], ['value' => 4, 'title' => 'Audio'], ['value' => 5, 'title' => 'Radio'], ['value' => 9, 'title' => 'Advert'], ['value' => 10, 'title' => 'Media browser'], ['value' => 11, 'title' => 'Tv archive'], ['value' => 12, 'title' => 'Records'], ['value' => 14, 'title' => 'TimeShift']];
    }
    private function getUsersByCreateDate($cond, $cond_value)
    {
        $this->setDateTimeFilter('created', $cond, $cond_value);
    }
    private function setDateTimeFilter($field, $cond, $cond_value)
    {
        if (\in_array($cond, $this->compare_cond)) {
            try {
                $date = @\date_create($cond_value);
                if ($date->format('H:i:s') == '00:00:00' && ($cond == '<=' || $cond == '<')) {
                    $date->modify('tomorrow -1 second');
                }
                $this->db->where(["{$field} {$cond} " => @\date_format($date, 'Y-m-d H:i:s')]);
                $this->applyFilter();
            } catch (\Ministra\Lib\FiltersException $e) {
                throw new \Ministra\Lib\FiltersException('Failed date params: ' . $e->getMessage());
            }
        }
    }
    private function getUsersByCountry($cond, $cond_value)
    {
        $this->setStringFilter('country', $cond, $cond_value);
    }
    private function setStringFilter($field, $cond, $cond_value)
    {
        if (\in_array($cond, $this->compare_cond) || \in_array($cond, $this->like_cond)) {
            if (\in_array($cond, $this->like_cond)) {
                $cond_value = $this->setLikeCondVal($cond, $cond_value);
                if (\strtolower(\trim($cond)) == 'in') {
                    $this->db->in($field, $cond_value);
                } else {
                    $this->db->like([$field => $cond_value]);
                }
            } else {
                $this->db->where(["{$field} {$cond} " => $cond_value]);
            }
            $this->applyFilter();
        }
    }
    private function getUsersCountrySet()
    {
        $field_name = 'name_en';
        if (!empty($_COOKIE) && !empty($_COOKIE['language']) && \substr($_COOKIE['language'], 0, 2) == 'ru') {
            $field_name = 'name';
        }
        $this->cleanDataSet();
        $this->db->select(['`iso2` as `value`', "`{$field_name}` as `title`"])->from('countries')->orderby($field_name);
        return $this->db->get()->all();
    }
    private function getUsersByLastStart($cond, $cond_value)
    {
        $this->setTimeStampFilter('last_start', $cond, $cond_value);
    }
    private function getUsersByLastActivity($cond, $cond_value)
    {
        $this->setTimeStampFilter('last_active', $cond, $cond_value);
    }
    private function getUsersByGroup($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->join('stb_in_group as S_I_G', 'users.id', 'S_I_G.uid', 'LEFT');
        if (\in_array($cond, $this->like_cond)) {
            $cond_value = $this->setLikeCondVal($cond, $cond_value);
            if (\strtolower(\trim($cond)) == 'in') {
                $this->db->in('S_I_G.stb_group_id', $cond_value);
            } else {
                $this->db->like(['S_I_G.stb_group_id' => $cond_value]);
            }
        } elseif (\in_array($cond, $this->compare_cond)) {
            $this->db->where(['S_I_G.stb_group_id' => $cond_value]);
        }
        $this->applyFilter();
    }
    private function getUsersGroupSet()
    {
        $this->cleanDataSet();
        $this->db->select(['`id` as `value`', '`name` as `title`'])->from('stb_groups');
        if (!empty($this->reseller)) {
            $this->db->where(['reseller_id' => $this->reseller]);
        }
        return $this->db->get()->all();
    }
    private function getUsersByInterfaceLanguage($cond, $cond_value)
    {
        $this->setStringFilter('locale', '=', $cond_value);
    }
    private function getUsersInterfaceLanguageSet()
    {
        $this->cleanDataSet();
        $this->db->select('locale')->from('users');
        if (!empty($this->reseller)) {
            $this->db->where(['reseller_id' => $this->reseller]);
        }
        $data = $this->db->groupby('locale')->get()->all('locale');
        $data = \array_intersect_key(\array_flip(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales')), \array_flip($data));
        $return_data = [];
        foreach ($data as $key => $value) {
            $return_data[] = ['value' => $key, 'title' => $value];
        }
        return $return_data;
    }
    private function getUsersByWatchingTV($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->where(['now_playing_type' => 1]);
        $this->setStringFilter('now_playing_content', $cond, $cond_value);
    }
    private function getUsersByWatchingMovie($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->where(['now_playing_type' => 2]);
        $this->setStringFilter('now_playing_content', $cond, $cond_value);
    }
    private function getUsersByUsingStreamServer($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->where(['now_playing_streamer_id' => $cond_value]);
        $this->setTimeStampFilter('keep_alive', '>', $this->watchdog);
    }
    private function getStreamServerSet()
    {
        $this->cleanDataSet();
        $this->db->select(['`id` as `value`', '`name` as `title`'])->from('streaming_servers')->where(['status' => 1]);
        return $this->db->get()->all();
    }
    private function getUsersBySTBModel($cond, $cond_value)
    {
        $this->setStringFilter('stb_type', '=', $cond_value);
    }
    private function getUserSTBModelSet()
    {
        $this->cleanDataSet();
        $this->db->select(['`stb_type` as `value`', '`stb_type` as `title`'])->from('users')->where(['stb_type <>' => '', 'stb_type IS NOT ' => null])->groupby('stb_type');
        if (!empty($this->reseller)) {
            $this->db->where(['reseller_id' => $this->reseller]);
        }
        return $this->db->get()->all();
    }
    private function getUsersBySTBFirmwareVersion($cond, $cond_value)
    {
        $this->setStringFilter('version', '*=', $cond_value);
    }
    private function getUsersByConnectedTariffPlan($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->join('tariff_plan', 'users.tariff_plan_id', 'tariff_plan.id or (users.tariff_plan_id = 0 and tariff_plan.user_default)', 'LEFT');
        if (\in_array($cond, $this->like_cond)) {
            $cond_value = $this->setLikeCondVal($cond, $cond_value);
            if (\strtolower(\trim($cond)) == 'in') {
                $this->db->in('tariff_plan_id', $cond_value);
            } else {
                $this->db->like(['tariff_plan_id' => $cond_value]);
            }
        } elseif (\in_array($cond, $this->compare_cond)) {
            $this->db->where(['tariff_plan_id' => $cond_value, "IF(tariff_plan.id = {$cond_value} and  users.tariff_plan_id = 0, 1, 0) and 1=" => 1], 'OR ');
        }
        $this->applyFilter();
    }
    private function getUserTariffPlanSet()
    {
        $this->cleanDataSet();
        $this->db->select(['`id` as `value`', '`name` as `title`'])->from('tariff_plan');
        return $this->db->get()->all();
    }
    private function getUsersByAccessibleServicePackages($cond, $cond_value)
    {
        $this->cleanDataSet();
        $this->db->from('package_in_plan');
        if (\in_array($cond, $this->like_cond)) {
            $cond_value = $this->setLikeCondVal($cond, $cond_value);
            if (\strtolower(\trim($cond)) == 'in') {
                $this->db->in('package_id', $cond_value);
            } else {
                $this->db->like(['package_id' => $cond_value]);
            }
        } else {
            $this->db->where(['package_id' => $cond_value]);
        }
        $tariff_with_packages = $this->db->get()->all();
        $tariff_with_packages_optional = \array_filter($tariff_with_packages, function ($row) {
            return (int) $row['optional'] == 1;
        });
        $tariff_with_packages = \array_filter($tariff_with_packages, function ($row) {
            return (int) $row['optional'] == 0;
        });
        $tariff_ids = \array_map(function ($row) {
            return (int) $row['plan_id'];
        }, $tariff_with_packages);
        $packages_ids = \array_unique(\array_map(function ($row) {
            return (int) $row['package_id'];
        }, $tariff_with_packages_optional));
        $this->cleanDataSet();
        $user_package_subscription = $this->db->from('user_package_subscription')->in('package_id', $packages_ids)->get()->all('user_id');
        $this->cleanDataSet();
        if (!empty($user_package_subscription)) {
            $this->db->where(["id in('" . \implode("', '", $user_package_subscription) . "') and 1" => 1, "tariff_plan_id in('" . \implode("', '", $tariff_ids) . "') and 1" => 1], 'OR ');
            $this->applyFilter();
        } else {
            $this->setStringFilter('tariff_plan_id', 'in', $tariff_ids);
        }
    }
    private function getUserAccessibleServicePackagesSet()
    {
        $this->cleanDataSet();
        $this->db->select(['`id` as `value`', '`name` as `title`'])->from('services_package');
        return $this->db->get()->all();
    }
}
