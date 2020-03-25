/**
 * Created by SITRAKA on 17/04/2019.
 */
var index_autre = 0,
    cl_bq_autre = 'bq_autre_sel';
$(document).ready(function(){
    $(document).on('click','.cl_soeurs',function(){
        var ids = $(this).attr('data-ids').split(';'),
            noms = $(this).attr('data-noms').split(';');

        if (ids.length === 1) show_image_pop_up(ids[0]);
        else
        {
            var table = '' +
                '<table class="table">';

            for (var i = 0; i < noms.length; i++)
                table += '<tr class="js_show_image" data-id_image="'+ids[i]+'"><td>'+noms[i]+'</td></tr>';

            table += '</table>';
            show_modal(table,'Liste d images',undefined,'modal-xs');
        }
    });

    $(document).on('click','.cl_lettrage_autre',function(){
        $('.'+cl_bq_autre).removeClass(cl_bq_autre);
        $(this).closest('tr').addClass(cl_bq_autre);

        remove_last_ui();
        index_autre++;
        var id = $(this).closest('tr').attr('id'),
            ind = index_autre;

        montantTTC = number_fr_to_float($(this).closest('tr').find('.js_cl_ttc').text());
        $.ajax({
            data: { releve: $('#js_zero_boost').val(), methode:methode_dossier, banque_sous_categorie_autre:id },
            url: Routing.generate('banque2_images_view'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                var exercice = parseInt($('#exercice').val().trim());
                modal_ui(
                    { modal: false, resizable: true,title: 'Rapprochement' },
                    data,undefined, 0.8,0.8
                );
                $('#js_id_n_1').next('label').text(exercice - 1);
                $('#js_id_n').next('label').text(exercice);
                $('#js_id_n_p_1').next('label').text(exercice + 1);

                $.ajax({
                    data: {
                        banque_sous_categorie_autre: id
                    },
                    type: 'POST',
                    url: Routing.generate('banque2_lettra_autre_show'),
                    dataType: 'json',
                    success: function(data) {
                        var table_selected = $('#js_cl_tb_affecter'),
                            w = table_selected.parent().width(),
                            editurl = 'index.php';
                        set_table_jqgrid(data,h_table,get_col_model_image_affecter(),get_col_model_image_affecter(w),table_selected,'hidden',w,editurl,false,undefined,false,undefined,undefined,undefined);
                        charger_ecriture_temp();
                    }
                });
            }
        });
    });

    $('#modal').on('shown.bs.modal', function(){
        remove_last_ui();
    });
    $('#modal').on('hidden.bs.modal', function(){
        if (up_row)
        {
            up_row = false;
            update_row();
        }
        remove_last_ui();
    });

    $(document).on('click','.cl_annuler_imputation_bsca',function(){
        $('.'+cl_bq_autre).removeClass(cl_bq_autre);
        var id = $(this).closest('tr').addClass(cl_bq_autre).attr('id');

        $.ajax({
            data: {
                banque_sous_categorie_autre: id
            },
            type: 'POST',
            url: Routing.generate('banque2_banque_autre_annuler_lettrage'),
            dataType: 'html',
            success: function(data) {
                if (parseInt(data) === 1)
                {
                    update_tr_bq_autre();
                    show_info('Succés','Modification bien enregistrée');
                }
            }
        });
    });
});

function remove_last_ui()
{
    var last_ui = undefined;
    if ($('#js_cl_tb_affecter').length > 0)
        last_ui = $('#js_cl_tb_affecter').closest('.ui-dialog-content').attr('id');
    if (typeof last_ui !== 'undefined') $('#'+last_ui).remove();
}

function update_tr_bq_autre(data)
{
    var id = $('.' + cl_bq_autre).attr('id');
    if (typeof data !== 'undefined') update_bq_autre(id,data);
    else
    {
        $.ajax({
            data: { banque_categorie_autre: id },
            async: true,
            url: Routing.generate('banque2_banque_autre_tr_updated'),
            type: 'POST',
            dataType: 'json',
            success: function(dat){
                update_bq_autre(id,dat);
            }
        });
    }
}

function update_bq_autre(id,data)
{
    var newData = data;
    newData.id = id;
    $('#id_detail_banque_autre').jqGrid('setRowData', id, newData);
    can_close_modal = false;
    update_row();
}
