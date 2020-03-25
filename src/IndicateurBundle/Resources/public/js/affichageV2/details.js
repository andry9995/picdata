/**
 * Created by SITRAKA on 03/04/2017.
 */
var index_table = 0;

$(document).on('click','.js_cl_valeur_box',function(){
    var container = $(this),
        category = $(this).find('.js_cl_valeur_key').text().trim();
    function_click_in_chart(container,category,'');
});

$(document).on('click','table.ui-jqgrid-btable td',function(){
    var montant = parseFloat($(this).text().trim().replace(/%/g, '').replace(/€/g, '').replace(/ /g, '').replace(/,/g, '.'));
    if (isNaN(parseFloat(montant))) return;

    function_click_in_chart($(this),'','',1);
});

$(document).on('change','.js_hide_col_to_hidden',function(){
    change_column_to_hidde($(this));
});

function resize_modal_ui()
{
    var tableau_grid = $('#table_' + index_table),
        tableau_grid_height = tableau_grid.closest('.modal-body').height() - 50;
    setTimeout(function() {
        setGridH(tableau_grid, tableau_grid_height);
        tableau_grid.jqGrid("setGridWidth", tableau_grid.closest(".modal-body").width() - 10);
    }, 600);
}

function change_column_to_hidde(input)
{
    var ui_modal = input.closest('.ui-dialog'),
        names_to_hides = [];
    ui_modal.find('.ui-jqgrid-btable').find('td.to_hidden').each(function(){
        var describe_spliter = $(this).attr('aria-describedby').trim().split('_'),
            name = '';

        $.each(describe_spliter, function( k, val ) {
            if(k > 1) name += val + '_';
        });
        name = name.substr(0,name.length - 1);

        if(!names_to_hides.in_array(name)) names_to_hides.push(name);
    });

    var showHide = (input.is(':checked')) ? 'showCol' : 'hideCol',
        table_id = ui_modal.find('.ui-jqgrid-btable').attr('id');

    jQuery("#"+table_id).jqGrid(showHide,names_to_hides);
}

