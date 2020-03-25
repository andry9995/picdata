/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */


/**
 * Recherche des clients
 * @returns {undefined}
 */
function searchClient() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListClient();
    }
}

/**
 * Réinitialise la recherche des clients
 * @returns {undefined}
 */
function initSearchClient() {
    initFilterQ();
    loadListClient();
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'un client
 * @param {int} id d'un client
 * @returns {undefined}
 */
function searchInClient(id) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowClient(id);
    }
}

/**
 * Réinitialise la recherche dans détail d'un client
 * @param {int} id d'un client
 * @returns {undefined}
 */
function initSearchInClient(id) {
    initFilterQ();
    loadShowClient(id);
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des clients
 * @returns {undefined}
 */
function loadListClient() {
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_client_list'),
        type: 'GET',
        dataType: 'html',
        data: {'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod,
            'dossierId': dossierId
        },
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-client .panel-body').html(response);
            setFilterType('client');
            setParent('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'un client
 */
function loadNewClient() {
    $.ajax({
        url: Routing.generate('one_client_new'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#tab-client .panel-body').html(response);
        }
    });
}

/**
 * Charge le formualire d'édtion d'un client
 * @param {int} id : ID du client
 * @returns {undefined}
 */
function loadEditClient(id) {

    $.ajax({
        url: Routing.generate('one_client_edit', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-client .panel-body').html(response);

            $('#id-dossier').val($('#dossier').val());
        }
    });
}

/**
 * Charge les détails d'un client
 * @param {int} id : ID du client
 * @returns {undefined}
 */
function loadShowClient(id) {
    if (getFilterType() === 'client')
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
        url: Routing.generate('one_client_show', {id:id}),
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
            'dossierId': dossierId,
            'exercice': exercice
        },
        success: function(response) {
            $('#tab-client').find('.panel-body').html(response);

            if (type === 'devis' || type === 'facture' || type === 'commande' ||
                type === 'paiement' || type === 'encaissement' || type === 'avoir') {
                $('#tab-ventes').find('a[href="#tab-client"]').tab('show');
            }

            setParent('client', id);
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le balance d'un client
 * @param {int} id : ID du client
 * @returns {undefined}
 */
function loadBalanceClient(id) {
    $.ajax({
        url: Routing.generate('one_client_balance', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data: {
            'dossierId': $('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        success: function(response) {
            $('#tab-client .panel-body').html(response);
        }
    });
}

/**
 * Sauvegarde un ajout ou une modification d'un client
 */
function saveClient() {
    var form = $('#client-form');
    var entrepriseField = form.find('#nom-entreprise');
    var nomField = form.find('#nom');
    var sirenField = form.find('#siret');
    var type = $('input[name="client-type"]:checked').val();
    var valid = false;

    $('#id-dossier').val($('#dossier').val());
    
    //Validation des champs requis
    if(type === '2') {
        if (validateField(entrepriseField)) {
            valid = true;
        }
    } else if(type === '1') {
        valid = validateField(nomField);
    }
    
    if(valid) {
       $.ajax({
            url: Routing.generate('one_client_save'),
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
                    loadShowClient(response['id']);
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    loadShowClient(response['id']);
                }
            }
        }); 
    } else {
        $('#toggle-advanced').click();
    }
}

/**
 * Suppression d'un client
 * @param {int} id
 * @returns {undefined}
 */
function deleteClient(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Client?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_client_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre client a bien été supprimé", response['type']);
                    loadListClient();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre client ne peut être supprimé car il est encore référencé", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs clients
 * @returns {undefined}
 */
function deleteSelectedClient() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Clients qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#tab-client').find($('input.element:checked'));
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_client_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre client a bien été supprimé", response['type']);
                        loadListClient();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre client ne peut être supprimé car il est encore référencé", response['type']);
                    }
                }
            });
        });
    });
}

/**
 * Charge le formulaire de duplication
 * @returns {undefined}
 */
function loadDuplicateClient() {
    $.ajax({
        url: Routing.generate('one_client_duplicate'),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-client .panel-body').html(response);
        }
    });
}

/**
 * Affichage des client dans un modal
 * @returns {undefined}
 */
function loadListClientModal() {
    $.ajax({
        url: Routing.generate('one_client_mlist'),
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
 * Séléction d'un compte client
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
 * Duplication d'un compte client
 * @returns {undefined}
 */
function duplicateAccount() {
    var sform = $('#selected-form');
    var dform = $('#duplicated-form');
    var accountId = sform.find('#selected-account-id').val();
    var accountName = sform.find('#selected-account-name').val();
    var mode = sform.find('#mode').val();
    var newCode = sform.find('#code').val();
    var dossierId = $('#dossier').val();

    var out = '';
    if ($('.selected-account tbody tr').length > 0) {
        if (mode === '0') {
            if (newCode !== '') {
                var value = 'id='+accountId+'&code='+newCode;
                out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
                out = out + '<td>'+newCode+'<input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
                out = out + '<td>'+accountName+'</td>';
                out = out + '<td><input type="hidden" name="id-dossier" value="'+dossierId+'"></td>';
                out = out + '</tr>';
            } else {
                var value = 'id='+accountId+'&code=';
                out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
                out = out + '<td><input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
                out = out + '<td>'+accountName+'</td>';
                out = out + '<td><input type="hidden" name="id-dossier" value="'+dossierId+'"></td>';
                out = out + '</tr>';
            }
        } else if (mode === '1') {
            var value = 'id='+accountId+'&code=';
            out = out + '<tr id="'+moment().valueOf()+'" onclick="selectDPToRemove(this);" style="cursor:pointer">';
            out = out + '<td><input type="hidden" class="duplicated-values" name="duplicated[]" value="'+value+'" /></td>';
            out = out + '<td>'+accountName+'</td>';
            out = out + '<td><input type="hidden" name="id-dossier" value="'+dossierId+'"></td>';
            out = out + '</tr>';
        }
        $('.duplicated-account tbody').append(out);
        $('.duplicated-account').removeClass('hidden');
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
 * Validation de la duplication
 * @returns {undefined}
 */
function validateDuplication() {
    var form = $('#duplicated-form');
    var values = form.find('.duplicated-values');
    if (values.length > 0) {
        $.ajax({
            url: Routing.generate('one_client_duplicate'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response['type'] === 'success')
                    show_info('Succès', 'Duplication effectuée', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Duplication non effectuée', response['type']);
                loadListClient();
            }
        }); 
    }
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

function loadProspectToClient() {
    $.ajax({
        url: Routing.generate('one_prospect_to_client'),
        type: 'GET',
        data: {'dossierId': $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#tab-client .panel-body').html(response);
        }
    });
}

function processProspectToClient() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Prospects sélectionnés seront transformés en Clients.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, transformer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#tab-client').find($('input.element:checked'));
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_prospect_to_client'),
                type: 'POST',
                dateType: 'json',
                data: { 'id': $(this).val() },
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Transformé!", "Votre prospect est transformé en client", response['type']);
                        loadListClient();
                    } else if (response['type'] === 'error') {
                        show_info("Non transformé!", "Votre prospect n'a pas été transformé en client", response['type']);
                    }
                    loadListClient();
                }
            });
        });
    });
}