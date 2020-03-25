// $(document).ready(function() {

	// intance_bancaires_manquantes_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_bancaires_manquantes_grid(data) {

		var colNames= ['','Dossiers','Pièces','Dossiers'];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-list'
		}, {
            name     : 'nb-dossiers',
            index    : 'nb-dossiers',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-nb-dossiers'
        }, {
            name     : 'n',
            index    : 'n',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-n'
        }, {
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
            datatype     : "jsonstring",
            datastr      : data,
            height       : 350,
            autowidth    : false,
            loadonce     : true,
            shrinkToFit  : true,
            rownumbers   : false,
            altRows      : false,
            colNames     : colNames,
            colModel     : colModel,
            viewrecords  : true,
            hidegrid     : true,
            caption      : 'OPERATIONS BANCAIRES MANQUANTES',
            sortable     : false,
            treeGrid     : true,
            treeGridModel: 'adjacency',
            treedatatype : "local",
            ExpandColumn : 'name',
            loadComplete : function() {
                prepare_tooltip_obm();
            },

        };

        var tableau_grid = $('#js_bancaires_manquantes');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_bancaires_manquantes').GridUnload('#js_bancaires_manquantes');
            tableau_grid = $('#js_bancaires_manquantes');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");

        tableau_grid.closest('.ui-jqgrid')
                    .find('table.ui-jqgrid-htable')
                    .css('padding', '6px');

        resize_grid('bancaires_manquantes');

        tableau_grid.jqGrid('hideCol', ["dossiers-n","dossiers-n-1"]);

        renameElement(tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title'),'div'); 
        tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').css('margin-top','11px');

        return tableau_grid;

	}

    function month_num_to_name(num) {
        var months = ['Janv','Fév','Mar','Avr','Mai','Juin','Juil','Aou','Sept','Oct','Nov','Dec'];
        return months[num - 1] || '';
    }

    function qtip_init_obm(rows_id, title, exo = 'nb-dossiers') {
         $('tr#'+ rows_id +' > td.js-' + exo+' > span').qtip({
             content:{
                 text: function(event, api) {
                    var tableau_grid  = $('#js_bancaires_manquantes');
                    var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
                    var dossier_value = tableau_grid.getCell(row_key, 'dossiers-n');
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
                        tr    += '<tr><td class="col-sm-12 qtip-dossier">'+ index +'</td><td class="col-sm-12 qtip-dossier">'+ td[0] +'</td></tr>';
                        index++;
                    });

                    var count = value.length;

                    thead = '<thead><tr><th class="head">#</th><th class="head">Dossier</th></tr></thead>';
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
                classes: 'qtip-pilotage qtip-light qtip-shadow',
                width: 1000
            }
        });
    }

    function prepare_tooltip_obm() {
        qtip_init_obm('obm-dossier','Dossiers');
        qtip_init_obm('obm-frais','Dossiers Frais bancaire');
        qtip_init_obm('obm-vrt','Dossiers Virements');
        qtip_init_obm('obm-lcr','Dossiers Rélevé LCR');
        qtip_init_obm('obm-remises','Dossiers Remise en banque');
        qtip_init_obm('obm-chq','Dossiers Chèques inconnus');
        qtip_init_obm('obm-cb','Dossiers Relevé Bancaire');
    }

// });