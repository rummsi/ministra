'use strict';

/**
 * @class DVBList
 * @constructor
 * @author Roman Stoian
 */
function DVBList(parent) {
	// parent constructor
	CScrollList.call(this, parent);

	/**
	 * link to the object for limited scopes
	 * @type {DVBList}
	 */
	var self = this;

	this.data = [];

	/**
	 * link to the BreadCrumb component
	 * @type {CBreadCrumb}
	 */
	this.bcrumb = null;
	this.sbar = null;

	/**
	 * hierarchy change flag: no change
	 * @type {number}
	 */
	this.LEVEL_CHANGE_NONE = 0;

	/**
	 * hierarchy change flag: go level up
	 * @type {number}
	 */
	this.LEVEL_CHANGE_UP = -1;

	/**
	 * hierarchy change flag: go level deeper
	 * @type {number}
	 */
	this.LEVEL_CHANGE_DOWN = 1;


	/**
	 * type filter for file listing
	 * @type Number
	 */
	this.filterType = MEDIA_TYPE_NONE;

	/**
	 * data filter for file listing
	 * @type String
	 */
	this.filterText = '';

	/**
	 * list of all media types on the current level
	 * @type {Array}
	 */
	this.mtypes = [];

	/**
	 * list of media objects data
	 * full chain from the root
	 * @type {[Object]}
	 */
	this.path = [];

	/**
	 * current media object opened
	 * @type {Object}
	 */
	this.parentItem = null;

	this.timer = {};

	this.prevChannel = null;
	this.lastChannel = null;

	/**
	 * list of action mapped to the media types
	 * @type {[Function]}
	 */
	this.openAction = {};

	this.openAction[MEDIA_TYPE_BACK] = function(){
		var st = this.Back();
		return st;
	};

	this.openAction[MEDIA_TYPE_DVB_ROOT] = function () {
		var url = '';
		if(this.parent.scanningInProgress){
			return;
		}
		this.channelStart = -1;
		this.Clear();
		if ( this.filterText) {
			this.Add({name: '..'}, {type: MEDIA_TYPE_BACK});
			this.channelStart++;
		}
		if (this.data.length > 0) {
			for (var i = 0; i < this.data.length; i++) {
				url = 'dvb://' + this.data[i].id;
				this.Add({name: this.data[i].name, number: this.data[i].channel_number, scrambled: this.data[i].scrambled}, {name: this.data[i].name, markable: true, index: i, number: this.data[i].channel_number, type: MEDIA_TYPE_DVB}, {stared: FAVORITES_NEW[ url ] || FAVORITES[ url ] ? true : false});
			}
			if (this.data[0].type === MEDIA_TYPE_DVB) {
				this.parent.domInfoTitle.innerHTML = this.data[0].name + '<br />' + _('Frequency')+ ': ' + this.data[0].frequency;
				this.parent.initEPGNow(0);
				setTimeout(function () {
					MediaPlayer.preparePlayer({
						id : self.data[0].id,
						name : self.data[0].name,
						url : 'dvb://' + self.data[0].id,
						type : self.data[0].type,
						sol : 'dvb'
					}, self.parent, true, false, true);
				}, 5);
			}
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF2, false);
			url = 'dvb://' + this.data[0].id;
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF3add, FAVORITES_NEW[url] || FAVORITES[url] ? true : false);
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF3del, FAVORITES_NEW[url] || FAVORITES[url] ? false : true);
		} else {
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF2, true);
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF3add, true);
			this.parent.BPanel.Hidden(this.parent.BPanel.btnF3del, true);
			this.parent.domInfoTitle.innerHTML = '';
		}
		return this.LEVEL_CHANGE_DOWN;
	};

	this.openAction[MEDIA_TYPE_DVB] = function (data, noPlay) {
		if ((!MediaPlayer.obj || MediaPlayer.obj.id !== this.parentItem.data[data.index].id) && !noPlay) {
			var obj = {
				id : this.parentItem.data[data.index].id,
				name : this.parentItem.data[data.index].name,
				url : 'dvb://' + this.parentItem.data[data.index].id,
				type : this.parentItem.data[data.index].type,
				sol : 'dvb'
			};
			echo(obj);
			MediaPlayer.preparePlayer(obj, this.parent, true, true, true);
		} else {
			MediaPlayer.Show(true, this.parent);
			MediaPlayer.showInfo(true);
			MediaPlayer.timer.showInfo = setTimeout(function () {
				MediaPlayer.showInfo(false);
			}, 3000);
		}
		return this.LEVEL_CHANGE_NONE;
	};
}

