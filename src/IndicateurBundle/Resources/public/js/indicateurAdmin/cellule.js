/**
 * Created by SITRAKA on 09/11/2016.
 */

//var ctrl_mode = false;
$(document).click(function(event) {
    var element = $(event.target);

    /**
     * mode control
     */
    /*if(element.closest('.js_indicateur_sortable').length > 0)
    {
        var div_container = element.closest('.js_indicateur_sortable'),
            btn_mode = div_container.find('.js_mode_control');
        if(element.closest('.js_mode_control').length > 0 || element.hasClass('js_mode_control'))
        {
            if(btn_mode.hasClass('btn-primary'))
            {
                btn_mode.removeClass('btn-primary').addClass('btn-default');
                div_container.find('.js_ctrl_edited').removeClass('js_ctrl_edited');
            }
            else
            {
                btn_mode.removeClass('btn-default').addClass('btn-primary');
                div_container.find('.js_cellule_edited').removeClass('js_cellule_edited');
                div_container.find('.js_format_edited').removeClass('js_format_edited');
                div_container.find('.js_row_col_edited').removeClass('js_row_col_edited');
                div_container.find('.js_border_left_right').removeClass('js_border_left_right');
                $('#js_blink_formule').remove();
            }
        }

        ctrl_mode = btn_mode.hasClass('btn-primary');
        div_container.find('.js_cl_toll').addClass('hidden').each(function(){
            if(ctrl_mode && $(this).hasClass('js_show_ctrl') || !ctrl_mode && !$(this).hasClass('js_show_ctrl'))
            {
                $(this).removeClass('hidden');
            }
        });
    }
    if(element.closest('.js_cell_indicateur').length > 0 || element.hasClass('js_cell_indicateur') ||
        element.closest('.js_show_ctrl').length > 0 || element.hasClass('js_show_ctrl'))
    {
        if(element.closest('.js_cell_indicateur').length > 0 || element.hasClass('js_cell_indicateur'))
        {
            var td_mode = (element.hasClass('js_cell_indicateur')) ? element : element.closest('.js_cell_indicateur');
            select_ctrl_cell(td_mode);
        }
    }
    else $('.js_ctrl_edited').removeClass('js_ctrl_edited');*/

    if($('#js_form_edit_indicateur_item').length > 0) return;

    //hide pop over
    if (element.data('toggle') !== 'popover' && element.parent().data('toggle') !== 'popover' && element.parent().parent().data('toggle') !== 'popover' && element.parents('.popover.in').length === 0) {
        $('[data-toggle="popover"]').popover('hide');
    }

    //format colonne
    if( element.hasClass('js_colonne_format') || element.parent().hasClass('js_colonne_format') || element.parent().parent().hasClass('js_colonne_format')) return;

    if(element.hasClass('js_format_edit'))
    {
        var ret = setColEdited(element);
        if(ret) return;
    }
    else if( !(element.hasClass('js_format_delete') || element.parent().hasClass('js_format_delete') ||
        element.hasClass('js_add_row_col') || element.parent().hasClass('js_add_row_col') ||
        element.hasClass('js_format_format') || element.parent().hasClass('js_format_format') || element.parent().parent().hasClass('js_format_format') ||
        element.hasClass('js_format_decimal')) )
    {
        setColEdited();
    }

    if(!(element.hasClass('js_edit_row_col') || element.parent().hasClass('js_edit_row_col')))
    {
        $('.js_cells_edited')
            .removeClass('js_cells_edited')
            .removeClass('js_border_left_right')
            .removeClass('js_border_top_bottom');
    }

    if(//font button
        element.hasClass('js_font') || element.parent().hasClass('js_font') ||
        //choose font
        element.hasClass('js_font_family') || element.parent().hasClass('js_font_family') ||
        //font weight
        element.hasClass('js_font_bold') || element.parent().hasClass('js_font_bold') ||
        //font italic
        element.hasClass('js_font_italic') || element.parent().hasClass('js_font_italic') ||
        //border
        element.hasClass('js_border') || element.parent().hasClass('js_border') || element.parent().parent().hasClass('js_border') ||
        //color texte
        element.hasClass('js_td_color_picker') ||
        //align
        element.hasClass('js_align') || element.parent().hasClass('js_align') ||
        //add rubrique
        element.hasClass('js_rubrique_item') || element.parent().hasClass('js_rubrique_item') ||
        //add rubrique with variation
        element.hasClass('js_rubrique_variation') || element.parent().hasClass('js_rubrique_variation') || element.parent().parent().parent().hasClass('js_rubrique_variation') ||
        //choose type rubrique
        element.hasClass('js_rubrique_sel') || element.parent().hasClass('js_rubrique_sel') ||
        //indent text
        element.hasClass('js_indent') || element.parent().hasClass('js_indent') ||
        //style
        element.hasClass('js_cl_edit_style') || element.parent().hasClass('js_cl_edit_style')
    ) return;

    //save cell
    if($('.blink').length > 0) save_cell();

    //remove old blink
    if($('.blink').parent().text().trim() == '|') $('.blink').parent().html('<span>&nbsp;</span>');
    else $('.blink').remove();
    if(!cntrlIsPressed && !($('.js_cl_container_styles').length > 0)) $('.js_cellule_edited').removeClass('js_cellule_edited');
    $('.js_summers').addClass('hidden');

    var span_element = null;

    if(element.hasClass('js_cell_indicateur'))
    {
        element.addClass('js_cellule_edited');
    }
    else if(element.parent().hasClass('js_cell_indicateur'))
    {
        element.parent().addClass('js_cellule_edited');
        span_element = element;
    }
    else if(element.parent().parent().hasClass('js_cell_indicateur'))
    {
        element.parent().parent().addClass('js_cellule_edited');
        span_element = element.parent();
    }
    else if(element.parent().parent().parent().hasClass('js_cell_indicateur'))
    {
        element.parent().parent().parent().addClass('js_cellule_edited');
        span_element = element.parent().parent();
    }
    else if(element.parent().parent().parent().parent().hasClass('js_cell_indicateur'))
    {
        element.parent().parent().parent().parent().addClass('js_cellule_edited');
        span_element = element.parent().parent().parent();
    }
    else return;

    //old value
    var cell_edited = $('.js_cellule_edited');

    $('.js_cell_indicateur').removeClass('js_border_left_right').removeClass('js_border_top_bottom');
    $('.js_format_edited').addClass('js_format_edited');

    cell_old_html = cell_edited.html().trim();
    cell_old_font_family = cell_edited.css('font-family').trim();
    cell_old_font_weight = cell_edited.css('font-weight').trim();
    cell_old_italic = cell_edited.css('font-style').trim();
    cell_old_text_align = cell_edited.css('text-align').trim();
    cell_old_indent = cell_edited.css('text-indent').trim();
    cell_old_color = cell_edited.css('color').trim();
    cell_old_bg = cell_edited.css('background-color').trim();
    cell_edited.parent().parent().parent().parent().parent().find('.js_summers').removeClass('hidden');
    if(cell_old_font_weight == 'bold') cell_edited.parent().parent().parent().parent().parent().find('.js_summers').find('.js_font_bold').addClass('active');
    else cell_edited.parent().parent().parent().parent().parent().find('.js_summers').find('.js_font_bold').removeClass('active');
    if(cell_old_italic == 'italic') cell_edited.parent().parent().parent().parent().parent().find('.js_summers').find('.js_font_italic').addClass('active');
    else cell_edited.parent().parent().parent().parent().parent().find('.js_summers').find('.js_font_italic').removeClass('active');

    cell_old_border = '';
    //bottom double
    if(cell_edited.hasClass('cell_border_4')) cell_old_border += '1';
    else cell_old_border += '0';
    //left double
    if(cell_edited.hasClass('cell_border_5')) cell_old_border += '1';
    else cell_old_border += '0';
    //top double
    if(cell_edited.hasClass('cell_border_6')) cell_old_border += '1';
    else cell_old_border += '0';
    //right double
    if(cell_edited.hasClass('cell_border_7')) cell_old_border += '1';
    else cell_old_border += '0';
    //bottom
    if(cell_edited.hasClass('cell_border_0')) cell_old_border += '1';
    else cell_old_border += '0';
    //left
    if(cell_edited.hasClass('cell_border_1')) cell_old_border += '1';
    else cell_old_border += '0';
    //top
    if(cell_edited.hasClass('cell_border_2')) cell_old_border += '1';
    else cell_old_border += '0';
    //right
    if(cell_edited.hasClass('cell_border_3')) cell_old_border += '1';
    else cell_old_border += '0';

    var blink = blink = '<span class="blink" id="js_blink_formule">|</span>';

    if(element.hasClass('js_cell_indicateur')) $(blink).insertAfter(element.find('span:last'));
    else $(blink).insertAfter(span_element);

    //change type rubrique
    var type = 2;
    cell_edited.find('.operande').each(function(){
        type = parseInt($(this).attr('data-type'));
        return;
    });
    cell_edited.parent().parent().parent().parent().parent().find('.js_rubrique_sel_'+type).click();
});

