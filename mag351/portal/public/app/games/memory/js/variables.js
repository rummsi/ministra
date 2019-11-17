'use strict';

var version = '1.0.1',
	pages = {
		'back': PATH_ROOT + 'services.html',
		'referrer': ''
	},
	VKBlock = true,
	gs = { // game settings
		'layers': {
			'context': null
		},
		//'actualSize': 576,
		'size': {
			'480': {
				'scr': {
					'w': 500,
					'h': 400
				},
				'cll': {
					'w': 100,
					'h': 100
				}
			},
			'576': {
				'scr': {
					'w': 550,
					'h': 440
				},
				'cll': {
					'w': 110,
					'h': 110
				}
			},
			'720': {
				'scr': {
					'w': 650,
					'h': 520
				},
				'cll': {
					'w': 130,
					'h': 130
				}
			},
			'1080': {
				'scr': {
					'w': 900,
					'h': 720
				},
				'cll': {
					'w': 180,
					'h': 180
				}
			}
		},
		'iItems': {
			'x': 5,
			'y': 4
		},
		'imgs': [
			{
				'num': 0,
				'img': new Image(),
				'url': '0.png'
			},
			{
				'num': 1,
				'img': new Image(),
				'url': '1.png'
			},
			{
				'num': 2,
				'img': new Image(),
				'url': '2.png'
			},
			{
				'num': 3,
				'img': new Image(),
				'url': '3.png'
			},
			{
				'num': 4,
				'img': new Image(),
				'url': '4.png'
			},
			{
				'num': 5,
				'img': new Image(),
				'url': '5.png'
			},
			{
				'num': 6,
				'img': new Image(),
				'url': '6.png'
			},
			{
				'num': 7,
				'img': new Image(),
				'url': '7.png'
			},
			{
				'num': 8,
				'img': new Image(),
				'url': '8.png'
			},
			{
				'num': 9,
				'img': new Image(),
				'url': '9.png'
			}
		],
		'arr': [],
		'position': {
			'old': {
				'x': 0,
				'y': 0
			},
			'current': {
				'x': 0,
				'y': 0
			}
		}
	},
	_GET;
