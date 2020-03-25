$(document).ready(function(){
    charger_site();
    $('#exercice').val((new Date()).getFullYear());
    dossier_depend_exercice = true;

    var client_selector = $('#client'),
        dossier_selector = $('#dossier'),
        lastsel_drt;

    $('#js_debut_date').datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true});
    $('#js_fin_date').datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true});

    // Changement client
    $(document).on('change', '#client', function ()
    {
        $("#col-drt").hide();
    });

    $(document).on('change', '#dossier', function()
    {
        $("#col-drt").hide();
        $('#js_drt_liste').jqGrid("clearGridData");
        go();
    });

    $(document).on('change', '#filtre_chrono', function()
    {
        $('#js_drt_liste').jqGrid("clearGridData");
        $("#col-drt").hide();
        if($(this).val() === '7'){
            $('#js-filtre-fourchette').modal('show');
        }else{
            go();
        }
    });

    $(document).on('change', '#exercice', function()
    {
        $("#col-drt").hide();
    });


    $(document).on('change', '#statut', function()
    {
        $('#js_drt_liste').jqGrid("clearGridData");
        $("#col-drt").hide();
        go();
    });

    $(document).on('click', '#add-drt-echange', function () {
        $('#drt-add-modal').modal('show');
    });

    $(document).on('click', '.js-drt-action', function () {
        $('#drt-repondre-modal').modal('show');
    });

    $(document).on('click', '.t-upload-drt', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var drt_grid = $('#js_drt_liste');
        var rowKey = drt_grid.jqGrid('getGridParam',"selrow");
        var drt = drt_grid.jqGrid('getCell',rowKey,'t-drt');
        if(drt !== ''){
            var numero_drt = drt.split(' ')[3];
            var dossier = drt_grid.jqGrid('getCell',rowKey,'t-dossierId');
            var exercice = $('#exercice').val();
            var echangeType = $(document).find('input[name="show-filter-item"]:checked').val();
            location.href = Routing.generate('drt_upload_file', {dossier : dossier, exercice: exercice, echangeType: echangeType, numero_drt: numero_drt, numero_reponse: 0}, true);
        }
        return false;
    });

    $(document).on('click', '.t-upload-rdrt', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var drt_grid = $('#js_drt_liste');
        var rowKey = drt_grid.jqGrid('getGridParam',"selrow");
        var rdrt = drt_grid.jqGrid('getCell',rowKey,'t-reponse');
        if(rdrt !== ''){
            var numero_drt = rdrt.split(' ')[3];
            var dossier = drt_grid.jqGrid('getCell',rowKey,'t-dossierId');
            var exercice = $('#exercice').val();
            var numero_rep = rdrt.split(' ')[0];
            var numero_reponse = numero_rep.split('-')[0];
            var echangeType = $(document).find('input[name="show-filter-item"]:checked').val();
            location.href = Routing.generate('drt_upload_file', {dossier: dossier, exercice: exercice, echangeType: echangeType, numero_drt: numero_drt, numero_reponse: numero_reponse[1]}, true);
        }
        return false;
    });

    $(document).on('change', '.t-statut', function(event) {
        event.preventDefault();
        event.stopPropagation();
        /*var statut = $('.editable').val();*/
        var grid = $("#js_drt_liste");
        var rowKey = grid.jqGrid('getGridParam',"selrow");
        var statut = $('.editable').val();
        var drt = grid.jqGrid('getCell',rowKey,'t-drt');
        var numero = drt.split(' ')[3];
        var dossier = grid.jqGrid('getCell',rowKey,'t-dossierId');
        saveDrtAction(dossier, numero, statut);
    });

    $(document).on('change', 'input[name="show-filter-item"]', function() {
        go();
    });

    $(document).on('click', '#btn-supprime-drt', function () {
        $('#drt-supprime-modal').modal('hide');
        var drt_grid = $('#js_drt_liste');
        var rowKey = drt_grid.jqGrid('getGridParam',"selrow");
        var rdrt = drt_grid.jqGrid('getCell',rowKey,'t-reponse');
        var numero_reponse = null;
        var numero_drt;
        if(rdrt !== ''){
            var numero_rep = rdrt.split(' ')[0];
            numero_reponse = numero_rep.split('-')[0];
            numero_reponse = numero_reponse[1];
            numero_drt = rdrt.split(' ')[3];
        }else{
            var drt = drt_grid.jqGrid('getCell',rowKey,'t-drt');
            numero_drt = drt.split(' ')[3];
        }
        var dossier = drt_grid.jqGrid('getCell',rowKey,'t-dossierId');
        var exercice = $('#exercice').val();
        var echangeType = $(document).find('input[name="show-filter-item"]:checked').val();
        var drtOrRdrt = $(this).attr('data-drt-rdrt');
        var url = Routing.generate('drt_delete');
        $.ajax({
            url:url,
            type: "POST",
            dataType: "json",
            data: {
                dossier: dossier,
                exercice: exercice,
                numero_drt: numero_drt,
                echangeType: echangeType,
                drtOrRdrt: drtOrRdrt,
                numero_reponse: numero_reponse
            },
            async: true,
            success: function (data)
            {
                if(data.status === 'delete_ok'){
                    show_info('Suppresssion DRT',  data.echange_type+' supprimée avec succès');
                    go();
                }
            }
        });
    });

    $(document).on('click', '#btn-fourchette-drt', function () {
        var periodeDeb = $("#js_debut_date").val(),
            periodeFin = $("#js_fin_date").val();
        if ( periodeDeb ==  '' || periodeFin == '') {
            show_info('Champ Fourchette Invalide', 'Veuillez Remplir les Dates', 'info');
            return false;
        }
        $('#js-filtre-fourchette').modal('hide');
        var perioDeb = periodeDeb.split("-"),
            perioFin = periodeFin.split("-");
        var dateDeb = perioDeb[2] + '-' + perioDeb[1] + '-' + perioDeb[0],
            dateFin = perioFin[2] + '-' + perioFin[1] + '-' + perioFin[0];
        $('.data_deb').attr('data', dateDeb);
        $('.data_fin').attr('data', dateFin);

        go();
    });

    $(document).on('click', '.tab-echange-type', function() {
        go();
    });

    $(document).on('click','.statut_stat',function(){
        if ($(this).hasClass('white-bg')) $(this).removeClass('white-bg');
        else $(this).addClass('white-bg');
        hide_statut_stat();
    });

    var mydata = [],
        height_erreur_jqgrid,
        width_erreur_jqgrid;

    $("#table_list_error_import").jqGrid({
        data: mydata,
        datatype: "local",
        shrinkToFit: true,
        width: width_erreur_jqgrid,
        rowList: [10, 20, 30],
        colNames: ['Nom fichier', 'Dossier Similaire', 'Etat', 'File'],
        colModel: [
            {name: 'name', index: 'name', width: 100},
            {name: 'dossier_similaire', index: 'name', width: 100},
            {name: 'etat', index: 'etat', width: 100},
            {
                name: 'fileToUpload',
                index: 'customer_id',
                align: 'left',
                editable: true,
                edittype: 'file',
                editoptions: {
                    enctype: "multipart/form-data"
                },
                width: 210,
                search: false
            }
        ],
        pager: "#pager_list_error_import",
        hidegrid: false,
        viewrecords: true,
        loadComplete: function () {
        }
    });
});

