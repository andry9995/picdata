$(document).ready(function(){
    $(document).on('change', '#dossier', function(){
        loadListFournisseur();
    });
});

/**
 * Suppression d'un compte dupliqué
 * @returns {undefined}
 */
function deleteDuplicatedAccount() {
    var elID = $('#dp-to-remove').val();
    $('.duplicated-account tbody tr#'+elID).remove();
    $('#dp-to-remove').val('');

    var dpnb = $('.duplicated-account tbody tr').length;
    if (dpnb == 0) {
        $('.delete-duplicated').addClass('hidden');
    }
}

/**
 * Suppression d'un fournisseur
 * @param {int} id
 * @returns {undefined}
 */
function deleteFournisseur(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Fournisseur?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_fournisseur_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre fournisseur a bien été supprimé", response['type']);
                    loadListFournisseur();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre fournisseur ne peut être supprimé car il est encore référencé", response['type']);
                }
            }
        });
    });
}


/**
 * Suppression de plusieurs fournisseurs
 * @returns {undefined}
 */
function deleteSelectedFournisseur() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Fournisseurs qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#tab-fournisseur').find($('input.element:checked'));
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_fournisseur_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre fournisseur a bien été supprimé", response['type']);
                        loadListFournisseur();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre fournisseur ne peut être supprimé car il est encore référencé", response['type']);
                    }
                }
            });
        });
    });
}


/**
 * Duplication d'un compte fournisseur
 * @returns {undefined}
 */
function duplicateAccount() {
    var sform = $('#selected-form');
    var dform = $('#duplicated-form');
    var accountId = sform.find('#selected-account-id').val();
    var accountName = sform.find('#selected-account-name').val();
    var mode = sform.find('#mode').val();
    var newCode = sform.find('#code').val();
    var out = '';
    if ($('.selected-account tbody tr').length > 0) {
        if (mode === '0') {
            if (newCode !== '') {
                var value = 'id='+accountId+'&code='+newCode;
                out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
                out = out + '<td>'+newCode+'<input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
                out = out + '<td>'+accountName+'</td>';
                out = out + '</tr>';
            } else {
                var value = 'id='+accountId+'&code=';
                out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
                out = out + '<td><input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
                out = out + '<td>'+accountName+'</td>';
                out = out + '</tr>';
            }
        } else if (mode === '1') {
            var value = 'id='+accountId+'&code=';
            out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
            out = out + '<td><input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
            out = out + '<td>'+accountName+'</td>';
            out = out + '</tr>';
        }
        $('.duplicated-account tbody').append(out);
        $('.duplicated-account').removeClass('hidden');
    }
}

/**
 * Réinitialise la recherche des fournisseurs
 * @returns {undefined}
 */
function initSearchFournisseur() {
    initFilterQ();
    loadListFournisseur();
    $('.init-search').addClass('hidden');
}


/**
 * Charge le formualire d'édtion d'un fournisseur
 * @param {int} id : ID du fournisseur
 * @returns {undefined}
 */
function loadEditFournisseur(id) {

    $.ajax({
        url: Routing.generate('one_fournisseur_edit', {'id': id}),
        type: 'GET',
        data:{dossierId: $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#tab-fournisseur .panel-body').html(response);

            $('#id-dossier').val($('#dossier').val());
        }
    });
}


/**
 * Charge le formulaire de duplication
 * @returns {undefined}
 */
function loadDuplicateFournisseur() {
    $.ajax({
        url: Routing.generate('one_fournisseur_duplicate'),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-fournisseur .panel-body').html(response);
        }
    });
}

/**
 *
 */
