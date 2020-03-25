/**
 * Created by MAHARO on 16/01/2017.
 */

function setBlink(interval, elem) {

    var btn = elem.closest('.ibox').find('button');

    //Ho an'ny regle de paiement
    if(btn.length == 0){
        if(elem.hasClass('info_regle_paiement')){
            btn = $('#btn-validation-info-regle-paiement');
        }
    }

    interval = setInterval(function () {
        btn.fadeOut(400, function () {
            btn.fadeIn(400);
        });
    }, 10);

    btn.on('click', function () {
        clearInterval(interval);
    });

    elem.on('keypress', function(e){
        if (e.keyCode == 13) {
            clearInterval(interval);
        }
    });

    return interval;
}

var intInstructionDossier;
var intInstructionReglePaiment;

var intIdentificationDossier;
var intCaracteristiqueDossier;
var intDocumentsComptablesFiscaux;
var intDocumentJuridique;
var intReglePaiement;
var intAgaCga;

var intMethConventionComptable;
var intMethPeriodicite;
var intMethMethodeComptable;

var intPrestComptable;
var intPrestFiscal;
var intPrestGestion;
var intPrestJuridique;

var dossier_id = 0;
var withResp = false;
var withRapp = false;
var withReglePaiement = false;

$(document).ready( function () {

    $.extend($.validator.messages, {
        required: "Champ obligatoire",
        equalTo: "Les champs ne correspondent pas",
        email: "Entrer un mail valide"
    });

    $(window).on('resize', function(e) {
        $("#js_recap_dossier_liste").jqGrid('setGridWidth', $('.tab-content').width()-40);
    });


    $(window).on('beforeunload', function(){
        notificationModificationDossier(dossier_id);
        notificationCreationDossier(dossier_id);
        return undefined;
    });


    var clientId = 0;

    setChosen(dossier_id);

    // charger_site();

    charger_site_info_perdos();

    setDate();
    addRequired();

    setInputsColor();

    // disableAPIInput();

    setButtonValiderDossierHeight();
    var gridWidth = $('#tab-instruction-dossier .ibox-content').width() - 20;

    setScrollerHeigt();

    var etape = [
        {tabClass: "tab-a-instruction-dossier", active: 1, valide: 0},
        {tabClass: "tab-a-information-dossier", active: 0, valide: 0},
        {tabClass: "tab-a-methode-comptable", active: 0, valide: 0},
        {tabClass: "tab-a-prestations-demandes", active: 0, valide: 0},
        {tabClass: "tab-a-piece-envoyer", active: 0, valide: 0}
    ];

    // setFirstTabs(0, etape);


    setIdentificationDossier(false);

    setTabProgressBtnValidation(etape,true,dossier_id);

    etape = changeClient(etape,gridWidth);


    $('.tutoriel').on('click', function(e){
        e.preventDefault();
        $( '#modal-tutoriel' ).modal();
    });

    /***************************************************************************/
    /*************************DEBUT VALIDATION PAR CHAMP************************/
    /***************************************************************************/

    /*************************INSTRUCTION DOSSIER*************************/
    $(document).on('keypress','.instr_methode_comptable', function(e) {
        if (e.keyCode == 13) {
            var selects = $('.instr_methode_comptable');
            var current = selects.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (selects[current + 1] != null) {
                if ($(this).valid()) {
                    var nextBox = $(this);

                    if ($(this).val() == '0' && $(this).attr('id') == 'js_instr_rapprochement_banque') {
                        nextBox = selects[current + 2];
                    } else {
                        nextBox = selects[current + 1];
                    }
                    nextBox.focus();
                    saveInstrMethodeComptableV2(field, value);
                }
                else{
                    canValide = false;
                }
            }

            var forms = $('[id="js_form_instr_methode_comptable"]');

            setTabInformationParForms(canValide,etape,0,forms);

            e.preventDefault();
            return false;
        }


    });


    /*****************************BLINK*****************************/

    /******************INSTRUCTION DOSSIER******************/
    $(document).on('change', '#js_form_instr_methode_comptable select', function(){
        intInstructionDossier = setBlink(intInstructionDossier, $(this));
    });

    $(document).on('change', '#js_form_instr_regle_paiement', function(){
        intInstructionReglePaiment = setBlink(intInstructionDossier, $(this));
    });

    $(document).on('change', '#js_cf_regle_paiement_date, #js_cf_regle_paiement_nbre_jour, #js_cf_regle_paiement_date_le,' +
        '#js_cc_regle_paiement_date, #js_cc_regle_paiement_nbre_jour, #js_cc_regle_paiement_date_le', function(){
        if(!withReglePaiement){
            var id = $(this).attr('id');
            var newId = id.slice(0, 3) + id.slice(4, id.length);
            $('#'+newId).val($(this).val());
        }
    });

    /******************END INSTRUCTION DOSSIER******************/


    /*******************INFORMATION DOSSIER***********************/
    $(document).on('change','#js_form_info_identification_dossier select, #js_form_info_identification_dossier input',function () {
        intIdentificationDossier = setBlink(intIdentificationDossier,$(this));
    });

    $(document).on('change','#js_form_info_caracteristique_dossier select, #js_form_info_caracteristique_dossier input',function () {
        intCaracteristiqueDossier = setBlink(intCaracteristiqueDossier,$(this));
    });

    $(document).on('change','#js_form_info_document_comptable_fiscaux select, #js_form_info_document_comptable_fiscaux input',function () {
        intDocumentsComptablesFiscaux = setBlink(intDocumentsComptablesFiscaux,$(this));
    });

    $(document).on('change','#js_form_info_forme_juridique select, #js_form_info_forme_juridique input',function () {
        intDocumentJuridique = setBlink(intDocumentJuridique,$(this));
    });

    $(document).on('change','#js_form_info_regle_paiement select, #js_form_info_regle_paiement input',function () {
        intReglePaiement = setBlink(intReglePaiement,$(this));
    });

    $(document).on('change','#js_form_prest_aga_cga select, #js_form_prest_aga_cga input',function () {
        intAgaCga = setBlink(intAgaCga,$(this));
    });

    /*******************END INFORMATION DOSSIER***********************/


    /*******************METHODE COMPTABLE***********************/
    $(document).on('change','#js_form_meth_convention_comptable select, #js_form_meth_convention_comptable input',function () {
        intMethConventionComptable = setBlink(intMethConventionComptable,$(this));
    });

    $(document).on('change','#js_form_meth_periodicite select, #js_form_meth_periodicite input',function () {
        intMethPeriodicite = setBlink(intMethPeriodicite,$(this));
    });

    $(document).on('change','#js_form_meth_methode_comptable select, #js_form_meth_methode_comptable input',function () {
        intMethMethodeComptable = setBlink(intMethMethodeComptable,$(this));
    });
    /*******************END METHODE COMPTABLE***********************/


    /***************************PRESTATION DEMANDES***************************/
    $(document).on('change','#js_form_prest_comptable select, #js_form_prest_comptable input',function () {
        intPrestComptable = setBlink(intPrestComptable,$(this));
    });

    $(document).on('change','#js_form_prest_fiscal select, #js_form_prest_fiscal input',function () {
        intPrestFiscal = setBlink(intPrestFiscal,$(this));
    });

    $(document).on('change','#js_form_prest_gestion select, #js_form_prest_gestion input',function () {
        intPrestGestion = setBlink(intPrestGestion,$(this));
    });

    $(document).on('change','#js_form_prest_juridique select, #js_form_prest_juridique input',function () {
        intPrestJuridique = setBlink(intPrestJuridique,$(this));
    });

    /***************************END BLINK***************************/

    $(document).on('keypress', '.info_identification_dossier', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".info_identification_dossier");
            var current = inputs.index(this);

            var field = $(this).attr('data-field');
            var value = $(this).val();

            var id = $(this).attr('id');

            var canValide = true;

            if (inputs[current + 1] != null) {

                if($(this).valid()) {

                    var nextInput = $(this);

                    switch (id) {
                        case 'js_nom_dossier':
                            var canSave = verifierNomDossier(dossier_id);

                            if (canSave) {

                                var res = saveInformationDossierV2(dossier_id, field, value, gridWidth, etape);

                                if (res[2] == 1) {
                                    dossierId = res[0];
                                    // $('#js_dossier_id').val(res[0]);

                                    dossier_id = res[0];

                                    setChosen(dossier_id);
                                }
                            }

                            nextInput = inputs[current + 1];

                            break;

                        case 'js_siren_siret':

                            var siren = $('#js_siren_siret').val().replace(/\s/g, "");
                            var estSirenValide = verifierSiren(siren,dossier_id);

                            if(!estSirenValide){
                                canValide = false;
                            }

                            else {
                                nextInput = inputs[current + 4];
                            }

                            break;
                        case 'js_nom_mandataire':
                            if ($('#js_date_debut_activite').prop('disabled') == false) {
                                nextInput = inputs[current + 1];
                            } else {
                                nextInput = inputs[current + 2];
                            }
                            break;

                        case 'js_code_ape':
                            value = $(this).attr('data-id');
                            nextInput = inputs[current + 1];

                            break;

                        default:
                            nextInput = inputs[current + 1];
                            break;
                    }

                    if(id != 'js_nom_dossier'){
                        if(dossier_id != 0 && dossier_id!= '' && dossier_id !=null) {
                            if(canValide == true) {
                                res = saveInformationDossierV2(dossier_id, field, value, gridWidth, etape);

                                if (res[2] == 1) {

                                    dossier_id  = res[0];

                                    setChosen(dossier_id);
                                }
                            }
                        }
                    }

                    nextInput.focus();
                }
                else{
                    canValide = false;
                }
            }
            else{

                if($(this).valid()) {
                    $('#js_regime_fiscal').focus();

                    res = saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);

                    if (res[2] == 1) {

                        dossier_id = res[0];
                    }
                }
                else{
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_info"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = (canValide && canValideForms);

            setTabInformationParForms(canValide,etape,1,forms);


            e.preventDefault();
            return false;
        }
    });

    $(document).on('change', '#js_date_cloture, #js_date_debut_activite', function() {

        var canValide = false;

        if ($(this).valid()) {

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if(dossier_id != 0 && dossier_id!= '' && dossier_id !=null) {
                // saveInformationDossierV2(dossier_id, field, value, gridWidth, etape);
                canValide = true;
            }
        }

        var forms = $('[id^="js_form_info"]');
        var canValideForms = true;
        forms.each(function () {
            if (!($(this).valid())) {
                canValideForms = false;
            }
        });

        canValide = (canValideForms && canValide);

        setTabInformationParForms(canValide, etape, 1, forms);
    });


    $(document).on('change', '#js_date_debut_activite, #js_mois_cloture, #js_date_cloture' , function(){
        setDatePremiereCloture();
    });

    $(document).on('keypress', '.info_caracteristique_dossier', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".info_caracteristique_dossier");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {

                    var nextInput = $(this);

                    var id = $(this).attr('id');

                    switch (id) {
                        case 'js_forme_activite':
                            //Raha tsy profession liberale dia grisé ny champ manaraka
                            if ($('option:selected', $(this)).attr('data-code') != "CODE_PROFESSION_LIBERALE") {
                                nextInput = inputs[current + 2];
                            }
                            else {
                                nextInput = inputs[current + 1];
                            }
                            break;

                        case 'js_profession_liberale':
                            value = $(this).attr('data-id');
                            nextInput = inputs[current + 1];
                            break;

                        case 'js_tva_regime':
                            //Raha non soumis dia grisé ny champ manaraka
                            if ($('option:selected', $(this)).attr('data-code') === 'CODE_NON_SOUMIS' ||
                                $('option:selected', $(this)).attr('data-code') === 'CODE_FRANCHISE') {
                                nextInput = inputs[current + 2];
                            } else {
                                nextInput = inputs[current + 1];
                            }
                            break;

                        case 'js_tva_fait_generateur':
                            if ($('option:selected', $('#js_tva_regime')).attr('data-code') === 'CODE_NON_SOUMIS' ||
                                $('options:selected', $('#js_tva_regime').attr('data-code') === 'CODE_FRANCHISE')) {
                                nextInput = inputs[current + 3];
                            }
                            else {
                                $('#js_tva_taux');//.trigger('chosen:open');
                            }
                            break;

                        case 'js_date_tva':
                            if ($('option:selected', $('#js_tva_regime')).attr('data-code') !== 'CODE_NON_SOUMIS' &&
                                $('option:selected', $('#js_tva_regime').attr('data-code') !== 'CODE_FRANCHISE')) {
                                $('#js_compta_sur_serveur').focus();
                            } else {
                                nextInput = inputs[current + 1];
                            }
                            break;
                        default:
                            nextInput = inputs[current + 1];
                            break;
                    }
                    nextInput.focus();
                    saveInformationDossierV2(dossier_id, field, value, gridWidth, etape);

                }
                else {
                    canValide = false;
                }
            } else {
                if ($(this).valid()) {
                    $('#js_compta_sur_serveur').focus();
                    saveInformationDossierV2(dossier_id, field, value, gridWidth, etape);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_info"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 1, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.info_document_comptable_fiscaux', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".info_document_comptable_fiscaux");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);

                    var id = $(this).attr('id');

                    switch (id) {
                        case 'js_compta_sur_serveur':

                            if ($(this).val() != 0) {
                                $('#js_statut').focus();
                            }
                            else {
                                nextInput = inputs[current + 1];
                                nextInput.focus();
                            }

                            break;
                        default:
                            nextInput = inputs[current + 1];
                            nextInput.focus();
                            break;
                    }


                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    $('#js_statut').focus();
                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_info"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 1, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.info_forme_juridique', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".info_forme_juridique");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();

                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }

            }
            else {
                if ($(this).valid()) {
                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_info"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 1, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.info_regle_paiement', function(e) {

        if (e.keyCode == 13) {
            var inputs = $(".info_regle_paiement");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();
            var type = $(this).attr('data-id');

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();

                    saveReglePaiementV2(dossier_id, field, value, type);
                }

            }
            else {
                if ($(this).valid()) {
                    saveReglePaiementV2(dossier_id, field, value, type);
                }
            }

            e.preventDefault();
            return false;
        }
    });

    /*************************METHODE COMPTABLE*************************/
    $(document).on('keypress', '.meth_convention_comptable', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".meth_convention_comptable");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();

                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    $('#js_tenue_comptablilite').focus();

                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }


            var forms = $('[id^="js_form_meth"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 2, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.meth_periodicite', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".meth_periodicite");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();
                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    $('#js_vente').focus();
                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_meth"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 2, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.meth_methode_comptable', function(e) {
        if (e.keyCode == 13) {
            var field = $(this).attr('data-field');
            var value = $(this).val();

            var canValide = true;

            var inputs = $(".meth_methode_comptable");
            var current = inputs.index(this);
            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();
                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    saveMethodeComptableV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_meth"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 2, forms);

            e.preventDefault();
            return false;

        }
    });

    /*************************PRESTATION DEMANDE*************************/
    $(document).on('keypress', '.prest_comptable', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".prest_comptable");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = $(this);
                    nextInput = inputs[current + 1];
                    nextInput.focus();
                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    $('#js_tva').focus();
                    saveInformationDossierV2(dossier_id, field, value, gridWidth,etape);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_prest"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = (canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 3, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.prest_fiscal', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".prest_fiscal");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {

                var id = $(this).attr('id');

                if ($(this).valid()) {
                    var nextInput = $(this);
                    //Raha Tenue propre dia desactiver ny liasse fiscale & cice
                    if(id == 'js_accomptes_is_solde' && $('#js_prestation_comptable_demande').val() == 0 ) {
                        nextInput = inputs[current + 3];
                    }
                    else {
                        nextInput = inputs[current + 1];
                    }
                    nextInput.focus();
                    savePrestFiscalV2(dossier_id, field, value);
                }else{
                    canValide = false;
                }
            }
            else {
                if ($(this).valid()) {
                    $('#js_situation').focus();

                    savePrestFiscalV2(dossier_id, field, value);
                }
                else {
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_prest"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 3, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.prest_gestion', function(e) {
        if (e.keyCode == 13) {
            var inputs = $(".prest_gestion");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();


            if (inputs[current + 1] != null) {

                if($(this).valid()){
                    var nextInput = inputs[current + 1];
                    nextInput.focus();
                    savePrestGestionV2(dosdossier_idsierId, field, value);
                }else{
                    canValide = false;
                }

            }
            else {
                if ($(this).valid()) {
                    $('#js_rapport_gestion').focus();
                    savePrestGestionV2(dossier_id, field, value);
                }
                else{
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_prest"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 3, forms);

            e.preventDefault();
            return false;
        }
    });

    $(document).on('keypress', '.prest_juridique', function(e) {
        if (e.keyCode === 13) {

            var inputs = $(".prest_juridique");
            var current = inputs.index(this);

            var canValide = true;

            var field = $(this).attr('data-field');
            var value = $(this).val();

            if (inputs[current + 1] != null) {
                if ($(this).valid()) {
                    var nextInput = inputs[current + 1];
                    nextInput.focus();
                    savePrestJuridiqueV2(dossier_id, field, value);
                }
                else{
                    canValide = false;
                }
            }else{
                if($(this).valid()) {
                    savePrestJuridiqueV2(dossier_id, field, value);
                }
                else{
                    canValide = false;
                }
            }

            var forms = $('[id^="js_form_prest"]');
            var canValideForms = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValideForms = false;
                }
            });

            canValide = !!(canValide && canValideForms);

            setTabInformationParForms(canValide, etape, 3, forms);

            e.preventDefault();
            return false;
        }
    });

    /***************************************************************************/
    /**************************FIN VALIDATION PAR CHAMP*************************/
    /***************************************************************************/

    $(document).on('click','#btn-exporter-recap-dossier', function(){
        ExporterExcel();
    });


    // $(document).on('click', '#btn-validation-information-dossier', function() {
    //
    //     if(dossier_id != "") {
    //         notificationCreationDossier(dossier_id);
    //     }
    //
    // });

    $(document).on('change', '#js_assurance', function () {
        setEnvoi($('#js_assurance'), $('#js_envoi_assurance'));
    });

    $(document).on('change', '#js_autre', function () {
        setEnvoi($('#js_autre'), $('#js_envoi_autre'));
    });

    $(document).on('change', '#js_baux', function () {
        setEnvoi($('#js_baux'), $('#js_envoi_baux'));
    });

    $(document).on('change', '#js_balance_n1', function () {
        setEnvoi($('#js_balance_n1'), $('#js_envoi_balance_n1'));
    });

    $(document).on('change','#client',function () {
        etape = changeClient(etape,gridWidth);
        dossier_id = 0;
        charger_site_info_perdos();
    });

    $(document).on('change', '#js_compta_sur_serveur', function (e) {
        var comptaServeur = $("#js_compta_sur_serveur").val();
        setInfoComptableFiscal(comptaServeur);
    });

    $(document).on('change', '#dossier', function (e) {

        notificationModificationDossier(dossier_id);
        notificationCreationDossier(dossier_id);

        changeDossier(etape,gridWidth);

        clientId = $('#client').val();

    });

    $(document).on('change', '#js_emprunt', function () {
        setEnvoi($('#js_emprunt'), $('#js_envoi_emprunt'));
    });

    $(document).on('change', '#js_etat_immobilisation', function () {
        setEnvoi($('#js_etat_immobilisation'), $('#js_envoi_etat_immobilisation'));
    });

    $(document).on('change', '#js_forme_activite', function (e) {
        var formeActivite = $('option:selected', this).attr('data-code');
        setProfessionLiberale(formeActivite);

    });

    $(document).on('change', '#js_forme_juridique', function () {
        // var formeJuridique = $('#js_forme_juridique').val();
        var formeJuridique = $('option:selected', $('#js_forme_juridique')).attr('data-code');
        setTvaRegime(formeJuridique);
        // setMandataireGrid(formeJuridique);

        setStatuts(formeJuridique);
        setKbis(formeJuridique);

        setTypeMandataire(formeJuridique);


        setSirenSiret(formeJuridique);


    });


    $(document).on('change', '#js_grand_livre', function () {
        setEnvoi($('#js_grand_livre'), $('#js_envoi_grand_livre'));
    });

    $(document).on('change', '#js_kbis', function () {
        setEnvoi($('#js_kbis'), $('#js_envoi_kbis'));
    });

    $(document).on('change', '#js_leasing', function () {
        setEnvoi($('#js_leasing'), $('#js_envoi_leasing'));
    });

    $(document).on('change', '#js_liasse_n1', function () {
        setEnvoi($('#js_liasse_n1'), $('#js_envoi_liasse_fisacle_n1'));
    });

    $(document).on('change', '#js_mode_vente', function(){
        var modeVente = $('#js_mode_vente').find('option:selected').attr('data-code');
        setVenteByModeVente(modeVente);
    });

    $(document).on('change', '#js_prestation_comptable_demande', function(){
        var prestationDemande = $('option:selected', $('#js_prestation_comptable_demande')).attr('data-code');
        setPrestationFiscalByPrestationDemande(prestationDemande);
    });

    $(document).on('change', '#js_instr_rapprochement_banque', function (e) {

        var rapprochementBanque = $('#js_instr_rapprochement_banque').val();
        setSuiviCheque(rapprochementBanque, withRapp);

        setEnvoi($('#js_instr_rapprochement_banque'), $('#js_envoi_dernier_rapprochement_banque'));

        if(!withRapp){
            $('#js_rapprochement_banque_doss').val($(this).val());
        }

    });

    $(document).on('change', '#js_rapprochement_banque_doss', function() {

        var suiviChequeDoss = $('#js_suivi_cheque_emis_doss');
        var rapprochementBanqueDoss = $('#js_rapprochement_banque_doss').val();

        if (parseInt(rapprochementBanqueDoss) == 0 || isNaN(rapprochementBanqueDoss)) {
            suiviChequeDoss.attr('disabled', "");
            suiviChequeDoss.removeAttr('required');
            suiviChequeDoss.val("");
            removeRequiredText(suiviChequeDoss);

        }
        else {
            suiviChequeDoss.removeAttr('disabled');
            setRequired(suiviChequeDoss);
        }

        setInputColor(suiviChequeDoss);
    });

    $(document).on('change', '#js_instr_suivi_cheque_emis', function () {
        if(!withRapp){
            $('#js_suivi_cheque_emis_doss').val($(this).val());
        }
    });

    $(document).on('change', '#js_statut', function () {
        setEnvoi($('#js_statut'), $('#js_envoi_statut'));
    });

    $(document).on('change', '#js_tva_derniere_ca3', function () {
        setEnvoi($('#js_tva_derniere_ca3'), $('#js_envoi_tva_derniere_ca3'));
    });

    $(document).on('change', '#js_tva_regime', function (e) {
        e.preventDefault();

        //wqa
        var tvaRegime = $('option:selected', $(this)).attr('data-code');

        setTvaDateTaux(tvaRegime);
        setTvaDateTaux(tvaRegime);
        setTaxeSalaire(tvaRegime);
        setTvaMode(tvaRegime, false);
        setTvaFaitGenerateur(tvaRegime);

        setPrestationTvaByRegimeTva(tvaRegime);

    });

    $(document).on('change', '#js_vente_comptoir', function(){
        $('#js_vente').val($('#js_vente_comptoir').val());
    });

    $(document).on('change', '#js_regime_fiscal', function(){
        var regimeFiscal = $('option:selected', $(this)).attr('data-code');

        var liasseFiscal = $('#js_liasse_fiscale').val();


        setAgaCga(regimeFiscal, liasseFiscal);
        setPrestationFiscalByRegimeFiscal(regimeFiscal);
        setTvaDateByRegimeFiscal(regimeFiscal);
        setTVaRegimeByRegimeFiscal(regimeFiscal, false);

        setModeVenteByRegimeFiscal(regimeFiscal, false);

        setFormeActiviteByRegimeFiscal(regimeFiscal, dossier_id);
        setRegimeImpositionByRegimeFiscal(regimeFiscal, dossier_id);
        setNatureActiviteByRegimeFiscal(regimeFiscal, dossier_id)

    });

    $(document).on('change', '#js_liasse_fiscale', function(){
        var regimeFiscal = $('option:selected', $('#js_regime_fiscal')).attr('data-code');
        var liasseFiscal = $(this).val();

        setAgaCga(regimeFiscal, liasseFiscal);

        setAccomptesIsTeledeclarationLiasse(liasseFiscal);

    });




    $(document).on('click', '.btn-dossier-add', function(){

        notificationModificationDossier(dossier_id);
        notificationCreationDossier(dossier_id);

        if(dossier_id != 0) {

            $('#dossier').val(0);

            dossier_id = 0;
            withResp = false;
            withRapp = false;



            changeDossier(etape, gridWidth);

        }
    });


    // INSTRUCTION DOSSIER
    $(document).on('click','#btn-validation-instr-instructiondossier', function () {

        var forms = $('[id^="js_form_instr_methode_comptable"]');
        var canValide = true;
        forms.each(function () {
            if (!($(this).valid())) {
                canValide = false;
            }
        });

        if (canValide) {
            saveInstrMethodeComptable();
            saveInstrInstructionSaisie();

            saveRemarqueClient($('#client').val());

            saveReglePaiementClient();

            etape[0].valide = 1;

            withReglePaiement = withReglePaiementDossier(dossier_id);

            // $('#js_contenu_tabs a[href="#tab-info-generales]').tab('show');
            $('#js_contenu_tabs a[href="#tab-info-generales"]').tab('show');
        }
        else {
            etape[0].valide = -1;
        }
    });


    $(document).on('click', '#btn-validation-instr-regle-paiement', function () {
        var form = $('#js_form_instr_regle_paiement');

        if (form.valid()) {
            saveReglePaiementClient();
        }

        withReglePaiement = withReglePaiementDossier(dossier_id);
    });


    // FIN INSTRUCTION DOSSIER


    // IDENTIFICATION DOSSIER
    $(document).on('click', '#btn-validation-info-identification', function () {

        var form = $('#js_form_info_identification_dossier');


        if (form.valid())
        {

            // var res = saveIdentification(dossierId,gridWidth,etape);
            //
            //
            // if(res == false){
            //     form.valid();
            //     setInputColor($('#js_siren_siret'))
            // }
            //
            // else{
            //     if(res[2] == 1) {
            //         dossierId = res[0];
            //
            //         // $('#dossier').val(dossierId);
            //         $('#js_dossier_id').val(res[0]);
            //     }
            //
            //     setIdentificationDossier(true);
            // }
            //
            // //ProgressBar any @ fileinput
            // hideProgessBar();
        }
        else {
            sweetAlert();
        }

        var res = saveIdentification(dossier_id,gridWidth,etape, false);


        if(res == false){
            form.valid();
            setInputColor($('#js_siren_siret'))
        }

        else{
            if(res[2] == 1) {
                dossier_id = res[0];
            }

            setIdentificationDossier(true);

            setSummerNote($('#js_instruction_saisie'), true);
        }

        // notificationCreationDossier(dossier_id);

        //ProgressBar any @ fileinput
        hideProgessBar();



    });

    $(document).on('click', '#btn-validation-info-caracteristique-dossier', function () {
        var form = $('#js_form_info_caracteristique_dossier');



        if (form.valid())
        {
            // saveCarracteristique(dossierId);
        }

        //Tester-na ny valeur an'ny tva taux
        // if($('#js_tva_taux').val() == null){
        //     if($('#js_regime_fiscal option:selected').attr('data-code') != 'CODE_NON_SOUMIS'){
        //
        //         formeValide = false;
        //     }
        //     else{
        //         $('#js_tva_taux').removeAttr("style");
        //     }
        // }
        // else{
        //     $('#js_tva_taux').removeAttr("style");
        // }




        else{
            sweetAlert();
        }


        saveCarracteristique(dossier_id, false);


    });

    $(document).on('click', '#btn-validation-info-regle-paiement', function () {
        var form = $('#js_form_info_regle_paiement');

        saveReglePaiement(dossier_id);
        if (form.valid())
        {
            // saveReglePaiement(dossier_id);
        }
        else{
            sweetAlert();
        }

        withReglePaiement = withReglePaiementDossier(dossier_id);

    });




    $(document).on('click', '#btn-validation-info-comptable-fiscal', function () {
        var form = $('#js_form_info_document_comptable_fiscaux');

        saveDocComptableFisc(dossier_id, false);

        if (form.valid())
        {
            // saveDocComptableFisc(dossierId);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-info-document-juridique', function () {
        var form = $('#js_form_info_forme_juridique');

        var forms = $('[id^="js_form_info"]');

        saveDocJuridique(dossier_id, false);

        if (form.valid())
        {
            // saveDocJuridique(dossierId);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-information-dossier', function () {
        var forms = $('[id^="js_form_info"]');
        var canValide = true;
        var formValide = true;
        forms.each(function () {
            if($(this).attr('id') == 'js_form_info_identification_dossier') {
                var siren = $('#js_siren_siret').val().replace(/\s/g, "");

                // if(siren == ''){
                //     show_info('Information', "Le Siren est obligatoire", 'warning');
                //     canValide = false
                // }
                //
                // else

                if (siren != '') {
                    var estSirenValide = verifierSiren(siren, dossier_id);

                    if (!estSirenValide) {
                        show_info_perdos('Information', "Ce Siren existe déjà", 'warning');
                        $('#js_siren_siret').val('');

                        $(this).valid();
                        setInputColor($('#js_siren_siret'));

                        canValide = false;
                    }
                }
            }

            if(canValide) {
                if (!($(this).valid())) {
                    // canValide = false;
                    formValide = false;
                }
            }
        });

        if (canValide) {

            var res = saveIdentification(dossier_id,gridWidth,etape, true);

            if (res[2] == 1) {
                dossier_id = res[0];
                // $('#dossier').val(dossierId);
            }

            saveCarracteristique(dossier_id, true);
            saveDocComptableFisc(dossier_id, true);
            saveDocJuridique(dossier_id, true);

            saveRemarqueDossier(dossier_id,2);

            saveReglePaiement(dossier_id);

            saveAgaCga(dossier_id);


            checkDossier(dossier_id);


            if(formValide == false){
                etape[1].valide = -1;

                sweetAlert();
            }


            else
            {
                if(withResp && withTvaTaux()) {
                    etape[1].valide = 1;

                    $('#js_contenu_tabs a[href="#tab-methodes-comptables"]').tab('show');
                }
                else{
                    etape[1].valide = -1;

                    sweetAlert();
                }
            }

            withReglePaiement = withReglePaiementDossier(dossier_id);

        }
        else {
            etape[1].valide = -1;
        }

    });
    // FIN IDENTIFICATION DOSSIER


    //METHODES COMPTABLES
    $(document).on('click', '#btn-validation-meth-covention-comptable', function () {
        var form = $('#js_form_meth_convention_comptable');
        var forms = $('[id^="js_form_meth"]');

        if (form.valid()) {
            // saveConventionComptable(dossierId);
        }
        else{
            sweetAlert();
        }
        saveConventionComptable(dossier_id);

    });

    $(document).on('click', '#btn-validation-meth-methode-comptable', function () {
        var form = $('#js_form_meth_methode_comptable');
        var forms = $('[id^="js_form_meth"]');

        if (form.valid()) {
            // saveMethodeComptable(dossierId);
        }
        else{
            sweetAlert();
        }

        saveMethodeComptable(dossier_id);

        withRapp = withRappBanque(dossier_id);

    });

    $(document).on('click', '#btn-validation-meth-periodicite', function () {
        var form = $('#js_form_meth_periodicite');
        var forms = $('[id^="js_form_meth"]');


        if (form.valid()) {
            // savePeriodicite(dossierId);
        }
        else{
            sweetAlert();
        }

        savePeriodicite(dossier_id);
    });

    $(document).on('click', '#btn-validation-methode-comptable', function () {
        var forms = $('[id^="js_form_meth"]');
        var canValide = true;
        var formValide = true;
        forms.each(function () {
            if (!($(this).valid())) {
                // canValide = false;
                formValide = false;
            }
        });

        if (canValide) {
            saveConventionComptable(dossier_id);
            saveMethodeComptable(dossier_id);

            withRapp = withRappBanque(dossier_id);

            savePeriodicite(dossier_id);

            saveRemarqueDossier(dossier_id,3);


            if(formValide == false){
                etape[2].valide = -1;
                sweetAlert();
            }

            else {
                etape[2].valide = 1;
                $('#js_contenu_tabs a[href="#tab-prestations-demandes"]').tab('show');
            }

        }
        else {
            etape[2].valide = -1;
        }
    });
    //FIN METHODES COMPTABLES

    // PRESTATION COMPTABLE
    $(document).on('click', '#btn-validation-prest-comptable', function () {
        var form = $('#js_form_prest_comptable');
        var forms = $('[id^="js_form_prest"]');


        savePrestDemande(dossier_id);

        saveRemarqueDossier(dossier_id, 42);
        if (form.valid()) {

            // savePrestDemande(dossierId);
            //
            // saveRemarqueDossier(dossierId,42);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-prest-fiscal', function () {
        var formx = $('#js_form_prest_fiscal');
        var forms = $('[id^="js_form_prest"]');

        savePrestFiscal(dossier_id);

        if (formx.valid()) {
            // savePrestFiscal(dossierId);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-prest-instruction', function(){

        saveInstructionSaisie(dossier_id);
    });

    $(document).on('click', '#btn-validation-prest-gestion', function () {
        var form = $('#js_form_prest_gestion');
        var forms = $('[id^="js_form_prest"]');

        savePrestGestion(dossier_id);

        if (form.valid()) {
            // savePrestGestion(dossierId);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-prest-juridique', function () {
        var form = $('#js_form_prestation_juridique');
        var forms = $('[id^="js_form_prest"]');

        savePrestJuridique(dossier_id);
        if (form.valid()) {
            // savePrestJuridique(dossierId);
        }
        else{
            sweetAlert();
        }
    });

    $(document).on('click', '#btn-validation-prest-aga-cga', function(){
        var form = $('#js_form_prest_aga_cga');

        saveAgaCga(dossier_id);

        if (form.valid()){
            // saveAgaCga(dossier_id);
        }
        else{
            sweetAlert();
        }

    });

    $(document).on('click', '#btn-validation-prestation-demande', function () {
        var forms = $('[id^="js_form_prest"]');
        var canValide = true;
        var formValide = true;
        forms.each(function () {
            if (!($(this).valid())) {
                // canValide = false;
                formValide = false;
            }
        });

        if (canValide) {
            savePrestDemande(dossier_id);
            savePrestFiscal(dossier_id);
            savePrestGestion(dossier_id);
            savePrestJuridique(dossier_id);
            saveInstructionSaisie(dossier_id);
            saveRemarqueDossier(dossier_id,41);

            if(formValide == false){
                etape[3].valide = -1;
                sweetAlert();
            }
            else {
                etape[3].valide = 1;
                $('#js_contenu_tabs a[href="#tab-piece-a-envoyer"]').tab('show');
            }

            // setBtnToutValider('tab-a-piece-envoyer active');
        }
        else {
            etape[3].valide = -1;
        }

    });
    //FIN PRESTATION COMPTABLE


    // PIECES A ENVOYER
    $(document).on('click', '#btn-validation-envoi-information-comptable', function (event) {
        event.preventDefault();

        var forms = $('[id^="js_form_envoi_"]');

        if (dossier_id != 0) {
            uploadFile('js_envoi_balance_n1');
            uploadFile('js_envoi_grand_livre');
            uploadFile('js_envoi_journaux_n1');
            uploadFile('js_envoi_dernier_rapprochement_banque');
            uploadFile('js_envoi_etat_immobilisation');
            uploadFile('js_envoi_liasse_fisacle_n1');
            uploadFile('js_envoi_tva_derniere_ca3');
        }
        else {
            show_info_perdos('INFORMATION', "IL FAUT CHOISIR UN DOSSIER AVANT D'ENVOYER DES PIECES", "warning")
        }

        setTabsInformation('tab-a-piece-envoyer',1,forms);
    });

    $(document).on('click', '#btn-validation-envoi-document-juridique', function (event) {
        event.preventDefault();

        var forms = $('[id^="js_form_envoi_"]');

        if (dossier_id != 0) {
            uploadFile('js_envoi_statut');
            uploadFile('js_envoi_kbis');
            uploadFile('js_envoi_baux');
            uploadFile('js_envoi_assurance');
            uploadFile('js_envoi_autre');
            uploadFile('js_envoi_emprunt');
            uploadFile('js_envoi_leasing');
        }
        else {
            show_info_perdos('INFORMATION', "IL FAUT CHOISIR UN DOSSIER AVANT D'ENVOYER DES PIECES", "warning")

        }

        setTabsInformation('tab-a-piece-envoyer',1,forms);

    });

    $(document).on('click', '#btn-validation-piece-envoyer', function () {
        var forms = $('[id^="js_form_envoi_"]');
        var canValide = true;
        forms.each(function () {
            if (!($(this).valid())) {
                canValide = false;
            }
        });

        if (canValide) {
            //Informations comptables et fiscales

            uploadFile('js_envoi_balance_n1');
            uploadFile('js_envoi_grand_livre');
            uploadFile('js_envoi_journaux_n1');
            uploadFile('js_envoi_dernier_rapprochement_banque');
            uploadFile('js_envoi_etat_immobilisation');
            uploadFile('js_envoi_liasse_fisacle_n1');
            uploadFile('js_envoi_tva_derniere_ca3');
            uploadFile('js_envoi_statut');

            //Documents juridiques
            uploadFile('js_envoi_kbis');
            uploadFile('js_envoi_baux');
            uploadFile('js_envoi_assurance');
            uploadFile('js_envoi_autre');
            uploadFile('js_envoi_emprunt');
            uploadFile('js_envoi_leasing');
            etape[4].valide = 1;
        }
        else {
            etape[4].valide = -1;
        }

        saveRemarqueDossier(dossier_id, 5);

        etape = setTabs(etape, 4);

        setTabsInformation('tab-a-piece-envoyer',1,forms);

    });
    // FIN PIECES A ENVOYER


    $(document).on('click', '.tab-a-recap',function() {
        var site = null;

        var sites = $('#site').find('option');
        if (sites.size() == 2) {
            site = (sites[1]).value;
        }
        //Multisite
        else {
            site = $('#site').val()
        }

        var client = $('#client').val();

        var dos = 0;

        $.ajax({
            data: {idDossier: dossier_id},
            url: Routing.generate('info_perdos_dossier_deboost'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            async: true,
            success: function (data) {

                showRecapGrid(site,client,data);

                try {
                    reloadGrid($('#js_recap_dossier_liste'), Routing.generate('info_perdos_recap', {siteId: site, clientId: client}));

                }
                catch (e)
                {}

            }
        });



    });



    $(document).on('click', '#btn-go-scriptura',function() {
        var site = null;


        var mois = $('#js_mois').val();
        var annee = $('#js_exercice').val();

        showScripturaGrid(annee, mois);

        try {
            reloadGrid($('#js_scriptura_liste'), Routing.generate('info_perdos_scriptura', {annee:annee, mois:mois}));

        }
        catch (e) {
        }


    });




    $(document).on('dblclick', '#js_profession_liberale', function () {

        var estDesactive = $(this).prop('disabled');

        if (!estDesactive) {
            var lien = Routing.generate('info_perdos_profession_show_tree');
            $.ajax({
                data: {},
                url: lien,
                type: 'POST',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: false
                ,
                dataType: 'html',
                success: function (data) {

                    show_modal(data, 'Profession Liberale', 'bounceInRight');

                }
            });

            $.ajax({

                datatype: 'json',
                url: Routing.generate('info_perdos_profession_tree'),
                type: 'GET',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: true
                ,
                dataType: 'html',
                success: function (data) {
                    $('#js_tree_profession_liberale').jstree(
                        {
                            'core': $.parseJSON(data),

                            'search': {
                                'case_insensitive': true, 'show_only_matches': true,
                                search_callback: function (searchString, node) {
                                    var tempSearchString = searchString.toLowerCase();
                                    for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
                                        tempSearchString = tempSearchString.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
                                    }
                                    var text = (node.text || '').toLowerCase();
                                    for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
                                        text = text.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
                                    }
                                    return text.indexOf(tempSearchString) != -1;
                                }

                            },
                            plugins: ['search']
                        })
                        .bind("dblclick.jstree", function (event) {

                            var CurrentNode = $(this).jstree("get_selected");
                            var selectId = 0;

                            try {
                                selectId = $(this).jstree().get_selected(true)[0].id;
                            }
                            catch (e) {

                            }
                            selectId = parseInt(selectId);

                            var selectText = $('#' + CurrentNode).text();

                            if (!isNaN(selectId) && selectId != 0) {

                                $('#js_profession_liberale').val(selectText);
                                $('#js_profession_liberale').attr('data-id', selectId);

                                close_modal();
                            }

                            setInputColor($('#js_profession_liberale'));
                        });
                }
            });
        }
    });

    $(document).on('click', '#btn-search-ape', function () {
        $('#js_tree_code_ape').jstree('search', $('#js_search_ape').val());

    });

    $(document).on('click', '#btn-search-profession', function(){
        $('#js_tree_profession_liberale').jstree('search', $('#js_search_profession_liberale').val());
    });

    $(document).on('click', '.btn-dossier-edit', function () {
        changeDossier(etape, gridWidth);

        $('.btn-dossier-edit').remove();
    });

    $(document).on('click', '.navbar-minimalize', function () {

        setTimeout(function () {


            var windowWitdh = $(window).width();

            if (windowWitdh > 1200) {
                $(".tableau").css("width", $('#wrapper-content').width());
                $("#js_recap_dossier_liste").jqGrid('setGridWidth', $('#wrapper-content').width() - 40);

            }


        }, 1000);


    });

    $(document).on('click', '#js_f_regle_paiement_date_le_active, #js_c_regle_paiement_date_le_active', function(){

        setDateLe($(this));

    });

    $(document).on('click', '#js_cf_regle_paiement_date_le_active, #js_cc_regle_paiement_date_le_active', function(){
        setDateLeClient($(this));
    });

    var defaultDiacriticsRemovalMap = [
        {
            'base': 'A',
            'letters': /[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g
        },
        {'base': 'AA', 'letters': /[\uA732]/g},
        {'base': 'AE', 'letters': /[\u00C6\u01FC\u01E2]/g},
        {'base': 'AO', 'letters': /[\uA734]/g},
        {'base': 'AU', 'letters': /[\uA736]/g},
        {'base': 'AV', 'letters': /[\uA738\uA73A]/g},
        {'base': 'AY', 'letters': /[\uA73C]/g},
        {
            'base': 'B',
            'letters': /[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g
        },
        {
            'base': 'C',
            'letters': /[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g
        },
        {
            'base': 'D',
            'letters': /[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g
        },
        {'base': 'DZ', 'letters': /[\u01F1\u01C4]/g},
        {'base': 'Dz', 'letters': /[\u01F2\u01C5]/g},
        {
            'base': 'E',
            'letters': /[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g
        },
        {'base': 'F', 'letters': /[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
        {
            'base': 'G',
            'letters': /[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g
        },
        {
            'base': 'H',
            'letters': /[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g
        },
        {
            'base': 'I',
            'letters': /[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g
        },
        {'base': 'J', 'letters': /[\u004A\u24BF\uFF2A\u0134\u0248]/g},
        {
            'base': 'K',
            'letters': /[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g
        },
        {
            'base': 'L',
            'letters': /[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g
        },
        {'base': 'LJ', 'letters': /[\u01C7]/g},
        {'base': 'Lj', 'letters': /[\u01C8]/g},
        {'base': 'M', 'letters': /[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
        {
            'base': 'N',
            'letters': /[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g
        },
        {'base': 'NJ', 'letters': /[\u01CA]/g},
        {'base': 'Nj', 'letters': /[\u01CB]/g},
        {
            'base': 'O',
            'letters': /[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g
        },
        {'base': 'OI', 'letters': /[\u01A2]/g},
        {'base': 'OO', 'letters': /[\uA74E]/g},
        {'base': 'OU', 'letters': /[\u0222]/g},
        {
            'base': 'P',
            'letters': /[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g
        },
        {'base': 'Q', 'letters': /[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
        {
            'base': 'R',
            'letters': /[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g
        },
        {
            'base': 'S',
            'letters': /[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g
        },
        {
            'base': 'T',
            'letters': /[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g
        },
        {'base': 'TZ', 'letters': /[\uA728]/g},
        {
            'base': 'U',
            'letters': /[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g
        },
        {'base': 'V', 'letters': /[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
        {'base': 'VY', 'letters': /[\uA760]/g},
        {
            'base': 'W',
            'letters': /[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g
        },
        {'base': 'X', 'letters': /[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
        {
            'base': 'Y',
            'letters': /[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g
        },
        {
            'base': 'Z',
            'letters': /[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g
        },
        {
            'base': 'a',
            'letters': /[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g
        },
        {'base': 'aa', 'letters': /[\uA733]/g},
        {'base': 'ae', 'letters': /[\u00E6\u01FD\u01E3]/g},
        {'base': 'ao', 'letters': /[\uA735]/g},
        {'base': 'au', 'letters': /[\uA737]/g},
        {'base': 'av', 'letters': /[\uA739\uA73B]/g},
        {'base': 'ay', 'letters': /[\uA73D]/g},
        {
            'base': 'b',
            'letters': /[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g
        },
        {
            'base': 'c',
            'letters': /[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g
        },
        {
            'base': 'd',
            'letters': /[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g
        },
        {'base': 'dz', 'letters': /[\u01F3\u01C6]/g},
        {
            'base': 'e',
            'letters': /[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g
        },
        {'base': 'f', 'letters': /[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
        {
            'base': 'g',
            'letters': /[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g
        },
        {
            'base': 'h',
            'letters': /[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g
        },
        {'base': 'hv', 'letters': /[\u0195]/g},
        {
            'base': 'i',
            'letters': /[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g
        },
        {'base': 'j', 'letters': /[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
        {
            'base': 'k',
            'letters': /[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g
        },
        {
            'base': 'l',
            'letters': /[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g
        },
        {'base': 'lj', 'letters': /[\u01C9]/g},
        {'base': 'm', 'letters': /[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
        {
            'base': 'n',
            'letters': /[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g
        },
        {'base': 'nj', 'letters': /[\u01CC]/g},
        {
            'base': 'o',
            'letters': /[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g
        },
        {'base': 'oi', 'letters': /[\u01A3]/g},
        {'base': 'ou', 'letters': /[\u0223]/g},
        {'base': 'oo', 'letters': /[\uA74F]/g},
        {
            'base': 'p',
            'letters': /[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g
        },
        {'base': 'q', 'letters': /[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
        {
            'base': 'r',
            'letters': /[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g
        },
        {
            'base': 's',
            'letters': /[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g
        },
        {
            'base': 't',
            'letters': /[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g
        },
        {'base': 'tz', 'letters': /[\uA729]/g},
        {
            'base': 'u',
            'letters': /[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g
        },
        {'base': 'v', 'letters': /[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
        {'base': 'vy', 'letters': /[\uA761]/g},
        {
            'base': 'w',
            'letters': /[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g
        },
        {'base': 'x', 'letters': /[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
        {
            'base': 'y',
            'letters': /[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g
        },
        {
            'base': 'z',
            'letters': /[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g
        }
    ];

    $(document).on('dblclick', '.tree.ape', function () {

        var estDesactive = $(this).prop('disabled');

        if(!estDesactive) {

            var lien = Routing.generate('info_perdos_code_ape_show_tree');
            $.ajax({
                data: {},
                url: lien,
                type: 'POST',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: false
                ,
                dataType: 'html',
                success: function (data) {

                    show_modal(data, 'Code APE', 'bounceInRight');

                }
            });


            $.ajax({
                datatype: 'json',
                url: Routing.generate('info_perdos_code_ape'),
                type: 'GET',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: true
                ,
                dataType: 'html',
                success: function (data) {
                    $('#js_tree_code_ape').jstree({
                        'core': $.parseJSON(data),

                        'search': {
                            'case_insensitive': true, 'show_only_matches': true,
                            search_callback: function (searchString, node) {
                                var tempSearchString = searchString.toLowerCase();
                                for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
                                    tempSearchString = tempSearchString.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
                                }
                                var text = (node.text || '').toLowerCase();
                                for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
                                    text = text.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
                                }
                                return text.indexOf(tempSearchString) != -1;
                            }

                        },
                        plugins: ['search']
                    })
                        .bind("dblclick.jstree", function (event) {

                            var CurrentNode = $(this).jstree("get_selected");
                            var selectId = $(this).jstree().get_selected(true)[0].id;
                            selectId = parseInt(selectId);

                            var selectText = $('#' + CurrentNode).text();

                            if (!isNaN(selectId) && selectId != 0) {
                                var codeApe = "";
                                var libelle = "";

                                //734: N/A
                                if(selectId != 734) {
                                    codeApe = selectText.substring(0, 6);
                                    libelle = selectText.substring(7);
                                }
                                else{
                                    codeApe = "N/A";
                                    libelle = "N/A";
                                }

                                $('#js_intitule_code_ape').val(libelle);
                                $('#js_code_ape').val(codeApe);
                                $('#js_code_ape').attr('data-id', selectId);

                                close_modal();
                            }

                            setInputColor($('#js_code_ape'));
                            setInputColor($('#js_intitule_code_ape'));
                        });
                }
            });
        }
    });

    $(document).on('focusout', '#js_nom_dossier', function () {
        try {
            verifierNomDossier(dossier_id);
        }
        catch(e) {
        }
    });

    $(document).on('focusout', '#js_siren_siret, #js_aga_cga_siren', function(){
        var siren = $(this).val();
        // verifierSiren(siren);

        if(isSiren(siren) || isSiret(siren)) {
            $(this).val(sirenFormat(siren));
        }

        else{

            var formeJuridique = $('#js_forme_juridique option:selected').attr('data-code');
            setSirenSiret(formeJuridique);
        }


    });

    $(document).on('focus', '#js_siren_siret, #js_aga_cga_siren', function(){
        $(this).val($(this).val().replace(/\s/g, ""));
    });

    $(document).on('input', '#js_search_ape', function () {
        $('#js_tree_code_ape').jstree('search', $('#js_search_ape').val());
    });

    $(document).on('input', '#js_search_profession_liberale', function () {
        $('#js_tree_profession_liberale').jstree('search', $('#js_search_profession_liberale').val());
    });

    // $('.tree').on('keypress', function (e) {
    //     e.preventDefault();
    // });

    // $('.api').on('keypress', function (e) {
    //     e.preventDefault();
    // });

    $(document).on('keyup', '.file-caption', function (e) {
        if (e.which == 8 || e.which == 46) {
            if (!($(this).is('.file-caption-disabled'))) {
                //Raha efa envoyé ilay pièce dia tsy reseter-na ny fileInput
                if (!($(this).find('i').is('.fa-check'))) {
                    var fileInput = $(this).parent().find('input');
                    fileInput.fileinput('reset');
                    fileInput.val('');
                }
            }
        }
    });

    $(document).on('keydown', '#js_siren_siret, #js_aga_cga_siren', function(e){

        var keyCode = e.keyCode || e.which;

        if(keyCode == 13 || keyCode == 9) {
            // verifierSirenSiretInfoGreffe();

            if($(this).attr('id') == 'js_siren_siret') {
                verifierSirenSiretInsee(dossier_id, true);
            }
            else{
                verifierSirenSiretInsee(dossier_id,false);
            }
            //
            // $(this).val(sirenFormat($(this).val()));
        }
    });

    $(document).on('keydown', '#js_code_ape', function(e){
        var keyCode = e.which || e.keyCode;

        if(keyCode == 13 || keyCode == 9){

            verifierCodeApe();

        }
    });


    $(document).on('click', '.swal2-cancel', function(){
        $('#js_aga_cga_adherant').prop('disabled', false);
        $('#js_aga_cga_adherant').val('0').change();

    });


    $(document).on('change', '#js_aga_cga_adherant', function(){
        setAgaCgaChange($(this).val(), false);
    });


});

/**
 * Manisy attribut required & * @zay champ obligatoire
 * @param input
 */
function setRequired(input){
    input.attr('required', true);
    addRequiredText(input);
}

/**
 * Manisy * @izay champ obligatoire
 * @param input
 */
function addRequiredText(input){

    input.closest('.form-group').find('label').find('span').remove();
    input.closest('.form-group').find('label').append('<span>&nbsp;*</span>');
}

/**
 * Manala ny * @izay champ tsy obligatoire
 * @param input
 */
function removeRequiredText(input){
    input.closest('.form-group').find('label').find('span').remove();
}

/**
 * Mametaka ny attribut Required ho an'ny champ rehetra
 */
function addRequired() {

    removeRequired();

    //Identification dossier
    setRequired($('#js_nom_dossier'));
    setRequired($('#js_raison_social'));
    setRequired($('#js_siren_siret'));
    setRequired($('#js_date_debut_activite'));
    setRequired($('#js_forme_juridique'));
    setRequired($('#js_code_ape'));
    setRequired($('#js_intitule_code_ape'));
    setRequired($('#js_type_mandataire'));
    setRequired($('#js_nom_mandataire'));
    setRequired($('#js_date_cloture'));
    setRequired($('#js_mois_cloture'));

    //caracteristique dossier
    setRequired($('#js_regime_fiscal'));
    setRequired($('#js_regime_imposition'));
    setRequired($('#js_nature_activite'));
    setRequired($('#js_forme_activite'));
    setRequired($('#js_profession_liberale'));
    setRequired($('#js_mode_vente'));
    setRequired($('#js_tva_regime'));
    setRequired($('#js_taxe_salaire'));
    setRequired($('#js_tva_mode'));

    //Information comptable et fiscale
    setRequired($('#js_compta_sur_serveur'));
    setRequired($('#js_balance_n1'));
    setRequired($('#js_grand_livre'));
    setRequired($('#js_tva_taux'));
    setRequired($('#js_date_tva'));
    setRequired($('#js_liasse_n1'));

    //Document juridique
    setRequired($('#js_statut'));
    setRequired($('#js_kbis'));

    //Methodes comptables
    setRequired($('#js_vente'));
    setRequired($('#js_achat'));
    setRequired($('#js_banque'));
    setRequired($('#js_convention_compbtale'));
    setRequired($('#js_suivi_cheque_emis'));
    setRequired($('#js_saisie_od_paye'));
    // setRequired($('#js_analytique'));
    setRequired($('#js_vente_comptoir'));
    setRequired($('#js_vente_facture'));
    setRequired($('#js_tenue_comptablilite'));

    //Prestations demandées
    setRequired($('#js_tva'));
    setRequired($('#js_accomptes_is_solde'));
    setRequired($('#js_liasse_fiscale'));
    setRequired($('#js_cice'));
    setRequired($('#js_cvae'));
    setRequired($('#js_tvts'));
    setRequired($('#js_das2'));
    setRequired($('#js_cfe'));
    setRequired($('#js_dividendes'));
    setRequired($('#js_prestation_comptable_demande'));
    setRequired($('#js_deb'));
    setRequired($('#js_dej'));

    //Instruction tous dossiers
    setRequired($('#js_instr_rapprochement_banque'));
    setRequired($('#js_instr_suivi_cheque_emis'));
    // setRequired($('#js_instr_logiciel'));

    setRequired($('#js_rapprochement_banque_doss'));
    setRequired($('#js_suivi_cheque_emis_doss'));


    setRequired($('#js_instr_gestion_date_ecriture'));
}


/**
 * Raha mbola new_row ilay jqGird dia tsy afaka manampy lignes
 * @param jqGrid
 * @returns {boolean}
 */
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

/**
 * Mametraka icone check raha efa envoyé ilay pièce
 * @param cmb
 * @param inputEnvoi
 */
function checkEnvoi(cmb, inputEnvoi){
    cmb.each(function() {
        var options = $(this).find('option');
        options.each(function(){
            var estEnvoye = $(this).attr('est-envoye');
            if(estEnvoye == 1)
            {
                var fileCapt = inputEnvoi.closest('.input-group').find('.file-caption-name');
                fileCapt.append('<i class="fa fa-check kv-caption-icon fa-2x" ></i>');
            }
        });

    });
}

function disableAPIInput() {
    var forms = $('.api');

    forms.each(function () {
        $(this).prop("disabled", true);
    });

    $('#js_forme_juridique').prop('disabled', false);
}

/**
 * mi-descativer an'ny combo ao @bloc information comptable et fiscal sy documents juridiques
 * raha efa envoyé ny pièce
 */
function disableEstEnvoye() {
    var selectRequired = $(".info-comptable-fiscal" );
    selectRequired.each(function() {
        var options = $(this).find('option');
        options.each(function(){
            var estEnvoye = $(this).attr('est-envoye');
            if(estEnvoye == 1)
            {
                $(this).parent().prop('disabled',true);
                $(this).parent().val($(this).val());
            }
        });

    });


    var selectInfoJuridique= $(".info-juridique" );
    selectInfoJuridique.each(function() {
        var options = $(this).find('option');
        options.each(function(){
            var estEnvoye = $(this).attr('est-envoye');
            if(estEnvoye == 1)
            {
                $(this).parent().prop('disabled',true);
                $(this).parent().val($(this).val());
            }
        });

    });
}

/**
 * mi-descativer an'ny combo ao @bloc information comptable et fiscal sy documents juridiques
 * rehefa avy envoyé ny pièce mifandraika @ ilay combo
 * @param cmbEnvoi
 */
function disableEstEnvoyeAfterEnvoi(cmbEnvoi) {

    var id = cmbEnvoi.attr('id');

    switch (id){

        //Documents comptables et fiscaux
        case 'js_envoi_plan_comptable':
            $('#js_plan_comptable').prop('disabled',true);
            break;
        case 'js_envoi_archive_comptable':
            $('#js_archive_comptable').prop('disabled',true);
            break;
        case 'js_envoi_grand_livre':
            $('#js_grand_livre').prop('disabled',true);
            break;
        case 'js_envoi_dernier_rapprochement_banque':
            $('#js_rapprochement_banque_n1').prop('disabled',true);
            break;
        case 'js_envoi_etat_immobilisation':
            $('#js_etat_immobilisation').prop('disabled',true);
            break;
        case 'js_envoi_liasse_fisacle_n1':
            $('#js_liasse_n1').prop('disabled',true);
            break;
        case 'js_envoi_tva_derniere_ca3':
            $('#js_tva_derniere_ca3').prop('disabled',true);
            break;

        //Documents juridiques
        case 'js_envoi_statut':
            $('#js_statut').prop('disabled',true);
            break;
        case 'js_envoi_kbis':
            $('#js_kbis').prop('disabled',true);
            break;
        case 'js_envoi_baux':
            $('#js_baux').prop('disabled',true);
            break;
        case 'js_envoi_assurance':
            $('#js_assurance').prop('disabled',true);
            break;
        case 'js_envoi_autre':
            $('#js_autre').prop('disabled',true);
            break;
        case 'js_envoi_emprunt':
            $('#js_emprunt').prop('disabled',true);
            break;
        case 'js_envoi_leasing':
            $('#js_leasing').prop('disabled',true);
            break;

    }

}

/**
 * Mi-activer ny champ rehetra sy ny bouton rehetra
 * @param dossierId
 */
function enableFileAllInput(dossierId) {

    var forms = $('[id^="js_form_"]');

    forms.each(function () {

        $(this).find('input').prop("disabled", false);
        $(this).find('select').prop("disabled", false);

    });
    // $("#js_tva_taux").prop('disabled', false);
    // setChosen(dossierId);

    var btns = $('[id^="btn-validation"]');
    btns.each(function () {
        $(this).prop("disabled", false);
    });

    //raha efa misy dossierId dia averina activer-na ny champ efa activé ho an'ilay dossier
    // if (dossierId != 0) {
    //     firstLoad();
    // }


}

/**
 * Mi-gerer ny champ rehetra miankina amin'ny dossier:
 * Mi-desactiver/activer
 * Mi-definir ny champ par defaut
 */
function firstLoad(){
    //Change premier exercice
    // var premierExercice = $('#js_premier_exercice').val();
    // setDateDebutActivite(premierExercice);
    // setKbis(premierExercice);

    //Change forme juridique
    var formeJuridique = $('option:selected', $('#js_forme_juridique')).attr('data-code');
    // var formeJuridique = $('#js_forme_juridique').val();
    setTvaRegime(formeJuridique);
    // setMandataireGrid(formeJuridique);


    setTypeMandataire(formeJuridique);

    setSirenSiret(formeJuridique);

    //Change forme activité (profession libérale)
    var formeActivite = $('option:selected', $('#js_forme_activite')).attr('data-code');
    setProfessionLiberale(formeActivite);


    //Change Tva Regime
    var tvaRegime = $('option:selected', $('#js_tva_regime')).attr('data-code');
    var comptaServeur = $('#js_compta_sur_serveur').val();
    setTvaDateTaux(tvaRegime);
    setTvaDateTaux(tvaRegime);
    setTaxeSalaire(tvaRegime);
    setTvaMode(tvaRegime, true);
    setTvaFaitGenerateur(tvaRegime);



    //Change compta sur serveur
    setInfoComptableFiscal(comptaServeur);

    // //Change mode vente
    // var modeVente = $('#js_mode_vente').find('option:selected').attr('data-code');
    // setVenteByModeVente(modeVente, true);

    setDateLe($('#js_f_regle_paiement_date_le_active'));
    setDateLe($('#js_c_regle_paiement_date_le_active'));

    setDatePremiereCloture();

    //Change prestation

    var prestationDemande = $('option:selected', $('#js_prestation_comptable_demande')).attr('data-code');

    var regimeFiscal = $('option:selected', $('#js_regime_fiscal')).attr('data-code');
    var liasseFiscal = $('#js_liasse_fiscale').val();
    setAgaCga(regimeFiscal, liasseFiscal, true);


    var adherant = $('#js_aga_cga_adherant').val();
    setAgaCgaChange(adherant,true);

    // setPrestationFiscalByRegimeFiscal(regimeFiscal, true);
    setTvaDateByRegimeFiscal(regimeFiscal);
    setTVaRegimeByRegimeFiscal(regimeFiscal, true);

    setModeVenteByRegimeFiscal(regimeFiscal, true);


    setPrestationFiscalByPrestationDemande(prestationDemande, true);

    // setAccomptesIsTeledeclarationLiasse(liasseFiscal);



    setSuiviChequeDossier($('#js_rapprochement_banque_doss').val());


    setStatuts(formeJuridique);
    setKbis(formeJuridique);



    setFormeActiviteByRegimeFiscal(regimeFiscal, dossier_id);
    setRegimeImpositionByRegimeFiscal(regimeFiscal, dossier_id);
    setNatureActiviteByRegimeFiscal(regimeFiscal, dossier_id);
    // setTypeActiviteByRegimeFiscal(regimeFiscal);

    setPrestationFiscalByRegimeFiscal(regimeFiscal, true);

    setPrestationTvaByRegimeTva(tvaRegime);


    setAccomptesIsTeledeclarationLiasse(liasseFiscal);

    setEnvoi($('#js_balance_n1'),$('#js_envoi_balance_n1'));
    setEnvoi($('#js_plan_comptable'),$('#js_envoi_plan_comptable'));
    setEnvoi($('#js_archive_comptable'),$('#js_envoi_archive_comptable'));
    setEnvoi($('#js_grand_livre'),$('#js_envoi_grand_livre'));
    setEnvoi($('#js_rapprochement_banque_n1'),$('#js_envoi_dernier_rapprochement_banque'));
    setEnvoi($('#js_etat_immobilisation'),$('#js_envoi_etat_immobilisation'));
    setEnvoi($('#js_liasse_n1'),$('#js_envoi_liasse_fisacle_n1'));
    setEnvoi($('#js_tva_derniere_ca3'),$('#js_envoi_tva_derniere_ca3'));

    setEnvoi($('#js_statut'),$('#js_envoi_statut'));
    setEnvoi($('#js_kbis'),$('#js_envoi_kbis'));
    setEnvoi($('#js_baux'),$('#js_envoi_baux'));
    setEnvoi($('#js_assurance'),$('#js_envoi_assurance'));
    setEnvoi($('#js_autre'),$('#js_envoi_autre'));
    setEnvoi($('#js_emprunt'),$('#js_envoi_emprunt'));
    setEnvoi($('#js_leasing'),$('#js_envoi_leasing'));
}

/**
 * Mi-valider raha marina ny Siren Nampidirina
 * @param siren
 * @returns {*}
 */
function isSiren(siren) {
    var estValide;
    if ( (siren.length != 9) || (isNaN(siren)) )
        estValide = false;
    else {
        // Donc le SIREN est un numérique à 9 chiffres
        var somme = 0;
        var tmp;
        for (var cpt = 0; cpt<siren.length; cpt++) {
            if ((cpt % 2) == 1) { // Les positions paires : 2ème, 4ème, 6ème et 8ème chiffre
                tmp = siren.charAt(cpt) * 2; // On le multiplie par 2
                if (tmp > 9)
                    tmp -= 9;	// Si le résultat est supérieur à 9, on lui soustrait 9
            }
            else
                tmp = siren.charAt(cpt);
            somme += parseInt(tmp);
        }

        if ((somme % 10) == 0)
            estValide = true;	// Si la somme est un multiple de 10 alors le SIREN est valide
        else
            estValide = false;
    }
    return estValide;
}

/**
 * Mi-valider raha marina ny Siret Nampidirina
 * @param siret
 * @returns {*}
 */
function isSiret(siret) {
    var estValide;
    if ( (siret.length != 14) || (isNaN(siret)) )
        estValide = false;
    else {
        // Donc le SIRET est un numérique à 14 chiffres
        // Les 9 premiers chiffres sont ceux du SIREN (ou RCS), les 4 suivants
        // correspondent au numéro d'établissement
        // et enfin le dernier chiffre est une clef de LUHN.
        var somme = 0;
        var tmp;
        for (var cpt = 0; cpt<siret.length; cpt++) {
            if ((cpt % 2) == 0) { // Les positions impaires : 1er, 3è, 5è, etc...
                tmp = siret.charAt(cpt) * 2; // On le multiplie par 2
                if (tmp > 9)
                    tmp -= 9;	// Si le résultat est supérieur à 9, on lui soustrait 9
            }
            else
                tmp = siret.charAt(cpt);
            somme += parseInt(tmp);
        }



        if ((somme % 10) == 0)
            estValide = true; // Si la somme est un multiple de 10 alors le SIRET est valide
        else
            estValide = false;
    }
    return estValide;
}

/**
 * Mi-verifier na valide na tsia ny forme Rehetra ao @ tab
 * @param dossierId
 * @param forms
 * @param etape
 * @param etapeIndex
 * @returns {*}
 */
function isValideAllForm(forms, dossierId, etape, etapeIndex) {

    var canValide = verifierChampObligatoire(forms);

    if(etapeIndex == 1){
        // var resp = withResponsable(dossierId);
        // if(resp == false)
        if(!withResp){
            canValide = false;
        }

        if(!withTvaTaux()){
            canValide = false;
        }
    }

    if (canValide) {

        etape[etapeIndex].valide = 1;

    }
    else {
        etape[etapeIndex].valide = -1;
    }


    etape = setTabs(etape, etapeIndex);

    return etape;
}

/**
 * Manala ny champ obligatoire rehetra
 */
function removeRequired() {

    var selectRequired = $( "select[required]" );
    selectRequired.each(function() {
        $(this).removeAttr('required');
        removeRequiredText($(this));
    });

    var inputRequired = $("input[required]");
    inputRequired.each(function () {
        $(this).removeAttr('required');
        removeRequiredText($(this));
    });
}

/**
 * Manala ny champ obligatoire ho an'ny information comptable et fiscal
 */
function removeRequiredInfoComptable() {

    var selectRequired = $(".info-comptable-fiscal" );
    selectRequired.each(function() {
        $(this).removeAttr('required');
        $(this).removeAttr('aria-required');
        $(this).removeClass('error');
        removeRequiredText($(this));
    });
}


/**
 * Manala ny champ obligatoire ho an'ny information comptable et fiscal
 */
function removeRequiredInfoJuridique() {

    var selectRequired = $(".info-juridique" );
    selectRequired.each(function() {
        $(this).removeAttr('required');
        $(this).removeAttr('aria-required');
        $(this).removeClass('error');
        removeRequiredText($(this));
    });
}

function saveTvaTauxV2(dossierId) {
    if (dossierId == 0) {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
        return;
    }
    var tvaTaux = $('#js_tva_taux').val();

    var lien = Routing.generate('info_perdos_tva_taux_edit');
    $.ajax({

        data: {
            dossierId: dossierId,
            tvaTaux: tvaTaux
        },

        url: lien,
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {

            var res = parseInt(data);

            // console.log(res);
        }
    });
}

/**
 * Mi-enregistrer ny bloc Carracteristique du dossier.
 * Onglet: Information Dossier
 * @param dossierId
 * @param toutValider boolean
 */
function saveCarracteristique(dossierId, toutValider) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var formeJuridique = $('#js_forme_juridique').val();
    var regimeFiscal = $('#js_regime_fiscal').val();
    var regimeImposition = $('#js_regime_imposition').val();
    var natureActivite = $('#js_nature_activite').val();
    var formeActivite = $('#js_forme_activite').val();
    var professionLiberale = $('#js_profession_liberale').attr('data-id');
    var modeVente = $('#js_mode_vente').val();
    var tvaRegime = $('#js_tva_regime').val();
    var taxeSalaire = $('#js_taxe_salaire').val();
    var tvaMode = $('#js_tva_mode').val();

    var tvaTaux = $('#js_tva_taux').val();
    var dateTva = $('#js_date_tva').val();
    var tvaFaitGenerateur = $('#js_tva_fait_generateur').val();


    var sites  =$('#site').find('option');
    var site;

    //Monosite
    if(sites.size() == 2)
    {
        site = (sites[1]).value;
    }
    //Multisite
    else
    {
        site = $('#site').val()
    }

    var lien = Routing.generate('info_perdos_caracteristique_edit');
    $.ajax({

        data:{

            dossierId:dossierId,
            site:site,

            //Caracteristique dossier
            formeJuridique:formeJuridique,
            regimeFiscal:regimeFiscal,
            regimeImposition:regimeImposition,
            natureActivite:natureActivite,
            formeActivite:formeActivite,
            professionLiberale:professionLiberale,
            modeVente:modeVente,
            tvaRegime:tvaRegime,
            taxeSalaire:taxeSalaire,
            tvaMode:tvaMode,
            tvaTaux:tvaTaux,
            dateTva:dateTva,
            tvaFaitGenerateur:tvaFaitGenerateur
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
                show_info_perdos('SUCCES', "MODIFICATION DU 'CARACTERISTIQUE DU DOSSSIER' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DU 'CARACTERISTIQUE DU DOSSSIER' EFFECTUEE");
            }
            else
            {
                res = JSON.parse(data);

                if(res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else{
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
                }
            }

            if(toutValider == false){
                checkDossier(dossierId);
            }
        }
    });

}



function saveReglePaiement(dossierId){
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');

    }
    else{


        var fDateLe = $('#js_f_regle_paiement_date_le').val();
        var fNbreJour = $('#js_f_regle_paiement_nbre_jour').val();
        var fTypeDate = $('#js_f_regle_paiement_date').val();

        var cDateLe = $('#js_c_regle_paiement_date_le').val();
        var cNbreJour = $('#js_c_regle_paiement_nbre_jour').val();
        var cTypeDate = $('#js_c_regle_paiement_date').val();


        var lien = Routing.generate('info_perdos_regle_paiement_edit');
        $.ajax({

            data:{

                dossierId:dossierId,
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

}

/**
 * Mi-enregistrer ny bloc Convention Comptable.
 * Onglet: Methodes comptable
 */
function saveConventionComptable(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var conventionComptable = $('#js_convention_compbtale').val();


    var lien = Routing.generate('info_perdos_convention_edit');
    $.ajax({

        data:{

            dossierId:dossierId,
            conventionComptable:conventionComptable

        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {

            var res = parseInt(data);

            if (res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DU 'CONVENTION COMPTABLE' BIEN ENREGISTREE");
            }
            else if (res == 1) {
                show_info_perdos('SUCCES', "AJOUT DU 'CONVENTION COMPTABLE' EFFECTUEE");
            } else {
                res = JSON.parse(data);

                if (res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else {
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
                }
            }

            checkDossier(dossierId);

        }
    });

}

/**
 * Mi-enregistrer ny bloc Information Comptable et fiscal
 * Onglet: Informations dossier
 * @param dossierId
 * @param toutValider boolean
 */
function saveDocComptableFisc(dossierId, toutValider) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    //Information comptable et fiscale
    var comptaSurServeur = $('#js_compta_sur_serveur').val();

    var balanceN1 = setValueAEnvoyer('js_balance_n1',2);
    var grandLivre = setValueAEnvoyer('js_grand_livre',2);
    var journauxN1 = setValueAEnvoyer('js_journaux_n1',2);
    var rapprochementBanqueN1 = setValueAEnvoyer('js_rapprochement_banque_n1',2);
    var etatImmobilisation = setValueAEnvoyer('js_etat_immobilisation',2);
    var liasseN1 = setValueAEnvoyer('js_liasse_n1',2);
    var tvaDerniereCa3 = setValueAEnvoyer('js_tva_derniere_ca3',2);

    var sites  =$('#site').find('option');
    var site;

    //Monosite
    if(sites.size() == 2)
    {
        site = (sites[1]).value;
    }
    //Multisite
    else
    {
        site = $('#site').val()
    }

    var lien = Routing.generate('info_perdos_docComptableFisc_edit');
    $.ajax({

        data:{

            dossierId:dossierId,
            site:site,

            //Information comptable et fiscale
            comptaSurServeur:comptaSurServeur,
            balanceN1:balanceN1,
            grandLivre:grandLivre,
            journauxN1:journauxN1,
            rapprochementBanqueN1:rapprochementBanqueN1,
            etatImmobilisation:etatImmobilisation,
            liasseN1:liasseN1,
            tvaDerniereCa3:tvaDerniereCa3

        },

        async: false,
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DES 'INFORMATIONS COMPTABLE ET FISCALE' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DES 'INFORMATIONS COMPTABLE ET FISCALE' EFFECTUEE");
            }
            else
            {
                res = JSON.parse(data);

                if(res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else{
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE! VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
                }
            }

            if(toutValider == false){
                checkDossier(dossierId);
            }
        }
    });
}

/**
 * Mi-enrigestrer ny bloc Identification du dossier
 * Onglet: Informations dossier
 * @param dossierId
 * @param gridWidth
 * @param etape
 * @param toutValider boolean
 * @returns {*}
 */
function saveIdentification(dossierId,gridWidth,etape, toutValider) {

    var siren = $('#js_siren_siret').val().replace(/\s/g, "");

    var sites = $('#site option');
    var siteText =$("#site option:selected").text();
    var monosite = true;

    if(sites.size()>2)
    {
        monosite = false;
    }
    else {
        site = (sites[1]).value;
    }

    //Mbola tsy ni-selectionner dossier
    if(siteText == "Tous" && monosite == false && dossierId == 0)
    {
        show_info_perdos('INFORMATION', 'IL FAUT CHOISIR UN SITE AVANT DE CREER UN DOSSIER', 'warning');
        return false;
    }



    if($('#js_date_debut_activite').val() != "" && $('#js_date_cloture').val() != "") {

        try {
            var debs = $('#js_date_debut_activite').val().split('/');
            var timeDeb = new Date(
                parseInt(debs[2]),
                parseInt(debs[1]) - 1,
                parseInt(debs[0])
            ).getTime();


            var clots = $('#js_date_cloture').val().split('/');
            var timeCloture = new Date(
                parseInt(clots[2]),
                parseInt(clots[1]) - 1,
                parseInt(clots[0])
            ).getTime();


            if (timeDeb > timeCloture) {
                $('#js_date_cloture').val('');
                show_info("Attention", "Date cloture doit être superieur à la date debut activité", "warning");


                $('#js_form_info_identification_dossier').valid();
            }
        }
        catch (error){
            console.log(error);
        }
    }

    if (siren != ''){
        var estSirenValide = verifierSiren(siren, dossierId);

        if (estSirenValide == false) {
            show_info_perdos('Information', "Ce Siren existe déjà", 'warning');
            $('#js_siren_siret').val('');
            return false;
        }
    }


    var retDoss = [];

    //Identification dossier
    var nomDossier = $('#js_nom_dossier').val();
    var sirenSiret = $('#js_siren_siret').val().replace(/\s/g, "");
    var raisonSocial = $('#js_raison_social').val();
    var dateDebutActivite = $('#js_date_debut_activite').val();
    var formeJuridique = $('#js_forme_juridique').val();
    var activiteComCat3 = $('#js_code_ape').attr('data-id');
    var dateCloture = $('#js_date_cloture').val();
    var cloture = $('#js_mois_cloture').val();

    var enseigne = $('#js_enseigne').val();
    var trancheEffectif = $('#js_tranche_effectif').val();
    var numRue = $('#js_num_rue').val();
    var codePostal = $('#js_code_postal').val();
    var pays = $('#js_pays').val();
    var ville = $('#js_ville').val();


    //Mandataire
    var mandataire = $('#js_type_mandataire').val();
    var nomMandataire = $('#js_nom_mandataire').val();

    var cegid = $('#js_cegid').val();

    //
    // var sites  =$('#site').find('option');
    var site;

    //Monosite
    if(sites.size() === 2)
    {
        site = (sites[1]).value;
    }
    //Multisite
    else
    {
        site = $('#site').val()
    }

    var lien = Routing.generate('info_perdos_identification_edit');

    $.ajax({

        data:{

            dossierId:dossierId,
            site:site,

            //Identification dossier
            nomDossier:nomDossier,
            raisonSocial:raisonSocial,
            sirenSiret:sirenSiret,
            dateDebutActivite:dateDebutActivite,
            dateCloture:dateCloture,
            formeJuridique:formeJuridique,
            activiteComCat3:activiteComCat3,
            mandataire:mandataire,
            nomMandataire:nomMandataire,
            cloture:cloture,

            enseigne:enseigne,
            trancheEffectif:trancheEffectif,
            numRue:numRue,
            codePostal:codePostal,
            pays:pays,
            ville:ville,
            cegid:cegid

        },
        async: false,
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {


            var res = JSON.parse(data);

            if (res.estInsere == 2) {

                show_info_perdos('SUCCES', "MODIFICATION L'IDENTIFCATION DOSSIER BIEN ENREGISTREE");

                dossierId = res.idDossier;

                retDoss = [dossierId, res.id, res.estInsere];

                withResp = withResponsable(dossierId);

                setTabProgressBtnValidation(etape, false, dossierId);
            }

            else if (res.estInsere == 1) {

                show_info_perdos('SUCCES', "AJOUT DE L'IDENTIFCATION DOSSIER BIEN ENREGISTREE");

                charger_dossier_info_perdos();

                dossierId = res.idDossier;

                retDoss = [dossierId, res.id, res.estInsere];

                //Mila recharger-na ilay jqGrid rehefa miova ny id an'ny dossier


                $('#js_infoCarac_responsableDossier_liste').jqGrid('GridUnload');
                $('#js_infoCarac_mandataire_liste').jqGrid('GridUnload');
                $('#js_infoCarac_banque_liste').jqGrid('GridUnload');
                $('#js_infoCarac_vehicule_liste').jqGrid('GridUnload');
                showGrids(dossierId, gridWidth,etape);

                withResp = withResponsable(dossierId);

                setTabProgressBtnValidation(etape, false,dossierId);

            }
            else if (res.estInsere == 0) {
                show_info_perdos('ATTENTION', 'Le champ' + res.message + 'est obligatoire', 'warning');
            }

            if(toutValider == false){
                checkDossier(dossierId);
            }
        }

    });

    // return dossierId;

    return retDoss;
}

/**
 * Mi-enregistrer ny bloc Documents juridiques
 * Onglet: Informations dossier
 * @param dossierId
 * @param toutValider boolean
 */
function saveDocJuridique(dossierId, toutValider) {

    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var statut = setValueAEnvoyer('js_statut',3);
    var kbis = setValueAEnvoyer('js_kbis',3);
    var baux = setValueAEnvoyer('js_baux',3);
    var assurance = setValueAEnvoyer('js_assurance',3);
    var autre = setValueAEnvoyer('js_autre',3);
    var emprunt = setValueAEnvoyer('js_emprunt',3);
    var leasing = setValueAEnvoyer('js_leasing',3);


    var sites  =$('#site').find('option');
    var site;


    //Monosite
    if(sites.size() == 2)
    {
        site = (sites[1]).value;
    }
    //Multisite
    else
    {
        site = $('#site').val()
    }
    var lien = Routing.generate('info_perdos_docJuridique_edit');
    $.ajax({

        data:{

            dossierId:dossierId,
            site:site,

            statut:statut,
            kbis:kbis,
            baux:baux,
            assurance:assurance,
            autre:autre,
            emprunt:emprunt,
            leasing:leasing
        },
        async: false,
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DES 'DOCUMENTS JURIDIQUES' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DES 'DOCUMENTS JURIDIQUES' EFFECTUEE");
            }
            else
            {
                res = JSON.parse(data);

                if(res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else{
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE! VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
                }


            }

            if(toutValider == false){
                checkDossier(dossierId);
            }

        }
    });
}

/**
 * Mi-enregister ny bloc Methodes comptables
 * Onglet: Méhodes comptables
 * @param dossierId
 */
function saveMethodeComptable(dossierId){
    //var dossierId = $('#js_id_dossier').val();

    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var vente = $('#js_vente').val();
    var achat = $('#js_achat').val();
    var banque = $('#js_banque').val();
    var nbBanque = $('#js_nombre_banque').val();
    var saisieOdPaie = $('#js_saisie_od_paye').val();
    var analytique = $('#js_analytique').val();
    // var venteComptoir = $('#js_vente_comptoir').val();
    // var venteFacture = $('#js_vente_facture').val();
    var rapprochementBanque = $('#js_rapprochement_banque_doss').val();
    var suiviChequeEmis = $('#js_suivi_cheque_emis_doss').val();

    var lien = Routing.generate('info_perdos_methode_comptable_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            vente:vente,
            achat:achat,
            banque:banque,
            nbBanque:nbBanque,
            saisieOdPaie:saisieOdPaie,
            analytique:analytique,
            // venteComptoir:venteComptoir,
            // venteFacture:venteFacture
            rapprochementBanque:rapprochementBanque,
            suiviChequeEmis:suiviChequeEmis
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DU 'METHODE COMPTABLE' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT  DU METHODE 'COMPTABLE' EFFECTUEE");
            }
            else
            {
                res = JSON.parse(data);

                if(res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else{
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE! VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
                }
            }

            checkDossier(dossierId);


        }
    });

}

/**
 * Mi-enregistrer ny bloc Periodicite
 * Onglet: Méthodes comptables
 * @param dossierId
 */
function savePeriodicite(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var tenueComptabilite = $('#js_tenue_comptablilite').val();

    var lien = Routing.generate('info_perdos_periodicite_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            tenueComptabilite:tenueComptabilite
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DE LA 'PERIODICITE' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DE LA 'PERIODICITE' EFFECTUEE");
            }

            else
            {
                res = JSON.parse(data);

                if(res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else{
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE! VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
                }


            }


            checkDossier(dossierId);


        }
    });

}

/**
 *Mi-enregistrer ny bloc Coptable
 * Onglet: Prestation demandées
 * @param dossierId
 */
function savePrestDemande(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var typePrestation = $('#js_prestation_comptable_demande').val();
    var autrePrestation = $('#js_prestation_autre').val();

    var lien = Routing.generate('info_perdos_prestation_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            typePrestation:typePrestation,
            autrePrestation:autrePrestation
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {

            var res = parseInt(data);

            if (res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DES 'PRESTATIONS DEMANDEES' BIEN ENREGISTREE");
            }

            else {
                res = JSON.parse(data);

                if (res.estInsere == 0) {
                    show_info_perdos('ATTENTION', 'Le champ ' + res.message + ' est obligatoire', 'warning');
                }
                else {
                    show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE! VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
                }

            }

            checkDossier(dossierId);
        }
    });

}

/**
 * Mi-enregistrer ny bloc Fiscales
 * Onglet: Prestation demandées
 * @param dossierId
 */
function savePrestFiscal(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var tva = $('#js_tva').val();
    var accompteIsSolde = $('#js_accomptes_is_solde').val();
    var liasseFiscale = $('#js_liasse_fiscale').val();
    var cice = $('#js_cice').val();
    var cvae = $('#js_cvae').val();
    var tvts = $('#js_tvts').val();
    var das2 = $('#js_das2').val();
    var cfe = $('#js_cfe').val();
    var dividende = $('#js_dividendes').val();
    var teledeclarationLiasse = $('#js_teledeclaration_liasse').val();
    var teledeclarationAutre = $('#js_teledeclaration_autres').val();
    var autres = $('#js_prestation_fiscal_autre').val();
    var deb = $('#js_deb').val();
    var dej = $('#js_dej').val();

    var lien = Routing.generate('info_perdos_prest_fiscal_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            tva:tva,
            accompteIsSolde:accompteIsSolde,
            liasseFiscale:liasseFiscale,
            cice:cice,
            cvae:cvae,
            tvts:tvts,
            das2:das2,
            cfe:cfe,
            dividende:dividende,
            teledeclarationLiasse: teledeclarationLiasse,
            teledeclarationAutre: teledeclarationAutre,
            autres: autres,
            deb: deb,
            dej: dej

        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DES 'PRESTATIONS FISCALES' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DES 'PRESTATIONS FISCALES' EFFECTUEE");
            }
            else if (res == -1)
            {
                show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
            }

            checkDossier(dossierId);
        }
    });

}

/**
 * Mi-enregistrer ny bloc Gestion
 * Onglet: Prestation demandées
 * @param dossierId
 */
function savePrestGestion(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var situation = $('#js_situation').val();
    var indicateur = $('#js_indicateur').val();
    var budget = $('#js_budget').val();
    var tableauBord = $('#js_type_tableau_bord').val();

    var lien = Routing.generate('info_perdos_prest_gestion_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            situation:situation,
            indicateur:indicateur,
            budget:budget,
            tableauBord:tableauBord
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DE LA 'PRESTATION DE GESTION' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DE LA 'PRESTATION DE GESTION' EFFECTUEE");
            }
            else if (res == -1)
            {
                show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
            }
        }
    });

}

/**
 * Mi-enregistrer ny bloc Juridique
 * Onglet: Prestation demandées
 * @param dossierId
 */
function savePrestJuridique(dossierId) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    var rapportGestion = $('#js_rapport_gestion').val();
    var assembleeOrdinaire = $('#js_assemblee_ordinaire').val();

    var lien = Routing.generate('info_perdos_prest_juridique_edit');
    $.ajax({

        data:{
            dossierId:dossierId,
            rapportGestion:rapportGestion,
            assembleeOrdinaire:assembleeOrdinaire
        },

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if(res == 2) {
                show_info_perdos('SUCCES', "MODIFICATION DE LA 'PRESTATION JURIDIQUE' BIEN ENREGISTREE");
            }
            else if (res ==1)
            {
                show_info_perdos('SUCCES', "AJOUT DE LA 'PRESTATION JURIDIQUE' EFFECTUEE");
            }
            else if (res == -1)
            {
                show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
            }
        }
    });

}

/**
 *
 * @param dossierId
 * @param typeRemarque
 */
function saveRemarqueDossier(dossierId,typeRemarque) {
    if (dossierId == 0) {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
        return;
    }

    var remarqueInformation = $('#js_remarque_information_dossier').val();
    var remarqueMethodeComptable = $('#js_remarque_methode_comptable').val();
    var remarquePrestationDemande = $('#js_remarque_prestation_demande').val();
    var remarquePrestationComptable = $('#js_remarque_prestation_comptable').val();
    var remarquePieceAEnvoyer = $('#js_remarque_piece_a_envoyer').val();

    var lien = Routing.generate('info_perdos_remarque_dossier_edit');
    $.ajax({
        url: lien,
        data: {
            dossierId: dossierId,
            typeRemarque: typeRemarque,
            remarqueInformation: remarqueInformation,
            remarqueMethodeComptable: remarqueMethodeComptable,
            remarquePrestationDemande: remarquePrestationDemande,
            remarquePrestationComptable: remarquePrestationComptable,
            remarquePieceAEnvoyer: remarquePieceAEnvoyer
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
 *
 * @param dossierId
 */
function saveAgaCga(dossierId) {

    if (dossierId == 0) {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
        return;
    }

    var adherant = $('#js_aga_cga_adherant').val();
    var nom = $('#js_aga_cga_nom').val();
    var siren = $('#js_aga_cga_siren').val();
    var numeroAdhesion = $('#js_aga_cga_num_adhesion').val();
    var dateAdhesion = $('#js_aga_cga_date_adhesion').val();
    var numRue = $('#js_aga_cga_num_rue').val();
    var codePostal = $('#js_aga_cga_code_postal').val();
    var ville = $('#js_aga_cga_ville').val();
    var pays = $('#js_aga_cga_pays').val();

    var lien = Routing.generate('info_perdos_aga_cga_edit');
    $.ajax({

        data: {
            dossierId: dossierId,
            adherant: adherant,
            nom: nom,
            siren: siren,
            numeroAdhesion: numeroAdhesion,
            dateAdhesion: dateAdhesion,
            numRue: numRue,
            codePostal: codePostal,
            ville: ville,
            pays: pays

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
                show_info_perdos('SUCCES', "MODIFICATION DE L'AGA/CGA BIEN ENREGISTREE");
            }
            else if (res == 1) {
                show_info_perdos('SUCCES', "AJOUT DE L'AGA/CGA EFFECTUEE");
            }
            else if (res == -1) {
                show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER", 'warning');
            }

            checkDossier(dossierId);
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


function setDateLe(input) {
    if (input.is(":checked")) {
        if(input.attr('id') == 'js_f_regle_paiement_date_le_active') {
            $('#js_f_regle_paiement_date_le').removeAttr('disabled');
        }
        else{
            $('#js_c_regle_paiement_date_le').removeAttr('disabled');
        }
    }
    else {

        if(input.attr('id') == 'js_f_regle_paiement_date_le_active') {
            $('#js_f_regle_paiement_date_le').prop('disabled', true);
            $('#js_f_regle_paiement_date_le').val("");
        }
        else{
            $('#js_c_regle_paiement_date_le').prop('disabled', true);
            $('#js_c_regle_paiement_date_le').val("");
        }
    }
}

function setDateLeClient(input) {
    if (input.is(":checked")) {
        if(input.attr('id') == 'js_cf_regle_paiement_date_le_active') {
            $('#js_cf_regle_paiement_date_le').removeAttr('disabled');

            if(!withReglePaiement && dossier_id != 0){
                $('#js_f_regle_paiement_date_le').removeAttr('disabled');
            }
        }
        else{
            $('#js_cc_regle_paiement_date_le').removeAttr('disabled');

            if(!withReglePaiement && dossier_id != 0){
                $('#js_c_regle_paiement_date_le').removeAttr('disabled');
            }
        }
    }
    else {

        if(input.attr('id') == 'js_cf_regle_paiement_date_le_active') {
            $('#js_cf_regle_paiement_date_le').prop('disabled', true);
            $('#js_cf_regle_paiement_date_le').val("");

            if(!withReglePaiement && dossier_id != 0){
                $('#js_f_regle_paiement_date_le').prop('disabled', true);
                $('#js_f_regle_paiement_date_le').val("");
            }

        }
        else{
            $('#js_cc_regle_paiement_date_le').prop('disabled', true);
            $('#js_cc_regle_paiement_date_le').val("");

            if(!withReglePaiement && dossier_id != 0) {
                $('#js_c_regle_paiement_date_le').prop('disabled', true);
                $('#js_c_regle_paiement_date_le').val("");
            }
        }
    }
}

function removeChampObligatoireLabel(input){
    var id = input.attr('id')+'-error';

    $('#'+id).remove();
}

function setAgaCga(regimeFiscal, liasseFiscal, isFirstLoad){

    isFirstLoad = isFirstLoad || false;


    var inputs = $('.prest_aga_cga');

    if(liasseFiscal == 1  && (regimeFiscal == "CODE_BNC" || regimeFiscal == "CODE_BIC_IR" || regimeFiscal == "CODE_BA")){

        if(!isFirstLoad) {


            swal({
                title: 'AGA/CGA',
                text: "Le dossier est-il adhérant du AGA/CGA?",
                type: 'question',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Non'
            }).then(function () {

                    $('#js_aga_cga_adherant').val('1');

                    inputs.each(function () {
                        $(this).removeAttr('disabled');

                        if ($(this).attr('id') != 'js_aga_cga_num_rue' && $(this).attr('id') != 'js_aga_cga_code_postal' &&
                            $(this).attr('id') != 'js_aga_cga_ville' && $(this).attr('id') != 'js_aga_cga_pays') {
                            setRequired($(this));
                            addRequiredText($(this));
                        }
                        setInputColor($(this));
                    });
                },
                function (dismiss) {
                    if (dismiss === 'cancel') {

                    } else {
                        throw dismiss;
                    }
                }
            );
        }
        else{

            // $('#js_aga_cga_adherant').val($('#js_aga_cga_adherant').val()).change();
            setAgaCgaChange($('#js_aga_cga_adherant').val(), isFirstLoad);

        }
    }

    else{
        inputs.each(function () {
            $(this).prop('disabled', true);
            $(this).val("");
            $(this).prop('required', false);
            removeRequiredText($(this));
            setInputColor($(this));
        });
    }
}

function setAgaCgaChange(adherant, isFirstLoad){
    var inputs = $('#js_form_prest_aga_cga input');

    if(adherant == 0){
        inputs.each(function () {
            $(this).prop('disabled', true);
            $(this).prop('required', false);

            removeRequiredText($(this));
            removeChampObligatoireLabel($(this));

            if(!isFirstLoad){
                $(this).val('');
            }

            setInputColor($(this));
        });
    }
    else {

        inputs.each(function () {

            $(this).prop('disabled', false);
            if ($(this).attr('id') != 'js_aga_cga_num_rue' && $(this).attr('id') != 'js_aga_cga_code_postal' &&
                $(this).attr('id') != 'js_aga_cga_ville' && $(this).attr('id') != 'js_aga_cga_pays') {

                $(this).prop('required', true);
                addRequiredText($(this));
            }
            setInputColor($(this));

        });

    }
}

/**
 *
 * @param liasseFiscal
 */
function setAccomptesIsTeledeclarationLiasse(liasseFiscal){
    var accopteIs = $('#js_accomptes_is_solde');
    var teleDeclarationLiasse = $('#js_teledeclaration_liasse');

    var regimeFiscal = $('option:selected', $('#js_regime_fiscal')).attr('data-code');

    //wba
    if(liasseFiscal != 1){
        accopteIs.prop('disabled', true);
        accopteIs.val("");
        accopteIs.prop('required', false);
        removeRequiredText(accopteIs);
        setInputColor(accopteIs);

        teleDeclarationLiasse.prop('disabled', true);
        teleDeclarationLiasse.val("");
        teleDeclarationLiasse.prop('required', false);
        removeRequiredText(teleDeclarationLiasse);
        setInputColor(teleDeclarationLiasse);
    }
    else{
        accopteIs.removeAttr('disabled');
        setRequired(accopteIs);
        addRequiredText(accopteIs);
        setInputColor(accopteIs);

        teleDeclarationLiasse.removeAttr('disabled');
        setRequired(teleDeclarationLiasse);
        addRequiredText(teleDeclarationLiasse);
        setInputColor(teleDeclarationLiasse);
    }

    if(regimeFiscal === 'CODE_BNC' || regimeFiscal === 'CODE_LMP_LMNP'){
        accopteIs.prop('disabled', true);
        accopteIs.val("");
        accopteIs.prop('required', false);
        removeRequiredText(accopteIs);
        setInputColor(accopteIs);

    }
}


/**
 * mi-desactiver/activer ny .info-comptable arakaraky ny valeur-an'ny comptaServeur
 * desactiver raha accès autorisé, activé raha accès non autorisé
 * @param comptaServeur
 */
function setInfoComptableFiscal(comptaServeur) {

    var selects = $('.info-comptable-fiscal');

    var juridiqueSelects = $('.info_forme_juridique');

    if (parseInt(comptaServeur) === 1 || isNaN(comptaServeur) || parseInt(comptaServeur) === 2 ||
        parseInt(comptaServeur) === 3 || parseInt(comptaServeur) === 5) {
        selects.each(function () {
            removeRequiredInfoComptable();
            $(this).attr('disabled', "");
            $(this).val("");

            setInputColor($(this));

            // removeChampObligatoireLabel($(this));

        });
    }
    else {
        selects.each(function () {
            // addRequiredInfoComptable();
            $(this).removeAttr('disabled');

            setRequired($(this));
            addRequiredText($(this));
            setInputColor($(this));
        });
        disableEstEnvoye();
    }


    //Dossier deja traité
    if (parseInt(comptaServeur) == 3) {
        juridiqueSelects.each(function () {

            removeRequiredInfoJuridique();
            $(this).prop('disabled', true);
            $(this).val("");

            setInputColor($(this));

        });
    }
    else {
        juridiqueSelects.each(function () {
            $(this).removeAttr('disabled');


            var id = $(this).attr('id');
            var nonObli = ['js_baux', 'js_assurance', 'js_autre', 'js_emprunt', 'js_leasing'];
            if(!nonObli.in_array(id)) {

                setRequired($(this));
                addRequiredText($(this));
                setInputColor($(this));
            }

        });
        disableEstEnvoye();
    }


}

/**
 * Mi-activer na mi-desactiver ny inputfile any @ onglet Pièce à envoyer
 * @param cmb
 * @param inputEnvoi
 */
function setEnvoi(cmb,inputEnvoi) {

    //Vide Non Non-Applicable
    if(cmb.val() == '' || cmb.val() == 0 || cmb.val() == 2)
    {
        inputEnvoi.attr('disabled', 'disabled');
        inputEnvoi.parent().attr('disabled','disabled');
        inputEnvoi.closest('.input-group').find('.file-caption').addClass('file-caption-disabled');
    }
    else
    {
        inputEnvoi.removeAttr('disabled');
        inputEnvoi.parent().removeAttr('disabled');
        inputEnvoi.closest('.input-group').find('.file-caption').removeClass('file-caption-disabled');
    }

}

/**
 * Mi-initialiser (asina icone check) ny inputs rehetra izay efa envoyé
 */
function setFileInputs(){
    checkEnvoi($('#js_plan_comptable'),$('#js_envoi_plan_comptable'));
    checkEnvoi($('#js_archive_comptable'),$('#js_envoi_archive_comptable'));
    checkEnvoi($('#js_grand_livre'),$('#js_envoi_grand_livre'));
    checkEnvoi($('#js_rapprochement_banque_n1'),$('#js_envoi_dernier_rapprochement_banque'));
    checkEnvoi($('#js_etat_immobilisation'),$('#js_envoi_etat_immobilisation'));
    checkEnvoi($('#js_liasse_n1'),$('#js_envoi_liasse_fisacle_n1'));
    checkEnvoi($('#js_tva_derniere_ca3'),$('#js_envoi_tva_derniere_ca3'));

    checkEnvoi($('#js_statut'),$('#js_envoi_statut'));
    checkEnvoi($('#js_kbis'),$('#js_envoi_kbis'));
    checkEnvoi($('#js_baux'),$('#js_envoi_baux'));
    checkEnvoi($('#js_assurance'),$('#js_envoi_assurance'));
    checkEnvoi($('#js_autre'),$('#js_envoi_autre'));
    checkEnvoi($('#js_emprunt'),$('#js_envoi_emprunt'));
    checkEnvoi($('#js_leasing'),$('#js_envoi_leasing'));

}

function setFormeActiviteByRegimeFiscal(regimeFiscal, dossierId){

    $.ajax({
        url: Routing.generate('info_perdos_formeAct', {json: 0}),
        type: "POST",
        async: false,
        data: {
            dossierId: dossierId,
            codeFiscal:regimeFiscal
        },
        success: function (data) {
            $('#js_forme_activite').html(data);
            setInputColor($('#js_forme_activite'));

            var formeActivite = $('option:selected', $('#js_forme_activite')).attr('data-code');

            setProfessionLiberale(formeActivite);
        }
    });
}

/**
 *
 * @param regimeFiscal
 */
function setTvaDateByRegimeFiscal(regimeFiscal) {
    if(regimeFiscal === 'CODE_BA')
    {


        if($("#js_date_tva option[value='55']").length == 0) {

            $('#js_date_tva').append($('<option>', {
                value: 55,
                text: '5eme jour du 5eme mois'
            }));
        }


        // $('#js_forme_activite option').each(function(){
        //     if ($(this).attr("data-code") == "CODE_AGRICOLE") {
        //         $('#js_forme_activite').val($(this).val());
        //     }
        // });

        // $('#js_nature_activite option').each(function(){
        //     if ($(this).attr("data-code") == "CODE_AGRICOLE") {
        //         $('#js_nature_activite').val($(this).val());
        //     }
        // });

        var tvaRegime = $('#js_tva_regime option:selected').attr('data-code');

        if(tvaRegime !== 'CODE_NON_SOUMIS' && tvaRegime !== 'CODE_FRANCHISE') {
            $('#js_date_tva').val(55);
        }


        // setInputColor($('#js_forme_activite'));
        // setInputColor($('#js_nature_activite'));
        setInputColor($('#js_date_tva'));

        var comptaSurServeur = $('#js_compta_sur_serveur').val();

        //Raha dossier dejà traité dia tsy atao
        if(comptaSurServeur != 3) {

            $('.info-juridique').each(function () {
                $(this).val(0);

                setInputColor($(this))
            });
        }

        setEnvoi($('#js_statut'),$('#js_envoi_statut'));
        setEnvoi($('#js_kbis'),$('#js_envoi_kbis'));
        setEnvoi($('#js_baux'),$('#js_envoi_baux'));
        setEnvoi($('#js_assurance'),$('#js_envoi_assurance'));
        setEnvoi($('#js_autre'),$('#js_envoi_autre'));
        setEnvoi($('#js_emprunt'),$('#js_envoi_emprunt'));
        setEnvoi($('#js_leasing'),$('#js_envoi_leasing'));

    }
    else{
        //Esorina ny tvaDate
        $("#js_date_tva option[value='55']").remove();
    }
}


/**
 * Mi-desactiver/activer ny Kbis
 * Desactivé si regime = association
 * @param formeJuridique
 */
function setKbis(formeJuridique) {
    var kbis = $('#js_kbis');
    //Entreprise individuelle
    if (formeJuridique == "CODE_ENTREPRISE_INDIVIDUELLE" || formeJuridique == "CODE_INDIVIDUELLE") {
        kbis.attr('disabled', "");
        kbis.removeAttr('required');
        kbis.val("");
        removeRequiredText(kbis);
    }
    else {
        var comptaSurServeur = $('#js_compta_sur_serveur').val();

        //dossier dejà traité
        if(comptaSurServeur != 3) {
            kbis.removeAttr('disabled');
            setRequired(kbis);
        }
    }

    setInputColor(kbis);
}

function setModeVenteByRegimeFiscal(regimeFiscal, isFirstLoad){
    if(!isFirstLoad) {

        if (regimeFiscal === 'CODE_LMP_LMNP') {
            $('#js_mode_vente option').each(function () {
                if ($(this).attr('data-code') === 'CODE_FACTURE') {
                    $('#js_mode_vente').val($(this).val()).change();
                }
            });
        }

    }
}

function setNatureActiviteByRegimeFiscal(regimeFiscal, dossierId){
    $.ajax({
        url: Routing.generate('info_perdos_natureActivite', {json: 0}),
        type: "POST",
        async: false,
        data: {
            dossierId: dossierId,
            codeFiscal:regimeFiscal
        },
        success: function (data) {
            $('#js_nature_activite').html(data);
            setTypeActiviteByRegimeFiscal(regimeFiscal);
        }
    });
}

function setRegimeImpositionByRegimeFiscal(regimeFiscal, dossierId){
    $.ajax({
        url: Routing.generate('info_perdos_regimeImposition', {json: 0}),
        type: "POST",
        async: false,
        data: {
            dossierId: dossierId,
            codeFiscal:regimeFiscal
        },
        success: function (data) {
            $('#js_regime_imposition').html(data);


            if(regimeFiscal === 'CODE_NS'){
                $('#js_regime_imposition').prop('disabled', true);
                $('#js_regime_imposition').removeAttr('required');
                $('#js_regime_imposition').val('');
                removeRequiredText($('#js_regime_imposition'));
            }
            else{
                $('#js_regime_imposition').prop('disabled', false);
                setRequired($('#js_regime_imposition'));
            }

            setInputColor($('#js_regime_imposition'));
        }
    });
}

function setPrestationTvaByRegimeTva(regimeTva){
    if(regimeTva === 'CODE_NON_SOUMIS' || regimeTva === 'CODE_FRANCHISE'){
        $('#js_tva').prop('disabled', true);
        $('#js_tva').removeAttr('required');
        $('#js_tva').val('');
        removeRequiredText($('#js_tva'));
    }
    else{
        $('#js_tva').prop('disabled', false);
        setRequired($('#js_tva'));
    }
    setInputColor($('#js_tva'));
}

/**
 * Mi-desactier/activer ny Liasse Fiscal & Cice
 * @param prestationDemande
 * @param isFirstLoad
 */
function setPrestationFiscalByPrestationDemande(prestationDemande, isFirstLoad){

    isFirstLoad = isFirstLoad || false;

    var selects = $('.prest_fiscal');


    if(prestationDemande == "CODE_TENUE_COURANTE") {
        selects.each(function () {

            if($(this).attr('id') !== 'js_tva'){
                $(this).prop('disabled', true);
                $(this).removeAttr('required');
                removeRequiredText($(this));
                if($(this).attr('id') === 'js_dej' ||$(this).attr('id') === 'js_deb'){
                    $(this).val("0");
                }
                else {
                    $(this).val("");
                }
            }

            setInputColor($(this));

        })
    }
    else {

        var regimeFiscal = $('option:selected', $('#js_regime_fiscal')).attr('data-code');
        selects.each(function () {
            $(this).removeAttr('disabled');

            if ($(this).attr('id') != 'js_prestation_fiscal_autre') {
                setRequired($(this));
            }
            setInputColor($(this));

        });

        if (regimeFiscal == "CODE_BA") {


            var liasseFiscal = $('#js_liasse_fiscale').val();

            setAgaCga(regimeFiscal, liasseFiscal, isFirstLoad);

            setTvaDateByRegimeFiscal(regimeFiscal);
            setFormeActiviteByRegimeFiscal(regimeFiscal, dossier_id);
            setRegimeImpositionByRegimeFiscal(regimeFiscal, dossier_id);
            setNatureActiviteByRegimeFiscal(regimeFiscal, dossier_id);

            setTVaRegimeByRegimeFiscal(regimeFiscal, isFirstLoad);

            // $('#js_regime_fiscal').change();
        }

        if (regimeFiscal === 'CODE_BA' ||
            regimeFiscal === 'CODE_BIC_IR' ||
            regimeFiscal === 'CODE_BNC' ||
            regimeFiscal === 'CODE_LMP_LMNP') {
            setPrestationFiscalByRegimeFiscal(regimeFiscal, isFirstLoad);
        }

        var regimeTva = $('option:selected', $('#js_tva_regime')).attr('data-code');
        setPrestationTvaByRegimeTva(regimeTva);
    }


}
/**
 *
 * @param regimeFiscal
 * @param isFirstLoad
 */
function setPrestationFiscalByRegimeFiscal(regimeFiscal, isFirstLoad) {

    isFirstLoad = isFirstLoad || false;

    var accompteIs = $('#js_accomptes_is_solde');
    var dividendes = $('#js_dividendes');
    var selects = $('#js_form_prest_fiscal select');

    var prestation = $('option:selected', $('#js_prestation_comptable_demande')).attr('data-code');

    if(prestation !== 'CODE_TENUE_COURANTE') {

        selects.each(function () {
            $(this).removeAttr('disabled');
            setRequired($(this));
            $(this).removeAttr('disabled');
            setRequired($(this));
            setInputColor($(this));
        });


        switch (regimeFiscal) {
            case 'CODE_BIC_IR':
                if (!isFirstLoad) {
                    accompteIs.val("");
                    dividendes.val("");
                }

                accompteIs.prop('disabled', true);
                accompteIs.removeAttr('required');

                removeRequiredText(accompteIs);

                dividendes.prop('disabled', true);
                dividendes.removeAttr('required');

                removeRequiredText(dividendes);

                setInputColor(accompteIs);
                setInputColor(dividendes);


                break;

            case 'CODE_BA':
                selects.each(function () {

                    if ($(this).attr('id') !== 'js_tva' && $(this).attr('id') !== 'js_liasse_fiscale' &&
                        $(this).attr('id') !== 'js_accomptes_is_solde' &&
                        $(this).attr('id') !== 'js_teledeclaration_liasse'
                    ) {
                        $(this).prop('disabled', true);
                        $(this).removeAttr('required');

                        if (!isFirstLoad) {
                            $(this).val("");
                        }

                        removeRequiredText($(this));

                        setInputColor($(this));
                    }
                });

                break;

            case 'CODE_BNC':
            case 'CODE_LMP_LMNP':
                selects.each(function () {
                    if ($(this).attr('id') === 'js_accomptes_is_solde' || $(this).attr('id') === 'js_dividendes') {
                        $(this).prop('disabled', true);
                        $(this).removeAttr('required');

                        if (!isFirstLoad) {
                            $(this).val('');
                        }

                        removeRequiredText($(this));
                        setInputColor($(this));
                    }
                });
                break;

            case 'CODE_NS':
                selects.each(function(){
                   if(
                       $(this).attr('id') === 'js_liasse_fiscale' || $(this).attr('id') === 'js_cice' ||
                       $(this).attr('id') === 'js_cvae' || $(this).attr('id') === 'js_tvts' ||
                       $(this).attr('id') === 'js_cfe' || $(this).attr('id') === 'js_dividendes' ||
                       $(this).attr('id') === 'js_deb' || $(this).attr('id') === 'js_dej'
                   ){
                       $(this).prop('disabled', true);
                       $(this).removeAttr('required');

                       if(!isFirstLoad){
                           if($(this).attr('id') === 'js_dej' || $(this).attr('id') === 'js_deb'){
                               $(this).val('0');
                           }
                           else{
                               $(this).val('');
                           }
                       }

                       removeRequiredText($(this));
                       setInputColor($(this));

                   }
                });
                break;

            default:
                break;
        }
    }
}

function setTypeActiviteByRegimeFiscal(regimeFiscal){


    var natureActivite = $('#js_nature_activite');


    if(regimeFiscal === 'CODE_BNC')
    {
        natureActivite.attr('disabled',"");
        natureActivite.removeAttr('required');
        natureActivite.val("");
        removeRequiredText(natureActivite);
    }
    else
    {
        natureActivite.removeAttr('disabled');
        setRequired(natureActivite);
    }

    setInputColor($('#js_nature_activite'));
}

function setTypeMandataire(formeJuridique){

    var typeMandataire = $("#js_type_mandataire");

    typeMandataire.removeAttr('disabled');
    setRequired(typeMandataire);


    if(formeJuridique === "CODE_SA" || formeJuridique === "CODE_SAS" || formeJuridique === "CODE_SASU" ||
    formeJuridique === "CODE_ASSOCIATION" || formeJuridique === "CODE_COOPERATIVE" || formeJuridique === "CODE_ETABLISSEMENT_PUBLIC"){

        $("#js_type_mandataire option").each(function(){
            if ($(this).attr("data-code") === "CODE_PRESIDENT") {
                $('#js_type_mandataire').val($(this).val());
                return false;
            }
        });
    }
    else if(formeJuridique === "CODE_EARL" || formeJuridique === "CODE_EURL" || formeJuridique === "CODE_GEIE" ||
        formeJuridique === "CODE_GFA" || formeJuridique === "CODE_GFR" || formeJuridique === "CODE_GIE"){
        $("#js_type_mandataire option").each(function(){
            if ($(this).attr("data-code") === "CODE_GERANT") {
                $('#js_type_mandataire').val($(this).val());
                return false;
            }
        });
    }
    else if(formeJuridique === "CODE_AUTO_ENTREPRISE" || formeJuridique === "CODE_ENTREPRISE_INDIVIDUELLE" ||
        formeJuridique === "CODE_GFA" || formeJuridique === "CODE_GFR" || formeJuridique === "CODE_GIE"){
        $("#js_type_mandataire option").each(function(){
            if ($(this).attr("data-code") === "CODE_CHEF_ENTREPRISE") {
                $('#js_type_mandataire').val($(this).val());
                return false;
            }
        });
    }
    else if(formeJuridique === "CODE_AUTRE") {
        $("#js_type_mandataire option").each(function () {
            if ($(this).attr("data-code") === "CODE_AUTRE") {
                $('#js_type_mandataire').val($(this).val());
                return false;
            }
        });
    }
    else if(formeJuridique === "CODE_INDIVISION"){
            typeMandataire.attr("disabled", "");
            typeMandataire.removeAttr('required');
            typeMandataire.val("");
            removeRequiredText(typeMandataire);
    }



    // else if(formeJuridique == "CODE_ASSOCIATION" || formeJuridique == "CODE_INDIVIDUELLE" || formeJuridique == "CODE_ENTREPRISE_INDIVIDUELLE"
    //     || formeJuridique == "CODE_INDIVISION"){
    //
    //
    //     typeMandataire.attr("disabled", "");
    //     typeMandataire.removeAttr('required');
    //     typeMandataire.val("");
    //     removeRequiredText(typeMandataire);
    // }

    setInputColor(typeMandataire);
}

/**
 * Mi-desactiver/activer ny Profession liberale
 * Activer si forme activite = profession liberale
 * @param formeActivite
 */
function setProfessionLiberale(formeActivite)
{
    var professionLiberale = $('#js_profession_liberale');

    //1: profession liberale
    if(formeActivite !== 'CODE_PROFESSION_LIBERALE')
    {
        professionLiberale.attr('disabled',"");
        professionLiberale.removeAttr('required');
        professionLiberale.val("");
        removeRequiredText(professionLiberale);
        // removeChampObligatoireLabel(professionLiberale);
    }
    else
    {
        professionLiberale.removeAttr('disabled');
        setRequired(professionLiberale);
    }

    setInputColor($('#js_profession_liberale'));
}

/**
 * Mi-desactiver/activer ny statuts en fonction an'ny forme juridique
 * @param formeJuridique
 */
function setStatuts(formeJuridique){

    var statuts = $('#js_statut');
    //Entreprise individuelle
    if(formeJuridique == "CODE_INDIVIDUELLE" || formeJuridique == "CODE_ENTREPRISE_INDIVIDUELLE"){
        statuts.attr('disabled',"");
        statuts.removeAttr('required');
        statuts.val("");
        removeRequiredText(statuts);
    }
    else{

        var comptaSurServeur = $('#js_compta_sur_serveur').val();

        //dossier dejà traité
        if(comptaSurServeur != 3){
            statuts.removeAttr('disabled');
            setRequired(statuts);
        }
    }

    setInputColor(statuts);
}


/**
 * Mi-descativer/activer ny suivi chèque ho an'ny dossier + instruction tous dossier
 * @param rapprochementBanque
 */
function setSuiviChequeInstruction(rapprochementBanque)
{
    var suiviCheque = $('#js_instr_suivi_cheque_emis');

    if(parseInt(rapprochementBanque) == 0 || isNaN(rapprochementBanque))
    {
        suiviCheque.attr('disabled',"");
        suiviCheque.removeAttr('required');
        suiviCheque.val("");
        removeRequiredText(suiviCheque);


    }
    else
    {
        suiviCheque.removeAttr('disabled');
        setRequired(suiviCheque);

    }

    setInputColor(suiviCheque);
}


/**
 * Mi-descativer/activer ny suivi chèque ho an'ny dossier + instruction tous dossier
 * @param rapprochementBanque
 * @param withRappBanque
 */
function setSuiviCheque(rapprochementBanque, withRappBanque)
{
    var suiviCheque = $('#js_instr_suivi_cheque_emis');
    var suiviChequeDoss = $('#js_suivi_cheque_emis_doss');

    if(parseInt(rapprochementBanque) == 0 || isNaN(rapprochementBanque))
    {
        suiviCheque.attr('disabled',"");
        suiviCheque.removeAttr('required');
        suiviCheque.val("");
        removeRequiredText(suiviCheque);

        if(!withRappBanque){
            suiviChequeDoss.attr('disabled',"");
            suiviChequeDoss.removeAttr('required');
            suiviChequeDoss.val("");
            removeRequiredText(suiviChequeDoss);
        }
    }
    else
    {
        suiviCheque.removeAttr('disabled');
        setRequired(suiviCheque);

        if(!withRappBanque){
            suiviChequeDoss.removeAttr('disabled');
            setRequired(suiviChequeDoss);
        }
    }

    setInputColor(suiviCheque);
    setInputColor(suiviChequeDoss);
}

/**
 * Mi-descativer/activer ny suivi chèque ho an'ny dossier
 * @param rapprochementBanqueDossier
 */
function setSuiviChequeDossier(rapprochementBanqueDossier) {

    var suiviChequeDoss = $('#js_suivi_cheque_emis_doss');

    if (parseInt(rapprochementBanqueDossier) == 0 || isNaN(rapprochementBanqueDossier)) {
        suiviChequeDoss.attr('disabled', "");
        suiviChequeDoss.removeAttr('required');
        suiviChequeDoss.val("");
        removeRequiredText(suiviChequeDoss);
    }
    else {
        suiviChequeDoss.removeAttr('disabled');
        setRequired(suiviChequeDoss);
    }

    setInputColor(suiviChequeDoss);
}

/**
 * Mi-desactiver/activer ny Taxe sur les salaires
 * Activé raha TVA Régime != TVA non soumis
 * @param tvaRegime
 */
function setTaxeSalaire(tvaRegime) {
    //13: Tva non soumis
    var taxeSalaire = $('#js_taxe_salaire');
    if(tvaRegime !== 'CODE_NON_SOUMIS' && tvaRegime !== 'CODE_FRANCHISE' )
    {
        //Griser si != tva non soumis
        taxeSalaire.attr('disabled',"");
        taxeSalaire.removeAttr('required');
        taxeSalaire.val("");
        removeRequiredText(taxeSalaire);

        // removeChampObligatoireLabel(taxeSalaire);
    }
    else
    {
        setRequired(taxeSalaire);
        taxeSalaire.removeAttr('disabled');
    }

    setInputColor(taxeSalaire);
}


/**
 * Mi-desactiver/activer ny TVA Taux sy Date Tva
 * @param tvaRegime
 */
function setTvaDateTaux(tvaRegime) {

    // var tvas = $('.tva-comptable-fiscal');
    //13:Non soumis
    if (tvaRegime === 'CODE_NON_SOUMIS' || tvaRegime === 'CODE_FRANCHISE') {

        $("#js_tva_taux").chosen('destroy');
        $("#js_tva_taux").removeAttr('multiple').prop('disabled', true);
        $("#js_tva_taux").removeAttr('required');
        $("#js_tva_taux").val("");

        $("#js_date_tva").val("");
        $("#js_date_tva").prop('disabled', true);
        $("#js_date_tva").removeAttr('required');

        removeRequiredText($("#js_date_tva"));
        removeRequiredText($("#js_tva_taux"));

        // removeChampObligatoireLabel($("#js_tva_taux"));
        // removeChampObligatoireLabel($('#js_date_tva'));

    }
    else if (tvaRegime != undefined){

        $("#js_date_tva").removeAttr('disabled');

        $("#js_tva_taux").chosen('destroy');
        $("#js_tva_taux").attr('multiple','').prop('disabled', false);
        $("#js_tva_taux").chosen();


        setRequired($("#js_date_tva"));
        setRequired($("#js_tva_taux"));

    }

    setInputColor($("#js_date_tva"));
    // setInputColor($("#js_tva_taux"));



}

/**
 * Mi-dectiver/activer ny TVA Mode, mi-afficher/caher ny option ao anatiny
 * @param tvaRegime
 */
function setTvaFaitGenerateur(tvaRegime) {

    if(tvaRegime === 'CODE_NON_SOUMIS' || tvaRegime === 'CODE_FRANCHISE')
    {
        $('#js_tva_fait_generateur').prop('disabled',true);
        $('#js_tva_fait_generateur').removeAttr('required');
        $('#js_tva_fait_generateur').val('');
        removeRequiredText($('#js_tva_fait_generateur'));

        // removeChampObligatoireLabel( $('#js_tva_mode'));
        setInputColor( $('#js_tva_fait_generateur'));
    }

    else{
        $('#js_tva_fait_generateur').removeAttr('disabled');
        setRequired($('#js_tva_fait_generateur'));
    }
}

/**
 * Mi-dectiver/activer ny TVA Mode, mi-afficher/caher ny option ao anatiny
 * @param tvaRegime
 * @param isFirstLoad
 */
function setTvaMode(tvaRegime,isFirstLoad) {

    var tvaMode = $('#js_tva_mode'),
        regimeSimplifie = $('.regime_simplifie'),
        regimeNormale = $('.regime_normale');

    regimeSimplifie.hide();
    regimeNormale.hide();

    //13: non soumis
    if(tvaRegime === 'CODE_NON_SOUMIS' || tvaRegime === 'CODE_FRANCHISE')
    {
        tvaMode.prop('disabled',true);
        tvaMode.removeAttr('required');
        if(!isFirstLoad){
            tvaMode.val('');
        }
        removeRequiredText(tvaMode);
        // removeChampObligatoireLabel( $('#js_tva_mode'));
        setInputColor(tvaMode);
    }

    //16: Regime simplifié
    else if(tvaRegime === "CODE_REGIME_SIMPLIFIE" || tvaRegime === "CODE_REEL_SIMPLIFIE_AGRICOLE")
    {
        tvaMode.removeAttr('disabled');
        // regimeSimplifie.show();
        if(!isFirstLoad) {
            tvaMode.val('0');
        }
        $('.regime_simplifie[value="0"]').show();
        setRequired(tvaMode);
    }

    //14: Regime normale
    else if(tvaRegime === "CODE_REGIME_NORMAL" || tvaRegime === "CODE_MINI_REEL")
    {
        tvaMode.removeAttr('disabled');
        if(!isFirstLoad){
            tvaMode.val('');
        }
        regimeNormale.show();
        setRequired(tvaMode);
    }
    else{
        tvaMode.removeAttr('disabled');
        if(!isFirstLoad) {
            tvaMode.val('');
        }
        regimeSimplifie.show();
        regimeNormale.show();
        setRequired(tvaMode);
    }
}

/**
 * Mi-initialiser ny valeur-an'ny TVA Regime
 * @param formeJuridique
 */
function setTvaRegime(formeJuridique) {

    //15:forme juridique Auto entreprise
    if(formeJuridique === 'CODE_AUTO_ENTREPRISE')
    {
        var comptaServeur = $('#js_compta_sur_serveur').val();
        //13:regime_tva non soumis

        $("#js_tva_regime option").each(function(){
            if ($(this).attr("data-code") === 'CODE_NON_SOUMIS') {
                $('#js_tva_regime').val($(this).val());
                return false;
            }
        });

        //Activiter-na ny taxe salaire
        setTaxeSalaire("CODE_NON_SOUMIS");

        //Griser-na ny date tva & taux tva
        setTvaDateTaux("CODE_NON_SOUMIS");

        //modifier valeur tva mode
        setTvaMode("CODE_NON_SOUMIS", false);

        //modifier valeur tva fait generateur
        setTvaFaitGenerateur("CODE_NON_SOUMIS");

        setPrestationTvaByRegimeTva("CODE_NON_SOUMIS");
    }

}

/**
 *
 * @param regimeFiscal
 * @param isFirstLoad
 */
function setTVaRegimeByRegimeFiscal(regimeFiscal,isFirstLoad){

    if(!isFirstLoad) {

        if (regimeFiscal == 'CODE_BA') {
            $('#js_tva_regime option').each(function () {
                if ($(this).attr('data-code') == 'CODE_REEL_SIMPLIFIE_AGRICOLE') {

                    // if (isFirstLoad == true) {
                    //     $('#js_tva_regime').val($(this).val());
                    // }
                    // else {
                    //     $('#js_tva_regime').val($(this).val()).change();
                    //     $('#js_tva_regime').val($(this).val()).change();
                    // }

                    $('#js_tva_regime').val($(this).val()).change();
                    $('#js_tva_regime').val($(this).val()).change();
                }
            });
        }
        // else if (regimeFiscal == "CODE_BNC") {
        //     $('#js_tva_regime option').each(function () {
        //         if ($(this).attr('data-code') == 'CODE_REGIME_SIMPLIFIE') {
        //
        //             // if (isFirstLoad == true) {
        //             //     $('#js_tva_regime').val($(this).val());
        //             // }
        //             // else {
        //             //     $('#js_tva_regime').val($(this).val()).change();
        //             //     $('#js_tva_regime').val($(this).val()).change();
        //             // }
        //
        //             $('#js_tva_regime').val($(this).val()).change();
        //             $('#js_tva_regime').val($(this).val()).change();
        //         }
        //     });
        // }
    }
}

/**
 * Mi-initialiser ny valeur an'ny champ raha efa envoyé izy na tsia
 * @param selecteur
 * @param nbrOption
 * @returns {*|{}|jQuery}
 */
function setValueAEnvoyer(selecteur,nbrOption) {
    var selectVal = $('#' + selecteur).val();
    var selectEnvoi = $('#' + selecteur).find(':selected').attr('est-envoye');

    //Oui ou non ny ao anaty Combo
    if(nbrOption == 2) {

        //Raha efa envoyé ilay izy ka à envoyer ihany no selectionné
        if (selectVal == 1 && selectEnvoi == 1) {
            selectVal = 2;
        }
    }
    //Oui Non Pas ny ao anaty Combo
    else if (nbrOption == 3){
        //Raha efa envoyé ilay izy ka à envoyer ihany no selectionné
        if(selectEnvoi ==1)
        {
            if(selectVal == 1){
                selectVal = 3;
            }
            else if(selectVal == 2){
                selectVal = 4;
            }
        }
    }

    return selectVal;
}


function setVenteByModeVente(modeVente){
    if(modeVente === 'CODE_CAISSE'){
        $('#js_vente').val(3);
    }
}

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

function verifier_champ_obligatoire_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '')
        return [true, ""];

    if (posdata == '')
        message = "Le champ " + colName + " est obligatoire";

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info_perdos('INFORMATION', message, 'warning');

    return [false, ""];

}

function verifier_champ_nombre_obligatoire_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '' && $.isNumeric(posdata))
        return [true, ""];

    else {
        if (posdata == '')
            message = "Le champ " + colName + " est obligatoire";
        else{
            message = "Le champ " + colName + " n' est pas valide";
        }
    }

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info_perdos('INFORMATION', message, 'warning');

    return [false, ""];
}

function verifier_mail_jqgrid(posdata, colName) {
    var message = "";
    if (posdata != '' && isValidEmailAddress(posdata))
        return [true, ""];

    else {
        if (posdata == '')
            message = "Le champ " + colName + " est obligatoire";
        else{
            message = posdata + " est un mail invalide";
        }
    }

    setTimeout(function () {
        $("#info_dialog").hide();
    }, 10);

    show_info_perdos('INFORMATION', message, 'warning');


    return [false, ""];
}

/**
 * Mi-afficher ny jqGrid rehetra
 * @param idDossier
 * @param gridWidth
 * @param etape
 */
function showGrids(idDossier,gridWidth,etape) {

    var banqueGrid = $('#js_infoCarac_banque_liste');
    var lastsel_banque;

    var mandataireGrid = $('#js_infoCarac_mandataire_liste');
    var lastsel_mandataire;
    var selected;

    var responsableDossierGrid = $('#js_infoCarac_responsableDossier_liste');
    var lastsel_respDossier;

    var vehiculeGrid = $('#js_infoCarac_vehicule_liste');
    var lastsel_vehicule;

    if(idDossier == null || idDossier == ''){
        idDossier = '0';
    }

    banqueGrid.jqGrid({
        url: Routing.generate('info_perdos_banqueCompte', {dossierId: idDossier}),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 200,
        shrinkToFit: true,
        viewrecords: true,
        rownumbers: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        // pager: '#js_infoCarac_banque_pager',
        caption: " ",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_banqueCompte_edit', {dossierId: idDossier}),
        // colNames: ['Nom de la banque', 'Code banque', 'Nom banque autre', 'Code banque autre', 'Numero de compte', 'Numero CB', 'IBAN', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colNames: [
            'Nom de la banque',
            'Code banque',
            'Nom banque autre',
            'Numero de compte',
            'Numero CB',
            'IBAN',
            'Compte',
            '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {
                name: 'banque-nom', index: 'banque-nom', editable: true, width: 300, fixed: true, edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_banque', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    },
                    dataEvents:[{
                        type:'change',
                        fn: function (e) {
                            selected = parseInt($(e.target).val());

                            //-1: autre
                            if(selected != -1)
                            {
                                $('#'+lastsel_banque+'_banque-nom-autre').attr('disabled',true);
                                $('#'+lastsel_banque+'_banque-nom-autre').css({backgroundColor: '#dcdcdc'});
                                $('#'+lastsel_banque+'_banque-nom-autre').val('');
                                $('#js_infoCarac_banque_liste').jqGrid('getColProp', 'banque-nom-autre').editrules = {required: false};

                                $('#'+lastsel_banque+'_banque-code').attr('disabled',true);
                                $('#'+lastsel_banque+'_banque-code').css({backgroundColor: '#dcdcdc'});

                                $.ajax({
                                    data: {
                                        banqueId: $(e.target).val()
                                    },

                                    url:Routing.generate('info_perdos_banque_code'),
                                    type: 'POST',
                                    contentType: "application/x-www-form-urlencoded;charset=utf-8",
                                    beforeSend: function (jqXHR) {
                                        jqXHR.overrideMimeType('text/html;charset=utf-8');
                                    },
                                    async: true,
                                    dataType: 'html',
                                    success: function (data) {

                                        if(data != '-1') {
                                            $('#' + lastsel_banque + '_banque-code').val(data);
                                        }
                                        else{
                                            $('#' + lastsel_banque + '_banque-code').val('');
                                        }
                                    }

                                });

                                $('#js_infoCarac_banque_liste').jqGrid('getColProp', 'banque-code').editrules = {required: false};
                            }
                            else
                            {
                                $('#'+lastsel_banque+'_banque-nom-autre').removeAttr('disabled');
                                $('#'+lastsel_banque+'_banque-nom-autre').css({backgroundColor: 'white'});
                                $('#js_infoCarac_banque_liste').jqGrid('getColProp', 'banque-nom-autre').editrules = {custom: true, custom_func: verifier_champ_obligatoire_jqgrid};

                                $('#'+lastsel_banque+'_banque-code').removeAttr('disabled');
                                $('#'+lastsel_banque+'_banque-code').css({backgroundColor: 'white'});
                                $('#'+lastsel_banque+'_banque-code').val('');
                                $('#js_infoCarac_banque_liste').jqGrid('getColProp', 'banque-code').editrules = {custom: true, custom_func: verifier_champ_obligatoire_jqgrid};
                            }
                        }
                    }]
                },
                // editrules: { required: true },
                editrules: {custom: true, custom_func: verifier_champ_obligatoire_jqgrid},
                classes: 'banque-nom'

            },

            {
                name: 'banque-code',
                index: 'banque-code',
                editable: true,
                edittype: 'text',
                // editoptions: {disabled: true},
                editoptions: {
                    dataInit: function (e) {
                        e.style.textAlign = 'right';
                    },
                    disabled: true
                },
                classes: 'banque-code',
                align: "right"
            },

            {
                name: 'banque-nom-autre',
                index: 'banque-nom-autre',
                editable: true,
                edittype: 'text',
                editoptions: {disabled: true},
                classes: 'banque-nom-autre'
            },

            // {
            //     name: 'banque-code-autre',
            //     index: 'banque-code-autre',
            //     editable: true,
            //     edittype: 'text',
            //     editoptions: {disabled: true},
            //     classes: 'banque-code-autre'
            //
            // },

            {
                name: 'banque-numero', index: 'banque-numero', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: { required: true,  number: true},
                editrules: {custom: true, custom_func: verifier_champ_nombre_obligatoire_jqgrid},
                classes: 'js-banque-numero'

            },

            {
                name: 'banque-numcb', index: 'banque-numcb', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: { required: true, number: true },
                // editrules: {custom: true, custom_func: verifier_champ_nombre_obligatoire_jqgrid},
                classes: 'js-banque-numcb'
            },

            {
                name: 'banque-iban', index: 'banque-iban', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: { required: true, number: true },
                // editrules: {custom: true, custom_func: verifier_champ_nombre_obligatoire_jqgrid},
                classes: 'js-banque-iban'
            },

            {
                name: 'compte-banque', index: 'compte-banque', editable: true, width: 200, fixed: true, edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_compte_banque', {dossierId: idDossier}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                classes: 'js-banque-compte'
            },

            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-banqueCompte" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-banqueCompte" title="Supprimer"></i>'},
                classes: 'js-banque-action'
            }
        ],


        onSelectRow: function (id) {
            if (id && id !== lastsel_banque) {
                banqueGrid.restoreRow(lastsel_banque);
                lastsel_banque = id;
            }
            banqueGrid.editRow(id, false);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;


        },

        loadComplete: function (data) {

            if ($("#btn-add-banqueCompte").length == 0) {
                banqueGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-banqueCompte" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

            $('#js_infoCarac_banque_liste').jqGrid('setGridWidth', gridWidth);

            // $("tr.jqgrow:odd").css("background", "cornsilk");
        },

        ajaxRowOptions: {async: true},

        reloadGridOptions: {fromServer: true}

    });

    ///Ajout nouvelle banqueCompte
    $(document).on('click', '#btn-add-banqueCompte', function (event) {

        if(canAddRow(banqueGrid)) {
            event.preventDefault();
            banqueGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_infoCarac_banque_liste").effect("highlight", 20000);
        }
    });

    // Enregistrement modif banqueCompte
    $(document).on('click', '.js-save-banqueCompte', function (event) {

        event.preventDefault();
        event.stopPropagation();
        banqueGrid.jqGrid('saveRow', lastsel_banque, {
            "aftersavefunc": function() {
                reloadGrid(banqueGrid, Routing.generate('info_perdos_banqueCompte', {dossierId: idDossier }));
            }
        });
    });

    $(document).on('click', '.js-remove-banqueCompte', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_infoCarac_banque_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_banqueCompte_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    // Supprimer une banqueCompte
    // $(document).on('click', '.js-remove-banqueCompte', function (event) {
    //     event.stopPropagation();
    //     event.preventDefault();
    //     var rowid = $(this).closest('tr').attr('id');
    //     if(rowid =='new_row') {
    //         $(this).closest('tr').remove();
    //         return;
    //     }
    //     $('#js_infoCarac_banque_liste').jqGrid('delGridRow', rowid, {
    //         url: Routing.generate('info_perdos_banqueCompte_remove'),
    //         top: 200,
    //         left: 400,
    //         width: 400,
    //         mtype: 'DELETE',
    //         closeOnEscape: true,
    //         modal: true,
    //         msg: 'Supprimer cet enregistrement ?'
    //     });
    // });

    // mandataireGrid.jqGrid({
    //     url: Routing.generate('info_perdos_responsable', {typeResponsable: 0, dossierId: idDossier}),
    //     datatype: 'json',
    //     loadonce: true,
    //     sortable: true,
    //     autowidth: true,
    //     height: 120,
    //     shrinkToFit: true,
    //     viewrecords: true,
    //     rownumbers: true,
    //     rowNum: 100,
    //     rowList: [100, 200, 500],
    //     // pager: '#js_infoCarac_mandataire_pager',
    //     caption: "MANDATAIRE",
    //     hidegrid: false,
    //     editurl: Routing.generate('info_perdos_responsable_edit', {typeResponsable: 0, dossierId: idDossier}),
    //     colNames: ['Mandataire', 'Statut social', 'Regime suivi', 'Complementaire', 'Nom', 'Prenom', 'email', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
    //     colModel: [
    //
    //         {
    //             name: 'mandataire-mandataire',
    //             index: 'mandataire-mandataire',
    //             editable: true,
    //             // align: "center",
    //             width: 100,
    //             fixed: true,
    //             edittype: 'select',
    //             editoptions: {
    //                 dataUrl: Routing.generate('info_perdos_mandataire_mandataire', {json: 0}),
    //                 dataInit: function (elem) {
    //                     $(elem).width(100);
    //                 },
    //                 dataEvents:[{
    //                     type:'change',
    //                     fn: function (e) {
    //                         selected = parseInt($(e.target).val());
    //
    //                         //President
    //                         if(selected ==1)
    //                         {
    //                             $('#'+lastsel_mandataire+'_mandataire-statut-social').attr('disabled',true);
    //                             $('#'+lastsel_mandataire+'_mandataire-statut-social').css({backgroundColor: '#dcdcdc'});
    //                             $('#'+lastsel_mandataire+'_mandataire-statut-social').val('');
    //                         }
    //                         else
    //                         {
    //                             $('#'+lastsel_mandataire+'_mandataire-statut-social').removeAttr('disabled');
    //                             $('#'+lastsel_mandataire+'_mandataire-statut-social').css({backgroundColor: 'white'});
    //                         }
    //
    //                     }
    //                 }]
    //             },
    //             editrules: {required : true},
    //             classes: 'mandataire-mandataire'
    //         },
    //
    //         {
    //             name: 'mandataire-statut-social',
    //             index: 'mandataire-statut-social',
    //             editable: true,
    //             // align: "center",
    //             width: 100,
    //             fixed: true,
    //             edittype: 'select',
    //             editoptions: {
    //                 dataUrl: Routing.generate('info_perdos_mandataire_statut', {json: 0}),
    //                 dataInit: function (elem) {
    //                     $(elem).width(100);
    //                 }
    //             },
    //             // editrules: {required: true},
    //             classes: 'mandataire-statut-social'
    //         },
    //
    //         {
    //             name: 'mandataire-regime-suivi',
    //             index: 'mandataire-regime-suivi',
    //             editable: true,
    //             // align: "center",
    //             width: 100,
    //             fixed: true,
    //             edittype: 'select',
    //             editoptions: {
    //                 dataUrl: Routing.generate('info_perdos_regimeSuivi', {json: 0}),
    //                 dataInit: function (elem) {
    //                     $(elem).width(100);
    //                 }
    //             },
    //             editrules: {required: true},
    //             classes: 'mandataire-statut-suivi'
    //         },
    //
    //         {
    //             name: 'mandataire-complementaire',
    //             index: 'mandataire-complementaire',
    //             editable: true,
    //             // align: "center",
    //             width: 100,
    //             fixed: true,
    //             edittype: 'select',
    //             editoptions: {
    //                 dataUrl: Routing.generate('info_perdos_mandataire_complementaire'),
    //                 dataInit: function (elem) {
    //                     $(elem).width(100);
    //                 }
    //             },
    //
    //             classes: 'mandataire-complementaire'
    //         },
    //
    //         {
    //             name: 'mandataire-nom', index: 'mandataire-nom', editable: true,
    //             editoptions: {defaultValue: ''},
    //             editrules: {required:true},
    //             classes: 'js-mandataire-nom'
    //         },
    //
    //         {
    //             name: 'mandataire-prenom', index: 'mandataire-prenom', editable: true,
    //             editoptions: {defaultValue: ''},
    //             editrules: {required: true},
    //             classes: 'mandataire-prenom'
    //         },
    //
    //         {
    //             name: 'mandataire-email', index: 'mandataire-email', editable: true,
    //             editoptions: {defaultValue: ''},
    //             editrules: {required:true, email: true},
    //             classes: 'mandataire-email'
    //         },
    //
    //         {
    //             name: 'action', index: 'action', width: 40, align: "center", sortable: false,
    //             editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-mandataire" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-mandataire" title="Supprimer"></i>'},
    //             classes: 'js-mandataire-action'
    //         }
    //     ],
    //     onSelectRow: function (id) {
    //         if (id && id !== lastsel_mandataire) {
    //             mandataireGrid.restoreRow(lastsel_mandataire);
    //             lastsel_mandataire = id;
    //         }
    //         mandataireGrid.editRow(id, false);
    //     },
    //     beforeSelectRow: function (rowid, e) {
    //         var target = $(e.target);
    //         var item_action = (target.closest('td').children('.icon-action').length > 0);
    //
    //         return !item_action;
    //
    //
    //     },
    //
    //     loadComplete: function () {
    //
    //         if ($("#btn-add-mandataire").length == 0) {
    //             mandataireGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
    //                 '<button id="btn-add-mandataire" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
    //         }
    //
    //         },
    //
    //     ajaxRowOptions: {async: true},
    //     reloadGridOptions: {fromServer: true}
    //
    // });
    // //Enregistrement modif mandataire
    // $(document).on('click', '.js-save-mandataire', function (event) {
    //
    //
    //     event.preventDefault();
    //     event.stopPropagation();
    //
    //
    //     mandataireGrid.jqGrid('saveRow', lastsel_mandataire, {
    //         "aftersavefunc": function(rowID, response) {
    //             reloadGrid(mandataireGrid,Routing.generate('info_perdos_responsable',{typeResponsable:0,typeCsd:0,dossierId:idDossier}));
    //         }
    //     });
    // });
    // //Ajout nouveau mandataire
    // $(document).on('click', '#btn-add-mandataire', function (event) {
    //
    //     if(canAddRow(mandataireGrid)) {
    //         event.preventDefault();
    //         mandataireGrid.jqGrid('addRow', {
    //             rowID: "new_row",
    //             initData: {},
    //             position: "first",
    //             useDefValues: true,
    //             useFormatter: true,
    //             addRowParams: {extraparam: {}}
    //         });
    //         $("#" + "new_row", "#js_infocarac_mandataire_liste").effect("highlight", 20000);
    //     }
    // });
    // //Supprimer une mandataire
    // $(document).on('click', '.js-remove-mandataire', function (event) {
    //     event.stopPropagation();
    //     event.preventDefault();
    //     var rowid = $(this).closest('tr').attr('id');
    //
    //     if(rowid =='new_row') {
    //         $(this).closest('tr').remove();
    //         return;
    //     }
    //     $('#js_infoCarac_mandataire_liste').jqGrid('delGridRow', rowid, {
    //         url: Routing.generate('info_perdos_responsable_remove'),
    //         top: 200,
    //         left: 400,
    //         width: 400,
    //         mtype: 'DELETE',
    //         closeOnEscape: true,
    //         modal: true,
    //         msg: 'Supprimer cet enregistrement ?'
    //     });
    // });

    responsableDossierGrid.jqGrid({
        url: Routing.generate('info_perdos_responsable', {typeResponsable: 1, dossierId: idDossier}),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 120,
        shrinkToFit: true,
        viewrecords: false,
        rownumbers: false,
        rowNum: 100,
        rowList: [100, 200, 500],
        // pager: '#js_infoCarac_responsableDossier_pager',
        caption: "RESPONSABLE DOSSIER *",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_responsable_edit', {typeResponsable: 1, dossierId: idDossier}),
        colNames: ['Nom', 'Prenom', 'email', 'Responsable','Titre', 'Envoi mail','<span class="fa fa-bookmark-o" style="display:inline-block"/>'],
        colModel: [

            {
                name: 'responsableDossier-nom', index: 'responsableDossier-nom', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: {required: true},
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'js-responsableDossier-nom'
            },

            {
                name: 'responsableDossier-prenom', index: 'responsableDossier-prenom', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: {required: true},
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'responsableDossier-prenom'
            },

            {
                name: 'responsableDossier-email', index: 'responsableDossier-email', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: {required: true, email: true},
                editrules: { custom: true, custom_func: verifier_mail_jqgrid },
                classes: 'responsableDossier-email'
            },

            {
                name: 'responsableDossier-type',
                index: 'responsableDossier-type',
                editable: true,
                // align: "center",
                width: 120,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_responsable_type'),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: {required: true},
                classes: 'responsableDossier-type'
            },

            {
                name: 'responsableDossier-titre',
                index: 'responsableDossier-titre',
                editable: true,
                // align: "center",
                width: 150,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_responsable_titre', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: {required: true},
                classes: 'responsableDossier-titre'
            },



            {
                name: 'responsableDossier-envoi',
                index: 'responsableDossier-envoi',
                editable: true,
                align: "center",
                width: 65,
                fixed: true,
                edittype: 'checkbox',
                formatter: 'checkbox',
                // editrules: {required: true},
                classes: 'responsableDossier-envoi',
                cellattr: function () { return ' title="Recevoir mail à chaque modification du dossier"'; }
            },

            {
                name: 'action', index: 'action', width: 40, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-responsableDossier" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-responsableDossier" title="Supprimer"></i>'},
                classes: 'js-responsableDossier-action'
            }
        ],
        onSelectRow: function (id) {

            if (id && id !== lastsel_respDossier) {
                responsableDossierGrid.restoreRow(lastsel_respDossier);
                lastsel_respDossier = id;
            }
            responsableDossierGrid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            if ($("#btn-add-responsableDossier").length == 0) {
                responsableDossierGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-responsableDossier" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }

            $('#js_infoCarac_responsableDossier_liste').jqGrid('setGridWidth',gridWidth);

            // $("tr.jqgrow:odd").css("background", "cornsilk");

        },

        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });
    //Enregistrement modif responsable dossier
    $(document).on('click', '.js-save-responsableDossier', function (event) {
        event.preventDefault();
        event.stopPropagation();
        responsableDossierGrid.jqGrid('saveRow', lastsel_respDossier, {
            "aftersavefunc": function() {
                reloadGrid(responsableDossierGrid, Routing.generate('info_perdos_responsable', {
                    typeResponsable: 1,
                    dossierId: idDossier
                }));

                $('#tab-info-generales div:nth-child(5) h5').removeClass();
                $('#tab-info-generales div:nth-child(5) h5').text('Gestion du dossier');


                var forms = $('[id^="js_form_info"]');
                var canValide = true;
                forms.each(function () {
                    if (!($(this).valid())) {
                        canValide = false;
                    }
                });
                setTabInformationParForms(canValide, etape, 1, forms);

                withResp = withResponsable(dossier_id)
            }
        });
    });
    // //Ajout nouveau responsable dossier
    $(document).on('click', '#btn-add-responsableDossier', function (event) {
        if(canAddRow(responsableDossierGrid)) {

            event.preventDefault();
            responsableDossierGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_infoCarac_responsableDossier_liste").effect("highlight", 20000);
        }

    });
    //Supprimer un responsable dossier
    $(document).on('click', '.js-remove-responsableDossier', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_infoCarac_responsableDossier_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_responsable_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            afterComplete:function(data,postd) {
                var forms = $('[id^="js_form_info"]');
                var canValide = true;
                forms.each(function () {
                    if (!($(this).valid())) {
                        canValide = false;
                    }
                });

                //Mijery raha efa misy responsable dossier na tsia
                // var resp = withResponsable($('#js_dossier_id').val());

                withResp = withResponsable(dossier_id);

                if (!withResp) {
                    canValide = false;
                    $('#tab-info-generales div:nth-child(5) h5').removeClass().addClass('label-danger');
                    $('#tab-info-generales div:nth-child(5) h5').text('Gestion du dossier (Obligatoire)');
                }

                setTabInformationParForms(canValide, etape, 1, forms);
            },
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    vehiculeGrid.jqGrid({
        url: Routing.generate('info_perdos_vehicule', {dossierId: idDossier}),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: 200,
        shrinkToFit: true,
        viewrecords: true,
        rownumbers: true,
        rowNum: 100,
        rowList: [100, 200, 500],
        // pager: '#js_infoCarac_vehicule_pager',
        caption: " ",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_vehicule_edit', {dossierId: idDossier}),
        colNames: ['Proprietaire','Marque', 'Modèle', 'Carte grise', 'Matricule', 'Type remboursement', 'Type vehicule', 'Carburant', 'Nombre de Cv', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [

            {
                name: 'vehicule-propietaire',
                index: 'vehicule-proprietaire',
                editable: true,
                //align: "center",
                width: 90,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_vehicule_proprietaire', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-marque'

            },

            {
                name: 'vehicule-marque',
                index: 'vehicule-marque',
                editable: true,
                //align: "center",
                width: 90,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_vehicule_marque', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-marque'

            },

            {
                name: 'vehicule-modele', index: 'vehicule-modele', editable: true,
                editoptions: {defaultValue: ''},
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'js-vehicule-modele'

            },

            {
                name: 'vehicule-carte-grise',
                index: 'vehicule-carte-grise',
                editable: true,
                //align: "center",
                width: 120,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_vehicule_combo_envoi'),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-carte-grise'
            },

            {
                name: 'vehicule-immatricule', index: 'vehicule-immatricule', editable: true,
                editoptions: {defaultValue: ''}, classes: 'js-vehicule-immatricule',
                // editrules: { required: true }
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid }
            },

            {
                name: 'vehicule-type-remboursement',
                index: 'vehicule-type-remboursement',
                editable: true,
                // align: "center",
                width: 160,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_type_vehicule', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-type-remboursement'
            },


            {
                name: 'vehicule-type-vehicule',
                index: 'vehicule-type-vehicule',
                editable: true,
                // align: "center",
                width: 160,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ndf_type_vehicule', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-type-vehicule'
            },

            {
                name: 'vehicule-carburant',
                index: 'vehicule-carburant',
                editable: true,
                // align: "center",
                width: 100,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_carburant', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-carburant'
            },

            {
                name: 'vehicule-puissance',
                index: 'vehicule-puissance',
                editable: true,
                // align: "center",
                width: 90,
                fixed: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_vehicule_nb_cv'),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                // editrules: { required: true },
                editrules: { custom: true, custom_func: verifier_champ_obligatoire_jqgrid },
                classes: 'vehicule-puissance'
            },

            {
                name: 'action', index: 'action', width: 60, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-save icon-action js-save-vehicule" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-vehicule" title="Supprimer"></i>'},

                classes: 'js-vehicule-action'
            }
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_vehicule) {
                vehiculeGrid.restoreRow(lastsel_vehicule);
                lastsel_vehicule = id;
            }
            vehiculeGrid.editRow(id, false);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var cell_action = target.hasClass('js-activite-action');
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (cell_action || item_action) {
                return false;
            }
            return true;

        },

        loadComplete: function () {

            if ($("#btn-add-vehicule").length == 0) {
                vehiculeGrid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-vehicule" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
            }


            $('#js_infoCarac_vehicule_liste').jqGrid('setGridWidth',gridWidth);

            // $("tr.jqgrow:odd").css("background", "cornsilk");
        },

        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });
    //Enregistrement modif vehicule
    $(document).on('click', '.js-save-vehicule', function (event) {

        event.preventDefault();
        event.stopPropagation();
        vehiculeGrid.jqGrid('saveRow', lastsel_vehicule, {
            "aftersavefunc": function() {
                reloadGrid(vehiculeGrid,Routing.generate('info_perdos_vehicule', {dossierId:idDossier}));
                $('#js_infoCarac_vehicule_liste').jqGrid('setGridWidth',gridWidth);
            }
        });
    });
    //Ajout nouveau vehicule
    $(document).on('click', '#btn-add-vehicule', function (event) {

        if(canAddRow(vehiculeGrid,idDossier)) {
            event.preventDefault();
            vehiculeGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {extraparam: {}}
            });
            $("#" + "new_row", "#js_infoCarac_vehicule_liste").effect("highlight", 20000);
        }

    });
    //Supprimer un vehicule
    $(document).on('click', '.js-remove-vehicule', function (event) {
        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_infoCarac_vehicule_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_vehicule_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'

        });
    });
}

function showRecapGrid(idSite, idClient, idDossier) {
    var recapDossierGrid = $("#js_recap_dossier_liste");
    var lastsel_dossier;

    recapDossierGrid.jqGrid({
        url: Routing.generate('info_perdos_recap', {siteId: idSite, clientId: idClient}),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rownumbers: true,
        pager: '#js_recap_dossier_pager',
        caption: " ",
        hidegrid: false,
        editurl: Routing.generate('info_perdos_recap_edit'),
        colNames: ['Statut', 'Nom','Siren', 'Raison Social', 'Forme juridique','Code APE','Date démarrage','Date premiere clôture','Date clôture',
            'Type Mandataire', 'Nom Prenom', 'Rég fiscal', 'Rég imposition', 'Type activité', 'Forme activité', 'Profession libérale',
            'Type de vente', 'TVA régime', 'TVA paiement', 'TVA fait générateur', 'TVA Taux','TVA date', 'Taxe sur salaire',
            'Convention comptable','Périodicité', 'Ventes', 'Achats', 'Banques','Saisie des OD de paye','Rapprochement banque', 'Suivi des cheques emis',
            'Type prestation', 'TVA', 'Liasse fiscale', 'Accomptes IS et Solde','CICE','CVAE','TVTS','DAS2','CFE','Dividendes', 'Teledeclaration liasse','Teledeclaration Autres',
            '<span class="fa fa-bookmark-o " style="display:inline-block;"/> Action'],
        colModel: [

            // 'Statut', 'Nom','Siren', 'Raison Social', 'Forme juridique','Code APE','Date démarrage','Date premiere clôture','Date clôture',
            {
                name: 'recap-statut', index: 'recap-statut', editable: false,
                editoptions: {defaultValue: ''},
                width: 50,
                fixed: true,
                classes: 'js-recap-statut',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-nom',
                index: 'recap-nom',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-recap-nom',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-siren',
                index: 'recap-siren',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 85,
                fixed: true,
                classes: 'js-recap-siren',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-rs',
                index: 'recap-rs',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-recap-rs',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-forme-juridique',
                index: 'recap-forme-juridique',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_formeJuridique', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 70,
                fixed: true,
                classes: 'recap-forme-juridique',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-code-ape',
                index: 'recap-code-ape',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_code_ape', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 70,
                fixed: true,
                classes: 'recap-code-ape',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-date-demarrage',
                index: 'recap-date-demarrage',
                editable: true,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                classes: 'js-recap-date-demarrage',
                formatter: 'date',
                formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-date-premiere-cloture',
                index: 'recap-date-premiere-cloture',
                editable: true,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                classes: 'js-recap-date-premiere-cloture',
                formatter: 'date',
                formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-date-cloture',
                index: 'recap-date-cloture',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_cloture', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 80,
                fixed: true,
                classes: 'recap-date-cloture',
                cellattr: dossierCellAttr
            },

            // 'Type Mandataire', 'Nom Prenom', 'Rég fiscal', 'Rég imposition', 'Type activité', 'Forme activité', 'Profession libérale',
            {
                name: 'recap-type-mandataire',
                index: 'recap-type-mandataire',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_mandataire_mandataire', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 70,
                fixed: true,
                classes: 'recap-type-mandataire',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-nom-mandataire',
                index: 'recap-nom-mandataire',
                editable: true,
                editoptions: {defaultValue: ''},
                width: 165,
                fixed: true,
                classes: 'js-recap-nom-mandataire',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-regime-fiscal',
                index: 'recap-regime-fiscal',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_regimeFiscal', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 65,
                fixed: true,
                classes: 'recap-regime-fiscal',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-regime-imposition',
                index: 'recap-regime-imposition',
                edittype: 'select',
                editable: true,
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_regimeImposition', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 100,
                fixed: true,
                classes: 'recap-regime-imposition',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-type-activite',
                index: 'recap-type-activite',
                edittype: 'select',
                editable: true,
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_natureActivite', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 75,
                fixed: true,
                classes: 'recap-type-activite',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-forme-activite',
                index: 'recap-forme-activite',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_formeAct', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 90,
                fixed: true,
                classes: 'recap-forme-activite',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-profession-liberale',
                index: 'recap-profession-liberale',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_professionLiberale', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 90,
                fixed: true,
                classes: 'recap-profession-liberale',
                cellattr: dossierCellAttr
            },

            // 'Type de vente', 'TVA régime', 'TVA paiement', 'TVA fait générateur', 'TVA Taux','TVA date', 'Taxe sur salaire',
            {
                name: 'recap-type-vente',
                index: 'recap-type-vente',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_modeVente', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 100,
                fixed: true,

                classes: 'recap-type-vente',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva-regime',
                index: 'recap-tva-regime',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_regimeTva', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-tva-regime',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva-mode',
                index: 'recap-tva-mode',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_tvaMode', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-tva-mode',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva-fait-generateur',
                index: 'recap-tva-fait-generateur',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_tvaFaitGenerateur', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-tva-fait-generateur',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva-taux',
                index: 'recap-tva-taux',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 95,
                fixed: true,
                classes: 'js-recap-tva-taux',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva-date',
                index: 'recap-tva-date',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_tvaDate', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-tva-date',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-taxe-salaire',
                index: 'recap-taxe-salaire',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 70,
                fixed: true,
                classes: 'recap-taxe-salaire',
                cellattr: dossierCellAttr
            },

            // 'Convention comptable','Périodicité', 'Ventes', 'Achats', 'Banques','Saisie des OD de paye','Rapprochement banque', 'Suivi des cheques emis',
            {
                name: 'recap-convention-comptable',
                index: 'recap-convention-comptable',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_connvetion_comptable', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 90,
                fixed: true,

                classes: 'recap-convention-comptable',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-periodicite',
                index: 'recap-periodicite',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_periodicite', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 80,
                fixed: true,
                classes: 'recap-periodicite',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-vente',
                index: 'recap-vente',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_vente', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-vente',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-achat',
                index: 'recap-achat',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_achat', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 110,
                fixed: true,
                classes: 'recap-achat',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-banque',
                index: 'recap-banque',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_banqueRecap', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 110,
                fixed: true,
                classes: 'recap-banque',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-saisie-od',
                index: 'recap-saisie-od',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_saisie_od', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 70,
                fixed: true,
                classes: 'recap-saisie-od',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-rapp-banque',
                index: 'recap-rapp-banque',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0, indifferent: 1}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-rapp-banque',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-suivi-cheque',
                index: 'recap-suivi-cheque',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_methode_suivi_cheque', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 95,
                fixed: true,
                classes: 'recap-rapp-banque',
                cellattr: dossierCellAttr
            },

            // 'Type prestation', 'TVA', 'Liasse fiscale', 'Accomptes IS et Solde','CICE','CVAE','TVTS','DAS2','CFE','Dividendes', 'Teledeclaration liasse','Teledeclaration Autres',
            {
                name: 'recap-type-prestation',
                index: 'recap-type-prestation',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_type_prestation', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 110,
                fixed: true,
                classes: 'recap-type-prestation',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tva',
                index: 'recap-tva',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-tva',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-liasse-fiscal',
                index: 'recap-liasse-fiscal',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-liasse-fical',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-accompte-is',
                index: 'recap-accompte-is',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-accompte-is',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-cice',
                index: 'recap-cice',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0, indifferent:0, sinecessaire: 1}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-cice',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-cvae',
                index: 'recap-cvae',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-cvae',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tvts',
                index: 'recap-tvts',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-tvts',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-das2',
                index: 'recap-das2',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-das2',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-cfe',
                index: 'recap-cfe',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-cfe',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-dividende',
                index: 'recap-dividende',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0, indifferent:0, sinecessaire: 1}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 55,
                fixed: true,
                classes: 'recap-dividende',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tele-declaration-liasse',
                index: 'recap-tele-declaration-liasse',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 90,
                fixed: true,
                classes: 'recap-tele-declaration-liasse',
                cellattr: dossierCellAttr
            },
            {
                name: 'recap-tele-declaration-autre',
                index: 'recap-tele-declaration-autre',
                editable: true,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_ouiNon', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                width: 90,
                fixed: true,
                classes: 'recap-tele-declaration-autre',
                cellattr: dossierCellAttr
            },

            // '<span class="fa fa-bookmark-o " style="display:inline-block;"/> Action'],
            {
                name: 'recap-action', index: 'recap-action', editable: false,
                editoptions: {defaultValue: ''},
                width: 50,
                fixed: true,
                align: 'center',
                classes: 'js-recap-action'
            }

        ],


        onSelectRow: function (id) {
            // if (id && id !== lastsel_dossier) {
            //     recapDossierGrid.restoreRow(lastsel_dossier);
            //     lastsel_dossier = id;
            // }
            // recapDossierGrid.editRow(id, false);


            if (id && id !== lastsel_dossier) {
                recapDossierGrid.restoreRow(lastsel_dossier);
                lastsel_dossier = id;
            }
            if(id != idDossier) {
                recapDossierGrid.editRow(id, false);
            }
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;

        },

        loadComplete: function () {

            $('.ui-jqgrid-titlebar').hide();

            $("#js_recap_dossier_liste").jqGrid('destroyGroupHeader');

            $("#js_recap_dossier_liste").jqGrid('setGroupHeaders', {
                useColSpanStyle: true,
                groupHeaders: [
                    {startColumnName: 'recap-nom', numberOfColumns: 10, titleText: 'Identification Dossier'},
                    {startColumnName: 'recap-regime-fiscal', numberOfColumns: 12, titleText: 'Caractéristiques du dossier'},
                    {startColumnName: 'recap-vente', numberOfColumns: 6, titleText: 'Méthodes comptables'},
                    {startColumnName: 'recap-tva', numberOfColumns: 11, titleText: 'Fiscales'}
                ]
            });

            var recapHeight = $('#tab-piece-a-envoyer .scroller').height();
            recapDossierGrid.jqGrid('setGridHeight', recapHeight - 80);

        },

        ajaxRowOptions: {async: true},

        reloadGridOptions: {fromServer: true}

    });



    $(document).on('click', '.js-recap-action', function (event) {
        event.preventDefault();
        event.stopPropagation();
        recapDossierGrid.jqGrid('saveRow', lastsel_dossier, {
            "aftersavefunc": function() {
                reloadGrid(recapDossierGrid, Routing.generate('info_perdos_recap', {siteId: idSite, clientId: idClient}));

                checkDossier(lastsel_dossier);
            }
        });
    });
}

function showScripturaGrid(annee, mois) {
    var scripturaGrid = $("#js_scriptura_liste");

    var lastSelClient = '';

    scripturaGrid.jqGrid({
        url: Routing.generate('info_perdos_scriptura', {annee: annee, mois: mois} ),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        shrinkToFit: true,
        viewrecords: true,
        rownumbers: true,
        pager: '#js_recap_dossier_pager',
        caption: " ",
        hidegrid: false,
        // editurl: Routing.generate('info_perdos_recap_edit'),
        colNames: ['Client','Nombre de dossier', 'Dossier en création', 'Dossier créé', 'Dossier créé du mois'],
        colModel: [
            {
                name: 'script-client',
                index: 'script-client',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-script-client',
                cellattr: dossierCellAttr
            },
            {
                name: 'script-total',
                index: 'script-total',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-script-total',
                cellattr: dossierCellAttr,
                align: 'right',
                sorttype: 'number'
            },
            {
                name: 'script-en-creation',
                index: 'script-en-creation',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-script-en-creation',
                cellattr: dossierCellAttr,
                align: 'right',
                sorttype: 'number'
            },
            {
                name: 'script-cree',
                index: 'script-cree',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-script-cree',
                cellattr: dossierCellAttr,
                align: 'right',
                sorttype: 'number'

            },
            {
                name: 'script-cree-mois',
                index: 'script-cree-mois',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 140,
                fixed: true,
                classes: 'js-script-cree-mois',
                cellattr: dossierCellAttr,
                align: 'right',
                sorttype: 'number'

            }


        ],

        loadComplete: function () {

            $('.ui-jqgrid-titlebar').hide();

            var scriptHeight = $('#tab-piece-a-envoyer .scroller').height();
            scripturaGrid.jqGrid('setGridHeight', scriptHeight - 80);

        },


        ajaxRowOptions: {async: true},

        reloadGridOptions: {fromServer: true}

    });

}

/**
 * Upload file
 * @param selecteur
 */
function uploadFile(selecteur) {

    if ($('#'+selecteur).val()) {
        $('#' + selecteur).fileinput('upload');
    }
}

function verifierSiren(siren,dossierId){
    var estValide = false;

    var isSir = (isSiren(siren) || isSiret(siren));

    var siteId = $('#site').val();

    if(isSir) {
        $.ajax({
            data: {siren: siren, dossierId: dossierId, siteId: siteId},
            url: Routing.generate('info_perdos_verifier_siren'),
            type: 'POST',
            async: false,
            contentType: 'application/x-www-form-urlencoded;charset=utf-8',
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            success: function (data) {

                if (data == 1) {
                    estValide = true;
                }
            }
        });

    }
    return estValide;
}

/**
 * Mi-verifier ny Nom Dossier raha misy mitovy na tsia 70%
 * @param dossierId
 * @returns {boolean}
 */
function verifierNomDossier(dossierId) {
    var ret = true;

    var nomDossier = $('#js_nom_dossier').val();
    var site = $('#site').val();
    var siteText =$("#site option:selected").text();
    var monosite = true;

    var sites  =$('#site option');

    if(sites.size()>2)
    {
        monosite = false;
    }
    else {
        site = (sites[1]).value;
    }

    //Mbola tsy ni-selectionner site
    if(siteText == "Tous" && monosite == false && dossierId == 0)
    {
        show_info_perdos('INFORMATION', 'IL FAUT CHOISIR UN SITE AVANT DE CREER UN DOSSIER', 'warning');
    }

    // else if(nomDossier !='' && dossierId ==0 && monosite == true)
    // else if(nomDossier !='' && dossierId == 0)
    else if(nomDossier != '')
    {
        var lien = Routing.generate('info_perdos_distance', {nomDossier: nomDossier, site: site, idDossier: dossierId});
        chargement = false;

        $.ajax({
            url: lien,
            dataType: 'json',
            async: false,
            success: function(data) {
                var listedossier = '';

                try {
                    $.each(data, function (key) {

                        if (listedossier == '') {

                            listedossier = listedossier + data[key].nom;
                        }
                        else {
                            listedossier = listedossier + ', ' + data[key].nom;
                        }

                    });
                }
                catch (e) {
                }

                if (listedossier != '') {

                    ret = false;

                    // show_info_perdos('ATTENTION', "Ce dossier existe dejà sous le nom de: '" + listedossier + "'. Veuillez choisir un autre nom", 'warning');

                    // $('#js_nom_dossier').val('');

                    var text = "Le(s) dossier(s) suivant(s) existe(nt) déjà: \n" +
                        listedossier +
                        ". \nClickez sur 'Confirmer' pour continuer avec: "+nomDossier.toUpperCase()+
                        ", sur 'Nouveau nom' pour saisir un nouveau nom";
                    swal({
                        title: 'Dossier(s) similaire(s) trouvé(s)',
                        text: text,
                        type: 'question',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Confirmer',
                        cancelButtonText: 'Nouveau nom'
                    }).then(function () {
                            ret = true;
                        },
                        function (dismiss) {
                            if (dismiss === 'cancel') {
                                ret = false;
                                $('#js_nom_dossier').val('');

                            } else {

                                throw dismiss;
                            }
                        }
                    ).catch(function(err) {
                        console.error(err);
                        throw err;
                    });
                }
            }

        });

    }

    chargement = true;

    return ret;

}

/**
 * Mi-verifier raha valide ny Siren/Siret
 * @returns {boolean}
 */
function verifierSirenSiret(isDossier) {
    var siren = $('#js_siren_siret').val().replace(/\s/g, "");

    //1: Verifier-na ny Siren/siret raha valide


    var lien = 'https://firmapi.com/api/v1/companies/' + siren;
    var formeJuridique = "";
    var activite = "";
    var dateDebutActivite = "";


    var formeJuridiqueId = -1;
    var dateDeb = -1;
    var codeApeId = -1;
    var raisonSocial = "";

    //2: Maka ny information avy any @ firmapi
    $.ajax({
        url: lien,
        type: 'GET',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            res = JSON.parse(data);


            if(isDossier) {

                $('#js_raison_social').val(res.company.names.denomination);

                $('#js_raison_social').attr('disabled', true);

                raisonSocial = res.company.names.denomination;

                formeJuridique = res.company.legal_form;
                activite = res.company.activity;

                dateDebutActivite = res.company.established_on;

                if (activite == null) {
                    activite = -1;
                }
                if (formeJuridique == null) {
                    formeJuridique = -1;
                }
                if (dateDebutActivite == null) {
                    dateDebutActivite = -1;
                }

                $('#js_code_ape').removeAttr('disabled');
                $('#js_intitule_code_ape').removeAttr('disabled');

                $('#js_code_ape').focus();

                $('#js_enseigne').val("");
                $('#js_tranche_effectif').val("");
                $('#js_num_rue').val("");
                $('#js_code_postal').val("");
                $('#js_pays').val("");
                $('#js_ville').val("");


                $('#js_enseigne').removeAttr('disabled');
                $('#js_tranche_effectif').removeAttr('disabled');
                $('#js_num_rue').removeAttr('disabled');
                $('#js_code_postal').removeAttr('disabled');
                $('#js_pays').removeAttr('disabled');
                $('#js_ville').removeAttr('disabled');
            }
            else{
                $('#js_aga_cga_num_rue').val("");
                $('#js_aga_cga_code_postal').val("");
                $('#js_aga_cga_pays').val("");
                $('#js_aga_cga_ville').val("");

                $('#js_aga_cga_num_rue').removeAttr('disabled');
                $('#js_aga_cga_code_postal').removeAttr('disabled');
                $('#js_aga_cga_pays').removeAttr('disabled');
                $('#js_aga_cga_ville').removeAttr('disabled');
            }


        },
        error: function (xhr) {
            formeJuridique = -1;
            activite = -1;
            var jsonResponse = '';
            try {
                jsonResponse = JSON.parse(xhr.responseText);
            }
            catch (err) {}

            if(isDossier) {
                $('#js_raison_social').val("");
                $('#js_forme_juridique').val("");
                $('#js_code_ape').val("");
                $('#js_intitule_code_ape').val("");
                $('#js_code_ape').removeAttr('data-id');
                $('#js_date_debut_activite').val("");


                $('#js_raison_social').removeAttr('disabled');
                $('#js_forme_juridique').removeAttr('disabled');
                $('#js_code_ape').removeAttr('disabled');
                $('#js_intitule_code_ape').removeAttr('disabled');
                $('#js_code_ape').removeAttr('disabled');
                $('#js_date_debut_activite').removeAttr('disabled');
            }

            // $('#js_enseigne').val("");
            // $('#js_tranche_effectif').val("");
            // $('#js_num_rue').val("");
            // $('#js_code_postal').val("");
            // $('#js_pays').val("");

            // $('#js_enseigne').removeAttr('disabled');
            // $('#js_tranche_effectif').removeAttr('disabled');
            // $('#js_num_rue').removeAttr('disabled');
            // $('#js_code_postal').removeAttr('disabled');
            // $('#js_pays').removeAttr('disabled');

            show_info_perdos('Information', jsonResponse.message, 'warning');
        }
    });

    if(isDossier) {

        //3: Mametaka ny info rehetra azo avy any @ firmapi: code ape, forme juridique, date debut activite
        $.ajax({
            url: Routing.generate('info_perdos_firmapi', {
                formeJuridique: formeJuridique,
                activite: activite,
                dateDebutActivite: dateDebutActivite
            }),
            type: 'GET',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            async: false,
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            success: function (data) {
                res = JSON.parse(data);

                if (res.formeJuridiqueId != null) {
                    // $('#js_forme_juridique').attr('disabled', true);

                    $('#js_forme_juridique').val(res.formeJuridiqueId);

                    formeJuridiqueId = res.formeJuridiqueId;
                }
                else {
                    $('#js_forme_juridique').val("");

                    $('#js_forme_juridique').removeAttr('disabled');
                }

                $('#js_code_ape').val("");
                $('#js_intitule_code_ape').val("");
                $('#js_code_ape').removeAttr('data-id');


                if (res.dateDeb != null) {

                    // $('#js_date_debut_activite').attr('disabled', true);

                    $('#js_date_debut_activite').val(res.dateDeb);

                    dateDeb = res.dateDeb;
                }
                else {
                    $('#js_date_debut_activite').val("");
                    $('#js_date_debut_activite').removeAttr('disabled');
                }


                setInputColor($('#js_raison_social'));
                setInputColor($('#js_forme_juridique'));
                setInputColor($('#js_code_ape'));
                setInputColor($('#js_intitule_code_ape'));
                setInputColor($('#js_date_debut_activite'));


                setDatePremiereCloture();

                // setInputColor($('#js_enseigne'));
                // setInputColor($('#js_tranche_effectif'));
                // setInputColor($('#js_num_rue'));
                // setInputColor($('#js_code_postal'));
                // setInputColor($('#js_pays'));


                setTabsInformation('tab-a-information-dossier', 0, $('[id^="js_form_info_"]'));


                saveIdentificationSiren(dossier_id, raisonSocial, formeJuridiqueId, codeApeId, dateDeb, '', '', '', '', '', '');


            }


        });
    }

    return true;
}

/**
 * Mi-activer ny tab Information dossier @ voalohany
 */
function setFirstTabs(dossierId,etape) {
    if (dossierId == 0) {


        // ******************* DEBUT MODIF 192.168.0.5 ******************* \\

        // $('#js_contenu_tabs ul  li').addClass("disabled");
        // $('.tab-a-instruction-dossier').removeClass("disabled");

        $('#js_contenu_tabs ul  li a').css({"border-bottom": "3px solid #cc5965"});

        // var tabInformation = $('.tab-a-information-dossier');
        //
        // if(tabInformation.hasClass('active')){
        //     tabInformation.removeClass('disabled');
        // }

        // ******************* FIN MODIF ******************* \\


        //Progress rehetra atao mena
        $('#tab-progression tbody tr:nth-child(1) td').addClass('progress-bar-danger');
        $('#tab-progression tbody tr:nth-child(1) td').attr('title','Il y a des champs obligatoires non renseignés pour cette étape');


        setTabs(etape, 0);


        if(etape[0].valide == 1){

            setInformationProgression(1,true);
        }
        else{
            setInformationProgression(1,false);
        }
    }
    else {

        //Verifier-na ny champ obligatoire par bloc
        // var instructionTousDossierForms = $('[id^="js_form_instr"]');
        // etape = isValideAllForm(instructionTousDossierForms, etape, 0);

        var identificationDossier = $('#js_form_info_identification_dossier');

        var inputs = identificationDossier.find('input');

        var selects = identificationDossier.find('select');

        inputs.each(function(){
            if($(this).attr('required') == 'required' &&  $(this).val() == ''){
                $(this).removeAttr('disabled');
            }
        });

        selects.each(function(){
            if($(this).attr('required') == 'required' &&  $(this).val() == ''){
                $(this).removeAttr('disabled');
            }
        });

        var informationDossierForms = $('[id^="js_form_info"]');
        etape = isValideAllForm(informationDossierForms, dossierId, etape, 1);

        if(etape[1].active == 1){
            $('.'+etape[1].tabClass).addClass('active');
        }

        var methodeComptableForms = $('[id^="js_form_meth"]');
        etape = isValideAllForm(methodeComptableForms, 0, etape, 2);

        var prestationDemandeForms = $('[id^="js_form_prest"]');
        etape = isValideAllForm(prestationDemandeForms, 0, etape, 3);

        var pieceAEnvoyerForms = $('[id^="js_form_envoi"]');
        etape = isValideAllForm(pieceAEnvoyerForms,0 , etape, 4);

        var i = 1;
        var valide =true;
        for(i; i<=4; i++) {
            if (etape[i].valide == 1 ) {
                setInformationProgression((i+1),true);
            }
            else {
                setInformationProgression((i+1),false);
                valide = false;
            }
        }

        if (etape[0].valide == -1) {
            setTabs(etape, 0);
        }
        else if (etape[1].valide == -1) {
            setTabs(etape, 1);
        }
        else if (etape[2].valide == -1) {
            setTabs(etape, 2);
        }
        else if (etape[3].valide == -1) {
            setTabs(etape, 3);
        }
        else if (etape[4].valide == -1){
            setTabs(etape, 4);
        }

    }

    //Tsy voakasika ny Tab Recap
    $('.tab-a-recap a').css({"border-bottom": ""});
    $('.tab-a-recap').removeClass("disabled");

    $('.tab-a-scriptura a').css({"border-bottom": ""});
    $('.tab-a-scriptura').removeClass("disabled");

    setFirstTabsInformation(etape);
}

/**
 * Mi-activer na mi-desactiver ny Tab
 * @param etape
 * @param etapeIndex
 * @returns {*}
 */
function setTabs(etape,etapeIndex) {
    var ei1 = etapeIndex + 1;

    //Mi-initialiser ny etape active na tsia
    if(etapeIndex<4) {
        //Valide = 1 valider ilay bloc
        if (etape[etapeIndex].valide == 1) {
            etape[etapeIndex + 1].active = 1;
        }
        else {

            for (ei1; ei1 < etape.length; ei1++) {
                etape[ei1].active = 0;
            }
        }
    }

    //Mi-activer na tsia ny tab tsirairay
    ei1 = etapeIndex + 1;
    for (ei1; ei1 < etape.length; ei1++) {
        if (etape[ei1].active == 0) {
            // ******************* DEBUT MODIF 192.168.0.5 ******************* \\

            // $('.' + etape[ei1].tabClass).addClass("disabled");

            // ******************* FIN MODIF ******************* \\

        }
        else {
            $('.' + etape[ei1].tabClass).removeClass("disabled");
        }
    }

    //Valide
    if(etape[etapeIndex].valide == 1){
        $('.' + etape[etapeIndex].tabClass).removeClass('active').find('a').css({"border-bottom": "3px solid #18a689"});
        $('.'+ etape[etapeIndex].tabClass).removeClass('active').find('span').attr('class','badge badge-primary');
        $('.'+ etape[etapeIndex].tabClass).removeClass('disabled');
    }
    //Misy erreur
    else if(etape[etapeIndex].valide == -1) {
        $('.' + etape[etapeIndex].tabClass).find('a').css({"border-bottom": "3px solid #cc5965"});
        $('.'+ etape[etapeIndex].tabClass).find('span').attr('class','badge badge-danger');
    }
    return etape;
}

/**
 * Mi-initialiser ny texte @ Tabs @ voalohany
 * @param etape
 */
function setFirstTabsInformation(etape) {
    var estValide = true;

    etape.forEach(function (item) {

        if (item['valide'] == 0 || item['valide'] == -1) {
            estValide = false;
        }
    });

    if (!estValide) {
        $('.tab-information').append('<p style="color: rgb(204, 89, 101);padding: 11px 0 13px 0;margin-bottom: 0;"><i class="fa fa-warning"></i>&nbsp;Saisie incomplète: Le dossier ne peut pas être créé</p>');
    }
    else {
        $('.tab-information').append('<p style="color: #18a689;padding: 11px 0 13px 0;margin-bottom: 0;"><i class="fa fa-check"></i>&nbsp;Saisie complète: Le dossier est créé</p>');
    }

}

/**
 * Mametraka message information raha feno ny information na tsia
 * @param tabclass
 * @param forms
 * @param valide
 */
// function setTabsInformation(tabclass,forms) {
function setTabsInformation(tabclass,valide,forms) {

    var estValide = verifierChampObligatoire(forms);

    var etapeValide = true;

    if(valide != 1){
        etapeValide = false;
    }

    if (estValide && etapeValide) {
        // $('.tab-information p').remove();
        // $('.tab-information').append('<p style="color: #18a689;padding: 11px 0 13px 0;margin-bottom: 0;"><i class="fa fa-check"></i>&nbsp;Saisie complète: Le dossier est créé</p>');

        $('.' + tabclass).find('a').css({"border-bottom": "3px solid #18a689"});
        $('.' + tabclass).find('span').attr('class', 'badge badge-primary');

        switch (tabclass) {
            case 'tab-a-instruction-dossier':
                setInformationProgression(1,true);
                break;

            case 'tab-a-information-dossier':
                setInformationProgression(2,true);
                break;

            case 'tab-a-methode-comptable':
                setInformationProgression(3,true);
                break;

            case 'tab-a-prestations-demandes':
                setInformationProgression(4,true);
                break;

            case 'tab-a-piece-envoyer':
                setInformationProgression(5,true);
                break;
        }

    }
    else {
        // $('.tab-information p').remove();
        // $('.tab-information').append('<p style="color: #cc5965;padding: 11px 0 13px 0;margin-bottom: 0;"><i class="fa fa-warning"></i>&nbsp;Saisie incomplète: Le dossier ne peut pas être créé</p>');

        $('.' + tabclass).find('a').css({"border-bottom": "3px solid #cc5965"});
        $('.' + tabclass).find('span').attr('class', 'badge badge-danger');


        switch (tabclass) {
            case 'tab-a-instruction-dossier':
                setInformationProgression(1,false);
                break;

            case 'tab-a-information-dossier':
                setInformationProgression(2,false);
                break;

            case 'tab-a-methode-comptable':
                setInformationProgression(3,false);
                break;

            case 'tab-a-prestations-demandes':
                setInformationProgression(4,false);
                break;

            case 'tab-a-piece-envoyer':
                setInformationProgression(5,false);
                break;
        }
    }
}

/**
 * Mametaka ny valeur-an'ny tva taux
 * @param dossierId
 */
function setTvaTauxDossierCombo(dossierId) {
    $.ajax({
        data: {dossier: dossierId},
        url: Routing.generate('info_perdos_tvatauxdossier'),
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            var res = Array.from(data);

            $('#js_tva_taux').val(res).trigger('chosen:updated');
        }
    });
}

/**
 * Mi-verifier ny champ obligatoire rehetra
 * @param forms
 * @returns {boolean}
 */
function verifierChampObligatoire(forms) {
    var canValide = true;

    // var dossierId = $('#dossier').val();

    $.each(forms, function (index, item) {

        var inputs = $(item).find('input');

        $.each(inputs, function (index, item) {
            var req = $(item).attr('required');
            var dis = $(item).attr('disabled');
            var val = $(item).val();
            // if (dis != 'disabled')
            {
                if (req == 'required' && val == '') {
                    canValide = false;
                    return false;
                }
            }
        });

        if (!canValide) {
            return false;
        }

        var selects = $(item).find('select');

        $.each(selects, function (index, item) {
            var req = $(item).attr('required');
            var dis = $(item).attr('disabled');
            var val = $(item).val();
            // if (dis != 'disabled')
            {
                if (req == 'required' && val == '') {
                    canValide = false;
                    return false;
                }
            }
        });

        if (!canValide) {
            return false;
        }
    });

    return canValide;
}

/**
 * Verification Siren/Siret infogreffe
 * @returns {boolean}
 */
function verifierSirenSiretV2(grid) {
    var representant = '{"Metadata":{"CreditsUsed":15,"CreditsLeft":468},"Data":{"LibelleGreffe":"BOBIGNY","CodeGreffe":"9301","Statut":"B","Denomination":"PROMULTITRAVAUX","Siren":"529209207","Representants":[{"Qualite":"PRESIDENT","Type":"PM","DenominationPM":"MULTIASSISTANCE - SOCIETE ANONYME","SirenPM":"413114901"},{"Qualite":"COMMISSAIRE AUX COMPTES TITULAIRE","Type":"PM","DenominationPM":"KPMG S.A - SOCIETE ANONYME","SirenPM":"775726417"},{"Qualite":"COMMISSAIRE AUX COMPTES SUPPLEANT","Type":"PM","DenominationPM":"SALUSTRO REYDEL - SOCIETE ANONYME","SirenPM":"652044371"}]}}';
    representant ='{"Metadata":{"CreditsUsed":15,"CreditsLeft":483},"Data":{"LibelleGreffe":"PARIS","CodeGreffe":"7501","Statut":"B","Denomination":"SCRIPTURA","Siren":"804823508","Representants":[{"Qualite":"GERANT ","Type":"PP","Nom":"CASTELLAN","Prenom":"PHILIPPE","NomUsage":null,"DateNaissance":"1949-01-31"}]}}';
    representant ='{"Metadata":{"CreditsUsed":15,"CreditsLeft":483},"Data":{"LibelleGreffe":"PARIS","CodeGreffe":"7501","Statut":"B","Denomination":"SCRIPTURA","Siren":"804823508","Representants":[{"Qualite":"GERANT ","Type":"PP","Nom":"CASTELLAN","Prenom":"PHILIPPE","NomUsage":null,"DateNaissance":"1949-01-31"},{"Qualite":"PRESIDENT","Type":"PP","Nom":"RAKOTO","Prenom":"BE","NomUsage":null,"DateNaissance":"1949-01-31"}]}}';
    var representantJson = JSON.parse(representant);

    // console.log(representantJson.Data.Representants);
    var listeRepresentant = representantJson.Data.Representants;

    $.each(listeRepresentant , function(i, val) {
        if(val['Type'] == 'PP'){
            var type = val['Qualite'];
            var qualite = '';
            if(type.toLowerCase().indexOf('gerant')){
                qualite = 'Gérant';
            }else if(type.toLowerCase().indexOf('president')){
                qualite = 'Président';
            }
            var newData = {"mandataire-mandataire":qualite,"mandataire-nom":val['Nom'], "mandataire-prenom": val['Prenom'], "action":'<i class="fa fa-save icon-action js-save-mandataire" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-mandataire" title="Supprimer"></i>'};
            grid.jqGrid('addRowData','new_row', newData, "first");
        }
    });
    return true;
}

function verifierCodeApe() {

    var lien = Routing.generate('info_perdos_verifier_code_ape');
    var codeApe = $('#js_code_ape').val();
    $.ajax({
        url: lien,
        type: 'POST',
        async: true,
        data: {codeApe: codeApe},
        success: function(data){
            $('#js_code_ape').attr('data-id', data.id);
            $('#js_code_ape').val(data.codeApe);
            $('#js_intitule_code_ape').val(data.intitule);

            if(data.id == 734){
                $('#js_intitule_code_ape').val('N/A');
            }

            setInputColor($('#js_code_ape'));
            setInputColor($('#js_intitule_code_ape'));

        }

    });
}

/**
 * Verification SIREN/SIREN Insee
 * @returns {boolean}
 */
function verifierSirenSiretInsee(dossierId, isDossier) {
    var siren = '';

    if(isDossier) {
        siren = $('#js_siren_siret').val().replace(/\s/g, "");
    }
    else{
        siren = $('#js_aga_cga_siren').val().replace(/\s/g, "");
    }
    //1: Verifier-na ny Siren/siret raha valide
    var estSiren = isSiren(siren);
    var estSiret = isSiret(siren);

    var returnfalse = false;

    var sirenvalide = true;

    if (!(estSiren || estSiret)) {
        show_info_perdos('Information', "Le SIREN/SIRET n'est pas valide", 'warning');
        sirenvalide = false;
        returnfalse = true;
    }


    if(isDossier) {

        //Verifier-na raha efa mi-existe any @ dossier hafa ny SIREN
        var estSirenValide = verifierSiren(siren, dossierId);

        if (!estSirenValide) {
            if (sirenvalide) {
                show_info_perdos('Information', "Ce Siren est déjà pris", 'warning');
            }
            returnfalse = true;
        }


        if (returnfalse == true) {

            $('#js_siren_siret').val('');

            $('#js_form_info_identification_dossier').valid();


            $('#js_forme_juridique').val("");
            $('#js_forme_juridique').removeAttr('disabled');

            //Ny CE & Autre ihany no afficher-na
            setFormeJuridique(false, isDossier);

            $('#js_raison_social').val("");
            $('#js_raison_social').removeAttr('disabled');

            $('#js_code_ape').val("");
            $('#js_intitule_code_ape').val("");
            $('#js_code_ape').removeAttr('data-id');
            $('#js_code_ape').removeAttr('disabled');

            $('#js_date_debut_activite').val("");
            $('#js_date_debut_activite').removeAttr('disabled');


            setInputColor($('#js_siren_siret'));
            setInputColor($('#js_raison_social'));
            setInputColor($('#js_forme_juridique'));
            setInputColor($('#js_code_ape'));
            setInputColor($('#js_intitule_code_ape'));
            setInputColor($('#js_date_debut_activite'));


            $('#js_enseigne').val("");
            $('#js_enseigne').removeAttr('disabled');
            $('#js_tranche_effectif').val("");
            $('#js_tranche_effectif').removeAttr('disabled');
            $('#js_num_rue').val("");
            $('#js_num_rue').removeAttr('disabled');
            $('#js_code_postal').val("");
            $('#js_code_postal').removeAttr('disabled');
            $('#js_pays').val("");
            $('#js_pays').removeAttr('disabled');
            $('#js_raison_social').val("");
            $('#js_raison_social').removeAttr('disabled');

            // $('#js_enseigne').removeAttr('disabled');
            // $('#js_tranche_effectif').removeAttr('disabled');
            // $('#js_num_rue').removeAttr('disabled');
            // $('#js_code_postal').removeAttr('disabled');
            // $('#js_pays').removeAttr('disabled');


            // setInputColor($('#js_enseigne'));
            // setInputColor($('#js_tranche_effectif'));
            // setInputColor($('#js_num_rue'));
            // setInputColor($('#js_code_postal'));
            // setInputColor($('#js_pays'));

            return false;
        }
    }
    else{
        $('#js_aga_cga_num_rue').val("");
        $('#js_aga_cga_num_rue').removeAttr('disabled');
        $('#js_aga_cga_pays').val("");
        $('#js_aga_cga_pays').removeAttr('disabled');
        $('#js_aga_cga_code_postal').val("")
        ;$('#js_aga_cga_code_postal').removeAttr('disabled');
        $('#js_aga_cga_pays').val("");
        $('#js_aga_cga_pays').removeAttr('disabled');
        $('#js_aga_cga_ville').val("");
        $('#js_aga_cga_ville').removeAttr('disabled');
    }


    // var formeJuridiqueId = -1;
    // var codeApeId = -1;
    // var dateDeb = -1;
    // var raisonSocial = "";
    //
    //
    // var enseigne = "";
    // var trancheEffectif ="";
    // var numRue = "";
    // var codePostal ="";
    // var pays = "";


    //Afficher-na daholo ny forme juridiques rehetra
    setFormeJuridique(true, isDossier);


    var baseOpendataSoft = -1;
    var baseinsee = -1;

    if(siren.length >= 14){
        baseOpendataSoft = setSiren('opendatasoft',siren,dossier_id, isDossier);

        if(baseOpendataSoft == -1){
            baseinsee = setSiren('insee', siren, dossier_id, isDossier);
        }
    }

    else {

        baseinsee = setSiren('insee', siren, dossier_id, isDossier);

        if (baseinsee == -1) {
            baseOpendataSoft = setSiren('opendatasoft', siren, dossier_id, isDossier);
        }

    }

    if(baseOpendataSoft == -1 && baseinsee == -1) {
        // Maka any @ FIRMAPI raha tsy mahita
        verifierSirenSiret(isDossier);
    }


    var formeJuridique = $('option:selected', $('#js_forme_juridique')).attr('data-code');
    setSirenSiret(formeJuridique);

    return true;
}

function setSiren(url,siren,dossierId, isDossier){

    var formeJuridiqueId = -1;
    var codeApeId = -1;
    var dateDeb = -1;
    var raisonSocial = "";


    var trouveSiren = -1;

    var enseigne = "";
    var trancheEffectif ="";
    var numRue = "";
    var codePostal ="";
    var pays = "";
    var ville = "";

    $.ajax({

        url: Routing.generate(url, {siren: siren}),
        type: 'GET',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('application/json;charset=utf-8');
        },
        success: function (data) {

            if (data != -1) {

                var res = data;

                if(isDossier) {

                    raisonSocial = res.raisonSocial;
                    $('#js_raison_social').val(res.raisonSocial);

                    if (res.formeJuridiqueId != null) {
                        // $('#js_forme_juridique').attr('disabled', true);
                        $('#js_forme_juridique').val(res.formeJuridiqueId);
                        formeJuridiqueId = res.formeJuridiqueId;
                    }
                    else {
                        $('#js_forme_juridique').val("");
                        $('#js_forme_juridique').removeAttr('disabled');
                    }

                    var formeJuridique = $('#js_forme_juridique option:selected').attr('data-code');
                    setTypeMandataire(formeJuridique);


                    if (res.codeApe != null) {
                        // $('#js_code_ape').attr('disabled', true);
                        $('#js_code_ape').val(res.codeApe);
                        $('#js_intitule_code_ape').val(res.codeApeLib);
                        // $('#js_intitule_code_ape').attr('disabled', true);
                        $('#js_code_ape').attr('data-id', res.codeApeId);

                        codeApeId = res.codeApeId;
                    }
                    else {
                        $('#js_code_ape').val("");
                        $('#js_intitule_code_ape').val("");
                        $('#js_code_ape').removeAttr('data-id');

                        $('#js_code_ape').removeAttr('disabled');
                    }

                    if (res.dateDeb != null) {

                        // $('#js_date_debut_activite').attr('disabled', true);

                        $('#js_date_debut_activite').val(res.dateDeb);

                        dateDeb = res.dateDeb;
                    }
                    else {
                        $('#js_date_debut_activite').val("");
                        $('#js_date_debut_activite').removeAttr('disabled');
                    }

                    if (res.numRue != null) {
                        numRue = res.numRue;

                        // $('#js_num_rue').attr('disabled', true);
                        $('#js_num_rue').val(res.numRue);
                    }
                    else {
                        $('#js_num_rue').removeAttr('disabled');
                        $('#js_num_rue').val("");
                    }

                    if (res.codePostal != null) {
                        codePostal = res.codePostal;

                        // $('#js_code_postal').attr('disabled', true);
                        $('#js_code_postal').val(res.codePostal);
                    }
                    else {
                        $('#js_code_postal').removeAttr('disabled');
                        $('#js_code_postal').val("");
                    }

                    if (res.pays != null) {
                        pays = res.pays;

                        // $('#js_pays').attr('disabled', true);
                        $('#js_pays').val(res.pays);
                    }
                    else {
                        $('#js_pays').removeAttr('disabled');
                        $('#js_pays').val("");
                    }

                    if (res.ville != null) {
                        ville = res.ville;

                        // $('#js_ville').attr('disabled', true);
                        $('#js_ville').val(res.ville);
                    }
                    else {
                        $('#js_ville').removeAttr('disabled');
                        $('#js_ville').val("");
                    }

                    if (res.trancheEffectif != null) {
                        trancheEffectif = res.trancheEffectif;

                        // $('#js_tranche_effectif').attr('disabled', true);
                        $('#js_tranche_effectif').val(res.trancheEffectif);
                    }
                    else {
                        $('#js_tranche_effectif').removeAttr('disabled');
                        $('#js_tranche_effectif').val("");
                    }


                    setDatePremiereCloture();

                    saveIdentificationSiren(dossierId, raisonSocial, formeJuridiqueId, codeApeId, dateDeb, enseigne, trancheEffectif, numRue, codePostal, pays, ville);

                    setInputColor($('#js_raison_social'));
                    setInputColor($('#js_forme_juridique'));
                    setInputColor($('#js_code_ape'));
                    setInputColor($('#js_intitule_code_ape'));
                    setInputColor($('#js_date_debut_activite'));



                }
                else{

                    if (res.numRue != null) {
                        numRue = res.numRue;

                        // $('#js_aga_cga_num_rue').attr('disabled', true);
                        $('#js_aga_cga_num_rue').val(res.numRue);
                    }
                    else {
                        $('#js_aga_cga_num_rue').removeAttr('disabled');
                        $('#js_aga_cga_num_rue').val("");
                    }

                    if (res.codePostal != null) {
                        codePostal = res.codePostal;

                        // $('#js_aga_cga_code_postal').attr('disabled', true);
                        $('#js_aga_cga_code_postal').val(res.codePostal);
                    }
                    else {
                        $('#js_aga_cga_code_postal').removeAttr('disabled');
                        $('#js_aga_cga_code_postal').val("");
                    }

                    if (res.pays != null) {
                        pays = res.pays;

                        // $('#js_aga_cga_pays').attr('disabled', true);
                        $('#js_aga_cga_pays').val(res.pays);
                    }
                    else {
                        $('#js_aga_cga_pays').removeAttr('disabled');
                        $('#js_aga_cga_pays').val("");
                    }

                    if (res.ville != null) {
                        ville = res.ville;

                        // $('#js_aga_cga_ville').attr('disabled', true);
                        $('#js_aga_cga_ville').val(res.ville);
                    }
                    else {
                        $('#js_aga_cga_ville').removeAttr('disabled');
                        $('#js_aga_cga_ville').val("");
                    }


                    saveAgaCgaSiren(dossierId, numRue, codePostal, pays, ville);


                }

                trouveSiren = 1;

            }

        }
    });

    return trouveSiren;
}

//Mijery raha efa misy rapprochement banque azy manokana ilay dossier
function withRappBanque(dossierId){
    var withrapp = false;

    $.ajax({
        data: {dossier: dossierId},
        url: Routing.generate('info_perdos_withRappBanque'),
        type: 'POST',
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            if (data == "1") {
                withrapp = true;
            }
        }
    });

    return withrapp;
}

//Mijery raha efa misy regle paiement azy manokana ilay dossier
function withReglePaiementDossier(dossierId){
    var withRegleP = false;

    $.ajax({
        data: {dossier: dossierId},
        url: Routing.generate('info_perdos_withReglePaiement'),
        type: 'POST',
        async: false,
        success: function(data){
            if(data == "1"){
                withRegleP = true;
            }
        }
    });

    return withRegleP;
}

function withResponsable(dossierId){
    var withres = false;
    $.ajax({
        data: {dossier: dossierId},
        url: Routing.generate('info_perdos_withResponsable'),
        type: 'POST',
        async: false,
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {

            var res = JSON.parse(data);

            var titre = $('#tab-info-generales div:nth-child(5) h5');
            titre.removeClass();
            if (data == "1") {
                withres = true;
                titre.text('Gestion du dossier');
            }
            else {
                titre.text('Gestion du dossier (Obligatoire)');
                titre.addClass('label-danger');
            }
        }
    });
    return withres;
}

function withTvaTaux(){

    var res = true;

    $('#js_tva_taux_chosen .chosen-choices').removeAttr('style');

    if($('#js_tva_regime option:selected').attr('data-code') !== 'CODE_NON_SOUMIS' &&
        $('#js_tva_regime option:selected').attr('data-code') !== 'CODE_FRANCHISE') {

        if ($('#js_tva_taux').val() == null) {


            $('#js_tva_taux_chosen .chosen-choices').css({border: "rgb(204, 89, 101) 1px solid"});

            res =false;
        }

    }
    return res;
}


/**
 * Mijery raha efa misy instruction dossier na tsia ilay dossier
 * @param clientId
 * @returns {number}
 */
function verifierInstructionDossier(clientId) {
    var res = 0;
    $.ajax({
        url: Routing.generate('info_perdos_verifier_instruction_dossier'),
        type: 'POST',
        data: {clientId: clientId},
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async: false,
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('application/json;charset=utf-8');
        },
        success: function (data) {

            if (data == 1) {
                setInformationProgression(1,true);

                $('.tab-a-instruction-dossier').find('a').css({"border-bottom": "3px solid #18a689"});
                $('.tab-a-instruction-dossier').find('span').attr('class', 'badge badge-primary');
                res = 1;
            }

            else {
                setInformationProgression(1,false);

                $('.tab-a-instruction-dossier').find('a').css({"border-bottom": "3px solid #cc5965"});
                $('.tab-a-instruction-dossier').find('span').attr('class', 'badge badge-danger');
            }
        }

    });

    return res;
}

function notificationCreationDossier(dossierId) {

    if(dossierId != "" && dossierId != 0) {
        $.ajax({
            url: Routing.generate('info_perdos_check_dossier', {json: 1}),
            type: 'POST',
            data: {dossierId: dossierId},
            async: false,
            success: function (data) {
                // console.log(data);
            }

        });
    }
}

function notificationModificationDossier(dossierId) {
    if (dossierId != "" && dossierId != 0) {
        $.ajax({
            url: Routing.generate('info_perdos_check_modif_dossier'),
            type: 'POST',
            data: {dossierId: dossierId},
            async: false,
            success: function (data) {
                // console.log(data);
            }
        });
    }
}

/**
 * Mi-desactiver/activer ny form rehetra ao @ identification dossier @ voalohany
 * @param enable
 */
function setIdentificationDossier(enable) {
    var forms = $('[id^="js_form_info"]');
    var btns = $('[id^="btn-validation-info"]');
    if (enable == true) {
        forms.each(function () {

            $(this).find('input').prop("disabled", false);
            $(this).find('select').prop("disabled", false);

        });
        btns.each(function () {
            $(this).prop("disabled", false);
        });

        $('#tab-info-generales .ibox').removeClass("border-bottom");
        $('#tab-info-generales .ibox-content').css({
            display: 'block'
        });

        $('#tab-info-generales').find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-up");

        setTouDevelopper(false,$('.developper .btn-default'));

    }
    else {

        forms.each(function () {
            $(this).find('input').prop("disabled", true);
            $(this).find('select').prop("disabled", true);
        });

        btns.each(function () {
            $(this).prop("disabled", true);
        });

        //Mi-cacher ny bloc rehetra
        // $('#tab-info-generales.ibox').removeClass("border-bottom").addClass("border-bottom");
        // $('#tab-info-generales .ibox-content').css({display: 'none'});
        // $('#tab-info-generales .ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-down");

        // $('#js_contenu_tabs .ibox').removeClass("border-bottom").addClass("border-bottom");
        // $('#js_contenu_tabs  .ibox-content').css({display: 'none'});
        // $('#js_contenu_tabs  .ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-down");



        setTouDevelopper(true,$('.developper .btn-default'));

        //Mi-afficher ny bloc identification dossier
        var rowIdentification  = $('#tab-info-generales .scroller div:nth-child(3)');

        rowIdentification.find('.ibox').removeClass("border-bottom");
        rowIdentification.find('.ibox-content').css({display: 'block'});
        rowIdentification.find('.ibox-title > div:nth-child(3) > a > i').removeClass().addClass("fa fa-chevron-up");


        $('#js_form_info_identification_dossier').find('input').prop('disabled',false);
        $('#js_form_info_identification_dossier').find('select').prop('disabled',false);

        $('#btn-validation-info-identification').prop('disabled',false);



    }

    // disableAPIInput();

    firstLoad();
}

function setButtonValiderDossierHeight(){
    $('.btn-dossier-validation').css({
        height: $('#tab-progression thead').height() + $('#tab-progression tbody').height()
    });
}

/**
 *
 * @param etape
 * @param valide
 */
function setInformationProgression(etape,valide){

    var td = $('#tab-progression tbody tr:nth-child(1) td:nth-child('+(etape)+')');
    td.removeClass();

    if(valide) {
        td.addClass('progress-bar-navy-light');
        td.attr('title','Etape valide');
    }
    else {
        td.addClass('progress-bar-danger');
        td.attr('title','Il y a des champs obligatoires non renseignés pour cette étape')
    }
}


/**
 *
 * @param etape
 * @param creation
 * @param dossierId
 * @returns {*}
 */
function setTabProgressBtnValidation(etape,creation,dossierId) {

    var btnValidationInstr = $('[id^="btn-validation-instr-methode-comptable"]');
    btnValidationInstr.each(function () {
        $(this).on('click', function () {
            var forms = $('[id^="js_form_instr_methode_comptable"]');
            var canValide = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValide = false;
                }
            });
            setTabInformationParForms(canValide, etape, 0, forms);
        });
    });

    var btnValidationInfo = $('[id^="btn-validation-info"]');
    btnValidationInfo.each(function () {
        $(this).on('click', function () {
            var forms = $('[id^="js_form_info"]');
            var canValide = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValide = false;
                }
            });

            //Raha création-na dossier dia mbola tsy valide aloha ilay dossier (mbola misy bloc tsy nofenoina)
            // if (creation) {
            //     canValide = false;
            // }

            //Mijery raha efa misy responsable dossier na tsia
            // var resp = withResponsable($('#js_dossier_id').val());
            // if (!resp)
            if (!withResp)
            {
                canValide = false;
            }

            if(!withTvaTaux()){
                canValide = false;
            }


            if(canValide) {

                var siren = $('#js_siren_siret').val().replace(/\s/g, "");

                var codeFormeJuridique = $('#js_forme_juridique option:selected').attr('data-code');

                var estSirenValide = false;

                if(codeFormeJuridique == 'CODE_AUTRE' || codeFormeJuridique == 'CODE_CE' ||
                    codeFormeJuridique == 'CODE_INDIVISION') {

                    // if(siren == "") {
                    estSirenValide = true;
                    // }
                }

                else {
                    estSirenValide = verifierSiren(siren, dossierId);
                }



                if (!estSirenValide) {
                    canValide = false;
                }
            }

            setTabInformationParForms(canValide, etape, 1, forms);


            //
            // var canValidePiece = false;
            // var formEnvoi = $('[id^="js_form_envoi"]');
            // var inputFiles = formEnvoi.find('input');
            // var trouveEnable = false;
            // inputFiles.each(function(){
            //
            //     var disabled = $(this).attr('disabled');
            //
            //     if (typeof disabled !== typeof undefined && disabled !== false && disabled !== "disabled") {
            //         trouveEnable = true;
            //         return false;
            //     }
            //
            // });
            //
            //
            // setTabInformationParForms(!trouveEnable, etape, 4, formEnvoi);

        });
    });

    var btnValidationMethCompta = $('[id^="btn-validation-meth"]');
    btnValidationMethCompta.each(function () {
        $(this).on('click', function () {
            var forms = $('[id^="js_form_meth"]');
            var canValide = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValide = false;
                }
            });

            setTabInformationParForms(canValide, etape, 2, forms);
        });
    });


    var btnValidationPrest = $('[id^="btn-validation-prest"]');
    btnValidationPrest.each(function () {
        $(this).on('click', function () {
            var forms = $('[id^="js_form_prest"]');
            var canValide = true;
            forms.each(function () {
                if (!($(this).valid())) {
                    canValide = false;
                }
            });

            setTabInformationParForms(canValide, etape, 3, forms)
        });
    });

    return etape;
}


function setTabInformationParForms(canValide, etape, etapeIndex, forms) {
    if (canValide) {
        etape[etapeIndex].valide = 1;
    }
    else {
        etape[etapeIndex].valide = -1;
    }

    etape = setTabs(etape, etapeIndex);
    // setTabsInformation(etape[etapeIndex].tabClass, forms);

    setTabsInformation(etape[etapeIndex].tabClass,etape[etapeIndex].valide, forms);

    $('.' + etape[etapeIndex].tabClass).addClass('active')
}

/**
 * Manova ny couleur raha valide ilay champ na tsia
 */
function setInputsColor() {

    $('#js_contenu_tabs input, #js_contenu_tabs select').each(
        function (index) {
            $(this).on('focusout change', function () {
                setInputColor($(this));
            });
        }
    );
}

function setInputColor(input) {
    if (input.attr('required')) {
        if (input.val() == '') {
            input.removeClass('valid');
            input.removeClass('error');
            input.addClass('error');
            input.css({'border-color': '#ed5565'});
        } else {
            input.removeClass('valid');
            input.removeClass('error');
            input.addClass('valid');
            input.css({'border-color': '#1bb394'});
            input.css({'border-color': '#1bb394'});
            removeChampObligatoireLabel(input);
        }
    }
    else {
        input.removeClass('valid');
        input.removeClass('error');
        input.removeAttr('style');
        removeChampObligatoireLabel(input);
    }
}

/**
 * Mi-initialiser ny couleur-n'n
 */
function setFirstLoadInputsColor() {

    $('#js_contenu_tabs input, #js_contenu_tabs select').each(
        function (index) {

            var input = $(this);
            if ($(this).attr('required')) {
                if ($(this).val() == '') {
                    $(this).removeClass('valid');
                    $(this).removeClass('error');
                    $(this).addClass('error');
                    $(this).css({'border-color': '#ed5565'});

                } else {
                    $(this).removeClass('valid');
                    $(this).removeClass('error');
                    $(this).addClass('valid');
                    $(this).css({'border-color': '#1bb394'});
                }
            }
        });

}

function changeClient(etape,gridWidth) {
    var clientId = $('#client').val();

    var instructionIsValide = verifierInstructionDossier(clientId);

    setInstructionDossier(clientId);

    if (instructionIsValide == 1) {
        etape[0].active = 1;
        etape[0].valide = 1;
        etape[1].active = 1;

        $('.tab-a-information-dossier').removeClass("disabled");

        $('#js_contenu_tabs a[href="#tab-info-generales"]').tab('show');

        setInformationProgression(1, true);

        $('#js_nom_dossier').focus();
    }
    else {
        etape[0].active = 1;
        etape[0].valide = -1;
        etape[1].active = 0;

        setInformationProgression(1, false);

        $('.tab-a-information-dossier').addClass("disabled");

        $('#js_contenu_tabs a[href="#tab-instruction-dossier"]').tab('show');

        $('#js_instr_rapprochement_banque').focus();
    }

    $('#dossier').val(0);

    dossier_id = 0;

    ready_inspinia();

    changeDossier(etape,gridWidth);

    $('.note-editable').css("cssText", "position: relative !important;");
    $('.note-editable').css("cssText", "padding-top: 20px !important;");

    return etape;
}

function changeDossier(etape,gridWidth) {
    setFirstLoadInputsColor();

    var lien = Routing.generate('info_perdos_caracteristique', {json: 1});

    var dossierOld = $('#dossier').val();

    $.ajax({
        data: {client: $('#client').val(), dossier: $('#dossier').val()},
        url: lien,
        type: 'POST',
        async: true,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {

            $('#js_contenu_tabs').html(data);

            ready_inspinia();
            // showGrids(dossierId, gridWidth);
            setDate();
            setButtonValiderDossierHeight();

            setScrollerHeigt();

            initAllFileInputs();
            disableEstEnvoye();

            setFileInputs();

            // enableFileAllInput($('#dossier').val());

            enableFileAllInput(dossierOld);

            addRequired();

            // setTvaTauxDossierCombo($('#dossier').val());

            setTvaTauxDossierCombo(dossierOld);

            //ProgressBar any @ fileinput
            hideProgessBar();

            etape[0].active = 1;

            var instructionIsValide = verifierInstructionDossier($('#client').val());

            if(instructionIsValide == 1){
                // $('.tab-a-information-dossier').removeClass('active').addClass('active');
                $('#js_contenu_tabs a[href="#tab-info-generales"]').tab('show');
            }else{
                // $('.tab-a-instruction-dossier').removeClass('active').addClass('active');
                $('#js_contenu_tabs a[href="#tab-instruction-dossier"]').tab('show');
            }

            setChks();



            var dossierTxt = $("#dossier option:selected").html();
            if (dossierTxt == "&nbsp;" || $('#dossier').val() == 0 || dossierTxt == undefined) {
                setIdentificationDossier(false);

                // setFirstTabs($('#dossier').val(), etape);

                setInstructionDossier($('#client').val());

                setFirstTabs(0,etape);


                setTabProgressBtnValidation(etape, true,0);

                dossier_id = 0;

                withResp = withResponsable(dossier_id);

            } else {

                dossier_id = dossierOld;
                withResp = withResponsable(dossier_id);

                setIdentificationDossier(true);
                showGrids($('#dossier').val(), gridWidth, etape);

                // firstLoad();

                setInstructionDossier($('#client').val());

                setFirstTabs($('#dossier').val(), etape);

                setTabProgressBtnValidation(etape, false, $('#dossier').val());

                setSummerNote($('#js_instruction_saisie'), true);


            }

            setSummerNote($('.js-instr-instruction'), true);

            withRapp = withRappBanque(dossier_id);
            withReglePaiement = withReglePaiementDossier(dossier_id);

            //
            // setFirstLoadInputsColor();
            setInputsColor();
            // disableAPIInput();

            setPrestationGestion();

            setChosen(dossier_id);

            withTvaTaux();

            $('.note-editable').css("cssText", "position: relative !important;");
            $('.note-editable').css("cssText", "padding-top: 20px !important;");

            $("#libelle-modele-achat, #libelle-achat").sortable({
                connectWith: ".connectedSortable"
            }).disableSelection();

            $("#libelle-modele-vente, #libelle-vente").sortable({
                connectWith: ".connectedSortable"
            }).disableSelection();

            $("#libelle-modele-banque, #libelle-banque").sortable({
                connectWith: ".connectedSortable"
            }).disableSelection();
        }
    });



    // intIdentificationDossier = setBlink(intIdentificationDossier, $('#js_form_info_identification_dossier'));
    // intCaracteristiqueDossier = setBlink(intCaracteristiqueDossier, $('#js_form_info_caracteristique_dossier'));
    // intDocumentsComptablesFiscaux = setBlink(intDocumentsComptablesFiscaux, $('#js_form_info_document_comptable_fiscaux'));
    // intDocumentJuridique = setBlink(intDocumentJuridique, $('#js_form_info_forme_juridique'));
}

function saveInstrMethodeComptableV2(field, value) {
    var clientId = $('#client').val();
    chargement = false;

    $.ajax({
        url: Routing.generate('info_perdos_instr_methode_comptable_v2'),
        type: 'POST',
        data: {
            clientId: clientId,
            field: field,
            value: value
        },
        success: function() {
            verifierInstructionDossier(clientId);
        }

    });

    chargement = true;
}

function saveInformationDossierV2(dossierId, field, value, gridWidth,etape) {
    var retDoss = [];
    var sites  =$('#site').find('option');
    var site;
    var siteText = '';

    //Monosite
    if(sites.size() == 2)
    {
        site = (sites[1]).value;
    }
    //Multisite
    else
    {
        site = $('#site').val();
    }


    siteText = $("#site option:selected").text();

    if(siteText !='Tous') {

        chargement = false;

        var lien = Routing.generate('info_perdos_information_dossier_edit_v2');
        $.ajax({

            data: {
                dossierId: dossierId,
                site: site,
                field: field,
                value: value
            },
            async: false,
            url: lien,
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function (jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function (data) {
                var res = JSON.parse(data);

                if (res.estInsere == 2) {

                    dossierId = res.idDossier;

                    retDoss = [dossierId, res.id, res.estInsere];
                }

                else if (res.estInsere == 1) {

                    charger_dossier_info_perdos();

                    dossierId = res.idDossier;

                    retDoss = [dossierId, res.id, res.estInsere];

                    //Mila recharger-na ilay jqGrid rehefa miova ny id an'ny dossier
                    $('#js_infoCarac_responsableDossier_liste').jqGrid('GridUnload');
                    $('#js_infoCarac_mandataire_liste').jqGrid('GridUnload');
                    $('#js_infoCarac_banque_liste').jqGrid('GridUnload');
                    $('#js_infoCarac_vehicule_liste').jqGrid('GridUnload');
                    showGrids(dossierId, gridWidth, etape);

                    setIdentificationDossier(true);

                    setTabProgressBtnValidation(etape, false, dossierId);
                }

                chargement = true;

            }

        });

        return retDoss;

    }

    retDoss = [0, 0, -1];

    return retDoss;
}

function saveReglePaiementV2(dossierId, field, value, type) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    chargement = false;

    $.ajax({
        url: Routing.generate('info_perdos_regle_paiement_edit_v2'),
        type: 'POST',
        data:{
            dossierId:dossierId,
            field:field,
            value:value,
            type:type
        },
        success: function(data){
            // console.log(data);
        }
    });

    chargement = true;

}

function saveMethodeComptableV2(dossierId, field, value){

    chargement = false;

    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    $.ajax({
        url: Routing.generate('info_perdos_methode_comptable_edit_v2'),
        type: 'POST',
        data:{
            dossierId:dossierId,
            field:field,
            value:value
        },
        success: function(data){
            // console.log(data);
        }
    });

    chargement = true;

}

function savePrestFiscalV2(dossierId, field, value) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    chargement = false;

    $.ajax({
        url: Routing.generate('info_perdos_prest_fiscal_edit_v2'),
        type: 'POST',
        data:{
            dossierId:dossierId,
            field:field,
            value:value
        },
        success: function(data){
            // console.log(data);
        }
    });

    chargement = true;

}

function savePrestGestionV2(dossierId, field, value) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    $.ajax({

        url: Routing.generate('info_perdos_prest_gestion_edit_v2'),
        type: 'POST',
        data:{
            dossierId:dossierId,
            field:field,
            value:value
        },
        success: function(data){
            console.log(data);
        }
    });

}

function savePrestJuridiqueV2(dossierId, field, value) {
    if(dossierId ==0)
    {
        show_info_perdos('ATTENTION', "LE DOSSIER N'EXISTE PAS ENCORE. VEUILLEZ CREER OU CHOISIR UN DOSSIER",'warning');
        return;
    }

    chargement = false;

    $.ajax({
        url: Routing.generate('info_perdos_prest_juridique_edit_v2'),
        type: 'POST',
        data:{
            dossierId:dossierId,
            field:field,
            value:value
        },

        success: function(data){
            console.log(data);
        }
    });

    chargement = true;

}

function saveAgaCgaSiren(dossierId, numRue, codePostal, pays, ville) {

    if (dossierId != "" && dossierId != null) {
        chargement = false;
        $.ajax({
            url: Routing.generate('info_perdos_aga_cga_siren_edit'),
            type: 'POST',
            data: {
                dossierId: dossierId,

                numRue:numRue,
                codePostal:codePostal,
                pays:pays,
                ville: ville
            },

            success: function (data) {
                console.log(data);
            }
        });
    }
    chargement = true;

}

function saveIdentificationSiren(dossierId,raisonSocial, formeJuridiqueId, codeApeId,dateDeb, enseigne, trancheEffectif, numRue, codePostal, pays, ville) {

    if (dossierId != "" && dossierId != null) {
        chargement = false;
        $.ajax({
            url: Routing.generate('info_perdos_information_dossier_siren_edit_v2'),
            type: 'POST',
            data: {
                dossierId: dossierId,
                raisonSocial: raisonSocial,
                formeJuridiqueId: formeJuridiqueId,
                codeApeId: codeApeId,
                dateDeb: dateDeb,

                enseigne:enseigne,
                trancheEffectif:trancheEffectif,
                numRue:numRue,
                codePostal:codePostal,
                pays:pays,
                ville: ville
            },

            success: function (data) {
                // console.log(data);
            }
        });
    }
    chargement = true;

}

function ExporterExcel() {

    var recap_grid = $('#js_recap_dossier_liste');

    var colNames = recap_grid.jqGrid("getGridParam", "colNames");
    $('#colNames').val(JSON.stringify(colNames));

    var groupHeader = recap_grid.jqGrid("getGridParam", "groupHeader");
    $("#groupHeader").val(JSON.stringify(groupHeader));

    var colModel = recap_grid.jqGrid("getGridParam", "colModel");
    $('#colModel').val(JSON.stringify(colModel));
    var rowData = recap_grid.jqGrid("getGridParam", "data");
    $('#rowData').val(JSON.stringify(rowData));
    var footerData = recap_grid.jqGrid("footerData");
    $('#footerData').val(JSON.stringify(footerData));


    var url_export = Routing.generate('info_perdos_recap_export');
    $('#form-export')
        .attr('action', url_export)
        .submit();
}

function setPrestationGestion(){
    $('#js_budget').attr('disabled', true);
    $('#js_type_tableau_bord').attr('disabled', true);
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
function dossierCellAttr(rowId, val, rawObject, cm, rdata) {

    if(val === '' || val === undefined || val === "&nbsp;" || val === null || val === '&#160;'){
        return ' style="background:#f8ac59;color:transparent;"';
    }

    if(val === 'NaN/NaN/NaN'){
        return ' style="color:transparent;"';
    }
    if(val === '.'){
        return ' style="color:transparent;"';
    }
}

function sweetAlert() {
    swal({
        title: "Attention",
        text: "Tous les renseignements obligatoires n'ont pas été renseignés. La création du dossier ne peut être validée sans ces renseignements",
        type: "warning",
        confirmButtonColor: '#d33'

    });
}

function checkDossier(dossierId) {
    $.ajax({
        url: Routing.generate('info_perdos_check_dossier'),
        type: 'POST',
        async: true,
        data: { dossierId: dossierId},
        success: function (data) {

            if(data == 1){
                console.log("Dossier validé");
            }
            else{
                console.log("Dossier en création");
            }
        }
    });
}

function setDatePremiereCloture(){
    try {
        var moisCloture = $('#js_mois_cloture').val();
        var dateCurrent = new Date();
        var y = dateCurrent.getFullYear();
        var dateNow = new Date(y, moisCloture, 0);

        var dateCloture = null;

        var dateClt = $('#js_date_cloture').val();
        if(dateClt === ''){
            dateCloture = new Date(y, moisCloture, 0);
        }
        else{
            var dateCltArray = dateClt.split('/');
            dateCloture = new Date(dateCltArray[2], dateCltArray[1], dateCltArray[0]);
        }

        var debutAct = $("#js_date_debut_activite").val().split("/");
        var dateDebutActivite = new Date(debutAct[2], debutAct[1] - 1, debutAct[0]);

        var monthsClotureDifference = monthDiff(dateDebutActivite, dateCloture);
        var monthsNowDifference = monthDiff(dateDebutActivite, dateNow);


        if(monthsNowDifference <= 23){
            setRequired($('#js_date_cloture'));
            $('#js_date_cloture').attr('required');
            $('#js_date_cloture').removeAttr("disabled");
        }
        else if (monthsNowDifference > 23){
            $('#js_date_cloture').removeAttr("required");
            removeRequiredText($('#js_date_cloture'));
            $('#js_date_cloture').prop('disabled', 'disabled');

            if(monthsClotureDifference > 23){
                $('#js_date_cloture').val("");
            }
        }


        // if (monthsClotureDifference<= 23) {
        //     setRequired($('#js_date_cloture'));
        //     $('#js_date_cloture').attr('required');
        //     $('#js_date_cloture').removeAttr("disabled");
        // }
        // else if (monthsClotureDifference > 23){
        //     $('#js_date_cloture').removeAttr("required");
        //     removeRequiredText($('#js_date_cloture'));
        //     $('#js_date_cloture').prop('disabled', 'disabled');
        //     $('#js_date_cloture').val("");
        //
        // }

        setInputColor($('#js_date_cloture'));
    }
    catch(e){

    }
}

function monthDiff(d1, d2) {
    var months;
    months = (d2.getFullYear() - d1.getFullYear()) * 12;
    months -= d1.getMonth() + 1;
    months += d2.getMonth();
    return months <= 0 ? 0 : months;
}

function sirenFormat(siren){
    var res = '';

    if(siren.length >= 9) {
        res = siren.substr(0, 3) + ' ' + siren.substr(3);
        res = res.substr(0,7) + ' ' + res.substr(7);

        if(siren.length >= 14){
            res = res.substr(0,11) + ' ' + res.substr(11);
        }
    }

    return res;
}

function show_info_perdos(titre, message, type) {
    type = typeof type === 'undefined' ? 'success' : type;
    setTimeout(function () {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 5000
        };
        if (type == 'success') toastr.success(message, titre);
        if (type == 'warning'){

            toastr.options = {
                closeButton: true,
                "positionClass": "toast-top-center",
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 5000
            };


            toastr.warning(message, titre);
        }
        if (type == 'error') toastr.error(message, titre);
        if (type == 'info') toastr.info(message, titre);
    }, 500);
}

function setFormeJuridique(sirenValide, isDossier){

    if(isDossier) {

        var lien = '';

        if (sirenValide) {
            lien = Routing.generate('info_perdos_formeJuridique', {json: 0, jqGrid: 0, withSiren: 1})
        }
        else {
            lien = Routing.generate('info_perdos_formeJuridique', {json: 0, jqGrid: 0, withSiren: 0})
        }

        $.ajax({
            url: lien,
            type: 'GET',
            async: false,
            success: function (data) {
                $('#js_forme_juridique option').remove();
                $('#js_forme_juridique').append(data);
            }

        });
    }

}

function setSirenSiret(formeJuridique){

    var sirenSiret = $('#js_siren_siret');

    if((formeJuridique == 'CODE_CE' || formeJuridique == 'CODE_AUTRE' || formeJuridique == 'CODE_INDIVISION') &&
        sirenSiret.val() == ''){
        sirenSiret.removeAttr('required');
        removeRequiredText(sirenSiret);
    }
    else{
        setRequired(sirenSiret)
    }

    setInputColor(sirenSiret);
}


function setSummerNote(selector, pj) {

    pj = typeof pj !== 'undefined' ? pj : false;

    if(!pj) {
        selector.summernote(
            {
                lang: 'fr-FR',
                focus: true,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['style']],
                    ['fontstyle', ['fontname', 'fontsize']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'unlink']]
                ],
                disableResizeEditor: true
            }
        );
    }
    else{

        var pjButton = function (context) {
            var ui = $.summernote.ui;

            // create button
            var button = ui.button({
                contents: '<i class="fa fa-chain"/> Pièce jointe',
                tooltip: 'Pièce jointe',
                click: function () {

                    var clientId = $('#client').val();
                    var dId = $('#dossier').val();

                    if(selector.hasClass('js-instr-instruction')){
                        dId = -1;
                        $('#js_instr_instruction_modal').modal('show');
                    }
                    else if(selector.attr('id') === 'js_instruction_saisie'){
                        clientId = -1;
                        $('#js_instruction_modal').modal('show');
                    }

                    //Chargement n'ny liste pièces jointes
                    reloadFichierList(dId, clientId);
                    var fichierInput = $('.js_instr_instruction_pj');

                    fichierInput.fileinput({
                        language: 'fr',
                        theme: 'fa',
                        uploadAsync: false,
                        showPreview: false,
                        showUpload: true,
                        showRemove: false,
                        showCancel: false,
                        uploadUrl: Routing.generate('info_perdos_piece_jointe_upload', {dossierId: dId, clientId: clientId}),
                        // uploadExtraData: function() {
                        //     return {
                        //         dossierId: dId,
                        //         clientId: clientId
                        //     };
                        // }
                    });

                    fichierInput.on('filebatchuploadcomplete', function(up, file, res) {
                        reloadFichierList(dId, clientId);
                    });

                    $('#js_instr_instruction_pj').on('fileuploaderror', function(event, data, msg) {
                        var form = data.form, files = data.files, extra = data.extra,
                            response = data.response, reader = data.reader;
                        console.log('File upload error');
                        alert(msg);
                    });

                    $(document).on('click', '.delete-file-pdf', function(){
                        $.ajax({
                            url: Routing.generate('info_perdos_piece_jointe_delete'),
                            type: 'DELETE',
                            data: {
                                dossierId: dId,
                                clientId: clientId,
                                fichierId: $(this).closest('li').attr('data-id')
                            },
                            datatype: 'html',
                            success: function(d){
                                reloadFichierList(dId, clientId);
                            }

                        });
                    });

                }

            });

            return button.render();   // return button as jquery object
        };


        selector.summernote(
            {
                lang: 'fr-FR',
                focus: true,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['style']],
                    ['fontstyle', ['fontname', 'fontsize']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'unlink']],
                    ['mybutton', ['pj']]

                ],

                buttons:{
                    pj: pjButton
                },
                disableResizeEditor: true

            }
        );
    }

    $('.note-statusbar').hide();
    // $('.note-editable').attr('style', 'padding-top: 30px !important');
}


function reloadFichierList(dId, clientId){
    $.ajax({
        url: Routing.generate('info_perdos_piece_jointe'),
        type: 'GET',
        data: {
            dossierId: dId,
            clientId: clientId
        },
        datatype: 'html',
        success: function(d){
            if(dId != -1)
                $('#fichier-dossier-list').html(d);
            else
                $('#fichier-client-list').html(d);
        }

    });
}

function saveInstructionSaisie(idDossier){

    var lien = Routing.generate('info_perdos_instruction_dossier_edit');
    var aHTML = $('#js_instruction_saisie').summernote('code');

    $.ajax({

        data:{
            dossierId:idDossier,
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
                show_info('SUCCES', 'AJOUR DE L\'INSTRUCTION BIEN ENREGISTREE');
            }

        }
    });
}


