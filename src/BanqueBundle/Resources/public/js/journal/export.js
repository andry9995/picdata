/**
 * Created by SITRAKA on 13/02/2019.
 */
var datas_export = [];
$(document).ready(function(){
    $(document).on('click','.cl_export',function(){
        if (datas_export.length === 0)
        {
            show_info('Vide','Pas de données à exporter','error');
            return;
        }

        var type = $(this).attr('data-type'),
            params = ''
                + '<input type="hidden" name="exp_banque_compte" value="'+$('#js_banque_compte').val()+'">'
                + '<input type="hidden" name="extension" value="'+type+'">'
                + '<input type="hidden" name="exp_exercice" value="'+$('#exercice').val()+'">'
                + '<input type="hidden" name="datas" value="'+encodeURI(JSON.stringify(datas_export))+'">';

        $('#id_export').attr('action',Routing.generate('jnb_bq_export')).html(params).submit();
    });
});