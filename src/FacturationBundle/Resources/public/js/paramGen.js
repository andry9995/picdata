/**
 * Created by MAHARO on 08/11/2016.
 */

$(function() {
    var lastsel_domaine;
    var lastsel_indice;
    var lastsel_unite;
    var lastsel_remise;
    var remise_first_load = true;
    var lastsel_modele;

    var domaine_grid = $('#js_domaine');
    var indice_grid = $('#js_indice');
    var unite_grid = $('#js_unite');
    var remise_grid = $('#js_remise');
    var modele_grid = $('#js_modele');

    //Liste domaine
    domaine_grid.jqGrid({
        url: Routing.generate('fact_domaine'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_domaine',
        caption: "DOMAINES",
        hidegrid: false,
        editurl: Routing.generate('fact_domaine_edit'),
        colNames: ['Code', 'Domaine', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'domaine-code', index: 'domaine-code', editable: true, align: "center", width: 200, fixed: true,
                editoptions: {defaultValue: ''}, classes: 'js-domaine-code'},
            {name: 'domaine-nom', index: 'domaine-nom', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-domaine-nom'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-domaine" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-domaine" title="Supprimer"></i>'},
                classes: 'js-domaine-action'}
        ],
        onSelectRow: function (id) {
            if (id && id != lastsel_domaine) {
                domaine_grid.restoreRow(lastsel_domaine);
                lastsel_domaine = id;
            }
            domaine_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },
        loadComplete: function() {
            if (domaine_grid.closest('.ui-jqgrid').find('#btn-add-domaine').length == 0) {
                domaine_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-domaine" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}
    });

    // Enregistrement modif Domaine
    $(document).on('click', '.js-save-domaine', function (event) {
        event.preventDefault();
        event.stopPropagation();
        domaine_grid.jqGrid('saveRow', lastsel_domaine, {
            "aftersavefunc": function() {
                reloadGrid(domaine_grid, Routing.generate('fact_domaine'));
            }
        });
    });

    // Ajouter nouveau Domaine
    $(document).on('click', '#btn-add-domaine', function(event) {
        event.preventDefault();
        domaine_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_domaine").effect("highlight", 20000);
    });

    // Supprimer un Domaine
    $(document).on('click', '.js-delete-domaine', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        domaine_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_domaine_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });


    //Liste indice
    indice_grid.jqGrid({
        url: Routing.generate('fact_indice'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_indice',
        caption: "INDICES (NIVEAU SCRIPTURA)",
        hidegrid: false,
        editurl: Routing.generate('fact_indice_edit'),
        colNames: ['N°', 'Date', 'Index', 'Indice', 'Pourcentage (%)', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'indice-code', index: 'indice-code', editable: true, align: "center", width: 100, fixed: true, classes: 'js-indice-code'},
            {name: 'indice-date', index: 'indice-date', editable: true, width: 105, align: "center", sorttype: 'date', formatter: 'date',
                formatoptions: {
                    newformat: "d-m-Y"
                },
                datefmt: 'd-m-Y',
                editoptions : {
                    dataInit: function (el) {
                        setTimeout(function () {
                            $(el).datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true, clearBtn: true});
                        }, 50);
                    }
                },
                classes: 'js-indice-date'},
            {name: 'indice-index', index: 'indice-index', editable: true, align: "center", classes: 'js-indice-index'},
            {name: 'indice-indice', index: 'indice-indice', editable: true, align: "center", classes: 'js-indice-indice'},
            {name: 'indice-percent', index: 'indice-percent', editable: true, align: "center", classes: 'js-indice-percent'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-indice" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-indice" title="Supprimer"></i>'},
                classes: 'js-indice-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_indice) {
                indice_grid.restoreRow(lastsel_indice, function() {

                });
                lastsel_indice = id;
            }
            indice_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },
        loadComplete: function() {
            if (indice_grid.closest('.ui-jqgrid').find('#btn-add-indice').length == 0) {
                indice_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-indice" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}
    });

    // Enregistrement modif Indice
    $(document).on('click', '.js-save-indice', function (event) {
        event.preventDefault();
        event.stopPropagation();
        indice_grid.jqGrid('saveRow', lastsel_indice, {
            "aftersavefunc": function() {
                reloadGrid(indice_grid, Routing.generate('fact_indice'));
            }
        });
    });

    // Ajouter nouvel indice
    $(document).on('click', '#btn-add-indice', function(event) {
        event.preventDefault();
        indice_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_indice").effect("highlight", 20000);
    });

    // Supprimer une indice
    $(document).on('click', '.js-delete-indice', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        indice_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_indice_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });


    //Liste unité de prestation
    unite_grid.jqGrid({
        url: Routing.generate('fact_unite'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_unite',
        caption: "UNITES DE PRESTATION",
        hidegrid: false,
        editurl: Routing.generate('fact_unite_edit'),
        colNames: ['N°', 'Unités de prestations', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'unite-code', index: 'unite-code', editable: true, align: "center", width: 200, fixed: true,
                editoptions: {defaultValue: ''}, classes: 'js-unite-code'},
            {name: 'unite-nom', index: 'unite-nom', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-unite-nom'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-unite" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-unite" title="Supprimer"></i>'},
                classes: 'js-unite-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_unite) {
                unite_grid.restoreRow(lastsel_unite);
                lastsel_unite = id;
            }
            unite_grid.editRow(id, false);
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
            if (unite_grid.closest('.ui-jqgrid').find('#btn-add-unite').length == 0) {
                unite_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-unite" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}
    });

    // Enregistrement modif Unité
    $(document).on('click', '.js-save-unite', function (event) {
        event.preventDefault();
        event.stopPropagation();
        unite_grid.jqGrid('saveRow', lastsel_unite, {
            "aftersavefunc": function() {
                reloadGrid(unite_grid, Routing.generate('fact_unite'));
            }
        });
    });

    // Ajouter nouvelle unité
    $(document).on('click', '#btn-add-unite', function(event) {
        event.preventDefault();
        unite_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_unite").effect("highlight", 20000);
    });

    // Supprimer une unité
    $(document).on('click', '.js-delete-unite', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        indice_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_unite_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });


    //Liste Remise volume
    remise_grid.jqGrid({
        url: Routing.generate('fact_remise_volume'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_remise',
        caption: "REMISE SUR VOLUME (Niveau cabinet)",
        hidegrid: false,
        editurl: Routing.generate('fact_remise_volume_edit'),
        colNames: ['N°', 'Type', 'Tranche1', 'Tranche2', 'Pourcentage (%)', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'remise-code', index: 'remise-code', editable: true, align: "center", width: 200, fixed: true,
                editoptions: {defaultValue: ''}, classes: 'js-remise-code'},
            {name: 'remise-niveau', index: 'remise-niveau', editable: true,
                edittype: "select", editoptions: { dataUrl: Routing.generate('fact_remise_niveau', {json: 0}),
                dataInit: function (elem) { $(elem).width(100); }}, classes: 'js-remise-niveau'},
            {name: 'remise-tranche1', index: 'remise-tranche1', editable: true, align: "right", formatter: 'integer',
                formatoptions: jq_integer_format, editoptions: {defaultValue: ''}, classes: 'js-remise-tranche1'},
            {name: 'remise-tranche2', index: 'remise-tranche2', editable: true, align: "right", formatter: 'integer',
                formatoptions: jq_integer_format, editoptions: {defaultValue: ''}, classes: 'js-remise-tranche2'},
            {name: 'remise-percent', index: 'remise-percent', editable: true, align: "center",
                editoptions: {defaultValue: ''}, classes: 'js-remise-percent'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-remise" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-remise" title="Supprimer"></i>'},
                classes: 'js-domaine-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_remise) {
                remise_grid.restoreRow(lastsel_remise);
                lastsel_remise = id;
            }
            remise_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },
        loadComplete: function() {
            if (remise_grid.closest('.ui-jqgrid').find('#btn-add-remise').length == 0) {
                remise_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-remise" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }
            if (remise_grid.closest('.ui-jqgrid').find('#remise-niveau').length == 0) {
                remise_grid.closest('.ui-jqgrid').find('#btn-add-remise')
                    .before('<div class="" style="display: inline-block; line-height: 40px; margin-right: 200px;">Type: ' +
                        '<select id="remise-niveau" style="line-height: inherit; width: 200px;"><option value="">Tous</option></select></div>');
            }

            if (remise_first_load) {
                $.ajax({
                    url: Routing.generate('fact_remise_niveau', {json: 1}),
                    success: function (data) {
                        data = $.parseJSON(data);
                        var remise_niveau = $('#remise-niveau');
                        var old_value = remise_niveau.val();
                        remise_niveau.empty();
                        var options = '<option value="">Tous</option>';
                        $.each(data, function (index, item) {
                            options += '<option value="' + item.id + '">' + item.libelle + '</option>';
                        });
                        remise_niveau.html(options);
                        remise_niveau.val(old_value);
                        remise_first_load = false;
                    }
                })
            }

        },
        ajaxRowOptions: {async: true}
    });

    // Enregistrement modif Remise
    $(document).on('click', '.js-save-remise', function (event) {
        event.preventDefault();
        event.stopPropagation();
        remise_grid.jqGrid('saveRow', lastsel_remise, {
            "aftersavefunc": function() {
                reloadGrid(remise_grid, Routing.generate('fact_remise_volume'));
            }
        });
    });

    // Ajouter nouvelle Remise
    $(document).on('click', '#btn-add-remise', function(event) {
        event.preventDefault();
        remise_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_remise").effect("highlight", 20000);
    });

    // Supprimer une Remise
    $(document).on('click', '.js-delete-remise', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        remise_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_remise_volume_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    //Filtrer Remise
    $(document).on('change', "#remise-niveau", function() {
        filterGridRemise();
    });


    //Liste Modele Tarification
    modele_grid.jqGrid({
        url: Routing.generate('fact_model_tarif'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 500,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        pager: '#pager_modele',
        caption: "MODELES DE TARIFICATION",
        hidegrid: false,
        editurl: Routing.generate('fact_model_tarif_edit'),
        colNames: ['Code', 'Modèles de tarification', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'modele-code', index: 'modele-code', editable: true, align: "center", width: 200, fixed: true,
                editoptions: {defaultValue: ''}, classes: 'js-modele-code'},
            {name: 'modele-nom', index: 'modele-nom', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-modele-nom'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-modele" title="Enregistrer"></i><i class="fa fa-trash icon-action js-delete-modele" title="Supprimer"></i>'},
                classes: 'js-modele-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_modele) {
                modele_grid.restoreRow(lastsel_modele);
                lastsel_modele = id;
            }
            modele_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },
        loadComplete: function() {
            if (modele_grid.closest('.ui-jqgrid').find('#btn-add-modele').length == 0) {
                modele_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-modele" class="btn btn-outline btn-primary btn-xs">Ajouter</button></div>');
            }
        },
        ajaxRowOptions: {async: true}
    });

    // Enregistrement modif Modele
    $(document).on('click', '.js-save-modele', function (event) {
        event.preventDefault();
        event.stopPropagation();
        modele_grid.jqGrid('saveRow', lastsel_modele, {
            "aftersavefunc": function() {
                reloadGrid(modele_grid, Routing.generate('fact_model_tarif'));
            }
        });
    });

    // Ajouter nouveau modele
    $(document).on('click', '#btn-add-modele', function(event) {
        event.preventDefault();
        modele_grid.jqGrid('addRow', {
            rowID: "new_row",
            initData: {},
            position: "first",
            useDefValues: true,
            useFormatter: true,
            addRowParams: {extraparam:{}}
        });
        $("#" + "new_row", "#js_modele").effect("highlight", 20000);
    });

    // Supprimer un modele
    $(document).on('click', '.js-delete-modele', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');
        modele_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_model_tarif_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });



    function filterGridRemise() {
        if ($("#remise-niveau").val() != undefined) {
            var searchFilter = $("#remise-niveau").find('option:selected').text(), f;

            if (searchFilter.length === 0 || searchFilter.trim() == '' || searchFilter == 'Tous') {
                remise_grid[0].p.search = false;
                $.extend(remise_grid[0].p.postData, {filters: ""});
            } else {
                f = {groupOp: "OR", rules: []};
                f.rules.push({field: "remise-niveau", op: "eq", data: searchFilter});
                f.rules.push({field: "remise-niveau", op: "eq", data: ''});
                remise_grid[0].p.search = true;
                $.extend(remise_grid[0].p.postData, {filters: JSON.stringify(f)});
            }
            remise_grid.trigger("reloadGrid", [{page: 1, current: true}]);
        }
    }

    //Width Jqgrid dans tabs
    $(document).on("click", ".jqgrid-tabs a", function () {
        domaine_grid.jqGrid("setGridWidth", domaine_grid.closest(".panel-body").width());
        indice_grid.jqGrid("setGridWidth", indice_grid.closest(".panel-body").width());
        unite_grid.jqGrid("setGridWidth", unite_grid.closest(".panel-body").width());
        remise_grid.jqGrid("setGridWidth", remise_grid.closest(".panel-body").width());
        modele_grid.jqGrid("setGridWidth", modele_grid.closest(".panel-body").width());
    });
});
