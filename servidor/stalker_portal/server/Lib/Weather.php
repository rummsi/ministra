<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class Weather implements \Ministra\Lib\StbApi\Weather
{
    protected $provider;
    public function __construct()
    {
        $this->provider = $this->getProvider();
    }
    private function getProvider()
    {
        $class = __NAMESPACE__ . '\\' . \ucfirst(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('weather_provider', 'weatherco'));
        if (!\class_exists($class)) {
            throw new \Exception('Resource "' . $class . '" does not exist');
        }
        return new $class();
    }
    public function getCurrent()
    {
        return $this->provider->getCurrent();
    }
    public function getForecast()
    {
        return $this->provider->getForecast();
    }
    public function updateFullCurrent()
    {
        return $this->provider->updateFullCurrent();
    }
    public function updateFullForecast()
    {
        return $this->provider->updateFullForecast();
    }
    public function getCities($country, $search = '')
    {
        return $this->provider->getCities($country, $search);
    }
    public function getCityFieldName()
    {
        return $this->provider->getCityFieldName();
    }
}
