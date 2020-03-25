/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des taches
 * @returns {undefined}
 */
function searchTache() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListTache();
    }
}

/**
 * Réinitialise la recherche des taches
 * @returns {undefined}
 */
function initSearchTache() {
    initFilterQ();
    loadListTache();
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des taches
 * @returns {undefined}
 */
function loadListTache() {
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
        url: Routing.generate('one_tache_list'),
        type: 'GET',
        dataType: 'html',
        data: {'stat': stat, 'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId': dossierId},
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-tache .panel-body').html(response);
            setFilterType('tache');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une tâche
 * @returns {undefined}
 */
function loadNewTache() {
    $.ajax({
        url: Routing.generate('one_tache_new'),
        type: 'GET',
        dataType: 'html',
        data: {'parent': getParent(), 'parentid': getParentID(), 'parent2': getParent2(), 'parentid2': getParentID2(), 'dossierId':$('#dossier').val()},
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent2() === 'opportunite')
                $('.btn-back').attr('onclick', 'loadShowOpportunite('+getParentID2()+');');
            else if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'opportunite')
                $('.btn-back').attr('onclick', 'loadShowOpportunite('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListTache();');
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'une tache
 * @param {type} id
 * @returns {undefined}
 */
function loadEditTache(id) {
    $.ajax({
        url: Routing.generate('one_tache_edit', {'id': id}),
        type: 'GET',
        data: {dossierId: $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent2() === 'opportunite')
                $('.btn-back').attr('onclick', 'loadShowOpportunite('+getParentID2()+');');
            else if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else if (getParent() === 'opportunite')
                $('.btn-back').attr('onclick', 'loadShowOpportunite('+getParentID()+');');
            else if (getParent() === 'projet')
                $('.btn-back').attr('onclick', 'loadShowProjet('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListTache();');
            
            initDateField();
        }
    });
}

/**
 * Sauvegarde une tache
 * @returns {undefined}
 */
function saveTache() {
    var form = $('#tache-form');
    var sujetField = form.find('#sujet');
    
    if (validateField(sujetField)) {
        $.ajax({
            url: Routing.generate('one_tache_save'),
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
                    
                    if (getParent2() === 'opportunite')
                        loadShowOpportunite(getParentID2());
                    else if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'opportunite')
                        loadShowOpportunite(getParentID());
                    else if (getParent() === 'projet')
                        loadShowProjet(getParentID());
                    else
                        loadListTache();
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    
                    if (getParent2() === 'opportunite')
                        loadShowOpportunite(getParentID2());
                    else if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'opportunite')
                        loadShowOpportunite(getParentID());
                    else if (getParent() === 'projet')
                        loadShowProjet(getParentID());
                    else
                        loadListTache();
                }
            }
        }); 
    }
}

/**
 * Suppression d'une tache
 * @param {int} id
 * @returns {undefined}
 */
function deleteTache(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Tâche?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_tache_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre tâche a bien été supprimée", response['type']);
                    
                    if (getParent2() === 'opportunite')
                        loadShowOpportunite(getParentID2());
                    else if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else if (getParent() === 'opportunite')
                        loadShowOpportunite(getParentID());
                    else
                        loadListTache();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre tâche ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs taches
 * @returns {undefined}
 */
function deleteSelectedTache() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Tâches qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
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
                url: Routing.generate('one_tache_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre tâche a bien été supprimée", response['type']);
                        
                        if (getParent2() === 'opportunite')
                            loadShowOpportunite(getParentID2());
                        else if (getParent() === 'prospect')
                            loadShowProspect(getParentID());
                        else if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else if (getParent() === 'opportunite')
                            loadShowOpportunite(getParentID());
                        else
                            loadListTache();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre tâche ne peut être supprimée car elle est encore référencée", response['type']);
                    }
                }
            });
        });
    });
}