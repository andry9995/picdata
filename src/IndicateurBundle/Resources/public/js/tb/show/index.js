var all_datas = [],
    all_stats = [],
    all_cles = [],
    all_entetes = [],
    statistique = {},
    exos = [];

$(document).ready(function(){
    texte_dossier_tous = 1;
    dossier_all_exercice = true;
    $('#js_id_container_dossiers').height(
        $(window).height() - 240
    );
    //lauch_charger_dossier = false;
    charger_site();

    $(document).on('click','#id_go',function(){
        go();
    });

    $(document).on('change','#site',function(){
        //go();
    });

    $(document).on('change','#dossier',function(){
        //charger_exos();
        //go();
    });

    $(document).on('change','#js_id_variation',function(){
        //go();
    });

    $(document).on('click','.js_cl_type_affichage',function(){
        $('.js_cl_type_affichage').removeClass('active');
        $(this).addClass('active');
        //go();
    });
});

function go()
{
    var div_hidden = $('#js_id_interval_hidden'),
        p1_container = div_hidden.find('.js_cl_interval_start_container'),
        p1_tr_periode = p1_container.find('table tbody tr .td-active'),
        p1 = {
            exercice: parseInt(p1_container.find('table thead tr .td-active').text().trim()),
            niveau: parseInt(p1_tr_periode.attr('data-niveau')),
            val: parseInt(p1_tr_periode.attr('data-val'))
        },
        p2_container = div_hidden.find('.js_cl_interval_end_container'),
        p2_tr_periode = p2_container.find('table tbody tr .td-active'),
        p2 = {
            exercice: parseInt(p2_container.find('table thead tr .td-active').text().trim()),
            niveau: parseInt(p2_tr_periode.attr('data-niveau')),
            val: parseInt(p2_tr_periode.attr('data-val'))
        },
        p = { p1:p1, p2:p2 },
        variation = $('#js_id_variation').is(':checked') ? 1 : 0;

    exos = [];

    $('.cl_exo_item:checked').each(function(){
        exos.push(parseInt($(this).attr('data-exercice')));
    });

    all_datas = [];
    all_stats = [];
    all_cles = [];
    all_entetes = [];
    statistique = {};

    var client_text = $('#client option:selected').text().trim().toUpperCase(),
        dossier_text = $('#dossier option:selected').text().trim().toUpperCase();

    if (client_text === 'TOUS' || client_text === '')
    {
        $.ajax({
            data: {
                client: $('#js_zero_boost').val(),
                site: $('#js_zero_boost').val(),
                exos: JSON.stringify(exos)
            },
            type: 'POST',
            url: Routing.generate('ind_tb_all_dossiers'),
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) go_ajax(data,0,p,variation);
                else set_table([]);
            }
        });
    }
    else
    {
        if (dossier_text === 'TOUS' || dossier_text === '')
        {
            $.ajax({
                data: {
                    client: $('#client').val(),
                    site: $('#site').val()
                },
                type: 'POST',
                url: Routing.generate('ind_tb_all_dossiers'),
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) go_ajax(data,0,p,variation);
                    else set_table([]);
                }
            });
        }
        else go_ajax([$('#dossier').val()],0,p,variation);
    }
}

function go_ajax(dossiers,index,p,variation)
{
    if (index === 0) show_info('Calcul dans ' + dossiers.length + ' Dossier(s)','Veuillez-patientez','warning');

    $.ajax({
        data: {
            client:$('#js_zero_boost').val(),
            site:$('#js_zero_boost').val(),
            dossier:dossiers[index],
            p:JSON.stringify(p),
            variation:variation,
            affichage: $('#id_tb_type').val(),
            exos: JSON.stringify(exos)
        },
        type: 'POST',
        url: Routing.generate('ind_tb_dossiers'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            /*$('#js_id_container_dossiers').html(data);
            return;*/

            var dataObject = $.parseJSON(data),
                entetes = dataObject.entetes,
                dataObjects = dataObject.datas;
            all_datas = all_datas.concat(dataObjects);
            all_stats = all_stats.concat(dataObject.stats);
            all_cles = all_cles.concat(dataObject.cles);
            all_entetes = entetes;

            if (index !== dossiers.length - 1) go_ajax(dossiers,index + 1,p,variation);
            else
            {
                var groupeds = [],
                    start = parseInt(entetes[0]) + 1 + parseInt($('#id_tb_type').val());

                if (dataObject.entetesGoupeds.length > 0)
                {
                    for (var v = 0; v < dataObject.entetesGoupeds.length; v++)
                    {
                        if (dataObject.entetesGoupeds[v].nb > 0)
                        {
                            groupeds.push({startColumnName: start, numberOfColumns: dataObject.entetesGoupeds[v].nb, titleText: '<strong>'+dataObject.entetesGoupeds[v].n+'</strong>'})
                        }
                        start += parseInt(dataObject.entetesGoupeds[v].nb);
                    }
                }
                set_table(entetes, groupeds);
            }
        }
    });
}

