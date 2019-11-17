<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class SMACCode
{
    const STATUS_NOT_ACTIVATED = 'Not Activated';
    const STATUS_ACTIVATED = 'Activated';
    const STATUS_BLOCKED = 'Blocked';
    const STATUS_MANUALLY_ENTERED = 'Manually entered';
    const STATUS_RESERVED = 'Reserved';
    public static $countImported;
    private $licenseKey = '';
    private $data;
    private $status;
    public function __construct($code)
    {
        $data = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('smac_codes')->where(['code' => $code])->get()->first();
        if (empty($data)) {
            throw new \Ministra\Lib\SMACCodeException('Activation code not found');
        }
        $this->licenseKey = $code;
        $this->data = $data;
        $this->status = $data['status'];
    }
    public static function importFile($filename, $content)
    {
        \preg_match('/request_(\\d+)/', $filename, $match);
        $request_id = isset($match[1]) ? $match[1] : 0;
        $lines = \array_map('str_getcsv', \str_getcsv($content, "\n"));
        $codes = [];
        $errors = [];
        foreach ($lines as $line) {
            if (isset($line[0])) {
                $line[0] = \trim($line[0]);
                if (!\preg_match("/^[0-9][0-9]\\w{10}\$/", $line[0])) {
                    $errors[] = $line[0];
                }
                $codes[] = \trim($line[0]);
            }
        }
        if (!empty($errors)) {
            $exception = new \Ministra\Lib\SMACLicenseInvalidFormatException('Wrong format for next license keys in file: ' . \implode(', ', $errors));
            $exception->setLicenses($errors);
            throw $exception;
        }
        if (empty($codes)) {
            throw new \Ministra\Lib\SMACCodeException('Empty import file.');
        }
        $existed_codes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('code')->from('smac_codes')->in('code', $codes)->get()->all('code');
        $new_codes = \array_diff($codes, $existed_codes);
        $new_codes = \array_unique($new_codes);
        if (empty($new_codes)) {
            throw new \Ministra\Lib\SMACCodeException('Nothing to import.');
        }
        $data = [];
        foreach ($new_codes as $new_code) {
            $data[] = ['code' => $new_code, 'request' => $request_id ? 'request_' . $request_id : '', 'added' => 'NOW()'];
        }
        static::$countImported = \count($data);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('smac_codes', $data)->result();
    }
    public function getLicenseKey()
    {
        return $this->licenseKey;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status, $user_id = null)
    {
        $this->status = $status;
        $data = ['status' => $status];
        if (null !== $user_id) {
            $data['user_id'] = $user_id;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('smac_codes', $data, ['code' => $this->licenseKey])->result();
    }
    public function setUser($user_id, $device = null)
    {
        $data = ['user_id' => $user_id, 'status' => self::STATUS_ACTIVATED];
        if (null !== $device) {
            $data['device'] = $device;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('smac_codes', $data, ['code' => $this->licenseKey])->result();
    }
    public function setDevice($device_name)
    {
        if (!$this->getParam('device') || $this->getParam('device') !== $device_name) {
            $this->data['device'] = $device_name;
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('smac_codes', ['device' => $device_name], ['code' => $this->licenseKey])->result();
        }
    }
    public function getParam($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
