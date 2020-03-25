/**
 * Created by SITRAKA on 03/07/2017.
 */
$(document).ready(function(){
    periodeDependant = true;
    charger_site();

    /*new Switchery(document.querySelector('#id_lettre'), { color: '#1AB394' });
    new Switchery(document.querySelector('#id_expand'), { color: '#1AB394' });
    new Switchery(document.querySelector('#id_group_lettre'), { color: '#1AB394' });
    new Switchery(document.querySelector('#id_an_detailler'), { color: '#1AB394' });*/
    new Switchery(document.querySelector('#id_simple'), { color: '#1AB394' });

    $(document).on('click','#js_container_tabs .nav-tabs li',function(){
        charger_fourchette_compte();
        set_one_exercice();
        //old_etat = parseInt($(this).attr('data-etat'));
    });

    $(document).on('change','#client',function(){
        $('#js_container_tabs .tab-content div.active div.js_cl_container_etat').empty();
    });

    $(document).on('change','#site',function(){
        $('#js_container_tabs .tab-content div.active div.js_cl_container_etat').empty();
    });

    $(document).on('click','#js_container_tabs input.js_option',function(){
        charger_fourchette_compte();
        set_one_exercice();
    });

    $(document).on('change','#id_lettre',function(){
        go();
    });

    $(document).on('change','#js_journal',function(){
        go();
    });

    $(document).on('click','.pi',function(){
        var image_id = $(this).parent().find('.ip').text().trim();
            //lien = Routing.generate('app_image_picdata'),
            //nom_image = $(this).text().trim();
        if(image_id === '') show_info('ERREUR','CET PIECE N EST PAS ACCESSIBLE','error');
        else show_image_pop_up(image_id);
    });

    $(document).on('change','#id_expand',function(){
        gl_expand_collapse();
    });

    $(document).on('change','.cl_compte',function(){
        go();
    });

    $(document).on('change','#id_group_lettre',function(){
        go();
    });

    $(document).on('change','#id_an_detailler',function(){
        go();
    });

    $(document).on('change','#id_simple',function(){
        go();
    });
});

function set_one_exercice()
{
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val());
    if(etat < 3 && option === 0)
    {
        oneExercice = false;
    }
    else
    {
        oneExercice = true;
        var select = false;
        $('.js_date_picker_hidden').find('.js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker()))
            {
                if(!select) select = true;
                else
                {
                    show_info('NOTICE','UN SEUL EXERCICE EST SELECTIONNE','warning');
                    $(this).removeClass(dpkGetActiveDatePicker());
                }
            }
        });
        $('#js_conteneur_periode .js_periode').attr('data-content',$('.js_date_picker_hidden').html());
    }

    //parametrage anciennete
    if((etat === 1 || etat === 2) && option === 1) $('#js_container_tabs .tab-content div.active .js_show_param_anciennete').removeClass('hidden');
    else $('#js_container_tabs .tab-content div.active .js_show_param_anciennete').addClass('hidden');
    go();
}

