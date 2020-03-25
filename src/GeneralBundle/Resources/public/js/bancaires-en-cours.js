// $(document).ready(function() {

	// intance_bancaires_en_cours_grid();

    var client_selector   = $('#dashboard-client');
    var exercice_selector = $('#dashboard-exercice');
    var dossier_selector  = $('#dashboard-dossier');

	function intance_bancaires_en_cours_grid(data) {

		var colNames= ['','Dossiers','Pièces','Dossiers'];

		var colModel= [{
            name     : 'list',
            index    : 'list',
            align    : 'left',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 100,
            classes  : 'js-list'
		}, {
            name     : 'dossier-nb',
            index    : 'dossier-nb',
            align    : 'center',
            editable : false,
            sortable : false,
            resizable: false,
            width    : 50,
            classes  : 'js-dossier-nb'
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
            caption    : 'TRAVAUX BANCAIRES EN COURS',
            sortable   : false,
            treeGrid     : true,
            treeGridModel: 'adjacency',
            treedatatype : "local",
            ExpandColumn : 'name',
            loadComplete : function() {
                prepare_tooltip_tbec();
            },

        };

        var tableau_grid = $('#js_bancaires_en_cours');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#js_bancaires_en_cours').GridUnload('#js_bancaires_en_cours');
            tableau_grid = $('#js_bancaires_en_cours');
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

        resize_grid('bancaires_en_cours');

        tableau_grid.jqGrid('hideCol', ["dossiers-n","dossiers-n-1"]);

        renameElement(tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title'),'div'); 
        tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').css('margin-top','11px');

        return tableau_grid;

	}

    function prepare_tooltip_tbec() {
        //qtip_init_tbec('tbc-dossier','Dossiers');
        qtip_init_tbec('tbc-cheque','Dossiers Cheque');
        qtip_init_tbec('tbc-encaissements','Dossiers Encaissements');
        qtip_init_tbec('tbc-decaissements','Dossiers Décaissement');
        qtip_init_tbec('tbc-rapp','Dossiers Rapprochés');
        qtip_init_tbec('tbc-encours','Dossiers en cours');
        
    }

    function month_num_to_name(num) {
        var months = ['Janv','Fév','Mar','Avr','Mai','Juin','Juil','Aou','Sept','Oct','Nov','Dec'];
        return months[num - 1] || '';
    }

    function qtip_init_tbec(rows_id, title, exo = 'dossier-nb') {
        
         $('tr#'+ rows_id +' > td.js-' + exo+' > span').qtip({
             content:{
                 text: function(event, api) {
                    var tableau_grid  = $('#js_bancaires_en_cours');
                    var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
                    var dossier_value = tableau_grid.getCell(row_key, 'dossiers-n');
                    var tr            = '<tbody>';
                    var thead         = '';
                    var aucun         = false;
                    var index = 1;

                    var exercice = exercice_selector.val();

                    var value = JSON.parse(dossier_value);

                    if(rows_id === "tbc-encours"){
                        value.sort().forEach(function(item) {
                            var td = item.split('*');
                            tr    += '<tr><td class="col-sm-12 qtip-dossier">'+ index +'</td><td class="col-sm-12 qtip-dossier">'+ td[0] +'</td><td class="col-sm-12 center qtip-dossier">'+ td[2] +'</td></tr>';
                            index++;
                        });

                        var count = value.length;

                        thead = '<thead><tr><th class="head">#</th><th class="head">Dossier</th><th class="head">Pièce à valider</th></tr></thead>';
                        var modal_body = '<div class="panel panel-default"><div class="panel-heading"><h3 class="qtip-panel-title"><span class="badge badge-info">' + count + '</span> ' + title + ' ' + exercice + ' </h3></div><div class="panel-body table-wrapper-scroll-y my-custom-scrollbar"><table class="table table-bordered table-striped">';
                        modal_body    += thead;
                        modal_body    += tr;
                        modal_body    += '</tbody></table></div></div>';
                    }else if(rows_id === "tbc-rapp"){
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
                    }else{
                        value.sort().forEach(function(item) {
                            var td = item.split('*');
                            tr    += '<tr><td class="col-sm-12 qtip-dossier">'+ index +'</td><td class="col-sm-12 qtip-dossier">'+ td[0] +'</td><td class="col-sm-12 center qtip-dossier">'+ td[2] +'</td></tr>';
                            index++;
                        });

                        var count = value.length;

                        thead = '<thead><tr><th class="head">#</th><th class="head">Dossier</th><th class="head">Pièce manquante</th></tr></thead>';
                        var modal_body = '<div class="panel panel-default"><div class="panel-heading"><h3 class="qtip-panel-title"><span class="badge badge-info">' + count + '</span> ' + title + ' ' + exercice + ' </h3></div><div class="panel-body table-wrapper-scroll-y my-custom-scrollbar"><table class="table table-bordered table-striped">';
                        modal_body    += thead;
                        modal_body    += tr;
                        modal_body    += '</tbody></table></div></div>';
                    }
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

// });