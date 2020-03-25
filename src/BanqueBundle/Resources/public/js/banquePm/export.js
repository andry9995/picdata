/**
 * Created by SITRAKA on 18/06/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_export',function(){
        var extension = $(this).attr('data-format');

        if (all_datas.length === 0)
        {
            show_info('Vide','Pas de données à exporter','error');
            return;
        }

        var tab_element = null;
        $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
            if ($(this).hasClass('active')) tab_element = $(this);
        });
        var type = parseInt(tab_element.attr('data-type'));

        var titre = $('#id_tabs').find('.nav-tabs').find('.cl_tab_li.active').text();

        var params = ''
                + '<input type="hidden" name="extension" value="'+extension+'">'
                + '<input type="hidden" name="datas" value="'+encodeURI(JSON.stringify(all_datas))+'">'
                + '<input type="hidden" name="exp_dossier" value="'+$('#dossier').val()+'">'
                + '<input type="hidden" name="exp_banque" value="'+$('#js_banque').val()+'">'
                + '<input type="hidden" name="exp_banque_compte" value="'+$('#js_banque_compte').val()+'">'
                + '<input type="hidden" name="exp_exercice" value="'+$('#exercice').val()+'">'
                + '<input type="hidden" name="exp_type" value="'+type+'">'
                + '<input type="hidden" name="exp_title" value="'+titre+'">';

        $('#id_export').attr('action',Routing.generate('banque_pm_export')).html(params);
        $('#id_export')[0].submit();
        //return;

        /*$.ajax({
             data: {
                 extension: extension,
                 datas: encodeURI(JSON.stringify(all_datas)),
                 exp_dossier: $('#dossier').val(),
                 exp_banque: $('#js_banque').val(),
                 exp_banque_compte: $('#js_banque_compte').val(),
                 exp_exercice: $('#exercice').val(),
                 exp_type: type,
                 exp_title: titre
             },
             url: Routing.generate('banque_pm_export'),
             type: 'POST',
             dataType: 'html',
             success: function(){
             }
         });*/
    });
});
