$(document).ready(function() {
    var data_date = [];
    var data_reponse = [];
    var data_piece = [];
    var btn_modal_drt_reponse = $('#submit-add-reponse');
    $(document).on('click','#btn-save-drt-reponse',function(){
        upload_files();
    });

    $(document).on('click','#btn-save-drt-echange',function(){
        upload_files_add();
    });

    $(document).on('click','#btn-import-drt',function(){
        upload_import_file();
    });


    $(document).on('click', '#submit-reimport', function() {
        var data_error = $.parseJSON($('#drt-data-error-import').attr('data'));
        var multi = (".js_id_input_file_reimports");
        var list_erreur = [];
        var data_date = [];
        $(multi).each(function () {
            var fieldVal = $(this).val();
            if(!fieldVal){
                var old_name = $(this).parent().parent().children().first()[0].innerHTML;
                data_error.forEach(function (drt) {
                    if(old_name === drt['name']){
                        list_erreur.push({
                            name: drt.name,
                            dossier_similaire: drt.dossier_similaire,
                            etat: drt.etat,
                            fileToUpload: drt.fileToUpload
                        })
                    }
                });
            }else{
                var file = $(this).parent()[0].firstChild.files[0];
                data_date.push({name:file.name, date: file.lastModifiedDate});
            }
        });
        $('#form-reimport').ajaxSubmit({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: {
                client: $('#client').val(),
                is_import: 0,
                list_erreur: JSON.stringify(list_erreur),
                date: JSON.stringify(data_date)
            },
            success: function (data) {
                var pas_dossier = data.pas_dossier,
                    existe = data.existe,
                    drt_importe = data.drt_importe,
                    fichier_incorrect = data.fichier_incorrect,
                    list_erreur = data.list_erreur_import,
                    drt_erreur = [],
                    drt_new_erreur = [],
                    title_error_import,
                    total_importe = existe.length + pas_dossier.length + drt_importe + fichier_incorrect.length + list_erreur.length,
                    dossier_name = '';

                var file_html = "<input class='js_id_input_file_reimports' id='reimport' name='js_id_input_file_reimport[]' type='file'>";
                existe.forEach(function (drt) {
                    drt_erreur.push({name: drt.file_name, dossier: '', etat: drt.echange_type + ' existe', fileToUpload: ''})
                });
                pas_dossier.forEach(function (drt) {
                    (drt.dossier).forEach(function (similar_dossier) {
                        dossier_name += similar_dossier.nom + ' ';
                    });
                    drt_erreur.push({
                        name: drt.file_name,
                        dossier_similaire: dossier_name,
                        etat: 'Pas de dossier',
                        fileToUpload: file_html
                    });
                    dossier_name = '';
                });
                fichier_incorrect.forEach(function (drt) {
                    drt_erreur.push({
                        name: drt.file_name,
                        dossier_similaire: '',
                        etat: 'Nom fichier incorrect',
                        fileToUpload: file_html
                    })
                });

                list_erreur.forEach(function (list_old_erreur_drt) {
                    drt_erreur.push({
                        name: list_old_erreur_drt.name,
                        dossier_similaire: list_old_erreur_drt.dossier_similaire,
                        etat: list_old_erreur_drt.etat,
                        fileToUpload: list_old_erreur_drt.fileToUpload
                    })
                });

                if (pas_dossier.length === 0 && existe.length === 0 && list_erreur.length === 0 && drt_importe > 0) {
                    $('#js_id_input_file_reimport').fileinput('clear');
                    $('#drt-error-import-modal').modal('hide');
                    show_info("Envoi fichier", "Les fichiers sont importés avec succès.");
                } else {
                    $('#js_id_input_file_reimport').fileinput('clear');
                    $('#table_list_error_import').jqGrid("clearGridData");
                    var import_grid = $('#table_list_error_import'),
                        height_erreur_jqgrid = $('.jqGrid_wrapper').height(),
                        width_erreur_jqgrid = $('.jqGrid_wrapper').width();
                    if (drt_importe === 0) {
                        title_error_import = 'Aucun fichier sur ' + total_importe + ' fichiers importés, veuillez verifier puis renvoyer les fichiers ci-dessous.';
                    } else {
                        title_error_import = drt_importe + ' fichiers sur ' + total_importe + ' sont importés avec succès, veuillez verifier puis renvoyer les fichiers ci-dessous.';
                    }

                    $('.title-error-import').html(title_error_import);
                    $('#drt-error-import-modal').modal({backdrop: 'static', keyboard: false});
                    $('#drt-error-import-modal').modal('show');
                    $('#drt-data-error-import').attr('data', JSON.stringify(drt_erreur));
                    $('.style-file-reimporte .file-preview .file-drop-zone').css('height', height_erreur_jqgrid - 67);
                    import_grid = $('#table_list_error_import');
                    import_grid.jqGrid('setGridWidth', width_erreur_jqgrid - 20);
                    import_grid.jqGrid('setGridHeight', height_erreur_jqgrid - 50);
                    import_grid.jqGrid('setGridParam', {
                        data: drt_erreur
                    })
                        .trigger('reloadGrid', {fromServer: true, page: 1});
                }
                return;
            }
        });
    });

    $(document).on('click', '#submit-add-reponse', function () {
        if (inputFileGetCount('js_id_input_file_drt') !== 1) {
            show_info('Fichier vide', 'Ajouter la réponse', 'warning');
            return false;
        }else{
            var ajaxData = new FormData();
            var idata = {};
            idata['client'] = $('#client').val();
            idata['exercice'] = $('#exercice').val();
            idata['dossier'] = btn_modal_drt_reponse.attr('data-dossier');
            idata['numero'] = btn_modal_drt_reponse.attr('data-numero');
            idata['echangeType'] = $(document).find('input[name="show-filter-item"]:checked').val();
            idata['message'] = $('#msg-reponse-rdrt').val();
            idata['statut'] = '';
            idata['is_reponse'] = true;
            $.each(data_piece,function(i,file){
                ajaxData.append('image['+i+']', file);
            });
            ajaxData.append('reponse', data_reponse[0]);
            ajaxData.append('idata', JSON.stringify(idata));
        }

        $.ajax({
            type: 'POST',
            url: Routing.generate('drt_add_reponse'),
            data: ajaxData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.toString() === 'ERROR') {
                    show_info("Une erreur est survenue pendant l'envoi", "Veuillez renvoyer le fichier", "error");
                    $('#js_id_input_file_drt').fileinput('clear');
                    $('#js_id_input_file_piece_drt').fileinput('clear');
                }
                else{
                    $('#js_id_input_file_drt').fileinput('clear');
                    $('#js_id_input_file_piece_drt').fileinput('clear');
                    $('#drt-repondre-modal').modal('hide');
                    show_info("Envoi fichier", "Le fichier est envoyé avec succès.");
                    go();
                }
            }
        })
    });

    var defaultReplyMessage = 'Pour le dépôt de pièce.';

    var defaultReplyMessagePiece = 'Pour le dépôt des xls et doc.';
    var defaultImportMessage = 'Sélectionner un client et Cliquer pour sélectionner des fichier concernés à importer.<br>' +
                                'La DRT ou DRP ou la réponse DRT ou la réponse DRP doit être sous forme: DRT ABC 18 1 ou DRP ABC 18 1 ou R 1 DRT 18 1 ou R 1 DRP 18 1';

    var defaultAddMessage =
        'Sélectionner le fichier concerné.';

    $('#js_id_input_file_drt')
        .fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            hideThumbnailContent: false,
            showCancel: false,
            showBrowse: false,
            showUpload: false,
            fileActionSettings: {
                showZoom : false
            },
            maxFilePreviewSize: 0,
            dropZoneTitle: defaultReplyMessagePiece,
            browseOnZoneClick: true,
            dropZoneClickTitle: '',
            previewFileIcon: '<i class="fa fa-file"></i>',
            allowedPreviewTypes: null,
            maxFileCount: 1,
            uploadUrl: Routing.generate('drt_add_reponse'),
            previewFileIconSettings: {
                'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                'xls': '<i class="fa fa-file-excel-o text-success"></i>',
                'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
                'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip': '<i class="fa fa-file-archive-o text-muted"></i>'
            },
        })
        .on('fileselect', function(event, numFiles, label) {
            verrou_fenetre(true);
        })
        .on('filebatchselected', function(event, files) {
            data_reponse = [];
            files.forEach(function (file) {
                data_reponse.push(file);
            });
            verrou_fenetre(false);
        });

    $('#js_id_input_file_add_drt')
        .fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            hideThumbnailContent: false,
            showCancel: false,
            showBrowse: false,
            showUpload: false,
            fileActionSettings: {
                showZoom : false
            },
            maxFilePreviewSize: 0,
            uploadUrl: Routing.generate('drt_add_echange'),
            dropZoneTitle: defaultAddMessage,
            browseOnZoneClick: true,
            dropZoneClickTitle: '',
            previewFileIcon: '<i class="fa fa-file"></i>',
            allowedPreviewTypes: null,
            maxFileCount: 1,
            previewFileIconSettings: {
                'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                'xls': '<i class="fa fa-file-excel-o text-success"></i>',
                'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
                'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip': '<i class="fa fa-file-archive-o text-muted"></i>'
            },
            uploadExtraData: function(){
                return {
                    client: $('#client').val(),
                    exercice: $('#exercice').val(),
                    dossier: $('#btn-save-drt-echange').attr('data'),
                    echange_type: $('#btn-save-drt-echange').attr('data-echange-type'),
                    drt_add: 1,
                    statut: $('#statut').val(),
                    date: data_date,
                    message: $('#msg-reponse-drt').val()
                }
            }
        })
        .on('filebatchuploadsuccess', function (event, data) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response.toString(), reader = data.reader;

            if (response === 'ERROR') {
                show_info("Une erreur est survenue pendant l'envoi", "Veuillez renvoyer le fichier", "error");
                $('#js_id_input_file_add_drt').fileinput('clear');
            }
            else{
                $('#js_id_input_file_add_drt').fileinput('clear');
                $('#drt-add-modal').modal('hide');
                show_info("Envoi fichier", "Le fichier est envoyé avec succès.");

                go();
            }
        })
        .on('filebatchuploaderror', function (event, data, msg) {
            $('#js_id_input_file_add_drt').fileinput('clear');
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            show_info("Une erreur est survenue pendant l'envoi", "Veuillez renvoyer le fichier", "error");
        })
        .on('fileselect', function(event, numFiles, label) {
            verrou_fenetre(true);
        })
        .on('filebatchselected', function(event, files) {
            data_date = [];
            files.forEach(function (file) {
                data_date.push({name:file.name, date: file.lastModifiedDate});
            });
            data_date = JSON.stringify(data_date);
            verrou_fenetre(false);
        });

    $('#js_id_input_file_piece_drt')
        .fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            hideThumbnailContent: false,
            showCancel: false,
            showBrowse: false,
            showUpload: false,
            fileActionSettings: {
                showZoom : false
            },
            maxFilePreviewSize: 0,
            dropZoneTitle: defaultReplyMessage,
            browseOnZoneClick: true,
            dropZoneClickTitle: ' ',
            previewFileIcon: '<i class="fa fa-file"></i>',
            uploadUrl: Routing.generate('drt_add_reponse'),
            allowedPreviewTypes: null,
            previewFileIconSettings: {
                'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                'xls': '<i class="fa fa-file-excel-o text-success"></i>',
                'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
                'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip': '<i class="fa fa-file-archive-o text-muted"></i>'
            }
        })
        .on('fileselect', function(event, numFiles, label) {
            verrou_fenetre(true);
        })
        .on('filebatchselected', function(event, files) {
            data_piece = [];
            files.forEach(function (file) {
                data_piece.push(file);
            });
            verrou_fenetre(false);
        });

    $('#js_id_input_file_import')
        .fileinput({
            language: 'fr',
            theme: 'fa',
            uploadAsync: false,
            showPreview: true,
            hideThumbnailContent: false,
            showCancel: false,
            showBrowse: false,
            showUpload: false,
            fileActionSettings: {
                showZoom : false
            },
            maxFilePreviewSize: 0,
            uploadUrl: Routing.generate('drt_import'),
            dropZoneTitle: defaultImportMessage,
            browseOnZoneClick: true,
            dropZoneClickTitle: '',
            previewFileIcon: '<i class="fa fa-file"></i>',
            allowedPreviewTypes: null,
            previewFileIconSettings: {
                'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                'xls': '<i class="fa fa-file-excel-o text-success"></i>',
                'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
                'ppt': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
                'pdf': '<i class="fa fa-file-pdf-o text-danger"></i>',
                'zip': '<i class="fa fa-file-archive-o text-muted"></i>'
            },
            uploadExtraData: function(){
                return {
                    client: $('#client').val(),
                    is_import: 1,
                    date : data_date
                }
            }
        })
        .on('filebatchuploadsuccess', function (event, data) {
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response.toString(), reader = data.reader,
                pas_dossier = data.response.pas_dossier,
                existe = data.response.existe,
                drt_importe = data.response.drt_importe,
                fichier_incorrect = data.response.fichier_incorrect,
                drt_erreur = [],
                title_error_import,
                total_importe = existe.length + pas_dossier.length + drt_importe + fichier_incorrect.length,
                dossier_name = '';

            var file_html = "<input class='js_id_input_file_reimports' id='reimport' name='js_id_input_file_reimport[]' type='file'>";
            existe.forEach(function (drt) {
                drt_erreur.push({name:drt.file_name, dossier: '', etat:drt.echange_type + ' existe', fileToUpload: ''})
            });
            pas_dossier.forEach(function (drt) {
                (drt.dossier).forEach(function (similar_dossier) {
                    dossier_name +=  similar_dossier.nom + ' ';
                });
                drt_erreur.push({name:drt.file_name, dossier_similaire: dossier_name, etat:'Pas de dossier', fileToUpload: file_html});
                dossier_name = '';
            });
            fichier_incorrect.forEach(function (drt) {
                drt_erreur.push({name:drt.file_name, dossier_similaire: '', etat:'Nom fichier incorrect', fileToUpload: file_html })
            });

            if(pas_dossier.length === 0 && existe.length === 0 && drt_importe > 0) {
                $('#js_id_input_file_import').fileinput('clear');
                show_info("Envoi fichier", "Les fichiers sont importés avec succès.");
            }else{
                $('#js_id_input_file_import').fileinput('clear');
                $('#drt-error-import-modal').modal({backdrop: 'static', keyboard: false});
                $('#table_list_error_import').jqGrid("clearGridData");
                var import_grid = $('#table_list_error_import'),
                    height_erreur_jqgrid = $('.jqGrid_wrapper').height(),
                    width_erreur_jqgrid = $('.jqGrid_wrapper').width();
                if(drt_importe === 0) {
                    title_error_import = 'Aucun fichier sur '+total_importe+' fichiers importés, veuillez verifier puis renvoyer les fichiers ci-dessous.';
                }else{
                    title_error_import = drt_importe+' fichiers sur '+total_importe+' sont importés avec succès, veuillez verifier puis renvoyer les fichiers ci-dessous.';
                }

                $('.title-error-import').html(title_error_import);
                $('#drt-error-import-modal').modal('show');
                $('#drt-data-error-import').attr('data', JSON.stringify(drt_erreur));
                $('.style-file-reimporte .file-preview .file-drop-zone').css('height', height_erreur_jqgrid - 67);
                import_grid = $('#table_list_error_import');
                import_grid.jqGrid('setGridWidth', width_erreur_jqgrid - 20);
                import_grid.jqGrid('setGridHeight', height_erreur_jqgrid - 50);
                import_grid.jqGrid('setGridParam', {
                    data: drt_erreur
                })
                    .trigger('reloadGrid', {fromServer: true, page: 1});
            }
        })
        .on('filebatchuploaderror', function (event, data, msg) {
            $('#js_id_input_file_import').fileinput('clear');
            var form = data.form, files = data.files, extra = data.extra,
                response = data.response, reader = data.reader;
            show_info("Une erreur est survenue pendant l'envoi", "Veuillez verifier puis renvoyer les fichiers", "error");
        })
        .on('fileselect', function(event, numFiles, label) {
            verrou_fenetre(true);
        })
        .on('filebatchselected', function(event, files) {
            data_date = [];
            files.forEach(function (file) {
                data_date.push({name:file.name, date: file.lastModifiedDate});
            });
            data_date = JSON.stringify(data_date);
            verrou_fenetre(false);
        });
    set_height();
});

