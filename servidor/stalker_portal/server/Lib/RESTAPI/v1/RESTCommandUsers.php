<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class RESTCommandUsers extends \Ministra\Lib\RESTAPI\v1\RESTCommandAccounts
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $accounts = parent::get($request);
        if (!empty($accounts)) {
            return $accounts[0];
        }
        return $accounts;
    }
    protected function getUsersIdsFromIdentifiers($identifiers)
    {
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12 && \strpos($identifiers[0], ':')) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::x80f8373c3ed2b0da8f43c0ad28eabc98($identifiers);
    }
}
