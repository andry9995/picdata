var rubriques = [],
    super_rubriques = [],
    hyper_rubriques = [],
    new_indicateur = null,
    cl_add_in = 'add-in';


$(document).ready(function(){
    charger_tb_domaine();
    //charger_indicateurs();
    charger_rubriques(10);

    $(document).on('click','.js_cl_edit_indicateur',function(e){
        //if ($(this).hasClass('js_add')) e.preventDefault();
        show_edit_indicateur($(this));
    });

    $(document).on('click','.js_cl_add_indicateur',function(){
        var libelle = $(this).closest('.form-horizontal').find('.js_cl_indicateur_libelle').val().trim();
        if (libelle === '')
        {
            show_info('ERREUR','Nom vide','error');
            return;
        }
        var exist = false,
            div_container_domaine = $('.'+cl_add_in);

        div_container_domaine.find('li.js_cl_edit_indicateur').each(function(){
            if ($(this).find('.js_cl_lib').text().trim().toUpperCase() === libelle.toUpperCase())
            {
                exist = true;
            }
        });
        if (exist)
        {
            show_info('ERREUR','CE NOM EXISTE DEJA','error');
            return;
        }
        new_indicateur = libelle;

        $.ajax({
            data: {
                libelle: libelle,
                affichage: $('#id_tb_type').val(),
                indicateur_tb_domaine: div_container_domaine.attr('data-id')
            },
            type: 'POST',
            url: Routing.generate('ind_tb_add_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = parseInt(data);
                if (res === 0)
                {
                    show_info('SUCCES','INDICATEUR BIEN ENREGISTRE');
                    close_modal();
                    charger_indicateurs();
                }
                else
                {
                    show_info('ERREUR','INDICATEUR DEJA EXISTANT','error');
                }
            }
        });
    });

    $(document).on('click','.js_cl_delete_indicateur',function(e){
        e.stopPropagation();
        var indicateur = $(this).closest('.cl_li').attr('data-id'),
            btn = $(this),
            type = $(this).closest('.cl_li').attr('data-type');

        $.ajax({
            data: {
                action: 2,
                indicateur: indicateur,
                type: type
            },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_id_container_indicateur_etail').empty();
                btn.closest('.cl_li').remove();
                show_info('SUCCES','SUPPRESION REUSSIE AVEC SUCCES');
            }
        });
    });

    $(document).on('click','.js_rubrique_sel',function(){
        change_rubrique_type($(this));
    });

    $(document).on('click','.js_rubrique_item',function(){
        add_rubrique_in_cell($(this));
    });

    $(document).on('change','#js_id_tb_lib',function(){
        var lib = $(this).val().trim()/*.toUpperCase()*/,
            indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        if (lib === '')
        {
            show_info('ERREUR','NOM VIDE','error');
            return;
        }
        var exist = false;
        $('#js_id_container_indicateur').find('li.js_cl_edit_indicateur').each(function(){
            if ($(this).find('.js_cl_lib').text().trim() === lib)
            {
                exist = true;
            }
        });
        if (exist)
        {
            show_info('ERREUR','CE NOM EXISTE DEJA','error');
            return;
        }

        $.ajax({
            data: { action:1, indicateur:indicateur, libelle:lib, champ:0 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var res = parseInt(data);
                if (res === 0)
                {
                    show_info('SUCCES','INDICATEUR BIEN ENREGISTRE');
                    $('.indicateur_edited').find('span.js_cl_lib').text(lib);
                }
                else if (res === 1)
                {
                    show_info('ERREUR','NOM DEJA EXISTANT','error');
                }
                else show_info('ERREUR','UNE ERREUR C EST PRODUITE PENDANT LA MODIFICATION','error');
            }
        });
    });

    $(document).on('change','#js_id_tb_norme',function(){
        var indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        $.ajax({
            data: { action:1, indicateur:indicateur, norme:$(this).val().trim(), champ:4 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','NORME BIEN ENREGISTREE');
            }
        });
    });

    $(document).on('change','#js_id_tb_description',function(){
        var indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        $.ajax({
            data: { action:1, indicateur:indicateur, description:$(this).val().trim(), champ:5 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','DESCRIPTION BIEN ENREGISTREE');
            }
        });
    });

    $(document).on('change','#js_id_tb_unite',function(){
        var indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        $.ajax({
            data: { action:1, indicateur:indicateur, unite:(($(this).is(':checked')) ? 1 : 0), champ:6 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            }
        });
    });

    $(document).on('change','#js_id_tb_decimal',function(){
        var indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        $.ajax({
            data: { action:1, indicateur:indicateur, dec:(($(this).is(':checked')) ? 2 : 0), champ:7 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
            }
        });
    });

    $(document).on('change','#js_id_tb_pond',function(){
        var ponderation = parseFloat($(this).val().trim()),
            total_ponderation = 0,
            indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');

        if (isNaN(ponderation))
        {
            show_info('ERREUR','LA PONDERATION DOIT ETRE UN NOMBRE','error');
            return;
        }
        $('#js_id_container_indicateur').find('li.js_cl_edit_indicateur a span.label').each(function(){
            if (!$(this).closest('.js_cl_edit_indicateur').hasClass('indicateur_edited')) total_ponderation += parseFloat($(this).text().trim());
        });
        total_ponderation += ponderation;
        if (total_ponderation > 100)
        {
            show_info('Total Ponderation:' + total_ponderation,'LA SOMME DES PONDERATIONS NE DOIT PAS DEPASSEE 100','error');
            return;
        }

        $.ajax({
            data: { action:1, indicateur:indicateur, ponderation:ponderation, champ:1 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('.indicateur_edited').find('span.label').text(ponderation);
                show_info('SUCCES','INDICATEUR BIEN ENREGISTRE');
                $('#id_total_ponderation').text(total_ponderation);
                var class_total;
                if (total_ponderation === 100) class_total = 'text-navy';
                else if (total_ponderation < 100) class_total = 'text-warning';
                else class_total = 'text-danger';
                $('#id_total_ponderation').closest('a').find('.fa')
                    .removeClass('text-navy')
                    .removeClass('text-warning')
                    .removeClass('text-danger')
                    .addClass(class_total);
            }
        });
    });

    $(document).on('click','#js_id_save_formule',function(){
        var operandes = [],
            formule = '',
            indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        //formule
        $('#js_id_formule').find('span').each(function(){
            var txt = $(this).text().trim();
            if (!$(this).hasClass('blink') && txt !== '')
            {
                if ($(this).hasClass('operande'))
                {
                    formule += '#';
                    operandes.push({ id:$(this).attr('data-id'), v:$(this).attr('data-variation')} );
                }
                else formule += txt;
            }
        });
        if (operandes.length < 0)
        {
            show_info('ERREUR','LA FORMULE NE CONTIENT AUCUN OPERANDE','error');
            return;
        }

        $.ajax({
            data: { action:1, indicateur:indicateur, formule:formule, operandes:JSON.stringify(operandes), champ:2 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','INDICATEUR BIEN ENREGISTRE');
            }
        });
    });

    $(document).on('change','input[name="radio_type"]',function(){
        var type = parseInt($('input[name="radio_type"]:checked').val()),
            indicateur = $(this).closest('.js_cl_indicateur_detail_container').attr('data-id');
        $.ajax({
            data: { action:1, indicateur:indicateur, type:type, champ:3 },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('SUCCES','INDICATEUR BIEN ENREGISTRE');
            }
        });
    });

    $(document).click(function(event) {
        var element = $(event.target);
        if (element.hasClass('js_rubrique_item') || element.closest('.js_rubrique_item').length > 0) return;
        $('#js_blink_formule').remove();
        if (element.hasClass('js_cell_indicateur') || element.closest('.js_cell_indicateur').length > 0)
        {
            $('.js_cell_indicateur').addClass('js_cellule_edited');

            var blink = '<span class="blink" id="js_blink_formule">|</span>';
            if (element.hasClass('js_cell_indicateur'))
            {
                element.append(blink);
            }
            else
            {
                if (element.hasClass('operateur')) $(blink).insertAfter(element);
                else $(blink).insertAfter(element.closest('.operateur'));
            }
        }
        else
        {
            $('.js_cellule_edited').removeClass('js_cellule_edited');
        }
    });

    $(window).keydown(function(e) {
        if($('.js_cellule_edited').length > 1) return;
        var key_spec = ['ESCAPE','CONTROL','SHIFT','NUMLOCK','CAPSLOCK','CONTEXTMENU','META','INSERT','HOME','PAGEUP','END','PAGEDOWN','ALT','ALTGRAPH'];

        for(var i = 1; i <= 12; i++) key_spec.push('F'+i);
        var key = e.key.toString().toUpperCase();

        if(key === 'CONTROL') cntrlIsPressed = true;
        //if(ctrl_mode) return;
        var span;

        if(!$('.js_cellule_edited').length > 0 || key_spec.in_array(key))
        {
            return;
        }
        e.preventDefault();
        if(key === 'ARROWLEFT')
        {
            span = $('#js_blink_formule').prev('.operateur');
            move_blink(span,'ib');
        }
        else if(key === 'ARROWRIGHT')
        {
            span = $('#js_blink_formule').next('.operateur');
            move_blink(span, 'ia');
        }
        else if(key === 'DELETE') move_blink(null,'da');
        else if(key === 'BACKSPACE') move_blink(null,'db');
        else if(key === 'TAB')
        {
            if($('.blink').parent().is(':last-child')) $('.blink').parent().parent().next('tr').children('td:first').click();
            else $('.blink').parent().next().click();
        }
        else $("<span class='operateur'>" + e.key + "</span>").insertBefore($('.blink'));
    });
});

