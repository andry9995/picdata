$(document).ready(function(){

    setScrollerHeight();

    $(document).on('click','#btn-edit-instr-contenu',function () {
        $('.js_instr_contenu').summernote({
            lang: 'fr-FR',
            focus: true,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['fontstyle', ['fontname','fontsize']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['picture', 'link', 'unlink']]
            ]
        });
    });

    $(document).on('click','#btn-save-instr-contenu',function () {

        var aHTML = $('.js_instr_contenu').summernote('code');
        saveInstruction(aHTML);
        $('.js_instr_contenu').summernote('destroy');

    });

    $(document).on('change','#js_instruction_type',function(){
        var instructionType = $('#js_instruction_type').val();
        $('.js_instr_contenu').summernote('destroy');
        if(instructionType == -1)
        {
            $('.js_instr_contenu').val('');
        }
        else {
            showInstructionEdit(instructionType);
            setScrollerHeight();
        }
        
    });
});

/**
 * Mi-enregistrer ny contenu an'ilay texte
 * @param aHTML
 */
function saveInstruction(aHTML){
    var instructionType = $('#js_instruction_type').val();
    var instructionTexte =aHTML;

    var lien = Routing.generate('info_perdos_instr_instruction_texte');

    $.ajax({

        data:{instructionType:instructionType, instructionTexte:instructionTexte},
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: false,
        dataType: 'html',
        success: function(data){

            var res = parseInt(data);

            if (res == 1) {
                show_info('SUCCES', 'MODIFICATION BIEN ENREGISTREE');
            }
            else if (res == -1) {
                show_info('ATTENTION', "IL FAUT D'ABORD CHOISIR UN TYPE",'warning');
            }
        }
    });
}

/**
 * Mi-afficher an'ny instruction
 * @param json
 */
function showInstructionEdit(json){
    
    var lien = Routing.generate('info_perdos_instr_show_editeur',{json: json});

    $.ajax({

        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        async: false,
        dataType: 'html',
        success: function(data){
            $('.js_instr_contenu').html(data);
        }
    });
}


function setScrollerHeight()
{
    $('.scroller').height(500);
}



