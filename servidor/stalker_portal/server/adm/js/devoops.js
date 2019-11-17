//
//    Main script of DevOOPS v1.0 Bootstrap Theme
//
"use strict";

/*-------------------------------------------
 Dynamically load plugin scripts
 ---------------------------------------------*/
//
//  Dynamically load jQuery Select2 plugin
//  homepage: https://github.com/ivaynberg/select2  v3.4.5  license - GPL2
//


/* $('.dropdown-toggle').click(function() {
        var dropdownList = $('.dropdown-menu');
        var dropdownOffset = $(this).parent('div').position();
       console.log(dropdownOffset.top);

        var dropdownWidth = dropdownList.height();
		console.log(dropdownWidth);
        var docWidth = $('#ajax-content').height();
        console.log(docWidth);

        var isDropdownVisible = (dropdownOffset.top + dropdownWidth + dropdownWidth + dropdownWidth <= docWidth);

        if (!isDropdownVisible) {
          $('.dropdown-toggle').parent('div').addClass('dropup');
        } else {
           $('.dropdown-toggle').parent('div').removeClass('dropup');
        }
    });*/

if (typeof (load_page_data) !== 'function') {
    var load_page_data = function(){};
}

$(document).on("click", "div.dropdown-menu *", function(e){
    e.stopPropagation();
});

$("#ui-datepicker-div").css("z-index:5050;");

$('#attribute_set').removeClass('dropup');
$('#status').removeClass('dropup');
$('.filter').removeClass('dropup');

for (var f = document.forms, i = f.length; i--;)f[i].setAttribute("novalidate", i)

function LoadSelect2Script(callback) {
    if (callback && typeof (callback) === "function") {

        $("select", document).on('select2:open', function(e){
            if (typeof($(this).select2) === 'function') {
                var selectID = this.id, nameSpace = 'select2.select2-' + selectID;
                var scrollEvent = 'scroll.' + nameSpace;
                var resizeEvent = 'resize.' + nameSpace;
                var orientationEvent = 'orientationchange.' + nameSpace;
                $(window).on(scrollEvent + ' ' + resizeEvent + ' ' + orientationEvent,
                    function (e) {
                        $("#" + selectID).select2("close");
                    });
            }
        });

        callback();
    }
}

//
//  Dynamically load DataTables plugin
//  homepage: http://datatables.net v1.9.4 license - GPL or BSD
//
function LoadDataTablesScripts(callback) {

    $.fn.dataTableExt.oApi.fnDataUpdate = function ( oSettings, nRowObject, iRowIndex ){
        $(nRowObject).find("TD").each( function(i) {
            var iColIndex = oSettings.oApi._fnVisibleToColumnIndex( oSettings, i );
            oSettings.oApi._fnSetCellData( oSettings, iRowIndex, iColIndex, $(this).html() );
        } );
    };

    $.fn.dataTableExt.aoFeatures.push( {

        // dataTable add update button
        "fnInit": function( oDTSettings ) {

                    var filterContainer = $(oDTSettings.nTableWrapper).find("#" + oDTSettings.sTableId + '_filter');

                    if (filterContainer.length && filterContainer.next("button.dataTables_ajax_update_button").length == 0) {

                        var newTableId = "dataTables_ajax_update_button_" + $.random(1000000);
                        filterContainer.after('<button id="'+newTableId+'" class="btn dataTables_ajax_update_button" type="button"><i class="fa fa-refresh"></i></button>');

                        $(document).on("click", "#" + newTableId, function(){
                            if (oDTSettings.aoServerParams.length) {
                                $("#" + oDTSettings.sTableId).DataTable().ajax.reload();
                            } else if (typeof (updateTableData) == 'function') {
                                updateTableData({tableId: oDTSettings.sTableId});
                            }
                        });
                    }
                },
        "cFeature": "A"
    } );

    $.fn.dataTableExt.aoFeatures.push( {
        // dataTable colVis dropdown
        "cFeature": "V",
        "fnInit": function( oDTSettings ) {
                    var chboxAll = $("#attribute_set li[data-filter='all'] input[type='checkbox']");
                    var operations = $("#attribute_set li[data-filter='operations'] input[type='checkbox']");

                    var chbox = $("#attribute_set input[type='checkbox']").not(chboxAll);
                    var chboxSelected = chbox.not(operations).filter(":checked");

                    // if last column left lock the checkbox
                    if ( chboxSelected.length == 1 ) {
                        chboxSelected.prop("disabled", true);
                        chboxSelected.closest(".checkbox").addClass("disabled");
                    }
                    // initializing checkbox All:
                    chboxSelected = chboxSelected.add( operations.filter(":checked") );
                    chboxAll.prop('checked', chboxSelected.length==chbox.length );
                    if( chboxSelected.length == chbox.length ){
                        chboxAll.prop("disabled", true);
                        chboxAll.prop("indeterminate", false);
                        chboxAll.closest(".checkbox").addClass("disabled");
                    } else {
                        chboxAll.prop("disabled", false);
                        chboxAll.prop("indeterminate", true); // show as indeterminate state
                        chboxAll.closest(".checkbox").addClass("indeterminated");
                    }

                    //console.log( oDTSettings );
                    return $("#attribute_set");
                }
    } );

    if (typeof (window.stateSaveReject) === 'undefined' || !window.stateSaveReject) {
        $.fn.dataTable.defaults.stateSave = true;
        $.fn.dataTable.defaults.stateDuration = 0;
        $.fn.dataTable.defaults.stateSaveCallback = function(settings,data) {
            localStorage.setItem( getDTPage() + 'DataTables_' + settings.sInstance, JSON.stringify(getDTStateParams(data)) )
        };
        $.fn.dataTable.defaults.stateLoadCallback = function(settings) {
            return getDTStateParams(JSON.parse( localStorage.getItem( getDTPage() + 'DataTables_' + settings.sInstance )));
        };
    }

    if (callback && typeof (callback) === "function") {
        callback();
    }
}

function getDTPage(){
    var pageParams = window.location.href.split("?");
    var page = pageParams[0].split("/");
    return (page[page.length - 1] ? page[page.length - 1] : page[page.length - 2]).replace(/[^\w]/ig, '');
}

function getDTStateParams(data){
    var localData = data || {};
    var pageParams = window.location.href.split("?");
    var filters = $.parseParams(pageParams[1] || '');
    var chkFilters = 0;
        $.each(localData, function(key){
            if (key.search('filters') !== -1 && typeof (filters[key]) !== 'undefined') {
                chkFilters += (filters[key] === data[key] ? 1: 0);
            }
        });

    if (chkFilters !== Object.keys(filters).length) {
        $.extend(localData, filters);
        localData.start = 0;
    }

    return localData;
}

