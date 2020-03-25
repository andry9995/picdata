/**
 * Project: oneup
 * Author : Mamy Rakotonirina
 * Created on : 23 oct. 2017 20:59:37
 */

$(document).ready(function () {

    readyModele();

    $(document).on('change', '#dossier', function () {
        loadListModele();
    });
});

function deleteModele(id) {
    swal({
        title: "Confirmation",
        text: "Êtes-vous sûr de vouloir supprimer votre Modèle?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, supprimer!",
        cancelButtonText: 'Non, annuler!'
    }).then(function () {
        //window.location.href = Routing.generate('one_modele_delete', {'id': id});
        $.ajax({
            url: Routing.generate('one_modele_delete', {'id': id}),
            type: 'GET',
            dateType: 'json',
            success: function(response) {
                if (response['type'] === 'success') {
                    show_info("Supprimé!", "Votre modèle a bien été supprimé", response['type']);
                    // window.location.href = Routing.generate('one_modeles');
                } else if (response['type'] === 'error') {
                    show_info("Non supprimé!", "Votre modèle ne peut être supprimée car il est encore référencé", response['type']);
                    // window.location.href = Routing.generate('one_modeles');
                }
                loadListModele();
            }
        });
    });
}

function changeModele(el, docId, docType) {
    $.ajax({
        url: Routing.generate('one_modele_change'),
        type: 'GET',
        dateType: 'json',
        data: {'modeleId': el.value, 'docId': docId, 'docType': docType},
        success: function(response) {
            if (response['type'] === 'success') {
                if (docType === 'devis') {
                    showDevis(docId);
                    generatePDF('devis', docId);
                } else if (docType === 'facture') {
                    showFacture(docId);
                    generatePDF('facture', docId);
                } else if (docType === 'commande') {
                    showCommande(docId);
                    generatePDF('commande', docId);
                } else if (docType === 'livraison') {
                    showShippedCommande(docId);
                    generatePDF('livraison', docId);
                } else if (docType === 'avoir') {
                    showAvoir(docId);
                    generatePDF('avoir', docId);
                } else if (docType === 'encaissement') {
                    showEncaissement(docId);
                    generatePDF('encaissement', docId);
                } else if (docType === 'paiement') {
                    showPaiement(docId);
                    generatePDF('paiement', docId);
                }
            } else {
                show_info("Erreur!", "Le changement de modèle a echoué", response['type']);
            }
        }
    });
}

function loadListModele() {
    $('#modele-container').empty();
    $.ajax({
        url: Routing.generate('one_modele_list'),
        data: {dossierId: $('#dossier').val()},
        type: 'POST',
        success: function (response) {
            $('#modele-container').html(response);
        }
    });
}


function loadEditModele(id, readonly){
    $.ajax({
        url: Routing.generate('one_modele_edit', {'id': id}),
        type: 'POST',
        data: {readonly: readonly},
        dataType: 'html',
        success: function(response) {
            $('#modele-container').html(response);
            readyModele();
        }
    });
}

function loadNewModele() {

    if($('#dossier').val() === ''){
        show_info('Attention', 'Il faut choisir un dossier', 'warning');
        return false;
    }

    $.ajax({
        url: Routing.generate('one_modele_add'),
        type: 'POST',
        dataType: 'html',
        success: function(response) {
            $('#modele-container').html(response);
            readyModele();
         }
    });
}


