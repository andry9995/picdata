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
    // loadListProspect();

    //Chargement des prospects au clic de l'onglet
    $(document).on('click', '#onglet-prospects', function () {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListProspect();
        }
    });
    //Chargement des opportunités au clic de l'onglet
    $(document).on('click', '#onglet-opportunites', function () {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListOpportunite();
        }
    });
    //Chargement des taches au clic de l'onglet
    $(document).on('click', '#onglet-taches', function () {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListTache();
        }
    });
    //Chargement des appels au clic de l'onglet
    $(document).on('click', '#onglet-appel', function () {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListAppel();
        }
    });

    //Pour le champ recherche
    $(document).on('keyup', 'input.search', function () {
        if ($(this).val() !== '') {
            $('.init-search').removeClass('hidden');
        } else {
            $('.init-search').addClass('hidden');
        }
    });

    //Changement du type de vue
    $(document).on('click', '.view-list', function () {
        updateView('list');
    });
    $(document).on('click', '.view-bloc', function () {
        updateView('bloc');
    });
    $(document).on('click', '.view-chart', function () {
        updateView('chart');
    });
    $(document).on('click', '.view-mesure', function () {
        updateView('mesure');
    });

    //Choix particulier/entreprise
    $(document).on('change', 'input[name="prospect-type"]', function () {
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
    $(document).on('change', '#adresse-livraison-identique', function () {
        var identique = $(this).prop('checked');
        if (identique) {
            $('.adresse-livraison-group').addClass('hidden');
        } else {
            $('.adresse-livraison-group').removeClass('hidden');
        }
    });

    // Avancé
    $(document).on('click', '#toggle-advanced', function () {
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
    $(document).on('change', '#tva-taux', function () {
        var value = $(this).val();
        if (value === '1') {
            $('#tva-prioritaire').prop('checked', false);
            $('#tva-prioritaire').attr('disabled', true);
        } else {
            $('#tva-prioritaire').removeAttr('disabled');
        }
    });

    //Réinitialise la couleur de bordure après validation du champ
    $(document).on('focus', '.form-control', function () {
        $(this).css('border-color', '#E5E6E7');
    });

    $(document).on('blur', '.number', function () {
        format($(this));
    });

    $(document).on('change', '#dossier', function () {
        $('#tab-prospects').find('a[href="#tab-prospect"]').trigger('click');
    });

});

/**
 * Ouvre le modal.
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
 * Ouvre le modal.
 * @returns {undefined}
 */
function openSecondModal() {
    if (!$('#secondary-modal').hasClass('in')) {
        $('#secondary-modal').modal('show');
    }
}

/**
 * Ferme le modal
 * @returns {undefined}
 */
function closeSecondModal() {
    if ($('#secondary-modal').hasClass('in')) {
        $('#secondary-modal').modal('hide');
    }
}

/**
 * Valide un champ requis
 * @param {dom} field : champ HTML
 * @returns {undefined}
 */
function validateField(field) {
    var valid = false;
    if(field.val() === '') {
        field.css('border-color', 'red');
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
        $('.chart-view').addClass('hidden');
        $('.mesure-view').addClass('hidden');
    } else if (view === 'list') {
        $('.list-view').removeClass('hidden');
        $('.bloc-view').addClass('hidden');
        $('.chart-view').addClass('hidden');
        $('.mesure-view').addClass('hidden');
    } else if (view === 'chart') {
        $('.chart-view').removeClass('hidden');
        $('.bloc-view').addClass('hidden');
        $('.list-view').addClass('hidden');
        $('.mesure-view').addClass('hidden');
    } else if (view === 'mesure') {
        $('.mesure-view').removeClass('hidden');
        $('.bloc-view').addClass('hidden');
        $('.list-view').addClass('hidden');
        $('.chart-view').addClass('hidden');
    } else {
        $('.bloc-view').removeClass('hidden');
        $('.list-view').addClass('hidden');
        $('.chart-view').addClass('hidden');
        $('.mesure-view').addClass('hidden');
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

    //Archive
    setFilterArchive('unarchived');

    //Activité
    setFilterActivity('all');

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

function setFilterActivity(value){
    $('#activity').val(value)
}

function setFilterArchive(value){
    $('#archive').val(value);
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
        seletectedtext = checked.length+' A faire séléctionné(s)';
    } else if (getFilterType() === 'tache') {
        seletectedtext = checked.length+' Tâche pour Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'opportunite') {
        seletectedtext = checked.length+' Opportunité sélectionnée(s)';
    } else if (getFilterType() === 'prospect') {
        seletectedtext = checked.length+' Prospect sélectionné(s)';
    } else if (getFilterType() === 'devis') {
        seletectedtext = checked.length+' Devis sélectionné(s)';
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
    } else if (getFilterType() === 'devis') {
        seletectedtext = checked.length+' Devis sélectionné(s)';
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
    $('#tab-prospect .panel-body').empty();
    $('#tab-opportunite .panel-body').empty();
    $('#tab-tache .panel-body').empty();
    $('#tab-appel .panel-body').empty();
}

function prettyAddress(address) {
     var regex = /<br\s*[\/]?>/gi;
    return (address.replace(regex, "\n"));
}

function format_old(valeur,decimal,separateur) {
    // formate un chiffre avec 'decimal' chiffres après la virgule et un separateur
    valeur = valeur.replace(' ', '');
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

function showInfoByResponse(response){
    if(response ===''){
        show_info('Notice', 'Il faut choisir un dossier', 'warning');
    }
}

function formatValue(value) {
    //return valeur.replace(/\B(?=(?:\d{3})+(?!\d))/g, ' ');
    var separateur = ' ';
    var o = value.toString().replace(new RegExp(separateur, "g"), '');
    return o.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1'+separateur);
}

function setModalDraggable(){
    var minWidth = $('.modal-dialog').width();
    $('.modal-content').resizable({
        minWidth: minWidth + 10
    });
    $('.modal-dialog').draggable();
}