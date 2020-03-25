
$(function() {
    var grid_envoi_ftp = $('#js_envoi_dropbox'),
        window_height = window.innerHeight,
        grid_envoi_ftp_width = grid_envoi_ftp.closest("div.row").width(),
        grid_envoi_ftp_height = window_height - 200,
        lastsel,
        numerotation_en_cours = false,
        text_fini = '<i class="fa fa-check"></i> Numérotation finie',
        text_original = '<i class="fa fa-check"></i> Numéroter',
        text_busy = '<i class="fa fa-cog fa-spin fa-fw"></i> Numérotation...';

    //jqGrid Liste images sur DropBox
    grid_envoi_ftp.jqGrid({
        url: Routing.generate('img_envoi_dropbox_liste'),
        datatype: 'json',
        loadonce: true,
        sortable: true,
        autowidth: true,
        height: grid_envoi_ftp_height,
        width: grid_envoi_ftp_width,
        shrinkToFit: true,
        viewrecords: true,
        rowNum: 5000,
        rowList: [5000, 8000, 10000],
        pager: '#pager_envoi_ftp',
        hidegrid: false,
        caption: 'Envoi images par dropbox',
        editUrl: Routing.generate('img_envoi_ftp_edit'),
        colNames: ['Type', 'Client_Id', 'Client', 'Image originale', 'Dossier', 'Exercice', 'Date scan', 'Clôture', 'Remarque', '<span class="fa fa-bookmark-o" style="display:inline-block"/> Action'],
        colModel: [
            {name: 'img-type', index: 'img-type', editable: false, sortable: true, width: 80, fixed: true, align: "center", classes: 'js-img-type'},
            {name: 'img-client-id', index: 'img-client-id', hidden: true, classes: 'js-img-client-id'},
            {name: 'img-client', index: 'img-client', sortable: true, editable: false,  width: 100, classes: 'js-img-client'},
            {name: 'img-orig', index: 'img-orig', editable: false, sortable: true, width: 100, classes: 'js-img-orig'},
            {name: 'img-dossier', index: 'img-dossier', editable: true, sortable: true, width: 100,
                edittype: 'select',
                editoptions: {
                    postData: function (rowid) {
                        var client_id = grid_envoi_ftp.find('#' + rowid)
                            .find('.js-img-client-id')
                            .text();
                        return { client: client_id }
                    },
                    dataUrl: Routing.generate('img_envoi_ftp_liste_dossier'),
                    dataInit: function (elem) { $(elem).width(100); } },
                classes: 'js-img-dossier'},
            {name: 'img-exercice', index: 'img-exercice', editable: true, sortable: true, width: 80, fixed: true, align: "center", classes: 'js-img-exercice'},
            {name: 'img-datescan', index: 'img-datescan', editable: true, sortable: true, width: 100, fixed: true, align: "center",
                sorttype: 'date', formatter: 'date',
                formatoptions: {
                    newformat: "d-m-Y"
                },
                datefmt: 'd-m-Y', classes: 'js-img-datescan'},
            {name: 'img-cloture', index: 'img-cloture', editable: false, sortable: true, width: 80, fixed: true, align: "center", classes: 'js-img-cloture'},
            {name: 'img-remarque', index: 'img-remarque', editable: false, sortable: true, width: 150, classes: 'js-img-remarque'},
            {name: 'img-action', index: 'img-action', width: 80, fixed: true, align: "center", sortable: false, classes: 'js-img-action'}
        ],
        onSelectRow: function (id) {
            if (id && id !== lastsel) {
                grid_envoi_ftp.restoreRow(lastsel);
                lastsel = id;
            }
            grid_envoi_ftp.editRow(id, false);
        },
        loadComplete: function() {
            if (grid_envoi_ftp.closest('.ui-jqgrid').find('#info').length === 0) {
                grid_envoi_ftp.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<span id="info" style="line-height: 40px;" class="label label-info"></span>');
                grid_envoi_ftp.closest('.ui-jqgrid').find('#info').after('<span id="error" style="line-height: 40px;" class="label label-danger"></span>');
            }
            if (grid_envoi_ftp.closest('.ui-jqgrid').find('#btn-refresh').length === 0) {
                grid_envoi_ftp.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px;margin-right: 2px;">' +
                    '<button id="btn-refresh" class="btn btn-default btn-sm btn-header"><i class="fa fa-refresh"></i> Rafraîchir</button></div>');
            }
            if (grid_envoi_ftp.closest('.ui-jqgrid').find('#btn-init').length === 0) {
                grid_envoi_ftp.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px;margin-right: 2px;">' +
                    '<button id="btn-init" class="btn btn-danger btn-sm btn-header"><i class="fa fa-close"></i> Réinitialiser la liste</button></div>');
            }
            if (grid_envoi_ftp.closest('.ui-jqgrid').find('#btn-numeroter').length === 0) {
                grid_envoi_ftp.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                    '<button id="btn-numeroter" class="btn btn-primary btn-sm btn-header"><i class="fa fa-check"></i> Numéroter</button></div>');
            }
            $(document).find('#info').text(grid_envoi_ftp.jqGrid('getGridParam', 'reccount') + ' images');
            setErrorNumber();
        },
        ajaxRowOptions: {async: true}
    });

    $(document).on('click', '.js-save-img', function(event) {
        event.preventDefault();
        event.stopPropagation();
        grid_envoi_ftp.jqGrid('saveRow', lastsel, {
            "aftersavefunc": function() {
                grid_envoi_ftp.setGridParam({
                    url: Routing.generate('img_envoi_ftp_liste'),
                    datatype: 'json',
                    loadonce: true,
                    page: 1
                }).trigger('reloadGrid');
                setErrorNumber();
            }
        });
    });

    $(document).on('click', '#btn-refresh', function(event) {
        event.preventDefault();

        if (!numerotation_en_cours) {
            clearGrid(grid_envoi_ftp);
            grid_envoi_ftp.setGridParam({
                url: Routing.generate('img_envoi_dropbox_liste'),
                datatype: 'json',
                loadonce: true,
                page: 1
            }).trigger('reloadGrid');
            var button = $('#btn-numeroter');
            button.removeClass('btn-primary btn-success')
                .addClass('btn-info')
                .html(text_original);
        } else {
            swal({
                title: 'Numérotation en cours!',
                text: "Merci d'attendre la numerotation en cours.",
                timer: 2000,
                type: 'warning'
            });
        }
    });

    $(document).on('click', '#btn-init', function(event) {
        event.preventDefault();

        if (!numerotation_en_cours) {
            swal({
                title: 'Réinitialiser la liste?',
                text: "Voulez-vous réinitialiser la liste des images non numérotées ?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, réinitialiser!',
                cancelButtonText: 'Annuler'
            }).then(function () {
                clearGrid(grid_envoi_ftp);
                grid_envoi_ftp.setGridParam({
                    url: Routing.generate('img_envoi_dropbox_liste', {init: 1}),
                    datatype: 'json',
                    loadonce: true,
                    page: 1
                }).trigger('reloadGrid');
                var button = $('#btn-numeroter');
                button.removeClass('btn-primary btn-success')
                    .addClass('btn-info')
                    .html(text_original);
            });
        } else {
            swal({
                title: 'Numérotation en cours!',
                text: "Merci d'attendre la numerotation en cours.",
                timer: 2000,
                type: 'warning'
            });
        }
    });

    $(document).on('click', '#btn-numeroter', function(event) {
        event.preventDefault();
        if (!numerotation_en_cours) {
            numerotation_en_cours = true;

            var button = $(this);
            button.html(text_busy);
            var url = Routing.generate('img_envoi_dropbox_numeroter');
            fetch(url, {
                method: 'POST',
                credentials: 'include'
            }).then(function(response) {
                return response.json();
            }).then(function() {
                button.removeClass('btn-primary')
                    .addClass('btn-success')
                    .html(text_fini);
                numerotation_en_cours = false;
            }).catch(function(error) {
                console.log(error);
                swal({
                    title: 'Erreur!',
                    text: "Une erreur est survenue lors de la numerotation.Merci de ré-essayer.",
                    type: 'error'
                });
                button.removeClass('btn-success')
                    .addClass('btn-primary')
                    .html(text_original);
                numerotation_en_cours = false;
            });
        } else {
            swal({
                title: 'Numérotation en cours!',
                text: "Merci d'attendre la numerotation en cours.",
                timer: 2000,
                type: 'warning'
            });
        }
    });

    function setErrorNumber() {
        var errors_count = 0;
        var remarques = grid_envoi_ftp.find('.js-img-remarque');
        $.each(remarques, function(index, item) {
            if ($(item).text() !== "" && $(item).text() !== "\xa0") {
                errors_count++;
            }
        });

        $(document).find('#error').text(errors_count + ' erreur(s)');
    }

    function clearGrid(selector) {
        var trf = selector.find("tbody:first tr:first")[0];
        selector.find("tbody:first").empty().append(trf);
    }
});
