// $(document).ready(function() {

	// intance_travaux_en_cours_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_travaux_en_cours_grid(data) {

		var colNames= ['','N','%','N-1','%'];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-list'
		}, {
            name     : 'n',
            index    : 'n',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-n'
		}, {
            name     : 'percent-n',
            index    : 'percent-n',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-percent-n'
		}, {
            name     : 'n-1',
            index    : 'n-1',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-n-1'
		}, {
            name     : 'percent-n-1',
            index    : 'percent-n-1',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-percent-n-1'
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
            caption    : 'SITUATIONS DES TRAVAUX EN COURS',
            sortable   : false,
            treeGrid     : true,
            treeGridModel: 'adjacency',
            treedatatype : "local",
            ExpandColumn : 'name',

        };

        var tableau_grid = $('#js_travaux_en_cours');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_travaux_en_cours').GridUnload('#js_travaux_en_cours');
            tableau_grid = $('#js_travaux_en_cours');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");

        resize_grid('travaux_en_cours');

        return tableau_grid;

	}

// });