function charger_indicateurs()
{
    var div_collapse = $('#id_accordion').find('.collapse.in');
    if (div_collapse.length === 0) return;
    var indicateur_tb_domaine = div_collapse.closest('.panel').attr('data-id');

    div_collapse.find('.panel-body').empty();
    $('#js_id_container_indicateur_etail').empty();

    $.ajax({
        data: {
            affichage: parseInt($('#id_tb_type').val().trim()),
            indicateur_tb_domaine: indicateur_tb_domaine
        },
        type: 'POST',
        url: Routing.generate('ind_tb_admin_indicateurs'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            div_collapse.find('.panel-body').html(data).height($(window).height() - 290);

            if (new_indicateur !== null)
            {
                $('.'+cl_add_in).find('.js_cl_edit_indicateur .js_cl_lib').each(function(){
                    if ($(this).text().trim().toUpperCase() === new_indicateur.toUpperCase()) $(this).closest('li').click();
                });
                new_indicateur = null;
            }
        }
    });
}

function sort_indicateur()
{
    var sorts = [],
        index = 1;
    $('#js_id_container_indicateur').find('.js_cl_edit_indicateur').each(function(){
        if (!$(this).hasClass('js_add'))
        {
            sorts.push({ id:$(this).attr('data-id'), rang:index });
            index++;
        }
    });

    $.ajax({
        data: { sorts:JSON.stringify(sorts) },
        type: 'POST',
        url: Routing.generate('ind_tb_sort_indicateurs'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('Succes','MODIFICATION ENREGISTREE AVEC SUCCES');
        }
    });
}

