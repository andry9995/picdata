/**
 * Created by SITRAKA on 10/02/2017.
 */

/**
 * VARIABLES
 */
var height_group_contener = 0.72,
    height_pack_content = 0.4,

    cell_old_html = '',
    cell_old_font_family = '',
    cell_old_font_weight = '',
    cell_old_italic = '',
    cell_old_text_align = '',
    cell_old_indent = 0,
    cell_old_border = '',
    cell_old_color = '',
    cell_old_bg = '',

    panel_group_active = 'panel-primary',

    rubriques = new Array(),
    super_rubriques = new Array(),
    hyper_rubriques = new Array();

/**
 * READY
 */
$(document).ready(function(){
    $('#id_groups_contener').height($(window).height() * height_group_contener);
    charger_site();
    charger_dossier_table();
    charger_rubriques(10);
    $(document).on('click','.js_show_indicateur',function(){
        change_show($(this));
    });

    $(document).on('change','#client',function(){
        $('#id_groups_contener').empty();
    });
    $(document).on('change','#site',function(){
        $('#id_groups_contener').empty();
    });

    if($('#client option').length == 1)
    {
        charger_dossier_table();
    }
});

/**
 * * FONCTIONS
 */
function change_show(btn)
{
    var type = parseInt(btn.attr('data-type')),
        element = btn.parent().parent().parent();
    if(type != 2) element = element.parent().parent();
    var entity = element.attr('data-id'),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier = zero_boost,
        status = btn.hasClass('btn-primary') ? 1 : 0,
        lien = Routing.generate('ind_change_enabled');

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier = $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else
        {
            if(type == 0)
            {
                show_info('NOTICE','CHOISIR LE DOSSIER A AFFECTER A CE GROUPE','warning');
                return;
            }
            client = $('#client').val();
        }
    }
    else return;

    $.ajax({
        data: { entity:entity, client:client, dossier:dossier, status:status, type:type },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(data == 1)
            {
                var tr_dossier_current = $('#js_dossier_table').find('tr.ui-state-highlight');
                if(btn.hasClass('btn-primary'))
                {
                    if(type == 0)
                    {
                        tr_dossier_current.addClass('tr-error').find('.js_td_dossier_groupe').attr('title','').text('');
                        btn.parent().parent().parent().parent().parent().removeClass(panel_group_active).addClass('panel-default');
                    }
                    btn.removeClass('btn-primary').addClass('btn-default');
                }
                else
                {
                    if(type == 0)
                    {
                        $('.js_btn_classement').removeClass('btn-primary').addClass('btn-default');
                        var group_libelle = btn.parent().parent().find('.js_group_libelle_string').text().trim();
                        tr_dossier_current.removeClass('tr-error').find('.js_td_dossier_groupe').attr('title',group_libelle).text(group_libelle);
                        $('.js_group_panel').removeClass(panel_group_active).addClass('panel-default');
                        btn.parent().parent().parent().parent().parent().addClass(panel_group_active).removeClass('panel-default');
                    }
                    btn.addClass('btn-primary').removeClass('btn-default');
                }
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
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

/**
 * soratable
 * @param type
 */
function change_rang(type)
{
    var liste = new Array();
    if(type == 0)
    {
        $('.group-not-sortable').find('.js_pack_panel').each(function () {
            liste.push($(this).attr('data-id'));
        });
    }
    else if(type == 1)
    {
        $('.pack-not-sortable').find('.js_indicateur_sortable').each(function(){
            liste.push($(this).attr('data-id'));
        });
    }
    else if(type == 2)
    {
        $('.js_group_panel').each(function(){
            liste.push($(this).attr('data-id'));
        });
    }

    var lien = Routing.generate('ind_rang');
    $.ajax({
        data: { liste:JSON.stringify(liste), type:type },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
        }
    });
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
