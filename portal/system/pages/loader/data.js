/**
 * Loading screen for multiply portals and error page. Data handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';


app.data = (function () {
    var module = {};


    module.portalsData = {};


    /**
     * get all data about portals from storage
     * @returns {Object} data portals data
     */
    module.getPortals = function () {
        var data = gSTB.LoadUserData('portals.json');

        try {
            data = JSON.parse(data);
            // this data can also be changed in settings
            data.time = Number(data.time) || 0;
            data.def = Number(data.def) || 0;
        } catch ( parseError ) {
            echo('can\'t parse "portals.json": ' + parseError);

            return {enable: false};
        }

        // Multiportal mode should use same minimal delay time as default mode.
        // This fix problems with slow wifi start.
        if ( data.time === 1 ) {
            data.time = 3; // migration to new minimal value
            gSTB.SaveUserData('portals.json', JSON.stringify(data));
        }
        echo(data, 'data JSON.parse(LoadUserData("portals.json"))');
        module.portalsData = data;

        return data;
    };


    /**
     * Read DHCP data from file.
     * @returns {Object} data - data obtained through DHCP
     */
    module.getDHCPData = function () {
        var data = {},
            answer, keyAndValue;

        // get DHCP data from file-buffer
        answer = gSTB.RDir('get_dhcp_params');
        answer = (answer || '').split('\n');
        answer.forEach(function ( item ) {
            keyAndValue = item.split('=');
            if ( keyAndValue[0] ) {
                data[keyAndValue[0]] = keyAndValue[1];
            }
        });

        return data;
    };


    module.environmentData = {};

    /**
     * Read environment variables.
     * @returns {Object} data environment variables
     */
    module.getEnvData = function () {
        var answer, data;
        // get data from file
        answer = gSTB.GetEnv(JSON.stringify({
            varList: [
                'portal1', 'portal2', 'custom_url_hider', 'autoPowerDownTime',
                'standByMode', 'hdmi_event_delay', 'use_portal_dhcp', 'portal_dhcp',
                'privacyPolicyConfirmed'
            ]
        }));
        answer = JSON.parse(answer);

        if ( answer.result.errMsg ) {
            // data read error.
            data = {
                portal1:                '',
                portal2:                '',
                custom_url_hider:       false,
                autoPowerDownTime:      0,
                hdmi_event_delay:       0,
                usePortalDHCP:          false,
                portalDHCP:             '',
                privacyPolicyConfirmed: false
            };
        } else {
            data = {
                portal1:                decodeURI(answer.result.portal1),
                portal2:                decodeURI(answer.result.portal2),
                custom_url_hider:       answer.result.custom_url_hider === 'true',
                standByMode:            Number(answer.result.standByMode),
                hdmiEventDelay:         Number(answer.result.hdmi_event_delay || 0),
                autoPowerDownTime:      Number(answer.result.autoPowerDownTime || 0),
                usePortalDHCP:          answer.result.use_portal_dhcp === 'true',
                portalDHCP:             answer.result.portal_dhcp,
                privacyPolicyConfirmed: answer.result.privacyPolicyConfirmed === 'true'
            };
        }
        module.environmentData = data;

        return data;
    };


    module.rcData = {};

    /**
     * Read remote control access data from user fs.
     * @returns {Object} data access data
     */
    module.getRCData = function () {
        var remoteControlFileData = gSTB.LoadUserData('remoteControl.json');

        try {
            remoteControlFileData = JSON.parse(remoteControlFileData);
        } catch ( error ) {
            remoteControlFileData = {enable: false, deviceName: '', password: ''};
            gSTB.SaveUserData('remoteControl.json', JSON.stringify(remoteControlFileData));
        }
        module.rcData = remoteControlFileData;

        return remoteControlFileData;
    };


    /**
     * Migration from environment variables to json config file.
     */
    module.migration = function () {
        var newPortalsFile;

        if ( !module.portalsData.portals ) {
            module.getPortals();
        }
        // create portals.json file if not exist
        if ( gSTB.LoadUserData('portals.json') === '' ) {
            newPortalsFile = {
                enable: false,
                time: 0,
                def: 0,
                portals: [
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false},
                    {name: '', url: '', enable: false}
                ]
            };
            // if we have portal1 we should save it
            if ( module.portalsData.portal1 ) {
                newPortalsFile.def = 1;
                newPortalsFile.portals[0].enable = true;
                newPortalsFile.portals[0].name = '';
                newPortalsFile.portals[0].url = module.portalsData.portal1;
            }
            // if we have portal2 we should save it too
            if ( module.portalsData.portal2 ) {
                newPortalsFile.def = 2;
                newPortalsFile.portals[1].enable = true;
                newPortalsFile.portals[1].name = '';
                newPortalsFile.portals[1].url = module.portalsData.portal2;
            }
            gSTB.SaveUserData('portals.json', JSON.stringify(newPortalsFile));
            echo('Migration completed. New portals data file saved');
        }
    };


    /**
     * if operator set env variables by force we should save and use it
     */
    module.checkForceEnvironmentSet = function () {
        var saveTrigger = false;
        // if we have new portal1 we should save it
        if ( module.portalsData.portals[0].url !== module.environmentData.portal1 ) {
            module.portalsData.def = module.environmentData.portal1 ? 1 : module.portalsData.def;
            module.portalsData.portals[0].enable = !!module.environmentData.portal1;
            module.portalsData.portals[0].name = '';
            module.portalsData.portals[0].url = module.environmentData.portal1;
            saveTrigger = true;
        }
        // if we have new portal2 we should save it too
        if ( module.portalsData.portals[1].url !== module.environmentData.portal2 ) {
            module.portalsData.def = module.environmentData.portal2 ? 2 : module.portalsData.def;
            module.portalsData.portals[1].enable = !!module.environmentData.portal2;
            module.portalsData.portals[1].name = '';
            module.portalsData.portals[1].url = module.environmentData.portal2;
            saveTrigger = true;
        }

        if ( saveTrigger ) {
            gSTB.SaveUserData('portals.json', JSON.stringify(module.portalsData));
            echo('force environmental vars set completed. New portals data file saved');
        }

        // check if there is at least one working portal
        module.portalsData.empty = !module.portalsData.portals.some(function ( item ) {
            return item.url && item.enable;
        });

        return module.portalsData;
    };


    // export
    return module;
}());
