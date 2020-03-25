/**
 * Created by SITRAKA on 11/05/2018.
 */
$(document).on('click','#id_code',function(){
    $('#id_frame').attr('src','');

    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val(),
            code:$('#id_code').val().trim(),
        },
        type: 'POST',
        url: Routing.generate('linxo_update_linxo_account'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            var result = $.parseJSON(data),
                r = parseInt(result.r);

            if (r === 1)
            {
                close_modal();
                show_info('Compte Linxo bien valider','Veuillez patienter pendant la mise à jour de vos comptes LINXO');
                charger_linxo_account();
            }
            else if (r === 2)
            {
                $('#id_message').removeClass('hidden');
            }
            else show_info('Une erreur est survenue pendant l enregistement','Veillez reesayer','error');
        }
    });
});

$(document).on('click','#id_pop_up_cgu',function(){
    window.open("https://wwws.linxo.com/auth.page#Login","CGU LINXO","menubar=no, status=no, scrollbars=no, menubar=no, width=1000, height=1000");
    close_modal();
});