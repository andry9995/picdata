/**
 * Created by SITRAKA on 01/06/2016.
 */
var from_digit = 2,
    old_val = '',
    blink = '<span class="blink">|</span>',
    last_rubrique = '',
    last_super_rubrique = '',
    last_hyper_rubrique = '',
    rubriques = new Array(),
    super_rubriques = new Array(),
    hyper_rubriques = new Array(),
    rubriques_formules = new Array(),
    mere_formater = false;

$(document).ready(function(){
    /*set_height_content();
    initialise_table();
    formatTableByDigit();
    format_compte_mere();*/

    //modifications tableaux
    charger_tableau_rubriques();
});

/*$(document).on('click','#js_digit .js_btn',function(){
    change_digit($(this));
});

$(document).on('focusin','.js_input_rubrique',function(){
    old_val = $(this).val().toString().sansAccent().toUpperCase();
});

$(document).on('focusout','.js_input_rubrique',function(){
    change_rubrique($(this));
});

$(document).on('click','#js_nav_tab_compte',function(){
    initialise_table();
    formatTableByDigit();
    format_compte_mere();
});

$(document).on('click','#js_table_parametrage tr td.js_table_td',function(){
    edit_pcg_rubrique($(this));
});

$(document).on('change','#js_table_parametrage .js_select_rubrique',function(){
    change_pcg_rubrique($(this));
});
$(document).on('click','.js_div_resizable',function(){
    set_width_div_resizables($(this));
});

function set_width_div_resizables(div)
{
    $('.js_div_resizable').removeClass('col-sm-8').removeClass('col-sm-4').addClass('col-sm-2');
    div.removeClass('col-sm-2').removeClass('col-sm-4').addClass('col-sm-8');
    $('.js_div_resizable .ibox-content').addClass('hidden');
    div.find('.ibox-content').removeClass('hidden');
}

function initialise_table()
{
    charger_rubriques(10);
}

function change_pcg_rubrique(select)
{
    var selects = new Array();

    select.find('option:selected').each(function(){
       selects.push($(this).val());
    });
    var type = parseInt(select.parent().attr('data-type')),
        pcg = select.parent().parent().attr('data-id'),
        lien = Routing.generate('rubrique_pcg_change');
    $.ajax({
        data: { pcg:pcg , type:type, rubriques:JSON.stringify(selects) },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
        }
    });
}

function edit_pcg_rubrique(td)
{
    if($('#js_table_parametrage .js_select_rubrique').length > 0) reset_td();

    var type = parseInt(td.attr('data-type')),
        selects = new Array();

    td.find('.js_rubrique_select').each(function(){
       selects.push(parseInt($(this).attr('data-id')));
    });

    var rs = new Array();
    if(type == 0) rs = rubriques;
    else if(type == 1) rs = super_rubriques;
    else if(type == 2) rs = hyper_rubriques;

    var new_select = '<select class="form-control js_select_rubrique" multiple>';
    for(var i = 0;i < rs.length; i++)
    {
        var current_r = rs[i],
            is_selected = (selects.in_array(current_r.id)) ? 'selected' : '';
        new_select += '<option value="'+ current_r.id +'" '+ is_selected +'>'+ current_r.libelle +'</option>';
    }
    new_select += '</select>';

    td.html(new_select).removeClass('js_table_td');
    $('.js_select_rubrique').select2();
}

function reset_td()
{
    var old_td = $('#js_table_parametrage .js_select_rubrique').parent(),
        type = parseInt(old_td.addClass('js_table_td').attr('data-type')),
        class_span,
        class_r;

    if(type == 0)
    {
        class_span = 'label-default';
        class_r = 'js_td_rubrique';
    }
    else if(type == 1)
    {
        class_span = 'label-default';
        class_r = 'js_td_super_rubrique';
    }
    else if(type == 2)
    {
        class_span = 'label-default';
        class_r = 'js_td_hyper_rubrique';
    }
    else
    {
        class_span = 'label-default';
        class_r = 'js_td_hyper_rubrique';
    }

    var new_span = '';
    $('#js_table_parametrage .js_select_rubrique option:selected').each(function(){
        new_span += '<span class="label '+ class_span +' '+ class_r +' js_rubrique_select" data-id="'+ $(this).val() +'" style="margin: 1px!important;">'+ $(this).text().trim() +'</span>';
    });

    old_td.html(new_span);

    if(new_span != '')
    {
        var class_a_parcourir = '';
        if(type == 0) class_a_parcourir = 'js_td_rubrique';
        else if(type == 1) class_a_parcourir = 'js_td_super_rubrique';
        else if(type == 2) class_a_parcourir = 'js_td_hyper_rubrique';
        var compte_ = old_td.parent().find('.js_pcg_digit').text().trim();
        $('#js_table_parametrage tr td.'+class_a_parcourir).each(function(){
            if($(this).text().toString().trim() == '' && $(this).parent().find('.js_pcg_digit').text().trim().startsWith(compte_))
                $(this).html(new_span);
        });
    }
}

function format_compte_mere()
{
    if(mere_formater) return;
    mere_formater = true;

    var span_ok = '<span class="label label-primary"><i class="fa fa-check" aria-hidden="true"></i></span>',
        span_incomplet = '<span class="label label-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>';
    var index = 0,arrayR = new Array(),arraySR = new Array(),arrayHR = new Array();
    $($('#js_table_parametrage').find('.js_pcg_digit').get().reverse()).each(function(){
        var compte = $(this).text(),
            compte_class = compte.substring(0,compte.length - 1),
            tr = $(this).parent(),
            R = (tr.find('.js_td_rubrique').find('.js_rubrique_select').attr('data-id') !== undefined),
            SR = (tr.find('.js_td_super_rubrique').find('.js_rubrique_select').attr('data-id') !== undefined),
            HR = (tr.find('.js_td_hyper_rubrique').find('.js_rubrique_select').attr('data-id') !== undefined);
        index++;

        if(typeof arrayHR[compte] !== 'undefined')
        {
            if(!HR)
            {
                if(arrayHR[compte]) tr.find('.js_td_hyper_rubrique').append(span_ok);
                else tr.find('.js_td_hyper_rubrique').append(span_incomplet);
            }
            else
            {
                if(!arrayHR[compte]) tr.find('.js_td_hyper_rubrique').append(span_incomplet);
            }
        }
        else
        {
            arrayHR[compte] = HR;
            if(!HR) tr.find('.js_td_hyper_rubrique').append(span_incomplet);
        }
        if(typeof arrayHR[compte_class] === 'undefined') arrayHR[compte_class] = arrayHR[compte];
        else if(arrayHR[compte_class] && !arrayHR[compte]) arrayHR[compte_class] = false;


        if(typeof arraySR[compte] !== 'undefined')
        {
            if(!SR)
            {
                if(arraySR[compte]) tr.find('.js_td_super_rubrique').append(span_ok);
                else tr.find('.js_td_super_rubrique').append(span_incomplet);
            }
            else
            {
                if(!arraySR[compte]) tr.find('.js_td_super_rubrique').append(span_incomplet);
            }
        }
        else
        {
            arraySR[compte] = SR;
            if(!SR) tr.find('.js_td_super_rubrique').append(span_incomplet);
        }
        if(typeof arraySR[compte_class] === 'undefined') arraySR[compte_class] = arraySR[compte];
        else if(arraySR[compte_class] && !arraySR[compte]) arraySR[compte_class] = false;


        if(typeof arrayR[compte] !== 'undefined')
        {
            if(!R)
            {
                if(arrayR[compte]) tr.find('.js_td_rubrique').append(span_ok);
                else tr.find('.js_td_rubrique').append(span_incomplet);
            }
            else
            {
                if(!arrayR[compte]) tr.find('.js_td_rubrique').append(span_incomplet);
            }
        }
        else
        {
            arrayR[compte] = R;
            if(!R) tr.find('.js_td_rubrique').append(span_incomplet);
        }
        if(typeof arrayR[compte_class] === 'undefined') arrayR[compte_class] = arrayR[compte];
        else if(arrayR[compte_class] && !arrayR[compte]) arrayR[compte_class] = false;
    });
}

function charger_table_parametrage()
{
    lien = Routing.generate('rubrique_compte_indicateur');
    $.ajax({
        data: { admin_dossier:$('#js_admin_dossier').val().trim() , dossier:$('#dossier').val() },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            editurl = Routing.generate('rubrique_pcg_validate');
            var w = $("#js_table_parametrage").parent().width();
            var h = $(window).height() * 0.6;
            set_table_jqgrid($.parseJSON(data),h,indicateurs_get_col_model(),indicateurs_get_col_model(w),$("#js_table_parametrage"),'hidden',w,editurl,false);
            //format_tds();
            formatTableByDigit();
        }
    });
}

function indicateurs_get_col_model(w)
{
    var vide_val = '0: ;',i;
    //rubrique
    var rubrique_val = vide_val;
    for(i = 0; i < rubriques.length; i++)
    {
        rubrique_val += rubriques[i].id + ':' + rubriques[i].libelle;
        if(i != rubriques.length - 1) rubrique_val += ';';
    }
    var rubrique = { value:rubrique_val };

    //super rubrique
    var super_rubrique_val = vide_val;
    for(i = 0; i < super_rubriques.length; i++)
    {
        super_rubrique_val += super_rubriques[i].id + ':' + super_rubriques[i].libelle;
        if(i != super_rubriques.length - 1) super_rubrique_val += ';';
    }
    var superRubrique = { value:super_rubrique_val };

    //super rubrique
    var hyper_rubrique_val = vide_val;
    for(i = 0; i < hyper_rubriques.length; i++)
    {
        hyper_rubrique_val += hyper_rubriques[i].id + ':' + hyper_rubriques[i].libelle;
        if(i != hyper_rubriques.length - 1) hyper_rubrique_val += ';';
    }
    var hyperRubrique = { value:hyper_rubrique_val };
    var colModel1 = new Array();
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'pcg_compte', index:'pcg_compte', width:  w * 10 / 100, classes:'js_pcg_digit' });
        colModel1.push({ name:'pcg_intitule', index:'pcg_intitule', width:  w * 42 / 100 });
        colModel1.push({ name:'rubrique', index:'rubrique', width:  w * 15 / 100, classes:'js_jg_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:rubrique });
        colModel1.push({ name:'superRubrique', index:'superRubrique', width:  w * 15 / 100, classes:'js_jg_super_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:superRubrique });
        colModel1.push({ name:'hyperRubrique', index:'hyperRubrique', width:  w * 15 / 100, classes:'js_jg_hyper_rubrique', editable:true ,formatter:"select" ,edittype:"select" , editoptions:hyperRubrique });
        colModel1.push({ name:'save', index:'save', width:  w * 3 / 100, align:'center', formatter:'jqGridSaveFormatter',classes:'js-entite-action' });
    }
    else colModel1 = ['Compte','Intitule','rubrique','Super Rubrique','Hyper Rubrique',''];

    return colModel1;
}

function charger_rubriques(type)
{
    if(type == 0) rubriques = new Array();
    else if (type == 1) super_rubriques = new Array();
    else if (type == 2) hyper_rubriques = new Array();
    else
    {
        rubriques = new Array();
        super_rubriques = new Array();
        hyper_rubriques = new Array();
    }

    var lien = Routing.generate('rubrique_rubriques');
    $.ajax({
        data: { type:type },
        url: lien,
        type: 'POST',
        //async:false ,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            var results = $.parseJSON(data),i;
            for(i = 0; i < results.length; i++)
            {
                type = parseInt(results[i].type);
                if(type == 0) rubriques.push({ id:results[i].id, libelle:results[i].libelle });
                else if(type == 1) super_rubriques.push({ id:results[i].id, libelle:results[i].libelle });
                else if(type == 2) hyper_rubriques.push({ id:results[i].id, libelle:results[i].libelle });
            }
        }
    });
}

function jq_grid_format_rubrique(type,value)
{
    class_select = '';

    if(type == 0) class_select = 'js_rubrique_input';
    else if(type == 1) class_select = 'js_super_rubrique_input';
    else if(type == 2) class_select = 'js_hyper_rubrique_input';
    return '<input class="form-control js_input_rubrique ' + class_select + ' input-in-jqgrid js_input-in-jqgrid" value="' + value + '" data-type="'+ type +'">';
}

function format_tds()
{
    activer_auto_complete('.js_rubrique_input',rubriques);
    activer_auto_complete('.js_super_rubrique_input',super_rubriques);
    activer_auto_complete('.js_hyper_rubrique_input',hyper_rubriques);
}

function formatTableByDigit()
{
    if(from_digit == 5) $('#js_table_parametrage tr').removeClass('hidden');
    else
    {
        $('#js_table_parametrage tr td.js_pcg_digit').each(function(){
            if($(this).text().trim().length == from_digit) $(this).parent().removeClass('hidden');
            else  $(this).parent().addClass('hidden');
        });
    }
}

function change_digit(btn)
{
    $('#js_digit button').removeClass('active');
    btn.addClass('active');
    var digit = parseInt(btn.text().trim());
    from_digit = isNaN(digit) ? 5 : digit;
    formatTableByDigit();
}

function change_rubrique(input)
{
    var type = parseInt(input.attr('data-type')),
        rubrique_libelle = input.val().toString().trim().sansAccent().toUpperCase();
    input.val(rubrique_libelle);
    if(rubrique_libelle == old_val) return;

    if(!rubrique_exist(rubrique_libelle,type))
    {
        input.val('');
        return;
    }
    var pcg = parseInt(input.parent().parent().find('.js_pcg').attr('title')),
        lien = Routing.generate('rubriques_pcg_edit') + '/' + pcg;
    $.ajax({
        data: { type:type , libelle:rubrique_libelle },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            if(rubrique_libelle != '' && parseInt(data) == 1)
            {
                var class_a_parcourir = '';
                if(type == 0) class_a_parcourir = 'js_rubrique_input';
                else if(type == 1) class_a_parcourir = 'js_super_rubrique_input';
                else if(type == 2) class_a_parcourir = 'js_hyper_rubrique_input';
                var compte_ = input.parent().parent().find('.js_pcg_digit').attr('title').trim();
                $('#js_table_parametrage tbody tr td .'+class_a_parcourir).each(function(){
                    //alert($(this).val());
                    if($(this).val().toString().trim() == '' && $(this).parent().parent().find('.js_pcg_digit').attr('title').trim().startsWith(compte_))
                        $(this).val(rubrique_libelle);
                });
            }
        }
    });
}

function rubrique_exist(rubrique,type)
{
    if(rubrique == '') return true;
    var rbs = new Array(),i;
    if(type == 0) rbs = rubriques;
    else if (type == 1) rbs = super_rubriques;
    else if (type == 2) rbs = hyper_rubriques;

    for(i = 0 ; i < rbs.length ; i++) if(rbs[i].trim() == rubrique) return true;

    show_info('Erreur','CHOISIR UN RUBRIQUE PARMIS LA LISTE','error');
    return false;
}

function jqGridAfterSave(selecteur)
{
    var tr = $(selecteur).find('#'+lastsel),
        compte = tr.find('.js_pcg_digit').attr('title').trim();

    var new_rubrique = (tr.find('.js_jg_rubrique select').length > 0) ?
                        tr.find('.js_jg_rubrique select option:selected').text().trim() : tr.find('.js_jg_rubrique').text().trim(),
    new_super_rubrique = (tr.find('.js_jg_super_rubrique select').length > 0) ?
                        tr.find('.js_jg_super_rubrique select option:selected').text().trim() : tr.find('.js_jg_super_rubrique').text().trim(),
    new_hyper_rubrique = (tr.find('.js_jg_hyper_rubrique select').length > 0) ?
                        tr.find('.js_jg_hyper_rubrique select option:selected').text().trim() : tr.find('.js_jg_hyper_rubrique').text().trim();

    if(new_rubrique != last_rubrique && new_rubrique != '' ||
        new_super_rubrique != last_super_rubrique && new_super_rubrique != '' ||
        new_hyper_rubrique != last_hyper_rubrique && new_hyper_rubrique != '')
    {
        $(selecteur).find('.js_pcg_digit').each(function(){
            var tr_each = $(this).parent(),
                compte_each = $(this).text().trim();
            //rubrique
            if(new_rubrique != last_rubrique && new_rubrique != '')
                if(compte_each.startsWith(compte) && tr_each.find('.js_jg_rubrique').text().trim() == '')
                    tr_each.find('.js_jg_rubrique').text(new_rubrique);

            //super rubrique
            if(new_super_rubrique != last_super_rubrique && new_super_rubrique != '')
                if(compte_each.startsWith(compte) && tr_each.find('.js_jg_super_rubrique').text().trim() == '')
                    tr_each.find('.js_jg_super_rubrique').text(new_super_rubrique);

            //hyper rubrique
            if(new_hyper_rubrique != last_hyper_rubrique && new_hyper_rubrique != '')
                if(compte_each.startsWith(compte) && tr_each.find('.js_jg_hyper_rubrique').text().trim() == '')
                    tr_each.find('.js_jg_hyper_rubrique').text(new_hyper_rubrique);

            if(compte.substring(0,1) < compte_each.substring(0,1)) return true;
        });
    }
}
function jqGridOnSelectRow(tr)
{
    last_rubrique = (tr.find('.js_jg_rubrique select').length > 0) ?
        tr.find('.js_jg_rubrique select option:selected').text().trim() : tr.find('.js_jg_rubrique').text().trim();
    last_super_rubrique = (tr.find('.js_jg_super_rubrique select').length > 0) ?
        tr.find('.js_jg_super_rubrique select option:selected').text().trim() : tr.find('.js_jg_super_rubrique').text().trim();
    last_hyper_rubrique = (tr.find('.js_jg_hyper_rubrique select').length > 0) ?
        tr.find('.js_jg_hyper_rubrique select option:selected').text().trim() : tr.find('.js_jg_hyper_rubrique').text().trim();
}

function set_height_content()
{
    $('.js_conteneur_rubrique').height($(window).height() * 0.35);
    $('.js_conteneur_rubrique_calcule').height($(window).height() * 0.2);
    $('#js_scroll').height($(window).height() * 0.7);
}*/

