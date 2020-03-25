function charger_control()
{
    $.ajax({
        data: { banqueCompte:$('#js_banque_compte').val(), exercice:$('#exercice').val() },
        type: 'POST',
        url: Routing.generate('banque_control'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_control').html(data);
        }
    });
}