/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des encaissements
 * @returns {undefined}
 */
function search() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListEncaissement();
    }
}

/**
 * Réinitialise la recherche des encaissements
 * @returns {undefined}
 */
function initSearchEncaissement() {
    initFilterQ();
    loadListEncaissement();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des encaissements
 * @returns {undefined}
 */
function loadListEncaissement() {
    var stat = $('#stat').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dosserId = $('#dossier').val();
    var exercice = $('#exercice').val();

    resetTabContent();

    if(exercice === ''){
        showExerciceError();
    }

    $.ajax({
        url: Routing.generate('one_encaissement_list'),
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
            'dossierId': dosserId,
            'exercice': exercice
        },
        success: function(response) {
            $('#tab-encaissement .panel-body').html(response);
            setFilterType('encaissement');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une encaissement
 * @returns {undefined}
 */
function loadNewEncaissement() {

    var exercice = $('#exercice').val();

    if(exercice === ''){
        showExerciceError();
        return false;
    }

    $.ajax({
        url: Routing.generate('one_encaissement_new'),
        type: 'GET',
        dataType: 'html',
        data: {
            'parent': getParent(),
            'parentid': getParentID(),
            'dossierId': $('#dossier').val(),
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
                $('.btn-back').attr('onclick', 'loadListEncaissement();');
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'un encaissement
 * @param {id} id
 * @returns {undefined}
 */
function loadEditEncaissement(id) {
    $.ajax({
        url: Routing.generate('one_encaissement_edit', {'id': id}),
        type: 'GET',
        data: {'dossierId':$('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListEncaissement();');
            
            changeType($('#type-encaissement').val());
            if ($('#type-encaissement').val() > 2) {
                updateMontant();
            }
            
            initDateField();
        }
    });
}

/**
 * Sauvegarde d'une encaissement
 * @returns {undefined}
 */
function saveEncaissement() {
    var form = $('#encaissement-form');
    var typeEncaissementField = form.find('#type-encaissement');
    var clientProspectField = form.find('#client-prospect');
    var montantField = form.find('#montant');
    
    if (validateField(typeEncaissementField) && validateField(clientProspectField) && validateField(montantField)) {
        $.ajax({
            url: Routing.generate('one_encaissement_save'),
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
                        loadListEncaissement();
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
                        loadListEncaissement();
                }
                
                //Génération PDF
                generatePDF('encaissement', response['id']);
            }
        }); 
    }
}

/**
 * Affichage d'un encaissement
 * @param {type} id
 * @returns {undefined}
 */
function showEncaissement(id) {
    $.ajax({
        url: Routing.generate('one_encaissement_show', {'id': id}),
        type: 'GET',
        data:{
            'dossierId': $('#dossier').val()
        },
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}

/**
 * Send d'un encaissement
 * @param {type} id
 * @returns {undefined}
 */
function sendEncaissement(id) {
    $.ajax({
        url: Routing.generate('one_encaissement_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

/**
 * Suppression d'un encaissement
 * @param {int} id
 * @returns {undefined}
 */
function deleteEncaissement(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Encaissement?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_encaissement_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre encaissement a bien été supprimée", response['type']);
                    if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListEncaissement();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre devis ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs encaissement
 * @returns {undefined}
 */
function deleteSelectedEncaissement() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Encaissements qui sont utilisées autre part dans l'application, ne pourront pas être supprimées.",
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
                url: Routing.generate('one_encaissement_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre encaissement a bien été supprimée", response['type']);
                        if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListEncaissement();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre encaissement ne peut être supprimée car elle est encore référencée", response['type']);
                    }
                }
            });
        });
    });
}

function changeType(value) {
    if (value === '1') {
        $('.acompte-type').removeClass('hidden');
        $('.saisie-vente-type').addClass('hidden');
        $('#montant').attr('readonly', false);
        $('#compte').attr('readonly', true);
    } else if (value === '2') {
        $('.acompte-type').addClass('hidden');
        $('.saisie-vente-type').addClass('hidden');
        $('#montant').attr('readonly', false);
    } else {
        $('.acompte-type').addClass('hidden');
        $('.saisie-vente-type').removeClass('hidden');
        $('#montant').attr('readonly', true);
        $('#compte').attr('readonly', false);
    }
}

function addNewCompte() {
    $.ajax({
        url: Routing.generate('one_compte_listoption'),
        type: 'POST',
        data: {'dossierId': $('#dossier').val()},
        dateType: 'json',
        success: function(response) {
            var out = '<tr id="'+moment().valueOf()+'" class="" onclick="selectCompte(this);">';
            
            out = out + '<td>';
            out = out + '<select class="form-control item-compte" onchange="updateMontant();">';
            out = out + response;
            out = out + '</select>';
            out = out + '</td>';
            
            out = out + '<td>';
            out = out + '<input type="text" class="form-control item-montant number" value="0" onchange="updateMontant();">';
            out = out + '<input type="hidden" class="form-control serialized" name="articles[]" value="" />';
            out = out + '</td>';
            
            out = out + '</tr>';
            
            $('.article-list tbody').append(out);
        }
    });
}

function selectCompte(el) {
    var uid = $(el).attr('id');
    var id = $(el).attr('class');
    $('#article-to-remove').val(uid+':'+id);

    //Réinitialise
    $('.article-list tbody tr').css('background', '#FFFFFF');
    $('.article-list tbody tr').css('color', '#676a6c');

    $('.article-list tbody tr#'+uid).css('background', '#1CB394');
    $('.article-list tbody tr#'+uid).css('color', '#FFFFFF');

    $('.article-list tbody tr input').css('color', '#676a6c');
    $('.article-list tbody tr textarea').css('color', '#676a6c');
    $('.article-list tbody tr select').css('color', '#676a6c');
}

function removeCompte() {
    var item = $('#article-to-remove').val();
    var uid = item.split(':')[0];
    var compteid = parseInt(item.split(':')[1]);
    if (compteid) {
        var output = '<input type="hidden" name="deleted-articles[]" value="'+compteid+'" />';
        $('#articles-deleted').append(output);
    }
    $('.article-list tbody tr#'+uid).remove();
    $('#article-to-remove').val('');
    updateMontant();
}

function updateMontant() {
    var total_amount = 0;
    var article_line = $('.article-list tbody').find('tr');
    article_line.each(function() {
        var compteid = $(this).find('.item-compte').val();
        var detailid = parseInt($(this).attr('class'));
        var montant = $(this).find('.item-montant').val().replace(new RegExp(' ', "g"), '');
        total_amount = parseFloat(total_amount) + parseFloat(montant);
        
        if (detailid)
            var serialized = 'id='+detailid+'&compte-id='+compteid+'&montant='+montant;
        else
            var serialized = 'id=&compte-id='+compteid+'&montant='+montant;
        $(this).find('.serialized').val(serialized);
    });
    $('#montant').val(formatValue(total_amount));
}

/**
 * Chargement des encaissements client dans le modal
 * @returns {undefined}
 */
function loadClientEncaissement() {
    var facid = $('#facture-selected').val();
    var clientid = $('#clientid').val();
    var encids = [];
    var items = $('.paiement-list .encid');
    items.each(function() {
        encids.push($(this).val());
    });
    var dossierId = $('#dossier').val();
    var exercice = $('#exercice').val();
    $.ajax({
        url: Routing.generate('one_encaissement_client'),
        type: 'GET',
        dataType: 'html',
        data: {
            'facid': facid,
            'clientid': clientid,
            'excludeids': encids,
            'dossierId': dossierId,
            'exercice': exercice
        },
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}

/**
 * Sélection des encaissements pour les paiements
 * @returns {undefined}
 */
function selecteClientEncaissement() {
    var encaissements = $('.enc-item:checked');
    if (encaissements.length > 0) {
        encaissements.each(function() {
            var data = $(this).val();
            createEncaissementPaiement(data);
        });
        closeModal();
    }
}