function go()
{
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val()),
        exercices = [],
        moiss = [],
        periodes = [],
        dossier = $('#dossier').val(),
        etat_container = $('#js_container_tabs .tab-content div.active div.js_cl_container_etat'),
        div_hidden = $('.js_date_picker_hidden'),
        date_anciennete = '2017-12-30 21:00:00',
        anciennetes = '[]',
        journal = $('#js_zero_boost').val(),
        avec_lettre = 1,
        compte_de = { id:$('#id_compte_de').val(), t:parseInt($('#id_compte_de option:selected').attr('data-type')) },
        compte_a = { id:$('#id_compte_a').val(), t:parseInt($('#id_compte_a option:selected').attr('data-type')) },
        regroupe_lettre = 0,
        an_det = 0,
        col_solde = $('#id_simple').is(':checked') ? 1 : 0 ;

    if ($('#id_lettre').length > 0) avec_lettre = $('#id_lettre').is(':checked') ? 1 : 0;
    if ($('#id_group_lettre').length > 0) regroupe_lettre = $('#id_group_lettre').is(':checked') ? 1 : 0;
    if ($('#id_an_detailler').length > 0) an_det = $('#id_an_detailler').is(':checked') ? 1 : 0;

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
    etat_container.empty();
    /**
     * journal
     */
    if(etat === 4) journal = $('#js_journal').val();


    if($('#dossier option:selected').text().trim() === '')
    {
        show_info('NOTICE','CHOISIR LE DOSSIER','error');
        return;
    }

    /**
     * anciennetes
     */
    if((etat === 1 || etat === 2) && option === 1)
    {
        var container_params = $('#js_container_tabs .tab-content div.active .js_container_params');
        date_anciennete = container_params.find('.js_cl_date_anciennete').val();
        anciennetes = container_params.find('.js_cl_anciennetes').val();
    }

    $.ajax({
        data: {
            dossier: dossier,
            exercices: JSON.stringify(exercices),
            mois: JSON.stringify(moiss),
            periodes: JSON.stringify(periodes),
            etat: etat,
            option: option,
            date_anciennete: date_anciennete,
            anciennetes: anciennetes,
            journal: journal,
            avec_lettre: avec_lettre,
            compte_de: JSON.stringify(compte_de),
            compte_a: JSON.stringify(compte_a),
            regroupe_lettre: regroupe_lettre,
            an_det: an_det,
            col_solde: col_solde
        },
        url: Routing.generate('etat_b_etat'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var h = $(window).height() - 250,
                w = 0,
                editurl = 'index.php';

            if (etat === 0 ||
                etat === 1 && option === 0 ||
                etat === 2 && option === 0 ||
                etat === 4 ||
                etat === 5)
            {
                $('#js_eb_table_to_grid').remove();
                etat_container.html(data);
                w = $("#js_eb_table_to_grid").parent().width();
                tableToGrid("#js_eb_table_to_grid", {colModel:bl_col_mod(exercices,w), height:h, width:w});
                group_head_jqgrid('js_eb_table_to_grid',getGroupHeaders(exercices),true);
            }
            /*etat_container.html(data);
            return;
            if (true)
            {

            }*/
            else if(etat === 3)
            {
                $('#js_eb_table_to_grid').remove();
                etat_container.html('<table id="js_eb_table_to_grid"></table>');
                w = $("#js_eb_table_to_grid").parent().width();
                var table = $('#js_eb_table_to_grid');
                set_table_jqgrid($.parseJSON(data),h,gl_col_mod(),gl_col_mod(w),table,'hidden',w,editurl,false,undefined,true,{groupField : ['cp'],groupColumnShow : [false]},'asc','p');
                set_tables_responsive();
                gl_expand_collapse();
            }
            else
            {
                etat_container.html(data).addClass('scroller').height(h);
                reinitialiser_inspinia();
            }
            eb_set_class_table(exercices);
        }
    });
    set_status_exerices(dossier,exercices,$('#js_chart_footer'));
}

function gl_expand_collapse()
{
    var expandAll = $('#id_expand').is(':checked');
    var $grid = $("#js_eb_table_to_grid");
    var idPrefix =$grid[0].id + "ghead_0_", trspans;
    var groups =$grid[0].p.groupingView.groups;
    if ($grid[0].p.grouping) {
        for (var index = 0; index < groups.length; index++) {
            if (expandAll) {
                trspans = $("#" + idPrefix + index + " span.tree-wrap-" +$grid[0].p.direction + "." +$grid[0].p.groupingView.plusicon);
            } else {
                trspans = $("#" + idPrefix + index + " span.tree-wrap-" +$grid[0].p.direction + "." +$grid[0].p.groupingView.minusicon);
            }
            if (trspans.length > 0) {
                $grid.jqGrid('groupingToggle', idPrefix + index);
            }
        }
    }
}

/**
 * Grand livre
 */
