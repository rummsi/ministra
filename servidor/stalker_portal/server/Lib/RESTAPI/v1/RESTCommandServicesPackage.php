<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\Tariff;
class RESTCommandServicesPackage extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0])) {
            $package_id = (int) $identifiers[0];
        } else {
            $package_id = null;
        }
        $result = \Ministra\Lib\Tariff::getDetailedPackageInfo($package_id);
        return $result;
    }
}
