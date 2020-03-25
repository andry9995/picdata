/**
 * Created by TEFY on 10/04/2017.
 */

var tableau_client = $('#tableau-client');
var tableau_exercice = $('#tableau-exercice');
var tableau_site = $('#tableau-site');
var tableau_dossier = $('#tableau-dossier');
var modal_categorie = $('#param-categorie-modal');
var modal_periode = $('#param-periode-modal');
var tableau_grid = $('#js_tableau');
var tb_categorie_grid = $('#js_tb_categorie');
var tb_periode_grid = $('#js_tb_periode');
var clicked_cell = null;
var chargement_dossier_en_cours = [];

function destroy_grid(table) {
    if (table[0].grid != undefined) {
        delete table;
        table.jqGrid('GridUnload');
    }
}

function test_security(response)
{
    if(response.trim().toLowerCase() === 'security') location.reload();
}

function clearGrid(selector) {
    var trf = selector.find("tbody:first tr:first")[0];
    selector.find("tbody:first").empty().append(trf);
}

function getSites(client_selector, site_selector, dossier_selector, exercice_selector, callback) {
    var client = client_selector.val();
    var dossier_id = null;
    if  (dossier_selector) {
        dossier_id = dossier_selector.attr('id');
    }
    site_selector.empty();
    if (dossier_selector) {
        dossier_selector.empty();
    }

    var showTous = true;


        //Tester-na ny nbre-an'ny dossier
        $.ajax({
            url: Routing.generate('app_dossiers', {client: client, site: 0, conteneur: 1, tous: 0, tdi: 1}),
            data: {
                exercice: $('#tableau-exercice').val()
            },
            type: 'GET',
            async: false,
            success: function (data) {
                data = $.parseJSON(data);
                if (data.length >= 150) {
                    showTous = false;
                }

                $.ajax({
                    url: Routing.generate('app_sites', {'conteneur': 1, 'client': client}),
                    type: 'GET',
                    data: {},
                    success: function (data) {
                        data = $.parseJSON(data);
                        var tous = '<option value="0">Tous</option>';

                        var single = false;
                        site_selector.closest('.form-group')
                            .find('.label.label-warning')
                            .text(data.length.toString());

                        if (data.length <= 1) {
                            site_selector.attr('disabled', 'disabled');
                            single = true;
                        } else {
                            site_selector.removeAttr('disabled');

                            if(showTous)
                                site_selector.html(tous);
                        }

                        var options = '';
                        if (data instanceof Array) {
                            $.each(data, function (index, item) {
                                if (single) {
                                    options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
                                } else {
                                    options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
                                }
                            });
                            site_selector.append(options);
                        } else {
                            return 0;
                        }
                        if (!chargement_dossier_en_cours[dossier_id] && dossier_selector) {
                            chargement_dossier_en_cours[dossier_id] = true;
                            getDossiers(client, site_selector, dossier_selector, exercice_selector, dossier_id, callback);
                            return 0;
                        }
                        if (typeof callback === 'function') {
                            callback();
                        }
                    }
                });
            }
        });



}

