/**
 * Created by SITRAKA on 11/09/2019.
 */

$(document).ready(function(){
    $(document).on('click','.cl_btn_download_table_item',function(){
        var datas = [],
            headers = [],
            dossier = $('#dossier').val(),
            indicateur = $(this).closest('.ibox').attr('data-id'),
            extension = $(this).attr('data-extension').trim();

        $(this).closest('.ibox').find('.ui-jqgrid-htable').find('tr').each(function(){
            var tds = [];
            $(this).find('th').each(function(){
                tds.push({
                    t: 0,
                    v: $(this).text().trim(),
                    cs: typeof $(this).attr('colspan') !== 'undefined' ? parseInt($(this).attr('colspan')) : 1
                });
            });
            headers.push(tds);
        });

        $(this).closest('.ibox').find('.ui-jqgrid-btable').find('tr').each(function(){
            if (!$(this).hasClass('jqgfirstrow'))
            {
                var tds = [];
                $(this).find('td').each(function(){
                    var val = number_fr_to_float($(this).text().trim()),
                        t = 1;
                    if (isNaN(val))
                    {
                        t = 0;
                        val = $(this).text().trim();
                    }
                    tds.push({
                        t: t,
                        v: val,
                        cs: typeof $(this).attr('colspan') !== 'undefined' ? parseInt($(this).attr('colspan')) : 1
                    });
                });

                datas.push(tds);
            }
        });

        if (datas.length === 0)
        {
            show_info('Vide','Pas de données à exporter','error');
            return;
        }

        var type = $(this).attr('data-type'),
            params = ''
                + '<input type="hidden" name="exp_dossier" value="'+dossier+'">'
                + '<input type="hidden" name="exp_indicateur" value="'+indicateur+'">'
                + '<input type="hidden" name="extension" value="'+extension+'">'
                + '<input type="hidden" name="headers" value="'+encodeURI(JSON.stringify(headers))+'">'
                + '<input type="hidden" name="datas" value="'+encodeURI(JSON.stringify(datas))+'">';

        $('#id_export').attr('action',Routing.generate('ind_export_tb')).html(params).submit();
    });
});
