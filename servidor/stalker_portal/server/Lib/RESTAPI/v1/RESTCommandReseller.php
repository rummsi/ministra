<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Reseller;
class RESTCommandReseller extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function __construct()
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Resource denied by admin');
        }
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        $db = clone \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $db->from('reseller');
        if (!empty($identifiers)) {
            $identifiers = \array_unique($identifiers);
            $db->in('id', $identifiers);
        }
        $result = $db->get()->all();
        if (\count($identifiers) == 1 && \count($result) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if ($identifiers != null && \count($identifiers) > \count($result)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('One or more identifiers are incorrect');
        }
        return $result;
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $reseller = new \Ministra\Lib\Reseller();
        $reseller->setData($data);
        $check_data = \array_filter($reseller->getData());
        if (empty($check_data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        if (empty($check_data['name'])) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller name required');
        }
        if (!empty($reseller->getDBData())) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller with this name already exists');
        }
        return (bool) $reseller->updateDBData();
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (!empty($data['id'])) {
            unset($data['id']);
        }
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Data is empty');
        }
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        } elseif (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $identifiers = \array_values(\array_filter($identifiers, function ($row) {
            return \is_numeric($row);
        }));
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier is wrong, for this action use reseller\'s id');
        }
        $reseller = new \Ministra\Lib\Reseller();
        $reseller->setData(['id' => $identifiers[0]]);
        if (empty($reseller->getDBData())) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        $reseller->cleanData();
        $reseller->setData($data);
        if (\array_key_exists('name', $data)) {
            if (empty($data['name'])) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller name required');
            }
            $account = $reseller->getDBData();
            if (!empty($account) && $account['id'] != $identifiers[0]) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller with this name already exists');
            }
        }
        $reseller->setData(['id' => $identifiers[0]]);
        $reseller->updateData();
        $reseller->setData($data);
        $result = $reseller->updateDBData();
        if (\is_bool($result)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Action has been failed, database error');
        } elseif ($result === 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Nothing to update');
        }
        return (bool) $result;
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        }
        $identifiers = \array_values(\array_filter($identifiers, function ($row) {
            return \is_numeric($row);
        }));
        if (\count($identifiers) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier is wrong, for this action use reseller\'s id');
        } elseif (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $reseller = new \Ministra\Lib\Reseller();
        $reseller->setData(['id' => $identifiers[0]]);
        if (empty($reseller->getDBData())) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        return (bool) $reseller->deleteData();
    }
}
