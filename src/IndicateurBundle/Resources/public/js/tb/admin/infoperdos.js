/**
 * Created by SITRAKA on 16/09/2019.
 */

$(document).ready(function(){
    $(document).on('click','.js_add_in_infoperdos',function(){
        $.ajax({
            data: { },
            type: 'POST',
            url: Routing.generate('ind_tb_show_add_in_infoperdos'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Infos. dossier',undefined,'modal-sm');
            }
        });
    });

    $(document).on('click','.cl_tr_infoperdos',function(){
        var indicateur_info_perdos = $(this).attr('data-id');
        $.ajax({
            data: {
                indicateur_info_perdos: indicateur_info_perdos
            },
            type: 'POST',
            url: Routing.generate('ind_tb_add_indicateur_tb_infoperdos'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                charger_tb_domaine();
                close_modal();
                show_info('Succès','Modification enregistrée avec succès');
            }
        });
    });
});
