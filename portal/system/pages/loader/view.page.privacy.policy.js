/**
 * Page with privacy policy agreement. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.privacy = (function ( global ) {
    var module                 = {},
        resetCombination       = [KEYS.NUM1, KEYS.NUM4, KEYS.NUM0, KEYS.NUM7],
        combination            = [],
        ignoreThisConfirmation = false,
        page, modal, callback,
        $scrollArea;


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'privacy';
        page.Init(document.getElementById('pagePrivacyPolicy'));

        modal = new CModalBox(page);
        modal.bpanel = new CButtonPanel();
        modal.bpanel.Init(global.CMODAL_IMG_PATH);
        modal.bpanel.buttonOk = modal.bpanel.Add(KEYS.OK, 'ok.png', _('Confirm'), function () {
            gSTB.SetEnv(JSON.stringify({privacyPolicyConfirmed: true}));
            page.Show(false);
            callback();
        });

        modal.SetHeader(_('Privacy Policy'));

        // This text should be here. Otherwise it won't be correctly localized by gettext.
        modal.SetContent([
            element('p', {className: 'instruction'}, _('We respect your privacy and process data in ' +
                'accordance with our Privacy Policy. By clicking \'OK\' you confirm that you ' +
                'familiarised yourself with this Privacy Policy.')),
            element('hr', {}),

            $scrollArea = element('div', {className: 'content'}, [
                element('p', {className: 'header'}, _('Privacy Policy')),
                element('p', {}, _('This Privacy Policy is effective as of May 25, 2018.')),

                element('p', {className: 'header'}, _('What This Policy Covers')),
                element('p', {
                    innerHTML: _('We respect the confidentiality of your personal data and take measures to ' +
                        'safeguard it. This Policy describes the information we collect, how we use that ' +
                        'information, our legal basis for doing so, and your rights regarding the ' +
                        'information we collect.<br>' +
                        'We have implemented and maintain appropriate technical and organisational ' +
                        'measures in order to protect personal data against loss, misuse and alteration, ' +
                        'and to prevent personal data from being accessed by unauthorised persons. All ' +
                        'your personal data is stored on secure servers.')
                }),

                element('p', {className: 'header'}, _('Who We Are')),
                element('p', {}, _('When we refer to “we”, “our” or “us” in this Policy, we mean the company ' +
                    'Telecommunication Technologies LLC, which acts as a controller of the ' +
                    'information that is being collected from you. We list our contact details, as ' +
                    'well as the contact details of our EU representative, at the end of this Policy.')),

                element('p', {className: 'header'}, _('What We Collect')),
                element('p', {
                    innerHTML: _('In the process of your use of this set-top box (device), we may collect the ' +
                        'following data.<br>' +
                        '<strong>Device technical information.</strong> This data comprises device type, ' +
                        'serial number, MAC address, User Agent of a browser of the device, firmware ' +
                        'version of the device, language of the device, portal URL used on the device, ' +
                        'date of first use of a codec (computer program for encoding or decoding a ' +
                        'digital data stream or signal) on the device, statistics on use of the ' +
                        'applications installed on the device.<br>' +
                        '<strong>Device location information.</strong> This data comprises country and ' +
                        'city of the device, IP-address used by the device.')
                }),

                element('p', {className: 'header'}, _('Lawful Basis and Use of Collected Information')),
                element('p', {
                    innerHTML: _('We process personal data based on our legitimate interests for the following ' +
                        'purposes:<br> ' +
                        '<strong>Use of data analytics to improve and develop our products and ' +
                        'services.</strong> For these purposes, we collect device type, serial ' +
                        'number, MAC address, User Agent of a browser of the device, firmware version of ' +
                        'the device, language of the device, portal URL used on the device, statistics on ' +
                        'use of the applications installed on the device, country and city of the device, ' +
                        'IP-address used by the device.<br> ' +
                        '<strong>Troubleshooting, data analysis, testing, system maintenance, user ' +
                        'support, and counterfeiting prevention.</strong> For these purposes, we ' +
                        'collect device type, serial number, MAC address, User Agent of a browser of the ' +
                        'device, firmware version of the device, language of the device, portal URL used ' +
                        'on the device, statistics on use of the applications installed on the device, ' +
                        'country and city of the device, IP-address used by the device. <br> ' +
                        '<strong>Statistics on use of codecs.</strong> For these purposes, we collect ' +
                        'device type, serial number, MAC address, firmware version of the device, country ' +
                        'and city of the device, IP-address used by the device, date of first use of a ' +
                        'codec.')
                }),

                element('p', {className: 'header'}, _('How We Share Information We Collect')),
                element('p', {}, _('We do not normally share with third parties personal information we collect. We ' +
                    'limit access to your personal data to employees, agents, and contractors who ' +
                    'need to access it to perform the abovementioned purposes. We may share the ' +
                    'information with our affiliated companies in the course of normal business ' +
                    'activities. Personal information may also get into possession of another ' +
                    'controller as a result of a merger, acquisition or another business transaction.')),

                element('p', {className: 'header'}, _('How Long We Keep Information')),
                element('p', {}, _('We keep the information we collect for as long as it is necessary to fulfill the ' +
                    'purposes for which it was collected based on the following criteria: ' +
                    'the information we collect is device-specific; ' +
                    'the information we collect is of non-sensitive nature.')),

                element('p', {className: 'header'}, _('Your Rights')),
                element('p', {
                    innerHTML: _('Data subjects residing in certain countries, including the EU, are afforded ' +
                        'certain rights regarding their personal information, subject to exceptions and ' +
                        'exemptions set out in the applicable legislation. To exercise your rights, you ' +
                        'can contact us at our email dataprivacy@infomir.com. These rights include:<br> ' +
                        '<strong>Access to your personal data.</strong> You can clarify whether your ' +
                        'personal data is being processed and ask us for a copy of your personal data in ' +
                        'machine-readable form.<br>' +
                        '<strong>Change or correct your personal data.</strong> You can ask us to change, ' +
                        'update or fix your personal data in certain cases, particularly if it is ' +
                        'inaccurate.<br> ' +
                        '<strong>Restrict processing or delete your personal data.</strong> In some ' +
                        'cases, you can ask us to restrict processing of your personal data or delete all ' +
                        'or some of your personal data.<br>' +
                        '<strong>Object to processing.</strong> If you believe that the collection or ' +
                        'processing of your personal data is unlawful on grounds related to your ' +
                        'situation, you can object to such collection or processing. ' +
                        '<strong>Data portability.</strong> In certain cases, you can transmit your ' +
                        'personal data collected by us to another controller or ask us to transmit your ' +
                        'personal data to another controller.<br>' +
                        '<strong>File a complaint with a supervisory authority.</strong> You may have the ' +
                        'right to file a complaint with a relevant supervisory authority if you believe ' +
                        'that your data protection rights are being infringed.<br>' +
                        '<strong>Right to object to automated decision-making.</strong> You have the ' +
                        'right not to be subject to automated decision-making, including profiling, which ' +
                        'has legal or other significant effects on you.')
                }),

                element('p', {className: 'header'}, _('How We Protect Personal Data')),
                element('p', {}, _('While we implement safeguards designed to protect your information, no security ' +
                    'system is impenetrable and due to the inherent nature of the Internet, we cannot ' +
                    'guarantee that data, during transmission through the Internet or while stored on ' +
                    'our systems or otherwise in our care, is absolutely safe from intrusion by ' +
                    'others. We will respond to requests about this within a reasonable timeframe.')),

                element('p', {className: 'header'}, _('Our Policy Towards Minors')),
                element('p', {}, _('We collect only information that relates to the device and do not knowingly ' +
                    'collect personal information from natural persons under 18.')),

                element('p', {className: 'header'}, _('Changes to Our Privacy Policy')),
                element('p', {}, _('We may change our Privacy Policy from time to time. We will let you know of any ' +
                    'changes and their effective date.')),

                element('p', {className: 'header'}, _('Contact Information')),
                element('p', {}, _('If you have any questions or complaints regarding this Policy or you want to ' +
                    'exercise your data protection related rights, you can communicate with us at the ' +
                    'email address dataprivacy@infomir.com or by post at the addresses below:')),
                element('p', {
                    innerHTML: _('<strong>Controller:</strong><br>' +
                        'Telecommunication Technologies LLC<br>1 Mytna Square<br>Odesa 65026<br>Ukraine<br><br>' +
                        '<strong>EU representative:</strong><br>' +
                        'Infomir OÜ <br>Rävala pst. 8, kabinet A312 <br>Tallinn 10143 <br>Estonia')
                })
            ])
        ]);

        modal.SetFooter(modal.bpanel.handle);
        modal.Init();

        // set page keyHandler
        page.EventHandler = function ( event ) {
            switch ( event.code ) {
                case KEYS.UP:
                    $scrollArea.scrollByLines(-2);
                    break;
                case KEYS.DOWN:
                    $scrollArea.scrollByLines(2);
                    break;
                case KEYS.PAGE_DOWN:
                    $scrollArea.scrollByPages(1);
                    break;
                case KEYS.PAGE_UP:
                    $scrollArea.scrollByPages(-1);
                    break;
                default:
                    modal.bpanel.EventHandler(event);
            }

            // hidden skip
            if ( event.code >= KEYS.NUM0 && event.code <= KEYS.NUM9 ) {
                if ( resetCombination.indexOf(event.code) !== -1 ) {
                    combination.push(event.code);
                    if (
                        combination.length === resetCombination.length
                        && combination.join() === resetCombination.join()
                    ) {
                        gSTB.SetEnv(JSON.stringify({privacyPolicyConfirmed: false}));
                        page.Show(false);
                        callback();
                    }
                } else {
                    combination = [];
                }
            }
        };

        page.onShow = function () {
            modal.Show(true, true);
        };
    };


    /**
     * Fill and show page with privacy policy text
     *
     * @param {Object} callbackFromController will be called when user press "skip" or "confirm" button
     */
    module.show = function ( callbackFromController ) {
        if ( !page ) {
            module.init();
        }

        callback = callbackFromController;

        imageLoader([PATH_IMG_SYSTEM + 'loader/bg2.jpg'], function () {
            page.Show(true);
        });
    };


    // export
    return module;
})(window);
