/**
 * clotures exercices
 */
function set_status_exerices(dossier,exercices,container)
{
    $.ajax({
        data: { dossier:$('#dossier').val(), exercices:JSON.stringify(exercices) },
        url: Routing.generate('app_cloture_exercices'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            container.html(data);
        }
    });
}