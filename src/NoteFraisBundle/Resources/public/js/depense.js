var departCoord = null;
var arriveeCoord = null;


$(document).ready(function(){

    setDepenseTotal();

    $(document).on('click', '.btn-depense-add, .btn-depense-fk-add', function(){

        var data_type = -1;

        var note_id = $('.btn-note-select').attr('note-id');

        if($(this).hasClass('btn-depense-add')){
            data_type = 0;
        }

        if($(this).hasClass('btn-depense-fk-add')){
            data_type = 1;
        }

        if(note_id === '-1'){
            show_info("Attention", "Il faut choisir une note avant de saisir les dépenses", "warning");
        }

        else {
            if (data_type === 0) {
                showDepenseModal(0, note_id);
            }
            else if (data_type === 1) {
                showDepenseFKModal(0, note_id);
            }
        }

    });

    $(document).on('click', '#js_save_depense, #js_save_depense_fk', function () {
       var id = $('#js_depense_edit').attr('data-id');

       var note_id = $('#js_depense_edit').attr('note-id');

       if(id == undefined || id == '0' ){
           id = 0;
       }

       if(note_id == undefined || note_id == '0'){
           note_id = 0;
       }

       if($(this).attr('id') == 'js_save_depense') {
           saveDepense(id);
       }
       else if($(this).attr('id') == 'js_save_depense_fk'){
           saveDepenseFK(id);
       }

    });

    $(document).on('click', '#js_assigner_note', function(){

        var nbNoteChecked = 0;
        var noteId = -1;

        $('#js_depense_form .table-note tr').each(function(){

            var noteChecked = $(this).find('input').is(":checked");

            if(noteChecked){
                nbNoteChecked++;
                noteId = $(this).attr('data-id');
            }
        });

        if(nbNoteChecked === 1) {
            var tr = $(this).closest('tr');
            var data_type = $(this).attr('data-type');
            var depense_id = $(this).attr('depense-id');
            assignerNote(depense_id, noteId, data_type);
        }

    });

    $(document).on('click', '.action-depense-note', function(){


        var item = $(this).closest('tr');
        var dataId = -1;
        var dataType = -1;

        if(item.length === 1){
            dataId = item.attr('data-id');
            dataType = item.attr('data-type');
        }

        else{
            item = $(this).closest('.ibox').parent();

            var ibox = item.find('.ibox-title');
            dataId = ibox.attr('data-id');
            dataType = ibox.attr('data-type');
        }



        $.ajax({
            url: Routing.generate('note_frais_table_note'),
            type: 'POST',
            data: { dossierId: $('#dossier').val() },
            success: function(data) {

                setModalWidth(window.innerWidth);

                $('#js_depense_form').html(data);
                $('#js_depense_edit').modal('show');
                $('#js_assigner_note').attr('depense-id', dataId);
                $('#js_assigner_note').attr('data-type', dataType);
                $('#js_depense_form .note-assigner').removeClass("hidden");
            }
        });
    });

    $(document).on('click', '.action-depense-dupliquer', function(){

        var item = $(this).closest('tr');
        var dataId = -1;
        var dataType = -1;

        if(item.length === 1){
            dataId = item.attr('data-id');
            dataType = item.attr('data-type');
        }

        else{
            item = $(this).closest('.ibox').parent();

            var ibox = item.find('.ibox-title');
            dataId = ibox.attr('data-id');
            dataType = ibox.attr('data-type');
        }

        dupliqueDepense(dataId, dataType);

    });

    $(document).on('click', '.action-depense-editer', function(){

        var depense_id = $(this).closest('tr').attr('data-id');
        var note_id = $(this).closest('table').attr('data-id');

        if(typeof  note_id === 'undefined'){
            note_id = 0;
        }

        if(typeof depense_id === 'undefined'){

            depense_id = $(this).closest('.ibox ').find('.ibox-title').attr('data-id');

            if(typeof  depense_id === 'undefined') {
                depense_id = 0;
            }
        }

        var data_type = $(this).closest('tr').attr('data-type');

        if(typeof data_type === 'undefined'){
            data_type = $(this).closest('.ibox ').find('.ibox-title').attr('data-type');
        }


        if(data_type == 0) {
            showDepenseModal(depense_id, note_id);
        }
        else if(data_type == 1){
            showDepenseFKModal(depense_id, note_id);
        }

    });

    $(document).on('click', '.action-depense-supprimer', function(){

        var item = $(this).closest('tr');
        var dataId = -1;
        var dataType = -1;

        if(item.length == 1){
            dataId = item.attr('data-id');
            dataType = item.attr('data-type');
        }

        else{
            item = $(this).closest('.ibox').parent();

            var ibox = item.find('.ibox-title');
            dataId = ibox.attr('data-id');
            dataType = ibox.attr('data-type');
        }

        deleteDepense(item, dataId, dataType);
    });

    $(document).on('click', '.action-depense-pj', function(){

        var item = $(this).closest('tr');
        var depenseId = -1;
        var noteId = -1;
        var noteLibelle = '';


        if(item.length === 1){
            depenseId = item.attr('data-id');
            noteId = item.find('.depense-note').attr('data-id');
            noteLibelle = item.find('.depense-note').html();
        }

        else{
            item = $(this).closest('.ibox').parent();

            var ibox = item.find('.ibox-title');
            depenseId = ibox.attr('data-id');
            noteId = item.find('.depense-note-bloc').attr('data-id');
            noteLibelle = item.find('.depense-note-bloc .pull-right').html();
        }

        var dossierId = $('#dossier').val();


        showPjModal(dossierId,depenseId, noteId, noteLibelle);
    });

    $(document).on('click', '.action-depense-pj-show', function () {

        var item = $(this).closest('tr');

        var lastsel_piece = -1;

        if(item.length > 0){
            lastsel_piece = $(this).closest('tr').find('.depense-action').attr('data-image');
        }
        else{
            item = $(this).closest('.ibox').parent();
            lastsel_piece = item.find('.ibox-title').attr('data-image');
        }

        if(lastsel_piece !== -1) {
            $.ajax({
                data: {
                    imageId: lastsel_piece,
                    cr: 1
                },
                url: Routing.generate('consultation_piece_data_image'),
                type: 'POST',
                dataType: 'html',
                success: function (data) {
                    var options = {modal: false, resizable: true, title: 'Détails Pièces'};
                    modal_ui(options, data, undefined, 0.95, 0.85);
                }
            });
        }
        else{
            show_info("Attention", "Pièce non trouvée", "warning");
        }

    });

    $(document).on('change', '.depense-check input', function(){

        setDepenseAction();

    });

    $(document).on('click', '.depense-note-filtre', function() {
        var id = $(this).closest('li').attr('data-id');
        setBtnNoteSelectText($(this).html(), id);
        reloadDepenseTable(id);

    });

    $(document).on('change', '#js_depense_devise', function(){
        var devise = $(this).val();
        var input = $('#js_depense_ttc_converti');
        if(devise == 1 || devise == ''){

            if(!input.closest('.form-group').hasClass('hidden')){
                input.closest('.form-group').addClass('hidden');
            }
        }
        else{

            input.closest('.form-group').removeClass('hidden');

            var montantTtc = $('#js_depense_ttc').val();
            devise = $(this).val();
            var date = $('#js_depense_date').val();

            calculDevise(montantTtc, devise, date);


        }
    });

    $(document).on('change', '#js_depense_fk_vehicule, #js_depense_fk_trajet', function(){

        var vehicule  = $('#js_depense_fk_vehicule').val();
        var trajet = $('#js_depense_fk_trajet').val();

        if(vehicule != '' && trajet != ''){
            calculTarification(vehicule, trajet);
        }

    });

    $(document).on('click', '#js_depense_fk_edit_veh, #js_depense_fk_add_veh', function(){
        var vehicule_id = $(this).parent().find('#js_depense_fk_vehicule').val();

        if($(this).attr('id')== 'js_depense_fk_add_veh'){
            vehicule_id = 0;
        }
        showVehiculeModal(vehicule_id, false);

    });

    $(document).on('click', '#js_annuler_depense', function(){

        if ($('#js_depense_envoi').val()) {

            console.log($('#js_depense_envoi').val());

            $('#js_depense_envoi').fileinput('upload');
        }

    });

    $(document).on('click', '.btn-depense-filter', function(){

        $.ajax({
            url: Routing.generate('note_frais_depense_filtre'),
            type: 'POST',
            data:{
                dossierId: $('#dossier').val()
            },
            success: function(data){

                setModalWidth(window.innerWidth);

                $('#js_depense_form').html(data);
                $('#js_depense_edit').modal('show');
                setDate();
            }

        })

    });

    $(document).on('click', '#js_filtrer_depense', function () {

        var titre = $('#js_filtre_titre').val();
        var facturable = $('#js_filtre_facturable').val();
        var remboursable = $('#js_filtre_remboursable').val();
        var sousCategorieId = $('#js_filtre_categorie').val();
        var affaireId = $('#js_filtre_affaire').val();
        var noteId = $('#js_filtre_note').val();
        var dateDu = $('#js_filtre_date_du').val();
        var dateAu = $('#js_filtre_date_au').val();
        var dossierId = $('#dossier').val();

        $.ajax({
            url: Routing.generate('note_frais_depense_filtre', {json: 1}),
            type: 'POST',
            data: {
                'dossierId': dossierId,
                'titre': titre,
                'facturable': facturable,
                'remboursable': remboursable,
                'sousCategorieId': sousCategorieId,
                'affaireId': affaireId,
                'noteId': noteId,
                'dateDu': dateDu,
                'dateAu': dateAu
            },
            success: function (data) {
                $('.contenu-depense').html(data);
                $('#js_depense_edit').modal('hide');

                $('.footable').footable();

            }

        });
    });

    $(document).on('change','#js_depense_pj', function(){

        if($(this).is(':checked')){

            var depenseId = $('#js_depense_edit').attr('data-id');
            var noteId = $('#js_depense_note').val();
            var noteLibelle = $('#js_depense_note option:selected').text();

            var dossierId = $('#dossier').val();

            if(parseInt(depenseId) === 0){
                show_info('Attention', 'Il faut enregistrer la dépense avant d\'envoyer une pièce jointe', 'warning');
            }
            else{


                showPjModal(dossierId,depenseId, noteId,noteLibelle);
            }
        }
    });

});

