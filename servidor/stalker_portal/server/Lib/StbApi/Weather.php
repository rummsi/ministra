<?php

namespace Ministra\Lib\StbApi;

interface Weather
{
    public function getCurrent();
    public function getForecast();
}
