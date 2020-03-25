/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des opportunites
 * @returns {undefined}
 */
function searchOpportunite() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListOpportunite();
    }
}

/**
 * Réinitialise la recherche des opportunites
 * @returns {undefined}
 */
function initSearchOpportunite() {
    initFilterQ();
    loadListOpportunite();
    $('.init-search').addClass('hidden');
}

/**
 * Recherche dans détail d'une opportunite
 * @param {int} id d'un opportunite
 * @returns {undefined}
 */
function searchInOpportunite(id) {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadShowOpportunite(id);
    }
}

/**
 * Réinitialise la recherche dans détail d'une opportunite
 * @param {int} id d'un opportunite
 * @returns {undefined}
 */
function initSearchInOpportunite(id) {
    initFilterQ();
    loadShowOpportunite(id);
    $('.init-search').addClass('hidden');
}

/**
 * Charge la liste des opportunités
 * @returns {undefined}
 */
function loadListOpportunite() {
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
        url: Routing.generate('one_opportunite_list'),
        type: 'GET',
        dataType: 'html',
        data: {'stat': stat, 'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId': dossierId},
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-opportunite .panel-body').html(response);
            setFilterType('opportunite');
            setParent('', '');
            setParent2('', '');
            showInitSearch();
            initDateField();
            getView();
            if(response !== '')
                opportuniteSortable();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'une opportunité
 * @returns {undefined}
 */
function loadNewOpportunite() {
    $.ajax({
        url: Routing.generate('one_opportunite_new'),
        type: 'GET',
        dataType: 'html',
        data: {'parent': getParent(), 'parentid': getParentID(), 'dossierId': $('#dossier').val()},
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListOpportunite();');
            
            initDateField();
        }
    });
}

/**
 * Charge le formulaire d'édition d'une opportunité
 * @param {id} id
 * @returns {undefined}
 */
function loadEditOpportunite(id) {

    $.ajax({
        url: Routing.generate('one_opportunite_edit', {'id': id}),
        type: 'GET',
        data: {'dossierId': $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListOpportunite();');
            
            initDateField();

            $('#id-dossier').val($('#dossier').val());
        }
    });
}

/**
 * Chargement de la page info
 * @param {int} id
 * @returns {undefined}
 */
function loadShowOpportunite(id) {
    if (getFilterType() === 'opportunite')
        setFilterType('all');
    
    var type = $('#type').val();
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    
    $.ajax({
        url: Routing.generate('one_opportutnite_show', {'id': id}),
        type: 'GET',
        dataType: 'html',
        data: {'type': type, 'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod},
        success: function(response) {
            var activetab = $('.nav-tabs .active a').attr('href');
            $(activetab+' .panel-body').html(response);
            
            if (getParent() === 'prospect')
                $('.btn-back').attr('onclick', 'loadShowProspect('+getParentID()+');');
            else if (getParent() === 'client')
                $('.btn-back').attr('onclick', 'loadShowClient('+getParentID()+');');
            else
                $('.btn-back').attr('onclick', 'loadListOpportunite();');
            
            if (getParent() === 'prospect' || getParent() === 'client')
                setParent2('opportunite', id);
            else
                setParent('opportunite', id);
            
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Sauvegarde d'une opportunité
 * @returns {undefined}
 */
function saveOpportunite() {
    var form = $('#opportunite-form');
    var clientProspectField = form.find('#client-prospect');
    // var avancementField = form.find('#avancement');
    var nomField = form.find('#nom');

    if (validateField(clientProspectField) && validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_opportunite_save'),
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
                    loadShowOpportunite(response['id']);
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                    loadShowOpportunite(response['id']);
                }
            }
        }); 
    }
}

/**
 * Récupération des contacts d'un prospect
 * @param {object} clientProspectID
 * @returns {undefined}
 */
function getListContacts(clientProspectID) {
    $.ajax({
        url: Routing.generate('one_opportunite_list_contacts'),
        type: 'GET',
        dateType: 'html',
        data: {'client-prospect': clientProspectID},
        success: function(response) {
            $('#contact-client').html(response);
            $('#contact-livraison').html(response);
        }
    });
}


/**
 * Suppression d'une opportunité
 * @param {int} id
 * @returns {undefined}
 */
function deleteOpportunite(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Opportunité?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_opportunite_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre opportunité a bien été supprimée", response['type']);
                    if (getParent() === 'prospect')
                        loadShowProspect(getParentID());
                    else if (getParent() === 'client')
                        loadShowClient(getParentID());
                    else
                        loadListOpportunite();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre opportunité ne peut être supprimée car elle est encore référencée", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs opportunités
 * @returns {undefined}
 */
function deleteSelectedOpportunite() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Opportunités qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
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
                url: Routing.generate('one_opportunite_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre opportunité a bien été supprimée", response['type']);
                        if (getParent() === 'prospect')
                            loadShowProspect(getParentID());
                        else if (getParent() === 'client')
                            loadShowClient(getParentID());
                        else
                            loadListOpportunite();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre opportunité ne peut être supprimée car elle est encore référencée", response['type']);
                    }
                }
            });
        });
    });
}

