/**
 * Created by SITRAKA on 14/05/2018.
 */
/*$(document).on('change','.cl_dossier',function(){
    var dossier = $(this).val(),
        tr = $(this).closest('tr');
    $.ajax({
        url: Routing.generate('linxo_banque_compte'),
        type: 'POST',
        async: true,
        data: { dossier: dossier },
        success: function(data){
            test_security(data);
            tr.find('.cl_banque_compte').html(data);
        }
    });
});*/

$(document).on('click','.js_save_linxo_dossier',function(){
    var linxoDossiers = [];
    $('#id_table_linxo_dossiers').find('tbody').find('tr').each(function(){
        linxoDossiers.push({
            id: $(this).attr('data-id'),
            banque_compte: $(this).find('.cl_banque_compte').val(),
            date: $(this).find('.cl_date').val(),
            solde: parseInt($(this).find('.cl_solde').val()),
            periode: parseInt($(this).find('.cl_periode').val())
        });
    });

    $.ajax({
        url: Routing.generate('linxo_save_linxo_dossier'),
        type: 'POST',
        async: true,
        data: {
            linxo: $('#id_linxo').attr('data-id'),
            site: $('#id_site').val(),
            dossier: $('#id_dossier').val(),
            linxoDossiers: JSON.stringify(linxoDossiers)
        },
        success: function(data){
            test_security(data);

            if (parseInt(data) === 1)
            {
                show_info('SUCCES','Modifications bien enregistrées');
                close_modal();
                charger_linxo_account();
            }
            else show_info('Une erreur c est produite pendant les modifications','VEUILLEZ REESAYER!!','error');
        }
    });
});

$(document).on('change','#id_dossier',function(){
    charger_compte_banque();
});

function charger_compte_banque()
{
    $('#id_table_linxo_dossiers').find('tbody').find('tr').each(function(){
        var tr = $(this);
        tr.find('.cl_banque_compte').empty();
        $.ajax({
            url: Routing.generate('linxo_banque_compte'),
            data: {
                client:$('#client').val(),
                site:$('#id_site').val(),
                dossier: $('#id_dossier').val(),
                linxo_dossier: tr.attr('data-id')
            },
            type: 'POST',
            async: true,
            success: function(data){
                test_security(data);
                tr.find('.cl_banque_compte').html(data);
            }
        });
    });
}