function after_charged_dossier_not_select() {
    if ($('#dossier option:selected').text().trim() === '')
    {
        $('#dossier option:selected').text('Tous')
    }
    $('.filtre-drt').show();
    $('#show-radio-filter-item').show();
    go();
}

function showListDrtDossier(dossierId, chronoId, exercice, echangeType, dossierOrChrono) {
    var lastsel_drt,
        colModel,
        colNames,
        drt_grid = $('#js_drt_liste'),
        nomEchangeType = $(document).find('input[name="show-filter-item"]:checked').attr('id'),
        dateDeb,
        dateFin;

    drt_grid.jqGrid('GridUnload');
    drt_grid = $('#js_drt_liste');

    if(chronoId === '7') {
        dateDeb = $('.data_deb').attr('data');
        dateFin = $('.data_fin').attr('data');
    }else {
        dateDeb = null;
        dateFin = null;
    }

    var lien = Routing.generate('drt_get_list');
    var idata = {};
    if(dossierOrChrono) {
        colNames = ['', 'Date envoi', 'Attente(Jour)', 'Statut', 'Dossier',/*'',*/  nomEchangeType.split('-')[3], '<i class="fa fa-download" aria-hidden="true"></i>', 'Message', 'R-'+nomEchangeType.split('-')[3], '<i class="fa fa-download" aria-hidden="true"></i>', 'Message', 'Date envoi', 'Actions', 'dossierId', 'statut_stat'];
        colModel = [
            {name: 't-index', editable: false, sortable: true, sorttype: 'number', width: 20, align: 'left', classes: 't-index'},
            {name: 't-e-date-envoi',editable: false, sortable: true, width: 100,align: 'center', classes: 't-e-date-envoi-chrono'},
            {name: 't-attente', editable: false, sortable: true, sorttype: 'number', width: 50, align: 'left', classes: 't-attente'},
            {name: 't-statut', width: 100, editable: true, fixed: true, edittype:"select", sortable: true, align: 'center', classes: 't-statut'},
            {name: 't-dossier', editable: false, sortable: true, width: 150, align: 'left', classes: 't-dossier'},
            //{name: 't-x', editable: false, width: 30, align: 'center', classes: 'cl_analyser pointer', formatter:function(v){ return analyse_formatter(v) } },
            {name: 't-drt', editable: false, sortable: true, width: 200, align: 'left', classes: 't-drt'},
            {name: 't-upload-drt', editable: false, sortable: false, width: 30, align: 'center', classes: 'pointer t-upload-drt'},
            {name: 't-message-drt', editable: false, width: 200, align: 'left', classes: 't-message-drt'},
            {name: 't-reponse', editable: false, sortable: true, width: 200, align: 'left', classes: 't-reponse'},
            {name: 't-upload-rdrt', editable: false, sortable: false, width: 20, align: 'center', classes: 'pointer t-upload-rdrt'},
            {name: 't-message-rdrt', editable: false, width: 200, align: 'left', classes: 't-message-rdrt'},
            {name: 't-reponse-date', editable: false,sortable: true, width: 100, align: 'center', classes: ''},
            {name: 't-actions', title: false, editable: false, fixed: true, sortable: false, width: 100, align: 'center', classes: 't-actions', formatter: 'actionFormatter'},
            {name: 't-dossierId', editable: false,sortable: false, width: 1, align: 'center', classes: 't-dossierId'},
            {name: 't-statut-stat', editable: false,sortable: false, width: 1, align: 'center', classes: 't-statut-stat'}
        ];
    }else{
        colNames = ['', /*'',*/ nomEchangeType.split('-')[3], '<i class="fa fa-download" aria-hidden="true"></i>', 'Date création', 'Attente(Jour)', 'Statut', 'Message', 'Réponse', '<i class="fa fa-download" aria-hidden="true"></i>', 'Message', 'Date envoi', 'Actions', 'dossierId', 'statut_stat'];
        colModel = [
            {name: 't-index', editable: false, sortable: true, sorttype: 'number', width: 20, align: 'left', classes: 't-index'},
            //{name: 't-x', editable: false, width: 30, align: 'center', classes: 'cl_analyser pointer', formatter:function(v){ return analyse_formatter(v) } },
            {name: 't-drt', editable: false, sortable: true, width: 200, align: 'left', classes: 't-drt'},
            {name: 't-upload-drt', editable: false, sortable: false, width: 20, align: 'center', classes: 'pointer t-upload-drt'},
            {name: 't-e-date-envoi',editable: false, sortable: true, width: 100,align: 'center', classes: ''},
            {name: 't-attente', editable: false, sortable: true, sorttype: 'number', width: 50, align: 'left', classes: 't-attente'},
            {name: 't-statut', width: 100, editable: true, fixed: true, edittype:"select", sortable: true, align: 'center', classes: 't-statut'},
            {name: 't-message-drt', editable: false, width: 200, align: 'left', classes: 't-message-drt'},
            {name: 't-reponse', editable: false, sortable: true, width: 200, align: 'left', classes: 't-reponse'},
            {name: 't-upload-rdrt', editable: false, sortable: false, width: 20, align: 'center', classes: 'pointer t-upload-rdrt'},
            {name: 't-message-rdrt', editable: false, width: 200, align: 'left', classes: 't-message-rdrt'},
            {name: 't-reponse-date', editable: false,sortable: true, width: 100, align: 'center', classes: ''},
            {name: 't-actions', title: false, editable: false, fixed: true, sortable: false, width: 100, align: 'center', classes: 't-actions', formatter: 'actionFormatter'},
            {name: 't-dossierId', editable: false,sortable: false, width: 1, align: 'center', classes: 't-dossierId'},
            {name: 't-statut-stat', editable: false,sortable: false, width: 1, align: 'center', classes: 't-statut-stat'}
        ];
    }
    idata['dossierOrChrono'] = dossierOrChrono;
    idata['exercice'] = exercice;
    idata['statut'] = $('#statut').val();
    idata['dossier'] = dossierId;
    idata['chrono'] = chronoId;
    idata['chronoDeb'] = dateDeb;
    idata['chronoFin'] = dateFin;
    idata['client'] = $('#client').val();
    idata['echangeType'] = echangeType;

    drt_grid.jqGrid({
        url: lien,
        postData: {"idata": JSON.stringify(idata)},
        mtype: 'POST',
        datatype: 'json',
        rownumbers: false,
        viewrecords: true,
        autowidth: true,
        hidegrid: false,
        shrinkToFit: true,
        loadonce: true,
        sortable: true,
        height: window.innerHeight - 235,
        rowList: [100, 200, 500],
        altRows: true,
        pager: '#pager_liste_impute',
        colNames: colNames,
        colModel: colModel,
        multiselect: false,
        onSelectRow: function(id){
            if(id && id!==lastsel_drt){
                drt_grid.jqGrid('restoreRow',lastsel_drt);
                var val_t_statut = drt_grid.jqGrid('getCell',id,'t-statut');
                var cm = drt_grid.jqGrid('getColProp','t-statut');
                var rowdata = drt_grid.getRowData(id);
                if (val_t_statut !== '') {
                    cm.editable = true;
                    if(val_t_statut === 'Clôturée'){
                        drt_grid.jqGrid('setColProp', 't-statut', {
                            editoptions: {
                                value:"1:Clôturée; 4:Réouverte"
                            }
                        });
                    }else if(val_t_statut === 'Ouverte'){
                        drt_grid.jqGrid('setColProp', 't-statut', {
                            editoptions: {
                                value:"0:Ouverte; 1:Clôturée"
                            }
                        });
                    }else if(val_t_statut === 'En cours'){
                        drt_grid.jqGrid('setColProp', 't-statut', {
                            editoptions: {
                                value:"5:En cours; 1:Clôturée"
                            }
                        });
                    }else{
                        drt_grid.jqGrid('setColProp', 't-statut', {
                            editoptions: {
                                value:"0:Ouverte; 1:Clôturée; 2:Partielle"
                            }
                        });
                    }

                } else {
                    cm.editable = false;
                }
                drt_grid.jqGrid('editRow',id,true);
                lastsel_drt=id;
            }
        },
        loadComplete: function (data) {
            $('.repondre-echange-title').html('Répondre à la ' + nomEchangeType.split('-')[3]);
            $('.ajout-nouvelle-echange').html('Nouvelle demande');
            $('.supprime-echange-title').html('Supprimer la ' + nomEchangeType.split('-')[3]);
            drt_grid = $('#js_drt_liste');
            var rows = drt_grid.getDataIDs();
            var nb_encours = 0, nb_partielle = 0, nb_cloture = 0, nb_ouverte = 0;
            for (var i = 0; i < rows.length; i++) {
                var statut = drt_grid.getCell(rows[i], "t-statut");
                if( statut === 'Ouverte' ) {
                    drt_grid.jqGrid('setCell', rows[i], 't-statut', '', {'font-weight': 'bold'});
                    drt_grid.jqGrid('setCell', rows[i], 't-drt', '', {'font-weight': 'bold'});
                    drt_grid.jqGrid('setCell', rows[i], 't-upload-drt', '', {'font-weight': 'bold'});
                    drt_grid.jqGrid('setCell', rows[i], 't-e-date-envoi', '', {'font-weight': 'bold'});
                    drt_grid.jqGrid('setCell', rows[i], 't-attente', '', {'font-weight': 'bold'});
                    drt_grid.jqGrid('setCell', rows[i], 't-dossier', '', {'font-weight': 'bold'});
                    nb_ouverte++;
                }else if( statut === 'En cours' ){
                    nb_encours++;
                }else if( statut === 'Partielle' ){
                    nb_partielle++;
                }else if( statut === 'Clôturée' ){
                    nb_cloture++;
                }

                var dossier_row_id = drt_grid.getCell(rows[i], "t-dossierId");
                if( i === 0 ){
                    var dossier_id = dossier_row_id;
                }
                if(parseInt(dossier_id) !== parseInt(dossier_row_id)){
                    drt_grid.jqGrid('setCell', rows[i], 't-index', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-statut', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-drt', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-upload-drt', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-e-date-envoi', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-attente', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-dossier', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-x', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-message-drt', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-reponse', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-upload-rdrt', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-message-rdrt', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-reponse-date', '', {'border-top': '1px solid #dddddd'});
                    drt_grid.jqGrid('setCell', rows[i], 't-actions', '', {'border-top': '2px solid #dddddd'});
                    dossier_id = dossier_row_id;
                }
            }

            if($('#statut').val() === '3'){
                $('.statut_stat').each(function(){
                    var type = parseInt($(this).attr('data-type'));
                    if (type === 0) $(this).find('.badge').text(number_format(nb_ouverte,0,',',' '));
                    else if (type === 1) $(this).find('.badge').text(number_format(nb_encours,0,',',' '));
                    else if (type === 2) $(this).find('.badge').text(number_format(nb_partielle,0,',',' '));
                    else if (type === 3) $(this).find('.badge').text(number_format(nb_cloture,0,',',' '));
                });
                $('#id_total_echange').find('.badge').text(number_format((nb_encours+nb_partielle+nb_cloture+nb_ouverte),0,',',' '))
            }
        },
        ajaxRowOptions: {async: true}
    });
    drt_grid.jqGrid('hideCol',["t-dossierId"]);
    drt_grid.jqGrid('hideCol',["t-statut-stat"]);
}

