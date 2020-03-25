/**
 * Created by SITRAKA on 12/07/2018.
 */
$(document).on('click','#id_parametrer',function(){
    if ($('#id_group_indicateur option:selected').text().trim() === '')
    {
        show_info('Erreur','Choisir le groupe','error');
        return;
    }

    $.ajax({
        data: { dossier:$('#dossier').val(), indicateur_group: $('#id_group_indicateur').val() },
        url: Routing.generate('ind_parametre'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
             if (parseInt(data) === 1)
             {
                show_info('SUCCES','Modification bien enregistrée');
                charger_packs();
             }
             else show_info('Erreur','Une erreur c est produite pendant l enregistrement', 'error');
        }
    });
});
