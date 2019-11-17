<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\Tariff;
class RESTCommandTariffs extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0])) {
            $plan_id = (int) $identifiers[0];
        } else {
            $plan_id = null;
        }
        $result = \Ministra\Lib\Tariff::getDetailedPlanInfo($plan_id);
        return $result;
    }
}
