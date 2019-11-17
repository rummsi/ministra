<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Reseller;
class RESTCommandStb extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    private $manager;
    private $allowed_fields;
    public function __construct()
    {
        $this->manager = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
        $allowed_to_update_fields = ['mac', 'ls', 'login', 'status', 'online', 'additional_services_on', 'ip', 'version', 'expire_billing_date', 'account_balance', 'last_active'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_to_update_fields[] = 'reseller_id';
        }
        $this->allowed_fields = \array_fill_keys($allowed_to_update_fields, true);
    }
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $stb_list = $this->manager->getByUids($request->getConvertedIdentifiers());
        return $this->formatList($stb_list);
    }
    private function formatList($list)
    {
        $allowed_fields = $this->allowed_fields;
        $enable_internal_billing = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', false);
        $list = \array_map(function ($item) use($allowed_fields, $enable_internal_billing) {
            $item = \array_intersect_key($item, $allowed_fields);
            $item['status'] = (int) (!$item['status']);
            if ($enable_internal_billing) {
                $item['end_date'] = $item['expire_billing_date'];
            }
            unset($item['expire_billing_date']);
            return $item;
        }, $list);
        return $list;
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $put = $request->getPut();
        if (empty($put)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        $allowed_to_update_fields = ['status', 'additional_services_on', 'ls', 'reboot', 'end_date'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_to_update_fields[] = 'reseller_id';
        }
        $data = \array_intersect_key($put, \array_fill_keys($allowed_to_update_fields, true));
        if (\array_key_exists('status', $data)) {
            $data['status'] = (int) (!$data['status']);
        }
        if (isset($data['end_date'])) {
            $data['expire_billing_date'] = $data['end_date'];
            unset($data['end_date']);
        }
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Update data is empty');
        }
        if (\count($request->getIdentifiers()) == 0 && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_multiple_stb_update', false)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        }
        if (!empty($account['reseller_id'])) {
            $reseller = new \Ministra\Lib\Reseller($account['reseller_id']);
            if (empty($reseller->getDBData())) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller does not exist');
            }
        }
        $stb_list = $this->manager->updateByUids($request->getConvertedIdentifiers(), $data);
        if (empty($stb_list)) {
            return false;
        }
        return $this->formatList($stb_list);
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        if (\count($request->getIdentifiers()) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Identifier required');
        }
        $stb_list = $request->getConvertedIdentifiers();
        if (\count($stb_list) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('STB not found');
        }
        return $this->manager->fc7f5b0aa535040803958d608ade17ba($stb_list);
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $allowed_to_update_fields = ['mac', 'login', 'password', 'status', 'additional_services_on', 'ls', 'end_date', 'account_balance'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_to_update_fields[] = 'reseller_id';
        }
        $data = \array_intersect_key($data, \array_fill_keys($allowed_to_update_fields, true));
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        if (isset($data['end_date'])) {
            $data['expire_billing_date'] = $data['end_date'];
            unset($data['end_date']);
        }
        if (!empty($data['mac'])) {
            $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($data['mac']);
            if (!$mac) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Not valid mac address');
            }
            $data['mac'] = $mac;
        }
        if (empty($data['mac']) && (empty($data['login']) || empty($data['password']))) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Login and password required');
        }
        if (!empty($data['reseller_id'])) {
            $reseller = new \Ministra\Lib\Reseller($data['reseller_id']);
            if (empty($reseller->getDBData())) {
                throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Reseller does not exist');
            }
        }
        try {
            $uid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::create($data);
        } catch (\Exception $e) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException($e->getMessage());
        }
        $stb_list = $this->manager->getByUids([$uid]);
        $stb_list = $this->formatList($stb_list);
        if (\count($stb_list) == 1) {
            return $stb_list[0];
        }
        return $stb_list;
    }
}
