/**
 * Created by SITRAKA on 11/03/2020.
 */

var up_row = false;

$(document).ready(function(){
    $(document).on('click','.js_show_non_lettrable',function(){
        $('.'+ class_tr_edited).removeClass(class_tr_edited);

        var releve = $(this).closest('tr').addClass(class_tr_edited).attr('id'),
            cle_dossier_ext = $(this).closest('tr').find('.cl_cde_id').text().trim();

        $.ajax({
            data: {
                releve: releve,
                cle_dossier_ext: cle_dossier_ext
            },
            type: 'POST',
            url: Routing.generate('non_lettrable_show'),
            dataType: 'html',
            success: function(data) {
                show_modal(data,'Non lettrable',undefined,'modal-lg');
                up_row = true;
                charger_non_lettrable();
            }
        });
    });

    $(document).on('click','.cl_revert_lettrable',function(){
        var image = $(this).closest('tr').attr('id'),
            el = $('#id_container_id'),
            releve = el.attr('data-releve'),
            releve_ext = el.attr('data-releve_ext'),
            row = $(this).closest('row');

        $.ajax({
            data: {
                releve: releve,
                releve_ext: releve_ext,
                image: image
            },
            type: 'POST',
            url: Routing.generate('non_lettrage_annuler'),
            dataType: 'html',
            success: function(data) {
                $('#id-non-lettrable').jqGrid('delRowData',image);
                show_info('Succés','Modification bien enregistrée avec succès');
            }
        });
    });
});

function charger_non_lettrable()
{
    var el = $('#id_container_id');

    $.ajax({
        data: {
            releve: el.attr('data-releve'),
            releve_ext:el.attr('data-releve_ext')
        },
        type: 'POST',
        url: Routing.generate('non_lettrable_liste'),
        dataType: 'json',
        success: function(data) {
            //$('#r_test').html(data);

            var table_selected = $('#id-non-lettrable'),
                w = table_selected.parent().width(),
                h = $(window).height() - 210,
                editurl = 'index.php';

            set_table_jqgrid(data,h,get_col_model_non_lettrable(),get_col_model_non_lettrable(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
        }
    });
}

function get_col_model_non_lettrable(w)
{
    var colM = [];

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'d', index:'d', width: 15 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d', newformat: 'd/m/Y'} });
        colM.push({ name:'i', index:'i', width: 15 * w/100, formatter:function(v){ return image_formatter(v) } });
        colM.push({ name:'l', index:'l', width: 45 * w/100 });
        colM.push({ name:'m', index:'m', width: 15 * w/100, align:'right', sortable:true, sorttype: 'number', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } });
        colM.push({ name:'x', index:'x', width: 5 * w/100, align:'center', formatter:function(){ return '<i class="fa fa-reply pointer cl_revert_lettrable" aria-hidden="true"></i>' } });
    }
    else
    {
        colM.push('Date');
        colM.push('Pièce');
        colM.push('Libellé');
        colM.push('Montant');
        colM.push('');
    }
    return colM;
}