// extending
DVBList.prototype = Object.create(CScrollList.prototype);
DVBList.prototype.constructor = DVBList;


/**
 * Setter for linked component
 * @param {CBase} component associated object
 */
DVBList.prototype.SetBreadCrumb = function (component) {
	this.bcrumb = component;
};


/**
 * Setter for linked component
 * @param {CBase} component associated object
 */
DVBList.prototype.SetSearchBar = function (component) {
	this.sbar = component;
};


/**
 * Shows/hides items depending on the given filter string match
 * unmarks all hidden items
 */
DVBList.prototype.Filter = function () {
	// link to the object for limited scopes
	var self = this;
	// check all items
	this.Each(function(item){
		// check file name if regular file
		var text_ok = item.data.type === MEDIA_TYPE_BACK || (item.data.name && item.data.name.toLowerCase().indexOf(self.filterText) !== -1);
		// check file type if regular file
		var type_ok = item.data.type === self.filterType || self.filterType === MEDIA_TYPE_NONE || item.data.type === MEDIA_TYPE_BACK;
		// hide not matching items
		self.Hidden(item, !(text_ok && type_ok));
	});
};

/**
 * Finds the first appropriate item
 * @param {string} value
 * @return {Node}
 */
DVBList.prototype.FirstMatch = function ( value ) {
	// preparing
	var items = this.handleInner.children;  // all list items
	// iterate all items till all items are found
	for ( var i = 0; i < items.length; i++ ) {
		// floating pointer depends on direction
		var item = items[i];
		// check file name if regular file
		if ( item.data.type !== MEDIA_TYPE_BACK && item.data.name && item.data.name.toLowerCase().indexOf(value.toLowerCase()) !== -1 ) {
			return item;
			}
		}
		return null;
};

/**
 * Create new item and put it in the list
 * @param {string} obj item label
 * @param {Object} attrs set of item data parameters
 * @param {Object} states set of additional parameters (stared)
 * @return {Node}
 */
DVBList.prototype.Add = function (obj, attrs, states) {
	var self = this, number;
	if ( this.filterText) { // || this.filterType !== MEDIA_TYPE_NONE
		// check file name if regular file
		var text_ok = attrs.type === MEDIA_TYPE_BACK || (obj.name && obj.name.toLowerCase().indexOf(this.filterText.toLowerCase()) !== -1);
		// check file type if regular file
		var type_ok = attrs.type === this.filterType || this.filterType === MEDIA_TYPE_NONE || attrs.type === MEDIA_TYPE_BACK;
		// hide not matching items
		if ( !(text_ok && type_ok) ) {
			return null;
					}
			}
	if (this.mtypes.indexOf(attrs.type) === -1){
		this.mtypes.push(attrs.type);
			}
	// html prepare
	var body = element('div', {className: 'data'}, obj.name);
	var star = element('div', {className: 'star'});
	if (obj.number) {
		number = element('div', {className: 'number'}, obj.number);
		} else {
		number = element('div', {className: 'number'});
		number.style.background = 'url("' + PATH_IMG_PUBLIC + 'media/type_' + attrs.type + '.png") no-repeat center';
		}
	var scrambled = element('div', {className: obj.scrambled === 'true'? 'scrambled on' : 'scrambled'});
	if (!attrs.name) {
		attrs.name = obj.name;
	}
	// actual filling
	var item = CScrollList.prototype.Add.call(this, [number, body, scrambled, star], {
		star: star,
		data: attrs,
		// handlers
		onclick: function () {
			// open or enter the item
			this.self.Open(this.data);
			return false;
		},
		oncontextmenu: EMULATION ? null : function () {
			// mark/unmark the item
			self.parent.actionF3(false);
			return false;
	}
	});
	// mark as favourite
	if (states && states.stared) {
		item.self.SetStar(item, true);
	}
	return item;
};


/**
 * Set inner item flags and decoration
 * @param {Node} item the element to be processed
 * @param {boolean} state flag of the operation (true if change is made)
 */
DVBList.prototype.SetStar = function (item, state) {
	if (item.stared === state) {
		return;
	}
	this.SetState(item, 'stared', state);
	if (state !== false) {
		item.star.style.background = 'url("' + PATH_IMG_PUBLIC + 'ico_fav_s.png") no-repeat right';
	} else {
		item.star.style.background = 'none';
	}
};


/**
 * Hook method on focus item change
 * @param {Node} item the new focused item
 */
