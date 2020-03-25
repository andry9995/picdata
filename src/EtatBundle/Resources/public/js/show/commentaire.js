/**
 * Created by SITRAKA on 15/10/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_edit_commentaire',function(){
        $(this).closest('.ibox').find('.cl_summer').summernote({focus: true,lang:'fr-FR'});

        $(this).addClass('hidden');
        $('.cl_save_commentaire').removeClass('hidden');
    });

    $(document).on('click','.cl_save_commentaire',function(){
        $(this).closest('.ibox').find('.cl_summer').destroy();
        var html = $(this).closest('.ibox').find('.cl_summer').html(),
            tab_active = null;

        $('.cl_edit_commentaire').removeClass('hidden');
        $(this).addClass('hidden');

        $('#js_id_container_etat').find('.js_cl_tab_etat').each(function(){
            if($(this).hasClass('active')) tab_active = $(this);
        });

        $.ajax({
            data: {
                dossier:$('#dossier').val(),
                indicateur: tab_active.attr('data-id'),
                commentaire: html
            },
            url: Routing.generate('etat_commentaire_save'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);

                if (parseInt(data) === 1)
                {
                    show_info('Succès','Modification bien enregistée avec succès');
                }
            }
        });
    });
});