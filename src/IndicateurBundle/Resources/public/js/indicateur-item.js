/**
 * Created by SITRAKA on 21/07/2016.
 */
$(document).on('click','.js_indicateur_add_child',function(){
    edit_indicateur_item($(this));
});

/**
 * radio change
 */
$(document).on('change','.js_radio_type',function(){
    change_type();
});

/**
 * click ajouter operande
 */
$(document).on('click','#js_add_rubrique',function(){
    add_rubrique();
});

/**
 * click save indicateur item
 */
$(document).on('click','#js_btn_save_indicateur_item',function(){
    save_rubrique_item();
});

/**
 * modif indicateur item
 */
$(document).on('click','.js_indicateur_item_td',function(){
    $('.js_indicateur_item_edited').removeClass('js_indicateur_item_edited');
    $(this).addClass('js_indicateur_item_edited');
    edit_indicateur_item($(this));
});

/**
 * add row to indicateur
 */
$(document).on('click','.js_add_cell',function(){
    add_row_in_indicateur($(this));
});

$(document).on('click','.js_delete_indicateur_item',function(){
    edit_indicateur_item($(this));
});

/**
 * show formulaire add indicateur item
 * @param span
 */
function edit_indicateur_item(span)
{
    act = 0;
    row = col = 0;

    if(span.hasClass('js_indicateur_add_child'))
    {
        id_indicateur = span.parent().parent().parent().attr('data-id');
        id_indicateur_item = $('#js_zero_boost').val();

        $('.js_indicateur_edited').removeClass('js_indicateur_edited');
        span.parent().parent().parent().addClass('js_indicateur_edited');
        row = col = 0;

        rafraichir_indicateur = true;
    }
    else if(span.hasClass('js_delete_indicateur_item'))
    {
        act = 5;
        $('.js_indicateur_item_edited').removeClass('js_indicateur_item_edited');
        span.parent().parent().find('.js_indicateur_item_td').addClass('js_indicateur_item_edited');
        id_indicateur_item = $('.js_indicateur_item_edited').attr('data-id');
        id_indicateur = $('#js_zero_boost').val();
    }
    else
    {
        id_indicateur = $('#js_zero_boost').val();
        id_indicateur_item = span.attr('data-id');

        rafraichir_indicateur = false;

        if(id_indicateur_item.trim() == '')
        {
            $('.js_indicateur_edited').removeClass('js_indicateur_edited');
            span.parent().parent().parent().parent().parent().parent().find('.js_indicateur_item').addClass('js_indicateur_edited');
            id_indicateur = $('.js_indicateur_edited').attr('data-id');
            id_indicateur_item = $('#js_zero_boost').val();
            row = parseInt(span.attr('data-row'));
            col = parseInt(span.attr('data-col'));
        }
    }

    lien = Routing.generate('indicateur_item_edit');
    $.ajax({
        data: { action:act , id_indicateur:id_indicateur , id_indicateur_item:id_indicateur_item },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            animated = 'bounceInRight';
            if(id_indicateur_item = $('#js_zero_boost').val())
            {
                titre = '<i class="fa fa-plus-circle"></i> <span>Ajout item dans ' + $('.js_indicateur_edited').find('.js_indicateur_libelle').text().trim() + '</span>';
            }
            else
            {
                titre = '<i class="fa fa-pencil-square-o"></i> <span>Modification ' + span.text().trim() + '</span>';
            }

            if(act == 5)
            {
                if(parseInt(data.trim()) == 1)
                {
                    $('.js_indicateur_item_edited').parent().remove();
                    show_info('SUCCES','INDICATEUR BIEN SUPPRIME');
                }
                else
                {
                    show_info('ERREUR','INDICATEUR UTILISE ou INDICATEUR UTILISE','error');
                }
            }
            else
            {
                show_modal(data,titre,animated);
                charger_rubriques();
            }
        }
    });
}

/**
 * select in editeur formule
 */
$(document).on('click','#js_formule',function(){
    $('#js_focus').focus();
});

