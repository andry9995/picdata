/************************
 *     EVENEMENTS
 ************************/
$(document).on('click', '.js_jqgrid_save_row', function () {
    var selecteur = '#' + $(this).parent().parent().parent().parent().attr('id');
    $(selecteur).jqGrid('saveRow', lastsel);
    if (typeof 'jqGridAfterSave' === "function") jqGridAfterSave(selecteur);
});

$(document).on('click', '.js_menu_left', function () {
    $('.active-menu').each(function () {
        $(this).parent().removeClass('active-menu');
        $(this).removeClass('active-menu');
    });

    $(this).parent().addClass('active-menu');
    $(this).addClass('active-menu')
});

$(document).on('click', '.js_close_modal', function () {
    close_modal();
});

function updateTableGridSize(table,container,additif_h,additif_w) {
    additif_h = typeof additif_h !== 'undefined' ? additif_h : 0;
    additif_w = typeof additif_w !== 'undefined' ? additif_w : 0;

    setTimeout(function() {
        table.jqGrid("setGridHeight", container.height() + additif_h);
        table.jqGrid("setGridWidth", container.width() + additif_w);
    }, 200);
}

function setGridH(selector, height) {
    selector.jqGrid("setGridHeight", height);
}

function activer_checkbox() {
    $('input:radio').iCheck({
        radioClass: 'iradio_square-green'
    });
}

function activer_combow(selecteur, style) {
    style = typeof style !== 'undefined' ? style : '';
    $(selecteur).selectpicker({
        style: style,
        size: 8
    });
}

function set_tables_responsive() {
    var width = $('.jqGrid_wrapper').width();
    $('table.jqGridTable').setGridWidth(width);
}


function is_email(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function show_info(titre, message, type, timeout) {
    type = typeof type === 'undefined' ? 'success' : type;
    timeout = typeof timeout !== 'undefined' ? timeout : 5000;
    setTimeout(function () {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: timeout,
            preventDuplicates: true
        };
        if (type === 'success') toastr.success(message, titre);
        if (type === 'warning') toastr.warning(message, titre);
        if (type === 'error') toastr.error(message, titre);
        if (type === 'info') toastr.info(message, titre);
    }, 500);
}

function show_modal(contenu, titre, animated, size, as_footer) {
    //size
    size = typeof size !== 'undefined' ? size : 'modal-dialog';
    as_footer = typeof as_footer !== 'undefined' ? as_footer : false;
    $('#modal-size').removeClass('modal-lg');
    $('#modal-size').addClass(size);

    //animation
    $('#modal-animated').addClass('modal-content animated ' + animated);

    //header
    $('#modal-header').html(titre);

    //content
    $('#modal-body').html(contenu);

    if (as_footer)
    {
        $('#modal-footer').removeClass('hidden').html($('#js_id_to_modal_footer').html());
        $('#js_id_to_modal_footer').empty();
    }
    else
    {
        $('#modal-footer').addClass('hidden').empty();
    }

    //show modal
    $('#modal').modal('show');
    $('.modal-content').resizable({
        alsoResize: ".also",
        stop: function( event, ui ) { if (typeof resize_in_modal === "function") resize_in_modal(); }
    });
    $('.modal-dialog').draggable({ handle:'.deplacer'});
}

