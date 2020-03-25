/**
 * Created by MAHARO on 08/02/2017.
 */
$(document).ready( function () {

    $(document).on('click','#js_contenu_tabs a i',function () {

        if($(this).attr('class') == 'fa fa-chevron-up') {

            var formHorizontal = $(this).closest('.ibox').find('.ibox-content .form-horizontal');

            if (formHorizontal.attr('id') == 'js_form_info_identification_dossier') {
                $(this).closest('.ibox').find("input:enabled").first().focus();
            } else {
                $(this).closest('.ibox').find("select:enabled").first().focus();
            }
        }

    });

    $(document).on('click', '.btn-tout-replier', function () {
        $(this).closest('.panel-body').find('.ibox').addClass("border-bottom");
        $(this).closest('.panel-body').find('.ibox-content').css({display: 'none'});
        $(this).closest('.panel-body').find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-down");

        $(this).toggleClass('btn-tout-replier btn-tout-develloper');

        $(this).html('<i class="fa fa-expand"></i>&nbsp;&nbsp;Développer Tout');

    });

    $(document).on('click', '.btn-tout-develloper', function () {
        $(this).closest('.panel-body').find('.ibox').removeClass("border-bottom");
        $(this).closest('.panel-body').find('.ibox-content').css({display: 'block'});
        $(this).closest('.panel-body').find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-up");

        $(this).toggleClass('btn-tout-develloper btn-tout-replier');
        $(this).html('<i class="fa fa-compress"></i>&nbsp;&nbsp;Tout Replier');
    });

    setChks();

    $(document).on('click', '#btn-validation-instr-instruction-saisie', function () {
        var form = $('#js_form_instr_instruction_saisie');

        if (form.valid()) {
            saveInstrInstructionSaisie();
        }
    });

    $(document).on('click', '#btn-validation-instr-methode-comptable', function () {
        var form = $('#js_form_instr_methode_comptable');

        if (form.valid()) {
            // saveInstrMethodeComptable();
        }
        else{
            sweetAlert();
        }

        saveInstrMethodeComptable();


    });

    $(document).on('click', '#btn-validation-instr-instruction', function(){

        var lien = Routing.generate('info_perdos_instr_instruction');
        var aHTML = $('.js-instr-instruction ').summernote('code');

        $.ajax({

            data:{
                clientId: $('#client').val(),
                instruction:aHTML
            },
            url: lien,
            type: 'POST',
            dataType: 'html',
            success: function(data){

                var res = parseInt(data);

                if (res == 2) {
                    show_info('SUCCES', 'MODIFICATION DE L\'INSTRUCTION BIEN ENREGISTREE');
                }
                else if(res == 1){
                    show_info('SUCCES', 'AJOUT DE L\'INSTRUCTION BIEN ENREGISTREE');
                }

            }
        });
    });


    $(document).on('change', '#js_instr_logiciel', function(){
        if($(this).val() == 15){
            showAutreLogiciel();
        }
    });

    $(document).on('click', '.instr-pj', function(e) {
        e.preventDefault();
        window.open($(this).attr('href'), "Pièce jointe");
    });


    //
    // $("#qselected").sortable();
    // $("#qselected").disableSelection();
    //
    // $(".qitem").draggable({
    //     containment : "#container",
    //     helper : 'clone',
    //     revert : 'invalid'
    // });
    //
    // $("#qselected, #qlist").droppable({
    //     hoverClass : 'ui-state-highlight',
    //     accept: ":not(.ui-sortable-helper)",
    //     drop : function(ev, ui) {
    //         $(ui.draggable).clone().appendTo(this);
    //         $(ui.draggable).remove();
    //     }
    // });


    $('#sortable1, #sortable2').sortable({
        connectWith: '.connectedSortable',
        update: function( event, ui ) {

        }
    }).disableSelection();




});




/**
 * Initialisation an'ny checkBox
 */
function setChks() {
    var chks = $('[id^="js_instr_chk_"]');

    chks.each(function () {

        if(parseInt($(this).attr('data-id')) !== 13)

        $(this).click(function () {

            uncheckBox($(this));

            var typeInstruction = $(this).attr('data-id');
            var libelle = $(this).parent().parent().parent().text();
            showInstruction(typeInstruction, libelle, typeInstruction);
        });
    });
}

