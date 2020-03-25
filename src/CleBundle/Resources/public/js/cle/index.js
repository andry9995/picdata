/**
 * Created by SITRAKA on 06/03/2019.
 */
var dossiers_ids = '[]';

$(document).ready(function(){
    dossiers_ids = $('#id_dossiers_ids').val();
    charger_cles();

    $('#modal').on('hidden.bs.modal', function () {
        charger_cles();
    });
});

function charger_cles()
{
    $('#id_container_table').html('<table id="id_table_cle"></table>');
    $.ajax({
        data: { dossiers: dossiers_ids },
        url: Routing.generate('cle_liste'),
        dataType: 'json',
        type: 'POST',
        success: function(data){
            //$('#id_container_table').html(data);return;
            $('#id_table_cle').jqGrid({
                data: data,
                datatype: 'local',
                height: $(window).height() - 200,
                autowidth: true,
                shrinkToFit: true,
                rowNum: 100000,
                colNames: ['Clé','Dossiers Exclus'],
                colModel: [
                    { name: 'cl', index: 'cl', sortable:true, classes:'cl_nom_cle' },
                    { name: 'dx', index: 'dx', sortable:true, classes:'pointer cl_edit_exlude_dossier', formatter:function(v){ return dos_excl_formatter(v); } }
                ],
                viewrecords: true,
                hidegrid: false
            });

            activer_qtip_dossier_excludes();
        }
    });
}

function activer_qtip_dossier_excludes()
{
    $('.qtip_new').each(function(){
        var datas = $.parseJSON(decodeURIComponent($(this).attr('data-datas').toString()));
        $(this).qtip({
            content: {
                text: function (event, api) {
                    var html = '<table class="table table-bordered">';
                    for (var i = 0; i < datas.length; i++)
                    {
                        html +=
                            '<tr>' +
                                '<td>'+datas[i].c_nom+'</td>' +
                                '<td>'+datas[i].nom+'</td>' +
                            '</tr>';
                    }
                    html += '</table>';
                    return html;
                }
            },
            position: { my: 'top right', at: 'bottom left' },
            style: { classes: 'qtip-dark qtip-shadow' }
        });
    });
}