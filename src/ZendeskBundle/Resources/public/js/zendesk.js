
	// instance_tickets_grid();

	go("all","all");

	function go(status, priority) {
		var url  = Routing.generate('tickets_list', {
			status: status,
			priority: priority
		});
		$.ajax({
			url     : url,
			type    : 'GET',
			datatype: 'json',
			async   : true,
			success : function(data) {
                instance_tickets_grid(data);
                // $('#modal').modal().hide();
            }
        });
	}

	function instance_tickets_grid(data) {
		
		var colNames= ['','ID','Sujet','Demandeur','Assigné','Label','Description',''];

		var colModel = [{
			name     : 'status',
            index    : 'status',
            align    : 'center',
            editable : false,
            sortable : true,
            width    : 10,
            classes  : 'js-status',
            resizable: false
		}, {
			name     : 'id',
            index    : 'id',
            align    : 'center',
            editable : false,
            sortable : true,
            width    : 10,
            classes  : 'js-id',
            resizable: false
		}, {
			name     : 'subject',
            index    : 'subject',
            align    : 'left',
            editable : false,
            sortable : true,
            width    : 125,
            classes  : 'js-subject',
            resizable: false
		}, {
			name     : 'requester',
            index    : 'requester',
            align    : 'left',
            editable : false,
            sortable : true,
            width    : 125,
            classes  : 'js-requester',
            resizable: false
		}, {
			name     : 'assignee',
            index    : 'assignee',
            align    : 'left',
            editable : false,
            sortable : true,
            width    : 100,
            classes  : 'js-assignee',
            resizable: false
		}, {
			name     : 'status-label',
            index    : 'status-label',
            align    : 'left',
            editable : false,
            sortable : true,
            width    : 1,
            classes  : 'js-status-label',
            resizable: false
		}, {
			name     : 'description',
            index    : 'description',
            align    : 'left',
            editable : false,
            sortable : false,
            width    : 1,
            classes  : 'js-description',
            resizable: false
		}, {
			name     : 'show',
            index    : 'show',
            align    : 'center',
            editable : false,
            sortable : true,
            width    : 25,
            classes  : 'js-show',
            resizable: false
		}];

		var options = {
			datatype   : "jsonstring",
			datastr    : data,
			height     : 600,
			autowidth  : true,
			loadonce   : true,
			shrinkToFit: true,
			rownumbers : false,
			altRows    : false,
			colNames   : colNames,
			colModel   : colModel,
			viewrecords: true,
			hidegrid   : true,
			caption    : 'Tous les tickets',
			sortable   : false,
			loadComplete: function() {
        		var rows = $("#tickets_list").getDataIDs();
		        for (var i = 0; i < rows.length; i++) {
		        	qtip_initialize('js-status',rows[i]);
		        }
            },

		};

		var tableau_grid = $('#tickets_list');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#tickets_list').GridUnload('#tickets_list');
            tableau_grid = $('#tickets_list');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.jqGrid('hideCol', ["status-label",'description']);

        return tableau_grid;

	}

	function qtip_initialize(selector, row_id) {

		var qtip_selector = 'tr#' + row_id + ' > td.' + selector ;

		$(qtip_selector).qtip({
			content: {
				text: function (event, api) {
					var rowKey      = $("#tickets_list").jqGrid('getGridParam', "selrow");
					var status      = $("#tickets_list").getCell(rowKey, "status-label");
					var subject     = $("#tickets_list").getCell(rowKey, "subject");
					var description = $("#tickets_list").getCell(rowKey, "description");
					// var priority = $("#tickets_list").getCell(rowKey, "priority");
					var label       = getStatusLabel(status);
					var content     =  getStatusLabel(status) + "  Ticket #" + row_id + "<br><br>";
					content         += "<b>" + subject + "</b><br><br>";
					content         += description;
					return "<div class='content-tip'>" + content + "</div>";
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
            }
		});
	}

	function getStatusLabel(status) {
		var label = "";
		switch(status) {
			case "new":
				label = "Nouveau";
				break;
			case "open":
				label = "Ouvert";
				break;
			case "pending":
				label = "En attente";
				break;
			case "solved":
				label = "Résolu";
				break;
		}

		var status = "<span class='status-badge label-badge status-" + status + "'>"+ label +"</span>";

		return status;
	}

	$(document).on('change','#ticket-status', function(event) {
		event.preventDefault();
        event.stopPropagation();
        go($(this).val(), $('input[name=filter-priority]').val());
	})

	$("input[name=filter-priority]").change(function() {
	    if(this.checked) {
        	go($('#ticket-status').val(), $(this).val());
	    }
	});

	$(document).on('click','.show-ticket', function(event) {
		var rowKey      = $("#tickets_list").jqGrid('getGridParam', "selrow");
    	var url  = Routing.generate('ticket_show', {
			id: rowKey,
		});
		$.ajax({
			url     : url,
			type    : 'GET',
			datatype: 'json',
			async   : true,
			success : function(data) {
        		show_modal(data,'Ticket #' + rowKey,'bounceInRight','modal-lg');
            }
        });

	})