//
//  Dynamically load Colorbox Gallery plugin
//  homepage: http://www.jacklmoore.com/colorbox v1.6.4 License - MIT
//
function LoadColorboxScript(callback) {
    if (!$.fn.colorbox) {
        $.getScript('plugins/colorbox/jquery.colorbox.js', callback);
    }
    else {
        if (callback && typeof (callback) === "function") {
            callback();
        }
    }
}

/*-------------------------------------------
 Main scripts used by theme
 ---------------------------------------------*/
//
//  Function set min-height of window (required for this theme)
//
function SetMinBlockHeight(elem) {
    elem.css('min-height', window.innerHeight - 49)
}
//
//  Beauty tables plugin (navigation in tables with inputs in cell)
//  Created by DevOOPS.
//
(function ($) {
    $.fn.beautyTables = function () {
        var table = this;
        var string_fill = false;
        this.on('keydown', function (event) {
            var target = event.target;
            var tr = $(target).closest("tr");
            var col = $(target).closest("td");
            if (target.tagName.toUpperCase() == 'INPUT') {
                if (event.shiftKey === true) {
                    switch (event.keyCode) {
                        case 37: // left arrow
                            col.prev().children("input[type=text]").focus();
                            break;
                        case 39: // right arrow
                            col.next().children("input[type=text]").focus();
                            break;
                        case 40: // down arrow
                            if (string_fill == false) {
                                tr.next().find('td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                        case 38: // up arrow
                            if (string_fill == false) {
                                tr.prev().find('td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                    }
                }
                if (event.ctrlKey === true) {
                    switch (event.keyCode) {
                        case 37: // left arrow
                            tr.find('td:eq(1)').find("input[type=text]").focus();
                            break;
                        case 39: // right arrow
                            tr.find('td:last-child').find("input[type=text]").focus();
                            break;
                        case 40: // down arrow
                            if (string_fill == false) {
                                table.find('tr:last-child td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                        case 38: // up arrow
                            if (string_fill == false) {
                                table.find('tr:eq(1) td:eq(' + col.index() + ') input[type=text]').focus();
                            }
                            break;
                    }
                }
                if (event.keyCode == 13 || event.keyCode == 9) {
                    event.preventDefault();
                    col.next().find("input[type=text]").focus();
                }
                if (string_fill == false) {
                    if (event.keyCode == 34) {
                        event.preventDefault();
                        table.find('tr:last-child td:last-child').find("input[type=text]").focus();
                    }
                    if (event.keyCode == 33) {
                        event.preventDefault();
                        table.find('tr:eq(1) td:eq(1)').find("input[type=text]").focus();
                    }
                }
            }
        });
        table.find("input[type=text]").each(function () {
            $(this).on('blur', function (event) {
                var target = event.target;
                var col = $(target).parents("td");
                if (table.find("input[name=string-fill]").prop("checked") == true) {
                    col.nextAll().find("input[type=text]").each(function () {
                        $(this).val($(target).val());
                    });
                }
            });
        })
    };
})(jQuery);
//
// Beauty Hover Plugin (backlight row and col when cell in mouseover)
//
//
(function ($) {
    $.fn.beautyHover = function () {
        var table = this;
        table.on('mouseover', 'td', function () {
            var idx = $(this).index();
            var rows = $(this).closest('table').find('tr');
            rows.each(function () {
                $(this).find('td:eq(' + idx + ')').addClass('beauty-hover');
            });
        })
                .on('mouseleave', 'td', function (e) {
                    var idx = $(this).index();
                    var rows = $(this).closest('table').find('tr');
                    rows.each(function () {
                        $(this).find('td:eq(' + idx + ')').removeClass('beauty-hover');
                    });
                });
    };
})(jQuery);

/*-------------------------------------------
 Function for Form Layout page (form layouts.html)
 ---------------------------------------------*/
//
// Example form validator function
//
//
//  Helper for correct size of Messages page
//
function MessagesMenuWidth(){
	var W = window.innerWidth;
	var W_menu = $('#sidebar-left').outerWidth();
	var w_messages = (W-W_menu)*16.666666666666664/100;
	$('#messages-menu').width(w_messages);
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//
//      MAIN DOCUMENT READY SCRIPT OF DEVOOPS THEME
//
//      In this script main logic of theme
//
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
$(document).ready(function () {
    var $body = $("body");
    $('#modalbox').appendTo($body);
    $('#modalbox_ad').appendTo($body);

    $("#modalbox, #modalbox_clone, #modalbox_ad").on("show", function (e) {
        if (e.currentTarget != e.target) {
            return;
        }
        $("body").addClass("modal-open");
    }).on("hide", function (e) {
        // prevent execution if it's bubble come up event
        if (e.currentTarget != e.target) {
            return;
        }

        var $modals = $("#modalbox:visible, #modalbox_clone:visible, #modalbox_ad:visible");
        if( $modals.length == 1 ){
            $("body").removeClass("modal-open");
        }
    });

    $( document ).ajaxComplete(function( event,request, settings ) {
        if (typeof(request.responseJSON) !== 'undefined' && typeof(request.responseJSON.error) !== 'undefined' && request.responseJSON.error == 'Need authorization') {
            window.location.reload(true);
            console.log(request.responseJSON.error);
        }
    });

    $( document ).ajaxError(function( event,request, settings ) {
        if (typeof(request.responseJSON) !== 'undefined' && typeof(request.responseJSON.error) !== 'undefined' && request.responseJSON.error == 'Need authorization') {
            window.location.reload(true);
            console.log(request.responseJSON.error);
        } else if(typeof(request.responseJSON) == 'undefined' && request.status && request.status >= 400 && request.statusText) {
            notty('Server response: ' + request.status + ' - ' + request.statusText, false);
        }
    });

    if ($("#allowed_locales").length > 0) {
        $("#allowed_locales").on('click', 'a', function(e){
            e.stopPropagation();
            e.preventDefault();
            $.cookies.set('language', $(this).data('locale'), {expiresAt: new Date( 2037, 1, 1 )});
            localStorage.removeItem('opinionCheck');
            window.location.reload(true);
            return false;
        });
    }

    $('.show-sidebar').on('click', function (e) {
        e.preventDefault();
        $('div#main').toggleClass('sidebar-show');
        setTimeout(MessagesMenuWidth, 250);
    });

    $('.preloader').hide();

    // Humburger button (collapse menu)
    $('.menu-btn').click( function(){
        //when you set up menu state not from control classes, but CSS @media rules
        fixLeftSidebarState();

        $("body")
            .toggleClass('sidebar-expanded')
            .toggleClass('sidebar-collapsed');

        saveMenuSidebarState();
        setMenuScrollButtons();

        setTimeout( adjustFixedHeader, 210 ); // adjust FixedHeader after animation is finished
    });

    $('.main-menu').on('click', 'a', function (e) {

        if ($(this).next().length == 0) {
            return true;
        }
        e.stopPropagation();
        e.preventDefault();

        // Menu slide out
        slideoutMenuSidebar();

        var target = $(this);
        var current = $(this).next();
        var another_items = $('.main-menu li').not(target.parents('li'));

        // click from Menu Narrow state
        if( $("#sidebar-left").width() <= 60 ){
            if ( target.hasClass('opened') ){
                return;
            } else {
                another_items.find("ul.dropdown-menu").hide();
                another_items.find(".opened").removeClass('opened');
                target.addClass('opened');
                current.slideDown('fast');
                return;
            }
        }

        if ( target.hasClass('dropdown-toggle') ){

            if (current.is(':visible')) {
                target.removeClass('opened');
                target.closest('li.dropdown').find("ul.dropdown-menu").slideUp('fast');
            } else {
                another_items.find("ul.dropdown-menu").slideUp('fast');
                another_items.find(".opened").removeClass('opened');
                target.addClass('opened');
                current.slideDown('fast');
            }
        }
		/*
        var parents = $(this).parents('li');
        var li = $(this).closest('li.dropdown');

        var another_items = $('.main-menu li').not(parents);
        another_items.find('a').removeClass('active');
        another_items.find('a').removeClass('active-parent');

        if ($(this).hasClass('dropdown-toggle') || $(this).closest('li').find('ul').length == 0) {
            $(this).addClass('active-parent');
            var current = $(this).next();
            if (current.is(':visible')) {
                li.find("ul.dropdown-menu").slideUp('fast');
                li.find("ul.dropdown-menu a").removeClass('active')
            } else {
                another_items.find("ul.dropdown-menu").slideUp('fast');
                current.slideDown('fast');
            }
        } else {
            if (li.find('a.dropdown-toggle').hasClass('active-parent')) {
                var pre = $(this).closest('ul.dropdown-menu');
                pre.find("li.dropdown").not($(this).closest('li')).find('ul.dropdown-menu').slideUp('fast');
            }
        }

        if ($(this).hasClass('active') == false) {
            $(this).parents("ul.dropdown-menu").find('a').removeClass('active');
            $(this).addClass('active')
        }
        */

        if ($(this).attr('href') == '#') {
            e.preventDefault();
        }
    });


    /**
    *   Slim menu scroll setup
    **/
    // http://www.simonbattersby.com/demos/vertical_scrollbar_demo_14_scroll_buttons.htm
    //setSlider($('#scroll-pane'));

    $('#sidebar-left .scroll-btn-up').mousedown(function(event) {
        if(event.preventDefault) event.preventDefault();

        var intervalId = setInterval( scrollUp, 30);
        $(this).bind('mouseup mouseleave', function() {
            clearInterval(intervalId);
        });
    });
    $('#sidebar-left .scroll-btn-down').mousedown(function(event) {
        if(event.preventDefault) event.preventDefault();

        var intervalId = setInterval( scrollDown, 30);
        $(this).bind('mouseup mouseleave', function() {
            clearInterval(intervalId);
        });
    });

    setMenuScrollButtons();

    $(window).on('resize', setMenuScrollButtons );

    /**
    * end main menu scroll
    **/

    $('#wrapper').css({minHeight: heightCalculate()})
            .on('click', '.expand-link', function (e) {
                var body = $('body');
                e.preventDefault();
                var box = $(this).closest('div.box');
                var button = $(this).find('i');
                button.toggleClass('fa-expand').toggleClass('fa-compress');
                box.toggleClass('expanded');
                body.toggleClass('body-expanded');
                var timeout = 0;
                if (body.hasClass('body-expanded')) {
                    timeout = 100;
                }
                setTimeout(function () {
                    box.toggleClass('expanded-padding');
                }, timeout);
                setTimeout(function () {
                    box.resize();
                    box.find('[id^=map-]').resize();
                }, timeout + 50);
            })
            .on('click', '.close-link', function (e) {
                e.preventDefault();
                var content = $(this).closest('div.box');
                content.remove();
            });

    $('body').on('click', 'a.close-link', function (e) {
        e.preventDefault();
//        CloseModalBox();
    });

    $(document).on("click mousedown mouseup", "#attribute_set .checkbox", function (e) {
        e.stopPropagation();
    });

    // some collapse-link are in modals, that append to body now.
    $(document).on('click', '.collapse-link', function (e) {
                e.preventDefault();
                var box = $(this).closest('div.box');
                var button = $(this).find('i.fa-chevron-up, i.fa-chevron-down');
                var content = box.children('div.box-content');

                content.slideToggle('fast');
                button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
                setTimeout(function () {
                    box.resize();
                    box.find('[id^=map-]').resize();
                }, 50);
            })

    // moved from dataTables.bootstrap.js
    $(document).on("input keypress keyup", ".dataTables_filter input", function (e) {
        this.value = this.value.replace(/^\s+/ig, '').replace(/\s{2}/ig, ' ');
    });

    // click on column filter All

    $(document).on('click', "#attribute_set li[data-filter='all'] .checkbox", function (e) {

        var checkboxAll = $(this).find("input[type='checkbox']");

        if( !checkboxAll.prop('disabled') ) {

            var chboxAll = $("#attribute_set li[data-filter='all'] input[type='checkbox']");
            var operations = $(this).closest("#attribute_set").find("li[data-filter='operations'] input[type='checkbox']");
            var chbox = $(this).closest("#attribute_set").find("input[type='checkbox']").not(chboxAll);

            var is_checked = !checkboxAll.is(":checked");
            //is_checked = true;

            checkboxAll.prop('checked', is_checked);
            checkboxAll.prop('disabled', true).prop("indeterminate", false);
            checkboxAll.closest(".checkbox").addClass("disabled");
            checkboxAll.closest(".checkbox").removeClass("indeterminated");

            chbox.prop("disabled", false);
            chbox.closest(".checkbox").removeClass("disabled");

            var sendData = {};

            chbox.each(function(index){
                $(this).prop('checked', is_checked);
                var i = $("table[id^='datatable']").DataTable().column( $(this).val() +":name" ).index();
                $("table[id^='datatable']").dataTable().fnSetColumnVis( i != undefined ? i : index-1, is_checked, false );
                sendData[$(this).val()] = is_checked;
            });

            var table = $('#datatable-1').DataTable();
            if ( table.search() ) {
                table.draw("page");
            }

            setDropdownAttribute(sendData);
        }

        e.stopPropagation();
        return false;
    });


    // click on column filter other checkboxes

    $(document).on('change', "#attribute_set li:not([data-filter='all']) input[type='checkbox']", function (e) {

        var chboxAll = $(this).closest("#attribute_set").find("li[data-filter='all'] input[type='checkbox']");
        var operations = $(this).closest("#attribute_set").find("li[data-filter='operations'] input[type='checkbox']");

        var chbox = $(this).closest("#attribute_set").find("input[type='checkbox']").not(chboxAll);
        var chboxSelected = chbox.not(operations).filter(":checked");

        // make last chbox disabled
        if ( chboxSelected.length == 1 ) {
            chboxSelected.prop("disabled", true);
            chboxSelected.closest(".checkbox").addClass("disabled");
        }
        if ( chboxSelected.length > 1 ) {
            chbox.prop("disabled", false);
            chbox.closest(".checkbox").removeClass("disabled");
        }

        // set chboxAll
        chboxSelected = chboxSelected.add( operations.filter(":checked") );
        chboxAll.prop('checked', chboxSelected.length==chbox.length );
        if( chboxSelected.length == chbox.length ){
            chboxAll.prop("disabled", true);
            chboxAll.prop("indeterminate", false);
            chboxAll.closest(".checkbox").addClass("disabled");
            chboxAll.closest(".checkbox").removeClass("indeterminated");
        } else {
            chboxAll.prop("disabled", false);
            chboxAll.prop("indeterminate", true); // show as indeterminate state
            chboxAll.closest(".checkbox").removeClass("disabled");
            chboxAll.closest(".checkbox").addClass("indeterminated");
        }

        $("#attribute_set input[type='checkbox']").each(function(index){
            var i = $("table[id^='datatable']").DataTable().column(  $(this).val() +":name" ).index();
            $("table[id^='datatable']").dataTable().fnSetColumnVis( i != undefined ? i : index - 1, $(this).prop('checked'), false );
        });

        var sendData = {};
        chbox.each(function(){
            sendData[$(this).val()] = $(this).prop('checked');
        });

        var table = $('#datatable-1').DataTable();
        if ( table.search() ) {
            table.draw("page");
        }

        setDropdownAttribute(sendData);
    });

    $(document).on("click mousedown mouseup", "div.attribute_set[id!='attribute_set']  .checkbox", function (e) {
        e.stopPropagation();
    });

    $(document).on('click', "div.attribute_set[id!='attribute_set'] li:first-of-type label", function (e) {
        var _input = $(this).find("input[type='checkbox']");
        _input.prop('checked', !_input.is(":checked"));
        var parentSetName = "#" + $(this).closest('div.attribute_set').attr('id');
        if($(this).closest('div.attribute_set').find('.divider').length != 0){
            $(parentSetName + " li:not(first-of-type) input[type='checkbox']").prop('checked', _input.is(":checked"));
        } else if(_input.is(":checked")){
            $(parentSetName + " input[type='checkbox']").prop('checked', false).removeAttr('checked');
            _input.prop('checked', true);
        }
        e.stopPropagation();
        return false;
    });

    $(document).on('change', "div.attribute_set[id!='attribute_set'] li:not(:first-child) input[type='checkbox']", function (e) {
        var parentSetName = "#" + $(this).closest('div.attribute_set').attr('id');
        if($(this).closest('div.attribute_set').find('.divider').length != 0){
            var allChbLen = $(parentSetName + " li:not(:first-child) input[type='checkbox']").length;
            var chkChbLen = $(parentSetName + " li:not(:first-child) input[type='checkbox']:checked").length;
            $(parentSetName + " li:first-child input[type='checkbox']").prop('checked', allChbLen == chkChbLen);
        } else if($(this).is(":checked")){
            $(parentSetName + " input[type='checkbox']").prop('checked', false).removeAttr('checked');
            $(this).prop('checked', true);
        }
    });

    $(document).on('change', "#add_channel input[type='checkbox'], #adm_grp_perm :checkbox", function(e){
        if ($(this).is(':checked')) {
            $(this).val('on');
        } else {
            $(this).val('off');
        }
    });

    $(document).on("click", 'table[id^="datatable"] a', function (e) {
        if ($(this).attr('href') == '#') {
            e.preventDefault();
        }
    });

    $(document).on('click', "#modalbox_ad a.close-link, #modalbox_ad a.close-link .fa-times, #modalbox_ad, #modalbox_ad button[type='reset']", function (e) {
        if (e.target != e.currentTarget) {
            return;
        }
        e.stopPropagation();
        e.preventDefault();
        if (typeof(conf) == 'object' && typeof(conf.form) != 'undefined' && typeof($.validate) == 'function' ) {
            //$.validate();
            $('#modalbox_ad form').get(0).reset();
        }
        $("#modalbox_ad").find("input").prop("disabled", "disabled");
        $("#modalbox_ad").hide();
        return false;
    });

    $(document).on('click', 'div[data-tvfilter] ul a, div[data-tvfilter] .dropdown-menu button', function (e) {
        e.stopPropagation();
        e.preventDefault();
        if (!$(this).data("postponed")) {
            window.location.href = getURLFilterString(this);
            return false;
        }
    });

    $(document).off('click', "a[disabled], a.disabled");
    $(document).on('click', ".dropdown-menu a[disabled], .dropdown-menu a.disabled", function(e){
        e.stopPropagation();
        e.preventDefault();
        e.stopImmediatePropagation();
        $(this).clearQueue();
        $(this).stop();
        JSErrorModalBox({msg: words ? words['action_is_not_available'] : 'disabled'});
        $(this).closest('.open').removeClass('open');
        return false;
    });

    $(document).on('click', 'div[id^="ui-datepicker"], div[id^="ui-datepicker"] *, .ui-datepicker-next, .ui-datepicker-prev', function(e){
        e.stopPropagation();
    });

    $.datepicker.setDefaults({
        showButtonPanel: true,
        beforeShow: function (input) {
            $(input).attr('readonly', 'readonly');
            setTimeout(function () {
                var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
                buttonPane.empty();
                $("<button>", {
                    text: words['Clean'],
                    click: function (e) {
                        //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                        $.datepicker._clearDate(input);
                        e.stopPropagation();
                    }
                }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
            }, 1);
        },
        onChangeMonthYear: function (year, month, instance) {
            setTimeout(function () {
                var buttonPane = $(instance).datepicker("widget").find(".ui-datepicker-buttonpane");
                buttonPane.empty();
                $("<button>", {
                    text: words['Clean'],
                    click: function (e) {
                        //Code to clear your date field (text box, read only field etc.) I had to remove the line below and add custom code here
                        $.datepicker._clearDate(instance.input);
                        e.stopPropagation();
                    }
                }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
            }, 1);
        }
    });

    $('[id*="datepicker"]').attr('readonly', 'readonly');

    $(document).on('click', "#modalbox button[type='reset'], #modalbox, #modalbox a.close-link, #modalbox a.close-link *", function(e){
        if (e.currentTarget != e.target) {
            return;
        }
        e.stopPropagation();
        e.preventDefault();
        if (typeof($("#modalbox").data('complete')) == 'undefined' || $("#modalbox").data('complete') == 1) {
            if (typeof(closeModalBox) == 'function') {
                closeModalBox();
            } else {
                JScloseModalBox();
            }
        } else {
            for(var i=0;i<3;i++) {
                $('#modalbox > div').fadeTo('slow', 0.5).fadeTo('slow', 1.0);
            }
        }
        return false;
    });

    $(document).on('click', "#modalbox_clone, #modalbox_clone a.close-link, #modalbox_clone a.close-link *", function(e){
        if (e.currentTarget != e.target) {
            return;
        }
        e.stopPropagation();
        e.preventDefault();
        $('#modalbox_clone').hide();
        $('#modalbox_clone').find('.devoops-modal-inner').empty();
        $('#modalbox_clone').find('.devoops-modal-bottom').empty();
        return false;
    });

    $(document).on( "contextmenu", "a.no_context_menu", function(e){
        e.stopPropagation();
        e.preventDefault();
    });

    $('.br.filter', document).on('mouseenter mouseleave', 'ul.dropdown-menu:visible',function(e){
        switch (e.type){
            case 'mouseenter':{
                $(this).on('scroll mousewheel', function(e){
                    var top = $(this).scrollTop();
                    var bottom = $(this).get(0).scrollHeight - top + e.deltaY;
                    var isAtTop = e.deltaY > 0 && top - e.deltaY <= 0;
                    var isAtBottom = e.deltaY < 0 && bottom <= $(this).height();
                    if (isAtTop) {
                        $(this).scrollTop(0);
                        e.preventDefault();
                        e.stopPropagation();
                    } else if (isAtBottom) {
                        $(this).scrollTop(
                            $(this).get(0).scrollHeight - $(this).height()
                        );
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
                break;
            }
            case 'mouseleave': {
                $(this).off('scroll mousewheel');
            }
        }
    });

});

function setMenuScrollButtons ( ){

    var isSlim = isMenuSidebarNarrow();
    var maxScroll = $("#sidebar-left .main-menu").height() - $("#sidebar-left").height();
    //console.log( isSlim +" : "+ maxScroll );

    if( isSlim ) {
        if( maxScroll > 0){
            $("#sidebar-left .scroll-btn").show( );
        } else {
            $("#sidebar-left .scroll-btn").hide( );
        }
    } else {
        $("#sidebar-left .scroll-btn").hide( );
    }

}

// fix left sidebar state
function fixLeftSidebarState( ){
    var width = $("#sidebar-left").width();

    if( width <= 60 ){ // narrow menu width is 40
        $("body")
            .removeClass('sidebar-expanded')
            .addClass('sidebar-collapsed');
        return true;
    }

    if( width > 60 ) {
        $("body")
            .removeClass('sidebar-collapsed')
            .addClass('sidebar-expanded');
        return true;
    }

    return false;
}

function adjustFixedHeader( ){
    if ( !jQuery.fn.dataTable ) {
        return false;
    }

    var table = $('#datatable-1').DataTable();
    table.fixedHeader && table.fixedHeader.adjust();
}

function isMenuSidebarNarrow( ){
    /*
     var width = $("#sidebar-left").width();
     if( width <= 60 ){ // narrow menu width is 40
     return true;
     }
     return false;
     */
    if( $("body").hasClass('sidebar-collapsed') ) {
        return true;
    } else if( $("body").hasClass('sidebar-expanded') ) {
        return false;
    } else {
        // if CSS collapsed, define window width:
        var width = Math.max(
            document.body.scrollWidth, document.documentElement.scrollWidth,
            document.body.offsetWidth, document.documentElement.offsetWidth,
            document.body.clientWidth, document.documentElement.clientWidth
        );
        return ( width <= 1200 ) ? true : false;
    }
}

function slideoutMenuSidebar( ){
    $("body")
        .removeClass('sidebar-collapsed')
        .addClass('sidebar-expanded');

    // Reset scroll buttons visibility:
    $('#sidebar-left .scroll-btn').css({ display: "" })

    // adjust FixedHeader after animation is finished
    if ( jQuery.fn.dataTable ){
        setTimeout( adjustFixedHeader, 210 );
    }
}

function saveMenuSidebarState( ){
    var state;
    if( $("body").hasClass('sidebar-collapsed') ){
        state = 'collapsed';
    }
    if( $("body").hasClass('sidebar-expanded') ){
        state = 'expanded';
    }
    if( state ) {
        $.cookies.set( 'menuSidebarState', state, {hoursToLive: 6*30*24} );
    }
}

function scrollUp(){

    var maxScroll = $("#sidebar-left .main-menu").height() - $("#sidebar-left").height();

    $("#sidebar-left").scrollTo({top:'-=12px'}, 30, {axis:'y'} );
    /*
     if( $("#sidebar-left").scrollTop() > 0 )
     $('#sidebar-left .scroll-btn-down').css({display:""});

     if( $("#sidebar-left").scrollTop() >= maxScroll )
     $('#sidebar-left .scroll-btn-up').hide();
     */
}
function scrollDown(){

    var maxScroll = $("#sidebar-left .main-menu").height() - $("#sidebar-left").height();

    $("#sidebar-left").scrollTo({top:'+=12px'}, 30, {axis:'y'} );
    /*
     if( $("#sidebar-left").scrollTop() <= 0 )
     $('#sidebar-left .scroll-btn-down').hide();

     if( $("#sidebar-left").scrollTop() < maxScroll )
     $('#sidebar-left .scroll-btn-up').css({display:""});
     */
}

function getURLFilterString(obj, href){
    var hrefM = href ? href : window.location.href;
    var filterName = $(obj).closest('div[data-tvfilter]').data('tvfilter');
    var filter_str = 'filters[' + filterName + ']=' + ((obj.tagName == "A") ? $(obj).data('filter'): $(obj).prev('input').val() );
    if (window.location.search == '' && !href) {
        return hrefM + '?' + filter_str;
    } else {
        var filterRegExp = new RegExp('filters\\[' + filterName + '[^=]*=[^&|^$]*', 'ig');
        return filterRegExp.test(hrefM) ? hrefM.replace(filterRegExp, filter_str) : hrefM + '&' + filter_str;
    }
}

function heightCalculate(){
    var height =$("#content").height;
    var tableHeight = $("#datatable-1").length ? ($('#datatable-1 tr').length > 50 ? 50 : $('#datatable-1 tr').length) * 50 + $('#datatable-1').position().top + 200: 0;
    if (tableHeight > height) {
        height = tableHeight;
    }
    $('.attribute_set, [data-tvfilter]').each(function(){
        var newHeight = $(this).position().top + $(this).children('a').height() + $(this).children('ul').height();
        if (newHeight > height) {
            height = newHeight;
        }
        $('#main-wrapper').css({minHeight: height});
    });
    return height;
}

function ajaxSuccess(data, alertMsg, consoleMsg){
    var alertMsg = typeof(alertMsg) != 'undefined'? alertMsg: true;
    var consoleMsg = typeof(consoleMsg) != 'undefined'? consoleMsg: true;
    if (data.success  && !data.error) {
        if ($.isFunction(window[data.action]) && data.message) {
            window[data.action](data);
            JSSuccessModalBox(data);
        } else if ($.isFunction(window[data.action])) {
            window[data.action](data);
        } else {
            JSSuccessModalBox(data);
        }
    } else if (data.success===false) {
        ajaxError({responseJSON: data});
    } else if (data.error) {
        ajaxError({responseJSON: data});
    } else {
        if (alertMsg) {
            alert('Some server error');
        }
        if (consoleMsg) {
            console.log('Some server error');
        }
    }
}

function notty(data,type){
    var msg = data, timeout = false;

	if (type == 'undefined') {
	    type="notification";
	}

	if (type === "notification") {
	    timeout = "1000";
	} else if(type !== false) {
	    timeout = "10000";
	} else {
	    type = 'error'
	}
 	noty({
		text: msg,
		layout      :	'topCenter',
		type        :	type,
		theme       :	'defaultTheme',
		timeout		:	timeout,
		animation: {
			open: {height: 'toggle'}, // jQuery animate function property object
			close: {height: 'toggle'}, // jQuery animate function property object
			easing: 'swing', // easing
		    speed: 500 // opening & closing animation speed
    }
});
}

function ajaxError(data, alertMsg, consoleMsg){
    var alertMsg = typeof(alertMsg) != 'undefined'? alertMsg: false;
    var consoleMsg = typeof(consoleMsg) != 'undefined'? consoleMsg: false;
    var errAction = '';
    if (typeof (data.responseJSON) == 'object' && data.responseJSON.action) {
        errAction += data.responseJSON.action + 'Error';
    }
    if ($.isFunction(window[errAction])) {
        window[errAction](data.responseJSON);
    } else {
        if (alertMsg) {
            alert('Some server error');
        }
        if (consoleMsg) {
            console.log('Some server error');
        }
        if ($.isFunction(window['errAction'])) {
            window['errAction']();
        } else if (data.responseJSON){
            var msg = '';
            if (data && data.responseJSON && data.responseJSON.msg) {
                msg = data.responseJSON.msg;
            } else if (data && data.responseJSON && data.responseJSON.message) {
                msg = data.responseJSON.message;
            } else if (data && data.responseJSON && data.responseJSON.error) {
                msg = data.responseJSON.error;
            }
            JSErrorModalBox({msg: msg});
        }
    }
    if (data.responseJSON && data.responseJSON.additional) {
        additionalAJAXDataProcessing(data.responseJSON.additional);
    }
}

function ajaxPostSend(url, sendData, alertMsg, consoleMsg, async, adOptions){

    var alertMsg = typeof(alertMsg) != 'undefined'? alertMsg : false;
    var consoleMsg = typeof(consoleMsg) != 'undefined'? consoleMsg : false;
    var async = typeof(async) != 'undefined' ? async : false;

    var ajaxOptions ={
        url: url,
        type: 'POST',
        async: async,
        timeout: 0,
        dataType: "json",
        data: sendData,

        success: function (data) {
            ajaxSuccess(data, alertMsg, consoleMsg);
        },
        error: function (data) {
            if (data.readyState == 4 || data.status != 0) {
                if (typeof(data.nothing_to_do) != 'undefined' && data.nothing_to_do) {
                    JScloseModalBox();
                } else {
                    ajaxError(data, alertMsg, consoleMsg);
                }
            }
        }
    };

    if (typeof (adOptions) == 'object') {
        ajaxOptions = $.extend(ajaxOptions, adOptions);
    }

    $.ajax(ajaxOptions);
}

(function ($) {
    $.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
            this.trigger(ev);
            return el.apply(this, arguments);
        };
    });
})(jQuery);

(function($) {
    $.fn.selectRange = function(start, end) {
        if(end === undefined) {
            end = start;
        }
        return this.each(function() {
            if('selectionStart' in this) {
                this.selectionStart = start;
                this.selectionEnd = end;
            } else if(this.setSelectionRange) {
                this.setSelectionRange(start, end);
            } else if(this.createTextRange) {
                var range = this.createTextRange();
                range.collapse(true);
                range.moveEnd('character', end);
                range.moveStart('character', start);
                range.select();
            }
        });
    };
})(jQuery);

function setDropdownAttribute( sendData ){
    var param = '';
    var filterLink = $("a.btn-success.active[href*='filters']");

    if (filterLink.length > 0) {
        param = 'with-button-filters';
    }

    ajaxPostSend('./?set-dropdown-attribute' + (param != '' ? "=" + param : ''), sendData );
}

jQuery.extend({
    random: function (X) {
        return Math.floor(X * (Math.random() % 1));
    },
    randomBetween: function (MinV, MaxV) {
        return MinV + jQuery.random(MaxV - MinV + 1);
    },
    randString: function (n) {
        if (!n) {
            n = 5;
        }

        var text = '';
        var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for (var i = 0; i < n; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return text;
    }
});

(function($) {
    var re = /([^&=]+)=?([^&]*)/g;
    var decode = function(str) {
        return decodeURIComponent(str.replace(/\+/g, ' '));
    };
    $.parseParams = function(query) {
        var params = {}, e;
        if (query) {
            if (query.substr(0, 1) == '?') {
                query = query.substr(1);
            }

            while (e = re.exec(query)) {
                var k = decode(e[1]);
                var v = decode(e[2]);
                if (params[k] !== undefined) {
                    if (!$.isArray(params[k])) {
                        params[k] = [params[k]];
                    }
                    params[k].push(v);
                } else {
                    params[k] = v;
                }
            }
        }
        return params;
    };
})(jQuery);

var d = new Date();
/*if (!$.isFunction(d['toLocaleFormat'])) {*/
    Date.prototype.toLocaleFormat = function (format) {
        var year = this.getFullYear();
        var yearShort = year.toString().slice(2);
        var month = this.getMonth() + 1;
            month = (month.toString().length == 1) ? '0' + month: month;
        var monthNum = this.getMonth();
        var monthName = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        if (typeof (words) != 'undefined') {
            for(var counter in monthName){
                if (typeof words[monthName[counter]] != 'undefined') {
                    monthName[counter] = words[monthName[counter]];
                }
            }
        }
        var day = this.getDate();
            day = (day.toString().length == 1) ? '0' + day: day;
        var hour = this.getHours();
            hour = (hour.toString().length == 1) ? '0' + hour: hour;
        var min = this.getMinutes();
            min = (min.toString().length == 1) ? '0' + min: min;
        var sec = this.getSeconds();
            sec = (sec.toString().length == 1) ? '0' + sec: sec;
        return format.replace("%Y", year).replace("%y", yearShort).replace("%m", month).replace("%d", day).replace("%H", hour).replace("%M", min).replace("%s", sec).replace("%b", monthName[monthNum]) ;
    };
/*}*/

String.prototype.ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

String.prototype.lcfirst = function() {
    return this.charAt(0).toLowerCase() + this.slice(1);
};

String.prototype.ucwords = function () {
    return this.replace(/^(.)|\s(.)/g, function ($1) {
        return $1.toUpperCase();
    });
};

String.prototype.camelCase = function () {
    return (this.charAt(0).toLowerCase() + this.ucwords().slice(1)).replace(' ', '');
};

function JScloseModalBox(){
     /*$.noty.closeAll();*/
    $("#modalbox").hide();
    $("#modalbox_ad").hide();
    $("#modalbox").data('complete', 1);
    $("#modalbox_ad").data('complete', 1);
}

function JSshowModalBox(type){
    notty('<span>' + words['Request_is_being_prossessed'] + '...</span>',type? type: 'notification');
}

function JSSuccessModalBox(data) {
    var msg = '';
    if (typeof(data) != 'undefined') {
        //msg = (typeof(data.msg) != 'undefined' ? data.msg : ''); // #23306
        msg = (typeof(data.message) != 'undefined') ? data.message : (typeof(data.msg) != 'undefined' ? data.msg : '');
    } else {
        data = {};
    }
    if (typeof(data.nothing_to_do) == 'undefined' || !data.nothing_to_do) {
        //notty('<span>' + words['Done'] + (msg ? ', ' + msg.lcfirst() : '')  + '!</span>', 'success'); // #23306
        if( msg != '' ){
            notty('<span>'+ msg +'</span>', 'success');
        } else {
            notty('<span>'+ words['Done'] +'!</span>', 'success');
        }
    }
    $("#modalbox").hide();
    $("#modalbox_ad").hide();
    $("#modalbox").data('complete', 1);
    $("#modalbox_ad").data('complete', 1);
}

function JSErrorModalBox(data){
    var msg = '';
    if (typeof(data) != 'undefined') {
        //msg = ( typeof(data.msg) != 'undefined' ? data.msg : ''); // #23306
        msg = (typeof(data.message) != 'undefined') ? data.message : (typeof(data.msg) != 'undefined' ? data.msg : '');
        msg = ( msg.length == 0 && typeof(data.error) != 'undefined' ? data.error : msg);
    } else {
        data = {};
    }
    if (typeof(data.nothing_to_do) == 'undefined' || !data.nothing_to_do) {
        //notty('<span>' + words['Failed'] + (msg ? ', ' + msg.lcfirst() : '')  + '!</span>', 'error'); // #23306
        if( msg != '' ){
            notty('<span>'+ msg +'</span>', 'error');
        } else {
            notty('<span>'+ words['Failed'] +'!</span>', 'error');
        }
    }
    $("#modalbox").data('complete', 1);
    $("#modalbox_ad").data('complete', 1);
}

function setActiveFilter(obj){
    var parent = $(obj).closest('div[data-tvfilter]');
    parent.removeClass('open').data('filterval', $(obj).data('filter'));
    parent.children('a').children("span:last-of-type").text($(obj).find('span').text());
    $(obj).closest('ul').find("a").removeClass('active');
    $(obj).addClass('active');
}

function getDTRows(obj){
    var dTRows = {}, dTRow = (obj.RowOrder ? obj.RowOrder : (obj.id ? obj.id : false)), dTRowO, dTRowR;

    if (obj.data && obj.data instanceof Array && obj.data.length) {
        for (var i in obj.data) {
            dTRowR = getDTRows(obj.data[i]);
            dTRowR[Object.keys(dTRowR)[0]]['rowData'] = obj.data[i];
            Object.assign(dTRows, dTRowR);
        }
    } else if (dTRow !== false) {
        if (dTRow instanceof Array && dTRow.length) {
            for (var i in dTRow) {
                dTRowR = getDTRows({id: dTRow[i]});
                dTRowR[Object.keys(dTRowR)[0]]['rowData'] = {};
                Object.assign(dTRows, dTRowR);
            }
        } else {
            dTRowO = "#dTRow_" + ('' + dTRow).replace('#', '').replace('dTRow_', '');
            if ($(dTRowO)) {
                dTRows[dTRowO] = {'instance': $(dTRowO)};
            }
        }
    }
    return dTRows;
}

function deleteTableRow(obj){
    try{
        var dTRow = [];
        if (obj) {
            dTRow = getDTRows(obj);
        }
        for (var i in dTRow) {
            var oTable = dTRow[i].instance.closest('table').dataTable();
            oTable.fnRemoveCurrentRow(dTRow[i].instance);
        }
    } catch (e){
        console.log(e);
    }

    JSSuccessModalBox(obj);
    if (obj.additional) {
        additionalAJAXDataProcessing(obj.additional);
    }
}

function deleteTableRowError(obj){
    JSErrorModalBox(obj);
}

function updateTableRow(obj){
    try{
        var dTRow = [],
            curOption = {
                ddMenuMaxHeight: 0,
                ddMenuHeight: 0,
                trParentOffset: 0
            };
        if (obj) {
            dTRow = getDTRows(obj);
        }

        for(var i in dTRow) {
            var oTable = dTRow[i].instance.closest('table').dataTable();
            oTable.fnUpdateCurrentRow(dTRow[i].instance, {'data' : [dTRow[i].rowData]});
            checkMenuItemPosition(dTRow[i].instance, curOption);
        }
    } catch (e){
        console.log(e);
    }

    JSSuccessModalBox(obj);
    if (obj.additional) {
        additionalAJAXDataProcessing(obj.additional);
    }
}

function updateTableRowError(obj){
    JSErrorModalBox(obj);
}

function updateTableData(obj){
    try{
        var dTRow = [], oTables = [];
        if (obj && (obj.RowOrder || obj.id || obj.tableId)) {
            if (!obj.tableId) {
                dTRow = getDTRows(obj);
                $.each(dTRow, function(){
                    var tableID = $(this).instance.closest('table').attr('id');
                    if (oTables.indexOf('oTables') == -1) {
                        oTables.push($("#"+tableID));
                    }
                });
            } else {
                oTables.push($("#" + obj.tableId.replace('#', '')));
            }
        } else {
            oTables = $("table.dataTable:visible");
        }
        $.each(oTables, function(){
            if (typeof($(this).DataTable) == 'function') {
                $(this).DataTable().ajax.reload(null, false); // false = stay on the same page
            }
        });
    } catch (e){
        console.log(e);
    }
    if (typeof(obj) != 'undefined' && typeof(obj.msg) != 'undefined') {
        JSSuccessModalBox(obj);
    }
    if (obj && obj.additional) {
        additionalAJAXDataProcessing(obj.additional);
    }
}

function updateTableDataError(obj){
    JSErrorModalBox(obj);
}

function checkData(obj){
    if (typeof(obj.input_id) != 'undefined') {
        $("#" + obj.input_id).next('div').empty();
        if (typeof obj.nothing_to_do === 'undefined' || !obj.nothing_to_do) {
            $("#" + obj.input_id).next('div').append('<i class="txt-success fa fa-check"></i> ' + obj.chk_rezult).css('visibility', 'visible').show();
        }

        var errFields = $('div[id^="modalbox"]:visible [type="submit"]').data('err-fields') || '';

        errFields = errFields.replace(obj.input_id + '|', '');

        if (errFields.length == 0){
            $('div[id^="modalbox"]:visible [type="submit"]').prop('disabled', false);
        }
        $('div[id^="modalbox"]:visible [type="submit"]').data('err-fields', errFields);
    } else {
        JSSuccessModalBox({msg: obj.chk_rezult});
    }
}

function checkDataError(obj){
    if (typeof(obj.input_id) != 'undefined') {
        $("#" + obj.input_id).next('div').empty();
        if (typeof obj.nothing_to_do === 'undefined' || !obj.nothing_to_do) {
            $("#" + obj.input_id).next('div').append('<i class="txt-danger fa fa-ban"></i> ' + obj.chk_rezult).css('visibility', 'visible').show();
        }

        var errFields = $('div[id^="modalbox"]:visible [type="submit"]').data('err-fields') || '';

        if (errFields.length == 0 || errFields.split('|').indexOf(obj.input_id) == -1) {
            $('div[id^="modalbox"]:visible [type="submit"]').data('err-fields', errFields + obj.input_id + '|');
        }
        $('div[id^="modalbox"]:visible [type="submit"]').prop('disabled', true);
    } else {
        JSErrorModalBox({msg: obj.chk_rezult});
    }
}

function additionalAJAXDataProcessing(data){
    try {
        if (data) {
            if (data.action && $.isFunction(window[data.action])) {
                window[data.action](data);
            } else if (data.modal_data) {
                JScloseModalBox();
                $('#modalbox .modal-header-name').empty().append($('<span></span>').text(data.modal_data.title || ''));
                $('#modalbox .devoops-modal-inner').empty().append($('<div class="col-sm-12"></div>'));
                if (data.modal_data.body) {
                    $('#modalbox .devoops-modal-inner>div')
                        .append($('<p></p>').html(data.modal_data.body.info || ''))
                        .append($('<a></a>').attr('href', (data.modal_data.body.link && data.modal_data.body.link.src) || '#').attr('target', '_blank').text((data.modal_data.body.link && data.modal_data.body.link.title) || ''));
                }
                if (data.modal_data.buttons) {
                    $.each({
                        'yes': 'btn-success green',
                        'no': 'btn-default',
                        'cancel': 'btn-default'
                    }, function (key, class_name) {
                        if (data.modal_data.buttons[key]) {
                            var randID = $.random(1000) + '_btn_add_data_ajax';
                            $('#modalbox .devoops-modal-bottom').empty().append(
                                $('<button class="pull-right"></button>')
                                    .attr('id', randID)
                                    .attr('type', key === 'cancel' ? 'reset': 'button')
                                    .addClass('btn').addClass(data.modal_data.buttons[key].class_name || class_name)
                                    .text(data.modal_data.buttons[key].title || '')
                            );
                            if (data.modal_data.buttons[key].action && $.isFunction(window[data.modal_data.buttons[key].action])) {
                                $.data(window, randID, data);
                                $('#' + randID).on('click', window[data.modal_data.buttons[key].action]());
                            }
                        }
                    });
                }
                $('#modalbox').show();
            }
        }
    } catch (e) {
        console.log(e);
    }
}

jQuery.fn.getEvents = function() {
    if (typeof(jQuery._data) == 'function') {
        return jQuery._data(this.get(0), 'events') || {};
    } else if (typeof(this.data) == 'function') { // jQuery version < 1.7.?
        return this.data('events') || {};
    }
    return {};
};

function debounce(wait, func, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

// stop submitting form on Enter press
$(document).ready(function() {
  $(window).keydown(function(event){
    if( $(event.target).is("input:not([type]),input[type='text'],input[type='password'],input[type='checkbox'],input[type='radio']") && event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

// close modal window on Escape
$(document).ready(function() {
  $(document).keydown( function(event){
    if( event.keyCode === 27 ){
        var $modal = $("#modalbox:visible, #modalbox_ad:visible");
        if( $modal.length == 1 ){
            $modal.find(".devoops-modal-header .close-link").click();
        } else {
            // nothing if 2 modals are displayed
        }
        return false;
    }
  });
});

// Escape a string for HTML interpolation.
function escapeHtml(string) {
    // List of HTML entities for escaping.
    var htmlEscapes = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#x27;',
      '/': '&#x2F;'
    };
    // Regex containing the keys listed above.
    var htmlEscaper = /[&<>"'\/]/g;

    return ('' + string).replace(htmlEscaper, function(match) {
        return htmlEscapes[match];
    });
};
