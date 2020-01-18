/**
 * Loading screen for multiply portals and error page. Views handling module.
 * @author Fedotov Dmytro <bas.jsdev@gmail.com>
 */

'use strict';

app.view.corrupted = (function ( global ) {
    var module   = {},
        // time left before massage will be closed and user will be moved to next page
        timeLeft = 20,
        page, modal,
        callback, pageTimer;


    /**
     * Page initialization.
     */
    module.init = function () {
        page = new CPage();
        page.name = 'corrupted';
        page.Init(document.getElementById('pageCorrupted'));

        modal = new CModalBox(page);
        modal.bpanel = new CButtonPanel();
        modal.bpanel.Init(global.CMODAL_IMG_PATH);
        modal.bpanel.btnExit = modal.bpanel.Add(KEYS.OK, 'ok.png', _('Continue') + ' (20)', function () {
            window.clearInterval(pageTimer);
            page.Show(false);
            callback();
        });
        modal.SetHeader(_('Warning!'));
        modal.SetContent([
            element('div', {}, _('The device is loaded in fault-protection mode.')),
            element('div', {}, _('We recommend to update the software.'))
        ]);
        modal.SetFooter(modal.bpanel.handle);
        modal.Init();

        // set page keyHandler
        page.EventHandler = function ( event ) {
            modal.bpanel.EventHandler(event);
        };
        page.onShow = function () {
            modal.Show(true, true);
        };
    };


    /**
     * Fill and show warning page (its purpose to tell user about NAND storage problems)
     *
     * @param {Object} callbackFromController will be called in 30 seconds or if user press "continue" button
     */
    module.show = function ( callbackFromController ) {
        if ( !page ) {
            module.init();
        }

        callback = callbackFromController;

        clearInterval(pageTimer);

        imageLoader([PATH_IMG_SYSTEM + 'loader/bg2.jpg'], function () {
            page.Show(true);

            pageTimer = setInterval(function () {
                timeLeft--;
                if ( timeLeft > 0 ) {
                    modal.bpanel.btnExit.$name.innerHTML = _('Continue') + ' (' + timeLeft + ')';
                } else {
                    clearInterval(pageTimer);
                    page.Show(false);
                    callback();
                }
            }, 1000);
        });
    };


    // export
    return module;
})(window);