/**
 * Mi-enregistrer ny bloc Instructions pour la saisie
 */
function saveInstrInstructionSaisie() {
    
    var clientId = $('#client').val();

    var instructionVal = [];

    //instructionVal: liste an'izay coché
    for (var i = 1; i <= 13; i++) {
        instructionVal[i] = ($('#js_instr_chk_'+i).prop('checked')) ? 1 : 0;
    }

    var lien = Routing.generate('info_perdos_instr_instruction_saisie');
    
    $.ajax({
        data: {
            clientId:clientId,
            instructionVal:instructionVal
        },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {

            var res = parseInt(data);

            if (res == 2) {
                show_info('SUCCES', 'MODIFICATION BIEN ENREGISTREE');
            }
            else if (res == 1) {
                show_info('SUCCES', 'AJOUT EFFECTUEE');
            }

            verifierInstructionDossier(clientId);
        }
    });
}

/**
 * Mi-enregistrer ny bloc Mehtodes comptables
 */
function saveInstrMethodeComptable() {

    var rapprochementBanque = $('#js_instr_rapprochement_banque').val();
    var methodeSuiviCheque = $('#js_instr_suivi_cheque_emis').val();
    var gestionDateEcriture = $('#js_instr_gestion_date_ecriture').val();

    var logiciel = $('#js_instr_logiciel').val();

    var logicielLib = $("#js_instr_logiciel option:selected").text();
    
    var clientId = $('#client').val();

    var lien = Routing.generate('info_perdos_instr_methode_comptable');
    $.ajax({

        data: {
            clientId: clientId,
            methodeSuiviCheque: methodeSuiviCheque,
            rapprochementBanque: rapprochementBanque,
            gestionDateEcriture: gestionDateEcriture,
            logiciel: logiciel,
            logicielLib: logicielLib
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {

            var res = parseInt(data);

            if (res == 2) {
                show_info('SUCCES', 'MODIFICATION BIEN ENREGISTREE');
            }
            else if (res == 1) {
                show_info('SUCCES', 'AJOUT EFFECTUEE');
            }
            else {
                res = JSON.parse(data);

                if (res.estInsere == 0) {
                    show_info('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }

            }

            verifierInstructionDossier(clientId);

        }

    });
}

/**
 *
 * @param clientId
 */
function saveRemarqueClient(clientId) {

    var remarque = $('#js_remarque_client').val();
    var lien = Routing.generate('info_perdos_remarque_client_edit');
    $.ajax({
        url: lien,
        data: {
            clientId: clientId,
            remarque: remarque
        },

        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },

        success: function (data) {

            var res = parseInt(data);

            if (res == 2) {
                // show_info('SUCCES', "MODIFICATION DES REMARQUES EFFECTUEE");
            }
            else if (res == 1) {
                // show_info('SUCCES', "AJOUT DES REMARQUES EFFECTUEE");
            }
            else if (res == -1) {
                // show_info('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
            }
        }
    });
}

/**
 * Enregistrement regle de paiement
 */
function saveReglePaiementClient(){

    var clientId = $('#client').val();

    var fDateLe = $('#js_cf_regle_paiement_date_le').val();
    var fNbreJour = $('#js_cf_regle_paiement_nbre_jour').val();
    var fTypeDate = $('#js_cf_regle_paiement_date').val();

    var cDateLe = $('#js_cc_regle_paiement_date_le').val();
    var cNbreJour = $('#js_cc_regle_paiement_nbre_jour').val();
    var cTypeDate = $('#js_cc_regle_paiement_date').val();


    var lien = Routing.generate('info_perdos_regle_paiement_client_edit');
    $.ajax({

        data:{

            clientId:clientId,
            fDateLe:fDateLe,
            fNbreJour:fNbreJour,
            fTypeDate:fTypeDate,
            cDateLe:cDateLe,
            cNbreJour:cNbreJour,
            cTypeDate:cTypeDate

        },

        url: lien,
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);


            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DES 'REGLES DE PAIEMENTS' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DES 'REGLES DE PAIEMENTS' EFFECTUEE");
            }

        }
    });




}


/**
 * Mi-afficher an'ilay Instruction
 * @param json
 * @param titre
 * @param id_btn
 */
