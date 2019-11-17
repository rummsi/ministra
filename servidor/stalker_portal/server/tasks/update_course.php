<?php

\error_reporting(\E_ALL);
require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Course\CourseUpdater;
use Ministra\Lib\Course\ProviderFactory;
$options = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('course_providers_options', []);
$providers = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('course_providers_for_update');
foreach ($providers as $name => $currencies) {
    $provider = \Ministra\Lib\Course\ProviderFactory::build($name, [$currencies, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance(), \array_key_exists($name, $options) ? $options[$name] : []]);
    if ($provider instanceof \Ministra\Lib\Course\CourseUpdater) {
        $rez = $provider->updateData();
    } else {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47(\sprintf('Class "%s" must implement CourseUpdater interface', \get_class($provider)));
    }
    echo \PHP_EOL;
}
