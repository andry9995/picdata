/**
 * Created by SITRAKA on 28/10/2019.
 */

function charger_details()
{
    $('.cl_container_details').each(function(){
        charger_detail($(this));
    });
}

function charger_detail(div_container)
{
    div_container.find('.ibox-content').empty();
    if (!dossier_selected()) return;

    var type = div_container.attr('data-type'),
        base = parseInt($('.cl_base.active').attr('data-base')),
        new_html = '<table id="id_tb_detail_'+type+'"></table>';

    div_container.find('.ibox-content').html(new_html);

    $.ajax({
        data: {
            type: type,
            dossier: $('#dossier').val(),
            base: base,
            exercice : parseInt($('#exercice').val()),
            mois: $('#id_container_mois').find('.cl_treso_mois.active').attr('data-mois')
        },
        url: Routing.generate('treso_detail'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);

            //div_container.find('.ibox-content').html(data);return;

            var result = $.parseJSON(data),
                heads = result.categories,
                datas = result.results,
                table_selected = $('#id_tb_detail_'+ type),
                w = table_selected.parent().width(),
                h = $(window).height() - 210,
                editurl = 'index.php';

            set_table_jqgrid(datas,h,model_detail(heads),model_detail(heads,w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
        }
    });
}

function model_detail(heads, w)
{
    var colM = [];

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'k_tc', index:'k_tc', sortable:true, width: 7 * w/100 });
        colM.push({ name:'l', index:'l', sortable:true, width: 7 * w/100 });
        colM.push({ name:'i', index:'i', sortable:true, width: 7 * w/100, formatter:function(v){ return image_formatter(v) } });
        $.each(heads, function( index, value ) {
            colM.push({ name:index, index:index, sortable:true, width: 7 * w/100 });
        });
    }
    else
    {
        colM.push('Catégorie');
        colM.push('Libellé');
        colM.push('Pièce');
        $.each(heads, function( index, value ) {
            colM.push(value);
        });
    }
    return colM;
}