function gl_col_mod(w)
{
    var colSolde = $('#id_simple').is(':checked');
    var colM = [];
    if(typeof w !== 'undefined')
    {
        colM.push({ name:'p', index:'p', width:  w * 10 / 100, hidden: true,sorttype:'integer' });
        colM.push({ name:'de', index:'de', width:  w * 10 / 100, sortable: false });
        colM.push({ name:'j', index:'j', width:  w * 3 / 100, sortable: false });
        colM.push({ name:'pi', index:'pi', width:  w * 10 / 100, classes: 'pi', sortable: false});
        colM.push({ name:'ip', index:'ip', hidden:true, width:  0, classes:'ip' });
        colM.push({ name:'l', index:'l', width:  w * 35 / 100, sortable: false });

        if (!colSolde)
        {
            colM.push({ name:'d', index:'d', width:  w * 10 / 100, align:'right', sortable: false, formatter: function (v) { return number_format(v,2,',','\x20',true);} });
            colM.push({ name:'c', index:'c', width:  w * 10 / 100, align:'right', sortable: false, classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20',true);} });
        }

        colM.push({ name:'lt', index:'lt', width:  w * 2 / 100, sortable: false });

        if (!colSolde)
        {
            colM.push({ name:'sd', index:'sd', width:  w * 10 / 100, sortable: false, align:'right',formatter: function (v) { return number_format(v,2,',','\x20',true);} });
            colM.push({ name:'sc', index:'sc', width:  w * 10 / 100, sortable: false, align:'right', classes:'text-danger', formatter: function (v) { return number_format(v,2,',','\x20',true);} });
        }
        else
        {
            colM.push({ name:'s', index:'s', width:  w * 10 / 100, sortable: false, classes:'cl_solde', align:'right',formatter: function (v) { return number_format(v,2,',','\x20',true);} });
        }
        colM.push({ name:'cp', index:'cp', hidden:true, width:  0 });
    }
    else
    {
        colM = [
            'N',
            'Date',
            'Jnl',
            'Piece',
            '',
            'Libelle'];

        if (!colSolde)
        {
            colM.push('Debit');
            colM.push('Credit');
        }

        colM.push('L');
        if (!colSolde)
        {
            colM.push('solde Debit');
            colM.push('solde Credit');
        }
        else colM.push('solde');
        colM.push('Compte');
    }
    return colM;
}

/**
 * Balance
 */
function bl_col_mod(exercices,w)
{
    var colM = [];

    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val()),
        i,
        col_solde = $('#id_simple').is(':checked');

    if(etat === 0 ||
        etat === 1 && option === 0 ||
        etat === 2 && option === 0)
    {
        colM.push({name:"js_eb_est_tiers",align:"left",width:0,hidden:true});
        colM.push({name:"js_eb_id_compte",align:"center",width:0,hidden:true});
        colM.push({name:"js_eb_compte",align:"center",width:w*15/100});
        colM.push({name:"js_eb_intitule",align:"left",width:w*55/100});
        if(exercices.length === 1 && !col_solde)
        {
            colM.push({name: "js_eb_debit", align: "right", width: w * 15 / 100});
            colM.push({name: "js_eb_credit", align: "right", width: w * 15 / 100});
        }
        for(i = 0;i < exercices.length;i++)
        {
            if (col_solde)
            {
                colM.push({name: "js_eb_solde_" + exercices[i], align: "right", classes:"cl_solde",  width: w * 30 / 100});
            }
            else
            {
                colM.push({name: "js_eb_solde_debit_" + exercices[i], align: "right", width: w * 15 / 100});
                colM.push({name: "js_eb_solde_credit_" + exercices[i], align: "right", width: w * 15 / 100});
            }
        }
    }
    if(etat === 4)
    {
        colM.push({name:"js_eb_est_tiers",align:"center",width:0,hidden:true});
        colM.push({name:"js_eb_id_compte",align:"center",width:0,hidden:true});

        colM.push({name:"js_eb_date",align:"center",width:w*10/100});
        colM.push({name:"js_eb_journal",align:"center",width:w*6/100});
        colM.push({name:"js_eb_compte",align:"left",width:w*10/100});
        colM.push({name:"js_eb_piece",align:"left",width:w*11/100});
        colM.push({name:"js_eb_libelle",align:"left",width:w*45/100});

        if (col_solde)
            colM.push({name:"js_eb_solde",align:"right", classes:'cl_solde', width:w*9/100});
        else
        {
            colM.push({name:"js_eb_debit",align:"right",width:w*9/100});
            colM.push({name:"js_eb_credit",align:"right",width:w*9/100});
        }
    }
    if(etat === 5)
    {
        colM.push({name:"js_eb_date",align:"center",width:w*15/100});
        colM.push({name:"js_eb_journal",align:"center",width:w*10/100});
        colM.push({name:"js_eb_libelle",align:"left",width:w*35/100});
        colM.push({name:"js_eb_debit",align:"right",width:w*20/100});
        colM.push({name:"js_eb_credit",align:"right",width:w*20/100});
    }
    return colM;
}

function getGroupHeaders(exercices)
{
    var colM = [];
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val()),
        i;
    if(etat == 0 ||
        etat == 1 && option == 0 ||
        etat == 2 && option == 0)
    {
        if(exercices.length == 1) colM.push({startColumnName: 'js_eb_debit', numberOfColumns: 4, titleText: '<strong>'+exercices[0]+'</strong>'});
        else
            for(i = 0;i < exercices.length;i++)
                colM.push({startColumnName: 'js_eb_solde_debit_'+exercices[i], numberOfColumns: 2, titleText: '<strong>'+exercices[i]+'</strong>'});
    }
    /*if(etat == 4)
        colM.push({startColumnName: 'js_eb_est_tiers', numberOfColumns: 9, titleText: '<strong>'+exercices[0]+'</strong>'});*/
    /*if(etat == 5)
        colM.push({startColumnName: 'js_eb_date', numberOfColumns: 5, titleText: '<strong>'+exercices[0]+'</strong>'});*/
    return colM;
}

