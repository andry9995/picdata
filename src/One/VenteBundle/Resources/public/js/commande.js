/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des commandes
 * @returns {undefined}
 */
function searchCommande() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListCommande();
    }
}

/**
 * Réinitialise la recherche des commandes
 * @returns {undefined}
 */
function initSearchCommande() {
    initFilterQ();
    loadListCommande();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des commandes
 * @returns {undefined}
 */
function loadListCommande() {
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
        url: Routing.generate('one_commande_list'),
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
            $('#tab-commande .panel-body').html(response);
            setFilterType('commande');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une commande
 * @returns {undefined}
 */
function loadNewCommande() {

    var exercice = $('#exercice').val();

    if(exercice === ''){
        showExerciceError();
        return false;
    }

    $.ajax({
        url: Routing.generate('one_commande_new'),
        type: 'GET',
        dataType: 'html',
        data: {
            'parent': getParent(),
            'parentid': getParentID(),
            'dossierId': $('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListCommande();');
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'une commande
 * @param {id} id
 * @returns {undefined}
 */
function loadEditCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_edit', {'id': id}),
        type: 'GET',
        data: {
            'dossierId': $('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListCommande();');
            
            updateAddressVente();
            updateAddressLivraisonVente();
            initDateField();
            updateAmountTTC();
        }
    });
}

/**
 * Facturation d'une commande
 * @param {type} id
 * @returns {undefined}
 */
function invoiceCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_invoice', {'id': id}),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response['type'] === 'success')
                show_info('Succès', 'Votre commande a été facturée', response['type']);
            else if (response['type'] === 'error')
                show_info('Erreur', 'Votre commande n\'a pas pu être facturée', response['type']);
            
            loadListCommande();
            
            //Génération PDF
            generatePDF('facture', response['id']);
        }
    });
}

/**
 * Livraison d'une commande
 * @param {type} id
 * @returns {undefined}
 */
function shipCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_ship', {'id': id}),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response['type'] === 'success')
                show_info('Succès', 'Votre bon de livraison a été généré', response['type']);
            else if (response['type'] === 'error')
                show_info('Erreur', 'Votre bon de livraison n\'a pas été généré', response['type']);
            
            loadListCommande();
            
            //Génération PDF
            generatePDF('livraison', response['id']);
        }
    });
}

/**
 * Sauvegarde d'une commande
 * @returns {undefined}
 */
function saveCommande() {
    var form = $('#commande-form');
    var clientProspectField = form.find('#client-prospect');
    
    if (validateField(clientProspectField)) {
        $.ajax({
            url: Routing.generate('one_commande_save'),
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
                    else
                        loadListCommande();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListCommande();
                }
                
                //Génération PDF
                generatePDF('commande', response['id']);
            }
        }); 
    }
}

/**
 * Affichage d'une commande
 * @param {type} id
 * @returns {undefined}
 */
function showCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_show', {'id': id}),
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
 * Affichage d'un bon de livraison
 * @param {type} id
 * @returns {undefined}
 */
function showShippedCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_show_shipped', {'id': id}),
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
 * Envoi d'une commande
 * @param {type} id
 * @returns {undefined}
 */
function sendCommande(id) {
    $.ajax({
        url: Routing.generate('one_commande_show', {'id': id}),
        type: 'GET',
        data: {'dossierId':$('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

/**
 * Suppression d'une commande
 * @param {int} id
 * @returns {undefined}
 */
function deleteCommande(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Commande?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_commande_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre commande a bien été supprimée", response['type']);
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListCommande();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre commande ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs commande
 * @returns {undefined}
 */
function deleteSelectedCommande() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, toutes les Commandes qui sont utilisées autre part dans l'application, ne pourront pas être supprimées.",
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
                url: Routing.generate('one_commande_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre commande a bien été supprimée", response['type']);
                        if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListCommande();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre commande ne peut être supprimée car elle est encore référencée", response['type']);
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