// Go for drt
function go() {

    control();

    var type = parseInt($('#id_li_active_container').find('li.active').attr('data-type'));
    if (type === 2)
    {
        ecritures();
        return;
    }

    var client = $('#client').val(),
        exercice = $('#exercice').val(),
        chrono = $('#filtre_chrono').val(),
        dossier = $('#dossier').val(),
        echangeType = $(document).find('input[name="show-filter-item"]:checked').val(),
        periodeDeb = $("#js_debut_date").val(),
        periodeFin = $("#js_fin_date").val();

    if(client === null || client === ''){
        show_info('Erreur','Choisir un client','error');
        $('#client').closest('.form-group').addClass('has-error');
        return false;
    }

    if ( chrono === '7' && (periodeDeb ===  '' || periodeFin === '')) {
        show_info('Champ Fourchette Invalide', 'Veuillez Remplir les Dates', 'info');
        $('#js-filtre-fourchette').modal('show');
        return false;
    }

    if ($('#dossier option:selected').text().trim() === 'Tous')
    {
        $('.filtre').hide();
        $('#col-drt').show();
        $('#add-drt-echange').hide();
        showListDrtDossier(dossier, chrono, exercice, echangeType, true);
    }else{
        $('.filtre').show();
        $('#col-drt').show();
        $('#add-drt-echange').show();
        $('#btn-save-drt-echange').attr('data',dossier);
        $('#btn-save-drt-echange').attr('data-echange-type',echangeType);
        $('.drt-add-title').html($('#dossier option:selected').text());
        showListDrtDossier(dossier, chrono, exercice, echangeType, false);
    }
}