/**
 * before unload page
 */
/*$(window).unload(function(){
    $('.error-formule').each(function(){
        $(this).click();
        $(this).html('<span class="blink" id="js_blink_formule">|</span>');
        $('#dossier').click();
    });
});*/

var cntrlIsPressed = false;
$(document).keyup(function(){
    cntrlIsPressed = false;
});
/**
 * keydown number,operateur
 */
$(window).keydown(function(e) {
    if($('.js_cellule_edited').length > 1) return;
    var key_spec = ['ESCAPE','CONTROL','SHIFT','NUMLOCK','CAPSLOCK','CONTEXTMENU','META','INSERT','HOME','PAGEUP','END','PAGEDOWN','ALT','ALTGRAPH'];

    for(var i = 1; i <= 12; i++) key_spec.push('F'+i);
    var key = e.key.toString().toUpperCase();

    if(key == 'CONTROL') cntrlIsPressed = true;
    //if(ctrl_mode) return;

    if(!$('.js_cellule_edited').length > 0 || key_spec.in_array(key))
    {
        return;
    }
    e.preventDefault();
    if(key == 'ARROWLEFT')
    {
        span = $('#js_blink_formule').prev('.operateur');
        move_blink(span,'ib');
    }
    else if(key == 'ARROWRIGHT')
    {
        span = $('#js_blink_formule').next('.operateur');
        move_blink(span, 'ia');
    }
    else if(key == 'DELETE') move_blink(null,'da');
    else if(key == 'BACKSPACE') move_blink(null,'db');
    else if(key == 'TAB')
    {
        if($('.blink').parent().is(':last-child')) $('.blink').parent().parent().next('tr').children('td:first').click();
        else $('.blink').parent().next().click();
    }
    else $("<span class='operateur'>" + e.key + "</span>").insertBefore($('.blink'));
});

