// $(document).ready(function() {

	// intance_realisees_depassees_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_realisees_depassees_grid(data) {

        var now       = new Date();
        var tab_mois  = new Array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
        var m         = now.getMonth();
        var m_label   = tab_mois[m];
        var m_2       = (m - 2 >= 0) ? m - 2 : m - 2 + 12;
        var m_1       = (m - 1 >= 0) ? m - 1 : m - 1 + 12;
        var m_2_label = tab_mois[m_2];
        var m_1_label = tab_mois[m_1];
        
        var colNames  = ['', m_2_label, m_1_label, m_label];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 75,
            classes  : 'js-list'
		}, {
            name     : 'm-2',
            index    : 'm-2',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 75,
            classes  : 'js-m-2'
		},{
            name     : 'm-1',
            index    : 'm-1',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 75,
            classes  : 'js-m-1'
		}, {
            name     : 'm-en-cours',
            index    : 'm-en-cours',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 75,
            classes  : 'js-m-en-cours'
		}];

		var options = {
            datatype: "jsonstring",
            datastr: data,
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
            caption    : 'TACHES REALISEES ET DEPASSEES',
            sortable   : false,
            treeGrid: true,
            treeGridModel: 'adjacency',
            treedatatype: "local",
            ExpandColumn: 'name',

        };

        var tableau_grid = $('#js_realisees_depassees');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_realisees_depassees').GridUnload('#js_realisees_depassees');
            tableau_grid = $('#js_realisees_depassees');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");

        resize_grid('realisees_depassees');

        return tableau_grid;

	}

// });