function show_edit_indicateur(btn)
{
    $('.indicateur_edited').removeClass('indicateur_edited');
    $('.'+cl_add_in).removeClass(cl_add_in);

    btn.closest('.panel').addClass(cl_add_in);
    if (btn.hasClass('js_add'))
    {
        var indicateur_domaine = btn.closest('.panel').find('.cl_nom_domaine').text().trim();
        show_modal($('#js_id_indicateur_add_hidden').html(),'Nouveau indicateur' + ((indicateur_domaine === '') ? '' : (' (' + indicateur_domaine + ')')));
    }
    else
    {
        btn.addClass('indicateur_edited');
        $.ajax({
            data: { action:0, indicateur:btn.attr('data-id') },
            type: 'POST',
            url: Routing.generate('ind_tb_edit_indicateur'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_id_container_indicateur_etail').html(data);
                /*$('#js_id_formule').height(($('#js_id_container_indicateur_etail').height() - 200));
                $('#js_container_conditions').height(($('#js_id_container_indicateur_etail').height() - 23));*/
                menu_context();
            }
        });
    }
}

function move_blink(span,deplacement)
{
    //if(ctrl_mode) return;
    deplacement = (typeof deplacement !== 'undefined') ? deplacement : 'ib';
    var blink = '<span class="blink" id="js_blink_formule">|</span>';

    if(deplacement === 'ib')
    {
        $('.blink').remove();
        $(blink).insertBefore(span);
    }
    else if(deplacement === 'ia')
    {
        $('.blink').remove();
        $(blink).insertAfter(span);
    }
    else if(deplacement === 'da')
    {
        $('.blink').next('.operateur').remove();
    }
    else if(deplacement === 'db')
    {
        $('.blink').prev('.operateur').remove();
    }
}