function saveDrtAction(dossier, numero, statut){
    var exercice = $('#exercice').val(),
        echangeType = $(document).find('input[name="show-filter-item"]:checked').val();
    var url = Routing.generate('drt_add_reponse');
    var idata = {};
    idata['client'] = $('#client').val();
    idata['exercice'] = exercice;
    idata['dossier'] = dossier;
    idata['numero'] = numero;
    idata['echangeType'] = echangeType;
    idata['statut'] = statut;
    idata['is_reponse'] = false;
    $.ajax({
        url:url,
        type: "POST",
        dataType: "json",
        data: {
            'idata': JSON.stringify(idata)
        },
        async: true,
        success: function (data)
        {
            $('#js_drt_liste').jqGrid("clearGridData");
            $('#drt-repondre-modal').modal('hide');

            go();
        }
    });
}

function showRepondreDrt() {
    var grid = $("#js_drt_liste");
    var btn_modal_drt_reponse = $('#submit-add-reponse');
    var rowKey = grid.jqGrid('getGridParam',"selrow");
    var drt = grid.jqGrid('getCell',rowKey,'t-drt');
    var numero = drt.split(' ')[3];
    var title_modal = drt.split(' ')[1];
    $('.drt-reponse-title').html(title_modal);
    var dossier = grid.jqGrid('getCell',rowKey,'t-dossierId');
    btn_modal_drt_reponse.attr('data-numero', numero);
    btn_modal_drt_reponse.attr('data-dossier', dossier);
    $('#drt-repondre-modal').modal('show');
}

