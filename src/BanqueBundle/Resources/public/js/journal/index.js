/**
 * Created by SITRAKA on 04/06/2018.
 */
$(document).ready(function(){
    $('#exercice').val((new Date()).getFullYear());
    dossier_depend_exercice = true;
    charger_site();

    $(document).on('change','#dossier',function(){
        change_dossier();
    });

    $(document).on('change','#js_banque_compte',function(){
        charger_compte_comptable();
        go();
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    $(document).on('mouseenter ','#id_journal_banque tr',function(){
        $('.tr-over').removeClass('tr-over');
        var group = parseInt($(this).find('.js_group_journal').text());
        $('.js_g_'+group).each(function(){
            $(this).closest('tr').addClass('tr-over');
        });
    });

    $(document).on('change','#id_chk_centraliser',function(){
        go();
    });

    $(document).on('change','#id_chk_detailler_ob',function(){
        go();
    });
});

function after_load_dossier()
{
    change_dossier();
}

function change_dossier()
{
    charger_banque();
    charger_periode_pop_over();
}

function charger_banque()
{
    if($('#dossier option:selected').text().trim() === '')
    {
        //show_info('NOTICE','Choisir le Dossier','error');
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

        $('#id_container_pcc_bc').html(
            '<div class="form-horizontal">' +
                '<div class="form-group">' +
                    '<label class="control-label col-lg-2" for="id_pcc_banque_compte">' +
                        '<span>Compte</span> ' +
                        '<span class="label label-warning">0</span>' +
                    '</label>' +
                    '<div class="col-lg-10">' +
                        '<select class="form-control" id="id_pcc_banque_compte">' +
                            '<option value="'+$('#js_zero_boost').val()+'"></option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>'
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
            charger_banque_compte();
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
            charger_compte_comptable();
            go();
        }
    });
}

function vider_table()
{
    var new_table = '<table id="id_table_journaux"></table>';
    $('#id_journal_banque').html(new_table);
    set_table([]);
    set_control();
}

function go()
{
    var dossier_element = $('#dossier'),
        doss_texte = dossier_element.find('option:selected').text().trim().toUpperCase(),
        bc_element = $('#js_banque_compte'),
        compte_text = bc_element.find('option:selected').text().trim().toUpperCase(),
        error = false,
        el_filtre = $('#id_show_filtre_date'),
        filtre_type = parseInt(el_filtre.attr('data-type')),
        filtre_start = el_filtre.attr('data-start'),
        filtre_end = el_filtre.attr('data-end');

    var new_table = '<table id="id_table_journaux"></table>';
    $('#id_journal_banque').html(new_table);

    if (doss_texte === '' || doss_texte === 'TOUS')
    {
        dossier_element.closest('.form-group').addClass('has-error');
        error = true;
    }
    else dossier_element.closest('.form-group').removeClass('has-error');

    if (compte_text === '' || compte_text === 'TOUS')
    {
        bc_element.closest('.form-group').addClass('has-error');
        error = true;
    }
    else bc_element.closest('.form-group').removeClass('has-error');
    if (error)
    {
        vider_table();
        return;
    }

    //exercices,mois,periodes
    var periodes = [],
        moiss = [],
        div_hidden = $('.js_date_picker_hidden');

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

    $.ajax({
        data: {
            banque_compte: bc_element.val(),
            exercice: $('#exercice').val(),
            periode: JSON.stringify({ p:periodes, m:moiss }),
            centraliser: $('#id_chk_centraliser').is(':checked') ? 1 : 0,
            filtre_type: filtre_type,
            filtre_start: filtre_start,
            filtre_end: filtre_end,
            obs_detailler: $('#id_chk_detailler_ob').is(':checked') ? 1 : 0
        },
        type: 'POST',
        url: Routing.generate('jnl_bq_analyse'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#id_pcc_banque_compte').closest('.form-group').removeClass('has-error');
            $('#id_pcc_journal_dossier').closest('.form-group').removeClass('has-error');

            if (parseInt(data) < 0)
            {
                if (parseInt(data) === -3)
                {
                    $('#id_pcc_banque_compte').closest('.form-group').addClass('has-error');
                    $('#id_pcc_journal_dossier').closest('.form-group').addClass('has-error');
                    show_info('Compte Comptable','Associé d abord le Compte Banque à un compte 512xxx et à un CODE JOURNAL','error');
                }
                else if (parseInt(data) === -1)
                {
                    $('#id_pcc_banque_compte').closest('.form-group').addClass('has-error');
                    show_info('Compte Comptable','Associé d abord le Compte Banque à un compte 512xxx','error');
                }
                else
                {
                    $('#id_pcc_journal_dossier').closest('.form-group').addClass('has-error');
                    show_info('Compte Comptable','Associé d abord le Compte Banque à un CODE JOURNAL','error');
                }

                vider_table();
                return;
            }
            set_table($.parseJSON(data));
        }
    });
}

function set_table(datas)
{
    var table_selected = $('#id_table_journaux'),
        w = table_selected.parent().width(),
        h = $(window).height() - 300,
        editurl = 'index.php',
        i,
        tot_debit = 0,
        tot_credit = 0,
        ds = [],
        total_512 = 0,
        total_contre = 0;

    if (datas.datas !== undefined)
    {
        ds = datas.datas;
        for (i = 0; i < datas.datas.length; i++)
        {
            tot_debit += parseFloat(datas.datas[i].db);
            tot_credit += parseFloat(datas.datas[i].cr);

            if (parseInt(datas.datas[i].isb) === 1) total_512 += parseFloat(datas.datas[i].db) - parseFloat(datas.datas[i].cr);
            else total_contre += parseFloat(datas.datas[i].db) - parseFloat(datas.datas[i].cr);
        }
    }
    else ds = datas;

    datas_export = ds;

    jQuery('#id_table_journaux').jqGrid({
        data: ds,
        datatype: 'local',
        height: h,
        width: w,
        rowNum: 10000000,
        rowList: [10,20,30],
        colNames:col_model(),
        colModel:col_model(w),
        viewrecords: true,
        footerrow: true,
        userDataOnFooter: true,
        userData: { 'db': tot_debit, 'cr': tot_credit }
    });

    controle = {
        tot_debit: tot_debit,
        tot_credit: tot_credit,
        total_512: total_512,
        total_contre: total_contre
    };

    control();
}

function col_model(w)
{
    var colM = [];

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'d', index:'d', sortable:true, width: 10 * w/100, align:'center', sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'd/m/Y', newformat: 'd/m/Y'} });
        colM.push({ name:'jnl',index:'jnl', width: 5 * w/100, align:'center' });
        colM.push({ name:'c', index:'c', sortable:true, width: 10 * w/100, align:'center', formatter: function (v) { return compte_formatter(v) } });
        colM.push({ name:'i', index:'i', sortable:true, width: 10 * w/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colM.push({ name:'l', index:'l', sortable:true, width: 40 * w/100 });
        //colM.push({ name:'test', index:'test', sortable:true, width: 40 * w/100 });
        colM.push({ name:'db', index:'db', sortable:true, width: 10 * w/100, align:'right', sorttype: 'number', classes:'text-primary', formatter: function(v){ return '<strong>'+number_format(v, 2, ',', ' ',true)+'</strong>'} });
        colM.push({ name:'cr', index:'cr', sortable:true, width: 10 * w/100, align:'right', sorttype: 'number', classes:'text-danger', formatter: function(v){ return '<strong>'+number_format(v, 2, ',', ' ',true)+'</strong>'} });
        colM.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
        colM.push({ name:'g', index:'g', hidden:true, classes:'js_group_journal', formatter:function(v){ return '<span class="js_g_'+v+'">'+v+'</span>' } });
    }
    else
    {
        colM = [
            'Date',
            'Jnl',
            'Compte',
            'Image',
            'Libellé',
            //'test',
            'Débit',
            'Crédit',
            '',
            ''
        ];
    }
    return colM;
}