/**
 * Created by SITRAKA on 13/01/2020.
 */

var one_change = true;
$(document).ready(function(){

    $(document).on('click','#id_show_s_cat_param',function(){
        $.ajax({
            data: {
                client: $('#client').val(),
                site: $('#site').val(),
                dossier: $('#dossier').val(),
                exercice: $('#exercice').val()
            },
            type: 'POST',
            url: Routing.generate('banque_pm_sc_param'),
            dataType: 'json',
            success: function(data) {
                var html = '<table id="id_sc_param"></table>';
                show_modal(html,'Catégorie à saisir',undefined,'modal-lg');

                var table_selected = $('#id_sc_param'),
                    h_table = $(window).height() - 150,
                    w = table_selected.parent().width(),
                    editurl = 'index.php';

                var datas = data.datas,
                    headers = data.headers;


                set_table_jqgrid(datas,h_table / 2,col_model_sc(headers),col_model_sc(headers,w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined,undefined);
            }
        });
    });

    $(document).on('change','.cl_s_categorie_status',function(){
        if (!one_change) return;

        var dossier = $(this).closest('tr').attr('id'),
            status = $(this).is(':checked') ? 1 : 0,
            s_categorie = $(this).attr('data-s_categorie');

        $.ajax({
            data: {
                dossier: dossier,
                s_categorie: s_categorie,
                status: status
            },
            type: 'POST',
            url: Routing.generate('banque_pm_sc_save_status'),
            dataType: 'html',
            success: function(data) {
                //show_modal(data);return;
                show_info('Succès','Modification enregistrée avec succès');
            }
        });
    });

    $(document).on('change','.cl_input_chk_all',function(){
        var class_sc = parseInt($(this).attr('data-class')),
            s = $(this).is(':checked') ? 1 : 0,
            dossiers = [],
            s_categorie = $(this).attr('data-id');

        one_change = false;
        $('.cl_s_categorie_'+class_sc).each(function(){
            $(this).prop('checked', s);
            dossiers.push($(this).closest('tr').attr('id'));
        });
        one_change = true;

        $.ajax({
            data: {
                a_saisir: s,
                sc: s_categorie,
                dossiers: JSON.stringify(dossiers)
            },
            type: 'POST',
            url: Routing.generate('banque_pm_sc_save_stats_dossiers'),
            dataType: 'html',
            success: function(data) {
                show_info('Succès','Modifications enregistrées avec succès');
            }
        });
    });
});

function col_model_sc(headers, w)
{
    var col = [];
    if(typeof w !== 'undefined')
    {
        col.push({ name:'d', index:'d' });
        $.each(headers, function( k, v ) {
            col.push({ name:k, index:k, align:'center', formatter: function(v){ return chk_s_categorie(v) } });
        });
    }
    else
    {
        col.push('dossier');
        $.each(headers, function( k, v ) {
            col.push('' +
                '<input type="checkbox" class="cl_input_chk_all" data-id="'+ v.id +'" data-class="'+v.i+'" id="id_chk_'+k+'">' +
                '<label for="id_chk_'+k+'">&nbsp;'+v.l+'</label>');
        });
    }
    return col;
}

function chk_s_categorie(v)
{
    return '<input type="checkbox" class="cl_s_categorie_status pointer cl_s_categorie_'+v.i+'" data-s_categorie="'+v.id+'" '+((parseInt(v.s) === 1) ? 'checked' : '')+'>';
}
