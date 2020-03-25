// $(document).ready(function() {

	// intance_comptes_bancaires_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_comptes_bancaires_grid(data) {

		var colNames= ['', 'Aujourd\'hui', 'Dossiers'];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'center',
            editable : false,
            sortable : false,
            width    : 125,
            classes  : 'js-list',
            resizable: false
		}, {
            name     : 'n',
            index    : 'n',
            align    : 'center',
            editable : false,
            sortable : false,
            width    : 50,
            classes  : 'js-n',
            resizable: false
		},{
            name     : 'dossiers-n',
            index    : 'dossiers-n',
            align    : 'center',
            editable : false,
            sortable : false,
            width    : 50,
            classes  : 'js-dossiers-n',
            resizable: false
        }];

		var options = {
            datatype: "jsonstring",
            datastr: data,
            height     : 350,
            autowidth  : false,
            loadonce   : true,
            shrinkToFit: true,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
            caption    : 'SITUATION DES COMPTES BANCAIRES',
            sortable   : false,
            treeGrid: true,
            treeGridModel: 'adjacency',
            treedatatype: "local",
            ExpandColumn: 'name',
            loadComplete: function() {
                prepare_tooltip_cb();
            },

        };

        var tableau_grid = $('#js_comptes_bancaires');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_comptes_bancaires').GridUnload('#js_comptes_bancaires');
            tableau_grid = $('#js_comptes_bancaires');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");
        resize_grid('comptes_bancaires');

        renameElement(tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title'),'div'); 
        tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').css('margin-top','11px');
        tableau_grid.closest('.ui-jqgrid')
                    .find('table.ui-jqgrid-htable')
                    .css('padding', '6px');

        tableau_grid.jqGrid('hideCol', "dossiers-n");

        return tableau_grid;

	}

    function qtip_init_cb(rows_id, title, exo = 'n') {
        $('tr#'+ rows_id +' > td.js-' + exo+' > span').qtip({
            content: {
                text: function(event, api) {
                    
                    var tableau_grid  = $('#js_comptes_bancaires');
                    var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
                    var dossier_value = tableau_grid.getCell(row_key, 'dossiers-' + exo);
                    var value         = dossier_value.split(',');
                    var tr            = '<tbody>';
                    var thead         = '';
                    var aucun         = false;
                    var index = 1;
                    var modal_body = '';

                    var exercice = exercice_selector.val();

                    if (exo == 'n-1') {
                        exercice = exercice - 1;
                    }

                    value.sort().forEach(function(item) {

                        if (item == "" && value.length == 1) {
                           
                        } else if (item != "") {
                            var td = item.split('*');
                            tr    += '<tr><td class="col-sm-12 qtip-dossier">'+ index +'</td><td class="col-sm-12 qtip-dossier">'+ td[0] +'</td><td class="col-sm-12 center qtip-dossier">'+ td[1] +'</td></tr>';
                            index ++;
                        }
                    });

                    var count = 0;

                    if (value[0] != "") {
                        count = value.length;;
                    }

                    thead = '<thead><tr><th class="head">#</th><th class="head">Dossier</th><th class="head">Compte</th></tr></thead>';
                    modal_body = '<div class="panel panel-default"><div class="panel-heading"><h3 class="qtip-panel-title"><span class="badge badge-info">' + count + '</span> ' + title + ' ' + exercice + ' </h3></div><div class="panel-body table-wrapper-scroll-y my-custom-scrollbar"><table class="table table-bordered table-striped">';
                    modal_body    += thead;
                    modal_body    += tr;
                    modal_body    += '</tbody></table></div></div>';
                    
                    return modal_body;
                }
            },
            position: {
                viewport: $(window),
                adjust: {
                    method: 'shift none'
                },
            },
            show : 'click',
            hide : 'unfocus',
            style: {
                classes: 'qtip-pilotage qtip-light qtip-shadow',
            }
        });
    }

    function prepare_tooltip_cb() {
        //qtip_init_cb('tr-a-jour','Dossiers RB à jour');
        //qtip_init_cb('tr-dossier','Dossiers');
        qtip_init_cb('tr-m-1','Dossiers RB à m-1');
        qtip_init_cb('tr-m-2','Dossiers RB à m-2');
        qtip_init_cb('tr-incomplets','Dossiers RB incomplets');
        qtip_init_cb('tr-en-cours','Dossiers RB en cours de validation');
        qtip_init_cb('tr-inexitants','Dossiers RB sans CB');
    }


// });