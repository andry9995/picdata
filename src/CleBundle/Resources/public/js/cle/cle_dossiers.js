/**
 * Created by SITRAKA on 07/03/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_edit_exlude_dossier',function(){
        edit_dos_exlc($(this));
    });
    $(document).on('click','.cl_edit_exception',function(){
        var action = parseInt($(this).attr('data-action')),
            dossier = null,
            cle = $('#id_cle_selected').attr('data-cle');

        if (action === 1)
        {
            if ($('#dossier option:selected').text().trim() === '' || $('#dossier option:selected').text().trim().toUpperCase() === 'TOUS')
            {
                show_info('Erreur','Choisir le dossier','error');
                return;
            }
            dossier = $('#dossier').val()
        }
        else dossier = $(this).closest('tr').attr('id');

        $.ajax({
            data: {
                dossier: dossier,
                cle: cle,
                action: action,
                dossiers: dossiers_ids
            },
            url: Routing.generate('cle_dossiers_edit'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                set_table_cle_dossiers($.parseJSON(data));
            }
        });
    });
});

function dos_excl_formatter(v)
{
    return (v.length > 0) ?
        '<span class="qtip_new" data-datas="'+encodeURIComponent(JSON.stringify(v))+'">'+v.length+'&nbsp;dossier(s)</span>' :
        '';
}

function edit_dos_exlc(td)
{
    $.ajax({
        data: {
            cle: td.closest('tr').attr('id'),
            dossiers: dossiers_ids
        },
        url: Routing.generate('cle_dossiers_edit'),
        type: 'POST',
        dataType: 'html',
        success: function(data){
            test_security(data);
            show_modal(data,td.closest('tr').find('.cl_nom_cle').text(),undefined);
            var datas = $.parseJSON($('#id_container_table_dossiers_exclude').attr('data-datas'));
            charger_site();
            set_table_cle_dossiers(datas);
        }
    });
}

function set_table_cle_dossiers(datas)
{
    var html = '<table id="id_table_dossiers_exclude"></table>';
    $('#id_container_table_dossiers_exclude').html(html);

    $('#id_table_dossiers_exclude').jqGrid({
        data: datas,
        datatype: 'local',
        height: 250,
        autowidth: true,
        shrinkToFit: true,
        rowNum: 100000,
        colNames: ['Client','Dossier',''],
        colModel: [
            { name: 'c_nom', index: 'c_nom', sortable:true },
            { name: 'nom', index: 'nom', sortable:true },
            { name: 'x', index: 'x', align:'center', width: 25, formatter: function() { return '<i class="fa fa-trash cl_edit_exception pointer" data-action="2" aria-hidden="true"></i>' } }
        ],
        viewrecords: true,
        hidegrid: false
    });
}