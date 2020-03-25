$(function() {

	var chargement_dossier_en_cours = [];

    // get_dossiers('details');
    // get_dossiers('graphes');

	// get_dossiers_by_site($('#client-details'), null, $('#dossier-details'), $('#exercice-details'));

    get_sites($('#client-details'),$('#site-details'),$('#dossier-details'),$('#exercice-details'));

	get_sites($('#client-graphes'),$('#site-graphes'),$('#dossier-graphes'),$('#exercice-graphes'));


    function client_change(client_element_id, site_selector, dossier_selector, exercice_selector) {
       if ($(client_element_id +" option:selected").text() == 'Tous') {
            var tous = '<option value="0">Tous</option>';
            site_selector.html('').append(tous);
            dossier_selector.html('').append(tous);

            site_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
            dossier_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
        } else {
            // get_dossiers('details');
            site_selector.empty();
            // get_dossiers_by_site($('#client-details'), null, dossier_selector, $('#exercice-details'));
            get_sites($(client_element_id),site_selector,dossier_selector,exercice_selector);
        }
    }

	// Details
	$(document).on('change', '#client-details', function()
    {
        client_change('#client-details',$('#site-details'),$('#dossier-details'),$('#exercice-details'))

    });

	$(document).on('change', '#exercice-details', function()
    {
        // get_dossiers('details');
		// get_dossiers_by_site($('#client-details'), null, $('#dossier-details'), $('#exercice-details'));
        get_sites($('#client-details'),$('#site-details'),$('#dossier-details'),$(this));
    });

    $(document).on('change', '#site-details', function()
    {
    	get_dossiers_by_site($('#client-details'), $('#site-details'), $('#dossier-details'), $('#exercice-details'));
    });

    $(document).on('change', '#site-graphes', function()
    {
        get_dossiers_by_site($('#client-graphes'), $('#site-graphes'), $('#dossier-graphes'), $('#exercice-graphes'));
    });

    // Graphes
    $(document).on('change', '#client-graphes', function()
    {
        // get_dossiers('graphes');
        client_change('#client-graphes',$('#site-graphes'),$('#dossier-graphes'),$('#exercice-graphes'))

    });

	$(document).on('change', '#exercice-graphes', function()
    {
        // get_dossiers('graphes');
        get_sites($('#client-graphes'),$('#site-graphes'),$('#dossier-graphes'),$(this));
    });

    function get_sites(client_selector, site_selector, dossier_selector, exercice_selector) {


        if ($('#client-graphes option:selected').text() == 'Tous') {
            var tous = '<option value="0">Tous</option>';
            dossier_selector.empty();
            dossier_selector.html('').append(tous);
            return;
        }

        if ($('#client-details option:selected').text() == 'Tous') {
            var tous = '<option value="0">Tous</option>';
            dossier_selector.empty();
            dossier_selector.html('').append(tous);
            return;
        }


        // if ($(client_element_id +" option:selected").text() == 'Tous') {
        //     var tous = '<option value="0">Tous</option>';
        //     site_selector.html('').append(tous);
        //     $('#dossier-details').html('').append(tous);

        //     site_selector.closest('.form-group')
        //             .find('.label.label-warning')
        //             .text('');
        //     dossier_selector.closest('.form-group')
        //             .find('.label.label-warning')
        //             .text('');
        // } 

    	

        var client = client_selector.val();
        var dossier = dossier_selector.val();
        var url = Routing.generate('app_sites',{
            conteneur : 1,
            client : client
        });
        $.ajax({
            url: url,
            type : 'GET',
            data : {},
            success : function(data) {

                

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
                    get_dossiers_by_site(client_selector, site_selector, dossier_selector, exercice_selector);
                } else {
                    return 0;
                }


            }
        })
    }

    function get_dossiers_by_site(client_selector, site_selector, dossier_selector, exercice_selector) {

    	dossier_selector.empty();

        dossier_selector.html('');


    	var client = client_selector.val();
    	var site = 0;

    	if (site_selector != null) {
    		site = site_selector.val();
    	}

    	// console.log(client,site)

    	var now = new Date();
    	var current_year = now.getFullYear();
    	var exercice = typeof exercice_selector !== 'undefined' && exercice_selector != null ? exercice_selector.val() : current_year;
    	var url = Routing.generate('app_dossiers', {client: client, site: site, conteneur: 1, tdi: 0});

    	$.ajax({
    		url : url,
    		type : 'GET',
    		data : {
    			exercice : exercice,
    		},
    		success : function(data) {
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

    	});

    }

	function get_dossiers(pane) {

		var exercice_selector = $('#exercice-' + pane);
		var client            = $('#client-' + pane).val();
		var now               = new Date();
		var current_year      = now.getFullYear();
		var exercice          = exercice_selector.val();
		var dossier_selector  = $('#dossier-' + pane);

		dossier_selector.html('');

		var dossier_id = dossier_selector.attr('id');
		
		url            = Routing.generate('dashboard_dossiers_actifs',{
			client  : client,
			exercice: exercice
        });

        var client_selected = $('#client-'+ pane +' option:selected').html();

        if (client_selected == 'Tous') {
        	dossier_selector.append('<option value="0" >Tous</option>');
        	dossier_selector.closest('.form-group')
        		.find('.label.label-warning')
        		.text('');
        } else {

	    	$.ajax({
				url     : url,
				type    : 'GET',
				datatype: "json",
				async   : true,
				success : function(data) {

					if (pane == 'details') {
						get_sites($('#client-details'),$('#site-details'),$('#dossier-details'));
					}

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
		                chargement_dossier_en_cours[dossier_id] = false;
		                return 0;
		            }
		            chargement_dossier_en_cours[dossier_id] = false;
		            if (typeof callback === 'function') {
		                callback();
		            }
	    		}

	    	});
        	
        }

		
	}

});