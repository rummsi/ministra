<?php

namespace Ministra\Lib;

interface OssWrapperInterface
{
    public function getUserInfo(\Ministra\Lib\User $user);
    public function registerSTB($mac, $serial_number, $model);
    public function getPackagePrice($ext_package_id, $package_id);
    public function subscribeToPackage($ext_package_id);
    public function unsubscribeFromPackage($ext_package_id);
}