/**
* Ajout d'une étape d'opportunité
* @returns {undefined}
*/
function loadNewOpportuniteStep() {
    $.ajax({
        url: Routing.generate('one_opportunite_step_new'),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#secondary-modal').find('.modal-content').html(response);
            $('#dossier-id').val($('#dossier').val());
            openSecondModal();
            setSwitch();
        }
    });
}

/**
 * Edition d'une étape d'opportunité
 * @param {id} id
 * @returns {undefined}
 */
function loadEditOpportuniteStep(id) {
    $.ajax({
        url: Routing.generate('one_opportunite_step_edit', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#secondary-modal .modal-content').html(response);
            $('#dossier-id').val($('#dossier').val());
            openSecondModal();
            setSwitch();
        }
    });
}

/**
 * Sauvegarde d'une étape d'opportunité
 * @returns {undefined}
 */
function saveOpportuniteStep() {
    var form = $('#opportunite-step-form');
    var nomField = form.find('#nom');

    if (validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_opportunite_step_save'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                closeSecondModal();
                loadListOpportunite();
            }
        });
    }
}

function deleteOpportuniteStep(id) {
    $.ajax({
        url: Routing.generate('one_opportunite_step_delete', {'id': id}),
        type: 'GET',
        dateType: 'json',
        success: function(response) {
            if (response['type'] === 'success') {
                loadListOpportunite();
            } else if (response['type'] === 'error') {
                show_info("Non supprimé!", "Votre étape ne peut être supprimée car elle est encore référencée", response['type']);
            }
        }
    });
}

function setSwitch(){
    $('.switch').each(function () {
        var id = $(this).attr('id');
        var elem = document.querySelector('#'+id);
        new Switchery(elem, { color: '#1AB394' });
    });
}

function showHelp(el){
    var help = $(el).parent().find('.help-block');
    if($(el).is(':checked')){
        help.removeClass('hidden');
    }
    else{
       if(!help.hasClass('hidden')){
           help.addClass('hidden');
       }
    }
}



function opportuniteSortable(){

    // Kanban opportunities
    initializeUlHeight();

    Sortable.create(document.getElementById('opportunities'), {
        animation: 150,
        draggable: '.col-md-2',
        handle: '.ibox h3',
        onUpdate: function (evt) {
            var step_order = {};
            var items = $('#opportunities').find('.col-md-2');
            $.each(items, function(index, value) {
                var item = $(this);
                step_order[item.attr('id')] = $('#opportunities').find('> div').index(item);
            });

            $.ajax({
                url: Routing.generate('one_opportunite_step_order'),
                type: 'GET',
                data: { steps:step_order },
                dataType: 'json',
                success: function(response) {
                }
            });
        }
    });

    $(".sortable-list").sortable({
        connectWith: ".connectList",
        update: function( event, ui ) {
            var output = '';
            var opp_id = ui.item.attr('id');
            var step_id = $('#'+opp_id).parent('ul').attr('id');

            // alert(opp_id+':'+step_id);

            initializeUlHeight();

            $.ajax({
                url: Routing.generate('one_opportunite_step_update'),
                type: 'GET',
                data: { 'opp_id':opp_id, 'step_id':step_id },
                dataType: 'json',
                success: function(response) {
                }
            });
        }
    }).disableSelection();
}


function initializeUlHeight(){
    var containers = $('#opportunities').find('ul.agile-list');
    var max = 0;
    containers.each(function () {
        var height = $(this).height();
        if(max < height){
            max = height;
        }
    });
    containers.each(function () {
        $(this).css('height', max + 90);
    });
}