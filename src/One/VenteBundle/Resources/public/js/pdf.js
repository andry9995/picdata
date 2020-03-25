/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

/**
 * Génération du pdf d'un document
 * @param {type} type
 * @param {type} id
 * @returns {undefined}
 */
function generatePDF(type, id) {
    $.ajax({
        url: Routing.generate('one_pdf_generate', {'type': type, 'id': id}),
        type: 'GET',
        dateType: 'html',
        data:{
            'dossierId': $('#dossier').val()
        },
        success: function(response) {
            $.ajax({
                url: Routing.generate('one_pdf_generate', {'type': type, 'id': id}),
                type: 'POST',
                dateType: 'html',
                data: {'content': response, 'dossierId': $('#dossier').val()},
                success: function() {}
            });
        }
    });
}

