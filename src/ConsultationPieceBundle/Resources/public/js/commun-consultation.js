/**
 * Created by MAHARO on 17/03/2017.
 */
/********************************
 *          EVENEMENTS
* ******************************/
//datepicker

$(document).on('change','#client',function(){
    charger_site_consultation();


});

/********************************
 *          FONCTIONS
 * ******************************/
//charger site
function charger_site_consultation()
{
    var client = $('#client').val(),
        lien = Routing.generate('app_sites')+'/0/'+client+'/1';
    $('#js_conteneur_site').empty();

    verrou_fenetre(true);
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_conteneur_site').html(data).change();
            charger_dossier_consultation();
            $('#site').closest('.form-group').find('div').toggleClass('col-lg-9 col-lg-8');
            $('#site').closest('.form-group').find('label').toggleClass('col-lg-3 col-lg-2');
            $('#site').closest('.form-group').find('label').find('span').remove();
            $('#site').closest('.form-group').find('label').html('<span>Site</span>');
            $('#site').closest('.form-group').find('label').css({'padding-top':'7px'});

        }
    });
}
//charger dossier a partir du site
function charger_dossier_consultation(tous)
{
    if($('#js_conteneur_dossier').length > 0)
    {
        tous = (typeof tous !== 'undefined') ? tous : 1;
        var site = $('#site').val(),
            client = $('#client').val(),
            lien = Routing.generate('app_dossiers')+'/0/'+site+'/'+tous+'/'+client;

        verrou_fenetre(true);
        $.ajax({
            data: {},
            url: lien,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_conteneur_dossier').html(data);
                $('#dossier').closest('.form-group').find('label').toggleClass('col-lg-3 col-lg-2');
                $('#dossier').closest('.form-group').find('div').toggleClass('col-lg-9 col-lg-10');
                $('#dossier').closest('.form-group').find('div').css({'padding-right':'5px'});
                // $('#dossier').closest('.form-group').find('label').find('span').remove();
                $('#js_conteneur_dossier > form > div > label > span.label.label-warning').remove();

                charger_banque($('#dossier').val());
            }
        });
    }
}

function activer_qTip()
{
    $(document).find('.js_tooltip').qtip({
        content: {
            text: function (event, api) {
                return $(this).attr('data-tooltip');
            }
        },
        title: $(this).find('div').text().trim(),
        position: {
            my: 'top right', // Position my top left...
            at: 'bottom right' // at the bottom right of...

        },
        style: {
            classes: 'qtip-youtube'
        }
    });
}


function test_security(response)
{
    if(response.trim().toLowerCase() == 'security') location.reload();
}


function charger_banque(dossierId){
    $.ajax({
        data: {
            dossierId: dossierId
        },

        url: Routing.generate('banque_releve_banque_dossier'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: true,
        dataType: 'html',
        success: function (data) {

            var res = JSON.parse(data);


            $('#js_banque').children().remove().end().append('<option value="">Tous</option>');

            var banques = res.banques;

            $.each(banques, function (k, v) {
                $('<option>').val(v.id).text(v.nom_banque).appendTo('#js_banque');
            });
        }

    });
}
