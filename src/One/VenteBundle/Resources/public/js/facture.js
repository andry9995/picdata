/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des factures
 * @returns {undefined}
 */
function searchFacture() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListFacture();
    }
}

/**
 * Réinitialise la recherche des factures
 * @returns {undefined}
 */
function initSearchFacture() {
    initFilterQ();
    loadListFacture();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des factures
 * @returns {undefined}
 */
function loadListFacture() {
    var stat = $('#stat').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    var exercice = $('#exercice').val();

    resetTabContent();

    if(exercice === ''){
        showExerciceError();
    }

    $.ajax({
        url: Routing.generate('one_facture_list'),
        type: 'GET',
        dataType: 'html',
        data: {
            'stat': stat,
            'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod,
            'dossierId': dossierId,
            'exercice': exercice
        },
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-facture .panel-body').html(response);
            setFilterType('facture');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une facture
 * @returns {undefined}
 */
function loadNewFacture() {

    var exercice = $('#exercice').val();

    if(exercice === ''){
        showExerciceError();
        return false;
    }

    $.ajax({
        url: Routing.generate('one_facture_new'),
        type: 'GET',
        dataType: 'html',
        data: {
            'parent': getParent(),
            'parentid': getParentID(),
            'dossierId': $('#dossier').val(),
            'exercice' : $('#exercice').val()
        },
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListFacture();');
            
            if (parent !== '')
                updateAddressDevis();
            
            initDateField();

            $('#id-dossier').val($('#dossier').val());

        }
    });
}

/**
 * Charge le formulaire d'édition d'une facture
 * @param {id} id
 * @returns {undefined}
 */
function loadEditFacture(id, one) {

    var fromOneUp = 0;
    if(one === true || one === undefined){
        fromOneUp = 1;
    }

    $.ajax({
        url: Routing.generate('one_facture_edit', {'id': id, 'one': fromOneUp}),
        type: 'GET',
        data: {
            'dossierId':$('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);

            $('#id-dossier').val($('#dossier').val());
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+', '+ one +');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListFacture();');
            if(fromOneUp === 1) {
                updateAddressVente();
                updateAddressLivraisonVente();
                initDateField();
                updateAmountTTC();
            }
        }
    });
}

/**
 * Sauvegarde d'une facture
 * @returns {undefined}
 */
function saveFacture() {
    $('input:radio[name="status"]').removeAttr('disabled');

    var form = $('#facture-form');
    var clientProspectField = form.find('#client-prospect');

    if (validateField(clientProspectField)) {
        $.ajax({
            url: Routing.generate('one_facture_save'),
            type: 'POST',
            dateType: 'json',
            data: $('#facture-form, #paiement-form, #paiement-deleted-form').serialize(),
            success: function(response) {
                closeModal();
                //Si ajout
                if (response['action'] === 'add') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Ajout effectué', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Ajout non effectué', response['type']);
                    
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'projet')
                        loadShowProjet(getParentID());
                    else
                        loadListFacture();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'projet')
                        loadShowProjet(getParentID());
                    else
                        loadListFacture();
                }

                if(response['type'] !== 'error') {

                    //Génération PDF de la facture
                    generatePDF('facture', response['id']);

                    //Génération PDF des paiements
                    if (response['pids'].length > 0) {
                        for (var i = 0; i < response['pids'].length; i++) {
                            generatePDF('paiement', response['pids'][i]);
                        }
                    }
                }
            }
        }); 
    }
}

/**
 * Affichage d'une facture
 * @param {type} id
 * @returns {undefined}
 */
function showFacture(id) {
    $.ajax({
        url: Routing.generate('one_facture_show', {'id': id}),
        type: 'GET',
        data: {
            'dossierId': $('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();

        }
    });
}

/**
 * Envoi d'une facture
 * @param {type} id
 * @returns {undefined}
 */
function sendFacture(id) {
    $.ajax({
        url: Routing.generate('one_facture_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data:{'dossierId': $('#dossier').val()},
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

/**
 * Suppression d'une facture
 * @param {int} id
 * @returns {undefined}
 */
function deleteFacture(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Facture?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_facture_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre facture a bien été supprimée", response['type']);
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListFacture();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre facture ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs facture
 * @returns {undefined}
 */
function deleteSelectedFacture() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Factures qui sont utilisées autre part dans l'application, ne pourront pas être supprimées.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('input.element:checked');
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_facture_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre facture a bien été supprimée", response['type']);
                        if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListFacture();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre facture ne peut être supprimée car elle est encore référencée", response['type']);
                    }
                }
            });
        });
    });
}


/**
 * Récupération de l'adresse de vente
 * @param {string} type
 * @param {int} id
 * @returns {undefined}
 */
