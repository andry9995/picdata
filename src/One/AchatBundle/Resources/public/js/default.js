$(document).ready(function(){

    $('#page-wrapper .panel-body').css('min-height', '720px');

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green'
    });

    //Chargement des prospects par défaut
    initAllFilter();
    setPeriod('all');

    //Choix particulier/entreprise
    $(document).on('change', 'input[name="fournisseur-type"]', function() {
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

    //Chargement des clients au clic de l'onglet
    $(document).on('click', '#onglet-fournisseur', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListFournisseur();
            $('.tab-plus').removeClass('active');
        }
    });

    $(document).on('click', '#onglet-facture', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListFacture();
            $('.tab-plus').removeClass('active');
        }
    });

    //Chargement des commandes au clic de l'onglet
    $(document).on('click', '#onglet-commande', function() {
        if ($(this).hasClass('active')) {
            initAllFilter();
            setPeriod('all');
            loadListCommande();
            $('.tab-plus').removeClass('active');
            $(this).addClass('active');
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

    //Changement du type de vue
    $(document).on('click', '.view-list', function() {
        updateView('list');
    });

    $(document).on('click', '.view-bloc', function() {
        updateView('bloc');
    });


    //Pour le champ recherche
    $(document).on('keyup', 'input.search', function() {
        if ($(this).val() !== '') {
            $('.init-search').removeClass('hidden');
        } else {
            $('.init-search').addClass('hidden');
        }
    });

});


/**
 * Ferme le modal
 * @returns {undefined}
 */
function closeModal() {
    if ($('#primary-modal').hasClass('in')) {
        $('#primary-modal').modal('hide');
    }
}



function getFilterType() {
    return $('#type').val();
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
 * Ouvre le modal.
 * @returns {undefined}
 */
function openModal() {
    if (!$('#primary-modal').hasClass('in')) {
        $('#primary-modal').modal('show');
    }
}

/**
 *
 */
function resetTabContent() {
    $('#tab-fournisseur .panel-body').empty();
    $('#tab-depense .panel-body').empty();
    $('#tab-facture .panel-body').empty();
    $('#tab-devis .panel-body').empty();
    $('#tab-commande .panel-body').empty();
    $('#tab-paiement .panel-body').empty();
    $('#tab-avoir .panel-body').empty();
}


function setFilterStat(value) {
    $('#stat').val(value);
}


/**
 * Change le filtre type
 * @param {string} value
 * @returns {undefined}
 */
function setFilterType(value) {
    $('#type').val(value);
}

/* Change le filtre recherche
* @returns {undefined}
*/
function setFilterQ() {
    var search = $('.search').val();
    $('#q').val(search);
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
 * Change le filtre order
 * @param {string} value
 * @returns {undefined}
 */
function setSort(value) {
    $('#sort').val(value);
}

/**
 *
 * @param response
 */
function showInfoByResponse(response){
    if(response ===''){
        show_info('Notice', 'Il faut choisir un dossier', 'warning');
    }
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


function prettyAddress(address) {
    var regex = /<br\s*[\/]?>/gi;
    return (address.replace(regex, "\n"));
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
    if (getParent() === 'fournisseur') {
        $('.details-filter-bar').removeClass('hidden');
        $('.details-select-bar').addClass('hidden');
    } else {
        $('.filter-bar').removeClass('hidden');
        $('.select-bar').addClass('hidden');
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

function formatValue(value) {
    //return valeur.replace(/\B(?=(?:\d{3})+(?!\d))/g, ' ');
    var separateur = ' ';
    var o = value.toString().replace(new RegExp(separateur, "g"), '');
    return o.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1'+separateur);
}

function format(el) {
    //return valeur.replace(/\B(?=(?:\d{3})+(?!\d))/g, ' ');
    var separateur = ' ';
    var o = $(el).val().replace(new RegExp(separateur, "g"), '');
    $(el).val(o.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1'+separateur));
}