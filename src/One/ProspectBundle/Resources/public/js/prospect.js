/**
 * Created by TEFY on 21/06/2017.
 */

/**
 * Recherche des prospects
 * @returns {undefined}
 */
function searchProspect() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListProspect();
    }
}

/**
 * Réinitialise la recherche des prospects
 * @returns {undefined}
 */
function initSearchProspect() {
    initFilterQ();
    loadListProspect();
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'un prospect
 * @param {int} id d'un prospect
 * @returns {undefined}
 */
function searchInProspect(id) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowProspect(id);
    }
}

/**
 * Réinitialise la recherche dans détail d'un prospect
 * @param {int} id d'un prospect
 * @returns {undefined}
 */
function initSearchInProspect(id) {
    initFilterQ();
    loadShowProspect(id);
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des prospects
 * @returns {undefined}
 */
function loadListProspect() {
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    var activityId = $('#activity').val();
    var archive = $('#archive').val();

    resetTabContent();
    $.ajax({
        url: Routing.generate('one_prospect_list'),
        type: 'GET',
        dataType: 'html',
        data: {
            'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod,
            'dossierId': dossierId,
            'activityId': activityId,
            'archive': archive
        },
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-prospect').find('.panel-body').html(response);
            setFilterType('prospect');
            setParent('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'un prospect
 */
function loadNewProspect() {
    $.ajax({
        url: Routing.generate('one_prospect_new'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#tab-prospect').find('.panel-body').html(response);
            setDate();
        }
    });
}

/**
 * Charge le formualire d'édtion d'un prospect
 * @param {int} id : ID du prospect
 * @returns {undefined}
 */
function loadEditProspect(id) {
    $.ajax({
        url: Routing.generate('one_prospect_edit', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-prospect').find('.panel-body').html(response);
            setDate();

        }
    });
}

/**
 * Charge les détails d'un prospect
 * @param {int} id : ID du prospect
 * @returns {undefined}
 */
function loadShowProspect(id) {
    if (getFilterType() === 'prospect') {
        setFilterType('all');
    }


            
    var type = $('#type').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    $.ajax({
        url: Routing.generate('one_prospect_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data: {
            'type': type,
            'q': q,
            'sort': sort,
            'sortorder': sortorder,
            'period': period,
            'startperiod': startperiod,
            'endperiod': endperiod
        },
        success: function(response) {
            $('#tab-prospect').find('.panel-body').html(response);

            if(type === 'opportunite' || type === 'tache' || type === 'appel'){
                $('#tab-prospects').find('a[href="#tab-prospect"]').tab('show')
            }



            setParent('prospect', id);
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Sauvegarde un ajout ou une modification d'un prospect
 */
function saveProspect() {
    var form = $('#prospect-form');
    var entrepriseField = form.find('#nom-entreprise');
    var nomField = form.find('#nom');
    var sirenField = form.find('#siret');
    var type = $('input[name="prospect-type"]:checked').val();
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
            url: Routing.generate('one_prospect_save'),
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
                    loadShowProspect(response['id']);
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    loadShowProspect(response['id']);
                }
            }
        }); 
    } else {
        $('#toggle-advanced').click();
    }
}

/**
 * Suppression d'un prospect
 * @param {int} id
 * @returns {undefined}
 */
function deleteProspect(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Prospect?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_prospect_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre prospect a bien été supprimé", response['type']);
                    loadListProspect();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre prospect ne peut être supprimé car il est encore référencé", response['type']);
                }
            }
        });
    });
}


/**
 * Archivage d'un prospect
 * @param {int} id
 * @param archive
 * @returns {undefined}
 */
function archiveProspect(id, archive) {
    var text = '';
    switch (parseInt(archive)){
        case 1:
            text = "Êtes-vous sûr de vouloir annuler l'archivage de ce Prospect?";
            break;
        case 0:
            text = "Êtes-vous sûr de vouloir archiver votre Prospect?";
            break;
    }

    swal({
        title: "Confirmation",
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_prospect_archive', {'id': id}),
            type: 'GET',
            dataType: 'json',
            data:{'archive': archive},
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre prospect a bien été archivé", response['type']);
                    loadListProspect();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre prospect ne peut être archiver", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs prospects
 * @returns {undefined}
 */
function deleteSelectedProspect() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Prospects qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#tab-prospect').find($('input.element:checked'));
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_prospect_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre prospect a bien été supprimé", response['type']);
                        loadListProspect();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre prospect ne peut être supprimé car il est encore référencé", response['type']);
                    }
                }
            });
        });
    });
}



/**
 * Suppression de plusieurs prospects
 * @returns {undefined}
 */
function archiveSelectedProspect(archive) {

    var text = '';
    switch (parseInt(archive)){
        case 1:
            text = "Voulez vous annuler l'archivage du prospect";
            break;
        case 0:
            text = "Attention, tous les Prospects ne s'afficheront plus dans l'application.";
            break;
    }

    swal({
        title: "Êtes-vous sûr?",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#tab-prospect').find($('input.element:checked'));
        checked.each(function () {
            $.ajax({
                url: Routing.generate('one_prospect_archive', {'id': $(this).val()}),
                type: 'GET',
                data: {'archive' : archive},
                dateType: 'json',
                success: function (response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre prospect a bien été archivé", response['type']);
                        loadListProspect();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre prospect ne peut être archivé", response['type']);
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
function loadDuplicateProspect() {
    $.ajax({
        url: Routing.generate('one_prospect_duplicate'),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-prospect .panel-body').html(response);
        }
    });
}

/**
 * Affichage des prospect dans un modal
 * @returns {undefined}
 */
function loadListProspectModal() {
    $.ajax({
        url: Routing.generate('one_prospect_mlist'),
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
 * Séléction d'un compte prospect
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
 * Duplication d'un compte prospect
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
            url: Routing.generate('one_prospect_duplicate', {dossierId: $('#dossier').val()}),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response['type'] === 'success')
                    show_info('Succès', 'Duplication effectuée', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Duplication non effectuée', response['type']);
                loadListProspect();
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


function setRecommandation(){
    var code_origine = $('#origine').find('option:selected').attr('data-code');
    var rec = $('.recommandation');
    if(code_origine === "CODE_RECOMMANDATION"){
        rec.each(function(){
            $(this).removeClass('hidden');
        });
    }
    else{
        rec.each(function(){
            if(!$(this).hasClass('hidden')){
                $(this).addClass('hidden');
            }
        })
    }
}


function setDate() {
    $('.date').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        todayBtn: "linked",
        language: "fr"
    });
}