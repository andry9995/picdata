Dropzone.autoDiscover = false;

$(document).ready(function () {
    charger_site();
    var myDropzone = new Dropzone('#my-awesome-dropzone', {
        maxFilesize: 200,
        parallelUploads: 10000,
        uploadMultiple: true,
        dictDefaultMessage: "Déposer les images ici ou cliquer",
        addRemoveLinks: true,
        dictRemoveFile: "<i class='fa fa-times'></i>&nbsp;Enlever",
        thumbnailWidth: 30,
        thumbnailHeight: 30,
        autoProcessQueue: false,
        async: true,
        successmultiple: function (file, response) {
            alert(response);
            show_info("Envoi images","Les images sont envoyées avec succès.", 'success');
            myDropzone.removeAllFiles();
            $('#btn-cancel-image-param').addClass('hidden');
            $('#image-dropzone-area').addClass('hidden');
            $('#btn-valider-image-param').removeClass('hidden');
        }
    });

    $(document).on('change','#dossier',function(){
        $('#js_dossier_upl').val($('#dossier').val());
        $('#js_exercice_upl').val($('#exercice').val());
    });

    $(document).on('change','#exercice',function(){
        $('#js_dossier_upl').val($('#dossier').val());
        $('#js_exercice_upl').val($('#exercice').val());
    });

    $('#my-awesome-dropzone').height($(window).height() * 0.45);

    $(document).on('click', '#btn-upload', function (event) {
        event.preventDefault();
        myDropzone.processQueue();
    });

    $(document).on('click', '#btn-valider-image-param', function (event) {
        event.preventDefault();

        if ($('#dossier option:selected').text().trim() != '' && $('#dossier option:selected').text().trim().toUpperCase() != 'Tous' &&
                parseInt($('#exercice').val()) > 0) {
            $(this).addClass('hidden');
            $('#btn-cancel-image-param').removeClass('hidden');
            $('#image-dropzone-area').removeClass('hidden').addClass('animated fadeInLeft');
        } else {
            show_info("Envoi images","Sélectionner le dossier et l'exercice", 'error');
        }
    });

    $(document).on('click', '#btn-cancel-image-param', function (event) {
        event.preventDefault();
        $(this).addClass('hidden');
        $('#btn-valider-image-param').removeClass('hidden');
        $('#image-dropzone-area').addClass('hidden');
        myDropzone.removeAllFiles();
    });

});
