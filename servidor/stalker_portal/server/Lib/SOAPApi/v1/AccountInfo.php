<?php

namespace Ministra\Lib\SOAPApi\v1;

/**
 * AccountInfo complex type.
 *
 * @pw_element string $login Unique login
 * @pw_element string $full_name Full Name or description
 * @pw_element string $account_number
 * @pw_element string $tariff_plan
 * @pw_element string $stb_sn
 * @pw_element string $stb_mac
 * @pw_element string $stb_type
 * @pw_element int $status
 * @pw_element stringArray $subscribed
 * @pw_complex AccountInfo
 */
class AccountInfo
{
    public $login;
    // string
    public $full_name;
    // string
    public $account_number;
    // string
    public $tariff_plan;
    // string
    public $stb_sn;
    // string
    public $stb_mac;
    //string
    public $stb_type;
    //string
    public $status;
    // int
    public $subscribed;
    // array
}
