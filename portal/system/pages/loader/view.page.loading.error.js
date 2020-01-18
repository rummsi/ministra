/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.loading = (function () {
    var module = {},
        page, list,
        ping, imageList, updateTimer;


    ping = {
        // Set of ping addresses randomized on STB start. In case of problems with selected server we should switch to next one.
        servers: [
            'http://echo-01.infomir.com/',
            'http://echo-02.infomir.com/',
            'http://echo-03.infomir.com/',
            'http://echo-04.infomir.com/'
        ].shuffle(),
        attempts: 3,
        errorCount: 0,
        // current active ping address position
        linkIndex: 0,
        /**
         * Periodically check online status.
         * List of address is in the config.
         */
        sendRequest: function () {
            // send
            ajax(
                'get',
                ping.servers[ping.linkIndex] + '?time=' + Date.now(),
                function ( data, status ) {
                    var state        = status > 0 && data === 'ok',
                        $infoElement = document.getElementById('internet');

                    $infoElement.className = (state ? '' : 'error');
                    $infoElement.innerHTML = (state ? _('Enabled') : _('Not available'));

                    if ( !state ) {
                        // try next address
                        ping.linkIndex = ++ping.linkIndex % ping.servers.length;
                        // check limit
                        if ( ++ping.errorCount >= ping.attempts ) {
                            // stop trying
                            ping.errorCount = 0;
                        } else {
                            ping.sendRequest();
                        }
                    } else {
                        ping.errorCount = 0;
                    }
                });
        }
    };


    /***
     *  display wifi and lan connection, MAC, IP and internet connection status at information block.
     */
    function updateInterfacesState () {
        var $infoBlock = page.handleInner.querySelector('.body .content .infoList'),
            oldInfo    = {
                $ethernetState: $infoBlock.querySelector('#ethernet'),
                $ethernetIP: $infoBlock.querySelector('#ethernetIP'),
                $wifiState: $infoBlock.querySelector('#wifi'),
                $wifiIP: $infoBlock.querySelector('#wifiIP'),
                $wifiMAC: $infoBlock.querySelector('#wifiMAC')
            };

        updateTimer = setInterval(function () {
            var newInfo = {
                ethernetState: gSTB.GetLanLinkStatus(),
                ethernetIP: gSTB.RDir('IPAddress'),
                wifiState: gSTB.GetWifiLinkStatus(),
                wifiIP: gSTB.RDir('WiFi_ip'),
                wifiMAC: gSTB.GetNetworkWifiMac()
            };

            // Ethernet state
            if ( newInfo.ethernetState !== (oldInfo.$ethernetState.className !== 'error') ) {
                oldInfo.$ethernetState.className = newInfo.ethernetState ? '' : 'error';
                oldInfo.$ethernetState.innerHTML = newInfo.ethernetState ? _('Enabled') : _('Not connected');
            }
            if ( !!newInfo.ethernetIP !== (oldInfo.$ethernetIP.className !== 'error') ) {
                oldInfo.$ethernetIP.className = !!newInfo.ethernetIP ? '' : 'error';
                oldInfo.$ethernetIP.innerHTML = !!newInfo.ethernetIP ? newInfo.ethernetIP : _('Not available');
            }

            // WIFI state
            if ( newInfo.wifiMAC ) {
                if ( newInfo.wifiState !== (oldInfo.$wifiState.className !== 'error' && oldInfo.$wifiState.className !== 'noDevice') ) {
                    oldInfo.$wifiState.className = newInfo.wifiState ? '' : 'error';
                    oldInfo.$wifiState.innerHTML = newInfo.wifiState ? _('Enabled') : _('Disabled');
                }
                if ( !!newInfo.wifiIP !== (oldInfo.$wifiIP.className !== 'error' && oldInfo.$wifiIP.className !== 'noDevice') ) {
                    oldInfo.$wifiIP.className = !!newInfo.wifiIP ? '' : 'error';
                    oldInfo.$wifiIP.innerHTML = !!newInfo.wifiIP ? newInfo.wifiIP : _('Not available');
                }
                if ( oldInfo.$wifiMAC.className === 'error' || oldInfo.$wifiMAC.className === 'noDevice' ) {
                    oldInfo.$wifiMAC.className = '';
                    oldInfo.$wifiMAC.innerHTML = newInfo.wifiMAC;
                }
            } else {
                // no device
                if ( oldInfo.$wifiMAC.className !== 'noDevice' ) {
                    oldInfo.$wifiState.className = 'noDevice';
                    oldInfo.$wifiState.innerHTML = _('Disabled');
                    oldInfo.$wifiMAC.className = 'noDevice';
                    oldInfo.$wifiMAC.innerHTML = _('Not available');
                    oldInfo.$wifiIP.className = 'noDevice';
                    oldInfo.$wifiIP.innerHTML = _('Not available');
                }
            }

            // get internet connection
            ping.sendRequest();
        }, 5000);
    }


    /**
     * Page initialization.
     */
    module.init = function () {
        var hideUrls = RULES.hidePortalsURL || app.data.environmentData.custom_url_hider,
            items    = app.view.setServiceItems(app.data.portalsData),
            amount, settings, newScheme, oldScheme,
            $infoElement, $listItem, $infoList;

        // shortcuts
        amount = items.serviceItemsNumber;
        settings = items.hasServiceSettingItem;
        items = items.itemsArray;

        page = new CPage();
        page.name = 'main';
        page.Init(document.getElementById('pageLoadingError'));
        // set page keyHandler
        page.EventHandler = function ( event ) {
            switch ( event.code ) {
                case KEYS.EXIT:
                    break;
                case KEYS.SET:
                    // load server settings page (if allowed)
                    if ( RULES['page404/allowSystemSettings'] ) {
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
                $listItem = element('div', {className: app.view.getPortalType(items[i].url, items[i].name)}, '');
                elchild($listItem, [
                    element('div', {className: 'icon'}, ''),
                    element('div', {className: 'portalInfo'}, [
                        element('div', {className: hideUrls || items[i].servItem ? 'text single' : 'text'}, items[i].name),
                        // if hide url option was applied at ini file
                        element('div', {className: 'url'}, hideUrls || items[i].servItem ? '' : items[i].url)
                    ])
                ]);
                // add element to scroll list
                list.Add($listItem, {
                    url: items[i].url,
                    onclick: function () {
                        if ( this.url.indexOf(PATH_SYSTEM + 'settings/index.html') === -1 ) {
                            // load focused item page
                            clearInterval(updateTimer);
                            page.Show(false);
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
        $infoElement = page.handleInner.querySelector('.header .info div');
        $infoElement.innerHTML = _('Page loading error');
        $infoElement = page.handleInner.querySelector('.header .info span');
        $infoElement.innerHTML = _('Please check the connection and the page URL');
        // right panel
        $infoList = page.handleInner.querySelector('.content .infoList');
        // internet block
        $infoList.children[0].children[0].children[1].innerHTML = _('Internet: ');
        // ethernet block
        $infoList.children[1].children[1].children[1].innerHTML = _('Ethernet: ');
        $infoList.children[1].children[1].children[2].innerHTML = gSTB.GetLanLinkStatus() ? _('Enabled') : _('Not connected');
        $infoList.children[1].children[2].children[0].innerHTML = _('IP: ');
        $infoList.children[1].children[2].children[1].innerHTML = gSTB.RDir('IPAddress') || _('Not available');
        $infoList.children[1].children[3].children[0].innerHTML = _('MAC: ');
        $infoList.children[1].children[3].children[1].innerHTML = gSTB.GetDeviceMacAddress() || _('Not available');
        // wifi block
        $infoList.children[2].children[1].children[1].innerHTML = _('Wi-Fi: ');
        $infoList.children[2].children[1].children[2].innerHTML = gSTB.GetWifiLinkStatus() ? _('Enabled') : _('Not connected');
        $infoList.children[2].children[2].children[0].innerHTML = _('IP: ');
        $infoList.children[2].children[2].children[1].innerHTML = gSTB.RDir('WiFi_ip') || _('Not available');
        $infoList.children[2].children[3].children[0].innerHTML = _('MAC: ');
        $infoList.children[2].children[3].children[1].innerHTML = gSTB.GetNetworkWifiMac() || _('Not available');
        // device info block
        $infoList.children[3].children[1].children[0].innerHTML = _('Model: ');
        $infoList.children[3].children[1].children[1].innerHTML = gSTB.GetDeviceModelExt() || _('Not available');
        $infoList.children[3].children[2].children[0].innerHTML = _('Version: ');
        $infoList.children[3].children[2].children[1].innerHTML = gSTB.GetDeviceImageDesc() || _('Not available');

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
        // Get internet state
        ping.sendRequest();
        // start connection monitoring
        updateInterfacesState();

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
