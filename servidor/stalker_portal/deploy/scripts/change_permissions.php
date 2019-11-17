<?php

$config = \parse_ini_file(__DIR__ . '/../../server/config.ini');
$customConfig = \is_file(__DIR__ . '/../../server/custom.ini') ? \parse_ini_file(__DIR__ . '/../../server/custom.ini') : [];
$config = \array_merge($config, $customConfig);
$dir = \realpath(__DIR__ . '/../../');
$launcherPath = \str_replace('//', '/', $dir . '/../' . (isset($argv[1]) ? $argv[1] : $config['launcher_apps_path']));
$webPlayerPath = \str_replace('//', '/', $dir . '/../' . (isset($argv[2]) ? $argv[2] : $config['web_player_server_path']));
$appsPath = \str_replace('//', '/', $dir . '/../' . (isset($argv[3]) ? $argv[3] : $config['apps_path']));
$data = @\json_decode(@\file_get_contents('http://localhost/stalker_portal/deploy/scripts/permissions.php'), \true);
$data = $data ?: ['user' => \getenv('PROJECT_USER') ?: 'www-data', 'group' => \getenv('PROJECT_GROUP') ?: 'www-data'];
\exec("mkdir -p {$launcherPath} {$webPlayerPath}");
\exec("touch /var/log/.npmrc");
\exec("chown -R {$data['user']}:{$data['group']} {$dir} /var/log/.npmrc {$launcherPath} {$webPlayerPath} {$appsPath}");
\exec("chmod +x {$dir}/deploy/clear_key_util/ClearKeysTool {$dir}/storage/install.sh");
\exec("chmod +x {$dir}/deploy/clear_key_util");
\exec("find {$dir} -type f -iname '.htaccess' -exec chmod 0644 {} \\;");