function showSupprimeDrt() {
    var grid = $("#js_drt_liste");
    var btn_modal_drt_reponse = $('#btn-supprime-drt');
    var rowKey = grid.jqGrid('getGridParam',"selrow");
    var drt = grid.jqGrid('getCell',rowKey,'t-drt');
    var numero = drt.split(' ')[3];
    var title_modal = drt.split(' ')[1];
    $('.drt-reponse-title').html(drt + ' ('+title_modal+')');
    var dossier = grid.jqGrid('getCell',rowKey,'t-dossierId');
    btn_modal_drt_reponse.attr('data-numero', numero);
    btn_modal_drt_reponse.attr('data-dossier', dossier);
    btn_modal_drt_reponse.attr('data-drt-rdrt', 0);
    var titre = 'DEPLACER LES PIECES VERS...',animated = 'bounceInRight';
    // show_modal($('#js_hidden_add_drt').html(),titre,animated,'modal-lg');
    $('#drt-supprime-modal').modal('show');
}

/*function showSupprimeRdrt() {
    var grid = $("#js_drt_liste");
    var btn_modal_drt_reponse = $('#btn-supprime-drt');
    var rowKey = grid.jqGrid('getGridParam',"selrow");
    var rdrt = grid.jqGrid('getCell',rowKey,'t-reponse');
    var numero = rdrt.split(' ')[3];
    var title_modal = rdrt.split(' ')[1];
    $('.drt-reponse-title').html(rdrt + ' ('+title_modal+')');
    var dossier = grid.jqGrid('getCell',rowKey,'t-dossierId');
    btn_modal_drt_reponse.attr('data-numero', numero);
    btn_modal_drt_reponse.attr('data-dossier', dossier);
    btn_modal_drt_reponse.attr('data-drtOrRdrt', 1);
    $('#drt-supprime-modal').modal('show');
}*/
// Add responsive to jqGrid
$(window).bind('resize', function () {
    var grid = $('#js_drt_liste');
    grid.jqGrid("setGridWidth", grid.closest(".ui-jqgrid").parent().width());
});