function showInstruction(json, titre, id_btn)
{
    var lien = Routing.generate('info_perdos_instr_show_instruction_texte',{json: json});
    $.ajax({

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: false,
        dataType: 'html',
        success: function(data){

            show_modal(data, titre, 'bounceInRight');

            setModalBodyHeight();

            $('.instr-decliner').attr('id', 'btn-instr-decliner-'+id_btn);
            $('.instr-accepter').attr('id', 'btn-instr-accepter-'+id_btn);

            $('#btn-instr-accepter-'+id_btn).click(function () {

                $('#js_instr_chk_'+id_btn).prop("checked",true);
                 close_modal();
            });

            $('#btn-instr-decliner-'+id_btn).click(function () {
                $('#js_instr_chk_'+id_btn).prop("checked",false);

                //Afficher autre fenetre pour les remarques
                showInstructionDecliner(json, id_btn)
            });

        }
    });
}



function showAutreLogiciel(){
    var lien = Routing.generate('info_perdos_instr_show_autre_logiciel');
    var titre = 'Indiquer ci-dessous le logiciel que vous utilisez';
    $.ajax({

        url: lien,
        type: 'POST',

        async: false,
        dataType: 'html',
        success: function (data) {

            show_modal(data, titre, 'bounceInRight');

            $('#instr-autre-logiciel-valider').click(function () {

                var newLogiciel = $('#js_instr_autre_logiciel').val();

                close_modal();

                $("#js_instr_logiciel").append('<option value=-1>'+newLogiciel+'</option>');
                $('#js_instr_logiciel').val(-1);


            });
            setModalBodyHeight();
        }
    });
}

/**
 * Mi-afficher ny fenêtre decliner
 * @param json
 */
function showInstructionDecliner(json) {
    var lien = Routing.generate('info_perdos_instr_show_instruction_decline', {json: json});
    var titre = 'Indiquer ci-dessous vos souhaits, Attention, ils devront être approuvés par Scriptura';
    $.ajax({

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: false,
        dataType: 'html',
        success: function (data) {

            show_modal(data, titre, 'bounceInRight');

            $('#instr-decliner-valider').click(function () {
                var newInstruction = $('#js_instr_texte_decline').val();
                var clientId = $('#client').val();
                $.ajax({

                    data: {
                        typeInstruction: json,
                        clientId: clientId,
                        newInstruction: newInstruction
                    },
                    url: Routing.generate('info_perdos_instr_notif_decline'),
                    type: 'POST',

                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                    beforeSend: function (jqXHR) {
                        jqXHR.overrideMimeType('text/html;charset=utf-8');
                    },
                    async: false,

                    success: function (data) {
                        console.log(data);
                        close_modal();
                    }
                });
            });
            setModalBodyHeight();
        }
    });
}


