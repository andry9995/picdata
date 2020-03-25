/***********************************
 *          EVENEMENTS
 ***********************************/





/***********************************
 *          FONCTIONS
 ***********************************/
function go()
{
    eb_set_titre();
    $('#eb_etat_conteneur').empty();
    $('#js_export').empty();

    if(!set_parametre()) return;
    etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));

    lien = Routing.generate('etat_financier_show_item')+'/'+etat;
    verrou_fenetre(true);

    $.ajax({
        data: { dossier:dossier,exercice:JSON.stringify(exercice),mois:(mois.length != 12) ? JSON.stringify(mois) : 'Tous' },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#eb_etat_conteneur').html(data);
            //$('#eb_etat_conteneur').addClass('scroller');
            gerer_height();
            hauter = $('.scroller').height() * 0.92;
            width100 = $("#js_ef_table").parent().width();
            tableToGrid("#js_ef_table", {colModel:eb_get_col_model(width100), height:hauter,width:width100,});
            group_head_jqgrid('js_ef_table',getGroupHeaders(),true);
            $('.js_padding-top-4').parent().addClass('padding-top-4 niveau-1');
            $('.js_padding-top-3').parent().addClass('padding-top-3 niveau-2');
            $('.js_padding-top-2').parent().addClass('padding-top-2 niveau-3');
            $('.js_padding-top-1').parent().addClass('padding-top-1 niveau-4');
            $('.js_padding-top-0').parent().addClass('padding-top-0 niveau-5');
        }
    });
}

function eb_set_titre()
{
    etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));
    titre = '';
    if(etat < 2) titre += 'Bilan ';
    titre += $('#js_eb_menu a.eb_menu_active span').text();

    $('#js_eb_titre').html(titre.sansAccent().toUpperCase() + ' <small>(Comptabilit\xE9 en cours, non encore cl\xF4tur\xE9e)</small>');
}

function eb_get_col_model(width100)
{
    colModel1 = new Array();

    etat = parseInt($('#js_eb_menu a.eb_menu_active').attr('data-etat'));

    width_montant = 10;
    width_reste = 100 - (exercice.length * width_montant + 2);

    hidden_brut_amort = (etat == 0) ? false : true;

    colModel1.push({name:"js_ef_compte",align:"left",width:width100*width_reste/100 });
    colModel1.push({name:"js_ef_brut",align:"right",width:width100*width_montant/100,hidden:hidden_brut_amort});
    colModel1.push({name:"js_ef_amort",align:"right",width:width100*width_montant/100,hidden:hidden_brut_amort});
    for(i = 0;i < exercice.length;i++)
    {
        colModel1.push({name:"js_ef_net_"+exercice[i],align:"right",width:width100*width_montant/100});
    }

    return colModel1;
}

function getGroupHeaders()
{
    colModel1 = new Array();
    for(i=0; i<exercice.length; i++)
    {
        if(i == 0)
        {
            nbrColumn = 3;
            startColumn = 'js_ef_brut';
        }
        else
        {
            nbrColumn = 1;
            startColumn = 'js_ef_net_'+exercice[i];
        }
        colModel1.push({startColumnName: startColumn, numberOfColumns: nbrColumn, titleText: '<strong>'+exercice[i]+'</strong>'});
    }
    return colModel1;
}