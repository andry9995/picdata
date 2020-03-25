var client_selector = $('#etat-compta-client');
var exercice_selector = $('#etat-compta-exercice'); 

$(document).ready(function() {

    var lastsel_lib;

    var lastsel_lib_journal;

	function intance_gird() {
		var colNames = ['Dossier', 'Dossier Id', 'Exercice', 'Statut', 'Journaux', 'Ref Pièce','Import',];
		var colModel = [{
            name    : 'dossier',
            index   : 'dossier',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 300,
            classes : 'js-dossier'
        },{
            name    : 'did',
            index   : 'did',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 300,
            classes : 'js-did'
        },{
            name    : 'exercice',
            index   : 'exercice',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 60,
            classes : 'js-exercice'
        },{
            name    : 'status',
            index   : 'status',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-status cursor-default'
        },{
            name    : 'journaux',
            index   : 'journaux',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 150,
            classes : 'js-journaux cursor-pointer'
        },{
            name    : 'ref-piece',
            index   : 'ref-piece',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 60,
            classes : 'js-ref-piece cursor-pointer'
        },{
            name    : 'import',
            index   : 'import',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 60,
            classes : 'js-import'
        }];

        var options = {
            datatype   : 'local',
            height     : 100,
            autowidth  : true,
            loadonce   : true,
            shrinkToFit: true,
            rownumWidth: 35,
            rownumbers : true,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
        };

        var tableau_grid = $('#etat-compta-grid');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#etat-compta-grid').GridUnload('#etat-compta-grid');
            tableau_grid = $('#etat-compta-grid');
            tableau_grid.jqGrid(options);
        }

        var window_height = window.innerHeight - 200;

        if (window_height < 200) {
            tableau_grid.jqGrid('setGridHeight', 200);
        } else {
            tableau_grid.jqGrid('setGridHeight', window_height);
        }

        tableau_grid.jqGrid('hideCol', ["import","did","ref-piece"]);


        return tableau_grid;

	}

	function resize_grid() {
        setTimeout(function() {
            var tableau_grid = $('#etat-compta-grid');
            var window_height = window.innerHeight;

            var width = tableau_grid.closest(".grid-container").width();

            tableau_grid.jqGrid("setGridWidth", width);

            if (window_height < 200) {
                tableau_grid.jqGrid('setGridHeight', 200);
            } else {
                tableau_grid.jqGrid('setGridHeight', window_height);
            }

        }, 600);
    }

    $(document).on('click','.etat-compta-go',function(event) {
    	var client = client_selector.val();
    	var exercice = exercice_selector.val();

    	var url = Routing.generate('etat_compta');

    	var data = {
    		client: client,
    		exercice : exercice
    	};

    	$.ajax({
    		url: url,
    		type: 'POST',
    		data: data,
    		datatype: 'json',
    		success: function(res) {
    			var grid = intance_gird();
    			grid.jqGrid('setGridParam',{
                    data        : res,
                    loadComplete: function() {
                        resize_grid();

                        var rows = grid.getDataIDs();
                        
                        rows.forEach(function(row,index) {
                        	$('tr#'+ row +' > td.js-status').qtip({
					        	content : {
					        		text: function(event,api) {
					        			var tableau_grid = $('#etat-compta-grid');
								        // var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
								        var import_containt = tableau_grid.getCell(row, 'import');
								        return import_containt;
					        		}
					        	},
					        	position: {
					                viewport: $(window),
					                adjust  : {
					                    method: 'shift none'
					                }
					            },
					            style: {
					                classes: 'qtip-dark qtip-shadow',
					                width: 1000
					            }
					        });
                        })

                    },
                }).trigger('reloadGrid', [{
                    page: 1,
                    current: true
                }]);
    		}
    	})
    })

    $(window).resize(function() {
        resize_grid();
    });

    // $(document).on('click', '.js-status', function() {
    //     // console.log(import_containt);
    //     $('.js-status').qtip({
    //     	content : {
    //     		text: function(event,api) {
    //     			var tableau_grid = $('#etat-compta-grid');
			 //        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
			 //        var import_containt = tableau_grid.getCell(row_key, 'import');
			 //        return import_containt;
    //     		}
    //     	}
    //     });
    	
    // })

    $(document).on('click','.js-journaux',function(event) {

		var tableau_grid = $('#etat-compta-grid');

        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');

		var dossier = tableau_grid.getCell(row_key, 'did');

		var journaux = tableau_grid.getCell(row_key, 'journaux');

		if (journaux == '<span style="color:#f8ac59;"><i class="fa fa-warning"></i> Pas de journal</span>') {
			show_info('PAS DE JOURNAL',"Le Dossier n'a pas de Journal",'warning');
			return;
		}

		// console.log(dossier);


        var url = Routing.generate('etat_compta_journaux');

        var data = {
        	dossier : dossier
        };

        $.ajax({
        	url: url,
        	type: 'POST',
        	data: data,
        	datatype: 'json',
        	success: function(res) {
	        		
        		build_modal(dossier,'<table id="journaux-grid"></table>','Journaux',undefined,'modal-lg');

        		var table_selected = $('#journaux-grid'),
                    w = table_selected.parent().width(),
                    h = $(window).height() - 250,
                    total = 0;

        		var colNames = ['Journal Dossier','Type Journal',''];

                var colModels = [{
                    name: 'journal_dossier',
                    index: 'journal_dossier',
                    align: 'left'
                },{
                    name: 'type_journal',
                    index: 'type_journal',
                    classes: 'dnp-lib',
                    editable: true,
                    edittype:"select",
                    editoptions:{
                        value: res.journal,
                        dataInit: function (elem) {
                            $(elem).addClass('dnp-journal-option');
                        }
                    }
                },{
                    name: 'action',
                    index: 'action',
                    classes: 'dnp-action',
                    width: 30
                }];


                $('#journaux-grid').jqGrid({
                    data: res.data,
                    datatype: 'local',
                    height: h,
                    width: w,
                    rowNum: 10000000,
                    rowList: [10,20,30],
                    colNames:colNames,
                    colModel:colModels,
                    viewrecords: true,
                    sortname: 'dossier',
                    rownumbers:true,
                    editurl: Routing.generate('dnp_edit'),
                    onSelectRow: function (id) {
                        if (id && id != lastsel_lib) {
                            $('#journaux-grid').restoreRow(lastsel_lib);
                            lastsel_lib = id;
                        }
                        $('#journaux-grid').editRow(id, false);
                    },
                    beforeSelectRow: function (rowid, e) {
                        var target = $(e.target);
                        var item_action = (target.closest('td').children('.icon-action').length > 0);
                        return !item_action;

                    },
                    loadComplete: function() {
                        var rows = $('#journaux-grid').getDataIDs();

                        rows.forEach(function(row,index) {

					        var type = $('#journaux-grid').getCell(row, 'type_journal');

					        if (type == '') {
					        	$('#journaux-grid').jqGrid('setRowData', row, false, {
                                    'background' : '#f8595940',
                                });
					        } else {
					        	
					        	$('#journaux-grid').jqGrid('setRowData', row, false, {
                                    'background' : '#00968840',
                                });
					        }

                        });
                    	
                    },
                    ajaxRowOptions: {async: true}
                });


        	}

        });



    })

    $(document).on('click','.js-ref-piece',function(event) {

		var tableau_grid = $('#etat-compta-grid');

        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');

        build_modal('modal-ref-piece','<table id="ref-piece-grid"></table>','Référence Pièce',undefined,'modal-lg');

    })

    $(document).on('click', '.save-dnp', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $('#journaux-grid').jqGrid('saveRow', lastsel_lib, {
            "aftersavefunc": function() {
            }
        });
    });

    $(document).on('click','#journal-type-li',function() {
        journal_list()
    })

    function journal_list() {
        
        var journal_type_grid = $('#journal-type-grid');

        if (journal_type_grid[0].grid != undefined) {
            delete journal_type_grid;
            $('#journal-type-grid').GridUnload('#journal-type-grid');
            journal_type_grid = $('#journal-type-grid');
        }

        var w              = journal_type_grid.parent().width(),
            h              = $(window).height() - 250,
            total          = 0;

        var url = Routing.generate('journal_list');

        var colNames = ["Code","Libellé",""];

        var colModels = [{
            name: 'code',
            index: 'code',
            align: 'left',
            editable: true,
        },{
            name: 'libelle',
            index: 'libelle',
            align: 'left',
            editable: true,
        },{
            name: 'action',
            index: 'action',
            align: 'left',
        }];

        $.ajax({
            url : url,
            type : 'GET',
            datatype : 'json',
            success: function(data) {
                journal_type_grid.jqGrid({
                    editurl: Routing.generate('journal_edit'),
                    data: data,
                    datatype: 'local',
                    height: h,
                    width: w,
                    rowNum: 10000000,
                    // rowList: [10,20,30],
                    colNames:colNames,
                    colModel:colModels,
                    viewrecords: true,
                    // sortname: 'dossier',
                    rownumbers:true,
                    caption: '',
                    loadComplete: function() {
                        journal_type_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').parent().css('display', 'block');
                        journal_type_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').prev().css('display', 'none');
                        if($("#add-row-journal").length == 0) {
                            journal_type_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                                '<button id="add-row-journal" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
                        }
                    }
                })
            }
        })
    
    }

    $(document).on('click','.edit-journal',function() {
        var tableau_grid = $('#journal-type-grid');
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
        tableau_grid.editRow(row_key);
    })

    $(document).on('click','.save-journal',function() {
        var tableau_grid = $('#journal-type-grid');
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
        // tableau_grid.saveRow(row_key);
        // journal_list();
        tableau_grid.jqGrid('saveRow', row_key, {
            "aftersavefunc": function() {
                journal_list();
            }
        });
    })

    $(document).on('click','.restore-journal',function() {
        var tableau_grid = $('#journal-type-grid');
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
        tableau_grid.restoreRow(row_key);
    })

    $(document).on('click','.delete-journal',function() {
        var tableau_grid = $('#journal-type-grid');
        
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');

        if (row_key == 'new_row') {
            tableau_grid.jqGrid('delRowData',row_key);
        } else {
            var url = Routing.generate('journal_delete',{
                journal: row_key
            });

            $.ajax({
                url: url,
                datatype: 'json',
                success: function(res) {
                    tableau_grid.jqGrid('delRowData',row_key);
                }
            })
        }
       
    })

    $(document).on('click','#add-row-journal',function() {
        event.preventDefault();

        $('#journal-type-grid').addRowData(
            "new_row", 
            {
                code: '',
                libelle: '',
                action: "<i class=\'fa fa-edit icon-action js-save-button edit-journal\'></i><i class=\'fa fa-times icon-action js-save-button restore-journal\'></i><i class=\'fa fa-save icon-action js-save-button save-journal\'></i><i class=\'fa fa-trash icon-action js-save-button delete-journal\'></i>"
            }, 
            'first'
        );
    })

    $(document).on('click','.journal-dossier-go',function(event) {
        event.preventDefault();
        event.stopPropagation();

        var client = $('#journal-dossier-client').val();

        var url = Routing.generate('journal_dossier_param',{
            client: client
        });

        var w              = $('#journal-dossier-grid').parent().width(),
            h              = $(window).height() - 250,
            total          = 0;

        $.ajax({
            url : url,
            type : 'GET',
            datatype : 'json',
            success: function(data) {

                var grid = instance_grid_journal_dossier(data.journal);

                grid.jqGrid('setGridParam',{
                    editurl: Routing.generate('journal_dossier_param_save'),
                    data        : data.data,
                    caption     : '',
                    // onSelectRow: function (id) {
                    //     if (id && id != lastsel_lib_journal) {
                    //         // $('#journal-dossier-grid').restoreRow(lastsel_lib_journal);
                    //         lastsel_lib_journal = id;
                    //     }
                    //     $('#journal-dossier-grid').editRow(id, false);
                    // },
                    // beforeSelectRow: function (rowid, e) {
                    //     var target = $(e.target);
                    //     var item_action = (target.closest('td').children('.icon-action').length > 0);
                    //     return !item_action;

                    // },
                    loadComplete: function() {

                        $('#journal-dossier-grid').closest('.ui-jqgrid').find('.ui-jqgrid-title').parent().css('display', 'block');
                        $('#journal-dossier-grid').closest('.ui-jqgrid').find('.ui-jqgrid-title').prev().css('display', 'none');
                        if($("#save-jrx-model").length == 0) {
                            $('#journal-dossier-grid').closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                                '<button id="save-jrx-model" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;"><i class="fa fa-save" ></i> Enregistrer</button><button id="restore-jrx-model" class="btn btn-outline btn-default btn-xs" style="margin-right: 20px;"><i class="fa fa-recycle" ></i> Restaurer</button></div>');
                        }

                        var rows = $('#journal-dossier-grid').getDataIDs();

                        var inconnus = 0;
                        var param = 0;
                        var tous = 0;

                        rows.forEach(function(row,index) {

                            var type = $('#journal-dossier-grid').getCell(row, 'type_journal');

                            if (type == '') {
                                $('#journal-dossier-grid').jqGrid('setRowData', row, false, {
                                    'background' : '#f8595940',
                                });
                                inconnus += 1; 
                            } 
                            else {
                                param += 1;
                            }

                            // else {
                                
                            //     $('#journal-dossier-grid').jqGrid('setRowData', row, false, {
                            //         'background' : '#00968840',
                            //     });
                            // }

                        });

                        var filter = $("input[name='show-filter-item']:checked").val();

                        // console.log(filter);

                        switch(filter) {
                            case '1':
                                $( "#show-item-param").trigger( "change" );
                                break;
                            case '2':
                                $( "#show-item-non-param").trigger( "change" );
                                break;
                        }
                        // console.log(inconnus);

                        $('#nb-inconnus').html(inconnus);
                        $('#nb-param').html(param);
                        $('#nb-tous').html(inconnus + param);
                        
                    },
                    ajaxRowOptions: {async: true}

                }).trigger('reloadGrid', [{
                    page: 1,
                    current: true
                }]);


            }
        })
        
    })

    function instance_grid_journal_dossier(journal) {
        var colNames = ['Dossier', 'Journal dossier','code str', 'Type journal','','journal id'];
        var colModel = [{
            name    : 'dossier',
            index   : 'dossier',
            align   : 'left',
            editable: false,
            // sortable: true,
            width   : 300,
            classes : 'js-dossier'
        },{
            name    : 'journal_dossier',
            index   : 'journal_dossier',
            align   : 'left',
            editable: false,
            // sortable: true,
            width   : 300,
            classes : 'js-journal-dossier'
        },{
            name    : 'code_str',
            index   : 'code_str',
            align   : 'left',
            editable: false,
            // sortable: true,
            width   : 300,
            classes : 'js-code_str'
        },{
            name    : 'type_journal',
            index   : 'type_journal',
            align   : 'left',
            // sortable: true,
            width   : 300,
            classes : 'js-type-journal',
            editable: true,
            edittype:"select",
            editoptions:{
                value: journal,
                dataInit: function (elem) {
                    // $(elem).addClass('dnp-journal-option');
                }
            }
        },{
            name    : 'action',
            index   : 'action',
            align   : 'left',
            editable: false,
            // sortable: true,
            width   : 300,
            classes : 'js-action'
        },{
            name    : 'journal_id',
            index   : 'journal_id',
            align   : 'left',
            editable: false,
            // sortable: true,
            width   : 300,
            classes : 'js-journal_id'
        }];

        var options = {
            datatype   : 'local',
            height     : 100,
            autowidth  : true,
            // loadonce   : true,
            shrinkToFit: true,
            rownumWidth: 35,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
            sortname: 'dossier',

        };

        var tableau_grid = $('#journal-dossier-grid');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#journal-dossier-grid').GridUnload('#journal-dossier-grid');
            tableau_grid = $('#journal-dossier-grid');
            tableau_grid.jqGrid(options);
        }

        var window_height = window.innerHeight - 200;

        if (window_height < 200) {
            tableau_grid.jqGrid('setGridHeight', 200);
        } else {
            tableau_grid.jqGrid('setGridHeight', window_height);
        }

        tableau_grid.jqGrid('hideCol', ["code_str","journal_id"]);

        return tableau_grid;


    }

    $(document).on('click', '.save-journal-dossier-param', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $('#journal-dossier-grid').jqGrid('saveRow', lastsel_lib_journal, {
            "aftersavefunc": function(row,res) {
                var count =  res.responseJSON.count;

                if (count == '0') {
                    show_info('SUCCESS', "Aucun mise à jour",'success');
                } else {
                    show_info('SUCCESS', count + " Journaux dossier mise à jour",'success');
                }

                // show_info('SUCCESS',"Journal enregistré",'success');

                $( ".journal-dossier-go" ).trigger( "click" );
            }
        });
    });

    $(document).on('change','#show-item-non-param',function() {
        var rows = $('#journal-dossier-grid').getDataIDs();

        rows.forEach(function(row,index) {

            var type = $('#journal-dossier-grid').getCell(row, 'type_journal');

            if (type == '') {

                $("#" + row,"#journal-dossier-grid").css({display:"table-row"});

                $('#journal-dossier-grid').jqGrid('setRowData', row, false, {
                    'background' : '#f8595940',
                });
            } 
            else {
                $("#" + row,"#journal-dossier-grid").css({display:"none"});
            }

        });
    });

    $(document).on('change','#show-item-param',function() {
        var rows = $('#journal-dossier-grid').getDataIDs();

        rows.forEach(function(row,index) {

            var type = $('#journal-dossier-grid').getCell(row, 'type_journal');

            if (type == '') {

                $("#" + row,"#journal-dossier-grid").css({display:"none"});

                $('#journal-dossier-grid').jqGrid('setRowData', row, false, {
                    'background' : '#f8595940',
                });
            } 
            else {
                $("#" + row,"#journal-dossier-grid").css({display:"table-row"});
            }

        });
    });

    $(document).on('change','#show-item-tous',function() {
        var rows = $('#journal-dossier-grid').getDataIDs();

        rows.forEach(function(row,index) {
            $("#" + row,"#journal-dossier-grid").css({display:"table-row"});
        });
    });

    $(document).on('click','.sync-journal-model',function() {
        
        var url = Routing.generate('sync_journal_model');

        $.ajax({
            url: url,
            type: 'GET',
            datatype : 'json',
            success: function(res) {
                if (res.status == 200) {
                    $( ".journal-dossier-go" ).trigger( "click" );

                    if (res.count == '0') {
                        show_info('SUCCESS', "Aucun mise à jour",'success');
                    } else {
                        show_info('SUCCESS', res.count + " Journaux dossier mise à jour",'success');
                    }
                }
            }
        });

    });

    var ids_edit = [];


    $(document).on('change','#journal-dossier-client',function() {
        $('#nb-inconnus').html('');
        $('#nb-param').html('');
        $('#nb-tous').html('');
    });

    $(document).on('click','.restore-journal-dossier-param',function() {
        var tableau_grid = $('#journal-dossier-grid');
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
        tableau_grid.restoreRow(row_key);

        if (ids_edit.indexOf(row_key) != -1) {
            // ids_edit.push(row_key);
            ids_edit.splice(ids_edit.indexOf(row_key),1);
        }
    });

    $(document).on('click','.edit-journal-dossier-param',function() {
        var tableau_grid = $('#journal-dossier-grid');
        var row_key       = tableau_grid.jqGrid('getGridParam', 'selrow');
        tableau_grid.editRow(row_key);

        if (ids_edit.indexOf(row_key) == -1) {
            ids_edit.push(row_key);
        }

    });

    $(document).on('click','#save-jrx-model',function(event) {
        // console.log(ids_edit);
        event.preventDefault();
        event.stopPropagation();

        var tableau_grid = $('#journal-dossier-grid');
        var list = ids_edit;

        var length = list.length;

        list.forEach(function(row_key,index) {
            // tableau_grid.restoreRow(row_key);
            tableau_grid.jqGrid('saveRow', row_key, {
                "aftersavefunc": function() {
                    if (index == length - 1) {
                        $( ".journal-dossier-go" ).trigger( "click" );
                    }
                }
            });
        })
        ids_edit = [];

    });

    $(document).on('click','#restore-jrx-model',function() {
        var tableau_grid = $('#journal-dossier-grid');
        var list = ids_edit;
        list.forEach(function(row_key,index) {
            tableau_grid.restoreRow(row_key);
        })
        ids_edit = [];
    })

});