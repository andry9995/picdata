/**
 * Created by SITRAKA on 29/11/2018.
 */
var class_tr_edited = 'tr_releve_edited',
    tiers = [],
    pccs = [],
    all_datas = [],
    isPieceManquante = $('.isPieceManquante').attr('data-value');
$(document).ready(function(){
    dossier_depend_exercice = true;
    $('#exercice').val((new Date()).getFullYear());
    charger_site();

    /*console.log(isPieceManquante);
    if(!isPieceManquante){
        $('#js_id_conteneur_banque').addClass('hidden');
        $('#js_id_conteneur_compte').addClass('hidden');
        go();
    }*/

    $(document).on('click','.cl_tab_li',function(){
        var tab_element = null;
        $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
            if ($(this).hasClass('active')) tab_element = $(this);
        });
        var type = parseInt(tab_element.attr('data-type'));
        $('#id_show_exception').addClass('hidden');
        if ([2,3,4].in_array(type)) $('#id_show_exception').removeClass('hidden');

        if ([5,6,7,10,11].in_array(type)) // 10=>frns, 11=>clt
        {
            $('#id_interval').closest('.div_container').removeClass('hidden');
            $('#js_banque_compte').closest('.div_container').addClass('hidden');
            $('#js_id_conteneur_banque').addClass('hidden');
            $('#id_conteneur_date_anciennete').removeClass('hidden');
        }
        else
        {
            $('#id_interval').closest('.div_container').addClass('hidden');
            $('#js_banque_compte').closest('.div_container').removeClass('hidden');
            $('#js_id_conteneur_banque').removeClass('hidden');
            $('#id_conteneur_date_anciennete').addClass('hidden');
        }

        if([8,9].in_array(type))
        {
            $('#js_id_conteneur_banque').addClass('hidden');
            $('#js_id_conteneur_compte').addClass('hidden');
        }

        go();
    });

    $(document).on('change','#dossier',function(){
        if(isPieceManquante == 1)
            charger_banque();
        else
            go();
    });

    $(document).on('change','#js_banque',function(){
        charger_banque_compte();
    });

    $(document).on('change','#js_banque_compte',function(){
        go();
    });

    $(document).on('click','.cl_image',function(){
        show_image_pop_up($(this).attr('data-id'));
    });
});

function charger_banque()
{
    if($('#dossier option:selected').text().trim() === '')
    {
        $('#dossier').closest('.form-group').addClass('has-error');
        $('#js_id_conteneur_banque').html(
            '        <div class="form-horizontal">' +
            '            <div class="form-group">' +
            '                <label class="control-label col-lg-2" for="js_banque">' +
            '                    <span>Bq</span>' +
            '                    <span class="label label-warning">0</span>' +
            '                </label>' +
            '                <div class="col-lg-10">' +
            '                    <select class="form-control disabled" id="js_banque">' +
            '                        <option value="{{ 0|boost }}"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );
        $('#js_id_conteneur_compte').html(
            '        <div class="form-horizontal">' +
            '            <div class="form-group">' +
            '                <label class="control-label col-lg-2" for="js_banque_compte">' +
            '                    <span>N&deg;&nbsp;Cpt</span>' +
            '                    <span class="label label-warning">0</span>' +
            '                </label>' +
            '                <div class="col-lg-10">' +
            '                    <select class="form-control disabled" id="js_banque_compte">' +
            '                        <option value="{{ 0|boost }}"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );
        vider_table();
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque_dossier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_conteneur_banque').html(data);
            charger_comptes();
        }
    });
}

function charger_banque_compte()
{
    $.ajax({
        data: { dossier:$('#dossier').val(), banque:$('#js_banque').val(), tous:1 },
        type: 'POST',
        url: Routing.generate('banque_compte_dossier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_conteneur_compte').html(data);
            go();
        }
    });
}

function after_load_dossier()
{
    charger_banque();
}

