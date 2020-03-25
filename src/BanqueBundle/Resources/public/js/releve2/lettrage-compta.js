/**
 * Created by SITRAKA on 05/06/2019.
 */

var index_table_compta = 0;

$(document).ready(function(){
    $(document).on('click','.cl_show_lettrage_compta',function(){
        index_table_compta++;
        var bsca = $(this).closest('tr').attr('id');

        $.ajax({
            data: {
                bsca: bsca,
                index: index_table_compta
            },
            type: 'POST',
            url: Routing.generate('banque2_banque_autre_lettrage_compta'),
            dataType: 'html',
            success: function(data) {
                modal_ui({ modal: false, resizable: true,title: 'Lettrage Multiple' },data,undefined,undefined,0.4);
            }
        });
    });

    $(document).on('click','.cl_compte_a_chercher',function(){
        return;
        var row = $(this).closest('.row'),
            index = parseInt(row.attr('data-index')),
            new_table = '<table id="id_table_'+index+'"></table>',
            id_compte = $(this).attr('data-id'),
            type_compte = parseInt($(this).attr('data-type')),
            bsca = row.attr('data-id');
        
        row.find('.cl_container_table').html(new_table);

        $.ajax({
            data: {
                id_compte: id_compte,
                type_compte: type_compte,
                bsca: bsca
            },
            type: 'POST',
            url: Routing.generate('banque2_banque_autre_compta_a_lettre'),
            dataType: 'html',
            success: function(data) {
                row.find('.cl_container_table').html(data);
                //modal_ui({ modal: false, resizable: true,title: 'Lettrage Multiple' },data,undefined,undefined,0.4);
            }
        });
    });
});
