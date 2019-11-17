'use strict';

var version = '1.0.2',
	pages = {
		"back": PATH_ROOT + "services.html",
		"referrer": ''
	},
	keysBlock = false,
	gs = {
		"account": 0,
		"layers": {
			"bg": null,
			"context": null,
			"pointer": null
		},
		//"actualSize": 576,
		"size": {
			"480": {
				"scr": {
					"w": 360,
					"h": 360
				},
				"cll": {
					"w": 40,
					"h": 40
				}
			},
			"576": {
				"scr": {
					"w": 450,
					"h": 450
				},
				"cll": {
					"w": 50,
					"h": 50
				}
			},
			"720": {
				"scr": {
					"w": 495,
					"h": 495
				},
				"cll": {
					"w": 55,
					"h": 55
				}
			},
			"1080": {
				"scr": {
					"w": 765,
					"h": 765
				},
				"cll": {
					"w": 85,
					"h": 85
				}
			}
		},
		"cells": {
			"x": 9,
			"y": 9
		},
		"position": {
			"old": {
				"x": 0,
				"y": 0
			},
			"current": {
				"x": 0,
				"y": 0
			}
		},
		"move": {
			"selected": false,
			"readyToCheck": false,
			"start": {
				"x": -1,
				"y": -1
			},
			"finish": {
				"x": -1,
				"y": -1
			}
		},
		"coeff": 1,
		"balls": null,
		"colors": ['red', 'blue', 'green', 'orange', 'violet', 'black', 'white'],
		"nextBalls": []
	};
