'use strict';

window.onload = init;
window.onkeydown = key.press;

// prevent default right-click menu only for releases
window.oncontextmenu = EMULATION ? null : function(){return false};

function init () {
	VKBlock = true;

	var loc = window.location.href;
	if ( loc.indexOf('?') >= 0 ) { // referrer в _GET
		var parts = loc.substr(loc.indexOf('?') + 1).split('&'),
			_GET = {};
		for ( var key = 0; key < parts.length; key++ ) {
			var part = parts[key];
			_GET[part.substr(0, part.indexOf('='))] = part.substr(part.indexOf('=') + 1);
		}
		pages.referrer = decodeURIComponent(_GET['referrer']);
	} else {
		pages.referrer = document.referrer;
	}

	document.getElementById('game').width = gs.size[WINDOW_HEIGHT].scr.w;
	document.getElementById('game').height = gs.size[WINDOW_HEIGHT].scr.h;

	window.moveTo(0, 0);
	window.resizeTo(EMULATION ? window.outerWidth : screen.width, EMULATION ? window.outerHeight : screen.height);

	gSTB.EnableServiceButton(true);
	cvDraw.vars.model = gSTB.RDir("Model").trim();
}

function random ( m, n ) {
	m = parseInt(m, 10);
	n = parseInt(n, 10);
	return Math.floor(Math.random() * (n - m + 1)) + m;
}