function modal_ui(options, data, entete_html, percent_height, percent_width) {
    entete_html = typeof entete_html !== 'undefined' ? entete_html : false;
    percent_height = typeof percent_height !== 'undefined' ? percent_height : 0.85;
    percent_width = typeof percent_width !== 'undefined' ? percent_width : 0.9;

    var numero = parseInt($('#modal-ui').attr('data-id')),
        id = "modal-ui-" + numero;
    $('#modal-ui').append('<div id="' + id + '"></div>');

    var height = $(window).height() * percent_height,
        width = $(window).width() * percent_width;

    $('#' + id).html(data).dialog({
        title: options.title,
        height: height,
        width: width,
        show: {
            //effect: "scale",
            duration: 500
        },
        hide: {
            //effect: "scale",
            duration: 500
        },
        modal: options.modal
    })
    .dialogExtend({
        'closable' : true,
        'maximizable' : true,
        'minimizable' : true,
        'collapsable' : false,
        'dblclick' : 'collapse',
        'titlebar' : 'transparent',
        'minimizeLocation' : 'left',
        'icons' : {
            'close' : 'ui-icon-circle-close',
            'maximize' : 'ui-icon-circle-plus',
            'minimize' : 'ui-icon-circle-minus',
            'collapse' : 'ui-icon-triangle-1-s',
            'restore' : 'ui-icon-bullet'
        },
        'load' : function(evt, dlg){ /*alert(evt.type);*/ },
        'beforeCollapse' : function(evt, dlg){ /*alert(evt.type);*/ },
        'beforeMaximize' : function(evt, dlg){ /*alert(evt.type);*/ },
        'beforeMinimize' : function(evt, dlg){ /*alert(evt.type);*/ },
        'beforeRestore' : function(evt, dlg){ /*alert(evt.type);*/ },
        'collapse' : function(evt, dlg){ /*alert(evt.type);*/ },
        'maximize' : function(evt, dlg){ /*alert(evt.type);*/ },
        'minimize' : function(evt, dlg){ /*alert(evt.type);*/ },
        'restore' : function(evt, dlg){ /*alert(evt.type);*/ }
    })
    .parent().addClass('modal-shadow');

    if (entete_html) {
        $('#' + id).parent().find('div.ui-dialog-titlebar span.ui-dialog-title').html(options.title);
    }

    $('#modal-ui').attr('data-id', numero + 1);

    //modal
    $('div.ui-dialog').addClass('modal-content animated pulse');
    //modal-header
    $('div.ui-dialog div.ui-dialog-titlebar').addClass('modal-header');
    $('div.ui-dialog button.ui-dialog-titlebar-close').addClass('pull-right btn btn-default btn-xs').html('<i class="fa fa-times" aria-hidden="true"></i>');
    //modal-content
    $('div.ui-dialog div.ui-dialog-content').addClass('modal-body');
    //modal footer
    $('div.ui-dialog div.ui-resizable-se').addClass('pull-right')
        .removeClass('ui-icon')
        .removeClass('ui-icon-gripsmall-diagonal-se')
        .html('<i class="fa fa-expand fa-rotate-90" style="margin: 5px !important" aria-hidden="true"></i>');
    $('div.ui-dialog').resize(function(){
        if (typeof resize_modal_ui === 'function') resize_modal_ui();
    });
}

$('#modal').on('hidden.bs.modal', function () {
    close_modal();
});
function close_modal() {
    $('#modal-body').empty();
    $('#modal').modal('hide');
}

/**
 *
 * @param mydata
 * @param height
 * @param colNames
 * @param colModel
 * @param table
 * @param caption
 * @param width
 * @param editurl
 * @param rownumbers
 * @param rowNum
 * @param grouping
 * @param groupingView
 * @param firstSort
 * @param firtsColSorter
 * @param shrinkToFit
 * @param userdata
 * @param autoContent
 * @param sortable
 * @param show_num_row
 */