function assignerNote(id, note_id, data_type){

    $.ajax({
        url: Routing.generate('note_frais_table_note', {json: 1}),
        type: 'POST',
        data: {
            depenseId:id,
            noteId:note_id,
            dataType:data_type
        },
        success: function(data) {


            reloadDepenseTable(data.noteId);
            setBtnNoteSelectText(data.noteLibelle, data.noteId);

            $('#js_depense_edit').modal('hide');
        }
    })
}

function calculDevise(montant, deviseId, date){
    var ret = 0;

    $.ajax({
        url: Routing.generate('note_frais_devise'),
        type: 'POST',
        data: {
            montant: montant,
            deviseId:deviseId,
            date:date
        },
        success: function(data){
            ret = data;

            $('#js_depense_ttc_converti').val(ret);
        }
    });
}

function calculDistance(depart, arrivee){

    if(arriveeCoord != null && departCoord != null){

        var service = new google.maps.DistanceMatrixService;

        service.getDistanceMatrix({
            origins: [depart],
            destinations: [arrivee],
            travelMode: 'DRIVING',
            unitSystem: google.maps.UnitSystem.METRIC,
            avoidHighways: false,
            avoidTolls: false
        }, function(response, status) {
            if (status !== 'OK') {
                alert('Error was: ' + status);
            } else {
                var originList = response.originAddresses;

                if(originList.length > 0){
                    var results = response.rows[0].elements;
                    if(results.length > 0) {
                       // $('#js_depense_fk_trajet').val((results[0].distance.value) / 1000);

                        var trajet = ((results[0].distance.value) / 1000).toFixed(2);

                        $('#js_depense_fk_trajet').val(trajet);

                        var vehicule = $('#js_depense_fk_vehicule').val();

                        calculTarification(vehicule, trajet);

                    }
                }

            }
        });

    }

}

