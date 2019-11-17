<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Tariff
{
    public static function getDetailedPlanInfo($plan_id = null)
    {
        if (!empty($plan_id)) {
            $info = self::getPlanById($plan_id);
            if (!empty($info)) {
                $info['packages'] = \Ministra\Lib\Tariff::getPackagesForTariffPlan($info['id']);
            }
        } else {
            $info = self::getAllPlans();
            $info = \array_map(function ($plan) {
                $plan['packages'] = \Ministra\Lib\Tariff::getPackagesForTariffPlan($plan['id']);
                return $plan;
            }, $info);
        }
        return $info;
    }
    public static function getPlanById($plan_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $plan_id])->get()->first();
    }
    public static function getPackagesForTariffPlan($plan_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('services_package.*, package_in_plan.optional')->from('services_package')->join('package_in_plan', 'package_in_plan.package_id', 'services_package.id', 'INNER')->where(['plan_id' => $plan_id])->get()->all();
    }
    public static function getAllPlans()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->get()->all();
    }
    public static function getDetailedPackageInfo($package_id = null)
    {
        if (!empty($package_id)) {
            $info = self::getPackageById($package_id);
            if (!empty($info)) {
                $info['services'] = \Ministra\Lib\Tariff::getServicesForPackage($info['id']);
            }
        } else {
            $info = self::getAllPackages();
            $info = \array_map(function ($package) {
                $package['services'] = \Ministra\Lib\Tariff::getServicesForPackage($package['id']);
                return $package;
            }, $info);
        }
        return $info;
    }
    public static function getPackageById($package_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first();
    }
    public static function getServicesForPackage($package_id)
    {
        $package = self::getPackageById($package_id);
        if ($package['all_services'] == 1) {
            return 'all';
        }
        $service_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('service_in_package')->where(['package_id' => $package_id])->get()->all('service_id');
        $services = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        if ($package['type'] == 'tv') {
            $services = $services->select('id, name')->from('itv')->in('id', $service_ids)->orderby('name')->get()->all();
        } elseif ($package['type'] == 'radio') {
            $services = $services->select('id, name')->from('radio')->in('id', $service_ids)->orderby('name')->get()->all();
        } elseif ($package['type'] == 'video') {
            $services = $services->select('id, name')->from('video')->in('id', $service_ids)->orderby('name')->get()->all();
        } else {
            $services = \array_unique($service_ids);
        }
        return $services;
    }
    public static function getAllPackages()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->get()->all();
    }
}
