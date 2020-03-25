$(document).ready(function(){
    initPieceFileInput('js_ndf_envoi', 0);
    $('#js_ndf_envoi').fileinput('disable');

    $(document).on('click', '.table-periode-annee th', function () {
        if($(this).hasClass('active')){
            $("#js_ndf_envoi").fileinput('destroy');
            initPieceFileInput('js_ndf_envoi', $(this).html());
            $('#js_ndf_envoi').fileinput('enable');
        }
        else{
            $('#js_ndf_envoi').fileinput('disable');
        }
    });
});


function initPieceFileInput(selecteur, exercice) {
    $('#'+selecteur).fileinput({
        language: 'fr',
        theme: 'fa',
        uploadAsync: false,
        showPreview: true,
        showUpload: true,
        showRemove: false,
        showCancel: false,
        uploadUrl: Routing.generate('note_frais_envoi'),
        uploadExtraData: function() {
            return {
                dataId: exercice
            };
        }
    });

    $('#'+selecteur).on('filebatchuploadcomplete', function() {
        // var fileCapt = $('#'+selecteur).closest('.input-group').find('.file-caption-name');
        // fileCapt.append('<i class="fa fa-check kv-caption-icon"></i>');

    });

    $('#'+selecteur).on('fileuploaderror', function(event, data, msg) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
        console.log('File upload error');
        // get message
        alert(msg);
    });


}