<?php

\ini_set('display_errors', 1);
\error_reporting(\E_ALL);
if (\file_exists(__DIR__ . '/config.prod.php')) {
    require_once __DIR__ . '/config.prod.php';
} else {
    require_once __DIR__ . '/config.php';
}
\defined('PROJECT_PATH') or \define('PROJECT_PATH', \dirname(__FILE__));
require_once __DIR__ . '/vendor/autoload.php';
