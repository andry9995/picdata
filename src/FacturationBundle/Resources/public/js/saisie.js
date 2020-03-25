/**
 * Created by TEFY on 19/01/2017.
 */
$(function () {
    $(document).find('#client').trigger('change');
    var client_id = $('#client').val();
    var exercice = $('#exercice');
    showRemiseApplique();

    var current_year = moment().format('YYYY');
    exercice.find('option[selected]').remove();
    exercice.val(current_year);
    var btn_controle = $('#btn-controle');

    //Activer popover après hide
    $('body').on('hidden.bs.popover', function (e) {
        $(e.target).data("bs.popover").inState.click = false;
    });
    activatePopoverRemise();

    $('#select-nouveau-saisi').datepicker({
        language: 'fr',
        clearBtn: true,
        format: 'MM-yyyy',
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    }).on('changeDate', function (e) {
        var selected_date = moment(e.date).format('MM-YYYY');
        $('#select-nouveau-saisi').attr('data-selected-date', selected_date);
    }).on('clearDate', function () {
        $('#select-nouveau-saisi').attr('data-selected-date', '');
    });

    var window_height = window.innerHeight;

    var lastsel_saisie,
        lastsel_client_associe,
        saisie_grid = $('#js_saisie'),
        controle_grid = $('#js_controle'),
        controle_detail_grid = $('#js_controle_detail_image');

    saisie_grid.jqGrid({
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 336),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        rowList: [500, 1000, 5000],
        pager: '#pager_saisie',
        hidegrid: false,
        editurl: Routing.generate('fact_saisie_edit'),
        colNames: ['Domaine', 'DomaineCode', 'Code', 'Prestations', 'Unité <br>de facturat°', 'Honoraire<br>(€)', 'Ne pas<br>calculer', 'Formule', 'Quantité',
            'Pu<br>Fixe', 'Pu<br>Variable', 'Unités<br>réalisées', 'Prix<br>(€)', 'Remise', 'Prix Net<br>(€)', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'saisie-domaine', index: 'saisie-domaine', editable: false, align: "center", sortable: false, width: 100, fixed: true, edittype: 'select', classes: 'js-saisie-domaine'},
            {name: 'saisie-domaine-code', index: 'saisie-domaine-code', hidden: true, classes: 'saisie-domaine-code'},
            {name: 'saisie-code', index: 'saisie-code', editable: false, align: "center", sortable: false, width: 60, fixed: true, classes: 'js-saisie-code'},
            {name: 'saisie-prestation', index: 'saisie-prestation', editable: false, sortable: false, classes: 'js-saisie-prestation'},
            {name: 'saisie-unite', index: 'saisie-unite', editable: false, align: "center", sortable: false, width: 100, fixed: true, classes: 'js-saisie-unite'},
            {name: 'saisie-honoraire', index: 'saisie-honoraire', editable: true, align: "center", sortable: false, width: 65, fixed: true, formatter: 'number', formatoptions: jq_integer_format, classes: 'js-saisie-honoraire'},
            {name: 'saisie-nocalcul', index: 'saisie-nocalcul', editable: true, sortable: false, align: "center", width: 50, fixed: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-saisie-nocalcul'},
            {name: 'saisie-formule', index: 'saisie-formule', editable: false, sortable: false, align: 'center', classes: 'js-saisie-formule'},
            {name: 'saisie-quantite', index: 'saisie-quantite', editable: true, sortable: false, align: 'center', width: 60, fixed: true, formatter: 'integer', formatoptions: jq_integer_format, classes: 'js-saisie-quantite'},
            {name: 'saisie-pu-fixe', index: 'saisie-pu-fixe', editable: false, sortable: false, align: 'center', width: 60, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-saisie-pu-fixe'},
            {name: 'saisie-pu-variable', index: 'saisie-pu-variable', editable: false, sortable: false, align: 'center', width: 60, fixed: true, format: 'number', formatoptions: jq_number_format, classes: 'js-saisie-pu-variable'},
            {name: 'saisie-unite-realise', index: 'saisie-unite-realise', editable: true, sortable: false, align: 'center', width: 80, fixed: true, formatter: 'integer', formatoptions: jq_integer_format, classes: 'js-saisie-unite-realise'},
            {name: 'saisie-prix-calcule', index: 'saisie-prix-calcule', editable: false, sortable: false, align: 'center', width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-saisie-prix-calcule'},
            {name: 'saisie-remise-volume', index: 'saisie-remise-volume', editable: false, sortable: false, align: 'center', width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-saisie-remise-volume'},
            {name: 'saisie-prix-net', index: 'saisie-prix-net', editable: false, sortable: false, align: 'center', width: 80, fixed: true, formatter: 'number', formatoptions: jq_number_format, classes: 'js-saisie-prix-net'},
            {
                name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-saisie" title="Enregistrer"></i>'},
                classes: 'js-saisie-action'
            }
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_saisie) {
                saisie_grid.restoreRow(lastsel_saisie);
                lastsel_saisie = id;
            }
            saisie_grid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;
        },
        loadComplete: function (data) {
            window_height = window.innerHeight;
            setGridHeight(saisie_grid, (window_height - 336));
            var nb_ligne_client = data.nb_ligne_client;
            var nb_ligne_dossier = data.nb_ligne_dossier;
            var remise_pourcentage = data.remise_pourcentage;

            $('#nb-ligne-client').text(numeral(nb_ligne_client).format('0,0'));
            $('#nb-ligne-dossier').text(numeral(nb_ligne_dossier).format('0,0'));
            $('#remise-pourcentage').text(remise_pourcentage + '%');
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}
    });


    controle_detail_grid.jqGrid({
        datatype: 'local',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 350,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: false,
        colNames: ['Date', 'Libellé', 'Image', 'Exercice'],
        colModel: [
            {name: 'ctrl-detail-date', index: 'ctrl-detail-date', editable: false, align: "center", sortable: true, width: 100, fixed: true, classes: 'js-ctrl-detail-date'},
            {name: 'ctrl-detail-libelle', index: 'ctrl-detail-libelle', editable: false, width: 200, classes: 'js-ctrl-detail-libelle'},
            {name: 'ctrl-detail-image', index: 'ctrl-detail-image', editable: false, width: 100, fixed: true, align: "center", sortable: true, classes: 'js-ctrl-detail-image'},
            {name: 'ctrl-detail-exercice', index: 'ctrl-detail-exercice', editable: false, width: 100, fidex: true, sortable: true, classes: 'js-ctrl-detail-exercice'}
        ],
        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}
    });

    //Changer action: Voir saisie
    $(document).on('click', '#btn-saisi-fini', function (event) {
        event.preventDefault();
        $(this)
            .removeClass('btn-default')
            .addClass('btn-primary');
        $('#btn-nouveau-saisi')
            .addClass('btn-default')
            .removeClass('btn-primary');
        $('#form-saisi-fini').removeClass('hidden');
        $('#form-nouveau-saisi').addClass('hidden');
        getMoisSaisi();
    });

    //Changer action: Saisir un mois
    $(document).on('click', '#btn-nouveau-saisi', function (event) {
        event.preventDefault();
        $(this)
            .removeClass('btn-default')
            .addClass('btn-primary');
        $('#btn-saisi-fini')
            .addClass('btn-default')
            .removeClass('btn-primary');
        $('#form-nouveau-saisi').removeClass('hidden');
        $('#form-saisi-fini').addClass('hidden');
    });

    // Changer client
    $(document).on('change', '#client', function (event) {
        event.preventDefault();
        $('#btn-change-type-remise').popover('hide');
        showRemiseApplique();
    });

    //Liste mois saisie d'un dossier
    $(document).on('change', '#dossier', function (event) {
        event.preventDefault();
        if ($(this).text() !== 'Tous') {
            getMoisSaisi();
        }
    });

    $(document).on('change', '#exercice', function (event) {
        event.preventDefault();
        if ($('#dossier').text() !== 'Tous') {
            getMoisSaisi();
        }
    });

    //Affichage saisie
    $(document).on('click', '#btn-saisie', function (event) {
        event.preventDefault();
        clearGrid(saisie_grid);

        var dossier = $('#dossier').val(),
            dossier_text = $('#dossier').find('option:selected').text(),
            exercice = $('#exercice').val(),
            mois = $('#btn-saisi-fini').hasClass('btn-primary') ? $('#select-saisi-fini').val() : $('#select-nouveau-saisi').attr('data-selected-date'),
            annee_tarif = $('#annee').val(),
            type = $('#btn-saisi-fini').hasClass('btn-primary') ? 1 : 0;
        if (dossier_text !== 'Tous') {

            saisie_grid.jqGrid('setGridParam', {
                url: Routing.generate('fact_saisie_liste', {
                    dossier: dossier,
                    exercice: exercice,
                    mois: mois,
                    annee_tarif: annee_tarif,
                    type: type
                }),
                datatype: 'json',
                loadonce: true,
                page: 1
            }).trigger('reloadGrid');
        } else {
            show_info('Pas de dossier.', 'Vous devez choisir un dossier', 'warning');
        }
    });

    //Affichage liste Récap
    $(document).on('click', '#btn-controle', function(e) {
        e.preventDefault();
        btn_controle.html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Afficher Récap');
        clearGrid(controle_grid);
        setTimeout(function() {
            controle_grid.jqGrid('GridUnload');
            controle_grid = $('#js_controle');
            controleImportParDossier();
        }, 50);

    });

    //Affichage détail controle
    $(document).on('click', '.td-import', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var cell = $(this),
            cell_value = $(this).html(),
            row = cell.closest('tr'),
            dossier = row.find('.js-controle-dossier').text();
        var ligne_importer = $(this).hasClass('js-controle-importer');
        var affecter = $(this).hasClass('js-controle-affecter');
        var non_affecter = $(this).hasClass('js-controle-non-affecter');
        var is_prestation = $(this).hasClass('td-import-prestation');
        if (is_prestation) {
            if (cell_value !== '&nbsp;' ) {
                var prestation_code = cell.attr('data-prestation-code');
                $('#detail-controle-modal').modal('show');
                $('#detail-controle-modal-title').text(dossier + ": " + prestation_code);
            }
        }
    });


    //Enregistrer saisie
    $(document).on('click', '.js-save-saisie', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var dossier = $('#dossier').val(),
            exercice = $('#exercice').val(),
            mois = $('#btn-saisi-fini').hasClass('btn-primary') ? $('#select-saisi-fini').val() : $('#select-nouveau-saisi').attr('data-selected-date'),
            annee_tarif = $('#annee').val(),
            type = 1;
        saisie_grid.jqGrid('saveRow', lastsel_saisie, {
            "aftersavefunc": function () {
                reloadGrid(saisie_grid, Routing.generate('fact_saisie_liste', {
                    dossier: dossier,
                    exercice: exercice,
                    mois: mois,
                    annee_tarif: annee_tarif,
                    type: type
                }));
            }
        });
    });

    //Afficher les clients associés
    $(document).on('click', '#btn-client-associe', function (event) {
        event.preventDefault();
        $.ajax({
            url: Routing.generate('facturation_client_associe'),
            type: 'GET',
            success: function (data) {
                $('#select-client-modal')
                    .html(data)
                    .modal('show');
                loadGridClientAssocie();
                modalDraggable();
            }
        });
    });

    //Ajouter un client associé
    $(document).on('click', '#btn-add-client-associe', function (event) {
        client_id = $('#client').val();
        var client_autre_id = $('#client2').val();

        $.ajax({
            url: Routing.generate('fact_client_associe_add', {client: client_id}),
            type: 'POST',
            data: {
                client_autre: client_autre_id
            },
            success: function (data) {
                data = $.parseJSON(data);
                if (data.erreur) {
                    show_info('Erreur', data.erreur_text, 'error');
                } else {
                    var client_associe_grid = $(document).find('#js_client_associe');
                    reloadGrid(client_associe_grid, Routing.generate('fact_client_associe_liste', {client: client_id}));
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                show_info('Erreur', 'Une erreur est survenue.', 'error');
            }
        })
    });

    //Supprimer un client associé
    $(document).on('click', '.js-delete-client-associe', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var client_associe_grid = $(document).find('#js_client_associe');
        var rowid = $(this).closest('tr').attr('id');
        client_associe_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_client_associe_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    //Choiser Type Remise
    $(document).on('click', '#btn-change-type-remise', function (event) {
        event.preventDefault();
    });

    $(document).on('click', '#btn-save-type-remise', function (event) {
        event.preventDefault();
        var client = $('#client').val();
        var type_remise = $('#change-type-remise-form')
            .find('.remise-niveau-select')
            .val();
        $.ajax({
            url: Routing.generate('fact_remise_applique_edit', {client: client, remise: type_remise}),
            type: 'POST',
            success: function (data) {
                data = $.parseJSON(data);
                if (data.erreur == false) {
                    var remise = data.remise;
                    if (remise != null) {
                        $('#type-remise')
                            .text(remise.factRemiseNiveau.libelle)
                            .attr('data-type-remise-id', remise.factRemiseNiveau.id);

                    } else {
                        $('#type-remise')
                            .text('Aucune')
                            .attr('data-type-remise-id', '0');
                    }
                } else {
                    show_info('Erreur', data.erreur_text, 'error');
                }
                console.log(data);
                $('#btn-change-type-remise').popover('hide');
            }
        });
    });

    $('#btn-change-type-remise').on('shown.bs.popover', function () {
        var remise_id = $('#type-remise').attr('data-type-remise-id');
        $('#change-type-remise-form')
            .find('.remise-niveau-select')
            .val(remise_id);
    });


    function getMoisSaisi() {
        var dossier = $('#dossier').val();
        var exercice = $('#exercice').val();
        $('#select-saisi-fini').empty();

        $.ajax({
            url: Routing.generate('fact_saisie_mois_saisi_dossier', {dossier: dossier, exercice: exercice}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);

                var option = '';
                $.each(data, function (index, item) {
                    var mois_text = moment(item.mois_saisi)
                        .format('MMMM-YYYY')
                        .toUpperCase();
                    var mois_value = moment(item.mois_saisi)
                        .format('MM-YYYY');
                    option += '<option value="' + mois_value + '">' + mois_text + '</option>';
                });
                $('#select-saisi-fini').html(option);
            }
        });
    }

    function loadGridClientAssocie() {
        var client_associe_grid = $(document).find('#js_client_associe');
        lastsel_client_associe = null;
        var client = $('#client').val();
        var client_text = $('#client')
            .find('option:selected')
            .text();
        client_associe_grid.jqGrid({
            url: Routing.generate('fact_client_associe_liste', {client: client}),
            datatype: 'json',
            loadonce: true,
            sortable: true,
            autowidth: true,
            height: 200,
            shrinkToFit: true,
            viewrecords: true,
            rowNum: 10,
            rowList: [10, 20, 50],
            caption: 'Clients associés à "' + client_text + '"',
            hidegrid: false,
            colNames: ['Client', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
            colModel: [
                {
                    name: 'client-associe-client',
                    index: 'client-associe-client',
                    editable: false,
                    sortable: true,
                    classes: 'js-client-associe-client'
                },
                {
                    name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                    editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-button js-save-client-associe" title="Enregistrer"></i>'},
                    classes: 'js-client-associe-action'
                }
            ],
            onSelectRow: function (id) {
                if (id && id !== lastsel_client_associe) {
                    client_associe_grid.restoreRow(lastsel_client_associe);
                    lastsel_client_associe = id;
                }
                client_associe_grid.editRow(id, false);
            },
            beforeSelectRow: function (rowid, e) {
                var target = $(e.target);
                var item_action = (target.closest('td').children('.icon-action').length > 0);

                return !item_action;
            },
            loadComplete: function () {
                setTimeout(function () {
                    client_associe_grid.jqGrid("setGridWidth", client_associe_grid.closest(".modal-body").width());
                }, 500);
            },
            ajaxRowOptions: {async: true},
            reloadGridOptions: {fromServer: true}
        });
    }

    function col_prestation_attr(rowId, val, rawObject, cm, rdata)
    {
        var classes = cm.classes;
        if (classes && classes.indexOf('td-import-prestation') >= 0) {
            var array_col = cm.name.split("-");
            if (array_col.length === 2 && array_col[0] === 'controle') {
                return 'data-prestation-code="' + array_col[1] + '"';
            }
        }
        return '';
    }

    function controleImportParDossier() {
        var ajaxs = [],
            lignes = [],
            exercice = $('#exercice').val(),
            mois = $('#btn-saisi-fini').hasClass('btn-primary') ? $('#select-saisi-fini').val() : $('#select-nouveau-saisi').attr('data-selected-date'),
            annee_tarif = $('#annee').val();

        //Charger Contrôle import ligne
        client_id = $('#client').val();
        var url = Routing.generate('fact_controle_import_header', {
            client: client_id,
            exercice: exercice,
            mois: mois,
            annee_tarif: annee_tarif
        });
        fetch(url, {
            method: 'GET',
            credentials: 'include'
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            var cellattrMapping = {
                "col_prestation_attr": col_prestation_attr
            };

            for(var i = 0; i < data.col_models.length; i++) {
                if (data.col_models[i].hasOwnProperty('cellattr')) {
                    data.col_models[i].cellattr = cellattrMapping[data.col_models[i].cellattr];
                }
            }

            controle_grid.jqGrid({
                datatype: 'local',
                loadonce: true,
                sortable: true,
                autowidth: true,
                height: (window_height - 336),
                shrinkToFit: true,
                viewrecords: true,
                rowNum: 5000,
                rowList: [5000, 10000, 150000],
                pager: '#pager_controle',
                hidegrid: false,
                colNames: data.col_names,
                colModel: data.col_models
            });
        }).catch(function (error) {
            console.log(error);
        });

        $('#dossier').find('option').each(function (index, item) {
            // if (index > 15) return;
            if ($(item).text() !== "Tous") {
                var url = Routing.generate('fact_controle_import_dossier', { dossier: $(item).val(), exercice: exercice, mois: mois, annee_tarif: annee_tarif });
                ajaxs.push(
                    $.ajax({
                        url: url,
                        success: function(response) {
                            lignes.push(response);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                );
            }
        });

        $.when.apply($, ajaxs).done(function() {
            lignes.sort(function(a, b) {
                return (a['controle-dossier'] < b['controle-dossier']) ? -1 : 1;
            });
            controle_grid.jqGrid('setGridParam', {data: lignes}).trigger('reloadGrid');
            btn_controle.html('Afficher Récap');
        });
    }


    function activatePopoverRemise() {
        $('#btn-change-type-remise').popover({
            container: '#change-type-remise-form',
            title: 'Type remise à appliquer',
            content: function () {
                return $('#type-remise-form').html();
            },
            html: true,
            placement: 'auto left'
        });
    }

    function showRemiseApplique() {
        var client = $('#client').val();
        $.ajax({
            url: Routing.generate('fact_remise_applique', {client: client}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);
                $('#type-remise')
                    .text(data.libelle)
                    .attr('data-type-remise-id', data.id);
            }
        });
    }

    function currencyFormatter(cellvalue, options, rowObject) {
        if (cellvalue === '') {
            return '';
        } else {
            return numeral(cellvalue).format('0,0.00');
        }
    }

    $(document).on("click", ".jqgrid-tabs a", function () {
        saisie_grid.jqGrid("setGridWidth", saisie_grid.closest(".panel-body").width());
        controle_grid.jqGrid("setGridWidth", controle_grid.closest(".panel-body").width());
    });
});
