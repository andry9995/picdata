$(function () {

    var lastsel_lettre;
    var lettre_grid = $('#js_lettre_mission');
    var window_height = window.innerHeight;
    var client_id = $('#client').val();

    var url = Routing.generate('info_perdos_ldm_liste');

    lettre_grid.jqGrid({
        url: url,
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 260),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        caption: '&nbsp;',
        pager: '#pager_lettre_mission',
        hidegrid: false,
        postData: {
            clientid: client_id
        },
        mtype: 'POST',
        colNames: ['Dossier', 'Lettre de mission', 'PDF LDM',  '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'ldm-dossier', index: 'ldm-dossier', editable: false, classes: 'js-ldm-dossier js-ldm'},
            {name: 'ldm-date', index: 'ldm-date', editable: false, align: "center", width: 150, fixed: true, formatter: "date", sorttype: "date",
                formatoptions: {newformat: 'd-m-Y'}, datefmt: 'd-m-Y', classes: 'js-ldm-date js-ldm'},
            {name: 'ldm-pdf', index: 'ldm-pdf', editable: false, align: "center", classes: 'js-ldm-pdf js-ldm'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: { defaultValue: '<i class="fa fa-trash icon-action js-delete-ldm" title="Supprimer"></i>' },
                classes: 'js-ldm-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_lettre) {
                lettre_grid.restoreRow(lastsel_lettre);
                lastsel_lettre = id;
            }
            lettre_grid.editRow(id);
        },
        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);
            var item_action = (target.closest('td').children('.icon-action').length > 0);

            if (item_action) {
                return false;
            }
            return true;
        },
        loadComplete: function() {
            var uijqgrid = lettre_grid.closest('.ui-jqgrid');
            if (uijqgrid.find('#btn-add-ldm').length === 0) {
                uijqgrid.find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-ldm" class="btn btn-outline btn-primary btn-xs">Ajouter une lettre de mission</button></div>');
            }
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    //Width Jqgrid dans tabs
    $(document).on("click", ".jqgrid-tabs a", function () {
        lettre_grid.jqGrid("setGridWidth", lettre_grid.closest(".panel-body").width());
    });

    $(document).on('change', '#client', function () {
       reloadLdmGrid();
    });

    $(document).on('click', '.js-ldm', function(event) {
        event.preventDefault();
        event.stopPropagation();
        showModalLdm();

        var id = $(this).closest('tr').attr('id');
        $('#ldm-id').val(id);
        $('#dossier').prop('disabled', true);

        $.ajax({
            url: Routing.generate('info_perdos_ldm_detail', {ldm: id}),
            type: 'GET',
            success: function(res) {
                res = $.parseJSON(res);
                $('#dossier').val(res.data.dossier_id);
                $('#date-ldm').val(res.data.date_ldm);

                var list_group_item = '';
                $.each(res.data.fichiers, function(index, item) {
                    list_group_item += '<li data-id="' + item.id + '" class="list-group-item file-ldm-item"><a target="_blank" href="' + item.filepath + '">';
                    list_group_item += '<i class="fa fa-file-pdf-o file-pdf"></i>' + item.filename + '</a>';
                    list_group_item += '<i class="fa fa-trash pull-right delete-file-pdf" title="Supprimer le fichier"></i></li>';
                });
                $('#ldm-file-list').html(list_group_item);
            }
        });
    });

    $(document).on('click', '#btn-add-ldm', function(event) {
        event.preventDefault();
        showModalLdm();
        $('#client-add').prop('disabled', false);
    });

    $(document).on('click', '#btn-save-ldm', function(event) {
        event.preventDefault();
        $('#pdf-ldm').fileinput('upload');
    });

    $(document).on('click', '.delete-file-pdf', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var item = $(this).closest('.file-ldm-item');
        var id = item.attr('data-id');
        swal({
            title: 'Supprimer',
            text: "Voulez-vous supprimer ce fichier ?",
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler'
        }).then(function () {
            $.ajax({
                url: Routing.generate('info_perdos_ldm_fichier_delete'),
                type: 'DELETE',
                data: {
                    id: id
                },
                success: function(data) {
                    item.remove();
                    show_info('Lettre de Mission', 'Fichier supprimée.', 'info');
                    reloadLdmGrid();

                }
            });
        });


    });

    $(document).on('click', '.js-delete-ldm', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var rowid = $(this).closest('tr').attr('id');
        lettre_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('info_perdos_ldm_delete'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    function showModalLdm() {

        var pdfLdm = $('#pdf-ldm');
        var dateLdm = $('#date-ldm');

        $('#ldm-id').val('');
        dateLdm.val('');
        $('#dossier').val('');
        pdfLdm.fileinput('destroy');
        $('#ldm-file-list').empty();

        $.ajax({
            url: Routing.generate('info_perdos_ldm_dossier'),
            type: 'POST',
            async: false,
            data: { clientid: $('#client').val() },
            success: function (data) {
                $('#dossier-add').html(data)
           }
        });

        pdfLdm.fileinput({
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
            allowedFileTypes: ['image', 'text', 'pdf'],
            uploadUrl: Routing.generate('info_perdos_ldm_add'),
            uploadExtraData:function(previewId, index) {
                var data = {
                    id: $('#ldm-id').val(),
                    dossierid: $('#dossier').val(),
                    date_ldm: dateLdm.val()
                };
                return data;
            }
        });

        dateLdm.datepicker({
            format:'dd-mm-yyyy',
            language: 'fr',
            autoclose:true,
            todayHighlight: true,
            clearBtn: true
        });

        $('#ldm-modal').modal('show');
        modalDraggable();
    }

    $('#pdf-ldm').on('filebatchuploadsuccess', function(event, data, previewId, index) {
        reloadLdmGrid();
        $('#ldm-modal').modal('hide');
    });

    function reloadLdmGrid(){
        var url = Routing.generate('info_perdos_ldm_liste');

        var client_id = $('#client').val();

        lettre_grid.jqGrid('setGridParam', {
            url: url,
            postData: {
                clientid: client_id
            },
            mtype: 'POST',
            datatype: 'json'

        })
            .trigger('reloadGrid', {fromServer: true, page: 1});
    }
});