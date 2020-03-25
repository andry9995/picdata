/**
 * Created by SITRAKA on 17/10/2019.
 */

$(document).ready(function(){
    dossier_depend_exercice = true;
    $('#exercice').val((new Date()).getFullYear());
    charger_site();

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
    charger_mois();
    charger_tresorerie();
    charger_details();
}

function dossier_selected()
{
    var dossier_nom = $('#dossier option:selected').text().trim().toUpperCase();

    if (dossier_nom === '' || dossier_nom === 'TOUS')
    {
        $('#dossier').closest('.form-group').addClass('has-error');
        return false;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    return true;
}