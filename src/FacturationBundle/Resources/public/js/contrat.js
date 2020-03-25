/**
 * Created by TEFY on 16/01/2017.
 */
$(function() {
    var lastsel_contrat;
    var contrat_grid = $('#js_contrat');
    var window_height = window.innerHeight;

    //Liste contrats signés
    contrat_grid.jqGrid({
        url: Routing.generate('fact_contrat_liste'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: (window_height - 260),
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 100,
        caption: '&nbsp;',
        rowList: [100, 200, 500],
        pager: '#pager_contrat',
        hidegrid: false,
        editurl: Routing.generate('fact_contrat_edit'),
        colNames: ['Clients', 'Date Signature', 'PDF Contrat', 'Autoriser modif. tarif', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'contrat-client', index: 'contrat-client', editable: false, classes: 'js-contrat-client js-contrat'},
            {name: 'contrat-date', index: 'contrat-date', editable: false, align: "center", width: 150, fixed: true, formatter: "date", sorttype: "date",
                formatoptions: {newformat: 'd-m-Y'}, datefmt: 'd-m-Y', classes: 'js-contrat-date js-contrat'},
            {name: 'contrat-pdf', index: 'contrat-pdf', editable: false, align: "center", classes: 'js-p-gen-code js-contrat'},
            {name: 'contrat-modif', index: 'contrat-modif', editable: false, align: "center", width: 150, fixed: true,
                formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: "1:0"}, classes: 'js-contrat-modif js-contrat'},
            {name: 'action', index: 'action', width: 80, fixed: true, align: "center", sortable: false,
                editoptions: {defaultValue: '<i class="fa fa-edit icon-action js-edit-contrat" title="Modifier"></i><i class="fa fa-trash icon-action js-delete-contrat" title="Supprimer"></i>'},
                classes: 'js-contrat-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel_contrat) {
                contrat_grid.restoreRow(lastsel_contrat);
                lastsel_contrat = id;
            }
            contrat_grid.editRow(id);
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
            if (contrat_grid.closest('.ui-jqgrid').find('#btn-add-contrat').length == 0) {
                contrat_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-add-contrat" class="btn btn-outline btn-primary btn-xs">Ajouter un contrat</button></div>');
            }
        },
        ajaxRowOptions: {async: true},
        reloadGridOptions: { fromServer: true }
    });

    //Width Jqgrid dans tabs
    $(document).on("click", ".jqgrid-tabs a", function () {
        contrat_grid.jqGrid("setGridWidth", contrat_grid.closest(".panel-body").width());
    });


    //Ajouter un nouveau contrat
    $(document).on('click', '#btn-add-contrat', function(event) {
        event.preventDefault();
        showModalContrat();
        $('#client-add').prop('disabled', false);
    });

    //Modifier un contrat
    $(document).on('click', '.js-edit-contrat, .js-contrat', function(event) {
        event.preventDefault();
        event.stopPropagation();
        showModalContrat();

        var id = $(this).closest('tr').attr('id');
        $('#contrat-id').val(id);
        $('#client-add').prop('disabled', true);

        $.ajax({
            url: Routing.generate('fact_contrat_detail', {contrat: id}),
            type: 'GET',
            success: function(res) {
                res = $.parseJSON(res);
                $('#client-add').val(res.data.client_id);
                $('#date-signature').val(res.data.date_signature);
                if (res.data.autoriser_modif) {
                    $('#allow-tarif-edit').iCheck('check');
                } else {
                    $('#allow-tarif-edit').iCheck('uncheck');
                }

                var list_group_item = '';
                $.each(res.data.fichiers, function(index, item) {
                    list_group_item += '<li data-id="' + item.id + '" class="list-group-item file-contrat-item"><a target="_blank" href="' + item.filepath + '">';
                    list_group_item += '<i class="fa fa-file-pdf-o file-pdf"></i>' + item.filename + '</a>';
                    list_group_item += '<i class="fa fa-trash pull-right delete-file-pdf" title="Supprimer le fichier"></i></li>';
                });
                $('#contrat-file-list').html(list_group_item);
            }
        });
    });

    //Enregistrer/mettre à jour un  contrat
    $('#btn-save-contrat').on('click', function(event) {
       event.preventDefault();
       $('#pdf-contrat').fileinput('upload');
    });

    //Supprimer un contrat
    $(document).on('click', '.js-delete-contrat', function(event) {
       event.preventDefault();
       event.stopPropagation();
        var rowid = $(this).closest('tr').attr('id');
        contrat_grid.jqGrid('delGridRow', rowid, {
            url: Routing.generate('fact_contrat_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

    $('#pdf-contrat').on('filebatchuploadsuccess', function(event, data, previewId, index) {
        reloadGrid(contrat_grid, Routing.generate('fact_contrat_liste'));
        $('#contrat-modal').modal('hide');
    });

    //Supprimer un fichier contrat
    $(document).on('click', '.delete-file-pdf', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var item = $(this).closest('.file-contrat-item');
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
                url: Routing.generate('fact_contrat_fichier_remove'),
                type: 'DELETE',
                data: {
                    id: id
                },
                success: function(data) {
                    item.remove();
                    show_info('Contrat', 'Fichier supprimé.', 'info');
                }
            });
        });


    });


    function showModalContrat() {
        $('#allow-tarif-edit').iCheck({
            checkboxClass: 'icheckbox_square-green'
        });

        $('#contrat-id').val('');
        $('#date-signature').val('');
        $('#client-add').val('');
        $('#pdf-contrat').fileinput('destroy');
        $('#contrat-file-list').empty();
        $('#allow-tarif-edit').iCheck('uncheck');

        $('#pdf-contrat').fileinput({
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
            uploadUrl: Routing.generate('fact_contrat_add'),
            uploadExtraData:function(previewId, index) {
                var data = {
                    id: $('#contrat-id').val(),
                    client: $('#client-add').val(),
                    date_signature: $('#date-signature').val(),
                    allow_tarif_edit: $('#allow-tarif-edit').prop('checked') === true ? 1 : 0
                };
                return data;
            }
        });
        $('#date-signature').datepicker({
            format:'dd-mm-yyyy',
            language: 'fr',
            autoclose:true,
            todayHighlight: true,
            clearBtn: true
        });

        $('#contrat-modal').modal('show');
        modalDraggable();
    }
});
