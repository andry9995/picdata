/**
 * Created by SITRAKA on 05/04/2019.
 */
$(document).ready(function(){
    $(document).on('click','#id_show_filtre_date',function(){
        var type = parseInt($(this).attr('data-type')),
            start = $(this).attr('data-start'),
            end = $(this).attr('data-end');

        $.ajax({
            data: {
                type: type,
                start: start,
                end: end
            },
            type: 'POST',
            url: Routing.generate('jnl_bq_filtre_date'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Filtre');
                $('#id_date_range .input-daterange').datepicker({
                    dateFormat: 'dd/mm/yyyy',
                    language: 'fr',
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true
                });
                hide_date_range();
            }
        });
    });

    $(document).on('change','input[name="radio-type-filtre"]',function(){
        hide_date_range();
    });

    $(document).on('click','#id_valider_filtre',function(){
        var start = $('#id_date_start').val().trim(),
            end = $('#id_date_end').val().trim();
        $('#id_show_filtre_date')
            .attr('data-type', $('input[name="radio-type-filtre"]:checked').val())
            .attr('data-start', start)
            .attr('data-end', end);
        if (start !== '' || end !== '') $('#id_show_filtre_date').addClass('blink');
        else $('#id_show_filtre_date').removeClass('blink');

        close_modal();
        go();
    });
});

function hide_date_range()
{
    var type = parseInt($('input[name="radio-type-filtre"]:checked').val());
    if (type === 2)
    {
        $('#id_date_range').closest('.col-lg-12').addClass('hidden');
        $('#id_date_start').val('');
        $('#id_date_end').val('');
    }
    else $('#id_date_range').closest('.col-lg-12').removeClass('hidden');
}
