/**
 * Created by SITRAKA on 14/04/2017.
 */
var index_table_etat = 0;
$(document).ready(function () {
    charger_site();
    oneExercice = true;

    $(window).resize(function() {
        $('.js_cl_container_etat').height(window.innerHeight - 250);
        $('.cl_container').height($('.js_cl_container_etat').height() - 10);
        var table = $('#table_etat_'+index_table_etat);
        updateTableGridSize(table,table.closest('.cl_container'));
        update_height_comment();
    });

    $(document).on('click','.js_li_etat',function(){
        var tab_active = null;
        $('#js_id_container_etat').find('.js_cl_tab_etat').each(function(){
            if($(this).hasClass('active')) tab_active = $(this);
        });

        if(tab_active !== null)
        {
            var periode = tab_active.find('.js_per_hidden').val(),
                periodeSpliter = periode.split(''),
                anneeActivate = parseInt(periodeSpliter[0]) === 1,
                semestreActivate = parseInt(periodeSpliter[1]) === 1,
                trimetreActivate = parseInt(periodeSpliter[2]) === 1,
                moisActivate = parseInt(periodeSpliter[3]) === 1,
                a_activer = 0;

            if(anneeActivate) a_activer = 0;
            else if(semestreActivate) a_activer = 1;
            else if(trimetreActivate) a_activer = 2;
            else a_activer = 3;

            $('.js_date_picker_hidden .table-dpk .js_dpk_periode').each(function(){
                var niveau = parseInt($(this).attr('data-niveau'));

                if(niveau === a_activer) $(this).addClass(dpkGetActiveDatePicker());
                else $(this).removeClass(dpkGetActiveDatePicker());

                if(niveau === 0)
                {
                    if(!anneeActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau === 1)
                {
                    if(!semestreActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau === 2)
                {
                    if(!trimetreActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau === 3)
                {
                    if(!moisActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }
            });

            $('#js_conteneur_periode .js_periode').attr('data-content',$('.js_date_picker_hidden').html());
            go();
        }
    });
});

function update_height_comment()
{
    $('.js_cl_container_etat').height($(window).height() - 250);
    $('.cl_container_comment').height($('.js_cl_container_etat').height() - 75);
}

function go()
{
    if($('#dossier option:selected').text().trim() === '')
    {
        show_info('NOTICE','CHOISIR LE DOSSIER','warning');
        return;
    }

    index_table_etat++;
    var tab_active = null,
        is_etat_gestion = (parseInt($('#js_et').val()) === 1),
        new_table = '' +
            '<div class="row">' +
                '<div style="width: '+(is_etat_gestion ? 75 : 100 )+'%;padding-right: 5px; border-right: 2px dotted #CCCCCC" id="id_container_'+index_table_etat+'" class="pull-left cl_table_resize cl_container col-llg-'+(is_etat_gestion ? '9' : '12')+'"><table id="table_etat_'+index_table_etat+'"></table></div>';

    if (is_etat_gestion)
        new_table +=
            '<div class="pull-right cl_comment_resize" id="id_commet_n'+index_table_etat+'" style="width: 25%;padding-left: 5px"></div>';

    new_table += '</div>';

    $('#js_id_container_etat').find('.js_cl_tab_etat').each(function(){
        if($(this).hasClass('active')) tab_active = $(this);
    });

    tab_active.find('.js_cl_container_etat').html(new_table);

    if (is_etat_gestion)
        $('#id_container_'+index_table_etat).resizable({
            stop: resize_table
        });

    //exercices,mois,periodes
    var code_graphe = 'TAB',
        exercices = [],
        periodes = [],
        moiss = [],
        div_hidden = $('.js_date_picker_hidden');
    div_hidden.find('.js_dpk_exercice').each(function(){
        if($(this).hasClass(dpkGetActiveDatePicker()))
        {
            exercices.push($(this).text().trim());
        }
    });
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

    var lien = Routing.generate('ind_affiche'),
        dossier = $('#dossier').val();
    $.ajax({
        data: {
            dossier:dossier,
            indicateur: tab_active.attr('data-id'),
            code_graphe: 'TAB',
            analyse: 0,
            exercices:JSON.stringify(exercices),
            moiss:JSON.stringify(moiss),
            periodes:JSON.stringify(periodes),
            date_anciennete: null,
            anciennetes: JSON.stringify([]),
            is_etat:1
        },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            if(code_graphe === 'TAB')
            {
                var dataObject = $.parseJSON(data),
                    errors = dataObject.error,
                    entetes = dataObject.entetes,
                    models = dataObject.models,
                    datas = dataObject.datas,
                    styles = dataObject.styles,
                    typeU = parseInt(dataObject.typeU),
                    colsErros = dataObject.colsErrors,
                    dataObjects = [];

                tab_active.attr('data-idd',dataObject.id);

                $.each( datas, function( i, l ){
                    var item = new Object();
                    $.each( l , function ( j, v ){
                        var key = 'col_' + j;
                        item[key] = v;
                    });
                    dataObjects.push(item);
                });

                var editurl = 'test.php';

                var table_selected = $('#table_etat_'+index_table_etat),
                    w = table_selected.parent().width(),
                    h = $(window).height() - 245;

                if(errors.length > 0)
                {
                    var div_error = '<div>';

                    if(typeU <= 2)
                    {
                        $.each( errors, function( i, l ){
                            div_error += '<span class="label label-danger">'+l+'</span>';
                        });
                    }
                    else
                    {
                        div_error +=
                            '<div class="alert alert-danger">'+
                            'COLONNES&nbsp;GRISEES:&nbsp;<strong>COMPTES&nbsp;NON&nbsp;EQUILIBRES</strong>'+
                            '</div>';
                    }
                    div_error += '</div>';
                    $(div_error).insertBefore(table_selected);

                    //if(typeU > 2) return;
                }

                set_table_jqgrid(dataObjects,h,table_get_col_model(entetes),table_get_col_model(models,w),table_selected,'hidden',w + 20,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,true);
                var index_row = 0;

                /**
                 * thead
                 */
                var index_col = 0;
                $('#table_etat_'+index_table_etat).closest('.ui-jqgrid').find('.ui-jqgrid-hbox')
                    .find('.ui-jqgrid-sortable').each(function(){
                    var style = styles[0 + '-' + index_col];
                    if(style !== null)
                    {
                        $(this).closest('th')
                            .css('font-family',style.font)
                            .css('font-weight',style.weight)
                            .css('font-style',style.style)
                            .css('font-size',style.size)
                            .css('color',style.color)
                            .css('background-color',style.bg)
                            .css('text-align',style.align)
                            .css('border-top',style.bt)
                            .css('border-left',style.bl)
                            .css('border-right',style.br)
                            .css('border-bottom',style.bb);
                            //.find('.mywrapping').css('size',style.size+'!important');
                    }
                    index_col++;
                });

                //TBODY
                $('#table_etat_'+index_table_etat).find('tr').each(function(){
                    if(!$(this).hasClass('jqgfirstrow'))
                    {
                        index_col = 0;
                        $(this).find('td').each(function(){
                            var style = styles[(index_row + 1) + '-' + index_col];
                            if(style !== null)
                            {
                                $(this)
                                    .css('font-family',style.font)
                                    .css('font-weight',style.weight)
                                    .css('font-style',style.style)
                                    .css('font-size',style.size)
                                    .css('color',style.color)
                                    .css('background-color',style.bg)
                                    .css('text-align',style.align)
                                    .css('border-top',style.bt)
                                    .css('border-left',style.bl)
                                    .css('border-right',style.br)
                                    .css('border-bottom',style.bb);
                            }
                            //test control
                            if($(this).text().trim().startsWith('KOKO-'))
                            {
                                $(this)
                                    .html($(this).text().replace(/KOKO-/g,'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;'))
                                    .addClass('td-control-error');
                            }
                            else if($(this).text().trim() == 'OK')
                            {
                                $(this).html((typeU <= 2) ? '<i class="fa fa-check-circle" aria-hidden="true"></i>' : '');
                                if(typeU <= 2) $(this).addClass('td-control-ok');
                            }

                            if(typeU > 2 && colsErros.in_array(index_col))
                            {
                                $(this).html('...').addClass('td-column-disabled');
                            }
                            index_col++;
                        });
                        index_row++;
                    }
                });
            }

            update_height_comment();
            //set_footer(div_current,exercices);
        },
        error: function () {
            verrou_fenetre(false);
        }
    });

    if (is_etat_gestion)
    {
        $.ajax({
            data: {
                dossier: dossier,
                indicateur: tab_active.attr('data-id')
            },
            url: Routing.generate('etat_commentaire'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                $('#id_commet_n'+index_table_etat).html(data);
                update_height_comment();
            }
        });
    }

    set_status_exerices(dossier,exercices,$('#js_chart_footer'));
}

function table_get_col_model(model,w)
{
    var colModel1 = [],i;
    if(typeof w !== 'undefined')
    {
        for(i = 0;i < model.length; i++)
        {
            var width = (i === 0) ? 255 : 75;
            colModel1.push({ name:model[i].name, index:model[i].name, sortable:false, width:  width, classes:model[i].class, align:model[i].align });
        }
    }
    else
    {
        for(i = 0;i < model.length; i++) colModel1.push(model[i]);
    }
    return colModel1;
}

function resize_table(e, ui)
{
    var width = Math.round(ui.size.width),
        height = Math.round(ui.size.height),
        container = $(e.target).closest('.panel-body'),
        w_container = container.width();
    container.find('.cl_comment_resize').width(w_container - width + 20);

    var table = $('#table_etat_'+index_table_etat);
    updateTableGridSize(table,table.closest('.cl_container'));
    update_height_comment();
}