function calculDirection(depart, arrivee, map,directionsService,directionsDisplay){




    directionsDisplay.setMap(map);

    directionsService.route({
        origin: depart,
        destination: arrivee,
        travelMode: 'DRIVING'
    }, function(response, status) {
        if (status === 'OK') {
            directionsDisplay.setDirections(response);
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}

function calculTarification(vehicule_id, trajet){
    if(vehicule_id != "" && trajet != "") {
        $.ajax({
            url: Routing.generate('note_frais_tarification'),
            type: 'POST',
            data: {vehicule_id: vehicule_id, trajet: trajet},
            success: function (data) {
                $('#js_depense_fk_ttc').val(data);
            }
        });
    }
}

function dupliqueDepense(id, data_type){
    $.ajax({
        url: Routing.generate('note_frais_depense_dupliquer'),
        data: {
            depenseId: id,
            dataType: data_type
        },
        type: 'POST',
        // async: false,
        success: function(data){

            if(data !== -1) {
                reloadDepenseTable(data.noteId);
            }

        }
    });
}

function deleteDepense(item, dataId, dataType) {

    $.ajax({
        url: Routing.generate('note_frais_depense_delete'),
        type: 'POST',
        data: {
            id: dataId,
            data_type: dataType
        },
        success: function (data) {
            if (data != -1) {
                item.remove();
                // setDepenseAction();
            }
        }
    });
}

function initPJFileInput(selecteur, dossierId,depenseId, noteId, noteLibelle) {
    $('#'+selecteur).fileinput({
        language: 'fr',
        theme: 'fa',
        uploadAsync: false,
        showPreview: true,
        showUpload: true,
        showRemove: false,
        showCancel: false,
        uploadUrl: Routing.generate('note_frais_depense_pj', {json: 1}),
        uploadExtraData: function() {
            return {
                dossierId: dossierId,
                depenseId: depenseId
            };
        }
    });

    $('#'+selecteur).on('filebatchuploadcomplete', function() {
        var fileCapt = $('#'+selecteur).closest('.input-group').find('.file-caption-name');
        fileCapt.append('<i class="fa fa-check kv-caption-icon"></i>');
        $('#js_depense_pj_edit').modal('hide');
        reloadDepenseTable(noteId);
        setBtnNoteSelectText(noteLibelle, noteId);
    });

    $('#'+selecteur).on('fileuploaderror', function(event, data, msg) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
        console.log('File upload error');
        // get message
        alert(msg);
    });

}

function reloadDepenseTable(note_id){
    $.ajax({
        url: Routing.generate('note_frais_table_depense'),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            noteId: note_id
        },
        success: function(data){
            // $('.note-table').html(data);
            $('.contenu-depense').html(data);
            // $('#tab-note .table-depense').attr('data-id', note_id);

            setDepenseTotal();
            $('.footable').footable();

        }
    });
}




function saveDepense(id){
    var titre = $('#js_depense_titre').val();
    var date = $('#js_depense_date').val();
    var sousCategorie = $('#js_depense_categorie').val();
    var typeReglement = $('#js_depense_type_reglement').val();
    var modeReglement = $('#js_depense_mode_reglement').val();
    var pays = $('#js_depense_pays').val();
    var ttc = $('#js_depense_ttc').val();
    var devise = $('#js_depense_devise').val();
    var noteId = $('#js_depense_note').val();
    var affaire = $('#js_depense_affaire').val();
    var aFacturer = $('#js_depense_a_facturer').is(":checked");
    var aRembourser = $('#js_depense_a_rembourser').is(":checked");
    var tvaTaux = $('#js_depense_tva_taux').val();
    var contact = $('#js_depense_contact').val();
    var pj = $('#js_depense_pj').is(':checked');



    $.ajax({
        url: Routing.generate('note_frais_depense_edit', {json: 1}),
        data:{
            dossierId: $('#dossier').val(),
            depenseId:id,
            noteId:noteId,
            titre:titre,
            date:date,
            sousCategorie:sousCategorie,
            typeReglement:typeReglement,
            modeReglement:modeReglement,
            pays:pays,
            ttc:ttc,
            devise:devise,
            affaire:affaire,
            aFacturer:aFacturer,
            aRembourser:aRembourser,
            tvaTaux: tvaTaux,
            contact: contact,
            pj: pj
        },
        type: 'POST',
        success: function(data){

            if(data.errMsg !== ""){
                show_info('Attention',data.errMsg, 'warning');
            }
            else {
                reloadDepenseTable(data.noteId);
                setBtnNoteSelectText(data.noteLibelle, data.noteId);

                $('#js_depense_edit').modal('hide');
            }
        }

    })


}

function saveDepenseFK(id){
    var vehicule_id = $('#js_depense_fk_vehicule').val();
    var depart = $('#js_depense_fk_depart').val();
    var arrivee = $('#js_depense_fk_arrivee').val();
    var trajet = $('#js_depense_fk_trajet').val();
    var ttc =$('#js_depense_fk_ttc').val();
    var periodeDu = $('#js_depense_fk_periode_deb').val();
    var periodeAu = $('#js_depense_fk_periode_fin').val();
    var noteId = $('#js_depense_fk_note').val();
    var titre = $('#js_depense_fk_titre').val();
    var aFacturer = $('#js_depense_fk_a_facturer').is(":checked");
    var affaireId = $('#js_depense_fk_affaire').val();
    var depLat = '';
    var depLng = '';
    var arrLat = '';
    var arrLng = '';

    if(departCoord != null) {
        depLat = departCoord.lat();
        depLng = departCoord.lng();
    }
    if(arriveeCoord != null) {
        arrLat = arriveeCoord.lat();
        arrLng = arriveeCoord.lng();
    }

    $.ajax({
        url: Routing.generate('note_frais_depense_fk_edit', {json: 1}),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            depenseFkId: id,
            vehiculeId: vehicule_id,
            depart: depart,
            arrivee: arrivee,
            trajet: trajet,
            ttc: ttc,
            periodeDu: periodeDu,
            periodeAu: periodeAu,
            noteId: noteId,
            affaire_id: affaireId,
            titre: titre,
            aFacturer:aFacturer,
            depLat:depLat,
            depLng:depLng,
            arrLat:arrLat,
            arrLng:arrLng
        },
        success: function(data){

            if(data.errMsg !== ""){
                show_info('Attention',data.errMsg, 'warning');
            }
            else {
                reloadDepenseTable(data.noteId);
                setBtnNoteSelectText(data.noteLibelle, data.noteId);

                if(data.noteLi !== ''){
                    var ul = $('.btn-note-select').parent().find('ul');
                    ul.html(data.noteLi);
                }
                $('#js_depense_edit').modal('hide');
            }
        }
    });

}

