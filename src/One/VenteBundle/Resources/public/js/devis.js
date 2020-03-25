/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des devis
 * @returns {undefined}
 */
function searchDevis() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListDevis();
    }
}

/**
 * Réinitialise la recherche des devis
 * @returns {undefined}
 */
function initSearchDevis() {
    initFilterQ();
    loadListDevis();
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'une devis
 * @param {int} id d'un devis
 * @returns {undefined}
 */
function searchInDevis(id) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowDevis(id);
    }
}

/**
 * Réinitialise la recherche dans détail d'une devis
 * @param {int} id d'un devis
 * @returns {undefined}
 */
function initSearchInDevis(id) {
    initFilterQ();
    loadShowDevis(id);
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des devis
 * @returns {undefined}
 */
function loadListDevis() {
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
        url: Routing.generate('one_devis_list'),
        type: 'GET',
        dataType: 'html',
        data: {'stat': stat,
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
            $('#tab-devis .panel-body').html(response);
            setFilterType('devis');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une devis
 * @returns {undefined}
 */
function loadNewDevis() {

    var exercice = $('#exercice').val();

    if(exercice === ''){
        showExerciceError();
        return false;
    }

    $.ajax({
        url: Routing.generate('one_devis_new'),
        type: 'GET',
        dataType: 'html',
        data: {
            'parent': getParent(),
            'parentid': getParentID(),
            'dossierId':$('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'opportunite')
                $('.btn-back').attr('onclick', 'loadShowOpportunite('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListDevis();');
            
            if (parent !== '')
                updateAddressDevis();
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'un devis
 * @param {id} id
 * @returns {undefined}
 */
function loadEditDevis(id) {
    $.ajax({
        url: Routing.generate('one_devis_edit', {'id': id}),
        type: 'GET',
        data:{
            'dossierId':$('#dossier').val(),
            'exercice':$('#exercice').val()
        },
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListDevis();');
            
            updateAddressDevis();
            initDateField();
            updateAmountTTC();
        }
    });
}

/**
 * Sauvegarde d'un devis
 * @returns {undefined}
 */
function saveDevis() {
    var form = $('#devis-form');
    var clientProspectField = form.find('#client-prospect');
    var datedevisField = form.find('#date-devis');
    var finvaliditeField = form.find('#fin-validite');
    
    if (validateField(clientProspectField) && validateField(datedevisField) && validateField(finvaliditeField)) {
        $.ajax({
            url: Routing.generate('one_devis_save'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                closeModal();
                //Si ajout
                if (response['action'] === 'add') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Ajout effectué', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Ajout non effectué', response['type']);

                    if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListDevis();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        // show_info('Erreur', 'Modification non sauvegardée', response['type']);
                        show_info('Erreur', response['message'], response['type']);


                    if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListDevis();
                }

                if (response['type'] === 'success') {
                    //Génération PDF
                    generatePDF('devis', response['id']);
                }
            }
        });

    }
}

/**
 * Affichage d'un devis
 * @param {type} id
 * @returns {undefined}
 */
function showDevis(id) {
    $.ajax({
        url: Routing.generate('one_devis_show', {'id': id}),
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
 * Envoi d'un devis
 * @param {type} id
 * @returns {undefined}
 */
function sendDevis(id) {
    $.ajax({
        url: Routing.generate('one_devis_show', {'id': id}),
        type: 'GET',
        data: {'dossierId': $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

/**
 * Facturation d'un devis
 * @param {type} id
 * @returns {undefined}
 */
function invoiceDevis(id) {
    $.ajax({
        url: Routing.generate('one_devis_invoice', {'id': id}),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response['type'] === 'success')
                show_info('Succès', 'Votre devis a été facturé', response['type']);
            else if (response['type'] === 'error')
                show_info('Erreur', 'Votre devis n\'a pas pu être facturé', response['type']);
            
            loadListDevis();
            
            //Génération PDF
            generatePDF('facture', response['id']);
        }
    });
}

/**
 * Commande d'un devis
 * @param {type} id
 * @returns {undefined}
 */
function commandeDevis(id) {
    $.ajax({
        url: Routing.generate('one_devis_commande', {'id': id}),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response['type'] === 'success')
                show_info('Succès', 'Votre devis a été commandé', response['type']);
            else if (response['type'] === 'error')
                show_info('Erreur', 'Votre devis n\'a pas pu être commandé', response['type']);
            
            loadListDevis();
            
            //Génération PDF
            generatePDF('commande', response['id']);
        }
    });
}

/**
 * Suppression d'un devis
 * @param {int} id
 * @returns {undefined}
 */
function deleteDevis(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Devis?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_devis_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre devis a bien été supprimé", response['type']);
                    if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListDevis();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre devis ne peut être supprimée car il est encore référencé", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs devis
 * @returns {undefined}
 */
function deleteSelectedDevis() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Opportunités qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
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
                url: Routing.generate('one_devis_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre devis a bien été supprimée", response['type']);
                        if (getParent() === 'prospect')
                            loadShowProspect(getParentID());
                        else if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListDevis();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre devis ne peut être supprimée car elle est encore référencée", response['type']);
                    }
                }
            });
        });
    });
}

/**
 * Récupération de l'adresse du devis
 * @param {string} type
 * @param {int} id
 * @returns {undefined}
 */
function getAddressDevis(type, id) {
    if (type === 'clientprospect' && id === '') {
        $('#adresse-facturation').val('');
        return;
    }
    else if (type === 'contact' && id === '') {
        type = 'clientprospect';
        id = $('#client-prospect').val();
    }
    $.ajax({
        url: Routing.generate('one_devis_address'),
        type: 'GET',
        dateType: 'html',
        data: {'type': type, id: id},
        success: function(response) {
            var address = prettyAddress(response);
            $('#adresse-facturation').val(address);
        }
    });
}

function updateAddressDevis() {
    var clientprospectID = $('#client-prospect').val();
    var contactID = $('#contact-client').val();
    if (contactID) {
        getAddressDevis('contact', contactID);
    } else {
        getAddressDevis('clientprospect', clientprospectID);
    }
}