function set_table(entetes, groupeds)
{
    var new_table = '<table id="js_id_table"></table>';
    $('#js_id_container_dossiers').height($(window).height() - 240);
    $('#js_id_container_dossiers').html(new_table);

    var editurl = 'test.php',
        table_selected = $('#js_id_table'),
        w = table_selected.parent().width(),
        h = $('#js_id_container_dossiers').parent().height() - 120;

    set_table_jqgrid(all_datas,h,table_get_col_model(entetes),table_get_col_model(entetes,w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
    if (parseInt($('#id_tb_type').val()) === 1) set_stat(all_stats,all_cles);

    groupeds = typeof w !== 'undefined' ? groupeds : [];
    if (groupeds.length > 0)
        group_head_jqgrid('js_id_table',groupeds,true);

    var v,
        colonnes = [];
    var start = parseInt(entetes[0]) + 1 + ( (parseInt($('#id_tb_type').val()) === 1) ? 2 : 0 );
    for (v = 0; v < groupeds.length; v++)
    {
        colonnes.push(start);
        start += parseInt(groupeds[v].numberOfColumns);
    }

    $('#js_id_table tr').each(function(){
        var c = 0;
        $(this).find('td').each(function(){
            if (colonnes.in_array(c))
                $(this).addClass('bordered-right');
            else if (colonnes.in_array(c - 1))
                $(this).addClass('bordered-left');
            c++;
        });
    });
}

function table_get_col_model(model,w)
{
    var colModel1 = [],i,
        type_affichage = parseInt($('#id_tb_type').val()),
        info_perdos = parseInt(model[0]);
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'dossier', index:'dossier', sortable:true, width:  300, classes:'dossier', frozen : true, formatter:function(v){
            var cla = (type_affichage === 1) ? 'cl_entity' : '';
            return '<span class="'+cla+'" data-dossier="'+v.id+'" data-exercice="'+v.ex+'">'+v.d+'</span>';
        } });
        if (type_affichage === 1) colModel1.push({ name:'exo', index:'exo', sortable:true, width:  80, classes:'exo', align:'center', frozen : true });
        for(i = 1;i < model.length; i++)
        {
            if (i > info_perdos)
            {
                var cl = (i === info_perdos + 1 && type_affichage === 1) ? 'cle cl_liste_occurence pointer' : '';
                colModel1.push({ name:i, index:i, sortable:true, sorttype:'string', width:  80, classes:cl, align:'center',formatter: function (v) { return formatter_cell(v); } });
            }
            else
                colModel1.push({ name:i, index:i, sortable:true, sorttype:'string', width:  80, formatter: function (v) { return v.v } });
        }
    }
    else
    {
        colModel1.push('Dossier');
        if (type_affichage === 1) colModel1.push('Exercice');
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
                colModel1.push(texte);
            }
            else
                colModel1.push(model[i]);
        }
    }
    return colModel1;
}

function formatter_cell(v)
{
    if (typeof v === 'undefined') return '';

    if (typeof v.type !== 'undefined' && parseInt(v.type) === 1)
    {
        var infobulles = v.infoBulles,
            infobulle = "<table class='table'>";

        for (var i = 0; i < infobulles.length; i++)
        {
            infobulle += '<tr>';
            infobulle += '<td>'+infobulles[i].cle+'</td><td>'+infobulles[i].exo+'</td><td>'+infobulles[i].occ+'</td>';
            infobulle += '</tr>';
        }

        infobulle += '</table>';
        return '<span class="'+((infobulles.length > 0) ? 'js_tooltip_header' : '')+'" data-title="'+infobulle+'">'+number_format(v.v, 0, ',', ' ',true)+'</span>';
    }

    if (v.p === 'NA') return '<span class="text-warning">' + v.p + '</span>';

    var dec = v.r,
        class_t;
    var format = parseInt($('#js_id_type_affichage').find('li.active').attr('data-type'));
    if (format === 1)
    {
        var u = parseInt(v.u),
            unite = (u === 1 && v.v !== 0) ? ' %' : '',
            uniteCoeff = (u === 1 && !$('#js_id_variation').is(':checked')) ? 1 : 1;

        if (u === 1) uniteCoeff = 100;

        class_t = v.ic.replace(/fa/g, '').replace(/fa-2x/g, '');
        var n_format = number_format((v.v * uniteCoeff),dec,',',' ',true);

        if (n_format === '') unite = '';

        return '<span class="' + class_t + '">' + n_format + unite +'</span>';
    }
    else if (format === 2)
    {
        class_t = v.ic.replace(/fa/g, '').replace(/fa-2x/g, '');
        return '<span class="' + class_t + '">' + number_format(v.p,dec,',',' ',true) +'</span>';
    }
    else return '<i class="fa-2x '+v.ic+'"></i>';
}

function after_charged_dossier()
{
    //go();
}

function  after_charged_dossier_not_select()
{
    //go();
}

/*function after_charged_site()
{
    go();
}*/

function loadCompleteJQgrid()
{
    $('.js_tooltip_header').each(function(){
        $(this).addClass('ici');
        var desc = $(this).attr('data-title').trim();
        if (desc !== '')
        {
            if ($(this).closest('th').length > 0) $(this).closest('th').addClass('js_tooltip').attr('data-tooltip',desc);
            if ($(this).closest('td').length > 0) $(this).closest('td').addClass('js_tooltip').attr('data-tooltip',desc);
        }
    });
    activer_qTip();
}

function after_change_client_tous()
{
    //go();
}