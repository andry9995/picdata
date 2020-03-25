/**
 * Created by SITRAKA on 24/05/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_export',function(){
        var format = $(this).attr('data-format') ;

        if (all_datas.length === 0)
        {
            show_info('Vide','Aucune données à exporter','error');
            return;
        }

        var type = $(this).attr('data-type'),
            params = ''
                + '<input type="hidden" name="format" value="'+format+'">'
                + '<input type="hidden" name="all_datas" value="'+encodeURI(JSON.stringify(all_datas))+'">'
                + '<input type="hidden" name="all_stats" value="'+encodeURI(JSON.stringify(all_stats))+'">'
                + '<input type="hidden" name="all_cles" value="'+encodeURI(JSON.stringify(all_cles))+'">'
                + '<input type="hidden" name="all_statistiques" value="'+encodeURI(JSON.stringify(statistique))+'">'
                + '<input type="hidden" name="all_entetes" value="'+encodeURI(JSON.stringify(all_entetes))+'">';
        $('#id_export').attr('action',Routing.generate('ind_tb_export')).html(params).submit();
        return;


        $.ajax({
            data: {
                all_datas: JSON.stringify(all_datas),
                all_stats: JSON.stringify(all_stats),
                all_cles: JSON.stringify(all_cles),
                all_entetes: JSON.stringify(all_entetes),
                all_statistiques: JSON.stringify(statistique)
            },
            type: 'POST',
            url: Routing.generate('ind_tb_export'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#id_test').html(data);
            }
        });
    });
});
