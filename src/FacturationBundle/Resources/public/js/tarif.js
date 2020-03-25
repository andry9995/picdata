/**
 * Created by TEFY on 15/12/2016.
 */

$(function() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        var switchery = new Switchery(html, {
            size: 'small',
            color: '#18a689'
        });
    });
    $(document).find('#client').trigger('change');
    var window_height = window.innerHeight;

    var lastsel_tarif_general;
    var lastsel_tarif_client;
    var tarif_general_grid = $('#js_tarif_general');
    var tarif_client_grid = $('#js_tarif_client');
    var annee_gen = $('#annee-gen').val();
    var annee_gen_val = $('#annee-gen')
        .find('option:selected')
        .text();
    var annee_client = $('#annee-client').val();
    var annee_client_val = $('#annee-client')
        .find('option:selected')
        .text();
    var apply_indice_gen = $('#apply-indice-gen').prop('checked') == true ? 1 : 0;
    var apply_indice_client = $('#apply-indice-client').prop('checked') == true ? 1 : 0;
    var recalc_pu_gen = $('#recalculer-pu-gen').prop('checked') == true ? 1 : 0;
    var recalc_pu_client = $('#recalculer-pu-client').prop('checked') == true ? 1 : 0;
    var client = $('#client').val();
    var modele_gen = $('#model-tarif-gen').val();
    var modele_client = $('#model-tarif-client').val();

    //Liste tarif général
    tarif_general_grid.jqGrid({
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 270),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_tarif_general',
        hidegrid: false,
        editurl: Routing.generate('fact_tarification_general_edit'),
        colNames: ['Domaine', 'DomaineCode', 'Code prestation', 'Prestations', 'Unité de facturation', 'Afficher la Quantité', 'Formule',
            'Pu Fixe', 'Pu Variable', 'Pu Fixe', 'Pu Variable', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 't-gen-domaine', index: 't-gen-domaine', editable: false, align: "center", sortable: false, width: 100, fixed: true, edittype: 'select',
                classes: 'js-t-gen-domaine'},
            {name: 't-gen-domaine-code', index: 't-gen-domaine-code', hidden:true, classes: 'js-t-gen-domaine-code'},
            {name: 't-gen-code', index: 't-gen-code', editable: false, align: "center", sortable: false, width: 100, fixed: true, classes: 'js-t-gen-code'},
            {name: 't-gen-prestation', index: 't-gen-prestation', editable: false, sortable: false, classes: 'js-t-gen-prestation'},
            {name: 't-gen-unite', index: 't-gen-unite', editable: false, align: "center", sortable: false, classes: 'js-t-gen-unite'},
            {name: 't-gen-quantite', index: 't-gen-quantite', editable: true, sortable: false, align: "center", width: 120, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-t-gen-quantite'},
            {name: 't-gen-formule', index: 't-gen-formule', editable: true, sortable: false, align: 'center', classes: 'js-t-gen-formule'},
            {name: 't-gen-pu-fixe-base', index: 't-gen-pu-fixe-base', editable: false, sortable: false, align: 'center',
                width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-t-gen-pu-fixe-base'},
            {name: 't-gen-pu-variable-base', index: 't-gen-pu-variable-base', editable: false, sortable: false, align: 'center',
                width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-t-gen-pu-variable-base'},
            {name: 't-gen-pu-fixe', index: 't-gen-pu-fixe', editable: true, sortable: false, align: 'center',
                width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-t-gen-pu-fixe'},
            {name: 't-gen-pu-variable', index: 't-gen-pu-variable', editable: true, sortable: false, align: 'center',
                width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-t-gen-pu-variable'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-tarif-gen" title="Enregistrer"></i>'},
                classes: 'js-tarif-gen-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_tarif_general) {
                tarif_general_grid.restoreRow(lastsel_tarif_general);
                lastsel_tarif_general = id;
            }
            tarif_general_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;
        },
        loadComplete: function(data) {
            var indice = $.parseJSON(data.indice);
            var indice_value = "";
            var date_indice_value = "";
            var indice_pourcentage = "";

            if (typeof indice === 'object') {
                indice_value = indice.indice;
                date_indice_value = moment.unix(indice.dateIndice.timestamp).format('DD-MM-YYYY');
                indice_pourcentage = indice.pourcentage + '%'
            }
            $(document)
                .find('#indice-value-gen')
                .html('Indice pour tarif ' + annee_gen_val + ' (' + date_indice_value + ') :<br/>' + indice_value
                    + ' (' + indice_pourcentage + ')');
            groupColTarifGen();
            window_height = window.innerHeight;
            setGridHeight(tarif_general_grid, (window_height - 270));
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    //Afficher tarif
    $(document).on('click', '#btn-tarif-general', function(event) {
        event.preventDefault();
        showTarifGen();
    });

    //Appliquer indice tarifGen
    $(document).on('change', '#apply-indice-gen', function(event) {
       event.preventDefault();
       showTarifGen();
    });

    $(document).on('change', '#recalculer-pu-gen', function(event) {
        event.preventDefault();
        showTarifGen();
    });

    //Enregistrer modification tarifs gen
    $(document).on('click', '.js-save-tarif-gen', function (event) {
        event.preventDefault();
        event.stopPropagation();
        tarif_general_grid.jqGrid('saveRow', lastsel_tarif_general, {
            "aftersavefunc": function() {
                showTarifGen();
            }
        });
    });

    // Supprimer Tarif Gen
    $(document).on('click', '.js-delete-tarif-gen', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        tarif_general_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_tarification_general_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    //Liste tarif client
    tarif_client_grid.jqGrid({
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 363),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_tarif_client',
        hidegrid: false,
        colNames: ['Domaine', 'DomaineCode', 'Code prestation', 'Prestations', 'Unité de facturation', 'Afficher la Quantité', 'Formule',
            'Pu Fixe', 'Pu Variable', 'Pu Fixe', 'Pu Variable', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 't-client-domaine', index: 't-client-domaine', editable: false, align: "center", sortable: false, width: 100, fixed: true, edittype: 'select',
                classes: 'js-t-client-domaine'},
            {name: 't-client-domaine-code', index: 't-client-domaine-code', hidden:true, classes: 'js-t-client-domaine-code'},
            {name: 't-client-code', index: 't-client-code', editable: false, align: "center", sortable: false, width: 100, fixed: true, classes: 'js-t-gen-code'},
            {name: 't-client-prestation', index: 't-client-prestation', editable: false, sortable: false, classes: 'js-t-client-prestation'},
            {name: 't-client-unite', index: 't-client-unite', editable: false, align: "center", sortable: false, classes: 'js-t-client-unite'},
            {name: 't-client-quantite', index: 't-client-quantite', editable: true, sortable: false, align: "center", width: 120, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-t-client-quantite'},
            {name: 't-client-formule', index: 't-client-formule', editable: true, sortable: false, align: 'center', classes: 'js-t-client-formule'},
            {name: 't-client-pu-fixe-base', index: 't-client-pu-fixe-base', editable: false, sortable: false, align: 'center',
                width: 80, fixed: true, format: 'number', formatoptions: jq_number_format, classes: 'js-t-client-pu-fixe-base'},
            {name: 't-client-pu-variable-base', index: 't-client-pu-variable-base', editable: false, sortable: false, align: 'center',
                width: 80, fixed: true, format: 'number', formatoptions: jq_number_format, classes: 'js-t-client-pu-variable-base'},
            {name: 't-client-pu-fixe', index: 't-client-pu-fixe', editable: true, sortable: false, align: 'center',
                width: 80, fixed: true, format: 'number', formatoptions: jq_number_format, classes: 'js-t-client-pu-fixe'},
            {name: 't-client-pu-variable', index: 't-client-pu-variable', editable: true, sortable: false, align: 'center',
                width: 80, fixed: true, format: 'number', formatoptions: jq_number_format, classes: 'js-t-client-pu-variable'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-tarif-client" title="Enregistrer"></i>'},
                classes: 'js-tarif-client-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_tarif_client) {
                tarif_client_grid.restoreRow(lastsel_tarif_client);
                lastsel_tarif_client = id;
            }
            tarif_client_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;
        },
        loadComplete: function(data) {
            var can_modify = data.can_modify;
            var indice = $.parseJSON(data.indice);
            var indice_value = "";
            var date_indice_value = "";
            var indice_pourcentage = "";

            if (typeof indice === 'object') {
                indice_value = indice.indice;
                date_indice_value = moment.unix(indice.dateIndice.timestamp).format('DD-MM-YYYY');
                indice_pourcentage = indice.pourcentage + '%'
            }
            $(document)
                .find('#indice-value-client')
                .html('Indice pour tarif ' + annee_client_val + ' (' + date_indice_value + ') :<br/>' + indice_value
                    + ' (' + indice_pourcentage + ')');
            groupColTarifClient();

            if (!can_modify) {
                tarif_client_grid.jqGrid('setColProp', 't-client-pu-fixe', {editable: false});
                tarif_client_grid.jqGrid('setColProp', 't-client-pu-variable', {editable: false});
            } else {
                tarif_client_grid.jqGrid('setColProp', 't-client-pu-fixe', {editable: true});
                tarif_client_grid.jqGrid('setColProp', 't-client-pu-variable', {editable: true});
            }
            window_height = window.innerHeight;
            setGridHeight(tarif_client_grid, (window_height - 363));
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    //Liste tarif par client ou par dossier
    $(document).on('click', '#btn-tarif-client', function(event) {
        event.preventDefault();
        showTarifClient();
    });

    //Appliquer indice tarif Client
    $(document).on('change', '#apply-indice-client', function(event) {
        event.preventDefault();
        showTarifClient();
    });

    $(document).on('change', '#recalculer-pu-client', function(event) {
        event.preventDefault();
        showTarifClient();
    });

    //Enregistrer modification tarifs client
    $(document).on('click', '.js-save-tarif-client', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var dossier = $(document).find('#dossier').val();
        var edit_url = 'fact_tarification_client_edit';

        tarif_client_grid.jqGrid('setGridParam', {
           editurl: Routing.generate(edit_url)
        }).jqGrid('saveRow', lastsel_tarif_client, {
            "aftersavefunc": function() {
                showTarifClient();
            }
        });
    });

    // Supprimer Tarif Client
    $(document).on('click', '.js-delete-tarif-client', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        var dossier = $(document).find('#dossier').val();
        var delete_url = 'fact_tarification_client_remove';

        tarif_client_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate(delete_url),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    //Width Jqgrid dans tabs
    $(document).on("click", ".jqgrid-tabs a", function () {
        tarif_general_grid.jqGrid("setGridWidth", tarif_general_grid.closest(".panel-body").width());
        tarif_client_grid.jqGrid("setGridWidth", tarif_client_grid.closest(".panel-body").width());
        groupColTarifGen();
        groupColTarifClient();
    });

    function showTarifGen() {
        annee_gen = $('#annee-gen').val();
        annee_gen_val = $('#annee-gen')
            .find('option:selected')
            .text();
        apply_indice_gen = $('#apply-indice-gen').prop('checked') == true ? 1 : 0;
        recalc_pu_gen = $('#recalculer-pu-gen').prop('checked') == true ? 1 : 0;
        modele_gen = $('#model-tarif-gen').val();
        reloadGrid(tarif_general_grid, Routing.generate('fact_tarification_general',
            {annee: annee_gen, indice: apply_indice_gen, modele: modele_gen, recalculer: recalc_pu_gen}
            ));
    }

    function showTarifClient() {
        var client = $(document).find('#client').val();
        var dossier = $(document).find('#dossier').val();
        apply_indice_client = $('#apply-indice-client').prop('checked') == true ? 1 : 0;
        recalc_pu_client = $('#recalculer-pu-client').prop('checked') == true ? 1 : 0;
        annee_client = $('#annee-client').val();
        annee_client_val = $('#annee-client')
            .find('option:selected')
            .text();
        modele_client = $('#model-tarif-client').val();

        tarif_client_grid.setGridParam({
            url: Routing.generate('fact_tarification_client',
                {client: client, annee: annee_client, indice: apply_indice_client, modele: modele_client, recalculer: recalc_pu_client}
                ),
            datatype: 'json',
            loadonce: true,
            page: 1
        }).trigger('reloadGrid');
    }

    function groupColTarifGen() {
        var txt_indice = "(Sans indice)";
        if ($('#apply-indice-gen').prop('checked')) {
            txt_indice = "(Avec indice)";
        }
        tarif_general_grid.jqGrid('destroyGroupHeader');
        tarif_general_grid.jqGrid('setGroupHeaders', {
            useColSpanStyle: true,
            groupHeaders: [
                {startColumnName: 't-gen-pu-fixe-base', numberOfColumns: 2, titleText: '<strong>Tarif de base: ' + (annee_gen_val - 1) + '<br>&nbsp;</strong>'},
                {startColumnName: 't-gen-pu-fixe', numberOfColumns: 2, titleText: '<strong>Tarif appliqué: ' + (annee_gen_val) + '<br><span class="badge badge-primary">' + txt_indice + '</span></strong>'}
            ]
        });
    }

    function groupColTarifClient() {
        var txt_indice_client = "(Sans indice)";
        if ($('#apply-indice-client').prop('checked')) {
            txt_indice_client = "(Avec indice)";
        }
        tarif_client_grid.jqGrid('destroyGroupHeader');
        tarif_client_grid.jqGrid('setGroupHeaders', {
            useColSpanStyle: true,
            groupHeaders: [
                {startColumnName: 't-client-pu-fixe-base', numberOfColumns: 2, titleText: '<strong>Tarif de base: ' + (annee_client_val - 1) + '<br>&nbsp;</strong>'},
                {startColumnName: 't-client-pu-fixe', numberOfColumns: 2, titleText: '<strong>Tarif appliqué: ' + (annee_client_val) + '<br><span class="badge badge-primary">' + txt_indice_client + '</span></strong>'}
            ]
        });
    }
});