function set_table_jqgrid(mydata, height, colNames, colModel, table, caption, width, editurl, rownumbers, rowNum, grouping, groupingView, firstSort, firtsColSorter, shrinkToFit, userdata, autoContent,sortable,show_num_row) {
    var id_table = table.attr('id');
    $('#' + id_table).after('<table id="' + id_table + '_temp"></table>')
        .jqGrid("clearGridData")
        .jqGrid('GridDestroy')
        .remove();
    $('#' + id_table + '_pager').remove();
    $('#' + id_table + '_temp').attr('id', id_table);
    $('#' + id_table).after('<table id="' + id_table + '_temp"></table>')
        .after('<div id="' + id_table + '_pager"></div>');

    var id_pager = '';
    if (typeof rowNum !== 'undefined') id_pager = "#" + id_table + '_pager';
    else rowNum = 1000000;

    grouping = (typeof grouping === 'undefined') ? false : grouping;
    groupingView = (typeof groupingView === 'undefined') ? null : groupingView;
    rownumbers = (typeof rownumbers === 'undefined') ? true : rownumbers;
    shrinkToFit = (typeof shrinkToFit === 'undefined') ? true : shrinkToFit;
    autoContent = (typeof autoContent === 'undefined') ? false : autoContent;
    sortable = (typeof sortable === 'undefined') ? false : sortable;

    var footerRow = (typeof userdata !== 'undefined'),
        userDataOnFooter = (typeof userdata !== 'undefined');
    userdata = (typeof userdata !== 'undefined') ? userdata : [];


    if (typeof editurl === 'undefined') editurl = '';

    var current_jqgrid = $('#' + id_table).jqGrid({
        edit:false,add:false,del:false,
        data: mydata,
        datatype: "local",
        rownumbers: rownumbers,
        rownumWidth: (rownumbers) ? 40 : 0, // the width of the row numbers columns
        firstsortorder: 'asc',
        height: height,
        width: width,
        autowidth: false,
        shrinkToFit: shrinkToFit,
        rowNum: rowNum,
        rowList: [20, 50, 100, rowNum],
        colNames: colNames,
        colModel: colModel,
        viewrecords: true,
        caption: caption,
        hidegrid: true,
        pager: id_pager,
        editurl: editurl,
        grouping: grouping,
        groupingView: groupingView,
        ajaxRowOptions: {async: true},
        footerrow: footerRow,
        userDataOnFooter: userDataOnFooter,
        userdata:userdata,
        sortable: sortable,
        scroll:1,
        loadonce:true,
        //frozenStaticCols : true,
        onSelectRow: function (id) {
            if (typeof lastsel !== 'undefined')
                if (id) {
                    $('#' + id_table).restoreRow(lastsel).editRow(id, true);
                    lastsel = id;
                }
            //specifique pour chaque tableau
            if (typeof jqGridOnSelectRow === "function") jqGridOnSelectRow($(this).find('#' + id));

            //action after save
            if (typeof jqGridAfterSave === "function") {
                var self = $(this);
                var savedRows = self.jqGrid("getGridParam", "savedRow");
                if (savedRows.length > 0) self.jqGrid("restoreRow", savedRows[0].id);

                self.jqGrid("editRow", id, {
                    keys: true,
                    aftersavefunc: function (id) {
                        jqGridAfterSave('#' + id_table);
                    }
                });
            }
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var cell_action = target.hasClass('js-entite-action');
            var item_action = (target.closest('td').children('.js_jqgrid_save_row').length > 0);
            return !(cell_action || item_action);
        },
        aftersavefunc: function () {
        },
        afterInsertRow: function(rowid, aData) {
            if(typeof afterInsertRow === 'function') afterInsertRow(rowid, aData);
        },
        loadComplete: function () {
            if(autoContent)
            {
                var $this = $(this), iCol, iRow, rows, row, cm, colWidth,
                    $cells = $this.find(">tbody>tr>td"),
                    $colHeaders = $(this.grid.hDiv).find(">.ui-jqgrid-hbox>.ui-jqgrid-htable>thead>.ui-jqgrid-labels>.ui-th-column>div"),
                    colModel = $this.jqGrid("getGridParam", "colModel"),
                    n = $.isArray(colModel) ? colModel.length : 0,
                    idColHeadPrexif = "jqgh_" + this.id + "_";

                $cells.wrapInner("<span class='mywrapping'></span>");
                $colHeaders.wrapInner("<span class='mywrapping'></span>");

                for (iCol = 0; iCol < n; iCol++) {
                    cm = colModel[iCol];
                    colWidth = $("#" + idColHeadPrexif + $.jgrid.jqID(cm.name) + ">.mywrapping").outerWidth() + 25; // 25px for sorting icons
                    for (iRow = 0, rows = this.rows; iRow < rows.length; iRow++) {
                        row = rows[iRow];
                        if ($(row).hasClass("jqgrow"))
                        {
                            colWidth = Math.max(colWidth, $(row.cells[iCol]).find(".mywrapping").outerWidth() + 25);
                        }
                    }

                    $this.jqGrid("setColWidth", iCol, colWidth - (colWidth < 32 ? 15 : -5));
                    //$this.jqGrid("setColWidth", iCol, colWidth + 10);
                }
                $this.jqGrid('setGridWidth',width - 20);
            }

            if(typeof loadCompleteJQgrid === 'function') loadCompleteJQgrid();
        }
    });

    if (typeof firstSort !== 'undefined') {
        current_jqgrid.sortGrid(firtsColSorter);
        if (firstSort === 'asc') current_jqgrid.sortGrid(firtsColSorter);
    }
    if (caption === 'hidden') $('#gview_' + id_table + ' div.ui-jqgrid-caption').remove();

    return current_jqgrid;
    //current_jqgrid.jqGrid('setFrozenColumns');
}

