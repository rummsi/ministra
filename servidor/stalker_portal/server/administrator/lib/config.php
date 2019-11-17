<?php

\defined('PATH_SEPARATOR') or \define('PATH_SEPARATOR', \getenv('COMSPEC') ? ';' : ':');
\ini_set('include_path', \ini_get('include_path') . \PATH_SEPARATOR . \dirname(__FILE__));
