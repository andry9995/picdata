$(document).ready(function(){

    $(document).on('change', '.categorie-check', function(){
        setActionCatergorieItems();
    });


    $(document).on('click', '.action-activer, .action-desactiver', function(){

        var act = $(this).attr('class');
        var actVal = 0;

        if(act === 'action-activer'){
            actVal = 1;
        }

        activeDesactiveCategorieRows(actVal);

    });

    $(document).on('click', '.table-categorie td', function(){

        if($(this).hasClass('categorie-check')){
            return;
        }

        if($(this).parent().parent().is('tfoot')){
            return;
        }

        if($(this).parent().hasClass('categorie')){
            return;
        }

        var sous_categorie_dossier_id = $(this).closest('tr').attr('data-id');

        if(typeof sous_categorie_dossier_id=== 'undefined'){
            sous_categorie_dossier_id = 0;
        }

        $.ajax({
            url: Routing.generate('note_frais_admin_sous_categorie_edit'),
            data: {
                dossierId: $('#dossier').val(),
                sousCategorieId: sous_categorie_dossier_id
            },

            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                setModalWidth(window.innerWidth);

                $('#js_categorie_form').html(data);

                $('#js_categorie_edit').attr('data-id', sous_categorie_dossier_id);

                $('#js_categorie_edit').modal('show');

                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }
            }
        });

    });

    $(document).on('click', '#js_save_sous_categorie', function(){
        var id = $('#js_categorie_edit').attr('data-id');
        saveSousCategorie(id);
    });

    $(document).on('click', '.js_edit_pcc, .js_add_pcc', function(){
        var pcc_id = $(this).parent().find('.js_pcc').val();
        var type_pcc = '';

        if($(this).hasClass('js_add_pcc')){
            pcc_id = 0;
        }

        if($(this).closest('.input-group').find('select').attr('id') === "js_pcc_charge"){
            type_pcc = "pcc_charge";
        }
        else if($(this).closest('.input-group').find('select').attr('id') === "js_pcc_tva"){
            type_pcc = "pcc_tva";
        }

        showPccModal(pcc_id, type_pcc);
    });

    $(document).on('click', '#js_save_pcc', function(){
        var id = $('#js_pcc_edit_2').attr('data-id');
        var type_pcc = $('#js_pcc_edit_2').attr('data-type-pcc');
        savePcc(id, type_pcc);
    });




    $(document).on('click', '#js_edit_pcg_charge, #js_edit_pcg_tva', function() {

        var typeGrid = '';
        if($(this).attr('id') === 'js_edit_pcg_charge'){
            typeGrid = 'pcg_charge';
        }
        else{
            typeGrid = 'pcg_tva';
        }

        var pcgGrid = $('#js_pcg_liste');
        var last_sel_pcg;

        var souscategorieId = $('#js_categorie_edit').attr('data-id');

        var url = Routing.generate('note_frais_pcg', {type: typeGrid, json: souscategorieId});
        var editUrl = Routing.generate('note_frais_pcg_edit', {type: typeGrid, json: souscategorieId});

        pcgGrid.jqGrid({
            url: url,
            editUrl: editUrl,
            datatype: 'json',
            loadonce: false,
            sortable: true,
            autowidth: true,
            width: 560,
            shrinkToFit: true,
            viewrecords: true,
            pager: '#js_pcg_pager',
            hidegrid: false,
            caption: 'PCG',
            colNames: ['Compte',
                '<span class="fa fa-bookmark-o " style="display:inline-block"/> Action'
            ],
            colModel: [
                {
                    name: 'pcg-compte',
                    index: 'pcg-compte',
                    editable: true,
                    sortable: true,
                    width: 100,
                    align: "center",
                    classes: 'js_pcg_compte'
                },

                {
                    name: 'pcg-action', index: 'pcg-action', width: 60, align: "center", sortable: false,
                    editoptions: {defaultValue: '<i class="fa fa-save icon-action js_save_pcg" title="Enregistrer"></i>' +
                    '<i class="fa fa-trash icon-action js_delete_pcg" title="Supprimer"></i>'},
                    classes: 'js_save_pcg_action'
                }
            ],

            onSelectRow: function (id) {
                if (id && id !== last_sel_pcg) {
                    pcgGrid.restoreRow(last_sel_pcg);
                    last_sel_pcg = id;
                }
                pcgGrid.editRow(id, false);
            },

            beforeSelectRow: function (rowid, e) {
                var target = $(e.target);

                var item_action = (target.closest('td').children('.icon-action').length > 0);

                return !item_action;

            },

            loadComplete: function () {

                if ($(".js_add_pcg").length === 0) {
                    pcgGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').
                    after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                        '<button class="btn btn-outline btn-primary btn-xs js_add_pcg" style="margin-right: 20px;">Ajouter</button></div>');
                }

                pcgGrid.jqGrid('setGridWidth', $('#js_pcg_edit').find('.modal-content').width() - 5);

            },

            ajaxRowOptions: {async: true},
            reloadGridOptions: {fromServer: true}
        });

        setModalWidth(window.innerWidth);


        pcgGrid.jqGrid('clearGridData');

        pcgGrid.jqGrid('setGridParam', {url: url});
        pcgGrid.jqGrid('setGridParam', {editurl: editUrl}
        ).trigger('reloadGrid');

        $('#js_pcg_edit').modal('show');

        $('.modal-backdrop.in').remove();


    });


    $(document).on('change', '#js_pcc_tva', function(){
        if($(this).val() == '-1' || $(this).val() == ''){
            $('#js_tva_rec').val('');
            $('#js_tva_rec').prop('disabled', true);

            $('#js_tva_rec2').val('');
            $('#js_tva_rec2').prop('disabled', true);

            enableChosen($('#js_tva_taux'), false);
        }
        else{
            $('#js_tva_rec').prop('disabled', false);
            $('#js_tva_rec2').prop('disabled', false);
            enableChosen($('#js_tva_taux'), true);
        }
    });


    $(document).on('click', '.js_add_pcg', function(){
        if(canAddRow(pcgGrid)) {
            event.preventDefault();
            pcgGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_pcg_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js_save_pcg_action', function (event) {

        var souscategorieId = $('#js_categorie_edit').attr('data-id');
        var url = Routing.generate('note_frais_pcg', {type: typeGrid, json: souscategorieId});
        var editUrl = Routing.generate('note_frais_pcg_edit', {type: typeGrid, json: souscategorieId});

        event.preventDefault();
        event.stopPropagation();
        pcgGrid.jqGrid('saveRow', last_sel_pcg, {
            "aftersavefunc": function() {
                pcgGrid.jqGrid('setGridParam', {url: url});
                pcgGrid.jqGrid('setGridParam', {editurl: editUrl}
                ).trigger('reloadGrid');

                reloadInput(typeGrid);

            }
        });
    });

    $(document).on('click', '.js_delete_pcg', function() {

        event.stopPropagation();
        event.preventDefault();

        var rowid = $(this).closest('tr').attr('id');

        if (rowid === 'new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_pcg_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('note_frais_pcg_delete', {type: typeGrid}),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer ce pcg?',
            afterComplete: function(){
                reloadInput(typeGrid);
            }
        });
    });

});