/**
 * Initialisation datePicker
 */
function setDate() {

    $('.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        language: "fr"
    });
}

function setSwitch(){
    // var elem = document.querySelector('.js-switch');
    // var switchery = new Switchery(elem, { color: '#1AB394' });
    //
    // var elem_2 = document.querySelector('.js-switch_2');
    // var switchery_2 = new Switchery(elem_2, { color: '#1AB394' });

    // var elem_3 = document.querySelector('.js-switch_3');
    // var switchery_3 = new Switchery(elem_3, { color: '#1AB394' });
    //
    // var elem_4 = document.querySelector('.js-switch_4');
    // var switchery_4 = new Switchery(elem_4, { color: '#1AB394' });


}

function setDepenseAction() {

    var trouveCheck = false;
    var nbSelected = 0;
    var trSelected = null;

    $('.table-depense .depense-check').each(function () {

        var checked = $(this).find('input').is(":checked");

        if (checked) {
            trouveCheck = true;
            nbSelected++;
            trSelected = $(this).closest('tr');
        }
    });


    var inputGroup = $('.btn-depense-action').closest('.input-group');
    if (trouveCheck) {
        if (inputGroup.hasClass('hidden')) {
            inputGroup.removeClass('hidden');
        }
    }
    else {
        if (!inputGroup.hasClass('hidden')) {
            inputGroup.addClass('hidden');
        }
    }


    var canAddPj = false;
    if (nbSelected == 1) {
        //0: depenses ihany no afaka asina PJ
        if (trSelected.attr('data-type') == 0) {
            if (trSelected.attr('data-pj') == 1) {
                canAddPj = true;
            }
        }
    }

    if (canAddPj) {
        var ul = $('.btn-depense-action').closest('.input-group-btn').find('.dropdown-menu');
        ul.append('<li><a href="#" class="action-depense-pj">Envoyer Pièce Jointe</a></li>');
    }
    else {
        if ($('.action-depense-pj').length > 0) {
            $('.action-depense-pj').remove();
        }

    }

}


