/**
 * Created by SITRAKA on 13/06/2017.
 */
$(document).on('click','#js_show_move',function(){
    if($('.chk-file:checked').length == 0)
    {
        show_info('NOTICE','COCHER LES PIECES A DEPLACER','error');
        return;
    }

    $.ajax({
        data: {  },
        type: 'POST',
        url: Routing.generate('img_historique_show_move'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var titre = 'DEPLACER LES PIECES VERS...',animated = 'bounceInRight';
            show_modal(data,titre,animated);
            charger_site();
        }
    });
});

$(document).on('click','#js_move',function(){
    if($('#dossier option:selected').text().trim() == '')
    {
        show_info('ERREUR','SELECTIONNER LE DOSSIER','error');
        $('#dossier').closest('.form-group').addClass('has-error');
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    var dossier = $('#dossier').val().trim(),
        exercice = parseInt($('#exercice').val());
    if(exercice == 0)
    {
        show_info('ERREUR','SELECTIONNER L EXERCICE','error');
        $('#exercice').closest('.form-group').addClass('has-error');
        return;
    }
    else $('#exercice').closest('.form-group').removeClass('has-error');

    var images = [];
    $('.chk-file:checked').each(function(){
        images.push($(this).closest('.file-box').attr('data-id').trim());
    });

    $.ajax({
        data: { dossier:dossier, exercice:exercice, images:JSON.stringify(images) },
        type: 'POST',
        url: Routing.generate('img_historique_move'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            close_modal();
            $('#js_charger_tree').click();
            show_info('SUCCES','MODIFICATIONS ENREGISTREES AVEC SUCCES');
        }
    });
});

$(document).on('click','#js_remove',function(){
    if($('.chk-file:checked').length == 0)
    {
        show_info('NOTICE','COCHER LES PIECES A SUPPRIMER','error');
        return;
    }

    swal({
        title: 'Supprimer',
        text: "Voulez-vous vraiment supprimer ces pièces ?",
        type: 'question',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
    }).then(function () {
        var images = [];
        $('.chk-file:checked').each(function(){
            images.push($(this).closest('.file-box').attr('data-id').trim());
        });

        $.ajax({
            data: { images:JSON.stringify(images) },
            url: Routing.generate('img_historique_remove'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                $('#js_charger_tree').click();
                show_info('SUCCES','LES IMAGES SONT SUPPRIMES');
            }
        });
    }, function (dismiss) {
        if (dismiss === 'cancel') {
        }
    });
});
