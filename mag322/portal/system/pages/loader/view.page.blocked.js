/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.blocked = (function ( global ) {
    var module = {},
        page, modal;


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'blocked';
        page.Init(document.getElementById('pageBlocked'));
        page.EventHandler = function ( event ) {
            if ( event.code === KEYS.POWER ) {
                app.view.standByMode();
            } else {
                modal.bpanel.EventHandler(event);
            }
        };
        page.onShow = function () {
            modal.Show(true, true);
        };

        // modal message with warning
        modal = new CModalBox(page);
        modal.bpanel = new CButtonPanel();
        modal.bpanel.Init(global.CMODAL_IMG_PATH);
        modal.bpanel.btnExit = modal.bpanel.Add(
            KEYS.OK,
            'ok.png',
            _('Ok'),
            function () {
                modal.Show(false);
                page.Show(false);
                // go to portal select page
                app.view.portals.show();
            }
        );
        modal.SetHeader(_('Warning!'));
        modal.SetContent([
            element('div', {}, _('Access to the web page you were trying to visit has been blocked due to a report of copyright infringement.')),
            element('div', {}, _('Please contact your IPTV service provider for more information.'))
        ]);
        modal.SetFooter(modal.bpanel.handle);
        modal.Init();
    };


    module.show = function () {
        if ( !page ) {
            module.init();
        }

        imageLoader([PATH_IMG_SYSTEM + 'loader/bg.png'], function () {
            page.Show(true);
        });
    };


    // export
    return module;
})(window);