$(document).on('click','.js_font_family',function(){
    change_font_family($(this));
});

$(document).on('change','input[name="context-menu-input-radio_format"]',function() {
    change_format_colonne($(this));
});
$(document).on('change','input[name="context-menu-input-radio_unite"]',function() {
    change_format_colonne($(this));
});

$(document).on('click','.js_font_bold',function(){
    change_font_weight($(this));
});

$(document).on('click','.js_font_italic',function(){
    change_italic($(this));
});

$(document).on('click','.js_border',function(){
    change_border($(this));
});

$(document).on('click','.js_td_color_picker',function(){
    change_color($(this));
});

$(document).on('click','.js_align',function(){
    change_align($(this));
});

$(document).on('click','.js_indent',function(){
    change_indent($(this));
});

$(document).on('mouseover','.js_rubrique_item',function(){
    change_show_variation_rubrique($(this));
});

$(document).on('click','.js_rubrique_item',function(){
    add_rubrique_in_cell($(this));
});

$(document).on('click','.js_rubrique_sel',function(){
    change_rubrique_type($(this));
});

$(document).on('click','.js_edit_row_col',function(){
    change_row_col_indicateur($(this));
});

$(document).on('click','.js_add_row_col',function(){
    save_row_col_indicateur($(this));
});

$(document).on('click','.js_format_delete',function(){
    save_row_col_indicateur($(this));
});

$(document).on('click','.js_format_format',function(){
    change_format_colonne($(this));
});

$(document).on('dblclick','.js_format_edit',function(){
    if(parseInt($(this).attr('data-col')) != -1 || $(this).closest('.js_indicateur_sortable').hasClass('js_etat')) return;
    change_table_to_graphe($(this));
});

function change_table_to_graphe(td)
{
    //if(ctrl_mode) return;
    if(td.hasClass('js_td_to_chart')) td.removeClass('js_td_to_chart');
    else td.addClass('js_td_to_chart');

    var row = parseInt(td.attr('data-row')),
        indicateur = td.closest('div.js_indicateur_sortable').attr('data-id'),
        val = (td.hasClass('js_td_to_chart')) ? 1 : 0,
        lien = Routing.generate('ind_td_to_table');
    $.ajax({
        data: { indicateur:indicateur, row:row, val:val },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE AVEC SUCCES');
        }
    });
}

function change_format_colonne(btn)
{
    //if(ctrl_mode) return;
    var format,
        decimal,
        indicateur = btn.closest('.js_indicateur_sortable').attr('data-id'),
        col = parseInt($('.js_format_edited').attr('data-col')),
        is_etat = (btn.closest('.js_indicateur_sortable').hasClass('js_etat')) ? 1 : 0;
    if(btn.hasClass('js_format_decimal'))
    {
        if(btn.hasClass('active')) btn.removeClass('active');
        else btn.addClass('active');
        format = parseInt(btn.parent().find('.js_format_contener').find('li.active').attr('data-type'));
        decimal = btn.hasClass('active') ? 1 : 0;
    }
    else
    {
        if(btn.hasClass('active')) return;
        btn.parent().find('.js_format_format').removeClass('active');
        btn.addClass('active');
        format = parseInt(btn.attr('data-type'));
        decimal = btn.parent().parent().parent().find('.js_format_decimal').hasClass('active') ? 1 : 0;
    }

    var lien = Routing.generate('ind_col_format');
    $.ajax({
        data: { indicateur:indicateur, col:col, format:format, decimal:decimal , is_etat:is_etat },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('.js_format_edited').attr('data-format',format).attr('data-decimal',decimal);
            show_info('SUCCES','MODIFICATION BIEN ENREGISTREE AVEC SUCCES');
        }
    });
}

