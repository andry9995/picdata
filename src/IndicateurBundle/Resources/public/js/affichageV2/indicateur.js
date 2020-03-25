/**
 * Created by SITRAKA on 21/11/2016.
 */

$(document).ready(function(){
    /**
     * periode
     */
    $(document).on('click','.js_periode',function(){
        $('.js_current_div').removeClass('js_current_div');
        $(this).parent().parent().parent().addClass('js_current_div');
    });

    /**
     * valider periode
     */
    $(document).on('click','.js_dpk_valider',function(){
        valider_exercice($(this));
    });

    /**
     * show anciennete edit
     */
    $(document).on('click','.js_anciennete',function (){
        $('.js_current_div').removeClass('js_current_div');
        $(this).parent().parent().parent().addClass('js_current_div');
    });

    /**
     * analyse
     */
    $(document).on('click','.js_analyse',function(){
        valider_analyse($(this));
    });

    /**
     * graphe
     */
    $(document).on('click','.js_graphe',function(){
        valider_graphe($(this));
    });


    /**
     * full screen
     */
    $(document).on('click','.js_full_screen',function(){
        full_screen($(this));
    });

    $(document).on('click','.js_theme',function(){
        change_theme($(this));
    });

    $(document).on('change','.cl_commentaire_indicateur',function(){
        var dossier = $('#dossier').val(),
            indicateur = $(this).closest('.ibox').attr('data-id'),
            commentaire = $(this).val();

        $.ajax({
            data: {
                dossier: dossier,
                indicateur: indicateur,
                commentaire: commentaire
            },
            url: Routing.generate('ind_commentaire_change'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_info('Succès','Modification enregistrée avec succès');
            }
        });
    });
});

function change_theme(li)
{
    $('.js_current_div').removeClass('js_current_div');
    var div_current = li.closest('.js_indicateur_item').addClass('js_current_div');
    $('.js_current_div li.js_theme').removeClass('active');
    li.addClass('active');
    charger_graphe(div_current);
}

/**
 * periode
 * @param btn
 */
function valider_exercice(btn)
{
    var div_current = $('.js_current_div'),
        new_html = btn.parent().parent().parent().html();
    div_current.find('.js_periode').attr('data-content',new_html).click();
    div_current.find('.js_date_picker_hidden').html(new_html);
    set_anciennete(div_current);
    charger_graphe(div_current);
}

/**
 * analyse
 * @param btn
 */
function valider_analyse(btn)
{
    btn.parent().find('.js_analyse').removeClass('active');
    btn.addClass('active');
    var div_current = btn.parent().parent().parent().parent().parent();
    charger_graphe(div_current);
}

/**
 * graphe
 * @param btn
 */
function valider_graphe(btn)
{
    var element_icon = btn.parent().parent().find('.js_graphe_icon');
    btn.parent().find('.js_graphe').removeClass('active');
    btn.addClass('active');
    btn.parent().find('.js_graphe').each(function(){
        if($(this).hasClass('active')) element_icon.addClass($(this).attr('data-fa'));
        else element_icon.removeClass($(this).attr('data-fa'));
    });
    var div_current = btn.parent().parent().parent().parent().parent();
    charger_graphe(div_current);
}

/**
 * full screen
 * @param btn
 */
function full_screen(btn)
{
    var div_current = btn.parent().parent().parent();
    charger_graphe(div_current);
}


/**
 * charger graphe
 * @param div_current
 */