DVBList.prototype.onFocus = function (item) {
	if ( MediaPlayer.ts_inProgress ) {
		if ( MediaPlayer.tsExitCheck( 'focus', item ) ) {
			return true;
		}
	}
	if ( item.data.markable ) {
		this.parent.BPanel.Hidden(this.parent.BPanel.btnF2, false);
	} else {
		this.parent.BPanel.Hidden(this.parent.BPanel.btnF2, true);
	}

	var self = this;
	clearTimeout( this.timer.OnFocusPlay );
	if ( item.data.type === MEDIA_TYPE_DVB ) {
		this.parent.BPanel.Hidden( this.parent.BPanel.btnF3, false );
		if ( !this.states.marked || this.states.marked.length === 0 ) {
			this.parent.BPanel.Hidden( this.parent.BPanel.btnF3add, item.stared );
			this.parent.BPanel.Hidden( this.parent.BPanel.btnF3del, !item.stared );
		}
	} else {
		this.parent.BPanel.Hidden( this.parent.BPanel.btnF3add, true );
		this.parent.BPanel.Hidden( this.parent.BPanel.btnF3del, true );
	}

	this.timer.OnFocusPlay = setTimeout(function () {
		if ( item.data.type === MEDIA_TYPE_BACK ) {
			if(self.filterText){
				self.parent.domInfoTitle.innerHTML = _('Contains the list of items which are corresponding to the given filter request');
			} else {
				self.parent.domInfoTitle.innerHTML = self.parentItem.name?self.parentItem.name:'';
			}
		} else {
			self.parent.domInfoTitle.innerHTML = self.data[item.data.index].name + '<br />' +  _('Frequency')+ ': ' + self.data[item.data.index].frequency;
			self.parent.initEPGNow();
		}
		if ( item.data.type === MEDIA_TYPE_DVB ) {
			MediaPlayer.preparePlayer({
				id : self.data[item.data.index].id,
				name : self.data[item.data.index].name,
				url : 'dvb://' + self.data[item.data.index].id,
				type : self.data[item.data.index].type,
				sol : 'dvb'
			}, self.parent, true, false, true);
			self.prevChannel = self.lastChannel;
			self.lastChannel = self.parentItem.data[item.data.index];
		} else {
			self.lastChannel = null;
			MediaPlayer.end();
		}
	}, 500);
	return false;
};


/**
 * Reset and clear all items
 * This will make the component ready for a new filling.
 */
DVBList.prototype.Clear = function () {
	CScrollList.prototype.Clear.call(this);

	this.filterType = MEDIA_TYPE_NONE;
	this.mtypes = [];
};


/**
 * Move one level up
 */
DVBList.prototype.Back = function () {
	var self = this;
	// there are some levels
	if ( this.path.length > 1 ) {
		// exiting from favs and there are some changes
		// normal exit
		this.path.pop();
		this.lastChannel = null;
		if ( this.bcrumb ) {
			this.bcrumb.Pop();
		}
		// render the previous level
		this.Build(this.path[this.path.length-1]);
		// apply specific button visibility
		setTimeout(function(){
			self.onFocus(self.Current());
		}, 0);
		// go up
		return this.LEVEL_CHANGE_UP;
	}
	// stay here
	return this.LEVEL_CHANGE_NONE;
};

/**
 * Go to channel by number
 * @param {number} number
 */
DVBList.prototype.goToChannel = function ( number ) {
	var numbers = Array.prototype.map.call(this.handleInner.children, function(item){
		return item.data.number;
	});
	var index = numbers.indexOf(number);
	echo(numbers,'numbers '+index);
	if ( index >= 0 ) {
		this.Focused(this.handleInner.children[index], true);
	}
};

/**
 * Enter the item or open it
 * @param {Object} data media item inner data
 */
DVBList.prototype.Open = function (data, noPlay) {
	echo(data, 'DVB OPEN');
	var levelChange = this.Build(data, noPlay);

	// level changed
	if ( levelChange !== this.LEVEL_CHANGE_NONE ) {
		// reset tray filter icon
		if ( this.parent.Tray.iconFilter.parentNode === this.parent.Tray.handleInner ) {
			this.parent.Tray.handleInner.removeChild(this.parent.Tray.iconFilter);
		}
		// and hide at all if not necessary
		this.parent.Tray.Show(globalBuffer.size() > 0, false);
		// particular direction
		if ( levelChange === this.LEVEL_CHANGE_DOWN ) {
			// build breadcrumbs
			if ( this.filterText ) {
				// filter
				if ( this.bcrumb ) {
					this.bcrumb.Push('/', 'media/ico_filter.png', this.filterText);
				}
			} else {
				// default
				// build breadcrumbs
				if ( this.bcrumb ) {
					this.bcrumb.Push('/', 'media/type_'+data.type+'.png', data.name ? data.name : '');
				}
			}
			// save this step
			this.path.push(data);
			// sef focus to the first item
			this.Activate(true);
			if( data.data && data.data.length ){ this.onFocus(this.activeItem); }
		} else {
			// go up
			if ( !this.Reposition(this.parentItem) ){
			this.Activate(true);
			}
		}
		// current level item
		this.parentItem = this.path[this.path.length-1];
	}
	return levelChange;
};


