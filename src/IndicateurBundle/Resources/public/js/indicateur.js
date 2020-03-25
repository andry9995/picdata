/**
 * Created by SITRAKA on 12/07/2016.
 */

/**
 * act = 3 : affichage insertion indicateur
 * act = 4 : ajout nouveau
 * act = 5 : suppresion
 * act = 6 : affichage edition indicateur
 * act = 7 : modif indicateur
 */

//btn add indicateur
$(document).on('click','.js_group_add_child',function(){
    edit_indicateur($(this));
});
//btn edit indicateur
$(document).on('click','.js_indicateur_edit',function(){
    edit_indicateur($(this));
});
//btn save indicateur
$(document).on('click','.js_btn_save_indicateur',function(){
    save_indicateur();
});
//type graphe change status
$(document).on('click','.js_type_graphe_item',function(){
    change_status_graphe($(this));
});
$(document).on('click','.js_indicateur_delete',function(){
    $('.js_indicateur_edited').removeClass('js_indicateur_edited');
    $('#js_temp_action').val(5);
    $(this).parent().parent().parent().addClass('js_indicateur_edited');
    save_indicateur();
});


/**
 * act = 3 add child , act = 6 modif
 * @param span
 */
function edit_indicateur(span)
{
    act = (span.hasClass('js_group_add_child')) ? 3 : 6;
    $('#js_temp_action').val(act);
    $('.js_panel_group_indicateur').removeClass('js_group_edited');

    if(act == 3)
    {
        span.parent().parent().parent().addClass('js_group_edited');
        id_group = span.parent().parent().parent().attr('data-id');
        libelle_group = span.parent().parent().find('.js_group_libelle').text().trim();
        id = $('#js_zero_boost').val();
    }
    else
    {
        //if(span.hasClass('.js_indicateur_delete')) act = 5;
        $('.js_indicateur_edited').removeClass('js_indicateur_edited');
        span.parent().parent().parent().addClass('js_indicateur_edited');
        id_group = '';
        libelle_group = '';
        id = span.parent().parent().parent().attr('data-id');
    }

    var lien = Routing.generate('indicateur_edit');
    $.ajax({
        data: { id_group:id_group , action : act , id:id },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if(act == 3) titre = '<i class="fa fa-plus-circle"></i> <span>Ajout item dans ' + libelle_group + '</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i> <span>Modification de ' + $('.js_indicateur_edited').text() + '</span>';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
        }
    });
}

function save_indicateur()
{
    act = parseInt($('#js_temp_action').val());
    act = (act == 3) ? 4 : act;

    id_indicateur = $('#js_zero_boost').val();
    if(act == 6 || act == 5)
    {
        id_indicateur = $('.js_indicateur_edited').attr('data-id');
        if(act == 6) act = 7;
    }

    if(act != 5)
    {
        if($('#js_form_edit_indicateur .js_indicateur_libelle').val().trim() == '')
        {
            show_info('ERREUR','NOM VIDE','error');
            return;
        }
    }
    libelle = (act != 5) ? $('#js_form_edit_indicateur .js_indicateur_libelle').val().trim() : '';
    id_group = $('.js_group_edited').attr('data-id');

    objects = new Array();

    $('#js_form_edit_indicateur span.js_type_graphe_item').each(function(){
        objects.push({ idTypeGraphe: $(this).attr('data-id') , relation: $(this).attr('data-id_relation') , status: $(this).hasClass('active')});
    });

    lien = Routing.generate('indicateur_edit');
    $.ajax({
        data: { action:act,libelle:libelle,id_group:id_group,id_indicateur:id_indicateur,objects:JSON.stringify(objects) },
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
            if(reponse == 1)
            {
                if(act == 7) $('.js_indicateur_edited .js_indicateur_libelle').text(libelle);
                else if(act == 5) $('.js_indicateur_edited').parent().parent().remove();
                close_modal();
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                if(act == 4) rafraichir_group_indicateur();
            }
            else
            {
                if(act == 5) show_info('ERREUR','INDICATEUR NO VIDE','error');
                else if(act == 4 || act == 7) show_info('ERREUR','INDICATEUR DEJA EXISTANT','error');
            }
        }
    });
}

function change_status_graphe(span)
{
    if(span.hasClass('active')) span.removeClass('active');
    else
    {
        if(span.hasClass('js_is_table')) $('span.js_type_graphe_item').removeClass('active');
        else $('.js_is_table').removeClass('active');
        span.addClass('active');
    }
}

function rafraichir_group_indicateur()
{
    id_group = $('.js_group_edited').attr('data-id');
    index = parseInt($('.js_group_edited').attr('data-index').trim());
    lien = Routing.generate('indicateur_listes');
    $.ajax({
        data: { id_group:id_group, index:index },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('.js_group_edited').parent().parent().find('div.panel-collapse').find('div.panel-body').html(data);
        }
    });
}