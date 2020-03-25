/**
 *
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
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_achat_commande_list'),
        type: 'GET',
        dataType: 'html',
        data: {'stat': stat, 'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId': dossierId},
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
    $.ajax({
        type: 'GET',
        url: Routing.generate('one_achat_commande_edit'),
        dataType: 'html',
        data: {'parent': getParent(), 'parentid': getParentID(), 'dossierId': $('#dossier').val()},
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);

            if (getParent() === 'fournisseur')
                $('.btn-back').attr('onclick', 'loadShowFournisseur('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListCommande();');

            initDateField();
        }
    });
}


/**
 * Sauvegarde d'une commande
 * @returns {undefined}
 */
function saveCommande() {

    var form = $('#facture-form');

    var fournisseurField = form.find('#fournisseur');

    if (validateField(fournisseurField)) {
        $.ajax({
            url: Routing.generate('one_achat_commande_save'),
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
                        loadListCommande();
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
                        loadListCommande();
                }
            }
        });
    }
}


/**
 * Charge le formulaire d'édition d'une facture
 * @param {id} id
 * @param one
 * @returns {undefined}
 */
function loadEditCommande(id, one) {

    var fromOneUp = 0;
    if(one === true || one === undefined){
        fromOneUp = 1;
    }

    $.ajax({
        url: Routing.generate('one_achat_commande_edit', {'id': id, 'one': fromOneUp}),
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
                $('.btn-back').attr('onclick', 'loadListCommande();');
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
 * Suppression d'un bon de commande
 * @param {int} id
 * @returns {undefined}
 */
function deleteCommande(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Bon de Commande?",
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
                    show_info("Supprimé!", "Votre Bon de Commande a bien été supprimé", response['type']);
                    if (getParent() === 'fournisseur')
                        loadShowFournisseur(getParentID());
                    else
                        loadListCommande();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre Bon de Commande ne peut être supprimé car il est encore référencé", response['type']);
                }
            }
        });
    });
}