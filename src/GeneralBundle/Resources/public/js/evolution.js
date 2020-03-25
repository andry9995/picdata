var client_selector       = $('#client-evolution');
var dossier_selector      = $('#dossier-evolution');
var exercice_selector     = $('#exercice-evolution');
var operateur_sd_selector = $('#operateur-sd'); 
var value_sd_selector     = $('#value-sd');
var filtre_sd_selector    = $('#filtre-sd');
var site_selector         = $('#site-evolution');
var analyse_selector      = $('#analyse-evolution');

client_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');

$(document).ready(function() {

	get_sites(client_selector,dossier_selector,exercice_selector,site_selector);

	$(document).on('change', '#client-evolution', function(){
        	
        if ($('#client-evolution  option:selected').text() == 'Tous') {
        	var tous = '<option value="0">Tous</option>';
            site_selector.html('').append(tous);
            $('#dossier-evolution').html('').append(tous);

            site_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
            dossier_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
        } else {
            site_selector.empty();
            get_sites(client_selector,dossier_selector,exercice_selector,site_selector);

        }

    });

    $(document).on('change', '#site-evolution', function(){
    	get_dossiers(client_selector,site_selector,dossier_selector,exercice_selector);
    });

    $(document).on('change', '#exercice-evolution', function()
    {
    	get_sites(client_selector,dossier_selector,exercice_selector,site_selector);
    });

	function get_sites(client_selector,dossier_selector,exercice_selector,site_selector) {


        if ($('#client-evolution option:selected').text() == 'Tous') {
            var tous = '<option value="0">Tous</option>';
            dossier_selector.empty();
            dossier_selector.html('').append(tous);
            return;
        }
		
		var client = client_selector.val();
		var dossier = dossier_selector.val();

		var url = Routing.generate('app_sites',{
			conteneur : 1,
            client : client
		});

		$.ajax({
			url: url,
			type: 'GET',
			data: {},
			success: function(data) {
                data = $.parseJSON(data);
				var tous = '<option value="0">Tous</option>';
                var single = false;
                site_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text(data.length.toString());

                if (data.length <= 1) {
                    site_selector.attr('disabled', 'disabled');
                    single = true;
                } else {
                    site_selector.removeAttr('disabled');
                    site_selector.html(tous);
                }

                var options = '';
                if (data instanceof Array) {
                    $.each(data, function (index, item) {
                        if (single) {
                            options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
                        } else {
                            options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
                        }
                    });
                    site_selector.append(options);
                    get_dossiers(client_selector, site_selector, dossier_selector, exercice_selector);
                } else {
                    return 0;
                }
			}
		})
	}

	function get_dossiers(client_selector, site_selector, dossier_selector, exercice_selector) {
		dossier_selector.empty();
        dossier_selector.html('');
    	var client = client_selector.val();
    	var site = 0;
    	if (site_selector != null) {
    		site = site_selector.val();
    	}

    	var now = new Date();
    	var current_year = now.getFullYear();
    	var exercice = typeof exercice_selector !== 'undefined' && exercice_selector != null ? exercice_selector.val() : current_year;
    	var url = Routing.generate('app_dossiers', {
    		client: client, 
    		site: site, 
    		conteneur: 1, 
    		tdi: 0
    	});

    	$.ajax({
    		url : url,
    		type : 'GET',
    		data : {
    			exercice : exercice,
    		},
    		success: function(data) {
    			data = $.parseJSON(data);
	            var tous = '<option value="0">Tous</option>';
	            var single = false;

	            dossier_selector.closest('.form-group')
	                .find('.label.label-warning')
	                .text(data.length.toString());

	            if (data.length <= 1) {
	                single = true;
	            } else {
	                dossier_selector.html(tous);
	            }

	            var options = '';

	            if (data instanceof Array) {
	                $.each(data, function (index, item) {
	                    if (single) {
	                        options += '<option value="' + item.idCrypter + '" selected>' + item.nom + '</option>';
	                    } else {
	                        options += '<option value="' + item.idCrypter + '">' + item.nom + '</option>';
	                    }
	                });
	                dossier_selector.append(options);
	            } else {
	                return 0;
	            }
    		}
    	})
	}


	function instance_grid() {
		var colNames = ['Client','Dossiers/Images','N-1','N','< m','m1','m2','m3','m4','m5','m6','m7','m8','m9','m10','m11','m12','m13','m14','m15','m16','m17','m18','m19','m20','m21','m22','m23','m24'];

		var colModel = [{
            name    : 'client',
            index   : 'client',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 150,
            classes : 'js-client'
        },{
            name    : 'dossier',
            index   : 'dossier',
            align   : 'left',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-dossier'
        },{
            name    : 'n-1',
            index   : 'n-1',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-n-1',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'n',
            index   : 'n',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-n',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm-inf',
            index   : 'm-inf',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m-inf',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm1',
            index   : 'm1',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m1',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm2',
            index   : 'm2',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m2',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm3',
            index   : 'm3',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m3',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm4',
            index   : 'm4',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m4',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm5',
            index   : 'm5',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m5',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm6',
            index   : 'm6',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m6',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm7',
            index   : 'm7',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m7',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm8',
            index   : 'm8',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m8',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm9',
            index   : 'm9',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m9',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm10',
            index   : 'm10',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m10',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm11',
            index   : 'm11',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m11',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm12',
            index   : 'm12',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m12',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm13',
            index   : 'm13',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m13',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm14',
            index   : 'm14',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m14',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm15',
            index   : 'm15',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m15',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm16',
            index   : 'm16',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m16',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm17',
            index   : 'm17',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m17',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm18',
            index   : 'm18',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m18',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm19',
            index   : 'm19',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m19',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm20',
            index   : 'm20',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m20',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm21',
            index   : 'm21',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m21',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm22',
            index   : 'm22',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m22',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm23',
            index   : 'm23',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m23',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        },{
            name    : 'm24',
            index   : 'm24',
            align   : 'center',
            editable: false,
            sortable: false,
            width   : 100,
            classes : 'js-m24',
            formatter: 'number', 
            formatoptions: { decimalPlaces: 0, defaultValue: '' }
        }];

        var options = {
            datatype   : 'local',
            height     : 100,
            autowidth  : true,
            loadonce   : true,
            shrinkToFit: false,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModel,
            viewrecords: true,
            hidegrid   : true,
        };

        var tableau_grid = $('#grid-evolution');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#grid-evolution').GridUnload('#grid-evolution');
            tableau_grid = $('#grid-evolution');
            tableau_grid.jqGrid(options);
        }

        var window_height = window.innerHeight - 200;

        if (window_height < 200) {
            tableau_grid.jqGrid('setGridHeight', 200);
        } else {
            tableau_grid.jqGrid('setGridHeight', window_height);
        }

        // tableau_grid.jqGrid('hideCol', ["m24"]);

        var exercice = exercice_selector.val();
        var d        = new Date();
        var y        = d.getFullYear();

        if (exercice == y) {
            var m = d.getMonth() + 1;
            for (var i = 1; i <= 24; i++) {
                if (i > m) {
                    var label = 'm' + i;
                    tableau_grid.jqGrid('hideCol', [label]);
                }
            }
        }

        if (exercice == y - 1) {
            var m = d.getMonth() + 13;
            for (var i = 1; i <= 24; i++) {
                if (i > m) {
                    var label = 'm' + i;
                    tableau_grid.jqGrid('hideCol', [label]);
                }
            }
        }


        return tableau_grid;

	}

    function resize_grid() {
        setTimeout(function() {
            var tableau_grid = $('#grid-evolution');
            var window_height = window.innerHeight;

            var width = tableau_grid.closest(".ibox-content").width();

            tableau_grid.jqGrid("setGridWidth", width);

            if (window_height < 200) {
                tableau_grid.jqGrid('setGridHeight', 200);
            } else {
                tableau_grid.jqGrid('setGridHeight', window_height);
            }

        }, 600);
    }

    $(window).resize(function() {
        resize_grid();
    });

	function go(){
        var client       = client_selector.val();
        var dossier      = dossier_selector.val();
        var exercice     = exercice_selector.val();
        var operateur_sd = operateur_sd_selector.val();
        var value_sd     = value_sd_selector.val();
        var filtre_sd    = filtre_sd_selector.val()
        var site         = site_selector.val();
        var url          = Routing.generate('dashboard_evolution');
        var analyse      = analyse_selector.val();

        var selected_option = $('#client-evolution option:selected').text()

        if (selected_option == 'Tous') {
            options  = document.getElementById('client-evolution').options;

            var values = [];

            for (var i = 1; i < options.length; i++) {
                values.push(options[i].value);
            }


            var json = JSON.stringify(values);

            client = json;

        }

		var data = {
            client      : client,
            dossier     : dossier,
            site        : site,
            exercice    : exercice,
            analyse     : analyse,
            value_sd    : value_sd,
            filtre_sd   : filtre_sd,
            operateur_sd:operateur_sd
		}

		$.ajax({
			url: url,
			data: data,
			type: 'POST',
			datatype: 'json',
			success: function(data) {
				var grid = instance_grid();
				
                grid.jqGrid('setGridParam',{
                	// sortname    : 'dossier',
                    // sortorder   : 'asc',
                    data        : data,
                    loadComplete: function() {

                        if (selected_option != 'Tous') {
                            grid.jqGrid('hideCol', ["client"]);
                        }

                        resize_grid();
                    },
                }).trigger('reloadGrid', [{
                    page: 1,
                    current: true
                }]);
			}

		})

	}

	$('#btn-go-evolution').on('click',function() {
        go();
    });

    // Modal selection dossier 
    $('#btn-selection-dossier').on('click',function () {
        $('#modal-selection-dossier').modal('show');
    });

    $('#btn-annuler-nb').on('click',function() {
        $("#filtre-sd").val('0').trigger('chosen:updated');
        $("#operateur-sd").val('0').trigger('chosen:updated');
        $("#value-sd").val('').trigger('chosen:updated');
    })

    $('#btn-valider-nb').on('click',function() {
        $('#modal-selection-dossier').modal('hide');
        go();
    });
})