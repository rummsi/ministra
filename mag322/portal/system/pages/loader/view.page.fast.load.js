/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.fast = (function () {
    var module = {},
        page,
        imageList, correctPortalURL, fastLoadTimer;


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'pageFastLoad';
        page.Init(document.getElementById('pageFastLoad'));
        page.EventHandler = function ( event ) {
            switch ( event.code ) {
                case KEYS.OK:
                    // stop fast load page timeout and show (or load) another page
                    clearTimeout(fastLoadTimer);
                    // hide current and load new page
                    page.Show(false);
                    app.loadPortal(correctPortalURL);
                    break;
                case KEYS.POWER:
                    app.view.standByMode();
                    break;
                case KEYS.MENU:
                    if ( RULES['portalsLoader/useExtPortalsPage'] !== false ) {
                        // stop fast load page timeout and show main page
                        clearTimeout(fastLoadTimer);
                        page.Show(false);
                        // go to portal select page
                        app.view.portals.show();
                    }
                    break;
            }
        };

        // apply localisation
        document.body.querySelector('.fastLoad .container .title ').innerHTML = _('Loading portal...');
        document.body.querySelector('.fastLoad .container .second').innerHTML = RULES['portalsLoader/useExtPortalsPage'] ? _('Press MENU to load portal selection screen') : '';

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

        imageLoader(imageList, function () {
                echo('images has been loaded', 'imageLoader');
                // get right portal url
                if ( app.data.portalsData.enable ) {
                    // multiportal mode: we should use default portal
                    if ( app.data.portalsData.def ) {
                        correctPortalURL = app.data.portalsData.portals[app.data.portalsData.def - 1].url
                    } else {
                        correctPortalURL = PATH_ROOT + 'services.html';
                    }
                } else {
                    // classic mode: we should check only portal1 and portal2
                    correctPortalURL = app.data.environmentData.portal1 || app.data.environmentData.portal2;
                }
                // DHCP mode: we should use dhcp portal (highest priority)
                if ( app.data.environmentData.usePortalDHCP && app.data.environmentData.portalDHCP ) {
                    correctPortalURL = app.data.environmentData.portalDHCP;
                }

                page.Show(true);
                // set timeout for default fast loading condition
                if ( accessControl.state && accessControl.data.events.loader ) {
                    setTimeout(function () {
                        accessControl.showLoginForm(function () {
                            fastLoadTimer = setTimeout(function () {
                                page.Show(false);
                                app.loadPortal(correctPortalURL);
                            }, app.data.portalsData.enable ? app.data.portalsData.time * 1000 || 3000 : 3000);
                        }, null, true);
                    }, 0);
                } else {
                    fastLoadTimer = setTimeout(function () {
                        page.Show(false);
                        app.loadPortal(correctPortalURL);
                    }, app.data.portalsData.enable ? app.data.portalsData.time * 1000 || 3000 : 3000);
                }
            }
        );
    };


    // export
    return module;
})();
