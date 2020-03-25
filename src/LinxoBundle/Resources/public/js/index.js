/**
 * Created by SITRAKA on 08/05/2018.
 */
$(document).ready(function(){
    charger_site();

    $(document).on('change','#dossier',function(){
        charger_linxo_account();
    });
});

function charger_linxo_account()
{
    $('#id_accounts_container').empty();
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val()
        },
        url: Routing.generate('linxo_accounts'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#id_accounts_container').html(data);
            change_gbs();
        }
    });
}

function change_gbs()
{
    $('.cl_edit_linxo').each(function(){
        $(this).removeClass('yellow-bg').removeClass('navy-bg').removeClass('lazur-bg');

        if (parseInt($(this).attr('data-add')) === 1) $(this).addClass('lazur-bg');
        else $(this).addClass((parseInt($(this).attr('data-valide')) === 1) ? 'navy-bg' : 'yellow-bg');
    });
}

function after_charged_dossier()
{
    charger_linxo_account();
}

function after_charged_dossier_not_select()
{
    charger_linxo_account();
}