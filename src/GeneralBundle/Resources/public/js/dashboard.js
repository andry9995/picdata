$(document).ready(function() {

	var chargement_dossier_en_cours = [];
    var dashType = parseInt($('.dash-type').attr('data-type'));
    charge_by_default_grid_pilotage();

	// get_dossiers();

    function get_sites(client_selector, site_selector, dossier_selector, exercice_selector) {

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
                    go();
	            } else {
	                return 0;
	            }
	    			
    		}

    	});
    }

    function charge_by_default_grid_pilotage() {
        $.ajax({
            url : Routing.generate('dashboard_ajax_default'),
            datatype: 'json',
            type: 'GET',
            success : function(data) {
                if(dashType){
                    $('.cache-pilotage').show();
                }else{
                    intance_comptes_bancaires_grid(data.cb);
                    intance_bancaires_manquantes_grid(data.obm);
                    intance_bancaires_en_cours_grid(data.tbec);
                }
                get_sites($('#dashboard-client'),$('#dashboard-site'),$('#dashboard-dossier'),$('#dashboard-exercice'));
                $('#expand-grid').removeClass('hidden');
            }
        });
    }

    function client_change(client_element_id, site_selector, dossier_selector, exercice_selector) {
       if ($(client_element_id +" option:selected").text() == 'Tous') {
            var tous = '<option value="0">Tous</option>';
            site_selector.html('').append(tous);
            $('#dossier-details').html('').append(tous);

            site_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
            dossier_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');
        } else {
            site_selector.empty();
            get_sites($(client_element_id),site_selector,dossier_selector,exercice_selector);
        }
    }




	$(document).on('change', '#dashboard-client', function()
    {
        client_change('#dashboard-client',$('#dashboard-site'),$('#dashboard-dossier'),$('#dashboard-exercice'));
    });


	$(document).on('change', '#dashboard-exercice', function()
    {
        get_sites($('#dashboard-client'),$('#dashboard-site'),$('#dashboard-dossier'),$(this));
    });

    $(document).on('change', '#dashboard-site', function()
    {
        get_dossiers_by_site($('#dashboard-client'), $('#dashboard-site'), $('#dashboard-dossier'), $('#dashboard-exercice'));
    }); 

    $(document).on('change', '#dashboard-dossier', function()
    {
        go();
    });

    $('#dashboard-client').closest('.form-group')
                            .find('.label.label-warning')
                            .text('');

	/**
	 * Réccupération des dossiers actifs
	 */
	function get_dossiers() {

		var exercice_selector = $('#dashboard-exercice');
		var client            = $('#dashboard-client').val();
		var now               = new Date();
		var current_year      = now.getFullYear();
		var exercice          = exercice_selector.val();
		var dossier_selector  = $('#dashboard-dossier');

		dossier_selector.html('');

		var dossier_id = dossier_selector.attr('id');
		
		url            = Routing.generate('dashboard_dossiers_actifs',{
			client  : client,
			exercice: exercice
        });

    	$.ajax({
			url     : url,
			type    : 'GET',
			datatype: "json",
			async   : true,
			success : function(data) {

				var tous   = '<option value="0">Tous</option>';
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

    $(document).on('hover', '.qtip_compte_details').hover(function() {
        $("td.qtip_compte_details").qtip({
            content: {
                text: function (event, api) {
                    var nomBanque = $(this).attr('data-banque');
                    var label_html = '<label class="">Banque: '+nomBanque+'</label>';
                    return label_html;
                }
            },
            position: {
                viewport: $(window),
                corner: {
                    target: 'topLeft',
                    tooltip: 'middleRight'
                },
                adjust: {
                    x: -15,
                    y: -15
                },
                container: $('.qtip_compte_details')
            },
            style: {
                classes: 'qtip-light qtip-shadow'
            }
        });
    });

    $(document).on('hover', '.qtip_rb').hover(function() {
        $("td.qtip_rb").qtip({
            content: {
                text: function (event, api) {
                    var color = $(this).attr('data-color');
                    var text = '';
                    if(color === '#008000') text = 'Releves de banque complet et vérifies';
                    if(color === '#ffd700') text = 'Releves bancaires en cours de vérification';
                    if(color === '#e95443') text = 'Releves de banque incomplets';
                    var label_html = '<label>'+text+'</label>';
                    return label_html;
                }
            },
            position: {
                viewport: $(window),
                corner: {
                    target: 'topLeft',
                    tooltip: 'middleRight'
                },
                adjust: {
                    x: -15,
                    y: -15
                },
                container: $('.qtip_rb')
            },
            style: {
                classes: 'qtip-light qtip-shadow'
            }
        });
    });

    $(document).on('hover', '.qtip_ob').hover(function() {
        $("td.qtip_ob").qtip({
            content: {
                text: function (event, api) {
                    var color = $(this).attr('data-color');
                    var text = '';
                    if(color === '#008000') text = 'Opérations bancaires completes et verifiée';
                    if(color === '#ffd700') text = 'Opérations bancaires en cours de vérification';
                    if(color === '#e95443') text = 'Opérations bancaires incompletes';
                    var label_html = '<label>'+text+'</label>';
                    return label_html;
                }
            },
            position: {
                viewport: $(window),
                corner: {
                    target: 'topLeft',
                    tooltip: 'middleRight'
                },
                adjust: {
                    x: -15,
                    y: -15
                },
                container: $('.qtip_ob')
            },
            style: {
                classes: 'qtip-light qtip-shadow'
            }
        });
    });

    $(document).on('hover', '.qtip_rappro').hover(function() {
        $("td.qtip_rappro").qtip({
            content: {
                text: function (event, api) {
                    var color = $(this).attr('data-color');
                    var text = '';
                    if(color === '#008000') text = 'Lettrage terminé';
                    if(color === '#ffd700') text = 'Lettrage en cours';
                    if(color === '#e95443') text = 'Impossible car RB incomplets';
                    var label_html = '<label>'+text+'</label>';
                    return label_html;
                }
            },
            position: {
                viewport: $(window),
                corner: {
                    target: 'topLeft',
                    tooltip: 'middleRight'
                },
                adjust: {
                    x: -15,
                    y: -15
                },
                container: $('.qtip_rappro')
            },
            style: {
                classes: 'qtip-pilotage-detail qtip-light qtip-shadow'
            }
        });
    });

});