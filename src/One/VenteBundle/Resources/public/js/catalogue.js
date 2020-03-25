/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 14 oct. 2017 11:15:04
 */

$(document).ready(function () {
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });
    
    //Chargement des prospects par défaut
    initAllFilter();
    setPeriod('all');
    
    // loadListArticle();

    $(document).on('change', '#dossier', function(){
        loadListArticle();
    });
    
    //Pour le champ recherche
    $(document).on('keyup', 'input.search', function() {
        if ($(this).val() !== '') {
            $('.init-search').removeClass('hidden');
        } else {
            $('.init-search').addClass('hidden');
        }
    });
    
    //Changement du type de vue
    $(document).on('click', '.view-list', function() {
        updateView('list');
    });
    $(document).on('click', '.view-bloc', function() {
        updateView('bloc');
    });
    
    //Réinitialise la couleur de bordure après validation du champ
    $(document).on('focus', '.form-control', function() {
        $(this).css('border-color', '#E5E6E7');
    });
});

/**
 * Charge la liste des articles
 * @returns {undefined}
 */
function loadListArticle() {
    var q = $('#q').val();
    var sort = $('#sort').val();
    var sortorder = $('#sortorder').val();
    var period = $('#period').val();
    var startperiod = $('#startdate').val();
    var endperiod = $('#enddate').val();
    var dossierId = $('#dossier').val();
    resetTabContent();
    $.ajax({
        url: Routing.generate('one_article_list'),
        type: 'GET',
        dataType: 'html',
        data: {'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId': dossierId},
        success: function(response) {
            showInfoByResponse(response);
            $('#page-container').html(response);
            setFilterType('article');
            setParent('', '');
            showInitSearch();
            initDateField();
            getView();
        }
    });
}

/**
 * Charge le formulaire d'ajout d'un article
 */
function loadNewArticle() {
    $.ajax({
        url: Routing.generate('one_article_new'),
        type: 'POST',
        data: {dossierId: $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#page-container').html(response);
            $('#id-dossier').val($('#dossier').val());
        }
    });
}

/**
 * Enregistrement de l'article
 * @returns {undefined}
 */
function saveArticle() {
    var form = $('#article-form');
    var nomField = form.find('#nom');
    if (validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_article_save'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                //Si ajout
                if (response['action'] === 'add') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Ajout effectué', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Ajout non effectué', response['type']);
                }
                //Si édition
                else if (response['action'] === 'edit') {
                    if (response['type'] === 'success')
                        show_info('Succès', 'Modification sauvegardée', response['type']);
                    else if (response['type'] === 'error')
                        show_info('Erreur', 'Modification non sauvegardée', response['type']);
                }
                loadListArticle();
            }
        });
    }
}

/**
 * Suppression d'un article
 * @param {int} id
 * @returns {undefined}
 */
function deleteArticle(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Article?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        $.ajax({
            url: Routing.generate('one_article_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre article a bien été supprimé", response['type']);
                    loadListArticle();
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre article ne peut être supprimé car il est encore référencé", response['type']);
                }
            }
        });
    });
}

/**
 * Suppression de plusieurs articles
 * @returns {undefined}
 */
function deleteSelectedArticle() {
    swal({
        title: "Êtes-vous sûr?",
        text: "Attention, tous les Articles qui sont utilisés autre part dans l'application, ne pourront pas être supprimés.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        var checked = $('#page-container').find($('input.element:checked'));
        checked.each(function() {
            $.ajax({
                url: Routing.generate('one_article_delete', {'id': $(this).val()}),
                type: 'GET',
                dateType: 'json',
                success: function(response) {
                    if (response['type'] === 'success') {
                        show_info("Supprimé!", "Votre article a bien été supprimé", response['type']);
                        loadListArticle();
                    } else if (response['type'] === 'error') {
                        show_info("Non supprimé!", "Votre article ne peut être supprimé car il est encore référencé", response['type']);
                    }
                }
            });
        });
    });
}

/**
 * Charge le formualire d'édtion d'un article
 * @param {int} id : ID de l'article
 * @returns {undefined}
 */
function loadEditArticle(id) {
    $.ajax({
        url: Routing.generate('one_article_edit', {'id': id}),
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            $('#page-container').html(response);
        }
    });
}