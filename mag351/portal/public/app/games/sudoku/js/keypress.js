'use strict';

var key = {
	"press": function ( event ) {
		// get real key code or exit
		if ( !eventPrepare(event, false) ) {
			return;
		}

		document.getElementById('errorCell').style.display = 'none';
		if ( VKBlock == true ) {
			switch ( event.code ) {
				case KEYS.DOWN:
					cvDraw.startCursor(-1);
					break;
				case KEYS.UP:
					cvDraw.startCursor(1);
					break;
				case KEYS.OK:
					cvDraw.PressOK();
					break;
				case KEYS.EXIT:
					// hide everything before go away
					document.body.style.display = 'none';
					document.body.parentElement.style.background = 'none';
					if ( pages.referrer.length > 4 ) {
						window.location = pages.referrer;
					} else {
						window.location = pages.back;
					}
					break;
				case KEYS.POWER:
					triggerStandBy();
					break;
			}
			return;
		}
		switch ( event.code ) {
			case KEYS.RIGHT:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.x + 1 <= gs.infoItems.x - 1 ) {
					gs.position.current.x++;
				} else {
					gs.position.current.x = 0;
				}
				cvDraw.item();
				break;
			case KEYS.LEFT:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.x - 1 >= 0 ) {
					gs.position.current.x--;
				} else {
					gs.position.current.x = gs.infoItems.x - 1;
				}
				cvDraw.item();
				break;
			case KEYS.DOWN:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.y + 1 <= gs.infoItems.y - 1 ) {
					gs.position.current.y++;
				} else {
					gs.position.current.y = 0;
				}
				cvDraw.item();
				break;
			case KEYS.UP:
				gs.position.old.y = gs.position.current.y;
				gs.position.old.x = gs.position.current.x;
				if ( gs.position.current.y - 1 >= 0 ) {
					gs.position.current.y--;
				} else {
					gs.position.current.y = gs.infoItems.y - 1;
				}
				cvDraw.item();
				break;
			case KEYS.NUM1:
			case KEYS.NUM2:
			case KEYS.NUM3:
			case KEYS.NUM4:
			case KEYS.NUM5:
			case KEYS.NUM6:
			case KEYS.NUM7:
			case KEYS.NUM8:
			case KEYS.NUM9:
				cvDraw.PressNUM(event.code);
				break;
			case KEYS.BACK:
				cvDraw.Erase();
				break;
			case KEYS.F1:
				cvDraw.Tips();
				break;
			case KEYS.F4:
				cvDraw.Candidats();
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
				if ( pages.referrer.length > 4 ) {
					window.location = pages.referrer;
				} else {
					window.location = pages.back;
				}
				break;
			case KEYS.POWER:
				triggerStandBy();
				break;
		}
	}
};

function triggerStandBy () {
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
}
