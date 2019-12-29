/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view = (function ( global ) {
    var module = {};


    /**
     * Page initialization.
     */
    module.init = function () {
        // set webkit size
        global.moveTo(0, 0);
        window.resizeTo(EMULATION ? window.outerWidth : screen.width, EMULATION ? window.outerHeight : screen.height);

        global.CMODAL_IMG_PATH = configuration.newRemoteControl ? PATH_IMG_SYSTEM + 'buttons/new/' : PATH_IMG_SYSTEM + 'buttons/old/';

        /**
         * toggle stand by mode
         */
        module.standByMode = function () {
            var standByStatus = !gSTB.GetStandByStatus(),
                standByMode   = parseInt(app.data.environmentData.standByMode, 10);

            echo('PORTALS_LOADER.standByStatus=' + standByStatus);
            // check stand by mode trigger
            if ( gSTB.StandByMode !== standByMode ) {
                gSTB.StandByMode = standByMode;
            }
            // deep standBy mode
            if ( app.standByMode === 3 ) {
                gSTB.SetLedIndicatorMode(2);
                gSTB.StandBy(standByStatus);
                gSTB.SetLedIndicatorMode(1);
                return;
            }
            // active standby mode
            gSTB.StandBy(standByStatus);
            if ( standByStatus ) {
                gSTB.SetLedIndicatorMode(2);
            } else {
                gSTB.SetLedIndicatorMode(1);
            }
        };

        stbEvent.bind('window.reload', function () {
            document.body.style.display = 'none';
            stbStorage.removeItem(getWindowKey(WINDOWS.PORTALS_LOADER));
            window.location.reload();
        });

        stbEvent.bind('portal.standbyMode', function () {
            console.log('got portal.standbyMode');
            module.standByMode();
        });
    };


    /**
     * Set special item in list
     * @param {Object} data portals list
     * @returns {Object} data modified portal list with new items
     */
    module.setServiceItems = function ( data ) {
        var items                 = [],
            serviceItemsNumber    = 0,
            hasServiceSettingItem = false;

        // set 'try again' as first item (but don't show it for blocked urls)
        if ( !app.pageUrl.queryKey.bootmedia && app.pageUrl.queryKey.blocked !== 'true' && app.pageUrl.query ) {
            items.push({
                name: _('Try again'),
                url: app.pageUrl.queryKey.url ? decodeURIComponent(app.pageUrl.queryKey.url) : decodeURIComponent(app.pageUrl.query),
                enable: true
            });
            serviceItemsNumber++;
        }
        // Set inner portal as next item (if allowed)
        if ( RULES.allowInnerPortal ) {
            items.push({
                name: _('Embedded portal'),
                url: PATH_ROOT + 'services.html',
                enable: true,
                servItem: true
            });
            serviceItemsNumber++;
        }
        // portals
        (data.portals || []).forEach(function ( item ) {
            items.push(item);
        });
        // Set service menu as next item (if allowed)
        if ( RULES['page404/allowSystemSettings'] && app.isItErrorPage || RULES['portalsLoader/allowSystemSettings'] && !app.isItErrorPage ) {
            items.push({
                name: _('System settings'),
                url: PATH_SYSTEM + 'settings/index.html',
                enable: true,
                servItem: true
            });
            hasServiceSettingItem = true;
        }

        return {
            itemsArray: items,
            serviceItemsNumber: serviceItemsNumber,
            hasServiceSettingItem: hasServiceSettingItem
        };
    };


    /**
     * Set corresponding icon style
     * @param {string} url current item url
     * @param {string} name current item name
     * @returns {string} iconStyle CSS class name for specific icon.
     */
    module.getPortalType = function ( url, name ) {
        var urlByParts = parseUri(url),
            iconClass;

        // settings page
        if ( 'file://' + urlByParts.path === PATH_SYSTEM + 'settings/index.html' ) {
            iconClass = 'settings';
        } else
        // refresh action
        if ( name === _('Try again') ) {
            iconClass = 'refresh';
        } else
        // use internet for portal loading
        if ( urlByParts.protocol === 'http' || urlByParts.protocol === 'https' ) {
            iconClass = 'internet';
        } else
        // use path 'file:///home/...page.html' or '/home/web/...page.html' or '/media/...page.html'
        if ( urlByParts.protocol.indexOf('file') !== -1 || urlByParts.path.indexOf(PATH_ROOT) !== -1 || urlByParts.path.indexOf(PATH_MEDIA) !== -1 ) {
            iconClass = 'home';
        } else {
            iconClass = 'local'; // use local network for loading
        }
        return iconClass;
    };


    // export
    return module;
})(window);
