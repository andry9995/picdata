/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Recherche des articles
 * @returns {undefined}
 */
function searchArticle() {
    if ($('.search').val() !== '') {
        setFilterQ();
        loadListArticle();
    }
}

/**
 * Réinitialise la recherche des articles
 * @returns {undefined}
 */
function initSearchArticle() {
    initFilterQ();
    loadListArticle();
    $('.init-search').addClass('hidden');
}

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
        data: {'q': q, 'sort': sort, 'sortorder': sortorder, 'period': period, 'startperiod': startperiod, 'endperiod': endperiod, 'dossierId':dossierId},
        success: function(response) {
            showInfoByResponse(response);
            $('#tab-produit-service .panel-body').html(response);
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
        data:{dossierId:$('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#tab-produit-service .panel-body').html(response);
            $('#id-dossier').val($('#dossier').val());
        }
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
            $('#tab-produit-service .panel-body').html(response);
            $('#id-dossier').val($('#dossier').val());
        }
    });
}

/**
 * Chargement du formulaire d'ajout dans le modal
 * @returns {undefined}
 */
function loadNewArticleModal(parent) {
    $.ajax({
        url: Routing.generate('one_article_new_modal', {parent: parent}),
        type: 'POST',
        data: {dossierId: $('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
        }
    });
}

/**
 * Chargement de la liste des articles dans le modal
 * @param {string} parent
 * @returns {undefined}
 */
function loadListArticleModal(parent) {
    var route = '';
    switch (parent){
        case 'opportunite':
            route = 'one_article_opportunite_list';
            break;

        case 'achat':
            route = 'one_article_achat_list';
            break;

        default:
            route = 'one_article_vente_list';
            break;
    }

    $.ajax({
        url: Routing.generate(route),
        type: 'POST',
        data: {'dossierId':$('#dossier').val()},
        dataType: 'html',
        success: function(response) {
            $('#primary-modal .modal-content').html(response);
            openModal();
        }
    });
}

/**
 * Enregistrement de l'article
 * @returns {undefined}
 */
function saveArticle(parent) {
    var form = $('#article-form');
    var nomField = form.find('#nom');
    if (validateField(nomField)) {
        $.ajax({
            url: Routing.generate('one_article_save', {dossier: $('#dossier').val()}),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                if ($('#primary-modal').hasClass('in')) {
                    loadListArticleModal(parent);
                } else {
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
        var checked = $('#tab-produit-service').find($('input.element:checked'));
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

//OPPORTUNITE//

/**
 * Ajout des articles sélectionnés
 * @returns {undefined}
 */
function selectArticleOpportunite() {
    var articles = $('.articles:checked');
    if (articles.length > 0) {
        var response = '';
        articles.each(function() {
            var values = $(this).val().split(';');
            var id = values[0];
            var code = values[1];
            var nom = values[2];
            var unite = values[3];
            var montant = values[4];
            
            response = response + '<tr id="'+id+'-'+moment().valueOf()+'" class="" onclick="selectToRemove(this);">';
            
            response = response + '<td>';
            response = response + '<input type="hidden" class="form-control artoppid" value="" />';
            response = response + '<input type="hidden" class="form-control artid" value="'+id+'" />';
            response = response + code+' - '+nom+':';
            response = response + '</td>';
            
            response = response + '<td>';
            response = response + '<input type="number" class="form-control quantite" value="1" onblur="updateOppAmount();" />';
            response = response + '</td>';
            
            response = response + '<td>';
            response = response + unite;
            response = response + '</td>';
            
            response = response + '<td>';
            response = response + '<input type="text" class="form-control montant number" value="'+montant+'" onblur="updateOppAmount();" />';
            response = response + '<input type="hidden" class="form-control serialized" name="articles[]" value="" />';
            response = response + '</td>';
            
            response = response + '</tr>';
        });
        $('.article-list tbody').append(response);
        $('#montant').attr('readonly', true);
        updateOppAmount();
        closeModal();
    }
}

function updateOppAmount() {
    var total_amount = 0.00;
    var article_line = $('.article-list tbody').find('tr');
    article_line.each(function() {
        var artoppid = $(this).find('.artoppid').val();
        var artid = $(this).find('.artid').val();
        var quantite = $(this).find('.quantite').val();

        var montantVal = $(this).find('.montant').val();
        var montant = 0;
        if(montantVal !== undefined) {
            montant = montantVal.replace(new RegExp(' ', "g"), '');
        }
        var line_amount = parseFloat(quantite) * parseFloat(montant);
        total_amount = total_amount + line_amount;
        
        var serialized = 'id='+artoppid+'&article-id='+artid+'&quantite='+quantite+'&montant='+montant;
        $(this).find('.serialized').val(serialized);
    });
    $('#montant').val(total_amount);
    
    var moneyFields = $('.number');
    moneyFields.each(function() {
        format($(this));
    });
}

function removeArticle() {
    var item = $('#article-to-remove').val();
    var uid = item.split(':')[0];
    var artoppid = parseInt(item.split(':')[1]);
    if (artoppid) {
        var output = '<input type="hidden" name="deleted-articles[]" value="'+artoppid+'" />';
        $('#articles-deleted').append(output);
    }
    $('.article-list tbody tr#'+uid).remove();
    $('#article-to-remove').val('');
    
    var articlesnb = $('.article-list tbody tr').length;
    if (articlesnb == 0) {
        $('#montant').removeAttr('readonly');
    }
    updateOppAmount();
}

//VENTE//

function selectArticleVenteOld() {
    var articles = $('.articles:checked');
    if (articles.length > 0) {
        var response = '';
        articles.each(function() {
            var values = $(this).val().split(';');
            var id = values[0];
            var code = values[1];
            var nom = values[2];
            var unite = values[3];
            var price = values[4];
            
            response = response + '<tr id="'+id+'-'+moment().valueOf()+'" class="" onclick="selectToRemove(this);">';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<input type="hidden" class="form-control artventeid" value="" />';
            response = response + '<input type="hidden" class="form-control artid" value="'+id+'" />';
            response = response + code+' '+nom;
            response = response + '</td>';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<textarea class="form-control description" onblur="updateAmountTTC();"></textarea>';
            response = response + '</td>';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<input type="number" class="form-control quantite" value="1" onblur="updateAmountTTC();" />';
            response = response + '</td>';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<input type="number" class="form-control price" value="'+price+'" onblur="updateAmountTTC();" />';
            response = response + '</td>';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<input type="number" class="form-control remise" value="0" onblur="updateAmountTTC();" />';
            response = response + '</td>';
            
            response = response + '<td style="vertical-align:middle;">';
            response = response + '<span class="item-amount">0</span>';
            response = response + '<input type="hidden" class="form-control serialized" name="articles[]" value="" />';
            response = response + '</td>';
            
            response = response + '</tr>';
        });
        $('.article-list tbody').append(response);
        $('#montant-ht').attr('readonly', true);
        $('#montant-ttc').attr('readonly', true);
        updateAmountTTC();
        closeModal();
    }
}

function selectArticleVente() {
    var form = $('#article-form');
    var articles = $('.articles:checked');
    if (articles.length > 0) {
        $.ajax({
            url: Routing.generate('one_article_vente_add'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                $('.article-list tbody').append(response);
                $('#montant-ht').attr('readonly', true);
                $('#montant-ttc').attr('readonly', true);
                updateAmountTTC();
                closeModal();
            }
        });
    }
}


function selectArticleAchat() {
    var form = $('#article-form');
    var articles = $('.articles:checked');
    if (articles.length > 0) {
        $.ajax({
            url: Routing.generate('one_article_achat_add'),
            type: 'POST',
            dateType: 'json',
            data: form.serialize(),
            success: function(response) {
                $('.article-list tbody').append(response);
                $('#montant-ht').attr('readonly', true);
                $('#montant-ttc').attr('readonly', true);
                updateAmountTTC();
                closeModal();
            }
        });
    }
}


function updateItemAmount() {
    var article_line = $('.article-list tbody').find('tr');
    article_line.each(function() {
        var artventeid = $(this).find('.artventeid').val();
        var artid = $(this).find('.artid').val();
        var description = $(this).find('.description').val();
        var quantite = $(this).find('.quantite').val();

        var priceVal = $(this).find('.price').val();
        var price = 0;

        if(priceVal !== undefined) {
            price = priceVal.replace(new RegExp(' ', "g"), '');
        }
        var remise = $(this).find('.remise').val();
        var tva = $(this).find('.tva').val();
        var line_amount_ht = quantite * parseFloat(price);
        if (remise > 0) {
            var amount_remise = (line_amount_ht * remise) / 100;
            line_amount_ht = line_amount_ht - parseFloat(amount_remise);
        }
        $(this).find('.item-amount').html(formatValue(line_amount_ht));
        var serialized = 'id='+artventeid+'&article-id='+artid+'&description='+description+'&quantite='+quantite+'&price='+price+'&remise='+remise+'&tva='+tva;
        $(this).find('.serialized').val(serialized);
        
        var moneyFields = $('.number');
        moneyFields.each(function() {
            format($(this));
        });
    });



    var depense_line = $('.depense-list tbody').find('tr');
    depense_line.each(function() {
        var iddep = $(this).find('.iddep').val();
        var pccdep = $(this).find('.pccdep').val();

        var pricedepVal = $(this).find('.pricedep').val();
        var pricedep = 0;
        if(pricedepVal !== undefined) {
            pricedep = $(this).find('.pricedep').val().replace(new RegExp(' ', "g"), '');
        }

        var remisedep = $(this).find('.remisedep').val();
        var tvadep = $(this).find('.tvadep').val();
        var line_amount_ht = parseFloat(pricedep);
        if (remisedep > 0) {
            var amount_remise = (line_amount_ht * remisedep) / 100;
            line_amount_ht = line_amount_ht - parseFloat(amount_remise);
        }
        // $(this).find('.item-amount').val(formatValue(line_amount_ht));
        var serializeddep = 'id='+iddep+'&pcc-id='+pccdep+'&price='+pricedep+'&remise='+remisedep+'&tva-id='+tvadep;
        $(this).find('.serializeddep').val(serializeddep);

        var moneyFields = $('.number');
        moneyFields.each(function() {
            format($(this));
        });
    });


}

function updateAmountHT() {
    var amount_ht = 0;
    var article_line = $('.article-list tbody').find('tr');
    article_line.each(function() {
        var item_amount = parseFloat($(this).find('.item-amount').html().replace(new RegExp(' ', "g"), ''));
        amount_ht = amount_ht + item_amount;
    });


    var depense_line = $('.depense-list tbody').find('tr');
    depense_line.each(function() {
        var itemAmountVal = $(this).find('.item-amount').val();

        var item_amount = 0;
        if(itemAmountVal !== undefined) {
            item_amount = parseFloat(itemAmountVal.replace(new RegExp(' ', "g"), ''));
        }
        amount_ht = amount_ht + item_amount;
    });

    $('#montant-ht').val(formatValue(amount_ht));
}

function updateRemise() {

    var montantHt = $('#montant-ht').val();
    var amount = 0;

    if(montantHt !== undefined){
        amount = parseFloat(montantHt.replace(new RegExp(' ', "g"), ''));
    }

    var remise = parseFloat($('#remise-ht').val());
    var amount_remise = (amount * remise) / 100;
    $('#montant-remise').val(formatValue(amount_remise));

    var tva = $('#montant-tva').val();
    var amount_tva = 0;

    if(tva !== undefined) {
        amount_tva = parseFloat(tva.replace(new RegExp(' ', "g"), ''));
    }

    if (remise > 0 && amount_tva > 0) {
        var amount_remise_tva = (amount_tva * remise) / 100;
        var new_tva = amount_tva - amount_remise_tva;
        $('#montant-tva').val(formatValue(new_tva));
    }
}

function updateTva() {
    var amount_tva = 0;
    var article_line = $('.article-list tbody').find('tr');
    article_line.each(function() {
        var item_amount = parseFloat($(this).find('.item-amount').html().replace(new RegExp(' ', "g"), ''));
        var item_tva_taux = parseFloat($(this).find('.tva').val());
        var item_tva_amount = (item_tva_taux * item_amount) / 100;
        amount_tva = amount_tva + item_tva_amount;
    });



    var depense_line = $('.depense-list tbody').find('tr');
    depense_line.each(function() {
        var itemAmountVal  = $(this).find('.item-amount').val();
        var item_amount = 0;
        if(itemAmountVal !== undefined) {
            item_amount = parseFloat(itemAmountVal.replace(new RegExp(' ', "g"), ''));
        }

        var item_tva_taux = parseFloat($(this).find('.tvadep').val());
        var item_tva_amount = (item_tva_taux * item_amount) / 100;
        amount_tva = amount_tva + item_tva_amount;
    });




    $('#montant-tva').val(formatValue(amount_tva));
}

function updateAmountTTC() {
    updateItemAmount();
    updateAmountHT();
    updateTva();
    updateRemise();
    var montantHt = $('#montant-ht').val();
    var amount = 0;
    if(montantHt !== undefined) {
        amount = parseFloat(montantHt.replace(new RegExp(' ', "g"), ''));
    }

    var montantRemise = $('#montant-remise').val();
    var remise = 0;
    if(montantRemise !== undefined) {
        remise = parseFloat(montantRemise.replace(new RegExp(' ', "g"), ''));
    }

    var montantTva = $('#montant-tva').val();
    var tva = 0;
    if(montantTva !== undefined) {
        tva = parseFloat(montantTva.replace(new RegExp(' ', "g"), ''));
    }

    var amount_ttc = (amount - remise) + tva;
    $('#montant-ttc').val(formatValue(amount_ttc));
}

function removeArticleVente() {
    var item = $('#article-to-remove').val();
    var uid = item.split(':')[0];
    var artventeid = parseInt(item.split(':')[1]);
    if (artventeid) {
        var output = '<input type="hidden" name="deleted-articles[]" value="'+artventeid+'" />';
        $('#articles-deleted').append(output);
    }
    $('.article-list tbody tr#'+uid).remove();
    $('#article-to-remove').val('');
    updateAmountTTC();
}

//COMMON

function selectToRemove(el) {
    var uid = $(el).attr('id');
    var id = $(el).attr('class');
    $('#article-to-remove').val(uid+':'+id);

    //Réinitialise
    $('.article-list tbody tr').css('background', '#FFFFFF');
    $('.article-list tbody tr').css('color', '#676a6c');

    $('.article-list tbody tr#'+uid).css('background', '#1CB394');
    $('.article-list tbody tr#'+uid).css('color', '#FFFFFF');

    $('.article-list tbody tr input').css('color', '#676a6c');
    $('.article-list tbody tr select').css('color', '#676a6c');
    $('.article-list tbody tr textarea').css('color', '#676a6c');
}
