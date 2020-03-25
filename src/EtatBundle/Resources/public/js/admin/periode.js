/**
 * Created by SITRAKA on 10/05/2017.
 */
$(document).on('click','.js_show_periode_admin',function(){
    $('.indicateur_periode_edited').removeClass('indicateur_periode_edited');
    var indicateur = $(this).closest('.js_indicateur_sortable').addClass('indicateur_periode_edited').attr('data-id');

    $.ajax({
        data: { indicateur: indicateur  },
        url: Routing.generate('etat_periodes'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var animated = 'bounceInRight',
                titre = 'PERIODES';
            show_modal(data,titre,animated);
        }
    });
});

$(document).on('click','.js_validate_periodes',function(){
    var div_indicateur = $('.indicateur_periode_edited'),
        indicateur = div_indicateur.attr('data-id'),
        is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
        periode = '';
    $('#js_indicateur_periode .checkbox input').each(function(){
        if($(this).is(':checked')) periode += '1';
        else periode += '0';
    });

    $.ajax({
        data: {
            indicateur:indicateur,
            is_etat:is_etat,
            periode:periode
        },
        type: 'POST',
        url: Routing.generate('etat_change_periodes'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if(parseInt(data) == 1)
            {
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
            }
            else
            {
                show_info('ERREUR','UNE ERREUR C EST PRODUITE PENDANT LA MODIFICATION','error');
            }
            close_modal();
        }
    });
});