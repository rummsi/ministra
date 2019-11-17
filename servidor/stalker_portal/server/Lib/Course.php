<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Course\CourseGetter;
use Ministra\Lib\Course\ProviderFactory;
class Course implements \Ministra\Lib\StbApi\Course
{
    private $db;
    private $codes;
    private $providerName;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->providerName = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('course_provider');
        $providers = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('course_providers_for_update');
        if (\array_key_exists($this->providerName, $providers)) {
            $this->codes = $providers[$this->providerName];
        }
    }
    public function getData()
    {
        $provider = \Ministra\Lib\Course\ProviderFactory::build($this->providerName, [$this->codes, $this->db]);
        if ($provider instanceof \Ministra\Lib\Course\CourseGetter) {
            return $provider->getData();
        }
        throw new \Exception(\sprintf('Class "%s" must implement CourseGetter interface', \get_class($provider)));
    }
}
