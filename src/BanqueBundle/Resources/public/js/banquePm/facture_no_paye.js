/**
 * Created by SITRAKA on 09/07/2019.
 */
$(document).ready(function(){
    $(document).on('change','#id_interval',function(){
        go();
    });

    $(document).on('change','#id_date_anciennete',function(){
        go();
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text(),0);
    });

    $(document).on('change','.cl_image_comment',function(){
        change_image_comment($(this));
    });
});

function set_table_facture_np(tab_element,datas)
{
    var type = parseInt(tab_element.attr('data-type')),
        table = '<table id="table_pm_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);
    var table_selected = $('#table_pm_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = 'index.php';

    set_table_jqgrid(datas['datas'],h,get_col_model_facture_np(datas['m']),get_col_model_facture_np(datas.m,w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
}

function get_col_model_facture_np(intervals,w)
{
    var colM = [],i;

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'i', index:'i', sortable:true, width: 8 * w/100, align:'center', formatter: function(v){return image_formatter(v)} });
        colM.push({ name:'d', index:'d', sortable:true, width: 8 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d', newformat: 'd/m/Y'} });
        colM.push({ name:'de', index:'de', sortable:true, width: 8 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d', newformat: 'd/m/Y'} });
        colM.push({ name:'l', index:'l', sortable:true, width: 12 * w/100 });

        colM.push({ name:'b', index:'b', sortable:true, width: 8 * w/100, align:'center', formatter: function(v){return compte_formatter(v)} });
        /*colM.push({ name:'tva', index:'tva', sortable:true, width: 8 * w/100, align:'center', formatter: function(v){return compte_formatter(v)} });
        colM.push({ name:'r', index:'r', sortable:true, width: 8 * w/100, align:'center', formatter: function(v){return compte_formatter(v)} });*/

        for (i = 0; i < intervals.length; i++)
            colM.push({ name:'m_'+i, index:'m_'+i, sortable:true, sorttype: 'number', align:'right', width: (18/intervals.length) * w/100, formatter: function(v){return number_format(v, 2, ',', ' ',true)} });

        colM.push({ name:'st', index:'st', sortable:true, width: 16 * w/100, formatter: function(v){return status_fnp_formatter(v)}  });
        colM.push({ name:'cm', index:'cm', sortable:true, width: 16 * w/100, formatter: function(v){return commentaire_fnp_formatter(v)} });
    }
    else
    {
        colM.push('Pièce');
        colM.push('Date facture');
        colM.push('Date echeance');
        colM.push('Libellé');

        colM.push('Bilan');
        /*colM.push('Tva');
        colM.push('Résultat');*/

        for (i = 0; i < intervals.length; i++)
            colM.push(intervals[i]);

        colM.push('Statut');
        colM.push('Commentaire');
    }
    return colM;
}

function change_image_comment(input)
{
    var tr = input.closest('tr'),
        image = tr.attr('id'),
        statut = parseInt(tr.find('.cl_statut').val().trim()),
        comment = tr.find('.cl_comment').val().trim();

    $.ajax({
        data: {
            image: image,
            statut: statut,
            comment: comment
        },
        type: 'POST',
        url: Routing.generate('banque_pm_image_comment_save'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('Succès','Modification bien enregistrée');
        }
    });
}

function status_fnp_formatter(v)
{
    /** 0: default, 1:non paye; 2:payé personnellement $status */
    var val = parseInt(v);
    return '' +
        '<select class="input-in-jqgrid cl_image_comment cl_statut">' +
            '<option value="0"></option>' +
            '<option value="1" '+((val === 1) ? 'selected' : '')+'>Non Payée</option>' +
            '<option value="2" '+((val === 2) ? 'selected' : '')+'>Payée personnellement</option>' +
        '</select>';
}

function commentaire_fnp_formatter(v)
{
    return '<input type="text" value="'+v+'" class="input-in-jqgrid cl_image_comment cl_comment">';
}