function hide_statut_stat() {
    var checkeds = [],
        uncheckeds = [];
    $('#id_statut_stat_container').find('.statut_stat').each(function(){
        if ($(this).hasClass('white-bg')) checkeds.push(parseInt($(this).attr('data-type')));
        else uncheckeds.push(parseInt($(this).attr('data-type')));
    });

    if (checkeds.length === 0) checkeds = uncheckeds;

    $('#js_drt_liste').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow')){
            var statut = parseInt($(this).find('.t-statut-stat').text());
            if (statut === 0)
            {
                if (checkeds.in_array(0)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }else if(statut === 1){
                if (checkeds.in_array(1)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }else if(statut === 2){
                if (checkeds.in_array(2)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }else if(statut === 3){
                if (checkeds.in_array(3)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
        }
    });
}

function addNewEchangeDrt(){
    var echangeType = $(document).find('input[name="show-filter-item"]:checked').val();
    var grid = $('#js_drt_liste');
    var rowKey = grid.jqGrid('getGridParam',"selrow");
    var drt = grid.jqGrid('getCell',rowKey,'t-drt');
    var title_modal = 'Nouvelle Demande sur ' + drt.split(' ')[1] + ' ('+drt+')';
    var dossier = grid.jqGrid('getCell',rowKey,'t-dossierId');
    var btn_dossier = $('#btn-save-drt-echange');
    var id = grid.getGridParam('selrow');
    btn_dossier.attr('data',dossier);
    btn_dossier.attr('data-echange-type',id+'-'+echangeType);
    $('.drt-add-title').html(title_modal);
    $('#drt-add-modal').modal('show');
}