function div_to_tree(selecteur, type) {
    if (typeof entete_html === 'undefined')
        type = {
            'default': {
                'icon': 'fa fa-folder'
            },
            'html': {
                'icon': 'fa fa-file-code-o'
            },
            'svg': {
                'icon': 'fa fa-file-picture-o'
            },
            'css': {
                'icon': 'fa fa-file-code-o'
            },
            'img': {
                'icon': 'fa fa-file-image-o'
            },
            'js': {
                'icon': 'fa fa-file-text-o'
            }
        };

    $(selecteur).jstree({
        'core': {
            'check_callback': true
        },
        'plugins': ['types', 'dnd'],
        'types': {
            'default': {
                'icon': 'fa fa-folder'
            },
            'html': {
                'icon': 'fa fa-file-code-o'
            },
            'svg': {
                'icon': 'fa fa-file-picture-o'
            },
            'css': {
                'icon': 'fa fa-file-code-o'
            },
            'img': {
                'icon': 'fa fa-file-image-o'
            },
            'js': {
                'icon': 'fa fa-file-text-o'
            }
        }
    });
}

/**
 * colspan jqgrid
 *
 * @param id
 * @param groupHeaders
 * @param useColSpanStyle
 */
function group_head_jqgrid(id, groupHeaders, useColSpanStyle) {
    jQuery("#" + id).jqGrid('setGroupHeaders', {
        useColSpanStyle: useColSpanStyle,
        groupHeaders: groupHeaders
    });
}

/**
 *
 * @param html
 */
function set_wrapper_header(html) {
    $('#wrapper-header-text').html(html);
}

/**
 * choix menu active
 */
function menu_active() {
    // lien actuel sur le navigateur
    var current_path = window.location.pathname;

    $('.nav.metismenu li').removeClass('active');
    $('.nav.metismenu li').each(function () {
        // lien sur chaque menu
        var lien = $(this).find('a:first-child').attr('href');
        if (typeof lien !== 'undefined') {
            //si path_menu == lien actuel
            if (current_path === lien) {
                $(this).addClass('active');
                if ($(this).closest('ul.nav').hasClass('nav-second-level')) {
                    $(this).closest('ul.nav').addClass('collapse in');
                    $(this).closest('ul.nav').closest('li').addClass('active');
                } else if ($(this).closest('ul.nav').hasClass('nav-third-level')) {
                    $(this).closest('ul.nav-second-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-third-level').addClass('collapse in');
                    $(this).closest('ul.nav').closest('li').addClass('active');
                } else if ($(this).closest('ul.nav').hasClass('nav-fourth-level')) {
                    $(this).closest('ul.nav-second-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-third-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-fourth-level').addClass('collapse in');
                    $(this).closest('ul.nav').closest('li').addClass('active');
                } else if ($(this).closest('ul.nav').hasClass('nav-fifth-level')) {
                    $(this).closest('ul.nav-second-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-third-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-fourth-level')
                        .addClass('collapse in')
                        .closest('li').addClass('active');
                    $(this).closest('ul.nav-fifth-level').addClass('collapse in');
                    $(this).closest('ul.nav').closest('li').addClass('active');
                }
                return 0;
            }

        }
    });
}

/**
 * heights
 */
function gerer_height() {
    var hauteur = $(window).height() * 0.8;
    $('.scroller').height(hauteur);

    $('.menu-scroll').css({'min-height': hauteur * 0.8, 'max-height': hauteur * 0.8});
}

/**
 * get class active table tr
 * @returns {string}
 */
function get_active_tr() {
    return 'success';
}

/**
 *
 * @param e
 */
