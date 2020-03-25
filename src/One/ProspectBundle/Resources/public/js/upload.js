/* 
 * Created by Netbeans
 * Autor: Mamy RAKOTONIRINA
 */

$(document).ready(function() {
    $(document).on('click', 'div#dropzone span', function() {
        $('input[type="file"]').click();
    });
    
    $(document).on('change', 'input[type="file"]', function(e) {
        traverseFile(e.target.files);
    });
    
    $(document).on('dragenter', 'div#dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    
    $(document).on('dragover', 'div#dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    
    $(document).on('drop', 'div#dropzone', function(e) {
        traverseFile(e.originalEvent.dataTransfer.files);
        e.preventDefault();
        e.stopPropagation();
    });
});

function traverseFile(files) {
    if (typeof files !== "undefined") {
        for (var i=0, l=files.length; i<l; i++) {
            createFormData(files[i]);
        }
    }
}

function createFormData(file) {
    var formFile = new FormData();
    formFile.append('file-to-upload', file);
    uploadFormData(formFile);
}

function uploadFormData(formData) {
    $.ajax({
        url: Routing.generate('one_file_upload'),
        type: 'POST',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(response){
            if (response['type'] === 'success') {
                var output = '<div class="col-md-4" id="'+response['uniqid']+'">';
                output = output+'<div class="uploaded-content">';
                output = output+'<div class="content-image">';
                output = output+'<img src="'+response['toshow']+'">';
                output = output+'</div>';
                output = output+'<div class="content-footer">';
                output = output+'<p>'+response['name']+'</p>';
                output = output+'<input type="hidden" name="uploaded-files[]" value="'+response['path']+response['filename']+'" />';
                output = output+'</div>';
                output = output+'</div>';
                output = output+'<span onclick="deleteUploadedImage(\''+response['uniqid']+'\', \''+response['filename']+'\')">x</span>';
                output = output+'</div>';
                $('#dropzone-uploaded').append(output);
            }
        }
    });
}

function deleteUploadedImage(uniqid, filename) {
    var output = '<input type="hidden" name="deleted-files[]" value="'+filename+'" />';
    $('#dropzone-deleted').append(output);
    $('#dropzone-uploaded #'+uniqid).remove();
}
