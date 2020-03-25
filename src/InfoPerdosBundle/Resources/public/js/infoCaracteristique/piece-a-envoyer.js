/**
 * Created by MAHARO on 14/02/2017.
 */

$(document).ready(function(){
    initAllFileInputs();
    
});

/**
 * Initilalisation an'ny input 1 
 * @param selecteur
 */
function initFileInput(selecteur) {
    $('#'+selecteur).fileinput({
        language: 'fr',
        theme: 'fa',
        uploadAsync: false,
        showPreview: false,
        showUpload: false,
        showRemove: false,
        fileTypeSettings: {
            image: function(vType, vName) {
                return (typeof vType !== "undefined") ? vType.match('image.*') : vName.match(/\.(pdf|gif|png|jpe?g)$/i);
            },
            text: function(vType, vName) {
                return typeof vType !== "undefined" && vType.match('text.*') || vName.match(/\.(txt|xls|xlsx|doc|docx|ppt|pptx|csv)$/i);
            },
            pdf: function(vType, vName) {
                return typeof vType !== "undefined" && vType.match('pdf');
            }
        },
        allowedFileTypes: ['pdf'],
        uploadUrl: Routing.generate('info_perdos_piece_a_envoyer',{selecteur: selecteur}),
        uploadExtraData:function(previewId, index) {
            var data = {
                // dossier: $('#dossier').val()
                dossier: dossier_id
            };
            return data;
        }
    });

    $('#'+selecteur).on('filebatchuploadcomplete', function() {

        var fileCapt = $('#'+selecteur).closest('.input-group').find('.file-caption-name');
        fileCapt.append('<i class="fa fa-check kv-caption-icon"></i>');
        disableEstEnvoyeAfterEnvoi($('#'+selecteur));

    });

    $('#'+selecteur).on('fileuploaderror', function(event, data, msg) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
        console.log('File upload error');
       // get message
       alert(msg);
    });
}

/**
 * Mi-initialiser ny Input File rehetra
 */
function initAllFileInputs(){

    //Informations comptables et fiscales

    initFileInput('js_envoi_balance_n1');
    initFileInput('js_envoi_grand_livre');
    initFileInput('js_envoi_journaux_n1');
    initFileInput('js_envoi_dernier_rapprochement_banque');
    initFileInput('js_envoi_etat_immobilisation');
    initFileInput('js_envoi_liasse_fisacle_n1');
    initFileInput('js_envoi_tva_derniere_ca3');

    //Documents juridiques
    initFileInput('js_envoi_statut');
    initFileInput('js_envoi_kbis');
    initFileInput('js_envoi_baux');
    initFileInput('js_envoi_assurance');
    initFileInput('js_envoi_autre');
    initFileInput('js_envoi_emprunt');
    initFileInput('js_envoi_leasing');
    
}

/**
 * Manala ny Progress Bar sy nu bouton Cancel rehefa mandeha ny upload
 */
function hideProgessBar()
{
    $('.kv-upload-progress').hide();
    $('.fileinput-cancel').hide();
}

