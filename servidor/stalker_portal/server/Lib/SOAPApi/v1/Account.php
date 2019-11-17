<?php

namespace Ministra\Lib\SOAPApi\v1;

/**
 * Account complex type.
 *
 * @pw_element string $login
 * @pw_set minoccurs=0
 * @pw_element string $password
 * @pw_set minoccurs=0
 * @pw_element string $full_name
 * @pw_set minoccurs=0
 * @pw_element string $account_number
 * @pw_set minoccurs=0
 * @pw_element string $tariff_plan
 * @pw_set minoccurs=0
 * @pw_element int $status
 * @pw_set minoccurs=0
 * @pw_element string $stb_mac
 * @pw_complex Account
 */
class Account
{
    public $login;
    // string
    public $password;
    // string
    public $full_name;
    // string
    public $account_number;
    // string
    public $tariff_plan;
    // string
    public $status;
    // int
    public $stb_mac;
    //string
}
