/**
 * Created by SITRAKA on 11/07/2016.
 */
$(document).on('click','#js_add_groupe_indicateur',function(){
    $('#js_temp_action').val(0);
    titre = '<i class="fa fa-plus-circle"></i> <span>Nouveau Indicateur</span>';
    animated = 'bounceInRight';
    show_modal($('#js_temp_form').html(),titre,animated);
    $('input.js_group_libelle').val('');
});
$(document).on('click','#indicateur_group_acordion .js_group_indicateur_item span.js_group_edit',function(){
    $('.js_panel_group_indicateur').removeClass('js_group_edited');
    $(this).parent().parent().parent().parent().parent().addClass('js_group_edited');
    $('#js_temp_action').val(1);
    titre = '<i class="fa fa-pencil-square-o"></i> <span>Modification Indicateur</span>';
    animated = 'bounceInRight';
    show_modal($('#js_temp_form').html(),titre,animated);
    $('input.js_group_libelle').val($(this).parent().parent().find('.js_group_libelle').text());
});
$(document).on('click','.js_group_delete',function(){
    $('#js_temp_action').val(2);
    $('.js_group_edited').removeClass('js_group_edited');
    $(this).parent().parent().parent().parent().parent().addClass('js_group_edited');
    save_group($(this));
});

$(document).on('click','.js_btn_save',function(){
    save_group($(this));
});
$(document).on('click','.js_btn_cancel',function(){
    close_modal();
});

function save_group(span)
{
    act = parseInt($('#js_temp_action').val());
    if(act != 2) if(!libelleIsValid(span)) return;
    libelle = span.parent().parent().parent().find('.js_group_libelle').val().trim();
    id = (act != 0) ? $('#indicateur_group_acordion .js_group_edited').find('.js_group_indicateur_item').attr('data-id') : '0';

    lien = Routing.generate('indicateur_group_edit');
    verrou_fenetre(true);
    $.ajax({
        data: { action:act, id:id ,libelle:libelle},
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
                if(act == 0)
                {
                    show_info('SUCCES','INDICATEUR ' + libelle + ' AJOUTE');
                    location.reload();
                }
                if(act == 1)
                {
                    $('#indicateur_group_acordion .js_group_edited .js_group_libelle').text(libelle);
                    show_info('SUCCES','INDICATEUR BIEN MODIFIE');
                }
                if(act == 2)
                {
                    $('#indicateur_group_acordion .js_group_edited').remove();
                    show_info('SUCCES','INDICATEUR BIEN SUPPRIME');
                }

                close_modal();
            }
            else if(reponse == 0)
            {
                if(act == 0 || act == 1) show_info('ERREUR','INDICATEUR DEJA EXISTANT','error');
                if(act == 2) show_info('INDICATEUR NON VIDE','SUPPRESSION IMPOSSIBLE','error');
            }
        }
    });
}

function libelleIsValid(span)
{
    if(span.parent().parent().parent().find('.js_group_libelle').val().trim() == '')
    {
        span.parent().parent().parent().find('.js_group_libelle').parent().parent().addClass('has-error');
        show_info('ERREUR','LE NOM NE PEUT PAS ETRE VIDE','error');
        return false;
    }

    span.parent().parent().parent().find('.js_group_libelle').parent().parent().removeClass('has-error');
    return true;
}