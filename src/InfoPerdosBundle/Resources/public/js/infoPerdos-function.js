/**
 * Created by MAHARO on 13/01/2017.
 */
function reloadGrid(selector, url, callback) {
    selector.setGridParam({
        url: url,
        datatype: 'json',
        loadonce: true,
        page: 1
    }).trigger('reloadGrid');

    if (typeof callback == 'function') {
        callback();
    }
}

$(document).ready(function () {
    $(document).on('click', '#js-caracteristique-tab', function (event) {
        $('#js-regime-fiscal-tab').click();
    });

    $(document).on('click', '#js-js-statut-dirigeant-tab-tab', function (event) {
        $('#js-regime-suivi-tab').click();
    });

    $(document).on('change','#site',function(){
        charger_dossier_info_perdos();
    });
});


function setChosen(dossierId){


    //
    // var selector = "#js_tva_taux";
    // var chosen_control = $(selector);
    // chosen_control.chosen();
    // var chosen_element = $(selector + "_chosen");
    // var chosen_input = chosen_element.find("input");
    // chosen_input.keydown(function (e) {
    //     if (e.which == 13) {
    //         e.preventDefault();
    //
    //
    //         if($('#js_date_tva').prop('disabled') == false) {
    //             $('#js_date_tva').focus();
    //         }
    //         else{
    //             $('#js_taxe_salaire').focus();
    //         }
    //
    //         // var dossierId = $('#dossier').val();
    //         saveTvaTauxV2(dossierId);
    //     }
    // });



    var config = {
        '.chosen-select'           : {},
        '.chosen-select-deselect'  : {allow_single_deselect:true},
        '.chosen-select-no-single' : {disable_search_threshold:10},
        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
        '.chosen-select-width'     : {width:"95%"}
    };
    for (var selector in config) {
        $(selector).chosen('destroy');

        if(dossierId == 0){
            $(selector).chosen('destroy');
            $(selector).removeAttr('multiple').prop('disabled', true);
            $(selector).removeAttr('required');
            $(selector).val("");
        }
        else {
            $(selector).chosen(config[selector]);

            $(selector).find("input").keydown(function(e) {
                if (e.which == 13) {
                    e.preventDefault();


                    if ($('#js_date_tva').prop('disabled') == false) {
                        $('#js_date_tva').focus();
                    }
                    else {
                        $('#js_taxe_salaire').focus();
                    }

                    saveTvaTauxV2(dossierId);
                }
            });
        }
    }

}

function setScrollerHeigt() {
    $(".scroller").css("height", $('.panel-body').height() - 80);
    $(".ibox-content .scroller").css("height", 200);
    $('#tab-recap .scroller').css("height", $('.panel-body').height() -90);
}


//charger site
function charger_site_info_perdos()
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
            charger_dossier_info_perdos();
        }
    });
}
//charger dossier a partir du site
function charger_dossier_info_perdos(tous)
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
                // test_security(data);
                $('#js_conteneur_dossier').html(data);

                var nbDoss = $('#dossier option').length;
                //Ajout bouton modifier raha 1 ihany ny dossier
                // addEditDossierButton(nbDoss);
            }
        });
    }
}

function addAjoutButton(nbDossier) {
    $('.btn-dossier-edit').remove();
    if (nbDossier == 2) {
        var editDossier = $('#wrapper-content > div:nth-child(1) .col-sm-12 .row div:nth-child(4)');
        editDossier.append('<button class="btn btn-default btn-dossier-add" data-toggle="tooltip" data-placement="bottom" title="Clickez ici pour créer un nouveau dossier"><i class="fa fa-plus"></i></button>');
    }
}