function go()
{
    var tab_element = null;
    $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
    var type = parseInt(tab_element.attr('data-type'));

    var li_active = $('#id_tabs').find('.nav').find('.cl_tab_li.active');
    li_active.find('.cl_nb').addClass('hidden').text('');

    vider_table();

    var d_el = $('#dossier');
    var c_el = $('#client');
    var listDossier  = [];

    d_el.find('option').each(function(){
        var dossierId = $(this).attr('value');
        if(dossierId != 0)
            listDossier.push(dossierId);
    });
    if (d_el.find('option:selected').text().trim() === '')
    {
        if(isPieceManquante == 1){
            d_el.closest('.form-group').addClass('has-error');
            return;
        }
    }
    else d_el.closest('.form-group').removeClass('has-error');

    var intersvals_temps = $('#id_interval').val().trim().split('-'),
        intervals = [],
        i;

    for (i = 0; i < intersvals_temps.length; i++)
    {
        var val = parseInt(intersvals_temps[i]);
        if (!isNaN(val) && !intervals.in_array(val)) intervals.push(val);
    }

    if (intervals.length === 0) intervals.push(90);
    intervals.push(500000);
    intervals.sort(function(a, b){return a - b});

    $.ajax({
        data: {
            dossier: d_el.val(),
            exercice: $('#exercice').val(),
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val(),
            type: type,
            intervals: JSON.stringify(intervals),
            date: $('#id_date_anciennete').val(),
            client: c_el.val(),
            listDossier: listDossier,
            isPieceManquante : isPieceManquante
        },
        url: Routing.generate('banque_pm_item'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            all_datas = [];
            var nb = 0;
            if (type !== 1) all_datas = $.parseJSON(data);
            if (type === 2 || type === 3 || type === 4)
            {
                nb = all_datas.length;
                set_table(tab_element,all_datas);
            }
            else if (type === 0)
            {
                set_table_rm(tab_element,all_datas);
            }
            else if ([5,6].in_array(type))
            {
                nb = all_datas.datas.length;
                set_table_facture_np(tab_element,all_datas);
            }else if(type === 8){
                set_table_rm_all_dossier(tab_element,all_datas);
            }else if(type === 9){
                set_table_autr_pm(tab_element,all_datas);
            }
            else if ([10,11].in_array(type))
            {
                nb = all_datas.datas.length;
                set_table_new_facture_np(tab_element,all_datas);
            }
            else tab_element.find('.panel-body').html(data);

            if (typeof li_active !== 'undefined' && nb !== 0)
                li_active.find('.cl_nb').removeClass('hidden').text(nb);
        }
    });
}

