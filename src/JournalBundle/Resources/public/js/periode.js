
$(document).ready(function(){
    oneExercice = true;
    periodeDependant = false;

    $(document).on('click','.js_dpk_valider',function(){
        var new_html = $(this).closest('.popover-content').html();
        $('.js_date_picker_hidden').html(new_html);
        $('#id_periode_btn').attr('data-content',new_html).click();
    });
});

function charger_periode_pop_over()
{
    $.ajax({
        data: {
            dossier: $('#dossier').val()
        },
        url: Routing.generate('jnl_bq_periode'),
        type: 'POST',

        dataType: 'html',
        success: function(data){
            test_security(data);

            var btn =
                '<div class="form-horizontal">' +
                '<div class="form-group">' +
                '<label class="control-label col-lg-4">Mois</label>' +
                '<div class="col-lg-8">' +
                '<span type="button" style="width:100%" class="btn btn-white" id="id_periode_btn" data-container="body" data-toggle="popover" data-placement="bottom" data-content="">' +
                '<i class="fa fa-calendar-o"></i>' +
                '</span>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#id_container_btn_periode').html(btn);
            $('#id_periode_btn').attr('data-content',data);
            $("[data-toggle=popover]").popover({ html:true });
            $('.js_date_picker_hidden').html(data);
        }
    });
}
