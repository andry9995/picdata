/**
 * Created by SITRAKA on 24/04/2017.
 */
$(document).on('click','.js_cl_edit_style',function(){
    if(!($('.js_cellule_edited').length > 0)) return;
    show_edit_styles($(this));
});

$(document).on('click','.js_validate_styles',function(){
    validate_styles();
});

function show_edit_styles(btn)
{
    $('.indicateur_style_edited').removeClass('indicateur_style_edited');
    var lien = Routing.generate('etat_styles'),
        div_indicateur = btn.closest('.js_indicateur_sortable'),
        indicateur = div_indicateur.addClass('indicateur_style_edited').attr('data-id'),
        is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
        apercu = $('.js_cellule_edited').first().removeClass('js_cellule_edited'),
        st_font = apercu.css('font-family'),
        st_style = apercu.css('font-style'),
        st_weight = apercu.css('font-weight'),
        st_size = apercu.css('font-size'),
        st_color = apercu.css('color'),
        st_bg = apercu.css('background-color'),
        st_align = apercu.css('text-align'),
        st_bt = apercu.css('border-top'),
        st_bl = apercu.css('border-left'),
        st_br = apercu.css('border-right'),
        st_bb = apercu.css('border-bottom'),
        st_decimal = parseInt(apercu.attr('data-decimal')),
        st_format = parseInt(apercu.attr('data-format'));

    apercu.addClass('js_cellule_edited');

    /*$('.js_cellule_edited').each(function(){
        st_font = ($(this).css('font-family') == st_font) ? st_font : 'd';
        st_style = ($(this).css('font-style') == st_style) ? st_style : 'd';
        st_weight = ($(this).css('font-weight') == st_weight) ? st_weight : 'd';
        st_size = ($(this).css('font-size') == st_size) ? parseInt(st_size) : 'd';
        st_color = ($(this).css('color') == st_color) ? st_color : 'd';
        st_bg = ($(this).css('background-color') == st_bg) ? st_bg : 'd';
        st_align = ($(this).css('text-align') == st_align) ? st_align : 'd';
        $(this).removeClass('js_cellule_edited');
        st_bt = ($(this).css('border-top') == st_bt) ? st_bt : 'd' ;
        st_bl = ($(this).css('border-left') == st_bl) ? st_bl : 'd';
        st_br = ($(this).css('border-right') == st_br) ? st_br : 'd';
        st_bb = ($(this).css('border-bottom') == st_bb) ? st_bb : 'd';
        $(this).addClass('js_cellule_edited');
        st_decimal = (parseInt($(this).attr('data-decimal')) == st_decimal) ? st_decimal : -1;
        st_format = (parseInt($(this).attr('data-format')) == st_format) ? st_format : -1;
    });*/

    if(st_font == '"open sans", "Helvetica Neue", Helvetica, Arial, sans-serif'.trim() || st_font == 'd') st_font = '';
    if(st_size == 'd') st_size = 13;

    if(parseInt(st_bt.substring(0,1)) == 0) st_bt = 'NONE';
    if(parseInt(st_bl.substring(0,1)) == 0) st_bl = 'NONE';
    if(parseInt(st_br.substring(0,1)) == 0) st_br = 'NONE';
    if(parseInt(st_bb.substring(0,1)) == 0) st_bb = 'NONE';

    var styles =
        {
            font : st_font,
            style : st_style,
            weight : st_weight,
            size : st_size,
            color : st_color,
            bg : st_bg,
            align : st_align,
            bt : st_bt,
            bl : st_bl,
            br : st_br,
            bb : st_bb,
            dec: st_decimal,
            f : st_format
        };

    $.ajax({
        data: {
            indicateur:indicateur,
            is_etat:is_etat,
            styles: JSON.stringify(styles)
        },
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
            var animated = 'bounceInRight',
                titre = 'FONT ET STYLES';
            show_modal(data,titre,animated);
        }
    });
}

function validate_styles()
{
    var div_indicateur = $('.indicateur_style_edited'),
        indicateur = div_indicateur.attr('data-id'),
        cells = [],
        is_etat = (div_indicateur.hasClass('js_etat')) ? 1 : 0,
        apercu = $('#js_id_apercu'),
        st_font = apercu.css('font-family'),
        st_style = apercu.css('font-style'),
        st_weight = apercu.css('font-weight'),
        st_size = apercu.css('font-size'),
        st_color = apercu.css('color'),
        st_bg = apercu.css('background-color'),
        st_align = apercu.css('text-align'),
        st_bt = apercu.css('border-top'),
        st_bl = apercu.css('border-left'),
        st_br = apercu.css('border-right'),
        st_bb = apercu.css('border-bottom'),
        st_decimal = parseInt(apercu.attr('data-decimal')),
        st_format = parseInt(apercu.attr('data-format'));

    if(parseInt(st_bt.substring(0,1)) == 0) st_bt = 'NONE';
    if(parseInt(st_bl.substring(0,1)) == 0) st_bl = 'NONE';
    if(parseInt(st_br.substring(0,1)) == 0) st_br = 'NONE';
    if(parseInt(st_bb.substring(0,1)) == 0) st_bb = 'NONE';

    var styles =
        {
            font : st_font,
            style : st_style,
            weight : st_weight,
            size : st_size,
            color : st_color,
            bg : st_bg,
            align : st_align,
            bt : st_bt,
            bl : st_bl,
            br : st_br,
            bb : st_bb,
            dec: st_decimal,
            f : st_format
        };

    div_indicateur.find('.js_cellule_edited').each(function(){
        cells.push({ 'row':parseInt($(this).attr('data-row')), 'col':parseInt($(this).attr('data-col')) });
    });

    $.ajax({
        data: {
            indicateur:indicateur,
            is_etat:is_etat,
            cells: JSON.stringify(cells),
            styles: JSON.stringify(styles)
        },
        type: 'POST',
        url: Routing.generate('etat_change_styles'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            close_modal();

            if(parseInt(data))
            {
                $('.js_cellule_edited')
                    .css('font-family',st_font)
                    .css('font-style',st_style)
                    .css('font-weight',st_weight)
                    .css('font-size',st_size)
                    .css('color',st_color)
                    .css('background-color',st_bg)
                    .css('text-align',st_align)
                    .css('border-top',st_bt)
                    .css('border-left',st_bl)
                    .css('border-right',st_br)
                    .css('border-bottom',st_bb)
                    .attr('data-decimal',st_decimal)
                    .attr('data-format',st_format);
                show_info('SUCCES','MODIFICATIONS BIEN ENREGISTREES');
            }
        }
    });
}