function charger_graphe(div_current)
{
    div_current.find('.js_chart_conteneur').empty().height($(window).height() * 0.4);
    var code_graphe = '',
        analyse = '';
    if(typeof div_current.attr('data-id') !== 'undefined')
    {
        code_graphe = div_current.find('.js_ul_graphe').find('.active').attr('data-code').trim();
        analyse = parseInt(div_current.find('.js_ul_analyse').find('.active').attr('data-type'));

        //test dossier
        if($('#dossier option:selected').text().trim() === '')
        {
            show_info('NOTICE','CHOISIR LE DOSSIER','warning');
            return;
        }
        //test exercice
        var exercices = [];
        div_current.find('.js_date_picker_hidden .js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker())) exercices.push($(this).text().trim());
        });

        //test exercice pour un type tableau
        var tableExist = false;
        div_current.find('.js_graphe').each(function(){
            if($(this).attr('data-code').trim() === 'TAB') tableExist = true;
        });
        if(tableExist && exercices.length > 1)
        {
            show_info('NOTICE','CHOISIR Q UN SEUL EXERCICE POUR L INDICATEUR ' + div_current.find('.js_libelle_indicateur').text(),'error');
            div_current.find('.js_periode').addClass('btn-danger').removeClass('btn-white');
            return;
        }
        else div_current.find('.js_periode').removeClass('btn-danger').addClass('btn-white');

        //test exercice pour tri par date anciennete
        var date_anciennete = null,
            anciennetes = [];
        if(div_current.find('.js_anciennete_hidden').length > 0)
        {
            date_anciennete = div_current.find('.js_anciennete_hidden').find('.js_date_anciennete').val();
            div_current.find('.js_anciennete_hidden').find('td.js_td_anciennete').each(function(){
                anciennetes.push(parseInt($(this).text().trim()));
            });
        }

        if(exercices.length > 1 && div_current.find('.js_anciennete').length > 0)
        {
            show_info('NOTICE','UN SEUL EXERCICE POUR L INDICATEUR' + div_current.find('.js_libelle_indicateur').text(),'error');
            div_current.find('.js_periode').addClass('btn-danger').removeClass('btn-white');
            return;
        }
        else div_current.find('.js_periode').removeClass('btn-danger').addClass('btn-white');

        //test mois
        var moiss = [],
            periodes = [];
        div_current.find('.js_dpk_periode').each(function(){
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
                    //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':[((mois_val.length === 1) ? '0' : '') + mois_val]});
                }
                //trimestre; semestre; annee
                else if(niveau === 2 || niveau === 1 || niveau === 0)
                {
                    //each moiss
                    div_current.find('.js_dpk_mois').each(function(){
                        var mere = -2;
                        if(niveau === 2) mere = parseInt($(this).attr('data-mere-trimestre'));
                        else if(niveau === 1) mere = parseInt($(this).attr('data-mere-semestre'));
                        else if(niveau === 0) mere = parseInt($(this).attr('data-mere-annee'));
                        if(!$(this).hasClass(dpkGetActiveDatePicker()) && mere === value)
                        {
                            var mois_val = $(this).attr('data-value').trim();
                            //moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
                            array_mois.push(((mois_val.length === 1) ? '0' : '') + mois_val);
                        }
                    });
                    periodes.push({'libelle':$(this).text().trim(), 'moiss':array_mois});
                }
            }
        });//moiss; periodes{libelle, moiss}
        if(exercices.length > 1 && parseInt(div_current.attr('data-type_operation')) === 1 && analyse !== 2)
        {
            show_info('NOTICE: PLUS D UN EXERCICE !','L ANALYSE SERA AUTOMATIQUEMENT CONVERTI EN CUMULE DES SEXERCICES','error');
            analyse = 2;
        }

        var lien = Routing.generate('ind_affiche');
        $.ajax({
            data: { dossier:$('#dossier').val(),
                indicateur: div_current.attr('data-id'),
                code_graphe: code_graphe,
                analyse: analyse,
                exercices:JSON.stringify(exercices),
                moiss:JSON.stringify(moiss),
                periodes:JSON.stringify(periodes),
                date_anciennete: date_anciennete,
                anciennetes: JSON.stringify(anciennetes),
                is_etat:0
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

                var is_table = div_current.find('.js_graphe.active').attr('data-code').trim().toUpperCase() === 'TAB';
                if (is_table) div_current.find('.cl_btn_download_table').removeClass('hidden');
                else div_current.find('.cl_btn_download_table').addClass('hidden');

                var theme = parseInt(div_current.find('.js_ul_theme').find('li.active').attr('data-type')),
                    errorMessage = '';

                //div_current.find('.js_chart_conteneur').html(data);return;

                var resObj = null;
                try
                {
                    resObj = $.parseJSON(data);
                    errorMessage = typeof resObj.messageError !== 'undefined' ? resObj.messageError : '';
                }
                catch (e){ errorMessage = ''; }

                //alert(errorMessage);return;

                //div_current.find('.js_chart_conteneur').html(data);return;
                if(errorMessage.trim() !== '') div_current.find('.js_chart_conteneur').html(errorMessage);
                else if(code_graphe === 'VAL') div_current.find('.js_chart_conteneur').html(data);
                else if(code_graphe === 'TAB')
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


                    /*var dataObject = $.parseJSON(data),
                        entetes = dataObject.entetes,
                        models = dataObject.models,
                        datas = dataObject.datas,
                        styles = dataObject.styles,
                        dataObjects = [];*/

                    $.each( datas, function( i, l ){
                        var item = new Object();
                        $.each( l , function ( j, v ){
                            var key = 'col_' + j;
                            item[key] = v;
                        });
                        dataObjects.push(item);
                    });

                    var id_table = parseInt(div_current.attr('data-number')),
                        new_table = '<table id="js_table_indicateur_'+id_table+'"></table>',
                        editurl = 'test.php';
                    div_current.find('.js_chart_conteneur').html(new_table);

                    var table_selected = $('#js_table_indicateur_'+id_table),
                        w = table_selected.parent().width(),
                        h = div_current.find('.js_chart_conteneur').height() * 0.9;


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

                    set_table_jqgrid(dataObjects,h,table_get_col_model(entetes),table_get_col_model(models,w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,true);

                    /**
                     * thead
                     */
                    var index_col = 0;
                    $('#js_table_indicateur_'+id_table).closest('.ui-jqgrid').find('.ui-jqgrid-hbox')
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
                        }
                        index_col++;
                    });

                    /**
                     * tbody
                     */
                    var index_row = 0;
                    $('#js_table_indicateur_'+id_table).find('tr').each(function(){
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
                                else if($(this).text().trim() === 'OK')
                                {
                                    $(this)
                                        .html((typeU <= 2) ? '<i class="fa fa-check-circle" aria-hidden="true"></i>' : '');
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
                else
                {
                    to_chart_V2(div_current.find('.js_chart_conteneur'),code_graphe,$.parseJSON(data),theme);
                }
                set_footer(div_current,exercices);
            },
            error: function () {
                verrou_fenetre(false);
            }
        });
    }
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

