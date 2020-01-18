/**
 * Loading screen for multiply portals and error page. Main module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

/**
 * @namespace
 */
var app = (function ( global ) {
    var module = {},
        portalsData;


    function finishInitialisation () {
        var environment = module.data.environmentData;

        // check if URL has been blocked
        if ( module.pageUrl.query && module.pageUrl.queryKey.blocked === 'true' ) {
            module.view.blocked.show();
            return;
        }

        // check if page loading error happened
        if ( !module.pageUrl.queryKey.bootmedia && module.pageUrl.query ) {
            module.view.loading.show();
            return;
        }

        // wait until we get correct portal url through DHCP and load it
        if ( environment.usePortalDHCP && !environment.portalDHCP && RULES['waitForDHCPPortal'] ) {
            module.view.dhcp.show();
            return;
        }

        // check if there is no portals to load and as result we should load embedded portal
        if (
            // classic portal urls
            !environment.portal1 && !environment.portal2
            // DHCP portal
            && !(environment.usePortalDHCP && environment.portalDHCP)
            // new multi portal data
            && !(portalsData.enable && !portalsData.empty)
        ) {
            echo('There is no portal for loading. Redirecting to embedded portal');
            module.loadPortal(PATH_ROOT + 'services.html');
            return;
        }

        // check if we should load specific portal or show portals select page
        if (
            // old multi portal scheme
            (!(environment.portal1 && environment.portal2 && !portalsData.enable)
                // this is new multi portal scheme WITHOUT auto start flag
                && !(portalsData.enable && portalsData.time === 0))
            // DHCP portal loading
            || (environment.usePortalDHCP && environment.portalDHCP)
        ) {
            // show portal loading screen.
            module.view.fast.show();
        } else {
            // show portals selection page
            module.view.portals.show();
        }
    }

    function continueInitialization () {
        var environment = module.data.environmentData;

        // check if migration needed (portals.json not found or empty)
        module.data.migration(environment);
        // getting data about portals from portals.json
        portalsData = module.data.getPortals();
        // environment variables check mechanism
        checkEnvVars();
        // renew env data
        environment = module.data.getEnvData();
        // if operator set env variables by force we should use and save them
        portalsData = module.data.checkForceEnvironmentSet(portalsData, environment);
        module.view.init();
        accessControl.init();

        // check if we need to show privacy policy text
        if ( !environment.privacyPolicyConfirmed ) {
            module.view.privacy.show(finishInitialisation);
        } else {
            finishInitialisation();
        }
    }


    function continuePortalLoading ( portalUrl ) {
        document.getElementById('loading').style.display = 'block';
        // restore server settings page button
        gSTB.EnableServiceButton(true);
        // load portal
        setTimeout(function () {
            stbStorage.removeItem(getWindowKey(WINDOWS.PORTALS_LOADER));
            stbStorage.removeItem(getWindowKey(WINDOWS.PORTAL));
            echo(portalUrl, 'LOAD PORTAL:');
            window.location = portalUrl;
        }, 10);
    }


    // turn off all service buttons and screen saver
    gSTB.EnableServiceButton(false);
    gSTB.EnableAppButton(false);
    gSTB.EnableTvButton(false);
    gSTB.SetScreenSaverTime(0);

    // portals loader window registration
    stbStorage.setItem(getWindowKey(WINDOWS.PORTALS_LOADER), stbWebWindow.windowId());

    // prepare settings page
    gSTB.SetSettingsInitAttr(JSON.stringify({
        url: PATH_SYSTEM + 'settings/index.html',
        backgroundColor: '#000'
    }));

    // init player ( this is also HDMI events handler )
    gSTB.InitPlayer();

    /**
     * main entry point
     */
    module.init = function () {
        // get localization
        gettext.init({name: getCurrentLanguage()}, function () {
            var environment = module.data.getEnvData(),
                pageUrl     = parseUri(window.location),
                controlData = module.data.getRCData(),
                // currently applied wake up sources
                wakeUpSources;

            module.pageUrl = pageUrl;
            // init remote control
            gSTB.ConfigNetRc(controlData.deviceName, controlData.password);
            gSTB.SetNetRcStatus(controlData.enable);
            // init auto power down window
            if ( typeof gSTB.SetAutoPowerDownInitAttr === 'function' ) {
                gSTB.SetAutoPowerDownInitAttr(JSON.stringify({
                    url: PATH_SYSTEM + 'pages/standby/index.html',
                    backgroundColor: 'transparent'
                }));
                // time range: 0 - disable, max/max 10/86400 sec
                gSTB.SetAutoPowerDownTime(environment.autoPowerDownTime);
            }
            // Correct wake up sources to fix hdmi wake up reaction in deep standby mode
            if ( typeof gSTB.GetWakeUpSources === 'function' && (gSTB.SupportedWakeUpSources || []).indexOf(2) !== -1 ) {
                wakeUpSources = gSTB.GetWakeUpSources() || [];
                if ( environment.hdmiEventDelay === 0 && wakeUpSources.indexOf(2) !== -1 ) {
                    wakeUpSources.splice(wakeUpSources.indexOf(2), 1);
                    // hdmi reaction turned off so it's not a wake up src anymore
                    gSTB.SetWakeUpSources(wakeUpSources);
                } else if ( environment.hdmiEventDelay !== 0 && wakeUpSources.indexOf(2) === -1 ) {
                    wakeUpSources.push(2);
                    // hdmi reaction turned on so add it as wake up src
                    gSTB.SetWakeUpSources(wakeUpSources);
                }
            }

            // check NAND state - if corrupted we should tell user about this (page "Device is loaded in fault-protection mode")
            if ( pageUrl.queryKey.fallbackstate || pageUrl.queryKey.btnstate ) {
                stbStorage.setItem('nandEmergencyLoadingLogs', JSON.stringify({bootmedia: pageUrl.queryKey.bootmedia}));
                module.view.corrupted.show(continueInitialization);
            } else {
                continueInitialization();
            }
        });
    };


    // Main event listener
    function mainEventListener ( event ) {
        // get real key code or exit
        if ( !eventPrepare(event, false, 'mainEventListener ' + currCPage.name) ) {return;}
        if ( currCPage && typeof currCPage.EventHandler === 'function' ) {
            // stop if necessary
            if ( currCPage.EventHandler(event) ) {
                event.preventDefault();
                event.stopPropagation();
            }
        }
    }


    /**
     * load portal
     * @param {string} url - url for loading
     */
    module.loadPortal = function ( url ) {
        url = parseUri(decodeURI(url));
        // is it local file or we should use http protocol to load it?
        if ( !url.protocol ) {
            if ( gSTB.IsFileExist(url.source) ) {
                url.protocol = 'file';
            } else {
                url.protocol = 'http';
            }
            url.source = url.protocol + '://' + url.source;
        }

        // no need to ping local files
        if ( RULES['checkConnectionState'] && url.protocol !== 'file' ) {
            module.view.network.show(continuePortalLoading, url);
        } else {
            continuePortalLoading(url.source);
        }
    };

    // main entry point setup
    global.onload = module.init;
    // Main event listener
    document.addEventListener('keydown', mainEventListener);
    // prevent default right-click menu
    global.oncontextmenu = EMULATION
        ? null
        : function () {
            return false;
        };


    // export
    return module;
}(window));


// global event
var stbEvent = {
    onEvent: function () {},
    onMessage: function ( from, message, data ) {
        this.trigger(message, {from: from, data: data});
    },
    onBroadcastMessage: function ( from, message, data ) {
        echo(message, 'onBroadcastMessage');
        this.trigger('broadcast.' + message, {from: from, data: data});
    },
    event: 0
};

Events.inject(stbEvent);
