$(function() {
    var lastsel_prestation_general;
    var lastsel_prestation_client;
    var prestation_general_grid = $('#js_prestation_general');
    var prestation_client_grid = $('#js_prestation_client');

    $(document).find('#client').trigger('change');
    var window_height = window.innerHeight;

    //Liste prestation générale
    prestation_general_grid.jqGrid({
        url: Routing.generate('fact_prestation_generale'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 260),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_prestation_general',
        caption: "&nbsp;",
        hidegrid: false,
        editurl: Routing.generate('fact_prestation_generale_edit'),
        colNames: ['Domaine', 'DomaineCode', 'Code prestation', 'Prestations', 'Unité de facturation', 'Appliquer à l\indice', 'Appliquer à la remise', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'p-gen-domaine', index: 'p-gen-domaine', editable: true, align: "center",  width: 150, fixed: true, edittype: 'select',
                editoptions: { dataUrl: Routing.generate('fact_domaine', {json: 0}),
                    dataInit: function (elem) { $(elem).width(100); } }, classes: 'js-p-gen-domaine'},
            {name: 'p-gen-domaine-code', index: 'p-gen-domaine-code', hidden:true, classes: 'js-p-gen-code'},
            {name: 'p-gen-code', index: 'p-gen-code', editable: true, align: "center", width: 100, fixed: true,
                editoptions: {defaultValue: ''}, classes: 'js-p-gen-code'},
            {name: 'p-gen-prestation', index: 'p-gen-prestation', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-p-gen-prestation'},
            {name: 'p-gen-unite', index: 'p-gen-unite', editable: true, align: "center", edittype: 'select',
                editoptions: { dataUrl: Routing.generate('fact_unite', {json: 0}),
                    dataInit: function (elem) { $(elem).width(100); } }, classes: 'js-p-gen-unite'},
            {name: 'p-gen-indice', index: 'p-gen-indice', editable: true, align: "center", width: 150, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-p-gen-indice'},
            {name: 'p-gen-remise', index: 'p-gen-remise', editable: true, align: "center", width: 150, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-p-gen-remise'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-prest-gen" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-prest-gen" title="Supprimer"></i>'},
                classes: 'js-prest-gen-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_prestation_general) {
                prestation_general_grid.restoreRow(lastsel_prestation_general);
                lastsel_prestation_general = id;
            }
            prestation_general_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (item_action) {
                return false;
            }
            return true;
        },
        loadComplete: function() {
            if (prestation_general_grid.closest('.ui-jqgrid').find('#btn-add-prest-gen').length === 0) {
                prestation_general_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-prest-gen" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }

            window_height = window.innerHeight;
            setGridHeight(prestation_general_grid, (window_height - 260));
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    // Enregistrement modif Prest Gen
    $(document).on('click', '.js-save-prest-gen', function (event) {
        event.preventDefault();
        event.stopPropagation();
        prestation_general_grid.jqGrid('saveRow', lastsel_prestation_general, {
            "aftersavefunc": function() {
                reloadGrid(prestation_general_grid, Routing.generate('fact_prestation_generale'));
            }
        });
    });

    // Ajouter nouvelle Prest Gen
    $(document).on('click', '#btn-add-prest-gen', function(event) {
        event.preventDefault();
        prestation_general_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_prestation_general").effect("highlight", 20000);
    });

    // Supprimer une Prest Gen
    $(document).on('click', '.js-delete-prest-gen', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        prestation_general_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_prestation_generale_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    //Liste prestation par client
    prestation_client_grid.jqGrid({
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 278),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_prestation_client',
        caption: "",
        hidegrid: false,
        colNames: ['Domaine', 'DomaineCode', 'Type', 'Actif', 'Code prestation', 'Prestations', 'Unité de facturation', 'Appliquer à l\indice', 'Appliquer à la remise', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'p-client-domaine', index: 'p-client-domaine', align: "center",  width: 150, fixed: true, classes: 'js-p-client-domaine'},
            {name: 'p-client-domaine-code', index: 'p-client-domaine-code', hidden:true, classes: 'js-p-client-domaine-code'},
            {name: 'p-client-type', index: 'p-client-type', hidden:true, classes: 'js-p-client-type'},
            {name: 'p-client-status', index: 'p-client-status', editable: true, align: "center", width: 50, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-p-client-status'},
            {name: 'p-client-code', index: 'p-client-code', align: "center", width: 100, fixed: true, classes: 'js-p-client-code'},
            {name: 'p-client-prestation', index: 'p-client-prestation', classes: 'js-p-client-prestation'},
            {name: 'p-client-unite', index: 'p-client-unite', align: "center", width: 200, fixed: true, classes: 'js-p-client-unite'},
            {name: 'p-client-indice', index: 'p-client-indice', editable: true, align: "center", width: 150, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-p-client-indice'},
            {name: 'p-client-remise', index: 'p-client-remise', editable: true, align: "center", width: 150, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-p-client-remise'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-prest-client" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-prest-client" title="Supprimer"></i>'},
                classes: 'js-prest-gen-client'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_prestation_client) {
                prestation_client_grid.restoreRow(lastsel_prestation_client);
                lastsel_prestation_client = id;
            }
            prestation_client_grid.editRow(id, false);
        },
        loadComplete: function() {
            window_height = window.innerHeight;
            setGridHeight(prestation_client_grid, (window_height - 278));
        },
        ajaxRowOptions: {async: true},
    });

    //Liste prestation par client ou par dossier
    $(document).on('click', '#btn-prestation', function(event) {
        event.preventDefault();
        var client = $(document).find('#client').val();
        var dossier = $(document).find('#dossier').val();
            prestation_client_grid.setGridParam({
                url: Routing.generate('fact_prestation_client', {client: client}),
                datatype: 'json',
                loadonce: true,
                page: 1
            }).trigger('reloadGrid');
    });

    // Enregistrement modif Prestation client/ou dossier
    $(document).on('click', '.js-save-prest-client', function (event) {
        event.preventDefault();
        event.stopPropagation();
        savePrestationClient($(this));
    });

    $(document).on('change', '.js-p-client-status input, .js-p-client-indice input, .js-p-client-remise input', function() {
        savePrestationClient($(this));
        prestation_client_grid.jqGrid('resetSelection');
    });

    function savePrestationClient(cell) {
        var type = cell.closest('tr')
            .find('.js-p-client-type')
            .text();
        var url = type == '0' ? Routing.generate('fact_prestation_client') : Routing.generate('fact_prestation_dossier');
        var edit_url = type == '0' ? Routing.generate('fact_prestation_client_edit') : Routing.generate('fact_prestation_dossier_edit');

        prestation_client_grid.setGridParam({
            editurl: edit_url
        });
        prestation_client_grid.jqGrid('saveRow', lastsel_prestation_client, {
            "aftersavefunc": function() {
                reloadGrid(prestation_client_grid, url);
            }
        });
    }
    //Width Jqgrid dans tabs
    $(document).on("click", ".jqgrid-tabs a", function () {
        prestation_general_grid.jqGrid("setGridWidth", prestation_general_grid.closest(".panel-body").width());
        prestation_client_grid.jqGrid("setGridWidth", prestation_client_grid.closest(".panel-body").width());
    });
});