/**
 * set anciennete
 * @param div_current
 */
function set_anciennete(div_current)
{
    if(div_current.find('.js_date_anciennete_hidden').length > 0)
    {
        var exercice = 0;
        div_current.find('.js_date_picker_hidden .js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker()))
            {
                exercice = parseInt($(this).text().trim());
                return true;
            }
        });
        var lien = Routing.generate('app_date_anciennete_calcule');
        $.ajax({
            data: { dossier:$('#dossier').val(), exercice: exercice },
            url: lien,
            type: 'POST',
            async:false,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                var dateSpliter = $.parseJSON(data).date.toString().substring(0,10).split('-'),
                    dateVal = dateSpliter[2] + '-' + dateSpliter[1] + '-' + dateSpliter[0];
                div_current.find('.js_anciennete_hidden').find('.js_date_anciennete').val(dateVal).attr('value',dateVal);
                div_current.find('.js_anciennete')
                    .attr('data-date_anciennete',dateVal)
                    .attr('data-content',div_current.find('.js_anciennete_hidden').html());
            }
        });
    }
}

/**
 * set footer
 * @param div_current
 * @param exercices
 */
function set_footer(div_current,exercices)
{
    $.ajax({
        data: { dossier:$('#dossier').val(), exercices:JSON.stringify(exercices) },
        url: Routing.generate('ind_exercice_status'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            var id_btn = 'st_' + div_current.attr('data-id_ncr'),
                results = $.parseJSON(data);

            $(div_current).find('.cl_container_status_ind').html(
                '<span class="btn btn-xs btn-white" id="'+ id_btn +'">' +
                    '<i class="fa fa-shopping-bag" aria-hidden="true"></i>' +
                '</span>'
            );


            $('#'+id_btn).qtip({
                content: {
                    text: function (event, api) {
                        var html = '<table class="table table-bordered">';

                        html += '' +
                            '<tr>' +
                                '<th>Exercice</th>' +
                                '<th>Résultat</th>' +
                                '<th></th>' +
                                '<th>Import</th>' +
                            '</tr>';

                        for (var i = 0; i < results.length; i++)
                        {
                            /*'exo' => $exercice,
                            'res' => $historiqueUpload ? $historiqueUpload->getResultat() : '',
                            'sta' => $historiqueUpload ? $historiqueUpload->getCloture() : 0,
                            'dup' => $historiqueUpload ? $historiqueUpload->getDateUpload()->format('d/m/Y') : '',
                            'dve' => ($historiqueUpload && $historiqueUpload->getDateVerification()) ? $historiqueUpload->getDateVerification()->format('d/m/Y') : ''*/

                            var status = '';
                            if (parseInt(results[i].sta) === 1)
                                status = 'Clôt';
                            else
                                status = 'Projet du ' + results[i].dve;

                            html += '' +
                                '<tr>' +
                                    '<td>'+results[i].exo+'</td>' +
                                    '<td>'+number_format(results[i].res,2,' ',',',false)+'</td>' +
                                    '<td>'+status+'</td>' +
                                    '<td>'+results[i].dup+'</td>' +
                                '</tr>';
                        }

                        html += '</table>';

                        return html;
                    }
                },
                position: {my: 'bottom center', at: 'top left'},
                style: {
                    classes: 'qtip-dark qtip-shadow'
                }
            });
        }
    });

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            indicateur: div_current.attr('data-id')
        },
        url: Routing.generate('ind_commentaire'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            div_current.find('.js_chart_footer').html(data);
        }
    });
}