/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.network = (function () {
    var module         = {},
        firstTryToPing = true,
        page,
        timeLeft, imageList, timer, portalUrl, appCallback,
        $title, $loading;


    function sendPingRequest ( callback, config ) {
        config = config || {};

        ajax(
            'GET',
            portalUrl,
            function ( data, status ) {
                console.log('status for ' + portalUrl + ' ' + status);
                if ( typeof callback === 'function' ) {
                    callback(status === 0 || (status >= 400 && status < 600), {data: data, status: status});
                }
            },
            {},
            'text',
            true,
            config.abortTimeout || 60000
        );
    }


    function updateStatus () {
        timeLeft--;

        if ( timeLeft % RULES['checkConnectionState/interval'] === 0 ) {
            sendPingRequest(function ( error ) {
                if ( !error ) {
                    clearInterval(timer);
                    page.Show(false);
                    if ( typeof appCallback === 'function' ) {
                        appCallback(portalUrl);
                    }
                }
            });
        }

        if ( RULES['checkConnectionState/duration'] !== 0 ) {
            if ( timeLeft > 0 ) {
                $title.innerHTML = _('Waiting for network setup') + ' ' + timeLeft;
            } else {
                clearInterval(timer);
                page.Show(false);
                app.view.loading.show();
            }
        }
    }


    function checkConnectionToPortal () {
        sendPingRequest(
            function ( error ) {
                if ( error ) {
                    // What if this is slow box with bad wifi? Lets give it one more chance (wait for 3 sec).
                    if ( firstTryToPing ) {
                        firstTryToPing = false;
                        setTimeout(checkConnectionToPortal, 3000);
                    } else {
                        imageLoader(imageList, function () {
                            $loading.style.display = 'none';
                            page.Show(true);
                            timer = setInterval(updateStatus, 1000);
                        });
                    }
                } else {
                    clearInterval(timer);
                    $loading.style.display = 'none';
                    page.Show(false);
                    if ( typeof appCallback === 'function' ) {
                        appCallback(portalUrl);
                    }
                }
            },
            {abortTimeout: 3000}
        );
    }


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'pageNetworkError';
        page.Init(document.getElementById('pageNetworkError'));
        page.EventHandler = function ( event ) {
            if ( event.code === KEYS.MENU && !RULES['checkConnectionState/hideExit'] ) {
                clearInterval(timer);
                page.Show(false);
                app.view.loading.show();
            }
        };

        // apply localisation
        $title = document.body.querySelector('#pageNetworkError.fastLoad .container .title');
        $title.innerHTML = _('Waiting for network setup');
        if ( !RULES['checkConnectionState/hideExit'] ) {
            page.handle.querySelector('.fastLoad .container .second').innerHTML = _('Press MENU to cancel');
        }
        $loading = document.getElementById('loading');

        imageList = ['bg2.jpg', 'loading.png'].map(function ( image ) {
            return PATH_IMG_SYSTEM + 'loader/' + image;
        });
    };


    /**
     * Fill and show fast loader page
     */
    module.show = function ( callbackFromController, correctPortalUrl ) {
        if ( !page ) {
            module.init();
        }

        timeLeft = RULES['checkConnectionState/duration'] || 30;
        // reset timer
        $title.innerHTML = _('Waiting for network setup') + ' ' + timeLeft;
        $loading.style.display = 'block';

        appCallback = callbackFromController;
        portalUrl = correctPortalUrl.source;
        firstTryToPing = true;
        checkConnectionToPortal();
    };


    // export
    return module;
})();
