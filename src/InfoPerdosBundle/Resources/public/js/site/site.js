/**
 * Created by INFO on 01/06/2017.
 */
$(document).ready( function () {

    var siteGrid = $('#js_site_liste');
    var window_height = window.innerHeight;
    var gridWidth = siteGrid.closest("div.row").width();
    var gridHeight = window_height - 250;

    var idClient = $('#client').val();

    var lastsel_site;

    var url = Routing.generate('info_perdos_site',{clientId: idClient});
    var editUrl = Routing.generate('info_perdos_site_edit', {clientId: idClient});

    siteGrid.jqGrid({

        datatype: 'json',
        url: url,
        loadonce: false,
        sortable: true,
        autowidth: true,
        height: gridHeight,
        width: gridWidth,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [20, 50, 100],
        pager: '#js_site_pager',
        hidegrid: false,
        editurl: editUrl,
        caption: 'Sites',
        colNames: ['Nom',  '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'site-nom',
                index: 'site-nom',
                editable: true,
                sortable: true,
                width: 100,
                align: "center",
                classes: 'js-site-nom'
            },
            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-site" title="Enregistrer"></i>'},
                classes: 'js-banque-action'
            }
        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_site) {
                siteGrid.restoreRow(lastsel_site);
                lastsel_site = id;
            }
            siteGrid.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($("#btn-add-site").length == 0) {
                $('#js_site_liste').closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-site" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

            siteGrid.jqGrid('setGridWidth', gridWidth);

        },


        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    $(document).on('click', '#btn-add-site', function (event) {

        if(canAddRow(siteGrid)) {
            event.preventDefault();
            siteGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_site_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js-save-site', function (event) {
        var url = Routing.generate('info_perdos_site',{clientId: idClient});
        event.preventDefault();
        event.stopPropagation();
        siteGrid.jqGrid('saveRow', lastsel_site, {
            "aftersavefunc": function() {
                siteGrid.jqGrid('setGridParam', { url: url }, {page:1}).trigger('reloadGrid');
            }
        });
    });

    $(document).on('change', '#client', function () {
        idClient = $(this).val();
        url = Routing.generate('info_perdos_site', {clientId: idClient});
        editUrl = Routing.generate('info_perdos_site_edit', {clientId: idClient});

        siteGrid.jqGrid('clearGridData');

        siteGrid.jqGrid('setGridParam', {url: url});
        siteGrid.jqGrid('setGridParam', {editurl: editUrl}
        ).trigger('reloadGrid');
    });

});

function canAddRow(jqGrid) {
    var canAdd = true;
    var rows = jqGrid.find('tr');

    rows.each(function () {
        if ($(this).attr('id') == 'new_row') {
            canAdd = false;
        }
    });
    return canAdd;
}