function setDepenseTotal(){
    $('.total-ttc').html($('.depense-total-ttc').html());
    $('.total-tva').html($('.depense-total-tva').html());
    $('.total-remboursable').html($('.depense-total-remboursable').html());
    $('.total-facturable').html($('.depense-total-facturable').html());
}

function setBtnNoteSelectText(text, id){
    var btnParent = $('.btn-note-select');

    btnParent.html('' +
        '&nbsp;&nbsp;' + text + '&nbsp;&nbsp;' +
        '<span class="caret"></span>');


    btnParent.attr('note-id', id);
}

/**
 * Mametaka ny valeur-an'ny tva taux
 * @param depenseId
 */
function setContactDepenseCombo(depenseId) {
    $.ajax({
        data: {depenseId: depenseId},
        url: Routing.generate('note_frais_depense_contact'),
        type: 'POST',
        success: function (data) {
            var res = Array.from(data);


            $('#js_depense_contact').val(res).trigger('chosen:updated');
        }
    });
}

/**
 * Mametaka ny valeur-an'ny tva taux
 * @param depenseId
 */
function setTvaTauxDepenseCombo(depenseId) {
    $.ajax({
        data: {depenseId: depenseId},
        url: Routing.generate('note_frais_tva_taux'),
        type: 'POST',
        success: function (data) {
            var res = Array.from(data);

            $('#js_depense_tva_taux').val(res).trigger('chosen:updated');
        }
    });
}

