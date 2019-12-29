/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.portals = (function () {
    var module = {},
        page, list,
        imageList;

    /**
     * Page initialization.
     */
    module.init = function () {
        var hideUrls = RULES.hidePortalsURL || app.data.environmentData.custom_url_hider,
            items    = app.view.setServiceItems(app.data.portalsData),
            amount, settings, newScheme, oldScheme,
            $someListItem, $infoElement;

        // shortcuts
        amount = items.serviceItemsNumber;
        settings = items.hasServiceSettingItem;
        items = items.itemsArray;

        page = new CPage();
        page.name = 'pagePortals';
        page.Init(document.getElementById('pagePortals'));
        // set page keyHandler
        page.EventHandler = function ( event ) {
            switch ( event.code ) {
                case KEYS.EXIT:
                    break;
                case KEYS.SET:
                    // load server settings page (if allowed)
                    if ( RULES['portalsLoader/allowSystemSettings'] ) {
                        gSTB.StartLocalCfg();
                    }
                    break;
                case KEYS.POWER:
                    app.view.standByMode();
                    break;
                default:
                    list.EventHandler(event);
            }
        };
        page.onShow = function () {
            list.Activate(true, true);
        };

        // create and init portals list
        list = new CScrollList(page);
        list.Init(page.handleInner.querySelector('.content .cslist-main'));
        // fill portals list
        for ( var i = 0; i < items.length; i++ ) {
            // if (multiportal new scheme loading) => add if portal is turned on
            newScheme = app.data.portalsData.enable && items[i].enable;
            // if (old scheme) => always add portal1, portal2, inner portal (if allowed), try again (if this is error page) and sys settings (if allowed)
            oldScheme = !app.data.portalsData.enable && ((i < amount + 2) || (settings && (i === (items.length - 1))));
            // add if they has URL
            if ( (newScheme || oldScheme) && items[i].url ) {
                items[i].name = items[i].name || (_('Portal') + ' ' + (i - amount + 1)); // don't count service items
                // create element (icon + name + url) and get type of portal to set specific icon
                $someListItem = element('div', {className: app.view.getPortalType(items[i].url, items[i].name)}, '');
                elchild($someListItem, [
                    element('div', {className: 'icon'}, ''),
                    element('div', {className: 'portalInfo'}, [
                        element('div', {className: hideUrls || items[i].servItem ? 'text single' : 'text'}, items[i].name),
                        // if hide url option was applied at ini file
                        element('div', {className: 'url'}, hideUrls || items[i].servItem ? '' : items[i].url)
                    ])
                ]);
                // add element to scroll list
                list.Add($someListItem, {
                    url: items[i].url,
                    onclick: function () {
                        if ( this.url.indexOf(PATH_SYSTEM + 'settings/index.html') === -1 ) {
                            // load focused item page
                            app.loadPortal(this.url);
                        } else {
                            // load server settings page
                            gSTB.StartLocalCfg();
                        }
                        // prevent page from reloading
                        return false;
                    }
                });
            }
        }

        // apply localisation
        $infoElement = page.handleInner.querySelector('.header .info');
        $infoElement.innerHTML = _('Select a portal for loading');

        imageList = ['bg.png', 'home.png', 'local.png', 'net.png', 'settings.png', 'refresh.png', 'info.png', 'lan.png', 'wifi.png', 'connect.png'].map(function ( image ) {
            return PATH_IMG_SYSTEM + 'loader/' + image;
        });
    };


    /**
     * Fill and show page
     */
    module.show = function () {
        if ( !page ) {
            module.init();
        }

        imageLoader(imageList, function () {
            page.Show(true);

            if ( accessControl.state && accessControl.data.events.loader ) {
                window.setTimeout(function () {
                    accessControl.showLoginForm(null, null, true);
                }, 0);
            }
        });
    };


    // export
    return module;
})();
