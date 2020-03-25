/**
 * Created by SITRAKA on 20/09/2016.
 */
$(document).ready(function(){
    charger_site();

    $(document).on('change','#dossier',function(){
        charger_packs();
    });
    $(document).on('click','.js_periode',function(){
        $('.js_current_div').removeClass('js_current_div');
        $(this).parent().parent().parent().parent().parent().addClass('js_current_div');
    });
    $(document).on('click','.js_xdp_valider',function(){
        valider_exercice($(this));
    });
    $(document).on('click','.js_graphe',function(){
        valider_graphe($(this));
    });
    $(document).on('click','.js_analyse',function(){
        valider_analyse($(this));
    });
    $(document).on('click','.js_count_column',function(){
        change_count_column($(this));
    });
    $(document).on('click','.js_anciennete',function(){
        $('.js_current_div').removeClass('js_current_div');
        $(this).parent().parent().parent().parent().parent().addClass('js_current_div');
    });
    $(document).on('click','.js_anciennete_delete',function(){
       $(this).parent().remove();
    });
    $(document).on('click','.js_anciennete_add',function(){
        val = parseInt($(this).parent().find('.js_td_anciennete').val().trim());
        if(isNaN(val))
        {
            show_info('Erreur','VALEUR NON NUMERIQUE','error');
            return;
        }

        new_tr = '<tr>';
        new_tr += '<td class="js_td_anciennete">'+val+'</td>';
        new_tr += '<td class="pointer js_anciennete_delete text-center"><i class="fa fa-trash-o btn" aria-hidden="true"></i></td>';
        new_tr += '</tr>';

        $(new_tr).insertAfter($(this).parent().parent().parent().find('tbody tr:last'));
    });
    $(document).on('click','.js_valider_anciennete',function(){
        valider_anciennete($(this));
    });
    $(document).on('click','.js_reset_anciennete',function(){
        reset_anciennete();
    });
});

function set_date_anciennete(btn)
{
    var div_current = $('.js_current_div');
    var exercices = new Array();
    div_current.find('.js_date_picker_hidden .js_xdp .js_dp_exercice').each(function(){
        if($(this).hasClass('success')) exercices.push($(this).text().trim());
    });

    if(exercices.length > 1 && !div_current.find('.js_anciennete').hasClass('hidden'))
    {
        show_info('NOTICE','UN SEUL EXERCICE POUR L INDICATEUR' + div_current.find('.js_libelle_indicateur').text(),'error');
        div_current.find('.js_periode').addClass('btn-danger').removeClass('btn-white');
        return;
    }

    lien = Routing.generate('app_date_anciennete');
    $.ajax({
        data: { dossier:$('#dossier').val(), exercice:exercices[0] },
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
            date_ = $.parseJSON(data).date.split(' ');
            div_current.find('.js_anciennete').attr('data-date_anciennete',date_[0]);
        }
    });
}

function valider_anciennete(btn)
{
    var div_current = $('.js_current_div');
    var date_anciennete = btn.parent().parent().parent().find('.js_date_anciennete').val().trim();
    btn.parent().parent().parent().find('.js_date_anciennete').attr('value',date_anciennete).val(date_anciennete);
    var html_anciennete = btn.parent().parent().parent().html();
    var anciennetes = new Array();
    btn.parent().parent().parent().find('table.js_table_anciennete tbody tr td.js_td_anciennete').each(function(){
        anciennetes.push($(this).text().trim());
    });
    div_current.find('.js_anciennete')
        .attr('data-anciennetes',anciennetes.join(';'))
        .attr('data-date_anciennete',date_anciennete)
        .attr('data-content',html_anciennete).click();
}

function reset_anciennete()
{
    $('.js_current_div').find('.js_anciennete')
        .attr('data-anciennetes',div.find('.js_default_anciennete').val())
        .attr('data-date_anciennete',div.find('.js_default_date_anciennete').val())
        .attr('data-content',div.find('.js_default_html_anciennete').val()).click().click();
}

function charger_packs()
{
    height = $(window).height() * 0.8;
    //alert(height);
    $('#js_conteneur_pack').empty();
    if(!packs_can_load()) return;
    lien = Routing.generate('indicateur_affichage_pack');
    $.ajax({
        data: { dossier:$('#dossier').val(), count_column:$('#js_count_column').find('.active').text().trim(), height:height },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_conteneur_pack').html(data);
            $("[data-toggle=popover]").popover({html:true});
            $('#js_conteneur_pack').find('.js_indicateur_item').each(function(){
                //alert($(this).attr('data-id'));
                charger_graphe($(this));
            });
        }
    });
}