function loadListFournisseur() {
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_fournisseur_list'),
        type: 'GET',
        dataType: 'html',
        data: {
            'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod,
            'dossierId': dossierId
        },
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-fournisseur .panel-body').html(response);
            setFilterType('fournisseur');
            setParent('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}


/**
 * Affichage des fournisseur dans un modal
 * @returns {undefined}
 */
function loadListFournisseurModal() {
    $.ajax({
        url: Routing.generate('one_fournisseur_mlist'),
        type: 'POST',
        data: {'dossierId': $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}


/**
 * Charge le formulaire d'ajout d'un fournisseur
 */
function loadNewFournisseur() {
    $.ajax({
        url: Routing.generate('one_fournisseur_new'),
        type: 'POST',
        data: {dossierId: $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#tab-fournisseur .panel-body').html(response);
        }
    });
}

/**
 * Charge les détails d'un fournisseur
 * @param {int} id : ID du fournisseur
 * @param one
 * @returns {undefined}
 */
function loadShowFournisseur(id, one) {
    if (getFilterType() === 'fournisseur')
        setFilterType('all');

    var fromOneUp = 0;
    if (one === true || one === undefined) {
        fromOneUp = 1;
    }


    var type = $('#type').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();

    $.ajax({
        url: Routing.generate('one_fournisseur_show', {id:id, one: fromOneUp}),
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
            'dossierId': dossierId
        },
        success: function(response) {
            $('#tab-fournisseur .panel-body').html(response);
            setParent('fournisseur', id);
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}




/**
 * Sauvegarde un ajout ou une modification d'un fournisseur
 */
function saveFournisseur() {
    var form = $('#fournisseur-form');
    var entrepriseField = form.find('#nom-entreprise');
    var nomField = form.find('#nom');
    var sirenField = form.find('#siret');
    var type = $('input[name="fournisseur-type"]:checked').val();
    var valid = false;

    $('#id-dossier').val($('#dossier').val());

    //Validation des champs requis
    if(type === '2') {
        if (validateField(entrepriseField) && validateField(sirenField)) {
            valid = true;
        }
    } else if(type === '1') {
        valid = validateField(nomField);
    }

    if(valid) {
        $.ajax({
            url: Routing.generate('one_fournisseur_save'),
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
                    loadShowFournisseur(response['id'], true);
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    loadShowFournisseur(response['id'], true);
                }
            }
        });
    } else {
        $('#toggle-advanced').click();
    }
}

/**
 * Recherche des fournisseurs
 * @returns {undefined}
 */
function searchFournisseur() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListFournisseur();
    }
}


/**
 * Séléction d'un compte fournisseur
 * @returns {undefined}
 */
function selectAccount() {
    var account = $('.account:checked:visible:first');
    if (account.val() !== 'undefined') {
        var values = account.val().split(';');
        var id = values[0];
        var code = values[1];
        var nom = values[2];
        var response = '';
        response = response + '<tr>';
        response = response + '<td>'+code+'</td>';
        response = response + '<td>'+nom+'</td>';
        response = response + '</tr>';
        $('#selected-account-id').val(id);
        $('#selected-account-code').val(code);
        $('#selected-account-name').val(nom);
        $('.selected-account tbody').html(response);
        closeModal();
    }
}


/**
 * Séléction d'un compte dupliqué
 * @param {dom} el
 * @returns {undefined}
 */
function selectDPToRemove(el) {
    var elID = $(el).attr('id');
    $('#dp-to-remove').val(elID);
    $('.delete-duplicated').removeClass('hidden');

    //Réinitialise
    $('.duplicated-account tbody tr').css('background', '#FFFFFF');
    $('.duplicated-account tbody tr').css('color', '#676a6c');

    $('.duplicated-account tbody tr#'+elID).css('background', '#1CB394');
    $('.duplicated-account tbody tr#'+elID).css('color', '#FFFFFF');

    $('.duplicated-account tbody tr input').css('color', '#676a6c');
}


/**
 * Affiche/Cache le champs code dans la duplication
 * @param {dom} el
 * @returns {undefined}
 */
function toggleCodeField(el) {
    if ($(el).val() === '0') {
        $('.code-field').removeClass('hidden');
    } else {
        $('.code-field').addClass('hidden');
        $('#code').val('');
    }
}


/**
 * Validation de la duplication
 * @returns {undefined}
 */
function validateDuplication() {
    var form = $('#duplicated-form');
    var values = form.find('.duplicated-values');
    if (values.length > 0) {
        $.ajax({
            url: Routing.generate('one_fournisseur_duplicate'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response['type'] === 'success')
                    show_info('Succès', 'Duplication effectuée', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Duplication non effectuée', response['type']);
                loadListFournisseur();
            }
        });
    }
}


/**
 *
 * @param fournisseurId
 */
function getListContacts(fournisseurId) {
    $.ajax({
        url: Routing.generate('one_fournisseur_list_contacts'),
        type: 'GET',
        dateType: 'html',
        data: {'fournisseur': fournisseurId},
        success: function(response) {
            $('#contact-fournisseur').html(response);
            $('#contact-livraison').html(response);
        }
    });
}


/**
 *
 * @param id
 * @param one
 */
function initSearchInFournisseur(id, one) {
    initFilterQ();
    loadShowFournisseur(id, one);
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'un client
 * @param {int} id d'un client
 * @param one
 * @returns {undefined}
 */
function searchInFournisseur(id, one) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowFournisseur(id, one);
    }
}