function menu_context()
{
    var items = new Object(),i;
    for(i = 0; i < rubriques.length; i++)
        items['0_'+rubriques[i].libelle] = { name:rubriques[i].libelle,className:'js_rubrique_item js_r_0',text_:rubriques[i].libelle,id_:rubriques[i].id,class_:'label-primary', type_:rubriques[i].type/*,type:'text_select',options:options,selected:0*/ /*,span_:span_add*/ };
    for(i = 0; i < super_rubriques.length; i++)
        items['1_'+super_rubriques[i].libelle] = { name:super_rubriques[i].libelle,className:'js_rubrique_item js_r_1',text_:super_rubriques[i].libelle, id_:super_rubriques[i].id, class_:'label-info',type_:super_rubriques[i].type/*,type:'text_select',options:options,selected:0*/  };
    for(i = 0; i < hyper_rubriques.length; i++)
        items['2_'+hyper_rubriques[i].libelle] = { name:hyper_rubriques[i].libelle,className:'js_rubrique_item js_r_2',text_:hyper_rubriques[i].libelle, id_:hyper_rubriques[i].id, class_:'label-default',type_:hyper_rubriques[i].type/*,type:'text_select',options:options,selected:0*/  };

    $(function(){
        $('.js_cell_indicateur').contextMenu('destroy');
        $.contextMenu({
            selector: '.js_cell_indicateur',
            callback: function(key, options){ },
            autoHide: true,
            items:items,
            events: {
                show : function(){
                    //if(ctrl_mode) $(this).close();
                    var class_of_edited = 'label-primary',
                        type_chose = $('.js_rubrique_sel_ul .active').attr('data-type');

                    //si pas de cellule editable
                    if(!$(this).hasClass('js_cellule_edited')) $(this).click();

                    //get row,col
                    var row = parseInt($(this).attr('data-row'));
                    var col = parseInt($(this).attr('data-col'));
                    $('.js_rubrique_item').addClass('hidden');
                    $('.js_rubrique_item_r_c').addClass('hidden');

                    $('.context-menu-list').height($(window).height() * 0.3).addClass('scroller');
                    $('.js_r_'+type_chose).removeClass('hidden');
                    $(this).addClass(class_of_edited);
                },
                hide : function(){
                    $(this).removeClass('label-primary');
                }
            }
        });

        $('.context-menu-one').on('click', function(){
            console.log('clicked', this);
        });
    });

    $('.context-menu-list').addClass('dropdown-menu animated fadeInLeft');
}

function change_rubrique_type(li)
{
    $('.js_rubrique_sel').removeClass('active').removeClass('rubrique_sel');
    li.addClass('active rubrique_sel').closest('.btn-group').find('.dropdown-toggle').text(li.text());
}

function charger_rubriques(type)
{
    if(type === 0) rubriques = [];
    else if (type === 1) super_rubriques = [];
    else if (type === 2) hyper_rubriques = [];
    else
    {
        rubriques = [];
        super_rubriques = [];
        hyper_rubriques = [];
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
                if(type === 0) rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
                else if(type === 1) super_rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
                else if(type === 2) hyper_rubriques.push({ id:results[i].id, libelle:results[i].libelle, type:type });
            }
        }
    });
}

function add_rubrique_in_cell(li)
{
    //if(ctrl_mode) return;
    if(!$('.blink').length > 0) return;

    var select_variation = $('.blink').parent().parent().parent().parent().parent().parent().find('.js_variation_cell');

    var v = parseInt(select_variation.val().trim());
    var text = '<small>'+
        '<strong>'+li.attr('data-text').trim()+'</strong>&nbsp;&nbsp;' +
        '<i class="badge badge-danger" style="margin-bottom: 3px!important;"><small>'+select_variation.find('option:selected').text().trim()+'</small></i>'+
        '</small>';

    var new_rubrique = '<span class="operateur operande label label-default" data-type="'+li.attr('data-type')+'" data-id="'+ li.attr('data-id') +'" data-variation="'+v+'" style="padding: 5px!important;">'+ text +'</span>';
    $(new_rubrique).insertBefore($('.blink'));
}