function getAddressVente(type, id) {
    if (type === 'clientprospect' && id === '') {
        $('#adresse-facturation').val('');
        return;
    }
    else if (type === 'contact' && id === '') {
        type = 'clientprospect';
        id = $('#client-prospect').val();
    }
    $.ajax({
        url: Routing.generate('one_vente_address'),
        type: 'GET',
        dateType: 'html',
        data: {'type': type, id: id},
        success: function(response) {
            var address = prettyAddress(response);
            $('#adresse-facturation').val(address);
        }
    });
}

function updateAddressVente() {
    var clientprospectID = $('#client-prospect').val();
    var contactID = $('#contact-client').val();
    if (contactID) {
        getAddressDevis('contact', contactID);
    } else {
        getAddressDevis('clientprospect', clientprospectID);
    }
}

/**
 * Récupération de l'adresse de livraison
 * @param {string} type
 * @param {int} id
 * @returns {undefined}
 */
function getAddressLivraisonVente(type, id) {
    if (type === 'clientprospect' && id === '') {
        $('#adresse-livraison').val('');
        return;
    }
    else if (type === 'contact' && id === '') {
        type = 'clientprospect';
        id = $('#client-prospect').val();
    }
    $.ajax({
        url: Routing.generate('one_vente_address'),
        type: 'GET',
        dateType: 'html',
        data: {'type': type, id: id},
        success: function(response) {
            var address = prettyAddress(response);
            $('#adresse-livraison').val(address);
        }
    });
}

function updateAddressLivraisonVente() {
    var clientprospectID = $('#client-prospect').val();
    var contactID = $('#contact-livraison').val();
    if (contactID) {
        getAddressLivraisonVente('contact', contactID);
    } else {
        getAddressLivraisonVente('clientprospect', clientprospectID);
    }
}

function showFirstPage() {
    var pagination = $('.pagination');
    pagination.find('.previous').addClass('disabled');
    pagination.find('.first').addClass('active');
    pagination.find('.next').removeClass('disabled');
    pagination.find('.second').removeClass('active');
    $('.first-page').removeClass('hidden');
    $('.second-page').addClass('hidden');
    $(':focus').blur();
}

function showSecondPage() {
    var clientField = $('#client-prospect');
    var amountField = $('#montant-ttc');
    var pagination = $('.pagination');
    
    if(validateField(clientField) && validateField(amountField)) {
        var total = amountField.val().replace(new RegExp(' ', "g"), '');
        var paid = $('.paid').val();
        var unpaid = total - paid;
        $('input.total').val(total);
        $('input.unpaid').val(unpaid);
        $('td.totalVisible').html(number_format(total, 2, ',', ' '));
        $('td.unpaidVisible').html(number_format(unpaid, 2, ',', ' '));
        $('#clientid').val($('#client-prospect').val());
        
        pagination.find('.next').addClass('disabled');
        pagination.find('.second').addClass('active');
        pagination.find('.previous').removeClass('disabled');
        pagination.find('.first').removeClass('active');
        
        $('.second-page').removeClass('hidden');
        $('.first-page').addClass('hidden');
        $(':focus').blur();
    }
}

function showPaiementModal(){
    var clientField = $('#client-prospect');
    var amountField = $('#montant-ttc');

    if(validateField(clientField) && validateField(amountField)) {
        var total = amountField.val().replace(new RegExp(' ', "g"), '');
        var paid = $('.paid').val();
        var unpaid = total - paid;
        $('input.total').val(total);
        $('input.unpaid').val(unpaid);
        $('td.totalVisible').html(number_format(total, 2, ',', ' '));
        $('td.unpaidVisible').html(number_format(unpaid, 2, ',', ' '));
        $('#clientid').val($('#client-prospect').val());


        if (!$('#paiement-modal').hasClass('in')) {

            $("#paiement-modal").modal({backdrop: 'static'});
            setModalDraggable();
        }
    }
}

function closePaiementModal(){
    if ($('#paiement-modal').hasClass('in')) {
        $('#paiement-modal').modal('hide');

        var montantPaiements = $('#paiement-form').find('.montant-paiement');
        var totalPaiements = 0;

        montantPaiements.each(function () {
            var montant = $(this).val();
            if(montant !== undefined){
                totalPaiements += parseFloat(montant.replace(/\s+/g, ''));
            }
        });

        var totalVisibles = $('.totalVisible');
        var total = 0;

        totalVisibles.each(function () {

            if($(this).html()  !== ''){
                total += parseFloat($(this).html().replace(/\s+/g, '').replace(',','.'));
            }
        });

        if(total <= totalPaiements){
            $("#status-payee").prop("checked", true);
        }
        else{
            $("#status-a-payer").prop("checked", true);
        }
    }
}