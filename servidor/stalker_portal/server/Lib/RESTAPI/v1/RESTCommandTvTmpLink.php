<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\Itv;
class RESTCommandTvTmpLink extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $ids = $request->getIdentifiers();
        if (empty($ids[0])) {
            throw new \ErrorException('Empty token');
        }
        return \Ministra\Lib\Itv::checkTemporaryLink($ids[0]);
    }
}
