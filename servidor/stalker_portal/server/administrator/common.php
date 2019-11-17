<?php

$_SERVER['TARGET'] = 'ADM';
require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
$locales = [];
$allowed_locales = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales');
foreach ($allowed_locales as $lang => $locale) {
    $locales[\substr($locale, 0, 2)] = $locale;
}
$accept_language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : \null;
if (!empty($_COOKIE['language']) && \array_key_exists($_COOKIE['language'], $locales)) {
    $locale = $locales[$_COOKIE['language']];
} elseif ($accept_language && \array_key_exists(\substr($accept_language, 0, 2), $locales)) {
    $locale = $locales[\substr($accept_language, 0, 2)];
} else {
    $locale = $locales[\key($locales)];
}
\setcookie('debug_key', '', \time() - 3600, '/');
\setlocale(\LC_MESSAGES, $locale);
\setlocale(\LC_TIME, $locale);
\putenv('LC_MESSAGES=' . $locale);
\bindtextdomain('stb', \PROJECT_PATH . '/locale');
\textdomain('stb');
\bind_textdomain_codeset('stb', 'UTF-8');
require __DIR__ . '/../Lib/funcs/functions.php';
