<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Reseller;
use Ministra\Lib\User;
class RESTCommandAccounts extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        $users_ids = $this->getUsersIdsFromIdentifiers($identifiers);
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if ($identifiers != null && \count($identifiers) > \count($users_ids)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('One or more identifiers are incorrect');
        }
        $result = [];
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $result[] = $user->getAccountInfo();
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
    protected function getUsersIdsFromIdentifiers($identifiers)
    {
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12 && \strpos($identifiers[0], ':')) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::ba19fa195b9b610a69c936c130361be6($identifiers);
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $allowed_to_update_fields = ['login', 'password', 'full_name', 'phone', 'account_number', 'tariff_plan', 'tariff_expired_date', 'tariff_instead_expired', 'status', 'stb_mac', 'comment', 'end_date', 'account_balance'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_to_update_fields[] = 'reseller_id';
        }
        $account = \array_intersect_key($data, \array_fill_keys($allowed_to_update_fields, true));
        if (empty($account)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        if (!empty($account['stb_mac'])) {
            $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($account['stb_mac']);
            if (!$mac) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Not valid mac address');
            }
            $account['stb_mac'] = $mac;
        }
        if (empty($account['login'])) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Login required');
        }
        $user = \Ministra\Lib\User::getByLogin($account['login']);
        if (!empty($user)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Login already in use');
        }
        if (!empty($account['stb_mac'])) {
            $user = \Ministra\Lib\User::getByMac($account['stb_mac']);
            if (!empty($user)) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('MAC address already in use');
            }
        }
        if (!empty($account['reseller_id'])) {
            $reseller = new \Ministra\Lib\Reseller($account['reseller_id']);
            if (empty($reseller->getDBData())) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller does not exist');
            }
        }
        return (bool) \Ministra\Lib\User::createAccount($account);
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $allowed_to_update_fields = ['login', 'password', 'full_name', 'phone', 'account_number', 'tariff_plan', 'tariff_expired_date', 'tariff_instead_expired', 'status', 'stb_mac', 'comment', 'end_date', 'account_balance'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_to_update_fields[] = 'reseller_id';
        }
        $account = \array_intersect_key($data, \array_fill_keys($allowed_to_update_fields, true));
        if (empty($account)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        }
        $users_ids = $this->getUsersIdsFromIdentifiers($identifiers);
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        if (!empty($account['login'])) {
            $user = \Ministra\Lib\User::getByLogin($account['login']);
            if (!empty($user) && ($user->getId() != $users_ids[0] || \count($users_ids) > 1)) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Login already in use');
            }
        }
        if (!empty($account['reseller_id'])) {
            $reseller = new \Ministra\Lib\Reseller($account['reseller_id']);
            if (empty($reseller->getDBData())) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller does not exist');
            }
        }
        $result = true;
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $result = $user->updateAccount($account) && $result;
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        }
        $users_ids = $this->getUsersIdsFromIdentifiers($identifiers);
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $result = true;
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $result = $user->delete() && $result;
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
}
