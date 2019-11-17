<?php

namespace Ministra\Lib\SOAPApi\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\User;
class SoapApiHandler
{
    /**
     * If there is an account with a specified login, it updates its, otherwise - create new account.
     *
     * @param \Ministra\Lib\SOAPApi\v1\Account $params
     *
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMacAddressInUse
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam
     * @throws \Ministra\Lib\SOAPApi\v1\SoapWrongMacFormat
     * @throws \Ministra\Lib\SOAPApi\v1\SoapServerError
     *
     * @return bool
     */
    public function CreateOrUpdateAccount($params)
    {
        $params = (array) $params;
        $this->checkLoginAndMac($params);
        $user = \Ministra\Lib\User::getByLogin($params['login']);
        if (empty($user)) {
            return (bool) $this->CreateAccount($params);
        }
        return (bool) $this->UpdateAccount($params);
    }
    /**
     * Create new account.
     *
     * @param \Ministra\Lib\SOAPApi\v1\Account $params
     *
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMacAddressInUse
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam
     * @throws \Ministra\Lib\SOAPApi\v1\SoapWrongMacFormat
     * @throws \Ministra\Lib\SOAPApi\v1\SoapServerError
     *
     * @return bool|int
     */
    public function CreateAccount($params)
    {
        $params = (array) $params;
        $this->checkLoginAndMac($params);
        $user_id = \Ministra\Lib\User::createAccount($params);
        if (!$user_id) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapServerError(__METHOD__, __FILE__ . ':' . __LINE__);
        }
        return (bool) $user_id;
    }
    /**
     * Update account by specified login param.
     *
     * @param \Ministra\Lib\SOAPApi\v1\Account $params
     *
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMacAddressInUse
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam
     * @throws \Ministra\Lib\SOAPApi\v1\SoapWrongMacFormat
     * @throws \Ministra\Lib\SOAPApi\v1\SoapServerError
     *
     * @return bool|int
     */
    public function UpdateAccount($params)
    {
        $params = (array) $params;
        $this->checkLoginAndMac($params);
        $user = \Ministra\Lib\User::getByLogin($params['login']);
        $result = $user->updateAccount($params);
        if (!$result) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapServerError(__METHOD__, __FILE__ . ':' . __LINE__);
        }
        return $result;
    }
    /**
     * Return first account, that match params.
     *
     * @param \Ministra\Lib\SOAPApi\v1\SearchCondition $params
     *
     * @throws SoapAccountNotFound
     *
     * @return AccountInfo
     */
    public function GetAccountInfo($params)
    {
        $user = $this->getUserByParams($params);
        return $user->getAccountInfo();
    }
    /**
     * Delete account by search conditions.
     *
     * @param \Ministra\Lib\SOAPApi\v1\SearchCondition $params
     *
     * @throws SoapAccountNotFound
     *
     * @return bool
     */
    public function DeleteAccount($params)
    {
        $user = $this->getUserByParams($params);
        return (bool) $user->delete();
    }
    /**
     * Update optional package subscription.
     *
     * @param \Ministra\Lib\SOAPApi\v1\SearchCondition $params
     * @param \Ministra\Lib\SOAPApi\v1\SubscriptionAction $subscription
     *
     * @throws SoapMissingRequiredParam
     * @throws \SoapFault
     * @throws SoapSubscriptionUpdateError
     *
     * @return bool
     */
    public function UpdateAccountOptionalSubscription($params, $subscription)
    {
        $params = (array) $params;
        $subscription = (array) $subscription;
        $user = $this->getUserByParams($params);
        if (!$this->subscriptionManageMode($subscription)) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam(__METHOD__, __FILE__ . ':' . __LINE__);
        }
        if (!isset($_REQUEST['type'])) {
            $_REQUEST['type'] = 'stb';
        }
        $subscriptionResult = $user->updateOptionalPackageSubscription($subscription);
        if (!$subscriptionResult) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapSubscriptionUpdateError('1', 'Subscription update error');
        }
        return true;
    }
    /**
     * Get user by params.
     *
     * @param array|null $params
     *
     * @throws \Ministra\Lib\SOAPApi\v1\SoapAccountNotFound
     *
     * @return bool|\Ministra\Lib\User
     */
    private function getUserByParams($params)
    {
        $params = (array) $params;
        if (!empty($params['stb_mac'])) {
            $user = \Ministra\Lib\User::getByMac($params['stb_mac']);
        } elseif (!empty($params['login'])) {
            $user = \Ministra\Lib\User::getByLogin($params['login']);
        }
        if (empty($user)) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapAccountNotFound(__METHOD__, __FILE__ . ':' . __LINE__);
        }
        return $user;
    }
    /**
     * Check user login and mac.
     *
     * @param array $params
     *
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMacAddressInUse
     * @throws \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam
     * @throws \Ministra\Lib\SOAPApi\v1\SoapWrongMacFormat
     */
    private function checkLoginAndMac($params)
    {
        if (empty($params['login'])) {
            throw new \Ministra\Lib\SOAPApi\v1\SoapMissingRequiredParam();
        }
        if (!empty($params['stb_mac'])) {
            $params['stb_mac'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($params['stb_mac']);
            if (empty($params['stb_mac'])) {
                throw new \Ministra\Lib\SOAPApi\v1\SoapWrongMacFormat(__METHOD__, __FILE__ . ':' . __FILE__);
            }
            $user = \Ministra\Lib\User::getByLogin($params['login']);
            if (empty($user) || $user->getMac() != $params['stb_mac']) {
                $stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::R2015a49c88e682d6b426a39593db218e($params['stb_mac']);
                if (!empty($stb)) {
                    throw new \Ministra\Lib\SOAPApi\v1\SoapMacAddressInUse(__METHOD__, __FILE__ . ':' . __FILE__);
                }
            }
        }
    }
    private function subscriptionManageMode($params)
    {
        return !empty($params['subscribe']) || !empty($params['unsubscribe']);
    }
}
