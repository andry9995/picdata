/**
 * Created by TEFY on 06/04/2017.
 */

$(function() {
    /* Activer js-switch */
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            size: 'small',
            color: '#18a689'
        });
    });

    modalDraggable();

    $('.navbar-minimalize').click(function () {
        updateTableauGridSize();
    });

    var tableau_client = $('#tableau-client');
    var tableau_exercice = $('#tableau-exercice');
    var tableau_site = $('#tableau-site');
    var tableau_dossier = $('#tableau-dossier');
    var modal_categorie = $('#param-categorie-modal');
    var modal_periode = $('#param-periode-modal');

    var tableau_type_date = $('#tableau-type-date');

    var current_year = moment().format('YYYY');
    tableau_exercice.val(current_year);

    if (tableau_client.find('option').length === 1) {
        getSites(tableau_client, tableau_site, tableau_dossier, tableau_exercice);
    }

    tableau_client
        .closest('.form-group')
        .css('margin-bottom', '0');
    tableau_exercice
        .closest('.form-group')
        .css('margin-bottom', '10px');

    var window_height = window.innerHeight;

    var lastsel_tableau;
    var tableau_grid = $('#js_tableau');
    var tableau_image_container = $('#tableau-image').find('.panel-body');
    tableau_image_container.height(window_height - 170);
    var tableau_grid_height = tableau_image_container.height() - 110;
    var grid_width = tableau_grid.closest(".panel-body").width();

    var lastsel_tb_categorie;
    var tb_categorie_grid = $('#js_tb_categorie');
    var tb_categorie_col_model;

    var lastsel_tb_periode;
    var tb_periode_grid = $('#js_tb_periode');



    //Resize Document
    $(window).resize(function() {
        updateTableauGridSize();
    });

    function instance_tdi() {

        var colNames = ['#', 'Dossiers', '', '', '', '','cloture', 'N-1', 'N', '%', 'm1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7', 'm8', 'm9', 'm10', 'm11', 'm12',
            'm13', 'm14', 'm15', 'm16', 'm17', 'm18', 'm19', 'm20', 'm21', 'm22', 'm23', 'm24'];

        var colModel = [
            {name: 'tableau-periode', index: 'tableau-periode', align: 'center', editable: false, sortable: false, width: 35, fixed: true,
                formatter: BootstrapLabelFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-periode'},
            {name: 'tableau-dossier', index: 'tableau-dossier', editable: false, sortable: false, width: 175,
                cellattr: dossierHeaderAttr, classes: 'js-tableau-dossier'},
            {name: 'tableau-access', index: 'tableau-acces', align: 'center', editable: false, sortable: false, width: 25, fixed: true,
                cellattr: dossierHeaderAttr,  classes: 'js-tableau-acces', title: false},
            {name: 'tableau-rappel', index: 'tableau-rappel', align: 'center', editable: false, sortable: false, width: 25, fixed: true,
                cellattr: dossierHeaderAttr,  classes: 'js-tableau-rappel', title: false, hidden: true},
            {name: 'tableau-tva', index: 'tableau-tva', align: 'center', editable: false, sortable: false, width: 25, fixed: true,
                cellattr: dossierHeaderAttr,  classes: 'js-tableau-tva', title: false},
            {name: 'tableau-info', index: 'tableau-info', align: 'center', editable: false, sortable: false, width: 25, fixed: true,
                cellattr: dossierHeaderAttr,  classes: 'js-tableau-info', title: false},
            {name: 'tableau-cloture', index: 'tableau-cloture', align: 'center', editable: false, sortable: false, width: 55, fixed: true,
                formatter: BootstrapLabelFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-cloture'},
            {name: 'tableau-img-n-1', index: 'tableau-img-n-1', align: 'center', editable: false, sortable: false, width: 50, fixed: false,
                cellattr: dossierHeaderAttr, classes: 'js-tableau-img-n-1 hide-on-mask', title: false},
            {name: 'tableau-img-n', index: 'tableau-img-n', align: 'center', editable: false, sortable: false, width: 50, fixed: true,
                cellattr: dossierHeaderAttr, classes: 'js-tableau-img-n hide-on-mask', title: false},
            {name: 'tableau-percent', index: 'tableau-percent', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                cellattr: dossierHeaderAttr, classes: 'js-tableau-percent hide-on-mask'},
            {name: 'tableau-m1', index: 'tableau-m1', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m1 hide-on-mask', title: false },
            {name: 'tableau-m2', index: 'tableau-m2', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m2 hide-on-mask', title: false},
            {name: 'tableau-m3', index: 'tableau-m3', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m3 hide-on-mask', title: false},
            {name: 'tableau-m4', index: 'tableau-m4', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m4 hide-on-mask', title: false},
            {name: 'tableau-m5', index: 'tableau-m5', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m5 hide-on-mask', title: false},
            {name: 'tableau-m6', index: 'tableau-m6', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m6 hide-on-mask', title: false},
            {name: 'tableau-m7', index: 'tableau-m7', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m7 hide-on-mask', title: false},
            {name: 'tableau-m8', index: 'tableau-m8', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m8 hide-on-mask', title: false},
            {name: 'tableau-m9', index: 'tableau-m9', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m9 hide-on-mask', title: false},
            {name: 'tableau-m10', index: 'tableau-m10', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m10 hide-on-mask', title: false},
            {name: 'tableau-m11', index: 'tableau-m11', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m11 hide-on-mask', title: false},
            {name: 'tableau-m12', index: 'tableau-m12', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m12 hide-on-mask', title: false},
            {name: 'tableau-m13', index: 'tableau-m13', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m13 hide-on-mask', title: false},
            {name: 'tableau-m14', index: 'tableau-m14', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m14 hide-on-mask', title: false},
            {name: 'tableau-m15', index: 'tableau-m15', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m15 hide-on-mask', title: false},
            {name: 'tableau-m16', index: 'tableau-m16', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m16 hide-on-mask', title: false},
            {name: 'tableau-m17', index: 'tableau-m17', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m17 hide-on-mask', title: false},
            {name: 'tableau-m18', index: 'tableau-m18', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m18 hide-on-mask', title: false},
            {name: 'tableau-m19', index: 'tableau-m19', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m19 hide-on-mask', title: false},
            {name: 'tableau-m20', index: 'tableau-m20', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m20 hide-on-mask', title: false},
            {name: 'tableau-m21', index: 'tableau-m21', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m21 hide-on-mask', title: false},
            {name: 'tableau-m22', index: 'tableau-m22', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m22 hide-on-mask', title: false},
            {name: 'tableau-m23', index: 'tableau-m23', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m23 hide-on-mask', title: false},
            {name: 'tableau-m24', index: 'tableau-m24', align: 'center', editable: false, sortable: false, width: 40, fixed: true,
                formatter: cellClickableFormatter, cellattr: dossierHeaderAttr, classes: 'js-tableau-m24 hide-on-mask', title: false}

        ];

        var options = {
            datatype   : 'local',
            localReader: {repeatitems: true},
            loadonce   : true,
            sortable   : false,
            height     : tableau_grid_height,
            width      : grid_width,
            shrinkToFit: false,
            viewrecords: true,
            scroll     : 1,
            hidegrid   : true,
            rownumbers : false,
            colNames   : colNames,
            colModel   : colModel
        }

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            tableau_grid.jqGrid('GridUnload');
            tableau_grid = $('#js_tableau');
            tableau_grid.jqGrid(options);
        }
        return tableau_grid;
    }

    function tdi_initialize_qtip() {
        tableau_grid.find('.user-list-details, .js-tableau-dossier').qtip({
            content: {
                text: function() {
                    if($(this).hasClass('user-list-details'))
                        return $(this).find('.user-list-content').html();
                    else
                        return $(this).attr('title');
                }
            },
            position: { my: 'bottom center', at: 'top center' },
            style: {
                classes: 'qtip-dark qtip-shadow',
                tip: {
                    corner: true
                }
            }
        });
    }

    function tdi_rowattr(rowData, currentObj, rowId) {
        var dossier_id = 0;
        if (rowId.indexOf('header') !== -1) {
            split = rowId.split('_');
            if (split.length > 1) {
                dossier_id = split[1];
            }
            return {'style': "background: #efefef;", 'class': 'row_header row_' + dossier_id};
        } else if (rowId.indexOf('categorie') !== -1) {
            split = rowId.split('___');
            dossier_id = 0;
            if (split.length > 3) {
                dossier_id = split[3];
            }
            return {'class': 'row_categorie row_' + dossier_id};
        } else if (rowId.indexOf('encours') !== -1) {
            split = rowId.split('___');
            dossier_id = 0;
            if (split.length > 1) {
                dossier_id = split[1];
            }
            return {'class': 'row_encours row_' + dossier_id};
        } else if (rowId.indexOf('total') !== -1) {
            split = rowId.split('_');
            dossier_id = 0;
            if (split.length > 1) {
                dossier_id = split[1];
            }
            return {'class': 'row_total row_' + dossier_id};
        } else if (rowId.indexOf('separator') !== -1) {
            split = rowId.split('_');
            dossier_id = 0;
            if (split.length > 1) {
                dossier_id = split[1];
            }
            return {'class': 'row_separator row_' + dossier_id};
        } else {
            return {};
        }
    }

    function tdi_onselectrow(id) {
        if(id && id!==lastsel_tableau){
            $('#js_tableau').restoreRow(lastsel_tableau);
            lastsel_tableau=id;
        }
        $('#js_tableau').editRow(id, true);
    }

    function tdi_loadcomplete(status_dossiers) {
        var ids = tableau_grid.getDataIDs();

        for (var i = 0; i < ids.length; i++) {
            tableau_grid.setRowData(ids[i], false, { height : 15 });
        }

        $(window).trigger('resize');

        //Statut des dossiers
        if (typeof status_dossiers !== 'undefined' && status_dossiers instanceof Array) {

            $.each(status_dossiers, function(index, item) {
                // 1=fini,2=finissable,3=attente image,4=attente reponse,9=non traitable
                if (item !== null && typeof item === 'object') {
                    if (Object.keys(item).length > 0) {
                        var dossier_id = Object.keys(item)[0];
                        var status = item[dossier_id];
                        if (status === 1) {
                            $(document).find('.row_' + dossier_id)
                                .addClass('dossier-fini');
                        } else if (status === 2) {
                            $(document).find('.row_' + dossier_id)
                                .addClass('dossier-finissable');
                        } else if (status === 3) {
                            $(document).find('.row_' + dossier_id)
                                .addClass('dossier-attente-image');
                        }
                        else if (status === 4) {
                            $(document).find('.row_' + dossier_id)
                                .addClass('dossier-attente-reponse');
                        } else if (status === 9) {
                            $(document).find('.row_' + dossier_id)
                                .addClass('dossier-non-traitable');
                        }
                    }
                }
            });
        }

        filtrerAffichage();

        tdi_initialize_qtip();
    }


    function tdi_ajax_sync(select, rows, status_dossiers, chunck_index) {

        if (select[chunck_index] === undefined) {
            //Tableau des images
            tableau_grid.jqGrid('setGridParam',{
                data: rows,
                onSelectRow: tdi_onselectrow,
                loadComplete: function() {
                    tdi_loadcomplete(status_dossiers);
                },
                rowattr: tdi_rowattr
            }).trigger('reloadGrid', [{
                page: 1,
                current: true
            }]);

        } else{

            var dossier_list = select[chunck_index],
                url          = Routing.generate('tableau_image_liste'),
                data_post    = {
                client      : tableau_client.val(),
                site        : tableau_site.val(),
                exercice    : tableau_exercice.val(),
                typedate    : tableau_type_date.val(),
                dossier_list: dossier_list
            };

            $.ajax({
                url : url,
                type : 'POST',
                data : data_post,
                success : function(data) {
                    tdi_ajax_success_reccursive(data,rows,status_dossiers,chunck_index,select)
                },
                error: function(e) {
                    console.log('erreur dossier ' + chunck_index);
                    chunck_index += 1;
                    tdi_ajax_sync(select,rows,status_dossiers,chunck_index);
                }
            });
        }
    }

    function tdi_ajax_success_reccursive(data,rows,status_dossiers,chunck_index,select) {
        for(var i = 0; i < data['rows'].length; i++){
            rows.push(data['rows'][i]);
        }

        for(var i = 0; i < data['status_dossiers'].length; i++){
            status_dossiers.push(data['status_dossiers'][i]);
        }

        chunck_index += 1;
        tdi_ajax_sync(select,rows,status_dossiers,chunck_index);
    }

    function array_chunck(array,size) {
        let result_array = [];
        let chunk_size = array.length/size;
        for(i=0; i<chunk_size; i++) {
            result_array.push(array.splice(0, size));
        }
        return result_array;
    }

    $(document).on('click','#btn-go-tableau',function(event) {

        event.preventDefault();

        let rows            = [];
        let status_dossiers = [];
        var tous            = false,
            select          = [];

        // Dossier différent de Tous
        if ($('#tableau-dossier').val() !== '0') {
            var id_crypter = $('#tableau-dossier').val();
            select.push(id_crypter);

        } else {
            tous               = true;
            var select_element = $('select#tableau-dossier').find('option');
            select_element.each(function() {
                var id_crypter = $(this).val();
                if (id_crypter != '0') {
                    select.push(id_crypter);
                }
            });
        }

        tableau_grid      = instance_tdi();
        var select_chunck = array_chunck(select,100);

        tdi_ajax_sync(select_chunck, rows, status_dossiers,0);

    });

    //Selection clients
    tableau_client.on('change', function(event) {
        event.preventDefault();
        clearGrid(tableau_grid);
        getSites(tableau_client, tableau_site, tableau_dossier, tableau_exercice);
    });

    //Selection sites:
    tableau_site.on('change', function(event) {
        event.preventDefault();
        clearGrid(tableau_grid);
        getDossiers(tableau_client.val(), tableau_site, tableau_dossier, tableau_exercice);
    });

    //Selection exercice
    tableau_exercice.on('change', function(event) {
        event.preventDefault();
        clearGrid(tableau_grid);
        getDossiers(tableau_client.val(), tableau_site, tableau_dossier, tableau_exercice);
    });

    tableau_type_date.on('change', function(event) {
        event.preventDefault();
        clearGrid(tableau_grid);
    });

    //Selection dossier
    tableau_dossier.on('change', function(event) {
        event.preventDefault();
        clearGrid(tableau_grid);
    });

    //Charger site dossier au premier affichage
    tableau_client.trigger('change');

    //Affichage paramètre categorie
    $(document).on('click', '#btn-tb-categorie', function(event) {
        event.preventDefault();

        if (tableau_client.find('option:selected').text().trim() !== '') {
            $('#param-categorie-modal').modal('show');
        } else {
            show_info("", "Séléctionner un client.", "warning");
        }
    });

    //Parametre catégorie modal
    $(document).on('shown.bs.modal', '#param-categorie-modal', function() {
        tb_categorie_grid.jqGrid('GridUnload');
        tb_categorie_grid = $('#js_tb_categorie');
        var client = tableau_client.val();
        $.ajax({
            url: Routing.generate('tableau_image_categorie', {client: client, site: tableau_site.val(), exercice: tableau_exercice.val()}),
            type: 'GET',
            success: function (data) {
                data = $.parseJSON(data);
                var col_names = data.col_names;
                var col_model = data.col_model;
                var rowData = data.rowData;
                tb_categorie_col_model = col_model;

                tb_categorie_grid.jqGrid({
                    datatype: 'local',
                    data: rowData,
                    sortable: true,
                    width: 840,
                    height: 400,
                    autowidth: true,
                    shrinkToFit: true,
                    viewrecords: true,
                    rowNum: 5000,
                    rowList: [5000, 10000, 20000],
                    rownumbers: true,
                    rownumWidth: 35,
                    pager: '#pager_tb_categorie',
                    hidegrid: false,
                    colNames: col_names,
                    colModel: col_model,
                    idPrefix: 'categorie_',
                    onSelectRow: function (id) {
                        if (id && id !== lastsel_tb_categorie) {
                            tb_categorie_grid.restoreRow(lastsel_tb_categorie);
                            lastsel_tb_categorie = id;
                        }
                        tb_categorie_grid.editRow(id, false);
                    }
                });
            }
        })
    });

    //Modif categorie
    $(document).on('change', '.js-tb-categorie-check>input[type="checkbox"]', function(event) {
        event.preventDefault();
        //Gerer Centralisation caisse / client / caisse
        if ($(this).closest('td').hasClass('js-tb-categorie-centr-caisse')) {
            $(this).closest('tr')
                .find('.code_caisse>input[type="checkbox"]')
                .prop('checked', false);
            $(this).closest('tr')
                .find('.code_client>input[type="checkbox"]')
                .prop('checked', false);
        } else if ($(this).closest('td').hasClass('code_caisse') || $(this).closest('td').hasClass('code_client'))
        {
            $(this).closest('tr')
                .find('.js-tb-categorie-centr-caisse>input[type="checkbox"]')
                .prop('checked', false);
        }
        var rowid = $(this).closest('tr').attr('id');

        var tmp = rowid.split("_");
        var dossier_id = 0;
        if (tmp.length === 2) {
            dossier_id = tmp[1];
        }
        var rowdata = tb_categorie_grid.jqGrid('getRowData', rowid);
        $.ajax({
            url: Routing.generate('tableau_image_categorie_edit', {dossier: dossier_id}),
            type: 'POST',
            data: {
                rowdata: rowdata
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.erreur === true) {
                    show_info("", data.erreur_text, "error");
                } else {
                    setCheckAll($(document).find('.categ-check-all[data-code="code_client"]'), '.js-tb-categorie-check.code_client');
                    setCheckAll($(document).find('.categ-check-all[data-code="code_banque"]'), '.js-tb-categorie-check.code_banque');
                }
            }
        });
    });

    //Selectionner tout
    $(document).on('change', '#param-categorie-modal .categ-check-all', function() {
        var checkbox = $(this);
        var value = checkbox.prop('checked');
        var categorie = checkbox.attr('data-categorie');
        var code = checkbox.attr('data-code');

        //Confirmation
        swal({
            title: 'Attention',
            text: "Voulez-vous modifier cette catégorie pour tous les dossiers ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1ab394',
            cancelButtonColor: '#f8ac59',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler'
        }).then(function () {
            //Si OK
            if (categorie === '0') {
                //Centralisation caisse
                $(document).find('.js-tb-categorie-centr-caisse>input[type="checkbox"]')
                    .prop('checked', value);
                $(document).find('.js-tb-categorie-check.code_client>input[type="checkbox"]')
                    .prop('checked', false);
                $(document).find('.js-tb-categorie-check.code_caisse>input[type="checkbox"]')
                    .prop('checked', false);
            } else {
                //Catégories
                $(document).find('.js-tb-categorie-check.' + code + '>input[type="checkbox"]')
                    .prop('checked', value);
                if (code === 'code_client' || code === 'code_caisse') {
                    $(document).find('.js-tb-categorie-centr-caisse>input[type="checkbox"]')
                        .prop('checked', false);
                }
            }
            $.ajax({
                url: Routing.generate('tableau_image_categorie_edit_all',
                    {client: tableau_client.val(), site: tableau_site.val(), categorie: categorie, exercice: tableau_exercice.val()}
                ),
                type: 'POST',
                data: {
                    value: value ? 1 : 0
                },
                success: function(data) {
                    console.log(data);
                }
            });
        }, function() {
            // Si Annuler
            checkbox.prop('checked', !value);
        });

    });

    //Affichage paramètre période
    $(document).on('click', '#btn-tb-periode', function(event) {
        event.preventDefault();

        if (tableau_client.find('option:selected').text().trim() !== '') {
            $('#param-periode-modal').modal('show');
        } else {
            show_info("", "Séléctionner un client.", "warning");
        }
    });


    //Parametre période modal
    $(document).on('shown.bs.modal', '#param-periode-modal', function() {
        // tb_periode_grid.jqGrid('GridUnload');
        tb_periode_grid = $('#js_tb_periode');
        var client = tableau_client.val();
        var periodicite_items = {"M": "Mensuel", "T": "Trimestriel", "Q": "Quadrimestriel", "S": "Semestriel", "A": "Annuel"};
        var mois_plus_items = {"1": "1", "2": "2", "3": "3", "4": "4", "5": "5", "6": "6", "7": "7", "8": "8", "9": "9", "10": "10",
            "11": "11", "12": "12"};
        var jour_items = {"1": "1", "2": "2", "3": "3", "4": "4", "5": "5", "6": "6", "7": "7", "8": "8", "9": "9", "10": "10",
            "11": "11", "12": "12", "13": "13", "14": "14", "15": "15", "16": "16", "17": "17", "18": "18", "19": "19", "20": "20",
            "21": "21", "22": "22", "23": "23", "24": "24", "25": "25", "26": "26", "27": "27", "28": "28", "29": "29", "30": "30", "31": "31"};

        // var colNames = ['Dossier',
        //     'Démarrage<br><input id="demarrage-tout" style="width:80px;" onclick="checkHeaderClick(event)"><br>' +
        //     '<i id="demarrage-tout-clear" class="fa fa-close fa-lg" style="color:#f8ac59" title="Effacer tout" onclick="resetDemarrage(event)"></i>',
        //     '1<exp>ère</exp> cloture<br><input id="premiere-cloture-tout" style="width:80px;" onclick="checkHeaderClick(event)"><br>' +
        //     '<i id="premiere-cloture-tout-clear" class="fa fa-close fa-lg" style="color:#f8ac59" title="Effacer tout" onclick="resetPremiereCloture(event)"></i>',
        //     'Périodicité<br><select id="periode-tout" style="width:80px;" onclick="checkHeaderClick(event)"></select>',
        //     'Mois+<br><select id="mois-plus-tout" style="width:80px;" onclick="checkHeaderClick(event)"></select>',
        //     'Jour<br><select id="jour-tout" style="width:80px;" onclick="checkHeaderClick(event)"></select>'];
        var colNames = ['Dossier',
            'Démarrage<br><i class="fa fa-info-circle fa-lg with-tooltip" title="C\'est la date de début d\'activité renseignée dans le paramétrage des dossiers."></i>',
            '1<exp>ère</exp> cloture<br><i class="fa fa-info-circle fa-lg with-tooltip" title="C\'est la date de première clôture renseignée dans le paramétrage des dossiers."></i>',
            'Période compta<br><select class="periode-tout-edit" id="periode-tout" style="width:80px;" onclick="checkHeaderClick(event)"></select>',
            'Période pièces<br><select class="periode-tout-edit" id="periode-piece-tout" style="width:80px;" onclick="checkHeaderClick(event)"></select>',
            'Mois+<br><select id="mois-plus-tout" style="width:50px;" onclick="checkHeaderClick(event)"></select>',
            'Jour<br><select id="jour-tout" style="width:50px;" onclick="checkHeaderClick(event)"></select>'];
        var colModel =  [
            {name: 'tb-periode-dossier', index: 'tb-periode-dossier', align: 'left', editable: false, sortable: true, width: 400, classes: 'js-tb-periode-dossier'},
            {name: 'tb-periode-demarrage', index: 'tb-periode-demarrage', align: 'center', editable: false, sortable: true, width: 100, fixed: true,
                sorttype: 'date', formatter: 'date',
                formatoptions: {
                    newformat: "d-m-Y"
                },
                datefmt: 'd-m-Y',
                editoptions : {
                    dataInit: function (el) {
                        $(el).datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true, clearBtn: true})
                            .on('changeDate', function() {
                                var rowid = $(el).closest('tr').attr('id');
                                setTimeout(function() {
                                    saveParametrePeriode(rowid);
                                }, 0);
                            })
                            .on('clearDate', function() {
                                var rowid = $(el).closest('tr').attr('id');
                                setTimeout(function() {
                                    saveParametrePeriode(rowid);
                                }, 0);
                            });
                    }
                },
                classes: 'js-tb-periode-demarrage'},
            {name: 'tb-periode-cloture', index: 'tb-periode-cloture', align: 'center', editable: false, sortable: true, width: 100, fixed: true,
                sorttype: 'date', formatter: 'date',
                formatoptions: {
                    newformat: "d-m-Y"
                },
                datefmt: 'd-m-Y',
                editoptions : {
                    dataInit: function (el) {
                        $(el).datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true, clearBtn: true})
                            .on('changeDate', function() {
                                var rowid = $(el).closest('tr').attr('id');
                                setTimeout(function() {
                                    saveParametrePeriode(rowid);
                                }, 0);
                            });
                    }
                },
                classes: 'js-tb-periode-cloture'},
            {name : 'tb-periode-periode', index : 'tb-periode-periode', align : "center", editable: true, sortable: true, width: 150, formatter: 'select', edittype: 'select',
                editoptions: {
                    value: periodicite_items,
                    dataInit: function(el) {
                        $(el).on('change', function() {
                            var rowid = $(el).closest('tr').attr('id');
                            setTimeout(function() {
                                saveParametrePeriode(rowid);
                            }, 0);
                        });
                    }
                }, classes: 'js-tb-periode-periode'},
            {name : 'tb-periode-piece', index : 'tb-periode-piece', align : "center", editable: true, sortable: true, width: 150, formatter: 'select', edittype: 'select',
                editoptions: {
                    value: periodicite_items,
                    dataInit: function(el) {
                        $(el).on('change', function() {
                            var rowid = $(el).closest('tr').attr('id');
                            setTimeout(function() {
                                saveParametrePeriode(rowid);
                            }, 0);
                        });
                    }
                }, classes: 'js-tb-periode-piece'},
            {name : 'tb-periode-mois-plus', index : 'tb-periode-mois-plus', align : "center", editable: true, sortable: true, width: 60, fixed: true, formatter: 'select', edittype: 'select',
                editoptions: {
                    value: mois_plus_items,
                    dataInit: function(el) {
                        $(el).on('change', function() {
                            var rowid = $(el).closest('tr').attr('id');
                            setTimeout(function() {
                                saveParametrePeriode(rowid);
                            }, 0);
                        });
                    }
                }, classes: 'js-tb-periode-mois-plus'},
            { name : 'tb-periode-jour', index : 'tb-periode-jour', align : "center", editable: true, sortable: true, width: 60, fixed: true, formatter: 'select', edittype: 'select',
                editoptions: {
                    value: jour_items,
                    dataInit: function(el) {
                        $(el).on('change', function() {
                            var rowid = $(el).closest('tr').attr('id');
                            setTimeout(function() {
                                saveParametrePeriode(rowid);
                            }, 0);
                        });
                    }
                }, classes: 'js-tb-periode-jour'}
        ];

        tb_periode_grid.jqGrid({
            datatype: 'local',
            loadonce: true,
            sortable: true,
            width: 840,
            height: 400,
            shrinkToFit: true,
            viewrecords: true,
            rowNum: 5000,
            rowList: [5000, 10000, 20000],
            rownumbers: true,
            rownumWidth: 35,
            pager: '#pager_tb_periode',
            hidegrid: false,
            idPrefix: 'periode_',
            colNames: colNames,
            colModel: colModel,
            onSelectRow: function (id, status, e) {
                if (id && id !== lastsel_tb_periode) {
                    tb_periode_grid.restoreRow(lastsel_tb_periode);
                    lastsel_tb_periode = id;
                }
                tb_periode_grid.editRow(id, false);

                setTimeout(function() {
                    var row = $(e.target).closest('tr');
                    row.find('.js-tb-periode-demarrage')
                        .find("input").datepicker('hide');
                    $(e.target).find("input,select").focus();
                }, 0);
            },
            loadComplete: function() {
                //Modifier démarrage tous
                modal_periode
                    .find('#demarrage-tout')
                    .datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true})
                    .on('changeDate', function(e) {
                        var date_demarrage = moment(e.date).format('DD-MM-YYYY');
                        //Confirmation
                        swal({
                            title: 'Attention',
                            text: "Voulez-vous modifier la date de démarrage de tous les dossiers en " + date_demarrage + " ?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1ab394',
                            cancelButtonColor: '#f8ac59',
                            confirmButtonText: 'Oui',
                            cancelButtonText: 'Annuler'
                        }).then(function () {
                            //Si OK
                            var field = 'Demarrage';
                            saveParamPeriodeTous(field, date_demarrage, function() {
                                modal_periode
                                    .find('.js-tb-periode-demarrage')
                                    .text(date_demarrage);
                            });
                        }, function() {
                            // Si Annuler
                            modal_periode
                                .find('#demarrage-tout')
                                .val('');
                        });
                    });

                //Modifier première  cloture tous
                modal_periode
                    .find('#premiere-cloture-tout')
                    .datepicker({format:'dd-mm-yyyy', language: 'fr', autoclose:true, todayHighlight: true, clearBtn: true})
                    .on('changeDate', function(e) {
                        var date_premiere_cloture = moment(e.date).format('DD-MM-YYYY');
                        //Confirmation
                        swal({
                            title: 'Attention',
                            text: "Voulez-vous modifier la date de première cloture de tous les dossiers en " + date_premiere_cloture + " ?",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1ab394',
                            cancelButtonColor: '#f8ac59',
                            confirmButtonText: 'Oui',
                            cancelButtonText: 'Annuler'
                        }).then(function () {
                            //Si OK
                            var field = 'PremiereCloture';
                            saveParamPeriodeTous(field, date_premiere_cloture, function() {

                            });

                        }, function() {
                            // Si Annuler
                            modal_periode
                                .find('#premiere-cloture-tout')
                                .val('');
                        });
                    });

                //Periodicité Tout
                var options_periode = '<option value=""></option>';
                $.each(periodicite_items, function(index, item) {
                    options_periode += '<option value="' + index + '">' + item + '</option>';
                });
                modal_periode.find('#periode-tout')
                    .html(options_periode);
                modal_periode.find('#periode-piece-tout')
                    .html(options_periode);


                //Mois plus Tout
                var options_mois_plus = '<option value=""></option>';
                $.each(mois_plus_items, function(index, item) {
                    options_mois_plus += '<option value="' + index + '">' + item + '</option>';
                });
                modal_periode
                    .find('#mois-plus-tout')
                    .html(options_mois_plus);

                //Jour Tout
                var options_jour = '<option value=""></option>';
                $.each(jour_items, function(index, item) {
                    options_jour += '<option value="' + index + '">' + item + '</option>';
                });
                modal_periode
                    .find('#jour-tout')
                    .html(options_jour);

                modal_periode.find('.with-tooltip').qtip({
                    // position: {my: 'top center', at: 'bottom right'},
                    style: {
                        classes: 'qtip-tipsy'
                    }
                });

            },
            ajaxRowOptions: {async: true}
        });

        setTimeout(function() {
            tb_periode_grid.setGridParam({
                url: Routing.generate('tableau_image_periode', {client: client, site: tableau_site.val(), exercice: tableau_exercice.val()}),
                datatype: 'json'
            });
            tb_periode_grid.trigger("reloadGrid", [{page: 1, current: true}]);
        }, 500);
    });

    //Modif periodicité tout
    $(document).on('change', '.periode-tout-edit', function(event) {
        event.preventDefault();
        var periode = $(this).val();
        var periode_text = $(this).find('option:selected').text();
        var id = $(this).attr('id');
        if (periode !== '') {
            //Confirmation
            swal({
                title: 'Attention',
                text: "Voulez-vous modifier la périodicité de tous les dossiers en " + periode_text + " ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                cancelButtonColor: '#f8ac59',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then(function () {
                //Si OK
                var field;
                if (id === 'periode-tout') {
                    field = 'Periode';
                } else if (id === 'periode-piece-tout') {
                    field = 'PeriodePiece';
                }

                if (field === 'Periode') {
                    saveParamPeriodeTous(field, periode, function () {
                        modal_periode
                            .find('.js-tb-periode-periode')
                            .text(periode_text);
                    });
                } else if (field === 'PeriodePiece') {
                    saveParamPeriodeTous(field, periode, function () {
                        modal_periode
                            .find('.js-tb-periode-piece')
                            .text(periode_text);
                    });
                }
            }, function () {
                // Si Annuler
                modal_periode
                    .find('#periode-tout')
                    .val('');
            });
        } else {
            swal({
                title: 'Périodicité',
                text: "La périodicité ne peut pas être vide.",
                type: 'warning'
            })
        }
    });

    //Modif mois_plus tout
    $(document).on('change', '#mois-plus-tout', function(event) {
        event.preventDefault();
        var mois_plus = $(this).val();

        if (mois_plus !== '') {
            //Confirmation
            swal({
                title: 'Attention',
                text: "Voulez-vous modifier le mois plus de tous les dossiers en " + mois_plus + " ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                cancelButtonColor: '#f8ac59',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then(function () {
                //Si OK
                var field = 'MoisPlus';
                saveParamPeriodeTous(field, mois_plus, function () {
                    modal_periode
                        .find('.js-tb-periode-mois-plus')
                        .text(mois_plus);
                });
            }, function () {
                // Si Annuler
                modal_periode
                    .find('#mois-plus-tout')
                    .val('');
            });
        } else {
            swal({
                title: 'Périodicité',
                text: "Le mois plus ne peut pas être vide.",
                type: 'warning'
            });
        }
    });

    //Modif jour tout
    $(document).on('change', '#jour-tout', function(event) {
        event.preventDefault();
        var jour = $(this).val();

        if (jour !== '') {
            //Confirmation
            swal({
                title: 'Attention',
                text: "Voulez-vous modifier le jour de tous les dossiers en " + jour + " ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                cancelButtonColor: '#f8ac59',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then(function () {
                //Si OK
                var field = 'Jour';
                saveParamPeriodeTous(field, jour, function () {
                    modal_periode
                        .find('.js-tb-periode-jour')
                        .text(jour);
                });
            }, function () {
                // Si Annuler
                modal_periode
                    .find('#jour-tout')
                    .val('');
            });
        } else {
            swal({
                title: 'Périodicité',
                text: "Le jour ne peut pas être vide.",
                type: 'warning'
            })
        }
    });

    //FILTRER AFFICHAGE
    $(document).on('change', 'input[name="show-filter-item"]', function() {
        filtrerAffichage();
    });

    //MASQUER CATEGORIE
    $(document).on('change', '#show-categories', function() {
        showCategorie();
    });

    // MAJ SIZE GRID ON CLICK TAB
    $(document).on('click', 'a[href="#tableau-image"]', function() {
        setTimeout(function() {
            updateTableauGridSize();
        }, 0);
    });




    $(document).on('click', '.edit-rappel', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var tr = $(this).closest('tr'),
            rowId = tr.attr('id'),
            dossier_nom = tr.find('.js-tableau-dossier').text(),
            dossier_id = 0,
            split = rowId.split('_'),
            lien = Routing.generate('tableau_image_rappel_param');


        if (split.length > 1) {
            dossier_id = split[1];
        }

        var lien_historique = Routing.generate('tableau_image_rappel_historique', {dossierid: dossier_id});
        $('#rappel-param-modal-title').text('Dossier : ' + dossier_nom );

        $.ajax({
            url: lien,
            type: 'POST',
            data: { dossier_id: dossier_id},
            datatype: 'html',
            success: function (data) {
                $('#rappel-param-form').html(data);
                $('#notification-dossier-id').val(dossier_id);

                var config = {
                    '.chosen-select'           : {},
                    '.chosen-select-deselect'  : {allow_single_deselect:true},
                    '.chosen-select-no-single' : {disable_search_threshold:10},
                    '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                    '.chosen-select-width'     : {width:"95%"}
                };

                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }

                $('.date').datepicker({
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    todayBtn: "linked",
                    language: "fr",
                    startView: 1
                });

                ready_inspinia();

                var historique_grid = $('#table-rappel-historique');

                historique_grid.jqGrid('GridUnload');

                historique_grid.jqGrid({
                    datatype: 'json',
                    url: lien_historique,
                    loadonce: true,
                    sortable: false,
                    height: 160,
                    width: 860,
                    shrinkToFit: true,
                    autowidth: true,
                    viewrecords: true,
                    hidegrid: false,
                    rownumbers: true,
                    rownumWidth: 35,
                    colNames: ['Date Création',  'Sujet', 'Status'],
                    colModel: [
                        { name: 'tb-historique-envoi', index: 'tb-historique-envoi', align: 'center', width: 90, fixed: true, editable: false, sortable: true,  sorttype: 'date',formatter: 'date', formatoptions: { newformat: "d-m-Y"}, datefmt: 'd-m-Y',classes: 'js-tb-historique-envoi'},
                        { name: 'tb-historique-sujet', index: 'tb-historique-sujet', align: 'center', editable: false, sortable: true , classes: 'js-tb-historique-sujet'},
                        { name: 'tb-historique-status', index: 'tb-historique-status', align: 'center', width: 90, fixed: true, editable: false, sortable: true, classes: 'js-tb-historique-status'}
                    ]
                });

                historique_grid.find('tr').each(function () {
                    $(this).addClass("pointer");
                });

                historique_grid.on('click', 'tr',  function () {
                    previewEmail($(this));
                });
            }
        });

        $('#rappel-param-modal').modal('show');
    });

    $(document).on('click', '#rappel-frequence', function(){
        var editor = document.getElementById('rappel-email-content');
        editor.insertAtCaret("[[frequence]]");
    });

    $(document).on('click', '#rappel-default-content', function(){
        var editor = $('#rappel-email-content');
        var lien = Routing.generate('rappel_image_default_content');
        $.ajax({
            url: lien,
            type: 'GET',
            datatype: 'html',
            success: function (data) {
                editor.val(data);
            }
        })
    });

    $(document).on('click', '#btn-save-rappel-param', function () {
        var form = $('#rappel-param-form'),
            form_serialized = form.serialize(),
            lien = Routing.generate('tableau_image_rappel_param_edit');
        $.ajax({
            url: lien,
            type: 'POST',
            data: form_serialized,
            datatype: 'json',
            success: function (data) {
                show_info('Rappel image', data.message, data.type);
            }
        });
    });




    function showAll() {
        tableau_grid.removeClass('attente-image-show fini-show finissable-show attente-reponse-show non-traitable-show');
    }
    function showCategorie() {
        var show_categorie_check = $('#show-categories');
        if (show_categorie_check.prop('checked')) {
            tableau_grid.addClass('hidden-categorie');
        } else {
            tableau_grid.removeClass('hidden-categorie');
        }
    }
    function filtrerAffichage() {
        // 1=fini,2=finissable,3=attente image,4=attente reponse,9=non traitable
        tableau_grid = $('#js_tableau');
        var selected = $(document).find('input[name="show-filter-item"]:checked').val();
        var nb = 0;
        setTimeout(function() {
            showAll();
            showCategorie();
            if (selected === '0') {
                nb = tableau_grid.find('.row_header').length;
                // console.log(nb);
            } else if (selected === '1') {
                tableau_grid.addClass('fini-show');
                nb = tableau_grid.find('.row_header.dossier-fini').length;
            } else if (selected === '2') {
                tableau_grid.addClass('finissable-show');
                nb = tableau_grid.find('.row_header.dossier-finissable').length;
            } else if (selected === '3') {
                tableau_grid.addClass('attente-image-show');
                nb = tableau_grid.find('.row_header.dossier-attente-image').length;
            } else if (selected === '4') {
                tableau_grid.addClass('attente-reponse-show');
                nb = tableau_grid.find('.row_header.dossier-attente-reponse').length;
            } else if (selected === '9') {
                tableau_grid.addClass('non-traitable-show');
                nb = tableau_grid.find('.row_header.dossier-non-traitable').length;
            }
            // MAJ height tableau
            $(document).find('#tableau-image .ui-jqgrid-bdiv > div').css('height', 'auto');
            // MAJ nombre dossier
            $('#tableau-nb-dossier').text(nb);
            // $('#count-dossiers').text(nb + ' Dossiers');

        }, 100);


    }

    function updateTableauGridSize() {
        window_height = window.innerHeight;
        tableau_grid = $('#js_tableau');
        tableau_image_container = $('#tableau-image').find('.panel-body');
        tableau_image_container.height(window_height - 100);
        tableau_grid_height = tableau_image_container.height() - 175;

        setTimeout(function() {
            setGridHeight(tableau_grid, tableau_grid_height);
            tableau_grid.jqGrid("setGridWidth", tableau_grid.closest(".panel-body").width());
        }, 600);
    }
});