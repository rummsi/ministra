/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.dhcp = (function () {
    var module = {},
        page,
        timeLeft, imageList, timer, portalUrl,
        $title;


    function checkDHCPData () {
        timeLeft--;

        if ( timeLeft % 2 === 0 ) {
            portalUrl = app.data.getDHCPData().portal_dhcp;
            if ( portalUrl ) {
                clearInterval(timer);
                // mag322 and some other mags need some time to finish their network setup scripts. So we will wait here.
                // Otherwise we will get half-working network with loading problems.
                setTimeout(function () {
                    page.Show(false);
                    app.loadPortal(portalUrl);
                }, 10000);
            }
        }

        if ( RULES['waitForDHCPPortal/duration'] !== 0 ) {
            if ( timeLeft > 0 ) {
                $title.innerHTML = _('Waiting for network setup') + ' ' + timeLeft;
            } else {
                clearInterval(timer);
                page.Show(false);
                app.view.loading.show();
            }
        }
    }


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'pageDHCPWaiting';
        page.Init(document.getElementById('pageDHCPWaiting'));
        page.EventHandler = function ( event ) {
            if ( event.code === KEYS.MENU && !RULES['waitForDHCPPortal/hideExit'] ) {
                clearInterval(timer);
                page.Show(false);
                app.view.loading.show();
            }
        };

        // apply localization
        $title = document.body.querySelector('#pageDHCPWaiting.fastLoad .container .title');
        $title.innerHTML = _('Waiting for network setup');
        if ( !RULES['waitForDHCPPortal/hideExit'] ) {
            page.handle.querySelector('.fastLoad .container .second').innerHTML = _('Press MENU to cancel');
        }

        imageList = ['bg2.jpg', 'loading.png'].map(function ( image ) {
            return PATH_IMG_SYSTEM + 'loader/' + image;
        });
    };


    /**
     * Fill and show fast loader page
     */
    module.show = function () {
        if ( !page ) {
            module.init();
        }

        timeLeft = RULES['waitForDHCPPortal/duration'] || 30;
        // reset timer
        $title.innerHTML = _('Waiting for network setup') + ' ' + timeLeft;

        imageLoader(imageList, function () {
            page.Show(true);
            timer = setInterval(checkDHCPData, 1000);
        });
    };


    // export
    return module;
})();
