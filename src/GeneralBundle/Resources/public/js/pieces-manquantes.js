// $(document).ready(function() {

	// intance_pieces_manquantes_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_pieces_manquantes_grid(data) {

		var colNames= ['','N','Pièces','N-1','Pièces','Dossiers','Dossiers N-1'];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 115,
            classes  : 'js-list'
		}, {
            name     : 'n',
            index    : 'n',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 40,
            classes  : 'js-n'
		}, {
            name     : 'pieces-n',
            index    : 'pieces-n',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 65,
            classes  : 'js-pieces-n'
		}, {
            name     : 'n-1',
            index    : 'n-1',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 40,
            classes  : 'js-n-1'
		}, {
            name     : 'pieces-n-1',
            index    : 'pieces-n-1',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 65,
            classes  : 'js-pieces-n-1'
		}, {
            name     : 'dossiers-n',
            index    : 'dossiers-n',
            align    : 'center',
            editable : false,
            sortable : false,
            width    : 50,
            classes  : 'js-dossiers-n',
            resizable: false
        }, {
            name     : 'dossiers-n-1',
            index    : 'dossiers-n-1',
            align    : 'center',
            editable : false,
            sortable : false,
            width    : 50,
            classes  : 'js-dossiers-n-1',
            resizable: false
        }];

		var options = {
            datatype     : "jsonstring",
            datastr      : data,
            height     : 325,
            autowidth  : false,
            loadonce   : true,
            shrinkToFit: true,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
            caption    : 'DOSSIERS AVEC DES PIECES MANQUANTES',
            sortable   : false,
            treeGrid     : true,
            treeGridModel: 'adjacency',
            treedatatype : "local",
            ExpandColumn : 'name',
            loadComplete : function() {
                prepare_tooltip_pm();
            },

        };

        var tableau_grid = $('#js_pieces_manquantes');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_pieces_manquantes').GridUnload('#js_pieces_manquantes');
            tableau_grid = $('#js_pieces_manquantes');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");

        resize_grid('pieces_manquantes');

        tableau_grid.jqGrid('hideCol', ["dossiers-n","dossiers-n-1"]);

        return tableau_grid;

	}

    function month_num_to_name(num) {
        var months = ['Janv','Fév','Mar','Avr','Mai','Juin','Juil','Aou','Sept','Oct','Nov','Dec'];
        return months[num - 1] || '';
    }

    function qtip_init_pm(rows_id, title, exo = 'n') {
        
         $('tr#'+ rows_id +' > td.js-' + exo).qtip({
             content:{
                 text: function(event, api) {
                    var tableau_grid  = $('#js_pieces_manquantes');
                    var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
                    var dossier_value = tableau_grid.getCell(row_key, 'dossiers-' + exo);
                    var tr            = '<tbody>';
                    var thead         = '';
                    var aucun         = false;
                    var index = 1;

                    var exercice = exercice_selector.val();

                    if (exo == 'n-1') {
                        exercice = exercice - 1;
                    }

                    var value = JSON.parse(dossier_value);

                    value.sort().forEach(function(item) {
                        var td = item.split('*');
                        tr    += '<tr><td class="col-sm-12 qtip-dossier">'+ index +'</td><td class="col-sm-12 qtip-dossier">'+ td[0] +'</td><td class="col-sm-12 center qtip-dossier">'+ month_num_to_name(td[1]) +'</td><td class="col-sm-12 center qtip-dossier">'+ td[2] +'</td></tr>';
                        index++;
                    });

                    var count = value.length;

                    thead = '<thead><tr><th class="head">#</th><th class="head">Dossier</th><th class="head">Cloture</th><th class="head">Exercice</th></tr></thead>';
                    var modal_body = '<div class="panel panel-default"><div class="panel-heading"><h3 class="qtip-panel-title"><span class="badge badge-info">' + count + '</span> ' + title + ' ' + exercice + ' </h3></div><div class="panel-body table-wrapper-scroll-y my-custom-scrollbar"><table class="table table-bordered table-striped">';
                    modal_body    += thead;
                    modal_body    += tr;
                    modal_body    += '</tbody></table></div></div>';

                    return modal_body;
                 }
             },
             position: {
                viewport: $(window),
                adjust  : {
                    method: 'shift none'
                }
            },
            show : 'click',
            hide : 'unfocus',
            style: {
                classes: 'qtip-light qtip-shadow',
                width: 1000
            }
        });
    
    }

    function prepare_tooltip_pm() {
        qtip_init_pm('dpm-dossier','Dossiers');
        qtip_init_pm('dpm-ob','Dossiers OB');
        qtip_init_pm('dpm-ci','Dossiers Chèques inconnus');
        qtip_init_pm('dpm-ffrs','Dossiers Facture clients');
        qtip_init_pm('dpm-fclients','Dossiers Facture fournisseurs');

        qtip_init_pm('dpm-dossier','Dossiers', 'n-1');
        qtip_init_pm('dpm-ob','Dossiers OB', 'n-1');
        qtip_init_pm('dpm-ci','Dossiers Chèques inconnus', 'n-1');
        qtip_init_pm('dpm-ffrs','Dossiers Facture clients', 'n-1');
        qtip_init_pm('dpm-fclients','Dossiers Facture fournisseurs', 'n-1');
        
    }


// });