function save_row_col_indicateur(btn)
{
    //if(ctrl_mode) return;
    var action = parseInt(btn.attr('data-action'));

    var can_delete = true;
    var row_deleted = -1;
    var col_delete = -1;
    if(action == 5)
    {
        $('.js_row_col_edited').each(function(){
            if($(this).text().trim() != '')
            {
                can_delete = false;
                return can_delete;
            }
        });
        if(!$('.js_format_edited').length > 0)
        {
            show_info('NOTICE','SELECTIONNER LA LIGNE OU LA COLONNE A SUPPRIMEE','warning');
            return;
        }
    }
    if(!can_delete)
    {
        show_info('ERREUR','IL EXISTE UN CELLULE NON VIDE','error');
        return;
    }

    row_deleted = parseInt($('.js_format_edited').attr('data-row'));
    col_delete = parseInt($('.js_format_edited').attr('data-col'));

    if(isNaN(row_deleted)) row_deleted = -10;
    if(isNaN(col_delete)) col_delete = -10;

    $('.js_indicateur_edited').removeClass('js_indicateur_edited');

    var div_indicateur = btn.closest('.js_indicateur_sortable'),
        indicateur = div_indicateur.addClass('js_indicateur_edited').attr('data-id'),
        is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
        lien = Routing.generate('ind_row_col_edit');

    $.ajax({
        data: { indicateur:indicateur, action:action, row_deleted:row_deleted, col_delete:col_delete, is_etat:is_etat },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);

            if(parseInt(data) == 1)
            {
                var cell_header_edited = $('.js_indicateur_edited').find('.js_format_edited'),
                    row_header_edited = parseInt(cell_header_edited.attr('data-row')),
                    col_header_edited = parseInt(cell_header_edited.attr('data-col')),
                    new_cell = null,
                    hasError = false;

                //test changement formule
                if(!(isNaN(row_header_edited) && isNaN(col_header_edited)))
                {
                    $('.js_indicateur_edited').find('tbody').find('td').each(function(){
                        var current_row = parseInt($(this).attr('data-row')),
                            current_col = parseInt($(this).attr('data-col')),
                            current_text = $(this).text().trim();

                        if(current_text.indexOf('[') != -1 && current_text.indexOf(']') != -1)
                        {
                            if(!isNaN(col_header_edited) && col_header_edited <= current_col && col_header_edited != -1)
                            {
                                $(this).addClass('error-formule');
                                hasError = true;
                            }
                            if(!isNaN(row_header_edited) && row_header_edited <= current_row && row_header_edited != -1)
                            {
                                $(this).addClass('error-formule');
                                hasError = true;
                            }
                        }
                    });
                }

                var index_td = 0;
                //delete
                if(action == 5)
                {
                    cell_header_edited = $('.js_indicateur_edited').find('.js_format_edited');
                    row_header_edited = parseInt(cell_header_edited.attr('data-row'));
                    col_header_edited = parseInt(cell_header_edited.attr('data-col'));

                    $('.js_indicateur_edited').find('table.table-resizable').find('tbody').find('td').each(function(){
                        var row_current = parseInt($(this).attr('data-row')),
                            col_current = parseInt($(this).attr('data-col'));

                        if(row_header_edited != -1 && row_header_edited < row_current)
                        {
                            $(this).attr('data-row',row_current - 1);
                            if(col_current == -1)
                            {
                                $(this).text(parseInt($(this).text().trim()) - 1);
                            }
                        }
                        else if(col_header_edited != -1 && col_header_edited < col_current)
                        {
                            $(this).attr('data-col',col_current - 1);
                            if(row_current == -1) $(this).text(String.fromCharCode($(this).text().trim().charCodeAt(0) -1));
                        }
                        else if(row_header_edited != -1 && row_header_edited == row_current) $(this).remove();
                        else if(col_header_edited != -1 && col_header_edited == col_current) $(this).remove();
                    });
                }
                //add row
                else if(action == 4)
                {
                    if(!($('.js_format_edited').length > 0))
                    {
                        var last_row = $('.js_indicateur_edited').find('table.table-resizable').find('tbody').find('tr:last-child');
                        var new_row = '<tr class="no-padding">';
                        last_row.find('td').each(function(){
                            if(index_td == 0)
                            {
                                new_row += '<td class="gray-bg text-center js_format_edit" data-row="'+(parseInt($(this).attr('data-row')) + 1)+
                                    '" data-col="'+parseInt($(this).attr('data-col'))+'">'+(parseInt($(this).text()) + 1)+'</td>';
                            }
                            else
                            {
                                new_row += '<td data-row="'+ (parseInt($(this).attr('data-row')) + 1) +'" data-col="'+parseInt($(this).attr('data-col'))+'" class="padding-6 js_cell_indicateur" style="font-family:;font-weight: normal;font-style:normal;text-indent: 0px;">'+
                                    '<span>&nbsp;</span>'+
                                    '</td>';
                            }
                            index_td++;
                        });
                        new_row += '</tr>';
                        $(new_row).insertAfter(last_row);
                    }
                    else
                    {
                        cell_header_edited = $('.js_indicateur_edited').find('.js_format_edited');
                        row_header_edited = parseInt(cell_header_edited.attr('data-row'));
                        col_header_edited = parseInt(cell_header_edited.attr('data-col'));
                        var new_tr = '<tr>';
                        $('.js_indicateur_edited').find('table.table-resizable').find('tbody').find('td').each(function(){
                            var row_current = parseInt($(this).attr('data-row')),
                                col_current = parseInt($(this).attr('data-col'));

                            if($(this).hasClass('js_row_col_edited') || $(this).hasClass('js_format_edited'))
                            {
                                if($(this).hasClass('js_format_edited'))
                                {
                                    new_tr += '<td class="gray-bg text-center js_format_edit" data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+parseInt($(this).attr('data-col'))+'">'+$(this).text().trim()+'</td>';
                                }
                                else
                                {
                                    new_tr += '<td data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+parseInt($(this).attr('data-col'))+'" class="padding-6 js_cell_indicateur" style="font-family:;font-weight: normal;font-style:normal;text-indent: 0;">'+'<span>&nbsp;</span>'+ '</td>';
                                }
                            }

                            if(row_header_edited != -1 && row_header_edited <= row_current)
                            {
                                $(this).attr('data-row',row_current + 1);
                                if(col_current == -1)
                                {
                                    $(this).text(parseInt($(this).text().trim()) + 1);
                                }
                            }
                        });
                        $(new_tr).insertBefore(cell_header_edited.closest('tr'));
                    }
                }
                //add col
                else if(action == 6)
                {
                    if(!($('.js_format_edited').length > 0))
                    {
                        $('.js_indicateur_edited').find('table.table-resizable').find('tbody').find('td:last-child').each(function(){
                            if(index_td == 0)
                            {
                                new_cell = '<td class="gray-bg text-center js_format_edit" data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+(parseInt($(this).attr('data-col')) + 1)+'">'+
                                    String.fromCharCode($(this).text().trim().charCodeAt(0) + 1).toString()+
                                    '</td>';
                            }
                            else
                            {
                                new_cell = '<td data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+(parseInt($(this).attr('data-col')) + 1)+'" class="padding-6 js_cell_indicateur" style="font-family:;font-weight: normal;font-style:normal;text-indent: 0px;">'+
                                    '<span>&nbsp;</span>'+
                                    '</td>';
                            }
                            $(new_cell).insertAfter($(this));
                            index_td++;
                        });
                    }
                    else
                    {
                        cell_header_edited = $('.js_indicateur_edited').find('.js_format_edited');
                        row_header_edited = parseInt(cell_header_edited.attr('data-row'));
                        col_header_edited = parseInt(cell_header_edited.attr('data-col'));

                        $('.js_indicateur_edited').find('table.table-resizable').find('tbody').find('td').each(function(){
                            var row_current = parseInt($(this).attr('data-row')),
                                col_current = parseInt($(this).attr('data-col'));

                            if($(this).hasClass('js_row_col_edited') || $(this).hasClass('js_format_edited'))
                            {
                                if($(this).hasClass('js_format_edited'))
                                {
                                    new_cell = '<td class="gray-bg text-center js_format_edit" data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+parseInt($(this).attr('data-col'))+'">'+$(this).text().trim()+'</td>';
                                }
                                else
                                {
                                    new_cell = '<td data-row="'+parseInt($(this).attr('data-row'))+'" data-col="'+parseInt($(this).attr('data-col'))+'" class="padding-6 js_cell_indicateur" style="font-family:;font-weight: normal;font-style:normal;text-indent: 0;">'+'<span>&nbsp;</span>'+ '</td>';
                                }
                                $(new_cell).insertBefore($(this));
                            }
                            if(col_header_edited != -1 && col_header_edited <= col_current)
                            {
                                $(this).attr('data-col',col_current + 1);
                                if(row_current == -1) $(this).text(String.fromCharCode($(this).text().trim().charCodeAt(0) +1));
                            }
                        });
                    }
                }
                show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
                if (hasError) show_info('NOTICE','REVERIFIER LES FORMULES CADRES EN ROUGE','error');
            }
            else
            {
                show_info('ERREUR','UNE ERREUR EST SURVENUE PENDANT LA MODIFICATION','error');
            }
        }
    });
}