function setInstructionDossier(clientId) {
    var lien = Routing.generate('info_perdos_instruction_dossier', {json: 1});

    $.ajax({
        data: {clientId: clientId},
        url: lien,
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            res = JSON.parse(data);


            //valeur par defaut raha mbola tsy ni-valider (oui par default)
            if (res.rapprochementBanque == null) {
                $('#js_instr_rapprochement_banque').val('1');
            }
            else {
                $('#js_instr_rapprochement_banque').val(res.rapprochementBanque);
            }

            if (res.suiviChequeEmis == null) {
                if(res.rapprochementBanque == 0){
                    $('#js_instr_suivi_cheque_emis').val('');
                }
                else {
                    $('#js_instr_suivi_cheque_emis').val('1');
                }
            }
            else {
                $('#js_instr_suivi_cheque_emis').val(res.suiviChequeEmis);
            }

            if (res.gestionDateEcriture == null) {
                $('#js_instr_gestion_date_ecriture').val('1');
            }
            else {
                $('#js_instr_gestion_date_ecriture').val(res.gestionDateEcriture);
            }

            if (res.remarque == null || res.remarque == '') {
                // $('#js_remarque_client').val("Remarques: Les onglets doivent être renseignés dans l'ordre de 1 à 5. Certaines zones sont obligatoires afin de valider la création du dossier, et pouvoir envoyer les images. Quand les zones obligatoires d'un onglet sont renseignées l'onglet est souligné en vert au lieu de rouge");
            }
            else {
                $('#js_remarque_client').val(res.remarque);
            }

            if(res.findInstruction == true) {

                for (var i = 1; i <= 13; i++) {
                    if (res.instructionVal['' + i + ''] == 1) {
                        $('#js_instr_chk_' + i).prop('checked', true);
                    }
                    else {
                        $('#js_instr_chk_' + i).prop('checked', false);
                    }
                }
            }
            else{
                for (var i = 1; i <= 13; i++) {
                    $('#js_instr_chk_' + i).prop('checked', true);

                }
            }

            setSuiviChequeInstruction($('#js_instr_rapprochement_banque').val());

            var forms = $('[id^="js_form_instr"]');
            setTabsInformation('tab-a-instruction-dossier',1, forms);
            setFirstLoadInputsColor();

            if(res.reglePaiementClientClient['typeDate'] != null){
                $('#js_cc_regle_paiement_date').val(res.reglePaiementClientClient['typeDate']);
            }
            else{
                $('#js_cc_regle_paiement_date').val(0);
            }

            if(res.reglePaiementClientClient['nbreJour'] != null){
                $('#js_cc_regle_paiement_nbre_jour').val(res.reglePaiementClientClient['nbreJour']);
            }
            else{
                $('#js_cc_regle_paiement_nbre_jour').val(45);
            }

            if(res.reglePaiementClientClient['dateLe'] != null){
                $('#js_cc_regle_paiement_date_le').val(res.reglePaiementClientClient['dateLe']);
                $('#js_cc_regle_paiement_date_le_active').prop('checked',true);
            }
            else{
                $('#js_cc_regle_paiement_date_le').val('');
                $('#js_cc_regle_paiement_date_le').prop('disabled',true);
                $('#js_cc_regle_paiement_date_le_active').prop('checked',false);
            }

            if(res.reglePaiementClientFournisseur['typeDate'] != null){
                $('#js_cf_regle_paiement_date').val(res.reglePaiementClientFournisseur['typeDate']);
            }
            else{
                $('#js_cf_regle_paiement_date').val(0);
            }

            if(res.reglePaiementClientFournisseur['nbreJour'] != null){
                $('#js_cf_regle_paiement_nbre_jour').val(res.reglePaiementClientFournisseur['nbreJour']);
            }
            else{
                $('#js_cf_regle_paiement_nbre_jour').val(45);
            }


            if(res.reglePaiementClientFournisseur['dateLe'] != null){
                $('#js_cf_regle_paiement_date_le').val(res.reglePaiementClientFournisseur['dateLe']);
                $('#js_cf_regle_paiement_date_le_active').prop('checked',true);
            }
            else{
                $('#js_cf_regle_paiement_date_le').val('');
                $('#js_cf_regle_paiement_date_le').prop('disabled',true);
                $('#js_cf_regle_paiement_date_le_active').prop('checked',false);
            }
        }
    });
}

/**
 * Mi-initialiser an'ny halavan'ilay popup
 */
function setModalBodyHeight()
{
    var modalHeight = $('.modal-body .ibox-content').height();

    if(modalHeight>400)
    {
        $('.modal-body').height(400);
    }
    else
    {
        $('.modal-body').height(modalHeight+50);
    }
}

/**
 * Mampijanona ny valeur-an'ilay checkBox na clicker-na ary
 * @param chkBox
 */
function uncheckBox(chkBox)
{
    $(chkBox).prop('checked', !($(chkBox).prop('checked')));
}


function setTouDevelopper(toutDevelopper,selecteur){
    if(toutDevelopper){
        selecteur.closest('.panel-body').find('.ibox').addClass("border-bottom");
        selecteur.closest('.panel-body').find('.ibox-content').css({display: 'none'});
        selecteur.closest('.panel-body').find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-down");

        if(selecteur.hasClass('btn-tout-replier')) {
            selecteur.toggleClass('btn-tout-replier btn-tout-develloper');
        }
        selecteur.html('<i class="fa fa-expand"></i>&nbsp;&nbsp;Développer Tout');

    }
    else{
        selecteur.closest('.panel-body').find('.ibox').removeClass("border-bottom");
        selecteur.closest('.panel-body').find('.ibox-content').css({display: 'block'});
        selecteur.closest('.panel-body').find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-up");

        if(selecteur.hasClass('btn-tout-develloper')) {
            selecteur.toggleClass('btn-tout-develloper btn-tout-replier');
        }
        selecteur.html('<i class="fa fa-compress"></i>&nbsp;&nbsp;Tout Replier');
    }
}


