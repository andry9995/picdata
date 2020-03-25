/**
 * Created by SITRAKA on 15/05/2017.
 */
/*function select_ctrl_cell(td)
{
    if(!ctrl_mode || !cntrlIsPressed)
    {
        $('.js_ctrl_edited').removeClass('js_ctrl_edited');
        if(!ctrl_mode) return;
    }

    if(td.find('.operande').length > 0 || (td.text().indexOf('[') >= 0 && td.text().indexOf(']') >= 0))
    {
        td.addClass('js_ctrl_edited');
    }
}

$(document).on('click','.js_show_ctrl',function(){
    show_control_admin($(this));
});

function show_control_admin(btn)
{
    $('.js_indicateur_ctrl_edited').removeClass('js_indicateur_ctrl_edited');
    var div_container = btn.closest('.js_indicateur_sortable').addClass('js_indicateur_ctrl_edited'),
        indicateur = div_container.attr('data-id'),
        is_etat = div_container.hasClass('js_etat') ? 1 : 0,
        cells = [];

    div_container.find('.js_ctrl_edited').each(function(){
        var cell = { row:parseInt($(this).attr('data-row')), col:parseInt($(this).attr('data-col')) };
        cells.push(cell);
    });

    $.ajax({
        data: {
            indicateur: indicateur,
            is_etat: is_etat,
            cells: JSON.stringify(cells)
        },
        type: 'POST',
        url: Routing.generate('etat_show_control'),
        //async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var animated = 'bounceInRight',
                titre = 'CONTROL';
            show_modal(data,titre,animated);
        }
    });
}*/