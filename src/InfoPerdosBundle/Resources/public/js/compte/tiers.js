/**
 * Created by SITRAKA on 21/10/2016.
 */
$(document).on('change','.js_collectif',function(){
    change_collectif($(this));
});

function change_collectif(select)
{
    lien = Routing.generate('info_perdos_set_collectif');
    $.ajax({
        data: { dossier:$('#dossier').val().trim(), type:parseInt(select.attr('data-type').trim()),pcc:select.val() },
        type: 'POST',
        url: lien,
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
}

function charger_pcc_combow()
{
    lien = Routing.generate('info_perdos_pccs');
    $.ajax({
        data: { dossier:$('#dossier').val().trim(),action:1 },
        type: 'POST',
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_content_pcc_cobow').html(data);
        }
    });
}