function setColEdited(th)
{
    //if(ctrl_mode) return;
    $('.js_format_edited').removeClass('js_format_edited');

    $('.js_cell_indicateur')
        .removeClass('js_row_col_edited')
        .removeClass('js_border_left_right')
        .removeClass('js_border_top_bottom');

    $('.js_format_col').addClass('hidden');

    if(!(typeof th === 'undefined'))
    {
        var row_selected = parseInt(th.attr('data-row')),
            col_selected = parseInt(th.attr('data-col'));

        th.addClass('js_format_edited');

        if(row_selected == -1 && col_selected == -1)
        {
            $('.js_cellule_edited').removeClass('js_cellule_edited');
            th.closest('table').find('td').each(function(){
                if(!$(this).hasClass('js_format_edit'))
                {
                    $(this).addClass('js_cellule_edited');
                }
            });
            return true;
        }
        else
        {
            th.parent().parent().parent().find('.js_cell_indicateur').each(function(){
                var row = parseInt($(this).attr('data-row')),
                    col = parseInt($(this).attr('data-col'));
                if(row == row_selected) $(this).addClass('js_border_top_bottom').addClass('js_row_col_edited');
                if(col == col_selected) $(this).addClass('js_border_left_right').addClass('js_row_col_edited');
            });

            if(col_selected != -1)
            {
                //format column
                var format_column = parseInt(th.attr('data-format')),
                    format_decimal = parseInt(th.attr('data-decimal')),
                    ibox_indicateur = th.closest('.js_indicateur_sortable');
                ibox_indicateur.find('.js_format_format').each(function(){
                    if(parseInt($(this).attr('data-type')) == format_column) $(this).addClass('active');
                    else $(this).removeClass('active');
                });
                if(format_decimal == 1) ibox_indicateur.find('.js_format_decimal').addClass('active');
                else ibox_indicateur.find('.js_format_decimal').removeClass('active');

                $('.js_format_col').removeClass('hidden');
            }
        }
    }

    return false;
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

function save_cell()
{
    //if(ctrl_mode) return;
    var td = $('.blink').parent();
    $('.blink').remove();

    if($('.js_cellule_edited').length > 1) return;

    var cell_new_border = '';
    //bottom double
    if(td.hasClass('cell_border_4')) cell_new_border += '1';
    else cell_new_border += '0';
    //left double
    if(td.hasClass('cell_border_5')) cell_new_border += '1';
    else cell_new_border += '0';
    //top double
    if(td.hasClass('cell_border_6')) cell_new_border += '1';
    else cell_new_border += '0';
    //right double
    if(td.hasClass('cell_border_7')) cell_new_border += '1';
    else cell_new_border += '0';

    //bottom
    if(td.hasClass('cell_border_0')) cell_new_border += '1';
    else cell_new_border += '0';
    //left
    if(td.hasClass('cell_border_1')) cell_new_border += '1';
    else cell_new_border += '0';
    //top
    if(td.hasClass('cell_border_2')) cell_new_border += '1';
    else cell_new_border += '0';
    //right
    if(td.hasClass('cell_border_3')) cell_new_border += '1';
    else cell_new_border += '0';

    if(td.html().trim() == '') td.html('&nbsp;');
    if(//value
        td.html().trim() == cell_old_html &&
        //font family
        td.css('font-family').trim() == cell_old_font_family &&
        //font weight
        td.css('font-weight').trim() == cell_old_font_weight &&
        //bold
        td.css('font-style').trim() == cell_old_italic &&
        //text align
        td.css('text-align').trim() == cell_old_text_align &&
        //text indent
        td.css('text-indent').trim() == cell_old_indent &&
        //border
        cell_old_border == cell_new_border &&
        //color
        td.css('color').trim() == cell_old_color &&
        //bg color
        td.css('background-color').trim() == cell_old_bg
        ) return;

    //parametres
    var div_indicateur = td.closest('.js_indicateur_sortable'),
        indicateur = div_indicateur.attr('data-id'),
        is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
        row_ = parseInt(td.attr('data-row')),
        col_ = parseInt(td.attr('data-col')),
        formule = '',
        operandes = [],
        variations = [];
    td.find('.operateur').each(function(){
        if($(this).hasClass('operande'))
        {
            formule += '#';
            operandes.push($(this).attr('data-id'));
            variations.push($(this).attr('data-variation'));
        }
        else formule += $(this).text();
    });

    //detection boucle
    if(operandes.length == 0 && formule.indexOf('[') != -1 && formule.indexOf(']') != -1)
    {
        formule = formule.sansAccent().toUpperCase().replace(/ /g,'');
        var cell = '',
            formule_spliter = formule.split(''),
            row_td = td.closest('tr').find('td.js_format_edit').text().trim(),
            col_td = td.closest('tbody').find('tr:first td:nth-child('+(td.index() + 1)+')').text().trim();

        for(var i = 0;i < formule_spliter.length; i++)
        {
            var c = formule_spliter[i];
            if(c == '[') cell = '';
            else if(c == ']')
            {
                var col_a_tester = '',
                    row_a_tester = '',
                    row_col_spliter = cell.split('');

                for(var j = 0; j < row_col_spliter.length; j++)
                {
                    if(isNaN(parseInt(row_col_spliter[j]))) col_a_tester += row_col_spliter[j];
                    else row_a_tester += ''+row_col_spliter[j];
                }

                col_a_tester = col_a_tester.replace(/ /g,'');
                row_a_tester = row_a_tester.replace(/ /g,'');

                if(col_a_tester == col_td && row_a_tester == row_td)
                {
                    show_info('ERREUR','UNE BOUCLE DETECTER DANS LA FORMULE','error');
                    td.addClass('error-formule');
                    return;
                }
            }
            else
            {
                cell += ''+c;
            }
        }
    }

    td.removeClass('error-formule');

    var lien = Routing.generate('ind_cell_edit');
    $.ajax({
        data: { indicateur:indicateur , formule:formule , row:row_ , col:col_ ,
                operandes:JSON.stringify(operandes) , variations:JSON.stringify(variations),
                cell_font_family:td.css('font-family').trim() , cell_font_weight:td.css('font-weight') ,
                cell_italic:td.css('font-style'), cell_text_align:td.css('text-align') ,
                cell_indent:td.css('text-indent'), cell_border:cell_new_border,
                cell_color:td.css('color'), cell_bg:td.css('background-color'),
                is_etat:is_etat },
        type: 'POST',
        url: lien,
        //async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('SUCCES','MODIFICATION ENREGISTREE AVEC SUCCES');
        },
        error: function () {
            verrou_fenetre(false);
            show_info('ERREUR','UNE ERREUR C EST PRODUITE PENDANT LA MODIFICATION','error');
        }
    });

    var blink = blink = '<span class="blink" id="js_blink_formule">|</span>';
    if(td.html().trim() == '&nbsp;') td.html(blink);
    else $(blink).insertAfter(td.find('span:last'));
}

