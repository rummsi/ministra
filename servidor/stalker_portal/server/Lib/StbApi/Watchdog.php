<?php

namespace Ministra\Lib\StbApi;

interface Watchdog
{
    public function getEvents();
    public function confirmEvent();
}
