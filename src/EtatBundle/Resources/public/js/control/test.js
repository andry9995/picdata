$(document).on('click','#js_test',function(){
     launch_test();
});

function launch_test()
{
    show_info('Veuillez-patientez','Le test peut durer plusieurs minutes','warning');
    var dossiers = [];
    if ($('#dossier option:selected').text().trim() == '')
    {
        $('#dossier option').each(function(){
            if ($(this).text().trim() != '')
            {
                dossiers.push({ nom:$(this).text(), id:$(this).val() });
            }
        });
    }
    else
    {
        var selected = $('#dossier option:selected');
        dossiers.push({ nom:selected.text(), id:selected.val() });
    }

    //alert(dossiers.length);
    launch_dossier(dossiers,0);
}

function launch_dossier(dossiers,index)
{
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: dossiers[index].id,
            exercices: $('#exercice').val()
        },
        url: Routing.generate('etat_control_test'),
        type: 'POST',
        //async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            //show_modal(data,'titre');return;

            if (index != dossiers.length - 1)
            {
                launch_dossier(dossiers,index + 1);
                show_info((index + 1) + '/' + dossiers.length + ' Dossiers',dossiers[index].nom + ' teste avec succes','warning');
            }
            else
            {
                show_info('Le test c est termine avec succes','Chargement des nouveaux erreurs ...');
                charger_input_errors();
            }
        },
        error: function () {
            if (index != dossiers.length - 1)
            {
                launch_dossier(dossiers,index + 1);
                show_info((index + 1) + '/' + dossiers.length + ' Dossiers',dossiers[index].nom + ' :ERROR','warning');
            }
            else
            {
                show_info('Le test c est termine avec succes','Chargement des nouveaux erreurs ...');
                charger_input_errors();
            }
        }
    });
}