function change_font_family(li)
{
    //if(ctrl_mode) return;
    if($('.js_cellule_edited').length > 0)
    {
        $('.js_cellule_edited').css('font-family',li.attr('data-class'));
    }
}

function change_font_weight(li)
{
    //if(ctrl_mode) return;
    if($('.js_cellule_edited').length > 0)
    {
        if(li.hasClass('active'))
        {
            li.removeClass('active');
            $('.js_cellule_edited').css('font-weight','');
        }
        else
        {
            li.addClass('active');
            $('.js_cellule_edited').css('font-weight','bold');
        }
    }
}

function change_italic(li)
{
    //if(ctrl_mode) return;
    if($('.js_cellule_edited').length > 0)
    {
        if(li.hasClass('active'))
        {
            li.removeClass('active');
            $('.js_cellule_edited').css('font-style','normal');
        }
        else
        {
            li.addClass('active');
            $('.js_cellule_edited').css('font-style','italic');
        }
    }
}

function change_border(li)
{
    //if(ctrl_mode) return;
    var cell_edited = $('.js_cellule_edited');
    if(cell_edited.length > 0)
    {
        var border = parseInt(li.attr('data-border'));
        var class_a_teste = 'cell_border_'+border;
        if(cell_edited.hasClass(class_a_teste)) cell_edited.removeClass(class_a_teste);
        else cell_edited.addClass(class_a_teste);
    }
}