function readyModele(){
    $('.colorpicker').colorpicker();

    //Fond enete tableau
    $('#head-color').colorpicker().on('changeColor', function() {
        $('.table th').css('background-color', $(this).val());
    });

    //Couleur de texte
    $('#font-color').colorpicker().on('changeColor', function() {
        $('.modele-preview').css('color', $(this).val());
    });

    //Police
    $('#font-family').change(function() {
        $('.modele-preview').css('font-family', $(this).val());
    });

    //Taille de police
    $('#font-size').change(function() {
        $('.modele-preview').css('font-size', $(this).val()+'px');
    });

    //Nom entreprise
    $('input[type=radio][name=show-company-name]').change(function() {
        if (this.value == 1) {
            $('.company-name').fadeIn();
        } else if (this.value == 0) {
            $('.company-name').fadeOut();
        }
    });

    //Reglement
    $('input[type=radio][name=show-reglement]').change(function() {
        if (this.value == 1) {
            $('.document-reglement').fadeIn();
        } else if (this.value == 0) {
            $('.document-reglement').fadeOut();
        }
    });

    //Num client
    $('input[type=radio][name=show-num-client]').change(function() {
        if (this.value == 1) {
            $('.customer-num').fadeIn();
        } else if (this.value == 0) {
            $('.customer-num').fadeOut();
        }
    });

    //Tel client
    $('input[type=radio][name=show-tel-client]').change(function() {
        if (this.value == 1) {
            $('.shipping-customer-tel').fadeIn();
            $('.billing-customer-tel').fadeIn();
        } else if (this.value == 0) {
            $('.shipping-customer-tel').fadeOut();
            $('.billing-customer-tel').fadeOut();
        }
    });

    //Adresse livraison
    $('input[type=radio][name=show-shipping-address]').change(function() {
        if (this.value == 1) {
            $('.shipping-address').fadeIn();
        } else if (this.value == 0) {
            $('.shipping-address').fadeOut();
        }
    });
    $('input[name=shipping-address-label]').keyup(function() {
        $('.shipping-address-label').text($(this).val());
    });

    //Adresse facturation
    $('input[type=radio][name=billing-address-right]').change(function() {
        if (this.value == 1) {
            $('.billing-address').fadeIn();
        } else if (this.value == 0) {
            $('.billing-address').fadeOut();
        }
    });
    $('input[name=billing-address-label]').keyup(function() {
        $('.billing-address-label').text($(this).val());
    });

    //TVA Intracom
    $('input[type=radio][name=show-tva-intracom]').change(function() {
        if (this.value == 1) {
            $('.company-tva-intracom').fadeIn();
        } else if (this.value == 0) {
            $('.company-tva-intracom').fadeOut();
        }
    });

    //Désignation
    $('input[name=designation-label]').keyup(function() {
        $('.designation-label').text($(this).val());
    });

    //Quantité
    $('input[type=radio][name=show-quantity]').change(function() {
        if (this.value == 1) {
            $('.quantity-col').fadeIn();
        } else if (this.value == 0) {
            $('.quantity-col').fadeOut();
        }
    });
    $('input[name=quantity-label]').keyup(function() {
        $('.quantity-label').text($(this).val());
    });

    //Prix unitaire
    $('input[type=radio][name=show-price]').change(function() {
        if (this.value == 1) {
            $('.price-col').fadeIn();
        } else if (this.value == 0) {
            $('.price-col').fadeOut();
        }
    });
    $('input[name=price-label]').keyup(function() {
        $('.price-label').text($(this).val());
    });

    //Unité
    $('input[type=radio][name=show-unit]').change(function() {
        if (this.value == 1) {
            $('.article-unit').fadeIn();
        } else if (this.value == 0) {
            $('.article-unit').fadeOut();
        }
    });

    //Code article
    $('input[type=radio][name=show-product-code]').change(function() {
        if (this.value == 1) {
            $('.article-code').fadeIn();
        } else if (this.value == 0) {
            $('.article-code').fadeOut();
        }
    });

    //Information payment
    $('input[type=radio][name=show-payment-info]').change(function() {
        if (this.value == 1) {
            $('.payment-info-label').fadeIn();
            $('.deadline').fadeIn();
            $('.payment-info-default').fadeIn();
        } else if (this.value == 0) {
            $('.payment-info-label').fadeOut();
            $('.deadline').fadeOut();
            $('.payment-info-default').fadeOut();
        }
    });
    $('input[name=payment-info-label]').keyup(function() {
        $('.payment-info-label').text($(this).val());
    });

    //Deadline
    $('input[type=radio][name=show-deadline]').change(function() {
        if (this.value == 1) {
            $('.deadline').fadeIn();
        } else if (this.value == 0) {
            $('.deadline').fadeOut();
        }
    });

    //Information paiement par défaut
    $('textarea[name=payment-info-default]').keyup(function() {
        $('.payment-info-default').text($(this).val());
    });

    //Note globale
    $('textarea[name=global-note]').keyup(function() {
        $('.global-note').text($(this).val());
    });
}


function saveModele() {

    var form = $('#modele-form');


    if(!validateField(form.find('#modele-name'))){
        return false;
    }
    $.ajax({
        data: form.serialize(),
        url: Routing.generate('one_modele_save',{dossierId: $('#dossier').val()}),
        type: 'POST',
        success: function (response) {

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

            loadListModele();

        }
    });

}