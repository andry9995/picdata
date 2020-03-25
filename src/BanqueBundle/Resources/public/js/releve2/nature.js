/**
 * Created by SITRAKA on 15/01/2020.
 */

$(document).ready(function(){
    $(document).on('change','.cl_nature_releve',function(){
        var releve = $(this).closest('tr').attr('id'),
            nature = parseInt($(this).val());

        $.ajax({
            data: {
                releve: releve,
                nature: nature
            },
            type: 'POST',
            url: Routing.generate('banque_nature_releve_save'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                update_row(releve);
            }
        });
    });
});
