/**
 * Created by SITRAKA on 20/07/2017.
 */
function exporter(li)
{
    var format = parseInt(li.attr('data-format')),
        div_hidden = $('.js_date_picker_hidden'),
        exercices = [],
        headers = [],
        bodys = [],
        title = $('#js_id_container_etat ul.nav-tabs li.active').text().sansAccent().toUpperCase().trim().replace(/\s/g, '_'),
        container = $('#js_id_container_etat .tab-content div.active .js_cl_container_etat');

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
     * HEADERS
     */
    var stylesHeader = get_styles($('.ui-th-column').first());
    container.find('.ui-jqgrid-htable').find('thead').find('tr').each(function(){
        var tds = [],
            i = 0;
        $(this).find('th').each(function(){
            var text = $(this).text(),
                styles = get_styles($(this)),
                col_span = parseInt($(this).attr('colspan'));
            if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
            tds.push({ t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:i} });
            i++;
        });
        headers.push(tds);
    });
    /**
     * BODYS
     */
    container.find('.ui-jqgrid-btable').find('tbody').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow'))
        {
            var tds = [];
            $(this).find('td').each(function(){
                var text = $(this).text(),
                    styles = get_styles($(this)),
                    textNumber = text.replace(/\s/g, '').replace(/,/g, '.'),
                    col_span = parseInt($(this).attr('colspan'));
                if (jQuery.isNumeric(textNumber)) text = textNumber;
                if (typeof col_span === 'undefined' || isNaN(col_span)) col_span = 1;
                tds.push({ t: text, styles: styles, pos:{colspan:col_span,rowspan:1,col:0} });
                //i++;
            });
            bodys.push(tds);
        }
    });

    var lien = Routing.generate('etat_b_export'),
        params = ''
            + '<input type="hidden" name="exp_dossier" value="'+$('#dossier').val()+'">'
            + '<input type="hidden" name="exercices" value="'+encodeURI(JSON.stringify(exercices))+'">'
            + '<input type="hidden" name="headers" value="'+encodeURI(JSON.stringify(headers))+'">'
            + '<input type="hidden" name="bodys" value="'+encodeURI(JSON.stringify(bodys))+'">'
            + '<input type="hidden" name="titles" value="'+title+'">'
            + '<input type="hidden" name="formats" value="'+format+'">'
            + '<input type="hidden" name="etat" value="-1">';
    $('#js_export').attr('action',lien).html(params).submit();
}