/**
 * mi-activer na midesactiver ny lignes rehetra
 * 0: desactive, 1: active
 * @param type
 */
function activeDesactiveCategorieRows(type) {

    $('.table-categorie tbody tr').each(function () {

        var checked = $(this).find('.categorie-check input').is(":checked");
        var td = $(this).find('.categorie-status');

        if (checked) {

            var id = $(this).attr('data-id');

            $.ajax({
                url: Routing.generate('note_frais_admin_sous_categorie_status'),
                type: 'POST',
                data: {id: id, status: type},
                success: function () {
                    setStatusText(type, td);
                }
            });
        }
    });

}

function enableChosen(select, enabled){
    if (!enabled) {
        select.chosen('destroy');
        select.prop('disabled', true);
        select.removeAttr('required');
        select.val("");
    }
    else{
        select.removeAttr('disabled');
        select.chosen('destroy');
        select.prop('disabled', false);
        select.chosen();
    }
}

function reloadPccCombo(type_pcc) {
    var likes = '';
    if (type_pcc === 'pcc_charge') {
        likes = $('#js_pcg_charge').val();
    }
    else if (type_pcc === 'pcc_tva') {
        likes = $('#js_pcg_charge').val();
    }

    $.ajax({
        url: Routing.generate('note_frais_combo_pcc'),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            likes: likes,
            typePCc: type_pcc
        },
        success: function (data) {
            if (type_pcc === "pcc_charge") {
                $('#js_pcc_charge').html(data);
                $("#js_pcc_charge").trigger("chosen:updated");
            }
            else if (type_pcc === "pcc_tva") {
                $('#js_pcc_tva').html(data);
                $("#js_pcc_tva").trigger("chosen:updated");
            }
        }
    });
}

function reloadInput(typePcg){
    var pcgTxt = '';

    $('#js_pcg_liste').find('.js_pcg_compte').each(function(){
        var compte = $(this).html();

        if(compte.length < 6) {
            for (var i = compte.length; i < 6; i++) {
                compte = compte + 'X';
            }
        }
        if(pcgTxt === ''){
            pcgTxt = compte;
        }
        else{
            pcgTxt += ', ' + compte;
        }
    });

    if(typePcg === "pcg_charge") {
        $('#js_pcg_charge').val(pcgTxt);
    }
    else{
        $('#js_pcg_tva').val(pcgTxt);
    }

    reloadPccCombo(typePcg);
}