function set_height()
{
    $('.file-drop-zone').height($(window).height() - 320);
}
function upload_is_valid() {
    if (!$('.kv-preview-thumb').length > 0) {
        show_info('Fichier vide', 'Ajouter le fichier', 'warning');
        return false;
    }
    return true;
}

function upload_piece_is_valid() {
    var statut = false;
    if (inputFileGetCount('js_id_input_file_drt') >= 1) {
        $('#js_id_input_file_drt').fileinput('upload');
        statut = true;
    }

    if(!statut) {
        show_info('Fichier vide', 'Ajouter la réponse', 'warning');
        return statut;
    }

    if (inputFileGetCount('js_id_input_file_piece_drt') >= 1) {
        $('#js_id_input_file_piece_drt').fileinput('upload');
    }
}

function upload_files()
{
    upload_piece_is_valid();
}

function upload_files_add()
{
    if(upload_is_valid()) $('#js_id_input_file_add_drt').fileinput('upload');
}

function upload_import_file()
{
    if(upload_is_valid()) $('#js_id_input_file_import').fileinput('upload');
}

function upload_reimport_file()
{
    var cnt = $('.js_id_input_file_reimport').fileinput('getFilesCount');
    var upload_ok = true;
    cnt.forEach(function (count) {
        if(count === 0){
            upload_ok = false;
        }
    });

    if (upload_ok) {
        $('.js_id_input_file_reimport').fileinput('upload');
    }else{
        show_info('Fichier vide', 'Ajouter le fichier', 'warning');
        return false;
    }
}


function inputFileGetCount(id) {
    var cnt = $('#' + id).fileinput('getFilesCount');
    return cnt;
}