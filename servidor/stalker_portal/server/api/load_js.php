<?php

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
\defined('PROJECT_PATH') or \define('PROJECT_PATH', \realpath(\dirname(__FILE__) . '/../../server/'));
require_once \PROJECT_PATH . '/../storage/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
$zone_url = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('max_cdn_pull_zone_url', '');
$write_base_url = !empty($zone_url) ? "document.write(\"<base href='http://{$zone_url}/stalker_portal/c/' />\");" : '';
$ouput = <<<EOD

{$write_base_url}

this.loadRequiredFiles = function (callback) {
    var scripts = ['version.js', 'global.js', 'JsHttpRequest.js', 'keydown.keycodes.js', 'keydown.observer.js', 'watchdog.js', 'usbdisk.js', 'load_bar.js', 'xpcom.common.js', 'xpcom.webkit.js', 'blocking.js', 'player.js'];
    var filesloaded = 0;
    var filestoload = scripts.length;
    var i = 0;
    onLoadScript();

    function finishLoad() {
        //console.log('Loading finish');
        if (filesloaded === filestoload) {
            //console.log('Load callback');
            callback();
        }
    }

    function onLoadScript() {
        if ( i < scripts.length){
            //console.log('Loading script ' + scripts[i]);
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = scripts[i];
            script.onload = function () {
                //console.log('Loaded script ' + scripts[i]);
                filesloaded++;  // (This means increment, i.e. add one)
                i++;
                onLoadScript();
            };
            document.head.appendChild(script);
        } else {
            finishLoad();
        }
    }
};

EOD;
echo $ouput;
