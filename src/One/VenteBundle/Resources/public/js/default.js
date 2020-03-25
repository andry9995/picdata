/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

$(document).ready(function () {
    //var window_height = window.innerHeight;
    //$('#page-wrapper .panel-body').height(window_height - 170);
    $('#page-wrapper .panel-body').css('min-height', '720px');

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });
    
    //Chargement des prospects par défaut
    initAllFilter();
    setPeriod('all');
    //loadListProspect();
    
    //Chargement des clients au clic de l'onglet
    $(document).on('click', '#onglet-clients', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListClient();
            $('.tab-plus').removeClass('active');
        }
    });
    
    //Chargement des devis au clic de l'onglet
    $(document).on('click', '#onglet-devis', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListDevis();
            $('.tab-plus').removeClass('active');
        }
    });
    
    //Chargement des factures au clic de l'onglet
    $(document).on('click', '#onglet-factures', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListFacture();
            $('.tab-plus').removeClass('active');
        }
    });
    
    //Chargement des commandes au clic de l'onglet
    $(document).on('click', '#onglet-commandes', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListCommande();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    //Chargement des avoirs au clic de l'onglet
    $(document).on('click', '#onglet-avoirs', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListAvoir();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    //Chargement des projets au clic de l'onglet
    $(document).on('click', '#onglet-projets', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListProjet();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    //Chargement des produits et services au clic de l'onglet
    $(document).on('click', '#onglet-produit-service', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListArticle();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    //Chargement des encaissements au clic de l'onglet
    $(document).on('click', '#onglet-encaissements', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListEncaissement();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    //Chargement des paiements au clic de l'onglet
    $(document).on('click', '#onglet-paiements', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListPaiement();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
        }
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
    
    //Choix particulier/entreprise
    $(document).on('change', 'input[name="client-type"]', function() {
        var value = $(this).val();
        if (value === '1') {
            //Particulier
            $('.particulier-group').removeClass('hidden');
            $('.entreprise-group').addClass('hidden');
        } else {
            //Entreprise
            $('.entreprise-group').removeClass('hidden');
            $('.particulier-group').addClass('hidden');
        }
    });

    //Adresse livraison identique
    $(document).on('change', '#adresse-livraison-identique', function() {
        var identique = $(this).prop('checked');
        if (identique) {
            $('.adresse-livraison-group').addClass('hidden');
        } else {
            $('.adresse-livraison-group').removeClass('hidden');
        }
    });
    
    // Avancé
    $(document).on('click', '#toggle-advanced', function() {
       var icon = $('#toggle-advanced .fa');
       if (icon.hasClass('fa-caret-right')) {
           icon.addClass('fa-caret-down');
           icon.removeClass('fa-caret-right');
           $('.advanced-group').removeClass('hidden');
       } else {
           icon.addClass('fa-caret-right');
           icon.removeClass('fa-caret-down');
           $('.advanced-group').addClass('hidden');
       }
    });
    
    //Toggle TVA prioritaire
    $(document).on('change', '#tva-taux', function() {
        var value = $(this).val();
        if (value === '1') {
            $('#tva-prioritaire').prop('checked', false);
            $('#tva-prioritaire').attr('disabled', true);
        } else {
            $('#tva-prioritaire').removeAttr('disabled');
        }
    });
    
    //Réinitialise la couleur de bordure après validation du champ
    $(document).on('focus', '.form-control', function() {
        $(this).css('border-color', '#E5E6E7');
    });
    
    $(document).on('blur', '.number', function() {
        format($(this));
    });





    $(document).on('change', '#dossier', function(){
        $('#tab-ventes').find('a[href="#tab-client"]').trigger('click');
    });

    $(document).on('change', '#exercice', function(){

        var parentVal = $('#parent').val();
        var typeVal = $('#type').val();

        if(parentVal === undefined || parentVal === ''){
            switch (typeVal){
                case 'devis':
                    loadListDevis();
                    break;
                case 'facture':
                    loadListFacture();
                    break;
                case 'commande':
                    loadListCommande();
                    break;
                case 'paiement':
                    loadListPaiement();
                    break;
                case 'encaissement':
                    loadListEncaissement();
                    break;
                case 'avoir':
                    loadListAvoir();
                    break;
                default:
                    break;
            }
        }
        else{
            if(parentVal === 'client'){
                loadShowClient(getParentID());
            }
        }
    });






});

/**
 * Ouvre le modal
 * @returns {undefined}
 */
function openModal() {
    if (!$('#primary-modal').hasClass('in')) {
        $('#primary-modal').modal('show');
        setModalDraggable();
    }
}

/**
 * Ferme le modal
 * @returns {undefined}
 */
function closeModal() {
    if ($('#primary-modal').hasClass('in')) {
        $('#primary-modal').modal('hide');
    }
}

/**
 * Valide un champ requis
 * @param {dom} field : champ HTML
 * @returns {undefined}
 */
function validateField(field) {
    var valid = false;
    if(field.val() === '' || field.val() === '0') {
        field.css('border-color', 'red');
        $('.tabs-container').scrollTop();
    } else {
        valid = true;
    }
    return valid;
}

/**
 * Change le type d'affichage
 * @returns {undefined}
 */
function getView() {
    var view = $('#view').val();
    if (view === 'bloc') {
        $('.bloc-view').removeClass('hidden');
        $('.list-view').addClass('hidden');
    } else {
        $('.list-view').removeClass('hidden');
        $('.bloc-view').addClass('hidden');
    }
}

/**
 * Change le type d'affichage
 * @param {string} view
 * @returns {undefined}
 */
function updateView(view) {
    $('#view').val(view);
    getView();
}

/**
 * Réinitilisation des tous les filtres
 * @returns {undefined}
 */
function initAllFilter() {
    //Type
    setFilterType('all');
    
    //Stat
    setFilterStat('all');
    
    //Q
    initFilterQ();
    
    //Sort
    setSort('');
    
    //SortOrder
    setSortOrder('ASC');
    
    //View
    updateView('bloc');
    
    //Parent
    setParent('', '');
    setParent2('', '');
}

/**
 * Réinitialise le champ de recherche
 * @returns {undefined}
 */
function initFilterQ() {
    $('.search').val('');
    $('#q').val('');
    $('.init-search').addClass('hidden');
}

/**
 * Affiche le bouton de réinitialisation de recherche
 * @returns {undefined}
 */
function showInitSearch() {
    if ($('.search').val() !== '') {
        $('.init-search').removeClass('hidden');
    }
}

/**
 * Change le filtre recherche
 * @returns {undefined}
 */
function setFilterQ() {
    var search = $('.search').val();
    $('#q').val(search);
}

/**
 * Change le filtre type
 * @param {string} value
 * @returns {undefined}
 */
function setFilterType(value) {
    $('#type').val(value);
}

function getFilterType() {
    return $('#type').val();
}

function setFilterStat(value) {
    $('#stat').val(value);
}

function getFilterStat() {
    return $('#stat').val();
}

/**
 * Change le filtre order
 * @param {string} value
 * @returns {undefined}
 */
function setSort(value) {
    $('#sort').val(value);
}

/**
 * Change le filtre order direction
 * @param {string} value
 * @returns {undefined}
 */
function setSortOrder(value) {
    $('#sortorder').val(value);
}

/**
 * Change le parent de l'élément qui sera créé dans ce premier
 * @param {string} alias
 * @param {id} id
 * @returns {undefined}
 */
function setParent(alias, id) {
    $('#parent').val(alias);
    $('#parentid').val(id);
}

/**
 * Récupération de l'élément où on se trouve
 * @returns {jQuery}
 */
function getParent() {
    return $('#parent').val();
}

/**
 * Récupération de l'id de l'élement où on se trouve
 * @returns {jQuery}
 */
function getParentID() {
    return $('#parentid').val();
}

/**
 * Change le parent de l'élément qui sera créé dans ce premier
 * @param {string} alias
 * @param {id} id
 * @returns {undefined}
 */
function setParent2(alias, id) {
    $('#parent2').val(alias);
    $('#parentid2').val(id);
}

/**
 * Récupération de l'élément où on se trouve
 * @returns {jQuery}
 */
function getParent2() {
    return $('#parent2').val();
}

/**
 * Récupération de l'id de l'élement où on se trouve
 * @returns {jQuery}
 */
function getParentID2() {
    return $('#parentid2').val();
}

/**
 * Coche ou décoche tous les éléments
 * @returns {undefined}
 */
function toggleAll() {
    var checkall = $('.checkall');
    var elements = $('input.element');
    if (elements.length > 0) {
        if (checkall.is(':checked')) {
            elements.each(function() {
                $(this).prop('checked', true);
                if (getParent() === 'prospect' || getParent() === 'opportunite' || getParent() === 'client') {
                    $('.details-filter-bar').addClass('hidden');
                    $('.details-select-bar').removeClass('hidden');
                } else {
                    $('.filter-bar').addClass('hidden');
                    $('.select-bar').removeClass('hidden');
                }
            });
        } else {
            elements.each(function() {
                $(this).prop('checked', false);
                if (getParent() === 'prospect' || getParent() === 'opportunite' || getParent() === 'client') {
                    $('.details-filter-bar').removeClass('hidden');
                    $('.details-select-bar').addClass('hidden');
                } else {
                    $('.filter-bar').removeClass('hidden');
                    $('.select-bar').addClass('hidden');
                }
            });
        }
    }
    
    var seletectedtext = '';
    var checked = $('input.element:checked');
    if (getFilterType() === 'appel') {
        seletectedtext = checked.length+' Action séléctionnée(s)';
    } else if (getFilterType() === 'tache') {
        seletectedtext = checked.length+' Tâche pour Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'opportunite') {
        seletectedtext = checked.length+' Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'prospect') {
        seletectedtext = checked.length+' Prospect sélectionné(s)';
    } else if (getFilterType() === 'client') {
        seletectedtext = checked.length+' Client sélectionné(s)';
    } else if (getFilterType() === 'devis') {
        seletectedtext = checked.length+' Devis sélectionné(s)';
    } else if (getFilterType() === 'facture') {
        seletectedtext = checked.length+' Facture sélectionnée(s)';
    } else if (getFilterType() === 'commande') {
        seletectedtext = checked.length+' Commande sélectionnée(s)';
    } else if (getFilterType() === 'avoir') {
        seletectedtext = checked.length+' Avoir sélectionné(s)';
    } else if (getFilterType() === 'projet') {
        seletectedtext = checked.length+' Projet sélectionné(s)';
    } else if (getFilterType() === 'article') {
        seletectedtext = checked.length+' Article sélectionné(s)';
    } else if (getFilterType() === 'encaissement') {
        seletectedtext = checked.length+' Encaissement sélectionné(s)';
    }
    
    $('.elements-selected').html(seletectedtext);
}

/**
 * Coche ou décoche un élément
 * @returns {undefined}
 */
function toggleThis() {
    var seletectedtext = '';
    var elements = $('input.element');
    var checked = $('input.element:checked');
    
    if (checked.length > 0) {
        if (getParent() === 'prospect' || getParent() === 'opportunite' || getParent() === 'client') {
            $('.details-filter-bar').addClass('hidden');
            $('.details-select-bar').removeClass('hidden');
        } else {
            $('.filter-bar').addClass('hidden');
            $('.select-bar').removeClass('hidden');
        }
    } else {
        $('.checkall').prop('checked', false);
        if (getParent() === 'prospect' || getParent() === 'opportunite' || getParent() === 'client') {
            $('.details-filter-bar').removeClass('hidden');
            $('.details-select-bar').addClass('hidden');
        } else {
            $('.filter-bar').removeClass('hidden');
            $('.select-bar').addClass('hidden');
        }
    }
    
    if (checked.length === elements.length) {
        $('.checkall').prop('checked', true);
    }
    
    
    if (getFilterType() === 'appel') {
        seletectedtext = checked.length+' Action séléctionnée(s)';
    } else if (getFilterType() === 'tache') {
        seletectedtext = checked.length+' Tâche pour Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'opportunite') {
        seletectedtext = checked.length+' Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'prospect') {
        seletectedtext = checked.length+' Prospect sélectionné(s)';
    } else if (getFilterType() === 'client') {
        seletectedtext = checked.length+' Client sélectionné(s)';
    } else if (getFilterType() === 'devis') {
        seletectedtext = checked.length+' Devis sélectionné(s)';
    } else if (getFilterType() === 'facture') {
        seletectedtext = checked.length+' Facture sélectionnée(s)';
    } else if (getFilterType() === 'commande') {
        seletectedtext = checked.length+' Commande sélectionnée(s)';
    } else if (getFilterType() === 'avoir') {
        seletectedtext = checked.length+' Avoir sélectionné(s)';
    } else if (getFilterType() === 'projet') {
        seletectedtext = checked.length+' Projet sélectionné(s)';
    } else if (getFilterType() === 'article') {
        seletectedtext = checked.length+' Article sélectionné(s)';
    } else if (getFilterType() === 'encaissement') {
        seletectedtext = checked.length+' Encaissement sélectionné(s)';
    }
    
    $('.elements-selected').html(seletectedtext);
}

/**
 * Décoche tous les éléments
 * @returns {undefined}
 */
function uncheckAll() {
    var checkall = $('.checkall');
    var elements = $('input.element');
    checkall.prop('checked', false);
    if (elements.length > 0) {
        elements.each(function() {
            $(this).prop('checked', false);
        });
    }
    if (getParent() === 'prospect' || getParent() === 'opportunite' || getParent() === 'client') {
        $('.details-filter-bar').removeClass('hidden');
        $('.details-select-bar').addClass('hidden');
    } else {
        $('.filter-bar').removeClass('hidden');
        $('.select-bar').addClass('hidden');
    }
}

function resetTabContent() {
    $('#tab-client .panel-body').empty();
    $('#tab-devis .panel-body').empty();
    $('#tab-facture .panel-body').empty();
    $('#tab-commande .panel-body').empty();
    $('#tab-projet .panel-body').empty();
    $('#tab-produit-service .panel-body').empty();
    $('#tab-paiement .panel-body').empty();
    $('#tab-encaissement .panel-body').empty();
    $('#tab-avoir .panel-body').empty();
}

function prettyAddress(address) {
    var regex = /<br\s*[\/]?>/gi;
    return (address.replace(regex, "\n"));
}

function selectItem(el) {
    var uid = $(el).attr('id');
    var parent = $(el).parent();

    //Réinitialise
    parent.find('tr').css('background', '#FFFFFF');
    parent.find('tr').css('color', '#676a6c');

    parent.find('tr#'+uid).css('background', '#1CB394');
    parent.find('tr#'+uid).css('color', '#FFFFFF');

    parent.find('tr input').css('color', '#676a6c');
    parent.find('tr textarea').css('color', '#676a6c');
    parent.find('tr select').css('color', '#676a6c');
    parent.find('tr .glyphicon').css('color', '#676a6c');
    
    $('.option-button').removeClass('hidden');
}

function changeTab(tabid) {
    $('.nav-tabs a[href="#'+tabid+'"]').tab('show');
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Drew Noakes
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function format(valeur,decimal,separateur) {
    // formate un chiffre avec 'decimal' chiffres après la virgule et un separateur
    var deci=Math.round( Math.pow(10,decimal)*(Math.abs(valeur)-Math.floor(Math.abs(valeur)))) ; 
    var val=Math.floor(Math.abs(valeur));
    if ((decimal==0)||(deci==Math.pow(10,decimal))) {val=Math.floor(Math.abs(valeur)); deci=0;}
    var val_format=val+"";
    var nb=val_format.length;
    for (var i=1;i<4;i++) {
        if (val>=Math.pow(10,(3*i))) {
            val_format=val_format.substring(0,nb-(3*i))+separateur+val_format.substring(nb-(3*i));
        }
    }
    if (decimal>0) {
        var decim=""; 
        for (var j=0;j<(decimal-deci.toString().length);j++) {decim+="0";}
        deci=decim+deci.toString();
        val_format=val_format+"."+deci;
    }
    if (parseFloat(valeur)<0) {val_format="-"+val_format;}
    return val_format;
}

function format(el) {
    //return valeur.replace(/\B(?=(?:\d{3})+(?!\d))/g, ' ');
    var separateur = ' ';
    var o = $(el).val().replace(new RegExp(separateur, "g"), '');
    $(el).val(o.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1'+separateur));
}

function formatValue(value) {
    //return valeur.replace(/\B(?=(?:\d{3})+(?!\d))/g, ' ');
    var separateur = ' ';
    var o = value.toString().replace(new RegExp(separateur, "g"), '');
    return o.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1'+separateur);
}


function showInfoByResponse(response){
    if(response ===''){
        show_info('Notice', 'Il faut choisir un dossier', 'warning');
    }
}

function showExerciceError(){
    show_info('Attention', 'Il faut choisir une exercice','warning');
}

function setModalDraggable(){
    var minWidth = $('.modal-dialog').width();
    $('.modal-content').resizable({
        minWidth: minWidth + 10
    });
    $('.modal-dialog').draggable();
}