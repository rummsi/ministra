<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\User;
class RESTCommandAccountSubscription extends \Ministra\Lib\RESTAPI\v1\RESTCommand
{
    public function get(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12) {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        } else {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::ba19fa195b9b610a69c936c130361be6($identifiers);
        }
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $result = [];
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $info = $user->getAccountInfo();
            $result[] = ['mac' => $user->getMac(), 'subscribed' => $info['subscribed'], 'subscribed_id' => $info['subscribed_id']];
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
    public function create(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP POST data is empty');
        }
        $account = \array_intersect_key($data, ['subscribed' => true, 'subscribed_id' => true]);
        if (empty($account)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12) {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        } else {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::ba19fa195b9b610a69c936c130361be6($identifiers);
        }
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $result = true;
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $info = $user->getAccountInfo();
            $subscribe = empty($data['subscribed']) ? [] : \array_unique($data['subscribed']);
            $subscribe_id = empty($data['subscribed_id']) ? [] : $data['subscribed_id'];
            $unsubscribe = empty($data['subscribed']) ? [] : \array_diff($info['subscribed'], $data['subscribed']);
            $unsubscribe_id = empty($data['subscribed_id']) ? [] : \array_diff($info['subscribed_id'], $data['subscribed_id']);
            $subscribe = $user->updateOptionalPackageSubscription(['subscribe' => $subscribe, 'subscribe_ids' => $subscribe_id, 'unsubscribe' => $unsubscribe, 'unsubscribe_ids' => $unsubscribe_id]);
            $result = $result && $subscribe;
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
    public function update(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $data = $request->getData();
        if (empty($data)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('HTTP PUT data is empty');
        }
        $account = \array_intersect_key($data, ['subscribed' => true, 'subscribed_id' => true, 'unsubscribed' => true, 'unsubscribed_id' => true]);
        if (empty($account)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Insert data is empty');
        }
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12) {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        } else {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::ba19fa195b9b610a69c936c130361be6($identifiers);
        }
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $subscribed = empty($data['subscribed']) ? [] : $data['subscribed'];
        $subscribed_id = empty($data['subscribed_id']) ? [] : $data['subscribed_id'];
        $unsubscribed = empty($data['unsubscribed']) ? [] : $data['unsubscribed'];
        $unsubscribed_id = empty($data['unsubscribed_id']) ? [] : $data['unsubscribed_id'];
        $result = true;
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $subscribe = $user->updateOptionalPackageSubscription(['subscribe' => $subscribed, 'subscribe_ids' => $subscribed_id, 'unsubscribe' => $unsubscribed, 'unsubscribe_ids' => $unsubscribed_id]);
            $result = $result && $subscribe;
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
    public function delete(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $identifiers = $request->getIdentifiers();
        if (!empty($identifiers[0]) && \strlen($identifiers[0]) >= 12) {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($identifiers);
        } else {
            $users_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::ba19fa195b9b610a69c936c130361be6($identifiers);
        }
        if (\count($identifiers) == 1 && \count($users_ids) == 0) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Account not found');
        }
        if (\count($identifiers) > 1) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Only one identifier allowed');
        }
        $result = true;
        foreach ($users_ids as $user_id) {
            $user = \Ministra\Lib\User::getInstance($user_id);
            $info = $user->getAccountInfo();
            $subscribe = $user->updateOptionalPackageSubscription(['subscribe' => [], 'unsubscribe' => $info['subscribed']]);
            $result = $result && $subscribe;
            \Ministra\Lib\User::clear();
        }
        return $result;
    }
}
