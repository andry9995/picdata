/**
 * Created by SITRAKA on 21/02/2019.
 */
$(document).ready(function(){
    charger_site();
    $('#exercice').val((new Date()).getFullYear());
    dossier_depend_exercice = true;

    $(document).on('click','.cl_tab',function(){
        go();
    });

    $(document).on('change','#dossier',function(){
        go();
    });
});

function after_load_dossier()
{
    go();
}

function go()
{
    $('#id_fitre_container').removeClass('hidden');
    var type = parseInt($('#id_tabs_content').find('.tab-pane.active').attr('data-type'));
    if (type === 0) charger_export();
    else if (type === 1)
    {
        $('#id_fitre_container').addClass('hidden');
        charger_recap();
    }
    else charger_parametre();
}

function set_datepicker(input)
{
    input.datepicker({
        dateFormat: 'dd/mm/yyyy',
        language: 'fr',
        daysOfWeekHighlighted: '0,6',
        todayHighlight: true,
        autoclose: true
    });
}