function getDossiers(client, site_selector, dossier_selector, exercice_selector, dossier_id, callback) {
    var site = site_selector.val();
    var now = new Date();
    var current_year = now.getFullYear();
    var exercice = typeof exercice_selector !== 'undefined' && exercice_selector != null ? exercice_selector.val() : current_year;
    dossier_selector.empty();
    var url = Routing.generate('app_dossiers', {client: client, site: site, conteneur: 1, tdi: 1});
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            exercice: exercice
        },
        success: function (data) {
            data = $.parseJSON(data);
            var tous = '<option value="0">Tous</option>';
            var single = false;

            dossier_selector.closest('.form-group')
                .find('.label.label-warning')
                .text(data.length.toString());

            if (data.length <= 1) {
                single = true;
            } else {
                dossier_selector.html(tous);
            }

            var options = '';
            if (data instanceof Array) {
                $.each(data, function (index, item) {
                    if (single) {
                        options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
                    } else {
                        options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
                    }
                });
                dossier_selector.append(options);
            } else {
                chargement_dossier_en_cours[dossier_id] = false;
                return 0;
            }
            chargement_dossier_en_cours[dossier_id] = false;
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

/**
 * cocher sur checkbox sur entete jqGrid
 * Empecher de tri des colonnes
 *
 * @param event
 */
function checkHeaderClick(event) {
    event.stopPropagation();
}

function setCheckAll(headerCheckbox, checkClass) {
    var value = true;
    $(document).find(checkClass + '>input[type="checkbox"]').each(function (index, item) {
        if ($(item).prop('checked') === false) {
            value = false;
            headerCheckbox.prop('checked', value);
            return 0;
        }
    });
    headerCheckbox.prop('checked', value);
}

//Effacer demarrage tout
function resetDemarrage(event) {
    event.stopPropagation();

    swal({
        title: 'Attention',
        text: "Voulez-vous effacer la date de démarrage de tous les dossiers  ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1ab394',
        cancelButtonColor: '#f8ac59',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
    }).then(function () {
        //Si OK
        var field = 'Demarrage';
        saveParamPeriodeTous(field, '', function () {
            modal_periode
                .find('.js-tb-periode-demarrage')
                .text('');
        });
    });

}

//Effacer première cloture tout
function resetPremiereCloture(event) {
    event.stopPropagation();
    swal({
        title: 'Attention',
        text: "Voulez-vous effacer la date de première cloture de tous les dossiers  ?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1ab394',
        cancelButtonColor: '#f8ac59',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
    }).then(function () {
        //Si OK
        var field = 'PremiereCloture';
        saveParamPeriodeTous(field, '', function () {
            modal_periode
                .find('.js-tb-periode-cloture')
                .text('');
        });
    });
}

/**
 * Modification paramètre periode dossier
 * @param rowid
 */
function saveParametrePeriode(rowid) {
    var tmp = rowid.split("_");
    var dossier_id = 0;
    if (tmp.length === 2) {
        dossier_id = tmp[1];
    }

    tb_periode_grid.jqGrid('setGridParam', {
        'editurl': Routing.generate('tableau_image_periode_edit', {dossier: dossier_id})
    });
    tb_periode_grid.jqGrid('saveRow', rowid);
}

/**
 * Modification paramètre période Tous
 * @param field
 * @param value
 * @param callback
 */
function saveParamPeriodeTous(field, value, callback) {
    var client = tableau_client.val();
    var site = tableau_site.val();
    var exerice = tableau_exercice.val();
    $.ajax({
        url: Routing.generate('tableau_image_periode_edit_all', {client: client, site: site, exercice: exerice}),
        type: 'POST',
        data: {
            field: field,
            value: value
        },
        success: function (data) {
            if (typeof callback === 'function') {
                callback();
            }
            console.log(data);
        }
    })
}

/**
 * Show single image
 * @param event
 */
function showImage(event) {
    event.preventDefault();
    var row = $(event.target).closest('tr');
    var rowId = row.attr('id');
    show_image_pop_up(rowId);
}

/**
 * Affichage détail image par cellule
 */
$(document).on('click', '.show-detail-image', function (event) {
    event.preventDefault();
    var detail_image_grid = $('#js_tb_detail_image');
    clearGrid(detail_image_grid);

    var row = $(event.target).closest('tr');
    var rowId = row.attr('id'),
        pos = $(event.target).attr('data-pos');
    var month_index = pos - 10,
        split = rowId.split('___');
    if (split.length > 1) {
        var row_type = split[0];
        if (row_type === 'categorie') {
            //Images par catégorie
            var categorie_id = split[1],
                categorie_code = split[2],
                dossier_id = split[3],
                debut = split[4];
            if (month_index >= 0) {
                //Image par catégorie par mois
                var rowData = $('#js_tableau').jqGrid('getRowData', rowId);
                var cat = rowData['tableau-dossier'];
                var nom_dossier = $(cat).attr('data-dossier');

                var html = row.find('td.js-tableau-periode').html();
                var tmp = $(html).attr('data-props');
                var props = $.parseJSON(tmp);
                var banque_id = '';
                if (typeof props['banque_id'] !== 'undefined') {
                    banque_id = props['banque_id'];
                }

                var typedate = $('#tableau-type-date').val();

                $.ajax({
                    url: Routing.generate('tableau_detail_image', { dossier: props["dossier_id"], exercice: props["exercice"], typedate: typedate}),
                    type: 'GET',
                    data: {
                        categorie: props["categorie_id"],
                        banque_id: banque_id,
                        mois: moment(props["debut"]).add(month_index, 'M').format("YYYY-MM-DD")
                    },
                    success: function (data) {
                        data = $.parseJSON(data);
                        var rowData = data.images;
                        detail_image_grid = $('#js_tb_detail_image');
                        detail_image_grid.jqGrid('GridUnload');
                        detail_image_grid = $('#js_tb_detail_image');

                        detail_image_grid.jqGrid({
                            datatype: 'local',
                            data: rowData,
                            loadonce: true,
                            sortable: false,
                            height: 320,
                            width: 860,
                            shrinkToFit: true,
                            viewrecords: true,
                            hidegrid: false,
                            rownumbers: true,
                            rownumWidth: 35,
                            colNames: ['Image', 'Catégorie', 'Date scan', 'Date pièce', 'Période déb.', 'Période fin', 'RS', 'Avancement'],
                            colModel: [
                                { name: 'tb-detail-image', index: 'tb-detail-image', align: 'center', editable: false, sortable: true, width: 100, fixed: true, classes: 'js-tb-detail-image'},
                                { name: 'tb-detail-categorie', index: 'tb-detail-categorie', align: 'center', editable: false, sortable: true, width: 110, fixed: true, hidden: true, classes: 'js-tb-detail-categorie'},
                                { name: 'tb-detail-datescan', index: 'tb-detail-datescan', align: 'center', editable: false, sortable: true, width: 80, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-datescan'},
                                { name: 'tb-detail-datepiece', index: 'tb-detail-datepiece', align: 'center', editable: false, sortable: true, width: 80, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-datepiece'},
                                { name: 'tb-detail-periode-debut', index: 'tb-detail-periode-debut', align: 'center', editable: false, sortable: true, width: 80, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-periode-debut'},
                                { name: 'tb-detail-periode-fin', index: 'tb-detail-periode-fin', align: 'center', editable: false, sortable: true, width: 80, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-periode-fin'},
                                { name: 'tb-detail-rs', index: 'tb-detail-rs', align: 'left', editable: false, sortable: true, classes: 'js-tb-detail-rs'},
                                { name: 'tb-detail-avancement', index: 'tb-detail-avancement', align: 'center', editable: false, sortable: true, width: 90, fixed: true,  classes: 'js-tb-detail-avancement'}
                            ]
                        });
                        var modal_title = nom_dossier + ' - ' + $(cat).text() + ' - ' +
                            moment(debut).add(month_index, 'M').format("MMMM YYYY");
                        $('#detail-image-modal-title').text(modal_title);
                        $('#detail-image-modal').modal('show');
                    }
                });
            }
        }
    }

});

//Set Image 0
$(document).on('click', '.set-image-zero', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var cell = $(event.target);
    clicked_cell = cell;
    var pos = cell.attr('data-pos');
    var month_index = pos - 10;
    var with_retard = cell.hasClass('with-retard');

    if (with_retard) {
        $('#image-zero-attente').prop('checked', true);
    } else {
        $('#image-zero-null').prop('checked', true);
    }
    var row = cell.closest('tr');
    var rowId = row.attr('id');
    var rowData = $('#js_tableau').jqGrid('getRowData', rowId);
    var cat = rowData['tableau-dossier'];
    var nom_dossier = $(cat).attr('data-dossier');

    var html = row.find('td.js-tableau-periode').html();
    var tmp = $(html).attr('data-props');
    var props = $.parseJSON(tmp);
    var banque_id = '';
    if (typeof props['banque_id'] !== 'undefined') {
        banque_id = props['banque_id'];
    }
    var mois = '';
    $('#image-zero-mois').val('');
    if (typeof props['debut'] !== 'undefined') {
        mois = moment(props['debut']).add(month_index, 'M').format('MMM YYYY');
        $('#image-zero-mois').val(moment(props['debut']).add(month_index, 'M').format('YYYY-MM-DD'));
    }
    $('#image-zero-dossier').val(props['dossier_id']);
    $('#image-zero-categorie').val(props['categorie_id']);
    $('#image-zero-exercice').val(props['exercice']);
    if (typeof props['banque_id'] !== 'undefined') {
        $('#image-zero-banque').val(props['banque_id']);
    } else {
        $('#image-zero-banque').val('');
    }

    $('#image-zero-modal-title').html(nom_dossier + ' - ' + cat + ' - ' + mois);
    $('#image-zero-modal').modal('show');
});

//Enregistrer modif Image Zero
$(document).on('click', '#btn-save-image-zero', function (event) {
    event.preventDefault();
    var dossier = $('#image-zero-dossier').val(),
        exercice = $('#image-zero-exercice').val(),
        categorie = $('#image-zero-categorie').val(),
        mois = $('#image-zero-mois').val(),
        banque = $('#image-zero-banque').val(),
        status = $('input[name="image-zero"]:checked').val();
    $.ajax({
        url: Routing.generate('tableau_image_zero_edit', { dossier: dossier, exercice: exercice, categorie: categorie, mois: mois}),
        type: 'POST',
        data: {
            banque: banque,
            status: status
        },
        success: function () {
            $('#image-zero-modal').modal('hide');
            if (clicked_cell !== null) {
                if (status === '1') {
                    clicked_cell.text('xxx').addClass('with-retard').attr('title', 'xxx');
                    clicked_cell.closest('td').css({'text-align':'center', 'background':'#acb9ca', 'color':'transparent'});
                } else {
                    clicked_cell.text('0').removeClass('with-retard').attr('title', '0');
                    clicked_cell.closest('td').css({'text-align':'center','font-size':'9px','background':'none', 'color':'#676a6c'});
                }
            }
        }
    });
});

//Status dossier
$(document).on('click', '.edit-dossier-status', function(event) {
    event.preventDefault();
    event.stopPropagation();

    var rowId = $(event.target).closest('tr').attr('id');
    var dossier_nom = $(event.target).text(),
        dossier_id = 0,
        exercice = $('#tableau-exercice').val();
    var split = rowId.split('_');
    if (split.length > 1) {
        dossier_id = split[1];
    }
    $('#status-dossier-id').val(dossier_id);
    $('#status-dossier-modal-title').text('Dossier : ' + dossier_nom + ' (' + exercice + ')');
    $('#dossier-status-check').find('input[type="checkbox"]').prop('checked', false);

    if ($(event.target).closest('tr').hasClass('dossier-fini')) {
        $('#status-dossier-termine').prop('checked', true);
    } else if ($(event.target).closest('tr').hasClass('dossier-attente-reponse')) {
        $('#status-dossier-attente-reponse').prop('checked', true);
    } else if ($(event.target).closest('tr').hasClass('dossier-non-traitable')) {
        $('#status-dossier-non-traitable').prop('checked', true);
    }


    $('#status-dossier-modal').modal('show');
});

//Choix Status dossier
$(document).on('change', '#dossier-status-check input[type="checkbox"]', function() {
    var input = $(this);
    var checked = input.prop('checked');
    if (checked) {
        $('#dossier-status-check').find('input[type="checkbox"]').prop('checked', false);
        input.prop('checked', true);
    }
});

//Enregistrer Status dossier
$(document).on('click', '#btn-save-status-dossier', function(event) {
   event.preventDefault();
   var status = 0,
       dossier_id = $('#status-dossier-id').val(),
       exercice = tableau_exercice.val();

   if ($('#dossier-status-check').find('input[type="checkbox"]:checked').length > 0) {
       status = $('#dossier-status-check').find('input[type="checkbox"]:checked').val();
   }
   $.ajax({
       url: Routing.generate('tableau_image_dossier_status_edit', {dossier:dossier_id, exercice:exercice, status:status}),
       type: 'POST',
       success: function(data) {
           console.log(data);
           $('#status-dossier-modal').modal('hide');
           if (status === 0) {
               tableau_grid.find('.row_' + dossier_id)
                   .removeClass('dossier-fini dossier-attente-reponse dossier-non-traitable');
           } else if (status === '1') {
               tableau_grid.find('.row_' + dossier_id)
                   .removeClass('dossier-attente-reponse dossier-non-traitable')
                   .addClass('dossier-fini');
           } else if (status === '4') {
               tableau_grid.find('.row_' + dossier_id)
                   .removeClass('dossier-fini dossier-non-traitable')
                   .addClass('dossier-attente-reponse');
           } else if (status === '9') {
               tableau_grid.find('.row_' + dossier_id)
                   .removeClass('dossier-fini dossier-attente-reponse')
                   .addClass('dossier-non-traitable');
           }
       }
   })
});

/**
 * Afficher image encours
 * @param event
 */
function showEncours(event) {
    event.preventDefault();
    var row = $(event.target).closest('tr');
    var rowId = row.attr('id');
    var split = rowId.split('___');
    console.log(rowId);
    if (split.length > 2) {
        var dossier = split[1],
            nom_dossier = split[2],
            exercice = $(event.target).attr('data-exercice');
        $.ajax({
            url: Routing.generate('tableau_detail_encours', { dossier: dossier, exercice: exercice}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);
                var rowData = data.images;
                var detail_image_grid = $('#js_tb_detail_image');
                detail_image_grid.jqGrid('GridUnload');
                detail_image_grid = $('#js_tb_detail_image');

                detail_image_grid.jqGrid({
                    datatype: 'local',
                    data: rowData,
                    loadonce: true,
                    sortable: false,
                    height: 320,
                    width: 860,
                    shrinkToFit: true,
                    // autowidth: true,
                    viewrecords: true,
                    hidegrid: false,
                    rownumbers: true,
                    rownumWidth: 35,
                    colNames: ['Image', 'Catégorie', 'Date scan', 'Date pièce', 'Période début', 'Période fin', 'RS'],
                    colModel: [
                        { name: 'tb-detail-image', index: 'tb-detail-image', align: 'center', editable: false, sortable: true, width: 100, fixed: true, classes: 'js-tb-detail-image'},
                        { name: 'tb-detail-categorie', index: 'tb-detail-categorie', align: 'center', editable: false, sortable: true, width: 110, fixed: true, classes: 'js-tb-detail-categorie'},
                        { name: 'tb-detail-datescan', index: 'tb-detail-datescan', align: 'center', editable: false, sortable: true, width: 100, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-image'},
                        { name: 'tb-detail-datepiece', index: 'tb-detail-datepiece', align: 'center', editable: false, sortable: true, width: 100, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-image'},
                        { name: 'tb-detail-periode-debut', index: 'tb-detail-periode-debut', align: 'center', editable: false, sortable: true, width: 100, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-periode-debut'},
                        { name: 'tb-detail-periode-fin', index: 'tb-detail-periode-fin', align: 'center', editable: false, sortable: true, width: 100, fixed: true, sorttype: 'date', formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y', classes: 'js-tb-detail-periode-fin'},
                        { name: 'tb-detail-rs', index: 'tb-detail-rs', align: 'left', editable: false, sortable: true, classes: 'js-tb-detail-image'}
                    ]
                });
                var modal_title = 'Encours: ' + nom_dossier + ' - ' + exercice;
                $('#detail-image-modal-title').text(modal_title);
                $('#detail-image-modal').modal('show');
            }
        });
    }
}

/**
 * Formatter cellule jqGrid comme Badge bootstrap
 *
 * @return {string}
 */
function BootstrapBadgeFormatter(cellvalue, options, rowObject) {
    if (typeof cellvalue !== 'undefined') {
        if (options.rowId.indexOf('header') !== -1) {
            return '<span class="badge">' + cellvalue.toString() + '</span>';
        } else {
            return cellvalue;
        }
    } else {
        return '';
    }
}

/**
 * Formatter cellule jqGrid comme Label bootstrap
 *
 * @return {string}
 */
function BootstrapLabelFormatter(cellvalue, options, rowObject) {
    if (typeof cellvalue !== 'undefined') {
        if (options.rowId.indexOf('header') !== -1) {
            return '<span class="label label-default">' + cellvalue.toString() + '</span>';
        } else {
            return cellvalue;
        }
    } else {
        return '';
    }
}

/**
 * Format cell selon value
 *
 * @param cellvalue
 * @param options
 * @param rowObject
 * @returns {*}
 */
function cellClickableFormatter(cellvalue, options, rowObject) {
    // console.log(options);
    var rowId = options.rowId;
    var pos = options.pos;
    if (options.rowId.indexOf('total') === -1) {
        if (typeof cellvalue === 'undefined') {
            return '';
        }
        if (cellvalue !== '' && cellvalue !== 'xxx' && cellvalue !== 0) {
            return '<a href="#" data-pos="' + pos + '" class="show-detail-image" >' + cellvalue + '</a>';
        } else if (cellvalue === 0) {
            return '<a href="#" data-pos="' + pos + '" class="set-image-zero" >' + cellvalue + '</a>';
        } else if (cellvalue === 'xxx') {
            return '<a href="#" data-pos="' + pos + '" class="set-image-zero with-retard" >' + cellvalue + '</a>';
        } else {
            return cellvalue;
        }
    } else {
        if (cellvalue !== '0' && cellvalue !== 0) {
            return cellvalue;
        } else {
            return '';
        }
    }
}

/**
 * Format row
 *
 * @param rowId
 * @param val
 * @param rawObject
 * @param cm
 * @param rdata
 * @returns {*}
 */
function dossierHeaderAttr(rowId, val, rawObject, cm, rdata) {

    if (rowId.indexOf('header') !== -1) {
        //HEADER DOSSIER
        if (cm.name === 'tableau-dossier') {
            return ' style="font-weight: bold;font-size:10px;border-top: 1px solid #DDD;color: #676a6c;"';
        } else {
            return ' style="font-weight: bold;font-size:9px;border-top: 1px solid #DDD;color: #676a6c;"';
        }
    } else if (rowId.indexOf('total') !== -1) {
        //TOTAL DOSSIER
        return ' style="font-weight: bold;font-size:9px;border-bottom: 1px solid #DDD;"';
    } else if (rowId.indexOf('separator') !== -1) {
        //SEPARATEUR
        if (cm.name === 'tableau-periode') {
            return ' colspan="30"';
        } else {
            return ' style="display:none;"';
        }
    } else if (rowId.indexOf('categorie') !== -1) {
        //MOIS EN RETARD
        try {
            var month_cols = ['tableau-m1', 'tableau-m2', 'tableau-m3', 'tableau-m4', 'tableau-m5', 'tableau-m6', 'tableau-m7', 'tableau-m8', 'tableau-m9',
                'tableau-m10', 'tableau-m11', 'tableau-m12', 'tableau-m13', 'tableau-m14', 'tableau-m15', 'tableau-m16', 'tableau-m17', 'tableau-m18',
                'tableau-m19', 'tableau-m20', 'tableau-m21', 'tableau-m22', 'tableau-m23', 'tableau-m24'];
            if (typeof val !== 'undefined' && val.indexOf('xxx') !== -1) {
                if (month_cols.indexOf(cm.name) !== -1) {
                    return ' style="background:#acb9ca;color:transparent;"';
                } else {
                    return ' style="font-size:9px;color: #676a6c;"';
                }
            } else {
                if (cm.name === 'tableau-dossier') {
                    if (typeof rdata["tableau-periode"] !== 'undefined') {
                        if (typeof $(rdata["tableau-periode"]).attr('data-props') !== 'undefined') {
                            var props = $.parseJSON($(rdata["tableau-periode"]).attr('data-props'));
                            if (typeof props.num_compte !== 'undefined') {
                                var num_compte = props.num_compte;
                                return ' title="' + num_compte + '" style="font-size:9px;color: #676a6c;"';
                            }
                        }
                    }
                }
                return ' style="font-size:9px;color: #676a6c;"';
            }
        }
        catch(error) {
            console.error(error);
        }

    } else {
        return ' style="font-size:9px;color: #676a6c;"';
    }
}