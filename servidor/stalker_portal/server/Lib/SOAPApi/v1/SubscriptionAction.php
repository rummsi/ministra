<?php

namespace Ministra\Lib\SOAPApi\v1;

/**
 * Optional subscription complex type.
 *
 * @pw_set minoccurs=0
 * @pw_element stringArray $subscribe
 * @pw_set minoccurs=0
 * @pw_element stringArray $unsubscribe
 * @pw_complex SubscriptionAction
 */
class SubscriptionAction
{
    public $subscribe;
    //array
    public $unsubscribe = array();
    //array
}