/*$(document).ready(function(){
    old_val = '';
    new_val = '';
    from_digit = 2;

    $("#js_digit").ionRangeSlider({
        min: 1,
        max: 5,
        type: 'single',
        step: 1,
        postfix: " Digits",
        from: 2,
        prettify: false,
        hasGrid: true,
        onChange: saveResult
    });
    function saveResult(data)
    {
        from_digit = parseInt(data.fromNumber);
        formatTableByDigit();
    }

    rubriques = new Array();
    superRubriques = new Array();
    hyperRubriques = new Array();
    typesCout = new Array();
    regles = new Array();

    if($('#client').length > 0) charger_site();
    //charger_table();

    $(document).on('focusin','.js_input-in-jqgrid',function(){
        old_val = $(this).val().trim().toUpperCase();
    });
    $(document).on('focusout','.js_input-in-jqgrid',function(){
        new_val = $(this).val().trim().toUpperCase();
        if(old_val != new_val) save_change_rubrique($(this));
    });

    $(document).on('click','#js_digit .js_btn',function(){
        change_digit($(this));
    });
});

$(document).on('click','#js_rubriques',function(){
    show_params_rubriques();
});

function charger_table()
{
    if(!parametre_is_valid()) return;
    lien = Routing.generate('rubrique_compte_indicateur');

    $.ajax({
        data: { admin_dossier:$('#js_admin_dossier').val().trim() , dossier:$('#dossier').val() },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            charger_auto_complete();
            editurl = 'test.php';
            var w = $("#js_table_parametrage").parent().width();
            var h = $(window).height() * 0.6;
            set_table_jqgrid($.parseJSON(data),h,indicateurs_get_col_model(),indicateurs_get_col_model(w),$("#js_table_parametrage"),'hidden',w,editurl,false);
            formatter_td();
            formatTableByDigit();
        }
    });
}

function parametre_is_valid()
{
    if($('#client').length > 0)
        if($("#dossier option:selected").text().trim().toUpperCase() == 'TOUS' || $("#dossier option:selected").text().trim() == '')
        {
            show_info('Champ non valide', 'Choisir le Dossier', 'warning');
            $('#dossier').parent().parent().addClass('has-warning');
            return false;
        }
    else $('#dossier').parent().parent().removeClass('has-warning');
    return true;
}

function indicateurs_get_col_model(w)
{
    colModel1 = new Array();

    if(typeof w !== 'undefined')
    {
        colModel1.push({ name: 'pcg', index: 'pcg' , width:  w * 10 / 100, sorttype: "str" , formatter: function (pcg) { return pcg.compte } ,classes:'js_pcg_digit' });
        colModel1.push({ name: 'pcg', index: 'pcg', width:  w * 40 / 100, sorttype: "str" , formatter: function (pcg) { return pcg.intitule } });
        colModel1.push({ name: 'pcg', index: 'pcg', width:  0, sorttype: "str" , formatter: function (pcg) { return pcg.id } , hidden : true ,classes:'js_pcg'});
        colModel1.push({ name: 'rubrique', index: 'rubrique', width:  w * 10 / 100, formatter: function (rubrique) { return jq_grid_formatter_auto_complete((rubrique == null) ? '' : rubrique.rubrique.libelle,'js_rubrique') } });
        colModel1.push({ name: 'superRubrique', index: 'superRubrique', width:  w * 10 / 100, formatter: function (rubrique) { return jq_grid_formatter_auto_complete((rubrique == null) ? '' : rubrique.rubrique.libelle,'js_super_rubrique') } });
        colModel1.push({ name: 'hyperRubrique', index: 'hyperRubrique', width:  w * 10 / 100, formatter: function (rubrique) { return jq_grid_formatter_auto_complete((rubrique == null) ? '' : rubrique.rubrique.libelle,'js_hyper_rubrique') } });
        colModel1.push({ name: 'typeCount', index: 'typeCount', width:  w * 10 / 100, formatter: function (rubrique) { return jq_grid_formatter_auto_complete((rubrique == null) ? '' : rubrique.rubrique.libelle,'js_type_cout') } });
        colModel1.push({ name: 'regle', index: 'regle', width:  w * 10 / 100, formatter: function (rubrique) { return jq_grid_formatter_auto_complete((rubrique == null) ? '' : rubrique.rubrique.libelle,'js_regle') } });
    }
    else colModel1 = ['Compte','Intitule','Pcg','Rubrique','SuperRubrique','HyperRubrique','Type de cout','Regle'];

    return colModel1;
}

function jq_grid_formatter_auto_complete(value,class_spec)
{
    return '<input class="form-control ' + class_spec + ' input-in-jqgrid js_input-in-jqgrid" value="' + value + '">';
}

function save_change_rubrique(input)
{
    input.val(input.val().trim().toUpperCase());
    pcg = parseInt(input.parent().parent().find('.js_pcg').text().trim());
    lien = Routing.generate('rubrique_save_rubrique') + '/' + pcg;

    type = 0;
    if(input.hasClass('js_rubrique')) type = 0;
    if(input.hasClass('js_super_rubrique')) type = 1;
    if(input.hasClass('js_hyper_rubrique')) type = 2;
    if(input.hasClass('js_type_cout')) type = 3;
    if(input.hasClass('js_regle')) type = 4;

    $.ajax({
        data: { rubrique : input.val().trim() , type:type },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            charger_auto_complete(type);
            formatter_td(type);
        }
    });
}

function charger_auto_complete_item(type)
{
    lien = Routing.generate('rubrique_rubriques');
    $.ajax({
        data: { type:type},
        url: lien,
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            array_temp = $.parseJSON(data);
            array_empty = new Array();
            array_temp.forEach(function(entry) {
                array_empty.push(entry.libelle);
            });
            if(type == 0) rubriques = array_empty;
            if(type == 1) superRubriques = array_empty;
            if(type == 2) hyperRubriques = array_empty;
            if(type == 3) typesCout = array_empty;
            if(type == 4) regles = array_empty;
        }
    });
}

function formatter_td(type)
{
    type = (typeof rowNum !== 'undefined') ? type : 99;
    if(type == 0 || type == 99)
    {
        activer_auto_complete('.js_rubrique',rubriques,true);
        activer_auto_complete('.js_rubrique',rubriques);
    }
    if(type == 1 || type == 99)
    {
        activer_auto_complete('.js_super_rubrique',superRubriques,true);
        activer_auto_complete('.js_super_rubrique',superRubriques);
    }
    if(type == 2 || type == 99)
    {
        activer_auto_complete('.js_hyper_rubrique',hyperRubriques,true);
        activer_auto_complete('.js_hyper_rubrique',hyperRubriques);
    }
    if(type == 3 || type == 99)
    {
        activer_auto_complete('.js_type_cout',typesCout,true);
        activer_auto_complete('.js_type_cout',typesCout);
    }
    if(type == 4 || type == 99)
    {
        activer_auto_complete('.js_regle',regles,true);
        activer_auto_complete('.js_regle',regles);
    }
}

function charger_auto_complete(type)
{
    type = (typeof rowNum !== 'undefined') ? type : 99;
    if(type == 0 || type == 99) charger_auto_complete_item(0);
    if(type == 1 || type == 99) charger_auto_complete_item(1);
    if(type == 2 || type == 99) charger_auto_complete_item(2);
    if(type == 3 || type == 99) charger_auto_complete_item(3);
    if(type == 4 || type == 99) charger_auto_complete_item(4);
}

function formatTableByDigit()
{
    if(from_digit == 5) $('#js_table_parametrage tr').removeClass('hidden');
    else
    {
        $('#js_table_parametrage tr td.js_pcg_digit').each(function(){
            if($(this).text().trim().length == from_digit) $(this).parent().removeClass('hidden');
            else  $(this).parent().addClass('hidden');
        });
    }
}

function change_digit(btn)
{
    $('#js_digit button').removeClass('active');
    btn.addClass('active');
    digit = parseInt(btn.text().trim());
    from_digit = isNaN(digit) ? 5 : digit;
    formatTableByDigit();
}

function show_params_rubriques()
{
    lien = Routing.generate('rubriques_admin');
    $.ajax({
        data: {  },
        url: lien,
        //type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            titre = '<i class="fa fa-cogs fa-2x" aria-hidden="true"></i>&nbsp;<span>Param&egrave;tres d&apos;envoi</span>';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
            if(parseInt($('#js_charger_site').val()) == 1) charger_site();
        }
    });
}*/