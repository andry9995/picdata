/**
 * Created by SITRAKA on 21/09/2016.
 */
$(document).ready(function(){
    charger_site();
    charger_pack();
});




$(document).on('change','#js_is_general',function(){
    change_is_general();
});

$(document).on('change','#dossier',function(){
    change_is_general();
});

/**
 * EVENTS
 */
/************************PACK*****************************/
$(document).on('click','.js_pack_show_edit',function(){
    show_edit_pack($(this));
});
$(document).on('click','#js_btn_save_pack',function(){
    save_pack();
});
$(document).on('click','.js_edit_pack',function(){
    show_edit_pack($(this));
});
$(document).on('click','.js_pack_delete',function(){
    delete_pack($(this));
});
$(document).on('click','.js_pack_show',function(){
    change_affichage_pack($(this));
});

/***********************PACK ITEM*************************/
$(document).on('click','.js_pack_item_show_edit',function(){
    show_edit_pack_item($(this));
});
$(document).on('click','#js_select_indicateur_item',function(){
    select_indicateur_item();
});
$(document).on('click','.js_remove_pack_item',function(){
    remove_pack_item($(this));
});





/**
 * FONCTIONS
 */
function change_is_general()
{
    if($('#js_is_general').is(':checked')) $('#js_div_dossier').addClass('hidden');
    else $('#js_div_dossier').removeClass('hidden');

    charger_pack();
}

/************************PACK*****************************/
function charger_pack()
{
    $('#js_div_accordion_pack').empty();
    if(!param_is_valid()) return;
    dossier = ($('#js_is_general').is(':checked')) ? $('#js_zero_boost').val() : $('#dossier').val();

    lien = Routing.generate('indicateur_packs');
    $.ajax({
        data: { dossier:dossier },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_div_accordion_pack').html(data);
            activer_qTip();
        }
    });
}

function param_is_valid()
{
    if(!$('#js_is_general').is(':checked') && $('#dossier option:selected').text().trim() == '')
    {
        show_info('DOSSIER VIDE','CHOISIR UN DOSSIER','error');
        return false;
    }
    return true;
}

function show_edit_pack(btn)
{
    $('.js_pack_edited').removeClass('js_pack_edited');
    id_pack = (btn.hasClass('js_add')) ? $('#js_zero_boost').val() : btn.parent().parent().parent().parent().attr('data-id');
    lien = Routing.generate('indicateur_pack_edit');
    $.ajax({
        data: { id_pack:id_pack , action:0 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(id_pack == $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>Nouveau PACK</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification de '+ btn.parent().parent().find('.js_pack_libelle').addClass('js_pack_edited').text() +'</span>';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
        }
    });
}

function save_pack()
{
    if(!pack_is_valid()) return;
    dossier = $('#js_is_general').is(':checked') ? $('#js_zero_boost').val() : $('#dossier').val();

    lien = Routing.generate('indicateur_pack_edit');
    $.ajax({
        data: { id_pack:$('#js_id_pack').val() , action:1 , libelle:$('#js_pack_libelle').val() , dossier:dossier },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_pack_edited .js_pack_libelle_string').text($('#js_pack_libelle').val());
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            if(parseInt($('#js_pack_reload').val()) == 1) charger_pack();
            close_modal();
        }
    });
}

function pack_is_valid()
{
    if($('#js_pack_libelle').val().trim() == '')
    {
        show_info('ERREUR','NOM VIDE','error');
        return false;
    }
    $('#js_pack_libelle').val($('#js_pack_libelle').val().trim().toString().sansAccent().toUpperCase());
    return true;
}

function delete_pack(btn)
{
    lien = Routing.generate('indicateur_pack_edit');
    $.ajax({
        data: { id_pack:btn.parent().parent().parent().parent().attr('data-id') , action:2 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            btn.parent().parent().parent().parent().parent().remove();
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
        }
    });
}

function change_affichage_pack(btn)
{
    element = (btn.hasClass('js_pack')) ? btn.parent().parent().parent().parent() : btn.parent().parent();
    id_pack = (btn.hasClass('js_pack')) ? element.attr('data-id') : element.attr('data-id');
    dossier = $('#dossier').val();
    lien = Routing.generate('indicateur_pack_check');
    $.ajax({
        data: { id_pack:id_pack , dossier:dossier , pack : (btn.hasClass('js_pack')) ? 0 : 1 },
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
                if(btn.hasClass('btn-primary')) btn.removeClass('btn-primary').addClass('btn-default');
                else btn.addClass('btn-primary').removeClass('btn-default');
            }
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
        }
    });
}

/***********************PACK ITEM*************************/
function show_edit_pack_item(btn)
{
    $('.js_pack_added').removeClass('js_pack_added');
    btn.parent().parent().parent().parent().parent().addClass('js_pack_added');

    $('.js_pack_edited').removeClass('js_pack_edited');
    $('.js_pack_item_edited').removeClass('js_pack_item_edited');
    id_pack = (!btn.hasClass('js_add')) ? $('#js_zero_boost').val() : btn.parent().parent().parent().parent().attr('data-id');
    id_pack_item = (!btn.hasClass('js_add')) ? btn.attr('data-id') : $('#js_zero_boost').val();
    lien = Routing.generate('indicateur_pack_item_edit');
    $.ajax({
        data: { id_pack:id_pack , action:0 , id_pack_item:id_pack_item },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(id_pack != $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>AJOUT ELEMENT DANS '+ btn.parent().parent().find('.js_pack_libelle').addClass('js_pack_edited').text() +'</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification de '+ btn.parent().parent().find('.js_pack_libelle').addClass('js_pack_edited').text() +'</span>';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
            div_to_tree('#js_indicateur_tree');
            $('#js_conteneur_indicateur_tree').height($(window).height() * 0.5);
        }
    });
}

function select_indicateur_item()
{
    if(!indicateur_item_select_is_valid()) return;
    indicateur_item = $('.js_indicateur_item_select a.jstree-clicked').parent().attr('data-id');
    id_pack = $('.js_pack_edited').parent().parent().parent().parent().parent().attr('data-id');
    dossier = $('#js_is_general').is(':checked') ? $('#js_zero_boost').val() : $('#dossier').val();

    lien = Routing.generate('indicateur_pack_item_edit');
    $.ajax({
        data: { id_pack:id_pack , action:1 , id_pack_item:id_pack_item , indicateur_item:indicateur_item , dossier:dossier },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            charger_pack_items();
            show_info('SUCCES','INDICATEUR BIEN AJOUTE');
            close_modal();
        }
    });
}

function indicateur_item_select_is_valid()
{
    if($('.js_indicateur_item_select a.jstree-clicked').length < 1)
    {
        show_info('Erreur','choisir un indicateur','error');
        return false;
    }
    return true;
}

function remove_pack_item(btn)
{
    id_pack_item = btn.parent().parent().attr('data-id');
    lien = Routing.generate('indicateur_pack_item_edit');
    $.ajax({
        data: { id_pack_item:id_pack_item , action:2 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            btn.parent().parent().remove();
            show_info('SUCCES','SUPPRESSION AVEC SUCCES');
        }
    });
}

function charger_pack_items()
{
    id_pack = $('.js_pack_added').find('div.panel-heading').attr('data-id');
    dossier = ($('#js_is_general').is(':checked')) ? $('#js_zero_boost').val() : $('#dossier').val();
    lien = Routing.generate('indicateur_pack_items');
    $.ajax({
        data: { id_pack:id_pack , dossier:dossier },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('.js_pack_added').find('div.panel-collapse div.panel-body').html(data);
        }
    });
}