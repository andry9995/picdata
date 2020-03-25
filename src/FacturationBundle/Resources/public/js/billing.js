$(function() {

   	getMoisSaisiClient();

	$(document).on('change', '#client, #exercice', function(event) {
       event.preventDefault();
       getMoisSaisiClient();
    });

    $(document).on('click', '#btn-billing', function(event) {
    	console.log('test');
       event.preventDefault();
       gridFactureFinale();
    });

	function getMoisSaisiClient() {
        var client_id = $('#client').val();
        var exercice = $('#exercice').val();
        $('#select-saisi-fini').empty();

        $.ajax({
            url: Routing.generate('fact_saisie_mois_saisi_client', {client: client_id, exercice: exercice}),
            type: 'GET',
            success: function(data) {
                data = $.parseJSON(data);

                var option = '';
                $.each(data, function(index, item) {
                    var mois_text = moment(item.mois_saisi)
                        .format('MMMM-YYYY')
                        .toUpperCase();
                    var mois_value = moment(item.mois_saisi)
                        .format('MM-YYYY');
                    option += '<option value="' + mois_value + '">' + mois_text + '</option>';
                });
                $('#select-saisi-fini').html(option);
            }
        });
    }

    function gridFactureFinale() {
		var client_id = $('#client').val();
		var exercice  = $('#exercice').val();
		var mois      = $('#select-saisi-fini').val();
		var annee     = $('#annee  option:selected').text();

        var data = {
			client_id: client_id,
			exercice : exercice,
			mois     : mois,
			annee    : annee
        }

        var url = Routing.generate('billing_final_list');

        $.ajax({
        	url: url,
        	data: data,
        	type: 'POST',
        	datatype: 'json',
        	success: function(data) {

                var grid = instance_grid();

                grid.jqGrid('setGridParam',{
                    data        : data,
                    loadComplete: function() {

                        resize_grid();

                        grid.jqGrid('destroyGroupHeader');

                        grid.jqGrid('setGroupHeaders', {
                            useColSpanStyle: true,
                            groupHeaders: [
                                {startColumnName: 'bill_hbq_saisies', numberOfColumns: 2, titleText: 'Lignes hors banques'},
                                {startColumnName: 'bill_bq_saisies', numberOfColumns: 2, titleText: 'Lignes banques'},
                                {startColumnName: 'bill_drp', numberOfColumns: 1, titleText: 'DRP'},
                                {startColumnName: 'bill_tva', numberOfColumns: 1, titleText: 'TVA'}
                            ]
                        });
                        
                    }
                }).trigger('reloadGrid', [{
                    page: 1,
                    current: true
                }]);
                
        		
        	}
        });


    }

    function instance_grid() {

    	var colNames = ['Chrono', 'Dossiers', 'Clôture','Tarif Appl.', 'Hono', 'Images', 'Nb Mois Saisis', 'Saisies', 'Dossiers', 'Saisies', 'Dossiers', 'Nb Mois Banque', 'Qté', 'Qté', 'Rev1', 'Rev2'];

    	var colModels = colModels = [
         	{
         		name : 'bill_chrono', 
         		index : 'bill_chrono', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-chrono'
            },{
         		name : 'bill_dossier', 
         		index : 'bill_dossier', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-dossier'
            },{
         		name : 'bill_cloture', 
         		index : 'bill_cloture', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 60, 
                classes : 'js-bill-cloture'
            },{
         		name : 'bill_tarif', 
         		index : 'bill_tarif', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-tarif'
            },{
         		name : 'bill_hono', 
         		index : 'bill_hono', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-hono'
            },
            {
         		name : 'bill_images', 
         		index : 'bill_images', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-images'
            },{
         		name : 'bill_nb_mois_saisis', 
         		index : 'bill_nb_mois_saisis', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-nb-mois-saisis'
            },{
         		name : 'bill_hbq_saisies', 
         		index : 'bill_hbq_saisies', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-hbq-saisies'
            },{
         		name : 'bill_hbq_dossiers', 
         		index : 'bill_hbq_dossiers', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-hbq-dossiers'
            },{
         		name : 'bill_bq_saisies', 
         		index : 'bill_bq_saisies', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-bq-saisies'
            },{
         		name : 'bill_bq_dossiers', 
         		index : 'bill_bq_dossiers', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-bq-dossiers'
            },{
         		name : 'bill_bq', 
         		index : 'bill_bq', 
         		editable : false, 
         		align : 'center', 
         		sortable : false,
                width : 80, 
                classes : 'js-bill-bq'
            }
            ,{
                 name : 'bill_drp', 
                 index : 'bill_drp', 
                 editable : false, 
                 align : 'center', 
                 sortable : false,
                width : 80, 
                classes : 'js-bill-drp'
            },{
                 name : 'bill_tva', 
                 index : 'bill_tva', 
                 editable : false, 
                 align : 'center', 
                 sortable : false,
                width : 80, 
                classes : 'js-bill-tva'
            },{
                 name : 'bill_rev1', 
                 index : 'bill_rev1', 
                 editable : false, 
                 align : 'center', 
                 sortable : false,
                width : 80, 
                classes : 'js-bill-rev1'
            },{
                 name : 'bill_rev2', 
                 index : 'bill_rev2', 
                 editable : false, 
                 align : 'center', 
                 sortable : false,
                width : 80, 
                classes : 'js-bill-rev2'
            }
     	];

     	var options = {
            datatype   : 'local',
            height     : 100,
            autowidth  : true,
            loadonce   : true,
            shrinkToFit: false,
            rownumbers : false,
            altRows    : false,
            colNames   : colNames,
            colModel   : colModels,
            viewrecords: true,
            hidegrid   : true,
        };

        var billing_grid = $('#js_billing');

        if (billing_grid[0].grid == undefined) {
            billing_grid.jqGrid(options);
        } else {
            delete billing_grid;
            $('#js_billing').GridUnload('#js_billing');
            billing_grid = $('#js_billing');
            billing_grid.jqGrid(options);
        }

        var window_height = window.innerHeight - 300;

        if (window_height < 400) {
            billing_grid.jqGrid('setGridHeight', 400);
        } else {
            billing_grid.jqGrid('setGridHeight', window_height);
        }

        return billing_grid;
    }

     $(window).resize(function() {
        
        resize_grid();

    });

     function resize_grid(argument) {
        setTimeout(function() {
                var billing_grid = $('#js_billing');
                var window_height = window.innerHeight - 300;

                var width = billing_grid.closest(".panel-body").width();

                billing_grid.jqGrid("setGridWidth", width);

                if (window_height < 400) {
                    billing_grid.jqGrid('setGridHeight', 400);
                } else {
                    billing_grid.jqGrid('setGridHeight', window_height);
                }

            }, 600);
    }

});