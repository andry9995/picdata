/**
 * Created by SITRAKA on 02/11/2016.
 */

$(document).on('click','.js_pack_show_edit',function(){
   show_edit_pack($(this));
});

$(document).on('click','#js_btn_save_pack',function(){
    save_pack();
});

$(document).on('click','.js_pack_delete',function(){
    $('.js_pack_edited').removeClass('js_pack_edited');
    $(this).parent().parent().parent().parent().parent().addClass('js_pack_edited');
    delete_pack();
});

$(document).on('click','.js_pack_collapse',function(){
    set_control_visible();
});

$(document).on('click','.js_valider_indicateur_pack',function(){
    var btn = $(this),
        status = btn.hasClass('btn-primary') ? 0 : 1,
        pack = btn.closest('.js_pack_panel').attr('data-id');

    $.ajax({
        data: { pack:pack, status:status },
        url: Routing.generate('ind_valider_pack'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if (parseInt(data) === 1)
            {
                btn.removeClass('btn-primary').removeClass('btn-default');
                if(status === 0) btn.addClass('btn-default');
                else btn.addClass('btn-primary');

                show_info('SUCCES','MODIFICATION BIEN ENREGISTRE ACCES SUCCES');
            }
            else show_info('REESSAYER',"Une erreur c'est produite pendant la modification",'error');
        }
    });
});

/*function charger_pack()
{
    $('#js_div_accordion_pack').empty();
    if(!param_is_valid()) return;
    dossier = ($('#js_is_general').is(':checked')) ? $('#js_zero_boost').val() : $('#dossier').val();

    var lien = Routing.generate('ind_pack');
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

            //resizable
            $('#js_div_accordion_pack .table-resizable').resizableColumns();

            //set collapse
            $('#js_div_accordion_pack .collapse-link').click(function () {
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

            // Fullscreen ibox function
            $('#js_div_accordion_pack .fullscreen-link').click(function() {
                var ibox = $(this).closest('div.ibox');
                var button = $(this).find('i');
                $('body').toggleClass('fullscreen-ibox-mode');
                button.toggleClass('fa-expand').toggleClass('fa-compress');
                ibox.toggleClass('fullscreen');
                setTimeout(function() {
                    $(window).trigger('resize');
                }, 100);
            });

            //menu fo rubriques
            menu_context();

            //height contener
            var height_content = $(window).height() * height_pack_content;
            $('.js_pack_content').height(height_content);

            $( "#js_div_accordion_pack" ).sortable({
                placeholder: "ui-state-highlight",
                cancel: ".pack-not-sortable",
                update: function() {
                    change_rang(0);
                }
            }).disableSelection();
        }
    });
}*/

function param_is_valid()
{
    if(!$('#js_is_general').is(':checked') &&
        $('#dossier option:selected').text().trim() == '' &&
        $('#client option:selected').text().trim() == '')
    {
        show_info('DOSSIER VIDE','CHOISIR UN DOSSIER','error');
        return false;
    }
    return true;
}

function show_edit_pack(btn)
{
    if(!param_is_valid()) return;

    var pack;
    $('.js_group_edited').removeClass('js_group_edited');
    $('.js_pack_edited').removeClass('js_pack_edited');

    if(btn.hasClass('js_add'))
    {
        pack = $('#js_zero_boost').val();
        btn.parent().parent().parent().parent().parent().addClass('js_group_edited');
    }
    else pack = btn.parent().parent().parent().parent().parent().addClass('js_pack_edited').attr('data-id');

    var lien = Routing.generate('ind_pack_edit');
    $.ajax({
        data: { pack:pack, action:0 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var titre = '',
                animated = 'bounceInRight';
            if(pack == $('#js_zero_boost').val()) titre = '<i class="fa fa-plus-circle"></i> <span>Nouveau PACK</span>';
            else titre = '<i class="fa fa-pencil-square-o"></i><span>Modification de '+ btn.parent().parent().find('.js_pack_libelle').addClass('js_pack_edited').text() +'</span>';
            show_modal(data,titre,animated);
        }
    });
}

