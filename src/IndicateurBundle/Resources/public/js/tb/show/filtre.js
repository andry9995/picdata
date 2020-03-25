oneExercice = true;
onePeriode = true;
periodeEquivalent = true;

$(document).ready(function(){
    $(document).on('click','.js_periode',function(){
        show_modal($('#js_id_interval_hidden').html(),'PERIODE');
    });

    $(document).on('click','.js_dpk_valider',function(){
        var btn = $(this);
        $('#js_id_interval_hidden').html(btn.closest('.modal-body').html());
        close_modal();
        go();
    });
});

function charger_exos()
{
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val()
        },
        type: 'POST',
        url: Routing.generate('ind_exos'),
        dataType: 'json',
        success: function(data) {
            $('.cl_exos_container').html(data);
        }
    });
}