function savePcc(id, type_pcc){

    var compte = $('#js_pcc_compte').val();
    var intitule = $('#js_pcc_intitule').val();
    $.ajax({
        url: Routing.generate('note_frais_admin_pcc_edit', {json: 1} ),
        data:{
            dossierId: $('#dossier').val(),
            pccId: id,
            compte: compte,
            intitule: intitule
        },
        type: 'POST',
        success: function(data){

            if(data.id === -1){
                show_info('Attention', 'Ce compte existe dejà dans le pcc', 'warning');
            }
            else {
                $('#js_pcc_edit_2').modal('hide');
                reloadPccCombo(type_pcc);
            }
        }
    })
}

function saveSousCategorie(id){
    var libelle = $('#js_libelle').val();
    var pccCharge = $('#js_pcc_charge').val();
    var pccTva = $('#js_pcc_tva').val();
    var tvaRec = $('#js_tva_rec').val();
    var tvaRec2 = $('#js_tva_rec2').val();
    var tvaTaux = $('#js_tva_taux').val();
    var status = $('#js_status').is(":checked");

    $.ajax({
        url: Routing.generate('note_frais_admin_sous_categorie_edit', {json: 1}),
        type: 'POST',
        async: true,
        data: {
            dossierId: $('#dossier').val(),
            sousCategorieId: id,
            libelle: libelle,
            pccCharge: pccCharge,
            pccTva: pccTva,
            tvaRec: tvaRec,
            tvaRec2: tvaRec2,
            tvaTaux: tvaTaux,
            status: status
        },

        success: function (data) {

            reloadSouscategorieTable();

            $('#js_categorie_edit').modal('hide');
        }
    });
}

function setActionCatergorieItems(){

    var trouveActivee = false;
    var trouveDesactivee = false;

    $('.table-categorie tbody tr').each(function(){

        var checked = $(this).find('.categorie-check input').is(":checked");
        var status = $(this).find('.categorie-status span').attr('data-status');

        if(checked){
            if(parseInt(status) === 0){
                trouveDesactivee = true;
            }

            if(parseInt(status) === 1){
                trouveActivee = true;
            }
        }

        if(trouveDesactivee && trouveActivee){
            return false;
        }
    });

    var actionDropDownMenu = $('.btn-categorie-action').closest('.input-group-btn').find('.dropdown-menu');
    // actionDropDownMenu.html('<li><a href="#" class="action-defaut">Paramètres par défaut</a></li>');

    actionDropDownMenu.html('');

    if(trouveDesactivee){
        actionDropDownMenu.append('<li><a href="#" class="action-activer" >Activer</a></li>');
    }

    if(trouveActivee){
        actionDropDownMenu.append('<li><a href="#" class="action-desactiver">Désactiver</a></li>');
    }

}

function setStatusText(type, td){
    if(type == '0'){
        td.html('<span class="label label-warning" data-status="0" style="display: inline-block;width: 100%;">Desactivée</span>');
    }
    else if(type == '1'){
        td.html('<span class="label label-info" data-status="1" style="display: inline-block;width: 100%;">Activée</span>');
    }
}

function showPccModal(pcc_id, type_pcc){

    $.ajax({
        url: Routing.generate('note_frais_admin_pcc_edit'),
        data: {
            dossierId: $('#dossier_id').val(),
            pccId: pcc_id
        },
        type: 'POST',
        async: true,
        dataType: 'html',
        success: function (data) {
            setModalWidth(window.innerWidth);
            $('#js_pcc_form').html(data);
            $('#js_pcc_edit_2').attr('data-id', pcc_id);
            $('#js_pcc_edit_2').attr('data-type-pcc', type_pcc);
            $('#js_pcc_edit_2').modal('show');
            $('.modal-backdrop.in').remove();
        }
    });
}

function reloadSouscategorieTable(){
    $.ajax({
        url: Routing.generate('note_frais_categorie', {json: 1}),
        data: { dossierId: $('#dossier').val() },
        type: 'POST',
        success: function(data){
            $('.souscategorie-table').html(data);
            $('.footable').footable();
        }
    });
}

function canAddRow(jqGrid) {
    var canAdd = true;
    var rows = jqGrid.find('tr');

    rows.each(function () {
        if ($(this).attr('id') === 'new_row') {
            canAdd = false;
        }
    });
    return canAdd;
}