/**
 * select operateur or operande in editeur formule
 */
$(document).on('click','.operateur',function(){
    move_blink($(this));
});

/**
 * keydown number,operateur
 */
$(window).keydown(function(e) {
    if( !($('#js_form_edit_indicateur_item').length > 0) ||
        $('#js_indicateur_item_libelle').is(':focus') ||
        $('#js_formule_libelle').is(':focus') ||
        $('#js_rubrique_select').is(':focus') ||
        $('#js_rubrique_type').is(':focus') ||
        $('#js_indicateur_item_unite').is(':focus')) return;
    e.preventDefault();

    code = -1;

    //chiffre
    if(e.key == '0') code = 96;
    else if(e.key == '1') code = 97;
    else if(e.key == '2') code = 98;
    else if(e.key == '3') code = 99;
    else if(e.key == '4') code = 100;
    else if(e.key == '5') code = 101;
    else if(e.key == '6') code = 102;
    else if(e.key == '7') code = 103;
    else if(e.key == '8') code = 104;
    else if(e.key == '9') code = 105;
    //operateur
    else if(e.key == '.') code = 110;
    else if(e.key == '/') code = 111;
    else if(e.key == '*') code = 106;
    else if(e.key == '-') code = 109;
    else if(e.key == '+') code = 107;
    else if(e.key == ',') code = 188;
    else if(e.key == '(') code = 112;
    else if(e.key == ')') code = 113;
    else if(e.key == ';') code = 114;
    //mouvement
    else
    {
        code = parseInt(e.keyCode || e.which);
        if(code == 37) code = 0;
        if(code == 39) code = 1;
        if(code == 46) code = 2;
        if(code == 8) code = 3;
    }

    if(code >= 96 && code <= 114 || code >= 0 && code <= 3)
    {
        if(code >= 96 && code <= 114)
            $( "<span class='operateur'>" + e.key + "</span>" ).insertBefore( "#js_blink_formule" );
        else if(code == 0)
        {
            span = $('#js_blink_formule').prev('.operateur');
            move_blink(span,'ib');
        }
        else if(code == 1)
        {
            span = $('#js_blink_formule').next('.operateur');
            move_blink(span, 'ia');
        }
        else if(code == 2) move_blink(null,'da');
        else if(code == 3) move_blink(null,'db');
    }
});

/**
 * deplacement curseur
 *
 * @param span
 * @param deplacement
 */
function move_blink(span,deplacement)
{
    deplacement = (typeof deplacement !== 'undefined') ? deplacement : 'ib';
    blink = '<span class="blink" id="js_blink_formule">|</span>';

    if(deplacement == 'ib')
    {
        $('.blink').remove();
        $(blink).insertBefore(span);
    }
    else if(deplacement == 'ia')
    {
        $('.blink').remove();
        $(blink).insertAfter(span);
    }
    else if(deplacement == 'da')
    {
        $('#js_blink_formule').next('.operateur').remove();
    }
    else if(deplacement == 'db')
    {
        $('#js_blink_formule').prev('.operateur').remove();
    }
}

/**
 * change type rubrique
 */
function change_type()
{
    $('#js_formule').html('<span class="blink" id="js_blink_formule">|</span>');
    charger_rubriques();
}

/**
 * charger rubrique
 */
function charger_rubriques()
{
    lien = Routing.generate('rubriques_sel')+'/'+
        parseInt($("#js_group_rubrique input[type='radio']:checked").attr('data-val'));
    $.ajax({
        data: {  },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            $('#js_conteneur_rubrique').html(data);
        }
    });
}

/**
 * add operande
 */
function add_rubrique()
{
    libelle = $('#js_rubrique option:selected').text();
    id_operande = $('#js_rubrique').val().trim();
    variation = parseInt($('#js_varation_n').val());
    class_v = $('#js_varation_n option:selected').attr('data-class');
    operande_span = '<span class="label '+ class_v +' operateur operande" data-variation="'+variation+'" style="margin-right: 1px !important;margin-left: 1px !important;" data-id="'+id_operande+'">'+libelle+'</span>';
    $(operande_span).insertBefore($('#js_blink_formule'));
}

