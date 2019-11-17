<?php

namespace Ministra\Lib\Course;

class ProviderFactory
{
    const PROVIDER_NAMESPACE = 'Ministra\\Lib\\Course\\';
    public static function build($name, array $options)
    {
        $className = self::PROVIDER_NAMESPACE . \ucfirst($name) . 'Provider';
        $codes = \array_shift($options);
        $db = \array_shift($options);
        return new $className($codes, $db, $options);
    }
}
