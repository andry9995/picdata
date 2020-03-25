/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des projets
 * @returns {undefined}
 */
function searchProjet() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListProjet();
    }
}

/**
 * Réinitialise la recherche des projets
 * @returns {undefined}
 */
function initSearchProjet() {
    initFilterQ();
    loadListProjet();
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'un projet
 * @param {int} id d'un projet
 * @returns {undefined}
 */
function searchInProjet(id) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowProjet(id);
    }
}

/**
 * Réinitialise la recherche dans détail d'un projet
 * @param {int} id d'un projet
 * @returns {undefined}
 */
function initSearchInProjet(id) {
    initFilterQ();
    loadShowProjet(id);
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des projets
 * @returns {undefined}
 */
function loadListProjet() {
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_projet_list'),
        type: 'GET',
        dataType: 'html',
        data: {'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod},
        success: function(response) {
            $('#tab-projet .panel-body').html(response);
            setFilterType('projet');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une projet
 * @returns {undefined}
 */
function loadNewProjet() {
    $.ajax({
        url: Routing.generate('one_projet_new'),
        type: 'GET',
        dataType: 'html',
        data: {'parent': getParent(), 'parentid': getParentID(), 'dossierId':$('#dossier').val()},
        success: function(response) {
            $('#tab-projet .panel-body').html(response);
        }
    });
}

/**
 * Charge le formulaire d'édition d'une projet
 * @param {id} id
 * @returns {undefined}
 */
function loadEditProjet(id) {
    $.ajax({
        url: Routing.generate('one_projet_edit', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-projet .panel-body').html(response);
        }
    });
}

/**
 * Charge les détails d'un projet
 * @param {int} id : ID du projet
 * @returns {undefined}
 */
function loadShowProjet(id) {
    if (getFilterType() === 'projet')
        setFilterType('all');
    
    var type = $('#type').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    var exercice = $('#exercice').val();
    $.ajax({
        url: Routing.generate('one_projet_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data: {
            'type': type,
            'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod,
            'dossierId':dossierId,
            'exercice': exercice
        },
        success: function(response) {
            $('#tab-projet .panel-body').html(response);
            setParent('projet', id);
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Sauvegarde d'une projet
 * @returns {undefined}
 */
function saveProjet() {
    var form = $('#projet-form');
    var nomField = form.find('#nom');
    
    if (validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_projet_save'),
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
                    loadListProjet();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    loadListProjet();
                }
            }
        }); 
    }
}

/**
 * Suppression d'une projet
 * @param {int} id
 * @returns {undefined}
 */
function deleteProjet(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Projet?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_projet_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre projet a bien été supprimée", response['type']);
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListProjet();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre devis ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs projet
 * @returns {undefined}
 */
function deleteSelectedProjet() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Projets qui sont utilisées autre part dans l'application, ne pourront pas être supprimées.",
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
                url: Routing.generate('one_projet_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre projet a bien été supprimée", response['type']);
                        if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListProjet();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre projet ne peut être supprimée car elle est encore référencée", response['type']);
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