function packs_can_load()
{
    if($('#dossier option:selected').text().trim() == '')
    {
        show_info('NOTICE','CHOISIR LE DOSSIER','error');
        return false;
    }
    return true;
}

function charger_graphe(div_current)
{
    div_current.find('.js_chart').empty();
    var code_graphe = '',
        analyse = '';
    if(typeof div_current.attr('data-id') !== 'undefined')
    {
        code_graphe = div_current.find('.js_ul_graphe').find('.active').attr('data-code').trim();
        analyse = parseInt(div_current.find('.js_ul_analyse').find('.active').attr('data-type'));

        //test dossier
        if($('#dossier option:selected').text().trim() == '')
        {
            show_info('NOTICE','CHOISIR LE DOSSIER','warning');
            return;
        }
        //test exercice
        var exercices = new Array();
        div_current.find('.js_date_picker_hidden .js_xdp .js_dp_exercice').each(function(){
            if($(this).hasClass('success')) exercices.push($(this).text().trim());
        });

        if(exercices.length > 1 && !div_current.find('.js_anciennete').hasClass('hidden'))
        {
            show_info('NOTICE','UN SEUL EXERCICE POUR L INDICATEUR' + div_current.find('.js_libelle_indicateur').text(),'error');
            div_current.find('.js_periode').addClass('btn-danger').removeClass('btn-white');
            return;
        }
        else div_current.find('.js_periode').removeClass('btn-danger').addClass('btn-white');

        //test mois
        var moiss = new Array();
        div_current.find('.js_date_picker_hidden .js_xdp .js_dp_mois').each(function(){
            //mois.push((($(this).attr('data-val').trim().length == 1) ? '0' : '') + $(this).attr('data-val').trim());

            if($(this).hasClass('warning'))
            {
                var mois_val = $(this).attr('data-val').trim();
                moiss.push(((mois_val.length == 1) ? '0' : '') + mois_val);
            }
        });
        if(moiss.length < 1 || exercices.length < 1)
        {
            show_info('NOTICE','VERIFIER LA PERIODE','warning');
            return;
        }

        lien = Routing.generate('indicateur_affichage_indicateur');
        $.ajax({
            data: { dossier:$('#dossier').val(),
                    exercices:JSON.stringify(exercices),
                    moiss:JSON.stringify(moiss),
                    id_pack_item: div_current.attr('data-id'),
                    code_graphe: code_graphe,
                    analyse: analyse
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
                //div_current.find('.js_chart').html(data);return;
                if(code_graphe == 'VAL') div_current.find('.js_chart').html(data);
                else
                {
                    to_chart(div_current.find('.js_chart'),code_graphe,$.parseJSON(data),'','','','','','');
                    //to_chart(div_current.find('.js_chart'),code_graphe,$.parseJSON(data),'title X','X','title Y','Y','Titre','Sous titre');
                    highcharts_to_french();
                }
                //set_height();
            },
            error: function () {
                verrou_fenetre(false);
            }
        });
    }
}

function valider_exercice(btn)
{
    div_current = $('.js_current_div');
    new_html = btn.parent().parent().parent().parent().parent().html();
    div_current.find('.js_periode').attr('data-content',new_html).click();
    div_current.find('.js_date_picker_hidden').html(new_html);
    set_date_anciennete(div_current);
    charger_graphe(div_current);
}

function valider_graphe(btn)
{
    element_icon = btn.parent().parent().find('.js_graphe_icon');
    btn.parent().find('.js_graphe').removeClass('active');
    btn.addClass('active');
    btn.parent().find('.js_graphe').each(function(){
        if($(this).hasClass('active')) element_icon.addClass($(this).attr('data-fa'));
        else element_icon.removeClass($(this).attr('data-fa'));
    });
    div_current = btn.parent().parent().parent().parent().parent().parent().parent();
    charger_graphe(div_current);
}

function valider_analyse(btn)
{
    btn.parent().find('.js_analyse').removeClass('active');
    btn.addClass('active');
    div_current = btn.parent().parent().parent().parent().parent().parent().parent();
    charger_graphe(div_current);
}

function change_count_column(btn)
{
    $('.js_count_column').removeClass('active');
    btn.addClass('active');
    charger_packs();
}

function set_height()
{
    //$('.js_content_scroll').height($(window).height() * 0.5);
    $('.full-height-scroll').slimscroll({
        height: '10%',
        wheelStep: 10
        //color: '#a9a9a9',
    });
    // Add slimscroll to element
}