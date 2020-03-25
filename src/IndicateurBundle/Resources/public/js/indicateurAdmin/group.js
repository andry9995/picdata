/**
 * Created by SITRAKA on 07/02/2017.
 */
$(document).on('click','.js_group_collapse',function(){
    group_set_display_control();
});

$(document).on('click','.js_group_show_edit',function(){
    show_edit_group($(this));
});

$(document).on('click','#js_btn_save_group',function(){
   save_group();
});

$(document).on('click','.js_group_delete',function(){
    delete_group($(this));
});

//dupliquer
$(document).on('click','.js_dupliquer',function(){
   dupliquer($(this));
});

function dupliquer(btn)
{
    var type = parseInt(btn.attr('data-type')),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier = zero_boost,
        ind = zero_boost,
        lien = Routing.generate('ind_dupliquer');

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else client = $('#client').val();
    }

    if(type == 1) ind = btn.closest('.js_pack_panel').attr('data-id');
    else if(type == 2) ind = btn.closest('.js_indicateur_sortable').attr('data-id');

    $.ajax({
        data: { client:client, dossier:dossier, type:type, ind:ind },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data) == 1)
            {
                if(type == 1)
                {
                    $('.js_group_edited').removeClass('js_group_edited');
                    btn.closest('div.js_group_panel').addClass('js_group_edited');
                    reload_group();
                    show_info('SUCCES','PACK BIEN DUPLIQUE');
                }
                else if(type == 2)
                {
                    $('.js_pack_edited').removeClass('js_pack_edited');
                    btn.closest('div.js_pack_panel').addClass('js_pack_edited');
                    reload_pack();
                    show_info('SUCCES','INDICATEUR BIEN DUPLIQUE');
                }
            }
            else
            {
                show_info('ERREUR','UNE ERREUR EST SURVENUE PENDANT LA COPIE DES DONNEES','error');
            }
        }
    });
}

function group_charger()
{
    $('.js_cl_groups_contener').empty();
    if(!group_param_are_valid()) return;
    var client = $('#client').val(),
        dossier = ($('#dossier').val()),
        lien = Routing.generate('ind_groups');
    $.ajax({
        data: { client:client, dossier:dossier, param_gen:$('#js_is_general').is(':checked') ? 1 : 0 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_cl_groups_contener').html(data);

            $( "#id_groups_contener" ).sortable({
                placeholder: "ui-state-highlight",
                cancel: ".group-not-sortable",
                update: function() {
                    change_rang(2);
                }
            }).disableSelection();
            initialise_table();
        }
    });
}

function initialise_table(class_to_set)
{
    menu_context();
    set_action_tableau(class_to_set);
    set_height_pack_contener();
}

function group_param_are_valid()
{
    if(!$('#js_is_general').is(':checked') &&
        ($('#dossier option:selected').text().trim() == '' && $('#client option:selected').text().trim() == ''))
    {
        show_info('DOSSIER VIDE','IL FAUT SPECIFIE LE CLIENT OU LE DOSSIER','error');
        return false;
    }
    return true;
}

function group_set_display_control()
{
    $('.js_group_collapse').each(function(){
        if($(this).hasClass('collapsed'))
        {
            $(this).parent().parent().find('.js_group_control').addClass('hidden');
            $(this).parent().parent().parent().parent().parent().removeClass('group-not-sortable');
        }
        else
        {
            $(this).parent().parent().find('.js_group_control').removeClass('hidden');
            $(this).parent().parent().parent().parent().parent().addClass('group-not-sortable');
        }
    });

    $('.group-not-sortable').find('.js_pack_content').sortable({
        placeholder: "ui-state-highlight",
        cancel: ".pack-not-sortable",
        //handle: ".ibox-title",
        update: function() {
            change_rang(0);
        }
    }).disableSelection();
}

function show_edit_group(btn)
{
    var lien = Routing.generate('ind_group_edit'),
        indicateur_group = (btn.hasClass('js_add')) ? $('#js_zero_boost').val() : null;
    $('.js_group_edited').removeClass('js_group_edited');

    if(!btn.hasClass('js_add')) indicateur_group = btn.parent().parent().parent().parent().parent().addClass('js_group_edited').attr('data-id');

    $.ajax({
        data: { action:0, indicateur_group:indicateur_group },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var titre = '',animated = 'bounceInRight';
            if(indicateur_group == $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>Nouveau Groupe</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification</span>';
            show_modal(data,titre,animated);
        }
    });
}

function save_group()
{
    var lien = Routing.generate('ind_group_edit'),
        indicateur_group = $('#js_id_group').val(),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier,
        libelle = $('#js_group_libelle').val().trim().sansAccent().toUpperCase();

    if(!group_is_valid()) return;

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else client = $('#client').val();
    }

    $.ajax({
        data: { action:1, indicateur_group:indicateur_group, client:client, dossier:dossier, libelle:libelle },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data.trim()) == 1)
            {
                if(parseInt($('#js_group_reload').val()) == 1) group_charger();
                else $('.js_group_edited').find('.js_group_libelle_string').text(libelle);
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                close_modal();
            }
            else
            {
                show_info('NOM DEJA UTILISE','CHOISIR UNE AUTRE DESIGNATION','error');
            }
        }
    });
}

function group_is_valid()
{
    var element = $('#js_group_libelle');
    if(element.val().trim() == '')
    {
        show_info('ERREUR','INSERER LE NOM DU GROUPE','error');
        element.parent().parent().addClass('has-error');
        return false;
    }
    else
    {
        element.parent().parent().addClass('has-error');
        return true;
    }
}

function delete_group(btn)
{
    $('.js_group_edited').removeClass('js_group_edited');
    var indicateur_group = btn.parent().parent().parent().parent().parent().addClass('js_group_edited').attr('data-id'),
        lien = Routing.generate('ind_group_edit');
    $.ajax({
        data: { action:2, indicateur_group:indicateur_group },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data.trim()) == 1)
            {
                $('.js_group_edited').remove();
                set_height_pack_contener();
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            }
            else
            {
                show_info('ERREUR DE SUPPRESION','GROUPE NON VIDE','error');
            }
        }
    });
}

function reload_group()
{
    var gr_element = $('.js_group_edited'),
        group = gr_element.attr('data-id'),
        index = parseInt(gr_element.attr('data-index')),
        lien = Routing.generate('ind_group_reload'),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier = zero_boost;
    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else client = $('#client').val();
    }

    $.ajax({
        data: { group:group, index:index, client:client, dossier:dossier },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_group_edited').find('.js_pack_content').html(data);
            initialise_table('js_group_edited');
        }
    });
}