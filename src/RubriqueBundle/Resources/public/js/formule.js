/**
 * Created by SITRAKA on 15/12/2016.
 */
$(document).on('click','.js_show_rubriques_calcules',function(){
    var type = parseInt($("#js_table_type_rubrique input[type='radio']:checked").attr('data-val')),
        height = $(document).height() * 0.6;
    $.ajax({
        data: { type:type, height:height },
        url: Routing.generate('rubrique_table_show_calcules'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            var titre,animated = 'bounceInRight';
            titre = '<i class="fa fa-plus-circle"></i>&nbsp;<span>RUBRIQUES CALCULEES</span>';
            show_modal(data,titre,animated,'modal-lg');
            activer_menu_context_formule();
            $('.js_new_formule').click();
        }
    });
});

$(document).on('click','.js_td_table_rubrique_calcule',function(){
    var rubrique = $(this).closest('tr').attr('data-id');
    $('#js_id_table_formule tr').removeClass('active');
    $(this).closest('tr').addClass('active');
    $('#js_id_formule').val(rubrique);
    $('#js_formule_libelle').val($(this).text().trim());
    $.ajax({
        data: { rubrique:rubrique },
        url: Routing.generate('rubrique_table_rubriques_filles'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#js_formule').html(data);
        }
    });
});

$(document).on('click','.js_new_formule',function(){
    $('#js_id_formule').val($('#js_zero_boost').val().trim());
    $('#js_formule_libelle').val('');
    $('#js_formule').empty();
    $('#js_id_table_formule tr').removeClass('active');
});

/**
 * FORMULES
 */
$(document).on('focusin','#js_formule_libelle',function(){
    $('.blink').remove();
});

$(document).click(function(event){
    if(!($('#js_form_formule').length > 0)) return;

    var element = $(event.target);
    if((element.hasClass('js_formule') || element.parent().hasClass('js_formule'))) place_blink(element);
    else if(!(element.hasClass('js_context_rubrique_item') || element.parent().hasClass('js_context_rubrique_item'))) $('.blink').remove();
});

$(document).on('click','.js_context_rubrique_item',function(){
    add_rubrique_to_formule($(this));
});

$(document).on('click','#js_valider_formule',function(){
    save_formule($(this));
});

$(document).on('click','.js_td_table_rubrique_caclule_remove',function(){
    delete_formule($(this));
});

function formule_is_valid()
{
    if($('#js_formule_libelle').val().trim() == '')
    {
        show_info('ERREUR','NOM DE LA FORMULE VIDE','error');
        $('#js_formule_libelle').parent().addClass('has-error');
        return false;
    }
    else $('#js_formule_libelle').parent().removeClass('has-error');

    if(!($('#js_formule').find('.operande').length > 0))
    {
        show_info('ERREUR','VERIFIEZ LA FORMULE','error');
        return false;
    }
    return true;
}

function save_formule()
{
    var formule = '',
        id = $('#js_id_formule').val().trim(),
        rubriques_in_formules = new Array(),
        libelle = $('#js_formule_libelle').val().trim().sansAccent().toUpperCase(),
        type = parseInt($("#js_table_type_rubrique input[type='radio']:checked").attr('data-val'));

    if(!formule_is_valid()) return;

    $('#js_formule span.operateur').each(function(){
        if($(this).hasClass('operande'))
        {
            rubriques_in_formules.push($(this).attr('data-id'));
            formule += '#';
        }
        else formule += $(this).text().toString();
    });

    $.ajax({
        data: { action:1, type:type,
            libelle:libelle, formule:formule, id:id,
            rubriques_in_formules:JSON.stringify(rubriques_in_formules) },
        url: Routing.generate('rubrique_formule_edit'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data) == 0)
            {
                show_info('ERREUR','RUBRIQUE DEJA EXISTANTE','error');
            }
            else
            {
                if(id != $('#js_zero_boost').val().trim())
                    $('#js_id_table_formule tr.active td.js_td_table_rubrique_calcule').text(libelle);
                else
                    $('#js_id_table_formule').find('tbody').append(data);
                show_info('SUCCES','RUBRIQUE BIEN AJOUTEE');
                $('.js_new_formule').click();
            }
        }
    });
}