function function_click_in_chart(container,category,nm,is_td)
{
    is_td = typeof is_td !== 'undefined' ? is_td : 0;
    var is_etat = parseInt($('#js_is_etat').val()),
        div_current = (is_etat == 1) ? container.closest('div.js_cl_tab_etat') : container.closest('div.js_indicateur_item'),
        indicateur = (is_etat == 1) ? div_current.attr('data-idd') : div_current.attr('data-id'),
        dossier = $('#dossier').val().trim(),
        exercices = [],
        periodes = [],
        moiss = [],
        analyse = (is_etat == 1) ? 0 : parseInt(div_current.find('.js_ul_analyse').find('.active').attr('data-type')),
        row = 0,
        col = 0;

    if(is_td == 1)
    {
        row = container.closest('tr').index() - 1;
        col = container.index();

        var i = 0;
        div_current.find('.ui-jqgrid-htable').find('tr').find('th').each(function(){
            if(col == i) category = $(this).text().trim();
            i++;
        });
    }

    if(is_etat == 0)
    {
        //exercices
        div_current.find('.js_date_picker_hidden .js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker())) exercices.push($(this).text().trim());
        });
        //test mois
        div_current.find('.js_date_picker_hidden .js_dpk_periode').each(function(){
            if($(this).hasClass('js_dpk_mois'))
            {
                var m = $(this).attr('data-value').trim();
                moiss.push(((m.length == 1) ? '0' : '') + m);
            }

            if($(this).hasClass(dpkGetActiveDatePicker()))
            {
                var array_mois = new Array(),
                    value = parseInt($(this).attr('data-val'));
                var niveau = parseInt($(this).attr('data-niveau'));
                //mois
                if(niveau == 3)
                {
                    var mois_val = $(this).attr('data-value').trim();
                    //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':[((mois_val.length == 1) ? '0' : '') + mois_val]});
                }
                //trimestre; semestre; annee
                else if(niveau == 2 || niveau == 1 || niveau == 0)
                {
                    //each moiss
                    div_current.find('.js_dpk_mois').each(function(){
                        var mere = -2;
                        if(niveau == 2) mere = parseInt($(this).attr('data-mere-trimestre'));
                        else if(niveau == 1) mere = parseInt($(this).attr('data-mere-semestre'));
                        else if(niveau == 0) mere = parseInt($(this).attr('data-mere-annee'));
                        if(!$(this).hasClass(dpkGetActiveDatePicker()) && mere == value)
                        {
                            var mois_val = $(this).attr('data-value').trim();
                            //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                            array_mois.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                        }
                    });
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':array_mois});
                }
            }
        });
    }
    else
    {
        //exercices
        $('.js_date_picker_hidden').find('.js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker())) exercices.push($(this).text().trim());
        });

        //test mois
        $('.js_date_picker_hidden').find('.js_dpk_periode').each(function(){
            if($(this).hasClass('js_dpk_mois'))
            {
                var m = $(this).attr('data-value').trim();
                moiss.push(((m.length == 1) ? '0' : '') + m);
            }

            if($(this).hasClass(dpkGetActiveDatePicker()))
            {
                var array_mois = new Array(),
                    value = parseInt($(this).attr('data-val'));
                var niveau = parseInt($(this).attr('data-niveau'));
                //mois
                if(niveau == 3)
                {
                    var mois_val = $(this).attr('data-value').trim();
                    //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':[((mois_val.length == 1) ? '0' : '') + mois_val]});
                }
                //trimestre; semestre; annee
                else if(niveau == 2 || niveau == 1 || niveau == 0)
                {
                    //each moiss
                    $('.js_date_picker_hidden').find('.js_dpk_mois').each(function(){
                        var mere = -2;
                        if(niveau == 2) mere = parseInt($(this).attr('data-mere-trimestre'));
                        else if(niveau == 1) mere = parseInt($(this).attr('data-mere-semestre'));
                        else if(niveau == 0) mere = parseInt($(this).attr('data-mere-annee'));
                        if(!$(this).hasClass(dpkGetActiveDatePicker()) && mere == value)
                        {
                            var mois_val = $(this).attr('data-value').trim();
                            //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                            array_mois.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                        }
                    });
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':array_mois});
                }
            }
        });
    }

    index_table++;
    $.ajax({
        data: {
            dossier:dossier,
            indicateur:indicateur,
            category:category,
            nm:nm,
            exercices: JSON.stringify(exercices),
            moiss:JSON.stringify(moiss),
            periodes:JSON.stringify(periodes),
            analyse: analyse,
            is_td:is_td,
            row:row,
            col:col,
            is_etat:is_etat
        },
        url: Routing.generate('ind_details'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var result = $.parseJSON(data);

            if(result.formule != '' && is_td == 1)
            {
                show_info('CETTE CELLULE RESULTE D UNE CALCUL','POUR DETAILS VOIR CELLULES PRECEDENTES','warning');
                return;
            }

            var titre =
                '<div class="row">' +
                    '<div class="col-sm-8">'+result.titre+'</div>' +
                    '<div class="col-sm-4">'+
                        '<div class="checkbox checkbox-info no-margin">' +
                            '<input id="js_hide_col_to_hidden_'+index_table+'" class="js_hide_col_to_hidden" type="checkbox">' +
                            '<label for="js_hide_col_to_hidden_'+index_table+'">Details</label>' +
                        '</div>'+
                    '</div>' +
                '</div>',

                datas = result.datas,
                userData = result.userData,
                new_table = '<table id="table_'+index_table+'"></table>',
                round = parseInt(result.arrondir),
                options = { modal: false,
                            resizable: true,
                            title: titre };

            modal_ui(options,new_table,true,undefined,0.4);

            var table = $('#table_'+index_table),
                w = table.parent().width() * 0.99,
                h = table.parent().height() * 0.75,
                editurl = 'index.php',
                grouping = {
                    groupField : ['rubrique'],
                    groupSummary : [true],
                    groupColumnShow : [false],
                    groupText : ['<b>{0}</b>'],
                    groupCollapse : false
                };
            if(is_td == 1)
            {
                if(result.formule.trim() == '')
                {
                    var exos = result.exercices;
                    set_table_jqgrid(datas,h,eb_get_col_moldel_gl(undefined,undefined,exos),eb_get_col_moldel_gl(w,round,exos),table,'hidden',w,editurl,false,undefined,true,grouping,'asc','p',false,userData);
                }
            }
            else
            {
                set_table_jqgrid(datas,h,eb_get_col_moldel_gl(),eb_get_col_moldel_gl(w,round),table,'hidden',w,editurl,false,undefined,true,grouping,'asc','p',false,userData);
            }

            $('#js_hide_col_to_hidden_'+index_table).change();
        }
    });
}

