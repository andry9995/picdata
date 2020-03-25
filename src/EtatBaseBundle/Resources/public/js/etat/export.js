/**
 * Created by SITRAKA on 14/07/2017.
 */
$(document).on('click','.js_export',function(e){
    e.preventDefault();
    exporter($(this));
});

function hex_c(c)
{
    var parts = c.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if(typeof parts === 'undefined' || parts == null) return null;

    delete(parts[0]);
    for (var i = 1; i <= 3; ++i) {
        parts[i] = parseInt(parts[i]).toString(16);
        if (parts[i].length == 1) parts[i] = '0' + parts[i];
    }
    return parts.join('');
}

function get_styles(el)
{
    var isBold = (el.css('font-weight').trim().toUpperCase() == 'BOLD') ? 1 : 0;
    return {
        bg: hex_c(el.css('background-color')),
        cl: hex_c(el.css('color')),
        bold: isBold
    };
}
