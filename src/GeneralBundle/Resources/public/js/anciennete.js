$(document).ready(function() {

	// intance_anciennete_grid();

	function intance_anciennete_grid() {

		var colNames= ['','<15 jours','15 à 20','20 à 40','40 à 80','80 à 120', '>120'];

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
            name     : 'inferieur-15',
            index    : 'inferieur-15',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-inferieur-15'
		}, {
            name     : '15-20',
            index    : '15-20',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-15-20'
		}, {
            name     : '20-40',
            index    : '20-40',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-20-40'
		}, {
            name     : '40-80',
            index    : '40-80',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-40-80'
		}, {
            name     : '80-120',
            index    : '80-120',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-80-120'
		}, {
            name     : 'superieur-120',
            index    : 'superieur-120',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 125,
            classes  : 'js-superieur-120'
		}];

		var options = {
            datatype   : 'local',
            height     : 275,
            autowidth  : true,
            loadonce   : true,
            shrinkToFit: true,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
            caption    : 'ANCIENNETE DES PIECES PAR DATE ENVOI ET TRAITEMENT',
            sortable   : false

        };

        var tableau_grid = $('#js_anciennete');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_anciennete').GridUnload('#js_anciennete');
            tableau_grid = $('#js_anciennete');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.closest("div.ui-jqgrid-view")
                    .children("div.ui-jqgrid-titlebar")
                    .css("text-align", "center")
                    .children("span.ui-jqgrid-title")
                    .css("float", "none");
                    
        resize_tab_anciennete();

        return tableau_grid;

	}

	function resize_tab_anciennete() {
        setTimeout(function() {
            var tableau_grid = $('#js_anciennete');
            var width        = tableau_grid.closest("#anciennete_container").width();
            tableau_grid.jqGrid("setGridWidth", width);
        }, 600);
    }

    $(window).resize(function() {
        resize_tab_anciennete();
    });

});