function eb_get_col_moldel_gl(w,round,exos)
{
    var colModel1 = [];
    round = typeof round !== 'undefined' ? round : 0;
    if(typeof exos === 'undefined')
    {
        if(typeof w !== 'undefined')
        {
            colModel1.push({ name:'p', index:'p', hidden: true,sorttype:'integer' });
            colModel1.push({ name:'compte', index:'compte', width:  70, sortable: false });
            colModel1.push({ name:'intitule', index:'intitule', width:  300, sortable: false });
            colModel1.push({ name:'debit', index:'debit', width:  90, classes:'to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            colModel1.push({ name:'credit', index:'credit', width:  90, classes:'to_hidden text-danger', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            colModel1.push({ name:'soldeDebit', index:'soldeDebit', width:  90, classes:'to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            colModel1.push({ name:'soldeCredit', index:'soldeCredit', width:  90, classes:'to_hidden text-danger', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            colModel1.push({ name:'car', index:'car', width:  30, align:'center', sortable: false });
            colModel1.push({ name:'soldeCalcule', index:'soldeCalcule', width:  90, classes:'text-primary', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            colModel1.push({ name:'rubrique', index:'rubrique', hidden:true });
        }
        else colModel1 = [
            'N',
            'Compte',
            'Intitule',
            'Debit',
            'Credit',
            'S. Debit',
            'S. Credit',
            '',
            'Solde',
            'Rubrique'];
    }
    else
    {
        if(typeof w !== 'undefined')
        {
            colModel1.push({ name:'p', index:'p', hidden: true,sorttype:'integer' });
            colModel1.push({ name:'compte', index:'compte', width:  70, sortable: false });
            colModel1.push({ name:'intitule', index:'intitule', width:  300, sortable: false });

            $.each(exos, function( k, val ) {
                colModel1.push({ name:'debit_'+val, index:'debit_'+val, width:  90, classes:'to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
                colModel1.push({ name:'credit_'+val, index:'credit_'+val, width:  90, classes:'text-danger to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
                colModel1.push({ name:'soldeDebit_'+val, index:'soldeDebit_'+val, width:  90, classes:'to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
                colModel1.push({ name:'soldeCredit_'+val, index:'soldeCredit_'+val, width:  90, classes:'text-danger to_hidden', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
                colModel1.push({ name:'car_'+val, index:'car_'+val, width:  20, align:'center', sortable: false });
                colModel1.push({ name:'soldeCalcule_'+val, index:'soldeCalcule_'+val, width:  90, classes:'text-primary', align:'right', sortable: false, formatter: function (v) { return number_format(v,round,',','\x20');},summaryType:'sum' });
            });

            colModel1.push({ name:'rubrique', index:'rubrique', hidden:true });
        }
        else
        {
            colModel1 = [
                'N',
                'Compte',
                'Intitule'];
            $.each(exos, function( k, val )
            {
                colModel1.push('Debit '+val);
                colModel1.push('Credit '+val);
                colModel1.push('S. Debit '+val);
                colModel1.push('S. Credit '+val);
                colModel1.push('');
                colModel1.push('Solde '+ val);
            });
            colModel1.push('Rubrique');
        }
    }

    return colModel1;
}