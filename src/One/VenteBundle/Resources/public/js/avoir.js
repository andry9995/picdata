/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des avoirs
 * @returns {undefined}
 */
function searchAvoir() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListAvoir();
    }
}

/**
 * Réinitialise la recherche des avoirs
 * @returns {undefined}
 */
function initSearchAvoir() {
    initFilterQ();
    loadListAvoir();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des avoirs
 * @returns {undefined}
 */
function loadListAvoir() {
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
        url: Routing.generate('one_avoir_list'),
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
            $('#tab-avoir .panel-body').html(response);
            setFilterType('avoir');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une avoir
 * @returns {undefined}
 */
function loadNewAvoir() {

    var exercice = $('#exercice').val();

    if(exercice === ''){
        showExerciceError();
        return false;
    }

    $.ajax({
        url: Routing.generate('one_avoir_new'),
        type: 'GET',
        dataType: 'html',
        data: {
            'parent': getParent(),
            'parentid': getParentID(),
            'dossierId':$('#dossier').val(),
            'exercice': exercice
        },
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListAvoir();');
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'une avoir
 * @param {id} id
 * @returns {undefined}
 */
function loadEditAvoir(id) {
    $.ajax({
        url: Routing.generate('one_avoir_edit', {'id': id}),
        type: 'GET',
        data: {
            'dossierId': $('#dossier').val()
        },
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListAvoir();');
            
            updateAddressVente();
            updateAddressLivraisonVente();
            initDateField();
            updateAmountTTC();
        }
    });
}

/**
 * Sauvegarde d'une avoir
 * @returns {undefined}
 */
function saveAvoir() {
    var form = $('#avoir-form');
    var clientProspectField = form.find('#client-prospect');
    
    if (validateField(clientProspectField)) {
        $.ajax({
            url: Routing.generate('one_avoir_save'),
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
                    
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'projet')
                        loadShowProjet(getParentID());
                    else
                        loadListAvoir();
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
                        loadListAvoir();
                }
                
                //Génération PDF
                generatePDF('avoir', response['id']);
            }
        }); 
    }
}

/**
 * Affichage d'un avoir
 * @param {type} id
 * @returns {undefined}
 */
function showAvoir(id) {
    $.ajax({
        url: Routing.generate('one_avoir_show', {'id': id}),
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
 * Envoi d'un avoir
 * @param {type} id
 * @returns {undefined}
 */
function sendAvoir(id) {
    $.ajax({
        url: Routing.generate('one_avoir_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data: {'dossierId': $('#dossier').val()},
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

/**
 * Suppression d'une avoir
 * @param {int} id
 * @returns {undefined}
 */
function deleteAvoir(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Avoir?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_avoir_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre avoir a bien été supprimée", response['type']);
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListAvoir();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre devis ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs avoir
 * @returns {undefined}
 */
function deleteSelectedAvoir() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Avoirs qui sont utilisées autre part dans l'application, ne pourront pas être supprimées.",
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
                url: Routing.generate('one_avoir_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre avoir a bien été supprimée", response['type']);
                        if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListAvoir();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre avoir ne peut être supprimée car elle est encore référencée", response['type']);
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

/**
 * Chargement des avoir client dans le modal
 * @returns {undefined}
 */
function loadClientAvoir(fromFacture) {
    var facid = $('#facture-selected').val();
    var clientid = $('#clientid').val();
    var avoids = [];
    var items = $('.paiement-list .avoid');
    var dossierId = $('#dossier').val();
    var exercice = $('#exercice').val();

    var avoirModal = 0;
    if(fromFacture === true){
        avoirModal = 1;
    }

    items.each(function() {
        avoids.push($(this).val());
    });
    $.ajax({
        url: Routing.generate('one_avoir_client'),
        type: 'GET',
        dataType: 'html',
        data: {
            'facid': facid,
            'clientid': clientid,
            'excludeids': avoids,
            'dossierId':dossierId,
            'exercice': exercice,
            'avoirModal': avoirModal
        },
        success: function(response) {

            if(fromFacture === true){
                $('#avoir-modal').find('.modal-content').html(response);
                if (!$('#avoir-modal').hasClass('in')) {
                    $('#avoir-modal').modal({
                        backdrop: 'static'
                    });
                }

                $('#avoir-modal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('#paiement-modal').css('height', $('#facture-form').height());
                });
            }
            else {
                $('#primary-modal').find('.modal-content').html(response);
                openModal();
            }
        }
    });
}

/**
 * Sélection des avoirs pour les paiements
 * @returns {undefined}
 */
function selectAvoirPaiement(fromFacture) {
    var avoirs = $('.avo-item:checked');
    if (avoirs.length > 0) {
        avoirs.each(function() {
            var data = $(this).val();
            createAvoirPaiement(data);
        });

        if(fromFacture === true) {
            if ($('#avoir-modal').hasClass('in')) {
                $('#avoir-modal').modal('toggle');
            }
        }

        else{
            closeModal();
        }

    }
}