function showDepenseModal(depense_id, note_id){
    $.ajax({
        url: Routing.generate('note_frais_depense_edit'),
        data:{
            dossierId:$('#dossier').val(),
            depenseId:depense_id,
            noteId:note_id

        },
        type: 'POST',
        success: function(data){

            setModalWidth(window.innerWidth);

            $('#js_depense_form').html(data);
            $('#js_depense_edit').modal('show');

            $('#js_depense_edit').attr('data-id', depense_id);
            $('#js_depense_edit').attr('note-id', note_id);

            //raha misy devise dia calculer-na ny eo @ ttc converti
            if( !$('#js_depense_ttc_converti').closest('.form-group').hasClass('.hidden')){
                var montant = $('#js_depense_ttc').val();
                var devise = $('#js_depense_devise').val();
                var date = $('#js_depense_date').val();
                calculDevise(montant, devise, date);
            }

            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

            setTvaTauxDepenseCombo(depense_id);
            setContactDepenseCombo(depense_id);

            setDate();
            setSwitch();


        }
    });
}

function showDepenseFKModal(depense_fk_id, note_id){

    $.ajax({
        url: Routing.generate('note_frais_depense_fk_edit'),
        type: 'POST',
        data: {
            dossierId: $('#dossier').val(),
            depenseFkId: depense_fk_id,
            noteId:note_id},
        success: function(data){

            setModalWidth(window.innerWidth);

            $('#js_depense_form').html(data);
            $('#js_depense_edit').modal('show');

            $('#js_depense_edit').attr('data-id', depense_fk_id);
            $('#js_depense_edit').attr('note-id', note_id);

            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

            setDate();
            setSwitch();


            departCoord = null;
            arriveeCoord = null;

            var departMarkers = [];
            var arriveeMarkers = [];

            //Jerena aloha raha efa manana coordonnées ny départ & ny arrivée
            var depLng = $('#js_depense_fk_depart').attr('data-lng');
            var depLat = $('#js_depense_fk_depart').attr('data-lat');


            var arrLng = $('#js_depense_fk_arrivee').attr('data-lng');
            var arrLat = $('#js_depense_fk_arrivee').attr('data-lat');

            if(depLng != '' && depLat != ''){
                departCoord = new google.maps.LatLng(depLat, depLng);
            }

            if(arrLng != '' && arrLat != ''){
                arriveeCoord = new google.maps.LatLng(arrLat, arrLng);
            }

            // google.maps.event.addDomListener(window, 'load', initAutocomplete);

            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 48.85661400000001, lng: 2.3522219000000177},
                zoom: 13
            });

            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;

            //Raha efa misy données avy any @bdd
            if(departCoord != null && arriveeCoord != null) {
                directionsDisplay.setMap(null);
                calculDirection(departCoord, arriveeCoord, map, directionsService, directionsDisplay);
            }


            var depart = document.getElementById('js_depense_fk_depart');
            var departSearchBox = new google.maps.places.SearchBox(depart);

            var arrivee = document.getElementById('js_depense_fk_arrivee');
            var arriveeSearchBox = new google.maps.places.SearchBox(arrivee);


            var bounds = new google.maps.LatLngBounds();

            departSearchBox.addListener('places_changed', function() {
                var places = departSearchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                if(departMarkers.length > 0) {
                    departMarkers[0].setMap(null);
                }
                departMarkers = [];


                var trouveDepart = false;

                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    if(!trouveDepart) {

                        // Create a marker for each place.
                        departMarkers.push(new google.maps.Marker({
                            map: map,
                            title: place.name,
                            position: place.geometry.location
                        }));


                        departCoord = place.geometry.location;


                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }

                        trouveDepart = true;

                        calculDistance(departCoord, arriveeCoord);

                        directionsDisplay.setMap(null);
                        calculDirection(departCoord, arriveeCoord, map, directionsService, directionsDisplay);
                    }

                });
                map.fitBounds(bounds);

            });


            arriveeSearchBox.addListener('places_changed', function() {
                var places = arriveeSearchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                if(arriveeMarkers.length > 0) {
                    arriveeMarkers[0].setMap(null);
                }

                arriveeMarkers = [];
                var trouveArrivee = false;

                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    if(!trouveArrivee) {

                        arriveeMarkers.push(new google.maps.Marker({
                            map: map,
                            title: place.name,
                            position: place.geometry.location
                        }));

                        arriveeCoord = place.geometry.location;

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }

                        trouveArrivee = true;

                        calculDistance(departCoord, arriveeCoord);

                        directionsDisplay.setMap(null);
                        calculDirection(departCoord, arriveeCoord, map, directionsService, directionsDisplay);
                    }
                });
                map.fitBounds(bounds);
            });

            var height = $('#js_depense_form .form-horizontal .col-lg-6').first().height();
            $('.map').css('height',height);
        }
    })
}

function showPjModal(dossier_id,depense_id, note_id, note_libelle){

    $.ajax({
        url: Routing.generate('note_frais_depense_pj'),
        type: 'POST',

        success: function(data){
            setModalWidth(window.innerWidth);
            $('#js_depense_pj_form').html(data);
            $('#js_depense_pj_edit').modal('show');

            initPJFileInput('js_depense_envoi', dossier_id,depense_id, note_id, note_libelle);
        }
    });


}