function place_blink(span)
{
    $('.blink').remove();
    if(span.attr('id') !== undefined && span.attr('id').trim() == 'js_formule')
    {
        if(span.html().trim() == '') span.html(blink);
        else $(blink).insertAfter(span.children('.operateur').last());
    }
    else
    {
        $(blink).insertAfter(span);
    }

    $('#js_formule_focus').focus();
}

function move_blink(span,deplacement)
{
    deplacement = (typeof deplacement !== 'undefined') ? deplacement : 'ib';

    if(deplacement == 'ib')
    {
        $('.blink').remove();
        $(blink).insertBefore(span);
    }
    else if(deplacement == 'ia')
    {
        $('.blink').remove();
        $(blink).insertAfter(span);
    }
    else if(deplacement == 'da')
    {
        $('.blink').next('.operateur').remove();
    }
    else if(deplacement == 'db')
    {
        $('.blink').prev('.operateur').remove();
    }
}

$(window).keydown(function(e) {
    if(!($('#js_form_formule').length > 0)) return;
    var key_accepts = ['ARROWLEFT','ARROWRIGHT','BACKSPACE','DELETE','ENTER','(',')','.','+','-','*','/'],
        i,span;
    for(i = 0; i < 10; i++) key_accepts.push(i.toString());
    var key = e.key.toString().toUpperCase();

    if(!$('.blink').length > 0 || !key_accepts.in_array(key)) return;
    e.preventDefault();
    if(key == 'ARROWLEFT')
    {
        span = $('.blink').prev('.operateur');
        move_blink(span,'ib');
    }
    else if(key == 'ARROWRIGHT')
    {
        span = $('.blink').next('.operateur');
        move_blink(span, 'ia');
    }
    else if(key == 'DELETE') move_blink(null,'da');
    else if(key == 'BACKSPACE') move_blink(null,'db');
    else $("<span class='operateur'>" + e.key + "</span>").insertBefore($('.blink'));
});

function activer_menu_context_formule()
{
    charger_rubriques_menu_context();
    var items = new Object(),i;
    for(i = 0; i < rubriques_formules.length; i++)
        items[rubriques_formules[i].libelle] = { name:rubriques_formules[i].libelle,className:'js_context_rubrique_item',text_:rubriques_formules[i].libelle,id_:rubriques_formules[i].id,class_:'label-primary', type_:rubriques_formules[i].type };

    $('#js_formule').contextMenu('destroy');
    $.contextMenu({
        selector: '#js_formule',
        callback: function(key, options){
        },
        autoHide: true,
        items:items,
        events: {
            show : function(){
                $('#js_formule').click();
                $('.context-menu-list').height($(window).height() * 0.3).addClass('scroller');
            },
            hide : function(){
            }
        }
    });

    $('.context-menu-one').on('click', function(){
        console.log('clicked', this);
    });

    $('.context-menu-list').addClass('dropdown-menu animated fadeInLeft');
}

function charger_rubriques_menu_context()
{
    rubriques_formules = new Array();
    $.ajax({
        data: { type:parseInt($("#js_table_type_rubrique input[type='radio']:checked").attr('data-val')), niveau:2 },
        url: Routing.generate('rubrique_rubriques'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async:false,
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var i;
            var rubriques_objects = $.parseJSON(data);
            for(i = 0;i<rubriques_objects.length;i++) rubriques_formules.push({ id:rubriques_objects[i].id, libelle:rubriques_objects[i].libelle });
        }
    });
}

function add_rubrique_to_formule(li)
{
    var id = parseInt(li.attr('data-id')),
        text = li.attr('data-text').trim(),
        new_span = '<span class="operateur operande label" data-id="'+id+'">'+text+'</span>';
    $(new_span).insertBefore($('.blink'));
}