function set_table(tab_element,datas)
{
    var type = parseInt(tab_element.attr('data-type')),
        table = '<table id="table_pm_'+type+'"></table>';
    tab_element.find('.panel-body').html(table);

    var table_selected = $('#table_pm_'+type),
        w = table_selected.parent().width(),
        h = $(window).height() - 210,
        editurl = 'index.php';

    set_table_jqgrid(datas,h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
}

function get_col_model(w)
{
    var tab_element = null;
    $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
    var type = parseInt(tab_element.attr('data-type')),
        colM = [],
        bcSelect = ($('#js_banque_compte option:selected').text() !== '' && $('#js_banque_compte option:selected').text().toUpperCase() !== 'TOUS');

    if(typeof w !== 'undefined')
    {
        var widths =
            [
                [ (!bcSelect) ? 8 : 0, 8, 8,(!bcSelect) ? 11 : 19, 0, 8, 2, 7,10, 10, 0, 0, 6, 6, 6, 6 ],
                [ (!bcSelect) ? 8 : 0, 8, 8,(!bcSelect) ? 11 : 19, 8, 0, 2, 7,10, 10, 0, 0, 6, 6, 6, 6 ],
                [ (!bcSelect) ? 9 : 0, 9, 18,(!bcSelect) ? 18 : 27, 0, 9, 0, 0, 0, 0, 0, 0, 0, 7 ]
            ],
            ws = widths[type - 2];

        if (!bcSelect)
            colM.push({ name:'c', index:'c', sortable:true, width: ws[0] * w/100 });

            colM.push({ name:'i', index:'i', sortable:true, width: ws[1] * w/100, align:'center', formatter: function(v){return image_formatter(v)} });
            colM.push({ name:'d', index:'d', sortable:true, width: ws[2] * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d', newformat: 'd/m/Y'} });
            colM.push({ name:'l', index:'l', sortable:true, width: ws[3] * w/100 });

        if (type !== 2 && type !== 4)
            colM.push({ name:'rc', index:'rc', sortable:true, width: ws[4] * w/100, sorttype: 'number', classes:'text-primary gras', align:'right', formatter: function(v){return number_format(v, 2, ',', ' ',true)} });
        if (type !== 3)
            colM.push({ name:'dp', index:'dp', sortable:true, width: ws[5] * w/100, sorttype: 'number', classes:'text-danger gras', align:'right', formatter: function(v){return number_format(v, 2, ',', ' ',true)} });
        if (type === 2 || type === 3)
        {
            colM.push({ name:'gl', index:'gl', sortable:true, width: ws[7] * w/100, align:'center', formatter: function(v){return gl_formatter(v)} });
            colM.push({ name:'cl', index:'cl', sortable:true, width: ws[6] * w/100, align:'center', formatter: function(v){return icon_formatter(v)} });
            colM.push({ name:'cl', index:'cl', sortable:true, width: ws[7] * w/100, classes:'text-info' });
            colM.push({ name:'dec', index:'dec', sortable:true, width: ws[8] * w/100, formatter: function(v){return decision_formatter(v)} });
            colM.push({ name:'obs', index:'obs', sortable:true, width: ws[9] * w/100, formatter: function(v){ return observation_formatter(v) } });
        }

        if (type === 4)
        {
            colM.push({ name:'n', index:'n', sortable:true, width: ws[10] * w/100, formatter: function(v){return nature_tiers_formatter(v,1)} });
            colM.push({ name:'t', index:'t', sortable:true, width: ws[11] * w/100, formatter: function(v){return nature_tiers_formatter(v,0)} });
        }
            colM.push({ name:'imt', index:'imt', sortable:true, width: ws[12] * w/100, align:'center', formatter: function(v){return image_formatter(v)} });

        if (type === 2 || type === 3)
        {
            colM.push({ name:'ch', index:'ch', sortable:true, width: ws[14] * w/100, align:'center', formatter: function(v){return compte_formatter(v)} });
            colM.push({ name:'tva', index:'tva', sortable:true, width: ws[15] * w/100, align:'center', formatter: function(v){return compte_formatter(v)} });
        }
    }
    else
    {
        if (!bcSelect)
            colM.push('Compte');

            colM.push('Pièce');
            colM.push('Date');
            colM.push('Libellé');
        if (type !== 2 && type !== 1 && type !== 4) colM.push('Recette');
        if (type !== 3) colM.push('Dépense');
        if (type === 2 || type === 3)
        {
            colM.push('GL');
            colM.push('');
            colM.push('Clé');
            colM.push('Instruction');
            colM.push('Observation');
        }
        if (type === 4)
        {
            colM.push('Nature');
            colM.push('Tiers');
        }
            colM.push('Envoyer');

        if (type === 2 || type === 3)
        {
            colM.push('Résultat');
            colM.push('Tva');
        }
    }

    return colM;
}

function gl_formatter(v)
{
    if (v === null) return '';
    else if (Array.isArray(v) && v.length === 1)
        v = v[0];

    return '<span class="pointer cl_compte_detail" data-id="'+v.id+'" data-type="'+v.t+'"><i class="fa fa-book"></i></span>';
}

function image_formatter(v)
{
    if (v !== null)
        return '<span class="pointer js_show_image text-info" data-id_image="'+v.id+'">'+v.n+'</span>';

    return '<span class="pointer js_show_upload_image" data-type="0"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>';
}

function icon_formatter(v)
{
    if (v.trim() !== '')
        return '<i class="fa fa-key text-info qtip_new" aria-hidden="true" title="Affecté par Clé"></i>';
    return '';
}

function vider_table()
{
    var tab_element = null;
    $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
}

function charger_comptes()
{
    $.ajax({
        data: {
            dossier: $('#dossier').val()
        },
        url: Routing.generate('banque_pm_pcc_tier'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var comptes = $.parseJSON(data),
                i;
            tiers = comptes.t;
            pccs = comptes.p;

            var tiers_options = '<option value="0-0"></option>',
                bilan_options = '<option value="0-0"></option>',
                charge_options = '<option value="0-0"></option>',
                tva_options = '<option value="0-0"></option>';

            for (i = 0; i < tiers.length; i++)
            {
                var tier = tiers[i];
                tiers_options += '<option value="1-'+tier.id+'">'+(tier.c + ' - ' + tier.i)+'</option>';
            }

            for (i = 0; i < pccs.length; i++)
            {
                var pcc = pccs[i],
                    compte = pcc.c;
                if (compte.length >= 3 && compte.substr(0,3) === '445')
                    tva_options += '<option value="0-'+pcc.id+'">'+(pcc.c + ' - ' + pcc.i)+'</option>';
                if (compte.length >= 1 && parseInt(compte.substr(0,1)) >= 6)
                    charge_options += '<option value="0-'+pcc.id+'">'+(pcc.c + ' - ' + pcc.i)+'</option>';
                if (compte.length >= 1 && parseInt(compte.substr(0,1)) <= 6 && compte.substr(0,3) !== '445')
                    bilan_options += '<option value="0-'+pcc.id+'">'+(pcc.c + ' - ' + pcc.i)+'</option>';
            }

            $('#id_options_hidden').find('.cl_option_0').html(bilan_options);
            $('#id_options_hidden').find('.cl_option_1').html(tiers_options);
            $('#id_options_hidden').find('.cl_option_2').html(charge_options);
            $('#id_options_hidden').find('.cl_option_3').html(tva_options);
            charger_banque_compte();
        }
    });
}

function after_charged_dossier_not_select() {
    if(isPieceManquante == 0){
        if ($('#dossier option:selected').text().trim() === '')
            $('#dossier option:selected').text('Tous');
        $('#js_id_conteneur_banque').addClass('hidden');
        $('#js_id_conteneur_compte').addClass('hidden');
        go();
    }
}