/**
 * Created by SITRAKA on 15/05/2017.
 */
$(document).on('click','.js_add_pcg',function(){
    new_pcg($(this));
});

function new_pcg(btn)
{
    var div_container = btn.closest('.form-horizontal'),
        input_compte = div_container.find('.js_cl_pcg_compte'),
        compte = parseInt(input_compte.val().trim()),
        input_intitule = div_container.find('.js_cl_pcg_intitule'),
        intitule = input_intitule.val().trim().sansAccent().toUpperCase(),
        error = false;

    //compte
    if(isNaN(compte) || compte < 99)
    {
        input_compte.closest('.form-group').addClass('has-error');
        error = true;
    }
    else input_compte.closest('.form-group').removeClass('has-error');

    //intitule
    if(intitule == '')
    {
        input_intitule.closest('.form-group').addClass('has-error');
        error = true;
    }
    else input_intitule.closest('.form-group').removeClass('has-error');

    if(error)
    {
        show_info('ERREUR','VERIFIER LES CHAMPS','error');
        return;
    }

    $.ajax({
        data: { compte:compte, intitule:intitule, action: 1 },
        url: Routing.generate('pcg_edit'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            //alert(data);return;
            var result = parseInt(data);
            if(result == 1)
            {
                show_info('SUCCES','VOUS POUVEZ MAINTENANT UTILISE LE COMPTE ' + compte);
                close_modal();
            }
            else show_info('ERREUR','COMPTE DEJA EXISTANT','error');
        }
    });
}