/**
 * save rubrique item
 */
function save_rubrique_item()
{
    if(!indicateur_item_is_valid()) return;

    id_indicateur = $('#js_form_edit_indicateur_item').attr('data-id_indicateur').trim();
    id_indicateur_item = $('#js_form_edit_indicateur_item').attr('data-id_indicateur_item').trim();
    libelle = $('#js_indicateur_item_libelle').val().trim().sansAccent();
    type_rubrique = parseInt($("#js_group_rubrique input[type='radio']:checked").attr('data-val'));
    type_operateur = parseInt($("#js_type_operateur input[type='radio']:checked").attr('data-val'));
    libelle_formule = $('#js_formule_libelle').val().trim().sansAccent().toUpperCase();
    unite_formule = $('#js_indicateur_item_unite').val().trim();

    formule = '';
    operandes = new Array();
    $('#js_formule .operateur').each(function(){
        if($(this).hasClass('operande'))
        {
            formule += '#';
            operandes.push({id:$(this).attr('data-id') , variation:$(this).attr('data-variation')});
        }
        else
        {
            formule += $(this).text().trim();
        }
    });

    lien = Routing.generate('indicateur_item_edit');
    $.ajax({
        data: { action:1 , id_indicateur:id_indicateur , id_indicateur_item:id_indicateur_item , libelle_formule:libelle_formule ,
                operandes:JSON.stringify(operandes) , formule:formule , libelle:libelle , type_rubrique:type_rubrique ,
                row:row , col:col , type_operateur:type_operateur ,
                unite: unite_formule },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            reponse = parseInt(data);
            if(reponse == -1)
            {
                show_info('ERREUR','INDICATEUR DEJA EXISTANT','error');
                return;
            }

            $('.js_indicateur_item_edited').attr('data-id',data.trim()).text(libelle);
            if(rafraichir_indicateur) charger_indicateur_items();
            close_modal();
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
        }
    });
}

/**
 * @returns {boolean}
 */
function indicateur_item_is_valid()
{
    if($('#js_indicateur_item_libelle').val().trim() == '')
    {
        show_info('Erreur','NOM VIDE','warning');
        return false;
    }
    if($('.operateur').length <= 0)
    {
        show_info('Erreur','FORMULE VIDE','warning');
        return false;
    }
    if($('.operande').length <= 0)
    {
        show_info('Erreur','VOTRE FORMULE N\'A PAS DE RUBRIQUE','warning');
        return false;
    }
    /*if($('#js_formule_libelle').val().trim() == '')
    {
        show_info('Erreur','ENTREZ LE NOM DE LA FORMULE','warning');
        return false;
    }*/
    return true;
}

/**
 * add row indicateur
 *
 * @param span
 */
function add_row_in_indicateur(span)
{
    $('.js_indicateur_edited').removeClass('js_indicateur_edited');
    span.parent().parent().parent().parent().parent().parent().parent().find('.js_indicateur_item').addClass('js_indicateur_edited');
    act = (span.hasClass('js_add_row')) ? 0 : 1;
    id_indicateur = span.parent().attr('data-id_indicateur');
    lien = Routing.generate('indicateur_add_cell');
    $.ajax({
        data: { action:act , id_indicateur:id_indicateur },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('SUCCES',((act == 0) ? 'LIGNE ' : 'COLONNE ') + 'BIEN AJOUTE');
            charger_indicateur_items();
        }
    });
}

function charger_indicateur_items()
{
    indicateur = $('.js_indicateur_edited').attr('data-id');
    lien = Routing.generate('indicateur_item_listes');
    $.ajax({
        data: { action:act , id_indicateur:id_indicateur },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('.js_indicateur_edited').parent().parent().find('div.panel-collapse>div.panel-body').html(data);
        }
    });
}