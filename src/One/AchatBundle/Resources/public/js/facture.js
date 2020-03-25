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
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_achat_facture_list'),
        type: 'GET',
        dataType: 'html',
        data: {'stat': stat, 'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId': dossierId},
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
    $.ajax({
        url: Routing.generate('one_achat_facture_edit'),
        type: 'GET',
        dataType: 'html',
        data: {'parent': getParent(), 'parentid': getParentID(), 'dossierId': $('#dossier').val()},
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);

            if (getParent() === 'fournisseur')
                $('.btn-back').attr('onclick', 'loadShowFournisseur('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListFacture();');

            initDateField();

            $('#id-dossier').val($('#dossier').val());

            updateAddressAchat();
            getListContacts($('#fournisseur').val());

        }
    });
}

/**
 * Charge le formulaire d'édition d'une facture
 * @param {id} id
 * @param one
 * @returns {undefined}
 */
function loadEditFacture(id, one) {

    var fromOneUp = 0;
    if(one === true || one === undefined){
        fromOneUp = 1;
    }

    $.ajax({
        url: Routing.generate('one_achat_facture_edit', {'id': id, 'one': fromOneUp}),
        type: 'GET',
        data: {'dossierId':$('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);

            $('#id-dossier').val($('#dossier').val());

            if (getParent() === 'fournisseur')
                $('.btn-back').attr('onclick', 'loadShowFournisseur('+getParentID()+', '+ one +');');
            else
                $('.btn-back').attr('onclick', 'loadListFacture();');
            if(fromOneUp === 1) {
                updateAddressAchat();
                // updateAddressLivraisonVente();
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
    var form = $('#facture-form');
    var fournisseurField = form.find('#fournisseur');

    if (validateField(fournisseurField)) {
        $.ajax({
            url: Routing.generate('one_achat_facture_save'),
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

                    if (getParent() === 'fournisseur')
                        loadShowFournisseur(getParentID(), true);
                   else
                        loadListFacture();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);

                    if (getParent() === 'fournisseur')
                        loadShowFournisseur(getParentID(), true);

                    else
                        loadListFacture();
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
        data: {'dossierId': $('#dossier').val()},
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
            url: Routing.generate('one_achat_facture_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre facture a bien été supprimée", response['type']);
                    if (getParent() === 'fournisseur')
                        loadShowFournisseur(getParentID());
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
 * Récupération de l'adresse du devis
 * @param {string} type
 * @param {int} id
 * @returns {undefined}
 */
function getAddressAchat(type, id) {
    if (type === 'fournisseur' && id === '') {
        $('#adresse-expedition').val('');
        return;
    }
    else if (type === 'contact' && id === '') {
        type = 'fournisseur';
        id = $('#fournisseur').val();
    }
    $.ajax({
        url: Routing.generate('one_achat_address'),
        type: 'GET',
        dateType: 'html',
        data: {'type': type, id: id},
        success: function(response) {
            var address = prettyAddress(response);
            $('#adresse-expedition').val(address);
        }
    });
}


function updateAddressAchat() {
    var fournisseurID = $('#fournisseur').val();
    var contactID = $('#contact-fournisseur').val();
    if (contactID) {
        getAddressAchat('contact', contactID);
    } else {
        getAddressAchat('fournisseur', fournisseurID);
    }
}
function updateAddressLivraisonVente() {
    var clientprospectID = $('#client-prospect').val();
    var contactID = $('#contact-livraison').val();
    if (contactID) {
        getAddressLivraisonVente('contact', contactID);
    } else {
        getAddressLivraisonVente('fournisseur', clientprospectID);
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

