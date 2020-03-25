/**
 * Created by SITRAKA on 06/07/2017.
 */
$(document).on('click','.js_show_param_anciennete',function(){
    show_parametrage_agee($(this));
});

$(document).on('click','.js_eb_delete_periode_agee',function(){
    $(this).closest('tr').remove();
});

$(document).on('click','#js_eb_ajout_periode_agee',function(){
    add_periode_agee();
});

$(document).on('click','#js_eb_valider_periode',function(){
    valide_periode_agee();
});

$(document).on('click','#js_eb_defaut_periode',function(){
    charger_date_annciennete();
    close_modal();
    $('#js_container_tabs>.tab-content>div.active .js_show_param_anciennete').click();
    //show_parametrage_agee();
});

function valide_periode_agee()
{
    var date_anciennete = $('#js_date_anciennete_choise').datepicker('getDate'),
        periode_agee = [];

    $('td.js_eb_periode_agee').each(function(){
        var valeur = parseInt($(this).text().trim());
        if(!periode_agee.in_array(valeur)) periode_agee.push(valeur);
    });
    periode_agee.sort(function(a, b) {
        return (a < b);
    });

    var params_container = $('#js_container_tabs').find('.tab-content>div.active').find('.js_container_params');
    params_container.find('.js_cl_date_anciennete').val(date_anciennete.toMysqlFormat());
    params_container.find('.js_cl_anciennetes').val(JSON.stringify(periode_agee));

    close_modal();
    go();
}

function add_periode_agee()
{
    var periode_agee = [];
    $('#js_eb_ajout_periode_agee').closest('.modal-body').find('td.js_eb_periode_agee').each(function(){
        periode_agee.push(parseInt($(this).text().trim()));
    });

    $('#js_eb_periode_nouveau').blur();
    var nouveau_periode = parseInt($('#js_eb_periode_nouveau').val().trim());
    if(isNaN(nouveau_periode) || periode_agee.in_array(nouveau_periode))
    {
        $('#js_eb_periode_nouveau').closest('.form-group').addClass('has-warning');
        return;
    }
    else
        $('#js_eb_periode_nouveau').closest('.form-group').removeClass('has-warning');

    $('<tr><td class="js_eb_periode_agee">'+nouveau_periode+'</td><td class="pointer"><i class="fa fa-trash js_eb_delete_periode_agee"></i></td></tr>').insertBefore($('#js_eb_last_line_agee'));
}

function show_parametrage_agee(btn)
{
    var container_params = btn.closest('.js_container_params'),
        periode_agee = container_params.find('.js_cl_anciennetes').val(),
        date_anciennete = container_params.find('.js_cl_date_anciennete').val();

    $.ajax({
        data: { periode_agee:periode_agee , date_anciennete:date_anciennete },
        url: Routing.generate('etat_base_param_agee'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            show_modal(data,'Param\xE8trage balance ag\xE9e','pulse');
            var t = date_anciennete.split(/[- :]/),
                date_object = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
            var formatted = $.datepicker.formatDate("dd/mm/yy", date_object);
            $('#js_date_anciennete_choise input').val(formatted);
            $('#js_date_anciennete_choise .input-group.date').datepicker({
                startView: 1,
                todayBtn: "linked",
                language: "fr",
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true
            });
        }
    });
}
