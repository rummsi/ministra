﻿'use strict';

var key = {
	"press": function ( event ) {
		// get real key code or exit
		if ( !eventPrepare(event, false) ) return;

		switch ( event.code ) {
			case KEYS.RIGHT:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.x + 1 <= gs.iItems.x - 1 ) {
					gs.position.current.x++;
				} else {
					gs.position.current.x = gs.iItems.x - 1;
				}
				cvDraw.item();
				break;
			case KEYS.LEFT:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.x - 1 >= 0 ) {
					gs.position.current.x--;
				} else {
					gs.position.current.x = 0;
				}
				cvDraw.item();
				break;
			case KEYS.DOWN:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.y + 1 <= gs.iItems.y - 1 ) {
					gs.position.current.y++;
				} else {
					gs.position.current.y = gs.iItems.y - 1;
				}
				cvDraw.item();
				break;
			case KEYS.UP:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.y - 1 >= 0 ) {
					gs.position.current.y--;
				} else {
					gs.position.current.y = 0;
				}
				cvDraw.item();
				break;
			case KEYS.OK:
				cvDraw.PressOK();
				break;
			case KEYS.REFRESH:
				var new_loc = window.location.href.substr(0, window.location.href.indexOf('?'));
				window.location = new_loc + '?referrer=' + base64Encode(pages.referrer);
				break;
			case KEYS.EXIT:
				// hide everything before go away
				document.body.style.display = 'none';
				document.body.parentElement.style.background = 'none';
				window.location = pages.back;
				/*if ( pages.referrer.length > 4 ) {
					window.location = pages.referrer;
				} else {
					window.location = pages.back;
				}*/
				break;
			case KEYS.POWER:
				var standByStatus = !gSTB.GetStandByStatus(),
					environment = gSTB.GetEnv(JSON.stringify({
						varList: ['standByMode']
					})),
					standByMode;

				try {
					environment = JSON.parse(environment).result;
				} catch ( e ) {
					environment = {
						standByMode: null
					};
				}

				standByMode = parseInt(environment.standByMode, 10);

				// check stand by mode trigger
				if ( gSTB.StandByMode !== standByMode ) {
					gSTB.StandByMode = standByMode;
				}

				// deep standBy mode
				if ( standByMode === 3 ) {
					gSTB.SetLedIndicatorMode(2);
					gSTB.StandBy(standByStatus);
					gSTB.SetLedIndicatorMode(1);
					return;
				}

				// active standBy mode
				if ( standByStatus ) {
					document.body.style.display = 'none'; // hide page if exit
					gSTB.StandBy(standByStatus);
					gSTB.SetLedIndicatorMode(2);
					window.location = ''; // exit from app if needed
				} else {
					gSTB.StandBy(standByStatus);
					gSTB.SetLedIndicatorMode(1);
				}
				break;
		}
	}
};
