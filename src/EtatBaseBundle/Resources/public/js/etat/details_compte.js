/**
 * Created by SITRAKA on 07/07/2017.
 */
$(document).on('click','.js_show_detail_compte',function(){
    var div_hidden = $('.js_date_picker_hidden'),
        exercices = [],
        periodes = [],
        moiss = [],
        titre = $(this).text() + ' - ' + $(this).closest('tr').find('td[aria-describedby="js_eb_table_to_grid_js_eb_intitule"]').text();
    /**
     * exercice
     */
    div_hidden.find('.js_dpk_exercice').each(function(){
        if($(this).hasClass(dpkGetActiveDatePicker()))
        {
            exercices.push($(this).text().trim());
        }
    });
    /**
     * mois
     */
    div_hidden.find('.js_dpk_periode').each(function(){
        if($(this).hasClass('js_dpk_mois'))
        {
            var m = $(this).attr('data-value').trim();
            moiss.push(((m.length === 1) ? '0' : '') + m);
        }
        if($(this).hasClass(dpkGetActiveDatePicker()))
        {
            var array_mois = [],
                value = parseInt($(this).attr('data-val'));
            var niveau = parseInt($(this).attr('data-niveau'));
            //mois
            if(niveau === 3)
            {
                var mois_val = $(this).attr('data-value').trim();
                periodes.push({'libelle':$(this).text().trim(), 'moiss':[((mois_val.length === 1) ? '0' : '') + mois_val]});
            }
            //trimestre; semestre; annee
            else if(niveau === 2 || niveau === 1 || niveau === 0)
            {
                //each moiss
                div_hidden.find('.js_dpk_mois').each(function(){
                    var mere = -2;
                    if(niveau === 2) mere = parseInt($(this).attr('data-mere-trimestre'));
                    else if(niveau === 1) mere = parseInt($(this).attr('data-mere-semestre'));
                    else if(niveau === 0) mere = parseInt($(this).attr('data-mere-annee'));
                    if(!$(this).hasClass(dpkGetActiveDatePicker()) && mere === value)
                    {
                        var mois_val = $(this).attr('data-value').trim();
                        array_mois.push(((mois_val.length === 1) ? '0' : '') + mois_val);
                    }
                });
                periodes.push({'libelle':$(this).text().trim(), 'moiss':array_mois});
            }
        }
    });//moiss; periodes{libelle, moiss}

    show_compte_details($(this).attr('data-id'),$(this).attr('data-type'),exercices,moiss,periodes,titre);
});
