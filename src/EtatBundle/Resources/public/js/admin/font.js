/**
 * Created by SITRAKA on 03/05/2017.
 */
/**
 * font family
 */
$(document).on('change','#police_id',function(){
    $('#js_id_apercu').css('font-family',$(this).val());
});

/**
 * style
 */
$(document).on('change','#style_id',function(){
    var val = parseInt($(this).val()),
        font_style = 'normal',
        font_weight = '';
    if(val == 1) font_style = 'italic';
    else if(val == 2) font_weight = 'bold';
    else if(val == 3)
    {
        font_style = 'italic';
        font_weight = 'bold';
    }
    $('#js_id_apercu').css('font-style',font_style).css('font-weight',font_weight);
});

/**
 * size
 */
$(document).on('change','#taille_id',function(){
    $('#js_id_apercu').css('font-size',parseInt($(this).val()));
});

/**
 * color
 */
$(document).on('click','#js_container_color td.js_td_color_picker',function(){
    $(this).closest('table').find('.td-color-selected').removeClass('td-color-selected');
    $('#js_id_apercu').css('color',$(this).css('background-color'));
    $(this).addClass('td-color-selected');
});

/**
 * bg color
 */
$(document).on('click','#js_container_bg td.js_td_color_picker',function(){
    $(this).closest('table').find('.td-color-selected').removeClass('td-color-selected');
    $('#js_id_apercu').css('background-color',$(this).css('background-color'));
    $(this).addClass('td-color-selected');
});

/**
 * border color
 */
$(document).on('click','#js_container_border_color td.js_td_color_picker',function(){
    $(this).closest('table').find('.td-color-selected').removeClass('td-color-selected');
    $(this).addClass('td-color-selected');
});

/**
 * align
 */
$(document).on('change','input[name="radio-align"]',function(){
    $('#js_id_apercu').css('text-align',$(this).val());
});

/**
 * format
 */
$(document).on('change','input[name="radio-format"]',function(){
    if($(this).is(':checked'))
    {
        $('#js_id_apercu_format').html($(this).attr('data-content'));
        $('#js_id_apercu').attr('data-format',parseInt($(this).val()));
    }
});

/**
 * decimal
 */
$(document).on('change','#decimal-id',function(){
    var val = ($(this).is(':checked')) ? 1 : 0;
    $('#js_id_apercu').attr('data-decimal',val);
});

/**
 * change border
 */
$(document).on('change','.js_apercu_border',function(){
    var checked = ($(this).is(':checked')),
        border = $(this).attr('data-border'),
        border_type = $('input[name="radio-b-type"]:checked').val().sansAccent().trim().toUpperCase(),
        border_epaisseur = parseInt($('#bordure_taille_id').val()),
        border_color = $('#js_container_border_color .td-color-selected').css('background-color');

    if(!checked || border_type == 'NONE')
    {
        $('#js_id_apercu').css(border,'none');
    }
    else
    {
        var val = (border_type != 'DOUBLE') ? border_epaisseur + 'px ' : ' ';
        val += border_type + ' ';
        val += border_color;
        $('#js_id_apercu').css(border,val);
    }
});
