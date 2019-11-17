<?php

namespace Ministra\Lib\SOAPApi\v1;

/**
 * SearchCondition complex type.
 *
 * @pw_set minoccurs=0
 * @pw_element string $login
 * @pw_set minoccurs=0
 * @pw_element string $stb_mac
 * @pw_complex SearchCondition
 */
class SearchCondition
{
    public $login;
    // string
    public $stb_mac;
    //string
}
