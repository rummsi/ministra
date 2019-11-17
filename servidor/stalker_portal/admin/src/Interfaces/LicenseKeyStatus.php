<?php

namespace Ministra\Admin\Interfaces;

interface LicenseKeyStatus
{
    const NOT_ACTIVATED = 'Not Activated';
    const ACTIVATED = 'Activated';
    const BLOCKED = 'Blocked';
    const MANUALLY = 'Manually entered';
    const RESERVED = 'Reserved';
}