function delete_formule(btn)
{
    swal({
        title: 'Supprimer',
        text: "Voulez-vous vraiment supprimer cette rubrique ?",
        type: 'question',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
    }).then(function () {
        $.ajax({
            data: { action:2, id:btn.closest('tr').attr('data-id') },
            url: Routing.generate('rubrique_formule_edit'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                if(parseInt(data) == 0)
                {
                    show_info('ERREUR','CETTE FORMULE NE PEUT PAS ETRE SUPPRIMEE','error');
                }
                else
                {
                    btn.closest('tr').remove();
                    show_info('SUCCES','FORMULE BIEN SUPPRIMEE');
                }
            }
        });
    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
        }
    });
}

/*
$(document).on('click','.js_edit_formule',function(){
    show_edit_formule_rubrique($(this));
});

$(document).click(function(event){
    if(!($('#js_form_formule').length > 0)) return;

    var element = $(event.target);
    if((element.hasClass('js_formule') || element.parent().hasClass('js_formule'))) place_blink(element);
    else if(!(element.hasClass('js_context_rubrique_item') || element.parent().hasClass('js_context_rubrique_item'))) $('.blink').remove();
});

$(document).on('focusin','#js_formule_libelle',function(){
    $('.blink').remove();
});

$(document).on('click','.js_context_rubrique_item',function(){
    add_rubrique_to_formule($(this));
});

$(document).on('click','#js_valider_formule',function(){
    save_formule($(this));
});

$(document).on('click','.js_delete_formule',function(){
    delete_formule($(this));
});

function save_formule(span)
{
    var formule = '',
        rubriques_in_formules = new Array(),
        libelle = $('#js_formule_libelle').val().trim().sansAccent().toUpperCase();

    if(!formule_is_valid()) return;

    $('#js_formule span.operateur').each(function(){
        if($(this).hasClass('operande'))
        {
            rubriques_in_formules.push($(this).attr('data-id'));
            formule += '#';
        }
        else formule += $(this).text().toString();
    });

    lien = Routing.generate('rubrique_formule_edit');
    $.ajax({
        data: { action:1, type:$('.js_panel_rubrique_edited').attr('data-type'),
                libelle:libelle, formule:formule, id:$('#js_id_formule').val().trim(),
                rubriques_in_formules:JSON.stringify(rubriques_in_formules) },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data) == 0)
            {
                show_info('ERREUR','RUBRIQUE DEJA EXISTANT','error');
            }
            else
            {
                if($('.js_formule_edited').length > 0) $('.js_formule_edited').text(libelle);
                else $('.js_panel_rubrique_edited .panel-body .table tbody').append(data); //$(data).insertAfter($('.js_panel_rubrique_edited .panel-body .table tbody').children('tr').last());
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
                close_modal();
            }
        }
    });
}

function formule_is_valid()
{
    if($('#js_formule_libelle').val().trim() == '')
    {
        show_info('ERREUR','NOM DE LA FORMULE VIDE','error');
        $('#js_formule_libelle').parent().addClass('has-error');
        return false;
    }
    else $('#js_formule_libelle').parent().removeClass('has-error');

    if(!($('#js_formule').find('.operande').length > 0))
    {
        show_info('ERREUR','VERIFIEZ LA FORMULE','error');
        return false;
    }
    return true;
}

function place_blink(span)
{
    $('.blink').remove();
    if(span.attr('id') !== undefined && span.attr('id').trim() == 'js_formule')
    {
        if(span.html().trim() == '') span.html(blink);
        else $(blink).insertAfter(span.children('.operateur').last());
    }
    else
    {
        $(blink).insertAfter(span);
    }

    $('#js_formule_focus').focus();
}

function show_edit_formule_rubrique(btn)
{
    $('.js_panel_rubrique_edited').removeClass('js_panel_rubrique_edited');
    $('.js_formule_edited').removeClass('js_formule_edited');

    var id = $('#js_zero_boost').val();
    if(btn.hasClass('js_add')) btn.parent().parent().parent().parent().addClass('js_panel_rubrique_edited');
    else
    {
        btn.parent().parent().parent().parent().parent().addClass('js_panel_rubrique_edited');
        id = btn.addClass('js_formule_edited').attr('data-id');
    }

    lien = Routing.generate('rubrique_formule_edit');
    $.ajax({
        data: { action:0, id:id },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            charger_rubriques_menu_context();
            if(id == $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>Nouvelle Formule</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification de '+ btn.text().trim() +'</span>';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
            $('#js_resizable').resizable({
                //handles: 's',
                stop: function(event, ui) {
                    $(this).css("width", '');
                }
            });
            activer_menu_context_formule();
        }
    });
}

function charger_rubriques_menu_context()
{
    rubriques_formules = new Array();
    lien = Routing.generate('rubrique_rubriques');
    $.ajax({
        data: { type:$('.js_panel_rubrique_edited').attr('data-type'), niveau:2 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        async:false,
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var rubriques_objects = $.parseJSON(data);
            for(i = 0;i<rubriques_objects.length;i++) rubriques_formules.push({ id:rubriques_objects[i].id, libelle:rubriques_objects[i].libelle });
        }
    });
}

function activer_menu_context_formule()
{
    var items = new Object();
    var i = 0;
    for(i = 0; i < rubriques_formules.length; i++)
        items[rubriques_formules[i].libelle] = { name:rubriques_formules[i].libelle,className:'js_context_rubrique_item',text_:rubriques_formules[i].libelle,id_:rubriques_formules[i].id,class_:'label-primary', type_:rubriques_formules[i].type };

    $(function(){
        $('#js_formule').contextMenu('destroy');
        $.contextMenu({
            selector: '#js_formule',
            callback: function(key, options){
            },
            autoHide: true,
            items:items,
            events: {
                show : function(){
                    $('#js_formule').click();
                    $('.context-menu-list').height($(window).height() * 0.3).addClass('scroller');
                },
                hide : function(){
                }
            }
        });

        $('.context-menu-one').on('click', function(e){
            console.log('clicked', this);
        });
    });

    $('.context-menu-list').addClass('dropdown-menu animated fadeInLeft');
}

$(window).keydown(function(e) {
    var key_accepts = ['ARROWLEFT','ARROWRIGHT','BACKSPACE','DELETE','ENTER','(',')','.','+','-','*','/'];
    for(i = 0; i < 10; i++) key_accepts.push(i.toString());
    var key = e.key.toString().toUpperCase();

    if(!$('.blink').length > 0 || !key_accepts.in_array(key)) return;
    e.preventDefault();
    if(key == 'ARROWLEFT')
    {
        span = $('.blink').prev('.operateur');
        move_blink(span,'ib');
    }
    else if(key == 'ARROWRIGHT')
    {
        span = $('.blink').next('.operateur');
        move_blink(span, 'ia');
    }
    else if(key == 'DELETE') move_blink(null,'da');
    else if(key == 'BACKSPACE') move_blink(null,'db');
    else $("<span class='operateur'>" + e.key + "</span>").insertBefore($('.blink'));
});

function move_blink(span,deplacement)
{
    deplacement = (typeof deplacement !== 'undefined') ? deplacement : 'ib';

    if(deplacement == 'ib')
    {
        $('.blink').remove();
        $(blink).insertBefore(span);
    }
    else if(deplacement == 'ia')
    {
        $('.blink').remove();
        $(blink).insertAfter(span);
    }
    else if(deplacement == 'da')
    {
        $('.blink').next('.operateur').remove();
    }
    else if(deplacement == 'db')
    {
        $('.blink').prev('.operateur').remove();
    }
}

function add_rubrique_to_formule(li)
{
    var id = parseInt(li.attr('data-id')),
        text = li.attr('data-text').trim(),
        new_span = '<span class="operateur operande label" data-id="'+id+'">'+text+'</span>';
    $(new_span).insertBefore($('.blink'));
}

function delete_formule(btn)
{
    lien = Routing.generate('rubrique_formule_edit');
    $.ajax({
        data: { action:2, id:btn.parent().parent().find('.js_edit_formule').attr('data-id') },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(parseInt(data) == 0)
            {
                show_info('ERREUR','UNE ERREUR C EST PRODUITE PENDANT LA SUPPRESSION','error');
            }
            else
            {
                btn.parent().parent().remove();
                show_info('SUCCES','FORMULE BIEN SUPPRIMEE');
            }
        }
    });
}*/