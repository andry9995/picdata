/**
 * Created by SITRAKA on 09/05/2018.
 */
$(document).on('click','.cl_edit_linxo',function(){
    $('.linxo_edited').removeClass('linxo_edited');

    var linxo = $(this).attr('data-id');
    if (parseInt($(this).attr('data-add')) === 0) $(this).addClass('linxo_edited');
    $.ajax({
        url: Routing.generate('linxo_edit'),
        type: 'POST',
        async: true,
        data: { linxo: linxo },
        success: function(data){
            test_security(data);
            show_modal(data,'LINXO',undefined,'modal-lg');

            if ($('#id_site').length > 0)
            {
                charger_id_dossier();
            }
        }
    });
});

$(document).on('change','#id_site',function(){
    charger_id_dossier();
});

function charger_id_dossier()
{
    $.ajax({
        url: Routing.generate('linxo_dossiers'),
        type: 'POST',
        async: true,
        data: {
            client: $('#client').val(),
            site: $('#id_site').val()
        },
        success: function(data){
            test_security(data);
            $('#id_dossier').html(data);
            charger_compte_banque();
        }
    });
}