$(document).ready(function () {
    charger_site_tpe();

    $(document).on('change', '#client', function () {
        charger_site_tpe();
    });

    $(document).on('change', '#site', function () {
        charger_dossier_tpe();
    });
});

//charger site
function charger_site_tpe()
{
    var client = $('#client').val();
    var lien = Routing.generate('app_sites', {conteneur: 0, client: client, tous: 1, infoperdos: 1 });
    $('#js_conteneur_site').empty();

    verrou_fenetre(true);
    $.ajax({
        url: lien,
        dataType: 'html',
        success: function(data){
            $('#js_conteneur_site').html(data).change();
            charger_dossier_tpe();
        }
    });
}
//charger dossier a partir du site
function charger_dossier_tpe(tous)
{
    if($('#js_conteneur_dossier').length > 0)
    {
        tous = (typeof tous !== 'undefined') ? tous : 0;
        var site = $('#site').val(),
            client = $('#client').val(),
            lien = Routing.generate('app_dossiers')+'/0/'+site+'/'+tous+'/'+client+'/'+1;

        verrou_fenetre(true);
        $.ajax({
            url: lien,
            dataType: 'html',
            success: function(data){
                $('#js_conteneur_dossier').html(data);
            }
        });
    }
}