function close_pop_over(e) {
    $('[data-toggle="popover"]').each(function () {
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
}

/**
 * Qtip
 */
function activer_qTip() {
    $('.js_tooltip').each(function () {
        var content = $(this).attr('data-tooltip');
        $(this).qtip({
            content: content,
            show: 'mouseover',
            hide: 'mouseout',
            style: {
                classes: 'qtip-youtube'
            }
        })
    })
}

/**
 * reinitialise inspinia
 *
 * @param selecteur
 */
function reinitialiser_inspinia(selecteur) {
    // Collapse ibox function
    $('#' + selecteur + ' .collapse-link').click(function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('#' + selecteur + ' .close-link').click(function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Fullscreen ibox function
    $('#' + selecteur + ' .fullscreen-link').click(function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });

    // Initialize slimscroll for right sidebar
    $('#' + selecteur + ' .sidebar-container').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
    });

    // Add slimscroll to element
    $('#' + selecteur + ' .full-height-scroll').slimscroll({
        height: '100%',
        wheelStep: 3
        //color: '#a9a9a9',
    })
}

/**
 * remove class jquery ui
 */
function remove_j_query_ui() {
    $('.ui-corner-all').removeClass('ui-corner-all');
    $('.ui-widget').removeClass('ui-widget');
}

/**
 * get next class after class ul menu left
 *
 * @param ul
 * @returns {string}
 */
function get_next_class_ul_menu(ul) {
    var result = 'nav ';
    if (ul.hasClass('metismenu')) result += 'nav-second-level';
    else if (ul.hasClass('nav-second-level')) result += 'nav-third-level';
    else if (ul.hasClass('nav-third-level')) result += 'nav-fourth-level';
    else if (ul.hasClass('nav-fourth-level')) result += 'nav-fifth-level';
    return result;
}

/**
 * refresh metis Menu
 */
function refreshMetsiMenu() {
    $('.side-menu').removeData("mm");

    $('.side-menu ul').unbind("click");
    $('.side-menu li').unbind("click");
    $('.side-menu a').unbind("click");

    $('.side-menu').metisMenu();
}

/**
 * string sans accent
 *
 * @returns {String}
 */
String.prototype.sansAccent = function () {
    var accent = [
        /[\300-\306]/g, /[\340-\346]/g, // A, a
        /[\310-\313]/g, /[\350-\353]/g, // E, e
        /[\314-\317]/g, /[\354-\357]/g, // I, i
        /[\322-\330]/g, /[\362-\370]/g, // O, o
        /[\331-\334]/g, /[\371-\374]/g, // U, u
        /[\321]/g, /[\361]/g, // N, n
        /[\307]/g, /[\347]/g, // C, c
    ];
    var noaccent = ['A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n', 'C', 'c'];
    var str = this;
    for (var i = 0; i < accent.length; i++) {
        str = str.replace(accent[i], noaccent[i]);
    }
    return str;
};

/**
 * extention in array
 *
 * @param p_val
 * @returns {boolean}
 */
Array.prototype.in_array = function (p_val) {
    for (var i = 0, l = this.length; i < l; i++) {
        if (this[i] === p_val) {
            return true;
        }
    }
    return false;
};

/***
 * month to two digits
 *
 * @param d
 * @returns {*}
 */
function twoDigits(d) {
    if (0 <= d && d < 10) return "0" + d.toString();
    if (-10 < d && d < 0) return "-0" + (-1 * d).toString();
    return d.toString();
}

/**
 * extentions date to Mysql Format
 *
 * @returns {string}
 */
Date.prototype.toMysqlFormat = function () {
    return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate()) + " " + twoDigits(this.getUTCHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
};

String.prototype.frToMysqlFormat = function() {
    var spliter = this.split('/');
    if (spliter.length !== 3) return this;
    else return spliter[2] + '-' + spliter[1] + '-' + spliter[0];
};

/**
 * autocomplete
 *
 * @param selecteur
 * @param values
 * @param destroy
 */
function activer_auto_complete(selecteur, values, destroy) {
    destroy = (typeof rowNum !== 'undefined') ? destroy : false;
    if (destroy) {
        $(selecteur).autocomplete('destroy');
    }
    else {
        $(selecteur).autocomplete({
            source: values
        });
    }
}

jQuery.extend($.fn.fmatter, {
    jqGridSaveFormatter: function (cellvalue, options, rowdata) {
        return '<i class="fa fa-floppy-o fa-2x js-save-button pointer js_jqgrid_save_row"></i>';
    }
});

//number format
function number_format(number, decimals, dec_point, thousands_sep, return_blanc_if_0) {
    return_blanc_if_0 = (typeof return_blanc_if_0 !== 'undefined') ? return_blanc_if_0 : false;

    if (return_blanc_if_0 && Math.abs(parseFloat(parseFloat(number).toFixed(2))) < 0.009) return '';

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    var res = s.join(dec);
    if (return_blanc_if_0 && (res === '0,00' || res === '0')) return '';
    return res;
}

function number_fr_to_float(s)
{
    return parseFloat(s.replace(/&nbsp;/g, '').replace(/ /g,"").replace(/,/,'.').replace(/\u00a0/g, ''));
}

// Modal Déplaçable
function modalDraggable() {
    $(document).find('.modal-dialog').draggable({
        handle: '.modal-title'
    });
}

function getMoisLettre(mois, abreviation, majuscule) {
    abreviation = typeof abreviation !== 'undefined' ? abreviation : true;
    majuscule = typeof majuscule !== 'undefined' ? majuscule : true;
    mois = parseInt(mois);

    var lettre = '';
    if (mois === 1) lettre = (abreviation) ? 'JAN' : 'JANVIER';
    if (mois === 2) lettre = (abreviation) ? 'FEV' : 'FEVRIER';
    if (mois === 3) lettre = (abreviation) ? 'MAR' : 'MARS';
    if (mois === 4) lettre = (abreviation) ? 'AVR' : 'AVRIL';
    if (mois === 5) lettre = (abreviation) ? 'MAI' : 'MAI';
    if (mois === 6) lettre = (abreviation) ? 'JUI' : 'JUIN';
    if (mois === 7) lettre = (abreviation) ? 'JUL' : 'JUILLET';
    if (mois === 8) lettre = (abreviation) ? 'AOU' : 'AOUT';
    if (mois === 9) lettre = (abreviation) ? 'SEP' : 'SEPTEMBRE';
    if (mois === 10) lettre = (abreviation) ? 'OCT' : 'OCTOBRE';
    if (mois === 11) lettre = (abreviation) ? 'NOV' : 'NOVEMBRE';
    if (mois === 12) lettre = (abreviation) ? 'DEC' : 'DECEMBRE';

    if (!majuscule) lettre = strtolower(lettre);

    return lettre;
}

/* Activer choosen multiselect */
function activeChoosenSelect() {
    $('.chosen-select').chosen({width: '100%'});
}

/** permet de déclencher l'appel à une fonction après un certain délai
 * Annuler l'appel précédent si nouvel appel
 */
function makeDebounce(callback, delay){
    var timer;
    return function(){
        var args = arguments;
        var context = this;
        clearTimeout(timer);
        timer = setTimeout(function(){
            callback.apply(context, args);
        }, delay)
    }
}

/** Eviter des appels consécutifs en introduisant un délai */
function makeThrottle(callback, delay) {
    var last;
    var timer;
    return function () {
        var context = this;
        var now = +new Date();
        var args = arguments;
        if (last && now < last + delay) {
            // le délai n'est pas écoulé on reset le timer
            clearTimeout(timer);
            timer = setTimeout(function () {
                last = now;
                callback.apply(context, args);
            }, delay);
        } else {
            last = now;
            callback.apply(context, args);
        }
    };
}

function build_modal(attr_id,contenu, titre, animated, size, as_footer) {

    $('#modal-container').html('');

    var modal_builder = '<div class="modal inmodal" id="'+ attr_id +'" tabindex="-1" role="dialog" aria-hidden="true">';
    modal_builder += '<div class="modal-dialog" id="modal-build-size">';
    modal_builder += '<div class="modal-content animated" id="modal-build-animated">';
    modal_builder += '<div class="modal-header deplacer">';
    modal_builder += '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
    modal_builder += '<h5 class="modal-title" id="modal-build-header">Modal title</h5>';
    modal_builder += '</div>';
    modal_builder += '<div class="modal-body" id="modal-body" style="height: 100%!important;">...</div>';
    modal_builder += '<div class="modal-footer hidden" id="modal-build-footer"></div>';
    modal_builder += ' </div> </div> </div>';

    $('#modal-container').html(modal_builder);

    //size
    size = typeof size !== 'undefined' ? size : 'modal-dialog';
    as_footer = typeof as_footer !== 'undefined' ? as_footer : false;
    $('#modal-build-size').removeClass('modal-lg');
    $('#modal-build-size').addClass(size);

    //animation
    $('#modal-build-animated').addClass('modal-content animated ' + animated);

    //header
    $('#modal-build-header').html(titre);

    //content
    $('#modal-body').html(contenu);

    if (as_footer)
    {
        $('#modal-build-footer').removeClass('hidden').html($('#js_id_to_modal_footer').html());
        $('#js_id_to_modal_footer').empty();
    }
    else
    {
        $('#modal-footer').addClass('hidden').empty();
    }

    //show modal
    $('#' + attr_id).modal('show');
    $('.modal-content').resizable({
        alsoResize: ".also",
        stop: function( event, ui ) { if (typeof resize_in_modal === "function") resize_in_modal(); }
    });
    $('.modal-dialog').draggable({ handle:'.deplacer'});
}