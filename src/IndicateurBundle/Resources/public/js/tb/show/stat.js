/**
 * Created by SITRAKA on 16/05/2019.
 */

$(document).ready(function(){
    $(document).on('click','#id_stat',function(){
        var stats = {};
        for (var i = 0; i < all_datas.length; i++)
        {
            var exo = 2010,
                temp = {};
            $.each(all_datas[i], function (index, v) {
                if (index === 'exo') exo = v;
                else if (index !== 'dossier')
                {
                    var ind = parseInt(index);
                    if (ind !== 0)
                    {
                        var info_perdos = parseInt(all_entetes[0]);
                        if (ind > info_perdos)
                        {
                            var valeur = 0;
                            if (typeof v === 'undefined') valeur = 0;
                            else
                            {
                                if (typeof v.type !== 'undefined' && parseInt(v.type) === 1) valeur = v.v;
                                if (v.p === 'NA') valeur = 0;

                                var dec = v.r;
                                var format = parseInt($('#js_id_type_affichage').find('li.active').attr('data-type'));
                                if (format === 1)
                                {
                                    var u = parseInt(v.u),
                                        uniteCoeff = (u === 1 && !$('#js_id_variation').is(':checked')) ? 1 : 1;
                                    if (u === 1) uniteCoeff = 100;
                                    valeur = (v.v * uniteCoeff).toFixed(dec);
                                }
                                else if (format === 2)
                                {
                                    valeur = v.p.toFixed(dec);
                                }
                                else valeur = 0;
                            }
                            temp[index] = valeur;
                        }
                    }
                }
            });

            if (!(exo in stats))
            {
                stats[exo] = {};
            }

            $.each(temp, function (index, v) {
                if (index in stats[exo]) stats[exo][index] = stats[exo][index] + parseFloat((isNaN(v) ? 0 : v));
                else stats[exo][index] = parseFloat(isNaN(v) ? 0 : v);
            });
        }

        show_modal('<table id="table_stat"></table>','statistique par Compta',undefined,'modal-xx-lg');
        var editurl = 'test.php',
            table_selected = $('#table_stat'),
            w = table_selected.parent().width(),
            h = $(window).height() - 250,
            res = [];

        $.each(stats, function (index, v) {
             var re = {};
             re['exo'] = index;

            $.each(v, function (i, v1) {
                re[i] = v1;
            });
            res.push(re);
         });

        set_table_jqgrid(res,h,model_stat(all_entetes),model_stat(all_entetes,w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
    });
});

function model_stat(model,w)
{
    var colM = [],i,
        info_perdos = parseInt(model[0]);

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'exo', index:'exo', sortable:true, sorttype:'string', width:  60, align:'center' });
        for(i = 1;i < model.length; i++)
        {
            if (i > info_perdos)
                colM.push({ name:i, index:i, sortable:true, sorttype:'string', width:  80, align:'right',formatter: function (v) { return number_format(v, ((v - Math.floor(v)) !== 0 ) ? 2 : 0,',',' ',true); } });
        }
    }
    else
    {
        colM.push('Exercice');
        for(i = 1;i < model.length; i++)
        {
            if (i > info_perdos)
            {
                var texte = model[i].l;
                if (model[i].n.trim() !== '') texte += '<br><span class="text-primary">' + model[i].n.trim() + '</span>';

                var infoBulle = model[i].p + ' (%)';
                if (typeof model[i].infoBulles !== 'undefined')
                {
                    infoBulle = model[i].infoBulles.cles.join(', ');
                }

                texte += '<span class="hidden '+(infoBulle !== '' ? 'js_tooltip_header' : '')+'" data-title="' + infoBulle +'"></span>';
                colM.push(texte);
            }
        }
    }

    return colM
}

function set_stat(stats,cles)
{
    $('#id_stat_container').html(
        '<span class="btn btn-sm btn-white" id="id_stat">Statistique</span>'
    );

    var table = "<table class='table'>";

    var arr_dossiers = [],
        arr_comptas = [],
        arr_dossier_cles = [],
        arr_compta_cles = [];

    $('#js_id_table').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow'))
        {
            var dos = $(this).find('.dossier').text().trim(),
                exo = $(this).find('.exo').text().trim(),
                nb_cle = parseInt($(this).find('.cle').text().trim());

            if (isNaN(nb_cle)) nb_cle = 0;

            if (!arr_dossiers.in_array(dos)) arr_dossiers.push(dos);
            if (!arr_comptas.in_array(dos + '_' + exo)) arr_comptas.push(dos + '_' + exo);
            if (nb_cle > 0 && !arr_dossier_cles.in_array(dos)) arr_dossier_cles.push(dos);
            if (nb_cle > 0 && !arr_compta_cles.in_array(dos + '_' + exo)) arr_compta_cles.push(dos + '_' + exo);
        }
    });

    table +=
        '<tr>' +
            "<th>Au moins une Clé / Total (Dossier)</th><td align='right'>"+number_format(arr_dossier_cles.length, 0, ',', ' ')+ ' / ' +number_format(arr_dossiers.length, 0, ',', ' ')+'</td>' +
        '</tr>';

    table +=
        '<tr>' +
            "<th>Au moins une Clé / Total (Compta)</th><td align='right'>"+number_format(arr_compta_cles.length, 0, ',', ' ')+ ' / ' +number_format(arr_comptas.length, 0, ',', ' ')+'</td>' +
        '</tr>';

    var cles_totals = [],
        cles_index = [];
    $.each(cles, function(key_cle, cle_object){
        var index = (cles_index.indexOf(cle_object.cle));

        if (index === -1)
        {
            cles_totals.push(cle_object);
            cles_index.push(cle_object.cle);
        }
        else cles_totals[index].occ = cles_totals[index].occ + cle_object.occ;
    });

    var total_cle = 0;
    $.each(cles_totals, function(key_cle, cle_object){
        table +=
            '<tr>' +
                '<th>'+cle_object.cle+"</th><td align='right'>"+number_format(cle_object.occ, 0, ',', ' ')+'</td>' +
            '</tr>';
        total_cle += cle_object.occ;
    });

    table +=
        '<tr>' +
            "<th>Total Clé</th><td align='right'>"+number_format(total_cle, 0, ',', ' ')+'</td>' +
        '</tr>';

    table += '</table>';

    statistique = {
        dossier: arr_dossiers,
        dossier_cles: arr_dossier_cles,
        compta: arr_comptas,
        compta_cles: arr_compta_cles,
        cles_totals: cles_totals
    };

    $('#id_stat').qtip({
        content: {
            text: function (event, api) {
                return table;
            }
        },
        position: { my: 'top right', at: 'bottom left' },
        style: {
            classes: 'qtip-dark qtip-shadow'
        }
    });
}