function pack_is_valid()
{
    var input = $('#js_pack_libelle');
    if(input.val().trim() == '')
    {
        show_info('NOTICE','NOM VIDE','error');
        input.parent().parent().addClass('has-error');
        return false;
    }
    input.parent().parent().removeClass('has-error');
    return true;
}

function save_pack()
{
    if(!pack_is_valid()) return;

    var pack = $('#js_id_pack').val(),
        zero_boost = $('#js_zero_boost').val(),
        dossier = zero_boost,
        client = zero_boost,
        libelle = $('#js_pack_libelle').val().trim().sansAccent().toUpperCase(),
        lien = Routing.generate('ind_pack_edit'),
        group_pack = $('.js_group_edited').attr('data-id');

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();


        //if($('#dossier option:selected').text().trim() != '') dossier = $('#dossier').val();
        else client = $('#client').val();
    }

    $.ajax({
        data: { action:1, client:client, dossier:dossier, pack:pack, group_pack:group_pack, libelle:libelle },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                if(parseInt($('#js_pack_reload').val()) == 1)
                {
                    reload_group();
                }
                else $('.js_pack_edited').find('.js_pack_libelle_string').text(libelle);
                close_modal();
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            }
        }
    });
}

function reload_pack()
{
    var pack = $('.js_pack_edited').attr('data-id'),
        zero_boost = $('#js_zero_boost').val(),
        client = zero_boost,
        dossier = zero_boost,
        lien = Routing.generate('ind_pack_reload');

    if(!$('#js_is_general').is(':checked'))
    {
        if($('#dossier').length > 0 && $('#dossier option:selected').text().trim() != '' ||
            !($('#dossier').length > 0) && $('#js_dossier_table').find('tr.ui-state-highlight').length > 0)
            dossier =  ($('#dossier').length > 0) ? $('#dossier').val() : $('#js_dossier_table').find('tr.ui-state-highlight').find('.js_td_dossier_id').text().trim();
        else client = $('#client').val();
    }
    $.ajax({
        data: { pack:pack, client:client, dossier:dossier },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('.js_pack_edited').find('.js_indicateur_conteneur').html(data);
            initialise_table('js_pack_edited');
        }
    });
}

function delete_pack()
{
    var lien = Routing.generate('ind_pack_edit');
    $.ajax({
        data: { pack:$('.js_pack_edited').attr('data-id'), action:2 },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            if(data == 0)
            {
                show_info('ERREUR','PACK NON VIDE ou UTILISE PAR UN AUTRE CLIENT','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BEIN ENREGISTREE');
                $('.js_pack_edited').remove();
                close_modal();
            }
        }
    });
}

function set_control_visible()
{
    $('.js_pack_collapse').each(function(){
        if($(this).hasClass('collapsed'))
        {
            $(this).parent().parent().find('.js_pack_control').addClass('hidden');
            $(this).parent().parent().parent().parent().parent().removeClass('pack-not-sortable');
        }
        else
        {
            $(this).parent().parent().find('.js_pack_control').removeClass('hidden');
            $(this).parent().parent().parent().parent().parent().addClass('pack-not-sortable');
        }
    });

    $('.pack-not-sortable').find('.js_indicateur_conteneur').sortable({
        placeholder: "ui-state-highlight",
        handle: ".ibox-title",
        update: function() {
            change_rang(1);
        }
    }).disableSelection();
}

function set_height_pack_contener()
{
    var height_parent = $('#id_groups_contener').height(),
        nb_pack = $('.js_pack_content').length,
        height_pn_group = $('.js_height_pn_group:first').height(),
        reste_height = height_parent - (height_pn_group + 11) * nb_pack;
    $('.js_pack_content').height(reste_height);

    $('.js_height_ibox_tableau').css("max-height", reste_height * 0.7);
}