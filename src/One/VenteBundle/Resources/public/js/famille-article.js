/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 9 nov. 2017 11:49:53
 */

function loadNewFamilleArticleModal(oldID) {
    $.ajax({
        url: Routing.generate('one_famille_article_new_modal'),
        type: 'GET',
        data: { 'oldID': oldID },
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}

function loadUpdatedFamilleArticleOptions(selectedValue) {
    $.ajax({
        url: Routing.generate('one_famille_article_options_list'),
        type: 'GET',
        data: { 'selectedValue': selectedValue },
        dataType: 'html',
        success: function(response) {
            $('#famille-article').html(response);
        }
    });
}

function saveFamilleArticle() {
    var form = $('#famille-article-form');
    var nomField = form.find('#nom');
    if (validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_famille_article_save'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response['type'] === 'success') {
                    loadUpdatedFamilleArticleOptions(response['id']);
                }
                closeModal();
            }
        });
    }
}

function resetFamilleSelected(value) {
    $('#famille-article').val(value);
}