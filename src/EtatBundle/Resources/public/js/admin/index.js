/**
 * Created by SITRAKA on 31/03/2017.
 */

var rubriques = new Array(),
    super_rubriques = new Array(),
    hyper_rubriques = new Array(),

    cell_old_html = '',
    cell_old_font_family = '',
    cell_old_font_weight = '',
    cell_old_italic = '',
    cell_old_text_align = '',
    cell_old_indent = 0,
    cell_old_border = '',
    cell_old_color = '',
    cell_old_bg = '';

$(document).ready(function(){
    charger_site();

    charger_tab_active();

    charger_rubriques(10);

    $(document).on('change','#regime_fiscal',function(){
        charger_tab_active();
    });

    $(document).on('click','.js_li_tab',function(){
        charger_tab_active();
    });
});

function charger_tab_active()
{
    var //etat = $('#js_id_etat').val(),
        zero_boost = $('#js_zero_boost').val(),
        type = zero_boost,
        client = zero_boost,
        dossier = zero_boost,
        gen_element = $('#js_is_general'),
        regime = $('#regime_fiscal').val(),
        panel_body = $('#id_groups_contener .tab-content > div.active').find('.panel-body'),
        etat = panel_body.closest('.tab-pane').attr('data-id');

    if((gen_element.length > 0 && !gen_element.is(':checked')) || !gen_element.length > 0)
    {
        if($('#dossier option:selected').text().trim() == '' && $('#client option:selected').text().trim() == '')
        {
            show_info('NOTICE','SPECIFIER LE CLIENT OU LE DOSSIER','error');
            return;
        }
        client = $('#client').val();
        if($('#dossier').length > 0) dossier = $('#dossier').val();
    }

    if($('#js_type_client').length > 0)
    {
        type = $('#js_type_client').val();
    }

    $.ajax({
        data: { client:client, dossier:dossier, etat:etat, type:type, regime:regime },
        url: Routing.generate('etat_etat_details'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            panel_body.html(data);
            panel_body.find('.js_height_ibox_tableau').height($(window).height() - 300);
            initialise_table();
        }
    });
}

function charger_etats()
{
    $('#js_id_container_etats').empty();
    var lien = Routing.generate('etat_liste'),
        etat = $('#js_id_etat').val(),
        zero_boost = $('#js_zero_boost').val(),
        type = zero_boost,
        client = zero_boost,
        dossier = zero_boost,
        gen_element = $('#js_is_general');
    if((gen_element.length > 0 && !gen_element.is(':checked')) || !gen_element.length > 0)
    {
        if($('#dossier option:selected').text().trim() == '' && $('#client option:selected').text().trim() == '')
        {
            show_info('NOTICE','SPECIFIER LE CLIENT OU LE DOSSIER','error');
            return;
        }
        client = $('#client').val();
        if($('#dossier').length > 0) dossier = $('#dossier').val();
    }

    if($('#js_type_client').length > 0)
    {
        type = $('#js_type_client').val();
    }

    $.ajax({
        data: { client:client, dossier:dossier, etat:etat, type:type },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#id_groups_contener').html(data);
            $('.js_height_ibox_tableau').height($(window).height() * 0.6);
            initialise_table();
        }
    });
}

function initialise_table(class_to_set)
{
    menu_context();
    set_action_tableau(class_to_set);
    //set_height_pack_contener();
}

/**
 *
 * @param type
 */
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
        async:false ,
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
                if(type == 0) rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
                else if(type == 1) super_rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
                else if(type == 2) hyper_rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
            }
        }
    });
}

/**
 * set collapse, full screnn tableau
 * @param class_to_set
 */
function set_action_tableau(class_to_set)
{
    class_to_set = typeof class_to_set !== 'undefined' ? '.' + class_to_set + ' ' : '';

    // Fullscreen ibox function
    $(class_to_set + '.fullscreen-link').click(function() {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function() {
            $(window).trigger('resize');
        }, 100);
    });

    // Collapse ibox function
    $(class_to_set + '.collapse-link').click(function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });
}