$(document).ready(function() {
	var client_selector       = $('#client-graphes');
	var dossier_selector      = $('#dossier-graphes');
	var exercice_selector     = $('#exercice-graphes');
	var periode_selector      = $('#periode-graphes');
	var debut_date_selector   = $('#debut-date-graphes');
	var fin_date_selector     = $('#fin-date-detail');
	var analyse_selector      = $('#analyse-graphes');
	var typedate_selector     = $('#typedate-graphes');
	var operateur_sd_selector = $('#operateur-sd'); 
	var value_sd_selector     = $('#value-sd');
	var filtre_sd_selector    = $('#filtre-sd');
	var graphe                = $('#chart-graphes');
	var site_selector         = $('#site-graphes');

	client_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');

	// Modal fourchette
	$(document).on('change', '#periode-graphes', function()
    {
        if($(this).val() === '6'){
            $('#modal-fourchette-graphes').modal('show');
        } else{
			debut_date_selector.val('');
			fin_date_selector.val('');
        }

    });

    $('#valider-fourchette-graphes').on('click',function() {
        $("#modal-fourchette-graphes").modal('hide');
    })

	// Modal selection dossier 
    $('#btn-selection-dossier').on('click',function () {
        $('#modal-selection-dossier').modal('show');
    });

    // Date picker debut période
    debut_date_selector.datepicker({
		format        : 'dd-mm-yyyy',
		language      : 'fr',
		autoclose     : true,
		todayHighlight: true
    });

    // Date picker fin période
    fin_date_selector.datepicker({
		format        : 'dd-mm-yyyy',
		language      : 'fr',
		autoclose     : true,
		todayHighlight: true
    });

	$('#btn-go-graphes').on('click',function() {
        go_graphes();
    });

    function go_graphes() {
		var client       = client_selector.val();
		var dossier      = dossier_selector.val();
		var exercice     = exercice_selector.val();
		var periode      = periode_selector.val();
		var typedate     = typedate_selector.val();
		var analyse      = analyse_selector.val();
		var operateur_sd = operateur_sd_selector.val();
		var value_sd     = value_sd_selector.val();
		var filtre_sd    = filtre_sd_selector.val();
		var site         = site_selector.val();

		if (!value_sd || value_sd == '' || value_sd == 'undefined') {
			value_sd = 0;
		}

		if (client == '' || dossier == '') {
            show_info('Champs non Remplis', 'Veuillez Verifiez les Champs');
            return false;
		} else {
			if (periode == 6) {
				var periode_debut = debut_date_selector.val();
				var periode_fin = fin_date_selector.val();
				if (periode_debut == '' || periode_fin == '') {
            		show_info('Champ Fourchette Invalide', 'Veuillez Remplir les Dates');
            		return false;
				}
				var debut      = periode_debut.split('-');
				var fin        = periode_fin.split('-');
				var date_debut = debut[2] + '-' + debut[1] + '-' + debut[0];
				var date_fin   = fin[2] + '-' + fin[1] + '-' + fin[0];
			} else{
				var date_debut = 0;
				var date_fin = 0;
			}
		}

		var url = Routing.generate('general_images',{
			client      : client,
			dossier     : dossier,
			exercice    : exercice,
			periode     : periode,
			perioddeb   : date_debut,
			periodfin   : date_fin,
			typedate    : typedate,
			analyse     : analyse,
			tab         : 2,
			filtre_sd   : filtre_sd,
			operateur_sd: operateur_sd,
			value_sd    : value_sd,
			site        : site
    	});

    	$.ajax({
			url     : url,
			type    : 'GET',
			datatype: 'json',
			success : function(response) {
				var chart   = instance_chart();
				var data    = response['courbe'];
				var analyse = response['analyse'];
    			chart.addSeries({
                    name: "Images N-2",
                    data: data[2].data
                });
                chart.addSeries({
                    name: "Images N-1",
                    data: data[1].data
                });
                chart.addSeries({
                    name: "Images N",
                    data: data[0].data
                });

                if (analyse == 1) {
                	chart.addSeries({
						type  : 'line',
						name  : "Tendance images N-2 et N-1",
						marker: {
                            enabled: false
                        },
                        data: (function() {
                            return fitOneDimensionalData(arrayAddition(data[2].data, data[1].data));
                        })()
                    });

                    chart.addSeries({
						type  : 'line',
						name  : "Tendance images N",
						marker: {
                            enabled: false
                        },
                        data: (function() {
                            return fitOneDimensionalDataTendance(data[0].data,exercice);
                        })()
                    });
                }
    		}
    	});
    }

	function fitOneDimensionalDataTendance(source_data,exercice) {
		var trend_source_data = [];
		$continu              = false;
		var now               = new Date();
		var current_year      = now.getFullYear();
		var current_month     = now.getMonth();

	    for (var i = source_data.length; i-- > 0;) {

	        if (current_year == exercice) {
	            if (i < current_month + 9) {
	                trend_source_data[i] = [i, source_data[i]];
	            }
	        } else{
	            trend_source_data[i] = [i, source_data[i]];
	        }


	    }
	    var regression_data = fitData(trend_source_data).data;
	    var trend_line_data = [];
	    for (i = regression_data.length; i-- > 0;) {
	        trend_line_data[i] = Math.round(regression_data[i][1]);
	    }
	    return trend_line_data;
	}

	function fitOneDimensionalData(source_data) {
		var trend_source_data = [];
		$continu              = false;

	    for (var i = source_data.length; i-- > 0;) {
	        trend_source_data[i] = [i, source_data[i]];
	    }
	    var regression_data = fitData(trend_source_data).data;
	    var trend_line_data = [];
	    for (i = regression_data.length; i-- > 0;) {
	        trend_line_data[i] = Math.round(regression_data[i][1]);
	    }
	    return trend_line_data;
	}

	function arrayAddition(array1, array2) {
	    var result = [];

	    for (var i = 0; i < array1.length; i++) {
	        result[i] = array1[i] + array2[i];
	    }

	    return result;
	}

    function instance_chart() {

	    var chart = Highcharts.chart('chart-graphes', {

	        title: {
	            text: 'Graphe des images'
	        },

	        yAxis: {
				min  : 0,
				title: {
	                text: 'Nombres d\'images ( k = mille)'
	            }
	        },
	        xAxis: {
	            categories: ['Antérieur','Juin N-1', 'Juil N-1', 'Aout N-1', 'Sept N-1', 'Oct N-1', 'Nov N-1', 'Dec N-1', 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aout', 'Sept', 'Oct', 'Nov', 'Dec', 'Jan N+1', 'Fev N+1', 'Mar N+1', 'Avr N+1', 'Mai N+1', 'Juin N+1','Exterieur'],
	        },
	        legend: {
				layout       : 'vertical',
				align        : 'right',
				verticalAlign: 'middle'
	        },

	        plotOptions: {
	            series: {
	                label: {
	                    enabled: false
	                },
	            },
	        },
	        responsive: {
	            rules: [{
	                condition: {
	                    maxWidth: 500
	                },
	                chartOptions: {
	                    legend: {
							layout       : 'horizontal',
							align        : 'center',
							verticalAlign: 'bottom'
	                    }
	                }
	            }]
	        },
			credits: false,
			legend : {
				layout       : 'vertical',
				align        : 'right',
				verticalAlign: 'middle',
				borderWidth  : 0
	        },
	        colors: ["#23c300", "#82807a", "#0062e3", "#f8ac59", "#0062e3"],
	        exporting: {
	            buttons: {
	                contextButton: {
	                    menuItems: [
	                        'printChart',
	                        'separator',
	                        'downloadPNG',
	                        'downloadJPEG',
	                        'downloadPDF',
	                        'separator',
	                        'downloadCSV',
	                        'downloadXLS',
	                    ]
	                }
	            }
	        },
	        lang: {
				printChart        : '<i class="fa fa-print" aria-hidden="true"></i> Imprimer',
				downloadPNG       : '<i class="fa fa-download" aria-hidden="true"></i> En format PNG',
				downloadJPEG      : '<i class="fa fa-download" aria-hidden="true"></i> En format JPEG',
				downloadPDF       : '<i class="fa fa-download" aria-hidden="true"></i> En format PDF',
				downloadCSV       : '<i class="fa fa-file" aria-hidden="true"></i> Exporter en CSV',
				downloadXLS       : '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Exporter en XLS',
				contextButtonTitle: 'Menu'
	        }

	    });

	    return chart;
	}

	$('#export-print-graphes').click(function () {
        export_to('print');
    }); 

    $('#export-png-graphes').click(function () {
        export_to('png');
    });

    $('#export-jpeg-graphes').click(function () {
        export_to('jpg');
    });  

    $('#export-pdf-graphes').click(function () {
        export_to('pdf');
    }); 

    $('#export-csv-graphes').click(function () {
        export_to('csv');
    }); 

    $('#export-xls-graphes').click(function () {
        export_to('xls');
    });

    function export_to(filetype) {

      if (graphe.highcharts() == undefined) {
          show_info("Echec d\'exportation", "Graphe vide", "error");
          return false;
      }

      switch(filetype) {
        case 'print':
           graphe.highcharts()
                 .print();
           break;
       case 'png':
           graphe.highcharts()
                 .exportChart();
           break;
       case 'jpg':
           graphe.highcharts()
                 .exportChart({
                      type: 'jpg',
                  });
           break;
       case 'pdf':
           graphe.highcharts()
                 .exportChart({
                      type: 'application/pdf',
                  });
           break;
       case 'csv':
           graphe.highcharts()
                 .downloadCSV();
           break;
       case 'xls':
           graphe.highcharts()
                 .downloadXLS();
           break;
      }
    }
});