/**
 * Open root, clear all breadcrumbs, search options
 */
DVBList.prototype.Reset = function () {
	this.parentItem = null;
	this.path = [];
	this.Clear();
	// linked components
	if (this.bcrumb){
		this.bcrumb.Reset();
	}
	if (this.sbar){
		this.sbar.Reset();
	}
};

/**
 * Renders the given media item by executing associated action
 * @param {Object} data media item inner data
 */
DVBList.prototype.Build = function (data, noPlay) {
	var levelChange = this.LEVEL_CHANGE_NONE;
	// apply filter parameter from the current node
	this.filterText = data.filterText ? data.filterText : '';
	// get item associated open action and execute
	if ( data && data.type && typeof this.openAction[data.type] === 'function' ) {
		levelChange = this.openAction[data.type].call(this, data, noPlay);
	} else {
		// wrong item type
		new CModalAlert(this.parent, _('Error'), _('Unknown type of selected item'), _('Close'));
	}
	return levelChange;
//	this.filterText = data.filterText ? data.filterText : '';
};

/**
 * Clear the list and fill it again (will try to refocus)
 * @param {boolean} [refocus=true] if true then try to set focus to the previous focused element
 */
DVBList.prototype.Refresh = function (refocus) {
	var data = null;
	if (this.parentItem !== null) {
		this.Build(this.parentItem);
		if (refocus !== false) {
			if (!this.activeItem) {
				this.activeItem = this.FirstMatch();
			}
			if(this.activeItem){
				data = this.activeItem.data;
				this.Reposition(data);
				this.SetPosition(this.activeItem); // focus handle bug fix (even items per page problem)
			}
		}
	}
};

/**
 * Moves the cursor to the given element
 * @param {Object} data
 * @return {boolean} operation status
 */
DVBList.prototype.Reposition = function (data) {
	if ( data ) {
		for (var item, i = 0, l = this.Length(); i < l; i++) {
			item = this.handleInner.children[i];
			// url and type match
			if (data.index === item.data.index) {
				return this.Focused(item, true);
			}
		}
	}
	return false;
};

/**
 * Handle checked state for the given item according to the file type.
 * Mark only items available for marking.
 * @param {Node} item the element to be processed
 * @param {boolean} state flag of the state
 * @return {boolean} operation status
 */
DVBList.prototype.Marked = function (item, state) {
	// item exists and only allowed types
	if (item && item.data && item.data.markable) {
		// parent marking
		return CScrollList.prototype.Marked.call(this, item, state);
	}
	return false;
};


/**
 * Show/hide file items according to the specified filter options
 * @param {string} text filter file name option
 */
DVBList.prototype.SetFilterText = function (text) {
	echo('enter to SetFilterText : ' + this.filterText);
	// set global (case conversion for future string comparison speedup)
	this.filterText = text.toLowerCase();
	// apply filter
	this.Filter();
};


/**
 * Shows/hides items depending on the given filter string match
 * unmarks all hidden items
 */
DVBList.prototype.Filter = function () {
	// link to the object for limited scopes
	var self = this;
	// check all items
	this.Each(function (item) {
		// check file name if regular file
		var text_ok = item.data.type === MEDIA_TYPE_BACK || (item.data.name && item.data.name.toLowerCase().indexOf(self.filterText) !== -1);
		// check file type if regular file
		// hide not matching items
		echo('item.data.name : ' + item.data.name + ' ' + text_ok);
		self.Hidden(item, !text_ok);
	});
};


/**
 * Return all appropriate items available for actions (either marked or current with suitable type)
 * @return {Array} list of found Nodes
 */
DVBList.prototype.ActiveItems = function () {
	// get all marked items
	var items = this.states.marked ? this.states.marked.slice() : [];
	// no marked, check current and its type
	if (items.length === 0 && this.activeItem && this.activeItem.data.markable) {
		items.push(this.activeItem);
	}
	return items;
};