function change_color(td)
{
    //if(ctrl_mode) return;
    var type = parseInt(td.parent().parent().parent().parent().attr('data-type'));
    if(type == 0)
    {
        $('.js_cellule_edited').css('color',td.css('background-color'));
    }
    else if (type == 1)
    {
        $('.js_cellule_edited').css('background-color',td.css('background-color'));
    }
}

function change_align(li)
{
    $('.js_cellule_edited').css('text-align',li.attr('data-align'));
}

function change_indent(li)
{
    if($('.js_cellule_edited').length > 0)
    {
        var old_indent = parseInt($('.js_cellule_edited').css('text-indent'));
        var variation = parseInt(li.attr('data-value'));
        var new_indent = old_indent + variation;
        if(new_indent >= 0 && new_indent <= Math.abs(variation * 10)) $('.js_cellule_edited').css('text-indent',new_indent);
    }
}

function change_row_col_indicateur(li)
{
    //if(ctrl_mode) return;
    var action = parseInt(li.attr('data-class'));

    var can_delete = true;
    var row_deleted = -1;
    var col_delete = -1;
    if(action == 5 || action == 7)
    {
        if(action == 5)
        {
            $('.js_row_col_edited').each(function(){
                if($(this).hasClass('js_border_top_bottom'))
                {
                    row_deleted = parseInt($(this).attr('data-row'));
                    if($(this).text().trim() != '')
                    {
                        can_delete = false;
                        return false;
                    }
                }
                else
                {
                    $(this).removeClass('js_cells_edited').removeClass('js_border_left_right');
                }
            });
        }
        else
        {
            $('.js_row_col_edited').each(function(){
                if($(this).hasClass('js_border_left_right'))
                {
                    col_delete = parseInt($(this).attr('data-col'));
                    if($(this).text().trim() != '')
                    {
                        can_delete = false;
                        return false;
                    }
                }
                else
                {
                    $(this).removeClass('js_cells_edited').removeClass('js_border_top_bottom');
                }
            });
        }
    }
    if(!can_delete)
    {
        show_info('ERREUR','IL EXISTE UN CELLULE NON VIDE','error');
        return;
    }

    $('.js_indicateur_edited').removeClass('js_indicateur_edited');
    var indicateur = $('#js_div_accordion_pack').find('.js_cells_edited').parent().parent().parent().parent().parent().addClass('js_indicateur_edited').attr('data-id');

    var lien = Routing.generate('ind_row_col_edit');
    $.ajax({
        data: { indicateur:indicateur, action:action, row_deleted:row_deleted, col_delete:col_delete },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if(parseInt(data) == 1)
            {
                if(action == 5 || action == 7)
                {
                    $('.js_indicateur_edited').find('.js_cell_indicateur').each(function(){
                        var row_item = parseInt($(this).attr('data-row'));
                        var col_item = parseInt($(this).attr('data-col'));
                        if(action == 5 && row_item > row_deleted) $(this).attr('data-row',row_item - 1);
                        else if(action == 7 && col_item > col_delete) $(this).attr('data-col',col_item - 1);
                    });
                    $('.js_cells_edited').remove();
                }
                else if(action == 4)
                {
                    var last_row = $('.js_indicateur_edited').find('tbody').find('tr:last-child');
                    var new_row = '<tr class="no-padding">';
                    last_row.find('td').each(function(){
                        new_row += '<td data-row="'+ (parseInt($(this).attr('data-row')) + 1) +'" data-col="'+parseInt($(this).attr('data-col'))+'" class="padding-2 js_cell_indicateur"'+
                            'style="font-family:";font-weight:normal;font-style:normal;text-align:left;text-indent:0px;">' +
                                    '<span>&nbsp;</span></td>';
                    });
                    new_row += '</tr>';
                    $(new_row).insertAfter(last_row);
                }
                else if(action == 6)
                {
                    $('.js_indicateur_edited').find('tbody').find('td:last-child').each(function(){
                        var new_cell = '<td data-row="'+ parseInt($(this).attr('data-row')) +'" data-col="'+(parseInt($(this).attr('data-col')) + 1)+'" class="padding-2 js_cell_indicateur"'+
                            'style="font-family:";font-weight:normal;font-style:normal;text-align:left;text-indent:0px;">' +
                            '<span>&nbsp;</span></td>';
                        $(new_cell).insertAfter($(this));
                    });
                }
            }
        }
    });
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
        $('table.table-resizable tbody tr td.js_cell_indicateur').contextMenu('destroy');
        $.contextMenu({
            selector: 'table.table-resizable tbody tr td.js_cell_indicateur',
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
    li.addClass('active rubrique_sel').parent().parent().find('.dropdown-toggle').text(li.text());
}

function change_show_variation_rubrique(li)
{
    /*$('.js_menu_li_select').addClass('hidden');
    li.find('.js_menu_li_select').removeClass('hidden');*/
}