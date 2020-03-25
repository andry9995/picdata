/**
 * Created by SITRAKA on 20/07/2017.
 */
function exporter(li)
{
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val()),
        container = $('#js_container_tabs .js_cl_container_etat'),
        div_hidden = $('.js_date_picker_hidden'),
        title = $('#js_container_tabs ul.nav-tabs li.active').text().sansAccent().toUpperCase().trim().replace(/\s/g, '_');

    var format = parseInt(li.attr('data-format')),
        headers = [],
        bodys = [],
        exercices = [],
        i;
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
     * bodys
     */
    if ((etat === 1 || etat === 2) && option === 1)//(etat != 1 && etat != 2 && option != 1)
    {
        container.find('.ibox').each(function(){
            $(this).find('thead').find('tr').each(function(){
                var tds = [];
                $(this).find('th').each(function(){
                    var text = $(this).text(),
                        styles = get_styles($(this)),
                        col_span = parseInt($(this).attr('colspan'));

                    if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
                    tds.push({t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:0}});
                });
                bodys.push(tds);
            });

            $(this).find('tbody').find('tr').each(function(){
                var tds = [];
                $(this).find('td').each(function(){
                    var text = $(this).text(),
                        styles = get_styles($(this)),
                        textNumber = text.replace(/\s/g, '').replace(/,/g, '.'),
                        col_span = parseInt($(this).attr('colspan'));
                    if (jQuery.isNumeric(textNumber)) text = textNumber;
                    if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
                    tds.push({t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:0}});
                });
                bodys.push(tds);
            });

            $(this).find('tfoot').find('tr').each(function(){
                var tds = [];
                $(this).find('th').each(function(){
                    var text = $(this).text(),
                        styles = get_styles($(this)),
                        textNumber = text.replace(/\s/g, '').replace(/,/g, '.'),
                        col_span = parseInt($(this).attr('colspan'));
                    if (jQuery.isNumeric(textNumber)) text = textNumber;
                    if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
                    tds.push({t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:0}});
                });
                bodys.push(tds);
            });

            bodys.push([{t: '', styles: null, pos:{colspan:3,rowspan:1,col:0}}]);
        });
    }
    else
    {
        $('#js_eb_table_to_grid').find('tr').each(function () {
            if(!$(this).hasClass('jqgfirstrow'))
            {
                var tds = [];
                $(this).find('td').each(function(){
                    if ($(this).css('display') !== 'none')
                    {
                        var text = $(this).text(),
                            textNumber = text.replace(/\s/g, '').replace(/,/g, '.'),
                            styles = get_styles($(this)),
                            col_span = parseInt($(this).attr('colspan'));
                        if (jQuery.isNumeric(textNumber)) text = textNumber;
                        if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
                        tds.push({t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:0}});
                    }
                });
                bodys.push(tds);
            }
        });
    }

    /**
     * headers
     */
    var stylesHeader = get_styles($('.ui-th-column').first());
    if (etat === 0 || etat === 1 || etat === 2)
    {
        if(option === 0)
        {
            var header = [];
            header.push({ pos:{colspan:1,rowspan:2,col:0}, styles:stylesHeader, t: 'Compte' });
            header.push({ pos:{colspan:1,rowspan:2,col:1}, styles:stylesHeader, t: 'Intitule' });

            var nbColExercice = (exercices.length > 1) ? 2 : 4;
            for (i = 0; i < exercices.length; i++)
            {
                var exercice = exercices[i];
                header.push({ t:exercice, styles:stylesHeader, pos:{colspan:nbColExercice,rowspan:1,col:(2 + (i * nbColExercice) )} });
            }
            headers.push(header);

            header = [];
            for (i = 0; i < exercices.length; i++)
            {
                var inte = 0;
                if (exercices.length === 1)
                {
                    header.push({ t:'Débit' , styles:stylesHeader,pos:{colspan:1,rowspan:1,col:(2 + (i * nbColExercice) )} });
                    header.push({ t:'Crédit' , styles:stylesHeader,pos:{colspan:1,rowspan:1,col:(2 + (i * nbColExercice) + 1 )} });
                    inte = 2;
                }

                header.push({ t:'Solde Débit' , styles:stylesHeader,pos:{colspan:1,rowspan:1,col:(2 + (i * nbColExercice) + inte )} });
                header.push({ t:'Solde Crédit' , styles:stylesHeader,pos:{colspan:1,rowspan:1,col:(2 + (i * nbColExercice) + 1 + inte )} });
            }
            headers.push(header);
        }

        if ((etat === 1 || etat === 2) && option === 1) title += '-' + 'AGEE';
    }
    else if (etat === 3)
    {
        headers.push([
            { pos:{colspan:1,rowspan:1,col:0}, styles:stylesHeader, t: 'Date' },
            { pos:{colspan:1,rowspan:1,col:1}, styles:stylesHeader, t: 'Jnl' },
            { pos:{colspan:1,rowspan:1,col:2}, styles:stylesHeader, t: 'Pièce' },
            { pos:{colspan:1,rowspan:1,col:3}, styles:stylesHeader, t: 'Libellé' },
            { pos:{colspan:1,rowspan:1,col:4}, styles:stylesHeader, t: 'Débit' },
            { pos:{colspan:1,rowspan:1,col:5}, styles:stylesHeader, t: 'Crédit' },
            { pos:{colspan:1,rowspan:1,col:6}, styles:stylesHeader, t: 'L' },
            { pos:{colspan:1,rowspan:1,col:7}, styles:stylesHeader, t: 'Solde Drédit' },
            { pos:{colspan:1,rowspan:1,col:8}, styles:stylesHeader, t: 'Solde Crédit' }
        ]);

        if (option === 0) title += '-' + 'GENERAL';
        else if (option === 1) title += '-' + 'CLIENT';
        else title += '-' + 'FOURNISSEUR';
    }
    else if(etat === 4)
    {
        headers.push([
            { pos:{colspan:1,rowspan:1,col:0}, styles:stylesHeader, t: 'Date' },
            { pos:{colspan:1,rowspan:1,col:1}, styles:stylesHeader, t: 'Jnl' },
            { pos:{colspan:1,rowspan:1,col:2}, styles:stylesHeader, t: 'Compte' },
            { pos:{colspan:1,rowspan:1,col:3}, styles:stylesHeader, t: 'Piéce' },
            { pos:{colspan:1,rowspan:1,col:4}, styles:stylesHeader, t: 'Libellé Opération' },
            { pos:{colspan:1,rowspan:1,col:5}, styles:stylesHeader, t: 'Drédit' },
            { pos:{colspan:1,rowspan:1,col:6}, styles:stylesHeader, t: 'Crédit' }
        ]);
    }
    else if(etat === 5)
    {
        headers.push([
            { pos:{colspan:1,rowspan:1,col:0}, styles:stylesHeader, t: 'Date' },
            { pos:{colspan:1,rowspan:1,col:1}, styles:stylesHeader, t: 'Jnl' },
            { pos:{colspan:1,rowspan:1,col:2}, styles:stylesHeader, t: 'Libellé Jnl' },
            { pos:{colspan:1,rowspan:1,col:3}, styles:stylesHeader, t: 'Total Drédit' },
            { pos:{colspan:1,rowspan:1,col:4}, styles:stylesHeader, t: 'Total Crédit' }
        ]);
    }

    var lien = Routing.generate('etat_b_export'),
        params = ''
            + '<input type="hidden" name="exp_dossier" value="'+$('#dossier').val().trim()+'">'
            + '<input type="hidden" name="exercices" value="'+encodeURI(JSON.stringify(exercices))+'">'
            + '<input type="hidden" name="headers" value="'+encodeURI(JSON.stringify(headers))+'">'
            + '<input type="hidden" name="bodys" value="'+encodeURI(JSON.stringify(bodys))+'">'
            + '<input type="hidden" name="titles" value="'+title+'">'
            + '<input type="hidden" name="formats" value="'+format+'">'
            + '<input type="hidden" name="etat" value="'+etat+'">';
    $('#js_export').attr('action',lien).html(params).submit();
}