var cvDraw = {
	"vars": {
		"model": "MAG250",
		"mode": "setNums",
		"tips": true,
		"modeCandidats": true,
		"started": false,
		"gameTime": 0,
		"timer": null,
		"openOne": false,
		"counterSteps": 0,
		"counterGoodSteps": 0,
		"complexity": "easy"
	},
	"start": function () {
		var self = this;

		gs.items = [];
		for ( var y = 0; y < gs.infoItems.y; y++ ) {
			gs.items[y] = [];
			for ( var x = 0; x < gs.infoItems.x; x++ ) {
				gs.items[y][x] = {
					"delCand": [],
					"val": -1,
					"show": false,
					"changeble": false
				};
			}
		}
		var f_elems = [1, 2, 3, 4, 5, 6, 7, 8, 9].shuffle(),
			b1 = [f_elems[0], f_elems[1], f_elems[2]],
			b2 = [f_elems[3], f_elems[4], f_elems[5]],
			b3 = [f_elems[6], f_elems[7], f_elems[8]];
		/* line-1 */
		gs.items[0][0].val = b1[0];
		gs.items[0][1].val = b1[1];
		gs.items[0][2].val = b1[2];
		gs.items[0][3].val = b2[0];
		gs.items[0][4].val = b2[1];
		gs.items[0][5].val = b2[2];
		gs.items[0][6].val = b3[0];
		gs.items[0][7].val = b3[1];
		gs.items[0][8].val = b3[2];
		/* line-2 */
		gs.items[1][0].val = b3[0];
		gs.items[1][1].val = b3[1];
		gs.items[1][2].val = b3[2];
		gs.items[1][3].val = b1[0];
		gs.items[1][4].val = b1[1];
		gs.items[1][5].val = b1[2];
		gs.items[1][6].val = b2[0];
		gs.items[1][7].val = b2[1];
		gs.items[1][8].val = b2[2];
		/* line-3 */
		gs.items[2][0].val = b2[0];
		gs.items[2][1].val = b2[1];
		gs.items[2][2].val = b2[2];
		gs.items[2][3].val = b3[0];
		gs.items[2][4].val = b3[1];
		gs.items[2][5].val = b3[2];
		gs.items[2][6].val = b1[0];
		gs.items[2][7].val = b1[1];
		gs.items[2][8].val = b1[2];
		/* line-4 */
		gs.items[3][0].val = b3[1];
		gs.items[3][1].val = b3[2];
		gs.items[3][2].val = b3[0];
		gs.items[3][3].val = b1[1];
		gs.items[3][4].val = b1[2];
		gs.items[3][5].val = b1[0];
		gs.items[3][6].val = b2[1];
		gs.items[3][7].val = b2[2];
		gs.items[3][8].val = b2[0];
		/* line-5 */
		gs.items[4][0].val = b2[1];
		gs.items[4][1].val = b2[2];
		gs.items[4][2].val = b2[0];
		gs.items[4][3].val = b3[1];
		gs.items[4][4].val = b3[2];
		gs.items[4][5].val = b3[0];
		gs.items[4][6].val = b1[1];
		gs.items[4][7].val = b1[2];
		gs.items[4][8].val = b1[0];
		/* line-6 */
		gs.items[5][0].val = b1[1];
		gs.items[5][1].val = b1[2];
		gs.items[5][2].val = b1[0];
		gs.items[5][3].val = b2[1];
		gs.items[5][4].val = b2[2];
		gs.items[5][5].val = b2[0];
		gs.items[5][6].val = b3[1];
		gs.items[5][7].val = b3[2];
		gs.items[5][8].val = b3[0];
		/* line-7 */
		gs.items[6][0].val = b1[2];
		gs.items[6][1].val = b1[0];
		gs.items[6][2].val = b1[1];
		gs.items[6][3].val = b2[2];
		gs.items[6][4].val = b2[0];
		gs.items[6][5].val = b2[1];
		gs.items[6][6].val = b3[2];
		gs.items[6][7].val = b3[0];
		gs.items[6][8].val = b3[1];
		/* line-8 */
		gs.items[7][0].val = b3[2];
		gs.items[7][1].val = b3[0];
		gs.items[7][2].val = b3[1];
		gs.items[7][3].val = b1[2];
		gs.items[7][4].val = b1[0];
		gs.items[7][5].val = b1[1];
		gs.items[7][6].val = b2[2];
		gs.items[7][7].val = b2[0];
		gs.items[7][8].val = b2[1];
		/* line-9*/
		gs.items[8][0].val = b2[2];
		gs.items[8][1].val = b2[0];
		gs.items[8][2].val = b2[1];
		gs.items[8][3].val = b3[2];
		gs.items[8][4].val = b3[0];
		gs.items[8][5].val = b3[1];
		gs.items[8][6].val = b1[2];
		gs.items[8][7].val = b1[0];
		gs.items[8][8].val = b1[1];


		var
			original_order = [
				[0, 1, 2],
				[3, 4, 5],
				[6, 7, 8]
			],
			tmp_collumns = [[0, 1, 2].shuffle(), [0, 1, 2].shuffle(), [0, 1, 2].shuffle()],
			tmp_collumns_order = [0, 1, 2].shuffle(), // колонки (блоки по 3)
			columns =
				[
					original_order[tmp_collumns_order[0]][tmp_collumns[0][0]],
					original_order[tmp_collumns_order[0]][tmp_collumns[0][1]],
					original_order[tmp_collumns_order[0]][tmp_collumns[0][2]],
					original_order[tmp_collumns_order[1]][tmp_collumns[1][0]],
					original_order[tmp_collumns_order[1]][tmp_collumns[1][1]],
					original_order[tmp_collumns_order[1]][tmp_collumns[1][2]],
					original_order[tmp_collumns_order[2]][tmp_collumns[2][0]],
					original_order[tmp_collumns_order[2]][tmp_collumns[2][1]],
					original_order[tmp_collumns_order[2]][tmp_collumns[2][2]]
				],
			tmp_lines = [[1, 2, 0].shuffle(), [1, 2, 0].shuffle(), [1, 2, 0].shuffle()],
			tmp_lines_order = [1, 2, 0].shuffle(), // сроки (блоки по 3);
			lines =
				[
					original_order[tmp_lines_order[0]][tmp_lines[0][0]],
					original_order[tmp_lines_order[0]][tmp_lines[0][1]],
					original_order[tmp_lines_order[0]][tmp_lines[0][2]],
					original_order[tmp_lines_order[1]][tmp_lines[1][0]],
					original_order[tmp_lines_order[1]][tmp_lines[1][1]],
					original_order[tmp_lines_order[1]][tmp_lines[1][2]],
					original_order[tmp_lines_order[2]][tmp_lines[2][0]],
					original_order[tmp_lines_order[2]][tmp_lines[2][1]],
					original_order[tmp_lines_order[2]][tmp_lines[2][2]]
				];

		echo('\n\noriginal_order:\n[' + original_order.toString() + ']\ncolumns_order:\n[' + columns.toString() + ']\nlines_order:\n[' + lines.toString() + ']\n\n');
		var t_arr = [];
		for ( var y = 0; y < gs.infoItems.y; y++ ) {
			t_arr[y] = [];
			for ( var x = 0; x < gs.infoItems.x; x++ ) {
				t_arr[y][x] = gs.items[lines[y]][columns[x]];//подстановка по индексам исходной матрицы перемешаных индексов столбцов и строк
			}
		}

		gs.items = t_arr;
		t_arr = null;

		var v_v = random(0, gs.complexity[this.vars.complexity].length - 1);

		for ( var i = 0; i < gs.complexity[this.vars.complexity][v_v].length; i++ ) {
			var open = [0, 1, 2, 3, 4, 5, 6, 7, 8].shuffle().slice(0, gs.complexity[this.vars.complexity][v_v][i]);
			//  0 8 5 4
			// i -  номер квадрата и по индексу это кол-во открытых элементов квадрата. // gs.items[8][8] - 9 - gs.complexity[complexity][v_v][i]
			for ( var j = 0; j < open.length; j++ ) {
				gs.items[
					gs.squares[i][open[j]][0]
					][
					gs.squares[i][open[j]][1]
					].show = true;
			}
		}

		for ( var y = 0; y < gs.infoItems.y; y++ ) {
			for ( var x = 0; x < gs.infoItems.x; x++ ) {
				if ( gs.items[y][x].show == false ) {
					gs.items[y][x].val = -1;
					gs.items[y][x].changeble = true;
					gs.items[y][x].flag = false;
				}
			}
		}

		setTimeout(
			function () {
				self.item();
				echo('\n\n\n\n\n\n* * * * * * * * * * * * * * * * * * *  START\n\n\n\n\n\n');
				self.vars.started = true;
				VKBlock = false;
				if ( self.vars.timer != null ) {
					clearInterval(self.vars.timer);
				}
				self.vars.timer = null;
				self.Candidats();

				self.vars.timer = setInterval(
					function () {
						self.vars.gameTime++;
						var mins = Math.floor(self.vars.gameTime / 60),
							secs = (self.vars.gameTime - mins * 60);
						document.getElementById('counter_time').getElementsByClassName('cover')[0].innerHTML = (mins < 10 ? '0' + mins : mins) + ' ' + (secs < 10 ? '0' + secs : secs);
						if ( self.vars.gameTime >= 60 * 60 * 2 ) {
							document.getElementById('finish').style.display = 'block';
							if ( self.vars.timer != null ) {
								clearInterval(self.vars.timer);
							}
							self.vars.timer = null;
							VKBlock = true;
							self.vars.started = false;
						}
					},
					999
				);
				self.fillVariants();
			},
			1
		);
	},
	"item": function () {
		document.getElementById('cursor').style.display = 'block';
		document.getElementById('cursor').style.marginLeft = gs.position.current.x * gs.size[WINDOW_HEIGHT].cll.w + 'px';
		document.getElementById('cursor').style.marginTop = gs.position.current.y * gs.size[WINDOW_HEIGHT].cll.h + 'px';
	},
	"PressOK": function () {
		if ( this.vars.started == false && document.getElementById('finish').style.display == 'block' ) {
			window.location.reload(true);
			return;
		}
		if ( this.vars.started == false ) {
			document.getElementById('finish').style.display = 'none';
			document.getElementById('begin').style.display = 'none';
			this.start();
		}
	},
	//  заполнение всей матрицы
	"fillVariants": function () {
		echo("fillVariants");
		var items = document.getElementById('game').getElementsByClassName('box'),
			y = 0,
			x = 0,
			j;
		for ( var i = 0; i < items.length; i++ ) {
			y = Math.floor(i / gs.infoItems.x);
			x = i % gs.infoItems.x;
			j = (i < 10) ? '0' + i : '' + i;
			if ( gs.items[y][x].val != -1 ) {
				if ( gs.items[y][x].changeble == false ) {
					document.getElementById('p_' + j).innerHTML = "<span>" + gs.items[y][x].val + "</span>";
				} else {
					document.getElementById('p_' + j).innerHTML = "<strong>" + gs.items[y][x].val + "</strong>";
				}
			} else {
				if ( this.vars.tips == true ) {
					var arr = this.getArrToCell(x, y, document.getElementById('p_' + j).parentNode.id),
						_out = '',
						str = [];
					if ( gs.items[y][x].delCand.length > 0 ) {
						echo("x: " + x + ", y: " + y + ", delCand: " + gs.items[y][x].delCand.toString() + "\n" + arr.toString() + "\n" + '* * * * * * * ');
					}
					for ( var m = 0; m < arr.length; m++ ) {
						if ( gs.items[y][x].delCand.length > 0 ) {
							var tmp = false;
							for ( var l = 0; l < gs.items[y][x].delCand.length; l++ ) {
								if ( gs.items[y][x].delCand[l] == arr[m] ) {
									tmp = true;
									break;
								}
							}
							if ( tmp != true ) {
								str.push(arr[m]);
							}
						} else {
							str.push(arr[m]);
						}
					}
					var _s = [1, 2, 3, 4, 5, 6, 7, 8, 9];
					for ( var o = 0; o < 9; o++ ) {
						var tmp = false;
						for ( var h = 0; h < str.length; h++ ) {
							if ( _s[o] == str[h] ) {
								tmp = true;
							}
						}
						if ( tmp == true ) {
							_out += '' + _s[o] + ' ';
						} else {
							_out += "<s>" + _s[o] + "</s> ";
						}
					}
					document.getElementById('p_' + j).innerHTML = _out;
				} else {
					document.getElementById('p_' + j).innerHTML = '';
				}
			}
		}
	},
	"fillOne": function ( x, y, set_num, _x, _y ) {
		var id = gs.infoItems.x * _y + _x,
			flag;
		id = (id < 10) ? '0' + id : '' + id;
		var arr = this.getArrToCell(_x, _y, document.getElementById('p_' + id).parentNode.id); // возможные варианты
		if ( _x == x && _y == y ) {
			for ( var t = 0; t < arr.length; t++ ) {
				if ( arr[t] == set_num ) {
					gs.items[_y][_x].flag = true;
				}
			}
		} else {
			if ( gs.items[_y][_x].val != -1 ) {
				if ( gs.items[_y][_x].changeble == true ) {
					document.getElementById('p_' + id).innerHTML = "<strong>" + gs.items[_y][_x].val + "</strong>";
				} else {
					document.getElementById('p_' + id).innerHTML = "<span>" + gs.items[_y][_x].val + "</span>";
				}
			} else {
				if ( this.vars.tips == true ) {
					var _out = '',
						_s = [1, 2, 3, 4, 5, 6, 7, 8, 9];
					for ( var o = 0; o < _s.length; o++ ) {
						var tmp = false;
						for ( var h = 0; h < arr.length; h++ ) {
							if ( _s[o] == arr[h] ) {
								tmp = true;
							}
						}
						if ( tmp == true ) {
							if ( gs.items[y][x].flag == true && set_num == _s[o] ) {
								_out += "<s>" + _s[o] + "</s> ";
								continue;
							}
							_out += '' + _s[o] + ' ';
						} else {
							_out += "<s>" + _s[o] + "</s> ";
						}
					}
					document.getElementById('p_' + id).innerHTML = _out;
				} else {
					document.getElementById('p_' + id).innerHTML = '';
				}
			}
		}
	},
	// заполняет клетку кандидатами
	"fillCellCand": function ( x, y ) {
		echo('fillCellCand');
		var id = gs.infoItems.x * y + x;
		id = (id < 10) ? '0' + id : '' + id;
		var arr = this.getArrToCell(x, y, document.getElementById('p_' + id).parentNode.id); // возможные варианты
		if ( gs.items[y][x].val == -1 ) {
			if ( this.vars.tips == true ) {
				var _out = '',
					_s = [1, 2, 3, 4, 5, 6, 7, 8, 9];
				for ( var o = 0; o < _s.length; o++ ) {
					var tmp = false;
					for ( var h = 0; h < arr.length; h++ ) {
						if ( _s[o] == arr[h] ) {
							tmp = true;
							break;
						}
					}
					echo(gs.items[y][x].delCand.toString());
					for ( var l = 0; l < gs.items[y][x].delCand.length; l++ ) {
						if ( gs.items[y][x].delCand[l] == _s[o] ) {
							tmp = false;
							break;
						}
					}

					if ( tmp == true ) {
						_out += '' + _s[o] + ' ';
					} else {
						_out += "<s>" + _s[o] + "</s> ";
					}
				}
				document.getElementById('p_' + id).innerHTML = _out;
			} else {
				document.getElementById('p_' + id).innerHTML = '';
			}
		}
	},
	//заполняет строку, столбец и содержащий квадрат
	"fillVariantsXY": function ( x, y, set_num ) {
		gs.items[y][x].val = -1;
		var _x, _y, id;
		echo("fillVariantsXY");
		this.fillOne(x, y, set_num, x, y);

		for ( _x = 0; _x < gs.infoItems.x; _x++ ) {
			this.fillOne(x, y, set_num, _x, y);
		}
		for ( _y = 0; _y < gs.infoItems.y; _y++ ) {
			this.fillOne(x, y, set_num, x, _y);
		}
		if ( this.vars.tips == true && gs.items[y][x].flag == true ) {
			id = gs.infoItems.x * y + x;
			id = (id < 10) ? '0' + id : '' + id;
			var v_block = document.getElementById(document.getElementById('p_' + id).parentNode.id).getElementsByClassName('box');
			for ( var u = 0; u < v_block.length; u++ ) {
				var d = parseInt(v_block[u].id.substr(2, 2), 10);
				_x = d % gs.infoItems.x;
				_y = Math.floor(d / gs.infoItems.x);
				this.fillOne(x, y, set_num, _x, _y);
			}
		}
		if ( set_num != -1 ) {
			id = gs.infoItems.x * y + x;
			id = (id < 10) ? '0' + id : '' + id;
			if ( gs.items[y][x].flag == true ) {
				gs.items[y][x].val = set_num;
				document.getElementById('p_' + id).innerHTML = "<strong>" + gs.items[y][x].val + "</strong>";
			} else {
				document.getElementById('errorCell').style.margin = y * gs.size[WINDOW_HEIGHT].cll.h + 'px 0 0 ' + x * gs.size[WINDOW_HEIGHT].cll.w + 'px';
				document.getElementById('errorCell').style.display = 'block';
				setTimeout(
					function () {
						document.getElementById('errorCell').style.display = 'none';
					},
					1250
				);
			}
		} else {
			gs.items[y][x].val = set_num;
			var id = gs.infoItems.x * y + x;
			id = (id < 10) ? '0' + id : '' + id;
			if ( this.vars.tips == true ) {
				var arr = this.getArrToCell(x, y, document.getElementById('p_' + id).parentNode.id);
				var _out = '',
					_s = [1, 2, 3, 4, 5, 6, 7, 8, 9];
				for ( var o = 0; o < _s.length; o++ ) {
					var tmp = false;
					for ( var h = 0; h < arr.length; h++ ) {
						if ( _s[o] == arr[h] ) {
							tmp = true;
						}
					}
					if ( tmp == true ) {
						if ( gs.items[y][x].flag == true && set_num == _s[o] ) {
							_out += "<s>" + _s[o] + "</s> ";
							continue;
						}
						_out += '' + _s[o] + ' ';
					} else {
						_out += "<s>" + _s[o] + "</s> ";
					}
				}
				document.getElementById('p_' + id).innerHTML = _out;
			} else {
				document.getElementById('p_' + id).innerHTML = '';
			}
		}
	},
	"getArrToCell": function ( x, y, pId ) {
		var arr = [1, 2, 3, 4, 5, 6, 7, 8, 9],
			n_arr = [],
			v_block,
			v_break = false,
			s = -1;
		for ( var i = 0; i < arr.length; i++ ) {
			v_break = false;

			for ( var _x = 0; _x < gs.infoItems.x; _x++ ) {
				if ( gs.items[y][_x].val == arr[i] ) {
					v_break = true;
					break;
				}
			}
			for ( var _y = 0; _y < gs.infoItems.x; _y++ ) {
				if ( gs.items[_y][x].val == arr[i] ) {
					v_break = true;
					break;
				}
			}
			v_block = document.getElementById(pId).getElementsByClassName('box');
			for ( var j = 0; j < v_block.length; j++ ) {
				s = parseInt(v_block[j].id.substr(2, 2), 10);
				if ( gs.items[Math.floor(s / gs.infoItems.x)][s % gs.infoItems.x].val == arr[i] ) {
					v_break = true;
					break;
				}
			}
			if ( v_break == false ) {
				n_arr.push(arr[i]);
			}
		}
		return n_arr;
	},
	"PressNUM": function ( code ) {
		var num = -1;
		switch ( code ) {
			case KEYS.NUM1:
				num = 1;
				break;
			case KEYS.NUM2:
				num = 2;
				break;
			case KEYS.NUM3:
				num = 3;
				break;
			case KEYS.NUM4:
				num = 4;
				break;
			case KEYS.NUM5:
				num = 5;
				break;
			case KEYS.NUM6:
				num = 6;
				break;
			case KEYS.NUM7:
				num = 7;
				break;
			case KEYS.NUM8:
				num = 8;
				break;
			case KEYS.NUM9:
				num = 9;
				break;
		}
		if ( this.vars.modeCandidats == true ) {
			var exist = false;
			for ( var i in gs.items[gs.position.current.y][gs.position.current.x].delCand ) {
				if ( gs.items[gs.position.current.y][gs.position.current.x].delCand[i] == num ) {
					gs.items[gs.position.current.y][gs.position.current.x].delCand.splice(i, 1);
					exist = true;
				}
			}
			if ( exist == false ) {
				gs.items[gs.position.current.y][gs.position.current.x].delCand.push(num);
			}
			this.fillCellCand(gs.position.current.x, gs.position.current.y);
		} else {
			if ( gs.items[gs.position.current.y][gs.position.current.x].changeble == true ) {
				var id = gs.infoItems.x * gs.position.current.y + gs.position.current.x;
				id = (id < 10) ? '0' + id : '' + id;
				var arr = this.getArrToCell(gs.position.current.x, gs.position.current.y, document.getElementById('p_' + id).parentNode.id), // возможные варианты
					isset = false;

				for ( var u = 0; u < arr.length; u++ ) {
					if ( arr[u] == num ) {
						isset = true;
					}
				}
				if ( isset == true ) {
					this.fillVariantsXY(gs.position.current.x, gs.position.current.y, num);
				} else {
					document.getElementById('errorCell').style.margin = gs.position.current.y * gs.size[WINDOW_HEIGHT].cll.h + 'px 0 0 ' + gs.position.current.x * gs.size[WINDOW_HEIGHT].cll.w + 'px';
					document.getElementById('errorCell').style.display = 'block';
					setTimeout(
						function () {
							document.getElementById('errorCell').style.display = 'none';
						},
						1250
					);
				}

			}
			var counter = 0, iteraciy = 0;
			for ( var y = 0; y < gs.infoItems.y; y++ ) {
				for ( var x = 0; x < gs.infoItems.x; x++ ) {
					iteraciy++;
					if ( gs.items[y][x].val != -1 ) {
						counter++;
					}
				}
			}
			echo('\nNow ' + counter + ' cells filled.\niteraciy: ' + iteraciy + '\n');
			if ( iteraciy == counter ) {
				document.getElementById('finish').style.display = 'block';
				if ( this.vars.timer != null ) {
					clearInterval(this.vars.timer);
				}
				this.vars.timer = null;
				VKBlock = true;
				this.vars.started = false;
			}
		}
	},
	"Erase": function () {
		if ( gs.items[gs.position.current.y][gs.position.current.x].changeble == true ) {
			gs.items[gs.position.current.y][gs.position.current.x].val = -1;
			this.fillVariantsXY(gs.position.current.x, gs.position.current.y, -1);
		}
	},
	"Tips": function () {
		if ( this.vars.tips == true ) {
			this.vars.tips = false;
			this.fillVariants();
			document.getElementById('showTips').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/btn_red50.png)';
			document.getElementById('showCandidats').style.display = 'none';
			echo('tips: OFF');
		} else {
			this.vars.tips = true;
			this.fillVariants();
			document.getElementById('showTips').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/btn_red.png)';
			document.getElementById('showCandidats').style.display = 'block';
			document.getElementById('showCandidats').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/btn_blue.png)';
			echo('tips: ON');
		}
	},
	"Candidats": function () {
		if ( this.vars.tips == true ) {
			if ( this.vars.modeCandidats == true ) {
				this.vars.modeCandidats = false;
				document.getElementById('showCandidats').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/btn_blue50.png)';
				echo('candidats: OFF');
				document.getElementById('cursor').className = '';
			} else {
				this.vars.modeCandidats = true;
				document.getElementById('showCandidats').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/btn_blue.png)';
				echo('candidats: ON');
				document.getElementById('cursor').className = 'del';
			}
		}
	},
	"startCursor": function ( direction ) {
		switch ( this.vars.complexity ) {
			case "easy":
				document.getElementById('c_easy').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/easy0.png)';
				if ( direction == 1 ) {
					this.vars.complexity = "hard";
					document.getElementById('c_hard').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/hard1.png)';
				} else {
					this.vars.complexity = "normal";
					document.getElementById('c_normal').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/normal1.png)';
				}
				break;
			case "normal":
				document.getElementById('c_normal').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/normal0.png)';
				if ( direction == 1 ) {
					this.vars.complexity = "easy";
					document.getElementById('c_easy').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/easy1.png)';
				} else {
					this.vars.complexity = "hard";
					document.getElementById('c_hard').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/hard1.png)';
				}
				break;
			case "hard":
				document.getElementById('c_hard').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/hard0.png)';
				if ( direction == 1 ) {
					this.vars.complexity = "normal";
					document.getElementById('c_normal').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/normal1.png)';
				} else {
					this.vars.complexity = "easy";
					document.getElementById('c_easy').style.backgroundImage = 'url(' + PATH_IMG_PUBLIC + 'games/sudoku/start/easy1.png)';
				}
				break;
		}
	}
};
