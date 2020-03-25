/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des paiements
 * @returns {undefined}
 */
function searchPaiement() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListPaiement();
    }
}

/**
 * Réinitialise la recherche des paiements
 * @returns {undefined}
 */
function initSearchPaiement() {
    initFilterQ();
    loadListPaiement();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des paiements
 * @returns {undefined}
 */
function loadListPaiement() {
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
        url: Routing.generate('one_paiement_list'),
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
            $('#tab-paiement .panel-body').html(response);
            setFilterType('paiement');
            setParent('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire de paiement
 * @returns {undefined}
 */
function loadGetPaiement() {
    $.ajax({
        url: Routing.generate('one_paiement_get'),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#tab-paiement .panel-body').html(response);
            initDateField();
        }
    });
}

/**
 * Charge la liste des factures non payées
 * @returns {undefined}
 */
function loadUnpaidFacture() {
    $.ajax({
        url: Routing.generate('one_facture_unpaid_list'),
        type: 'GET',
        dataType: 'html',
        data: {
            'startperiod': $('#date-debut').val(),
            'endperiod': $('#date-fin').val(),
            'dossierId': $('#dossier').val(),
            'exercice': $('#exercice').val()
        },
        success: function(response) {
            $('.unpaid-list tbody').html(response);
            $('.unpaid-list').removeClass('hidden');
        }
    });
}

/**
 * Récupération de l'id de la facture sélectionnée
 * @param {type} el
 * @returns {undefined}
 */
function getSelectedFacture(el) {
    var uid = $(el).attr('id');
    $('#facture-selected').val(uid);
}

/**
 * Création d'une ligne de paiement
 * @returns {undefined}
 */
function createPaiement() {
    var uid = $('#facture-selected').val();
    var total = parseFloat($('#'+uid).find('.total').val());
    var paid = parseFloat($('#'+uid).find('.paid').val());
    var unpaid = parseFloat($('#'+uid).find('.unpaid').val());
    var page = getFilterType();
    var items = $('.paiement-list .'+uid);
    var dossierId = $('#dossier').val();
    
    if (page === 'paiement') {
        if (items.length > 0) {
            var totallist = paid;
            items.each(function() {
                var itemAmount = parseFloat($(this).find('.montant-paiement').val());
                totallist = totallist + itemAmount;
            });
            unpaid = parseFloat(total) - totallist;
        }
    }
    
    if (inferiorPaiement(uid)) {
        $.ajax({
            url: Routing.generate('one_paiement_new'),
            type: 'GET',
            dataType: 'html',
            data: {'facid': uid, 'total': total, 'paid': paid, 'unpaid': unpaid, 'page': page, 'dossierId':dossierId},
            success: function(response) {
                $('.paiement-list tbody').append(response);
                $('.paiement-list').removeClass('hidden');
                initDateField();
            }
        });
    } else {
        swal({
            title: "Attention",
            text: "Cette Echéance est complètement encaissée.",
            type: "warning"
        });
    }
}

function createEncaissementPaiement(data) {
    var uid = $('#facture-selected').val();
    var unpaid = parseFloat($('#'+uid).find('.unpaid').val());
    var page = getFilterType();
    var items = $('.paiement-list .'+uid);
    items.each(function() {
        var itemAmount = parseFloat($(this).find('.montant-paiement').val());
        unpaid = unpaid - itemAmount;
    });
    
    if (inferiorPaiement(uid)) {
        $.ajax({
            url: Routing.generate('one_encpaiement_new'),
            type: 'GET',
            dataType: 'html',
            data: {'facid': uid, 'data': data, 'page': page},
            success: function(response) {
                $('.paiement-list tbody').append(response);
                $('.paiement-list').removeClass('hidden');
                initDateField();
            }
        });
    } else {
        swal({
            title: "Attention",
            text: "Cette Echéance est complètement encaissée.",
            type: "warning"
        });
    }
}

function createAvoirPaiement(data) {
    var uid = $('#facture-selected').val();
    var unpaid = parseFloat($('#'+uid).find('.unpaid').val());
    var page = getFilterType();
    var items = $('.paiement-list .'+uid);
    var  dossierId  = $('#dossier').val();

    items.each(function() {
        var itemAmount = parseFloat($(this).find('.montant-paiement').val());
        unpaid = unpaid - itemAmount;
    });
    
    if (inferiorPaiement(uid)) {
        $.ajax({
            url: Routing.generate('one_avopaiement_new'),
            type: 'GET',
            dataType: 'html',
            data: {
                'facid': uid,
                'data': data,
                'page': page,
                'dossierId': dossierId
            },
            success: function(response) {
                $('.paiement-list tbody').append(response);
                $('.paiement-list').removeClass('hidden');
                initDateField();
            }
        });
    } else {
        swal({
            title: "Attention",
            text: "Cette Echéance est complètement encaissée.",
            type: "warning"
        });
    }
}

/**
 * Affichage d'un paiement
 * @param {type} id
 * @returns {undefined}
 */
function showPaiement(id) {
    $.ajax({
        url: Routing.generate('one_paiement_show', {'id': id}),
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
 * Envoi d'un paiement
 * @param {type} id
 * @returns {undefined}
 */
function sendPaiement(id) {
    $.ajax({
        url: Routing.generate('one_paiement_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            showEmailAction();
            openModal();
        }
    });
}

function savePaiement() {
    var form = $('#paiement-form');
    var items = $('.paiement-list tbody tr');
    if (items.length > 0) {
        $.ajax({
            url: Routing.generate('one_paiement_save'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response['type'] === 'success')
                    show_info('Succès', 'Ajout effectué', response['type']);
                else if (response['type'] === 'error')
                    show_info('Erreur', 'Ajout non effectué', response['type']);
                
                //Génération PDF
                for (var i = 0; i < response['ids'].length; i++) {
                    generatePDF('paiement', response['ids'][i]);
                };
                
                loadListPaiement();
            }
        });
    }
}

/**
 * Teste si le paiement n'est pas encore complet
 * @param {type} facid
 * @returns {Boolean}
 */
function inferiorPaiement(facid) {
    var total = parseFloat($('.unpaid-list #'+facid).find('.total').val());
    var paid = parseFloat($('.unpaid-list #'+facid).find('.paid').val());
    var page = getFilterType();
    if (page === 'facture') {
        var paiementTotal = 0;
    } else {
        var paiementTotal = paid;
    }
    var items = $('.paiement-list .'+facid);
    items.each(function() {
        var itemAmount = parseFloat($(this).find('.montant-paiement').val());
        paiementTotal = paiementTotal + itemAmount;
    });
    
    if (paiementTotal < total)
        return true;
    else
        return false;
}

/**
 * Récupération d'une ligne de paiement
 * @param {type} el
 * @returns {undefined}
 */
function getSelectedPaiement(el) {
    var uid = $(el).attr('id');
    $('#paiement-selected').val(uid);
    $('.remove-button').removeClass('hidden');
}

/**
 * Suppression d'une ligne de paiement
 * @returns {undefined}
 */
function removeSelectedPaiement() {
    var uid = $('#paiement-selected').val();
    $('.paiement-list tbody tr#'+uid).remove();
    $('.remove-button').addClass('hidden');
    $('#paiement-selected').val('');
    
    if (getFilterType() === 'facture') {
        var pid = uid.split('-')[0];
        if (pid > 0) {
            $('#paiement-deleted-form').append('<input type="hidden" name="deleted-paiement[]" value="'+pid+'">');
        }
    }
}

function updatePaiement(el) {
    var parent = $(el).parents('.paiement-list tr');
    var factureID = parent.attr('class').split(' ')[1];
    var paiementID = parent.attr('id').split('-')[0];
    var datePaiement = parent.find('.date-paiement').val();
    var montantPaiement = parent.find('.montant-paiement').val().replace(new RegExp(' ', "g"), '');
    var moyenPaiement = parent.find('.moyen-paiement').val();
    var refPaiement = parent.find('.ref-bancaire-paiement').val();
    var retardPaiement = parent.find('.retard-paiement').val();
    var comptePaiement = parent.find('.compte-paiement').val();
    var encid = parent.find('.encid').val();
    var avoid = parent.find('.avoid').val();
    var updated = factureID+';'+paiementID+';'+datePaiement+';'+montantPaiement+';'+moyenPaiement+';'+refPaiement+';'+retardPaiement+';'+comptePaiement+';'+encid+';'+avoid;
    parent.find('.paiement').val(updated);
}