function eb_set_class_table(exercices)
{
    var i;
    //compte cliquable
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_compte"]')
        .addClass(eb_get_class_compte()+' js_show_detail_compte');
    $('.js_show_detail_compte').each(function(){
        $(this)
            .attr('data-id',$(this).closest('tr').find('td[aria-describedby="js_eb_table_to_grid_js_eb_id_compte"]').text())
            .attr('data-type',$(this).closest('tr').find('td[aria-describedby="js_eb_table_to_grid_js_eb_est_tiers"]').text());
    });

    $('#jqgh_js_eb_table_to_grid_js_eb_compte').addClass(eb_get_class_compte());
    //piece
    $('#js_eb_table_to_grid tr td span.js_show_image').each(function(){
        $(this).parent().attr('data-id_image',$(this).attr('data-id_image'))
            .addClass(eb_get_class_piece() + ' pointer js_show_image')
            .html($(this).html().trim());
    });
    $('#jqgh_js_eb_table_to_grid_js_eb_piece').addClass(eb_get_class_piece());
    //solde credit
    for(i = 0;i<exercices.length;i++)
    {
        $('#jqgh_js_eb_table_to_grid_js_eb_solde_credit_' + exercices[i]).addClass(eb_get_class_credit());
        $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_solde_credit_'+ exercices[i] +'"]').addClass(eb_get_class_credit());
    }
    //credit
    $('#jqgh_js_eb_table_to_grid_js_eb_credit').addClass(eb_get_class_credit());
    $('#js_eb_table_to_grid td[aria-describedby="js_eb_table_to_grid_js_eb_credit"]').addClass(eb_get_class_credit());

    $('.cl_solde').each(function(){
        if (parseInt($(this).text()) < 0) $(this).addClass('text-danger');
    });
}

function eb_get_class_solde()
{
    return 'fa-check';
}

function eb_get_class_compte()
{
    return 'text-success pointer';
}

function eb_get_class_credit()
{
    return 'text-danger';
}

function eb_get_class_piece()
{
    return 'text-info pointer';
}