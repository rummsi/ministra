'use strict';

/**
 * Customization rules for manual tune
 * @author Stanislav Kalashnik <sk@infomir.eu>
 * @namespace
 */
var RULES = {
    // page "portals"
    // Allow "MENU" button on this page ( if false you can't start "portals" page by pressing 'MENU')
    'portalsLoader/useExtPortalsPage':   true,
    // Allow system settings on this page
    'portalsLoader/allowSystemSettings': true,

    // page "loading error"
    // Allow system settings on this page
    'page404/allowSystemSettings': true,

    // page "check connection state"
    // Check connection to portal's server before loading it.
    // If server not available go to this page and wait for connection.
    'checkConnectionState':          true,
    // Countdown (sec) before leaving this page and loading "loading error" page.
    // Change to 0 to turn off countdown and stay on this page until connection will be restored.
    'checkConnectionState/duration': 60,
    // interval (sec) between check requests to portal's server
    'checkConnectionState/interval': 3,
    // hide "Press MENU to cancel" button on this page
    'checkConnectionState/hideExit': false,

    // page "waiting for DHCP data"
    // If bootloader variable use_portal_dhcp and this option were set to true and portal_dhcp wasn't obtained
    // through DHCP - load this page and wait for portal_dhcp.
    'waitForDHCPPortal':          false,
    // Countdown (sec) before leaving this page and loading "loading error" page.
    // Change to 0 to turn off countdown and stay on this page until portal_dhcp will be obtained through DHCP.
    'waitForDHCPPortal/duration': 60,
    // hide "Press MENU to cancel" button on this page
    'waitForDHCPPortal/hideExit': false,

    // other
    // Hide all portals URL on "loading error" page and "portals" page
    'hidePortalsURL':   false,
    // Allow inner portal on "loading error" page and "portals" page
    'allowInnerPortal': true
};
