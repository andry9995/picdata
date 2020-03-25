var client_selector = $('#client-repartitions');
var site_selector = $('#site-repartitions');
var exercice_selector = $('#exercice-repartitions');

$(document).ready(function() {


  get_sites(client_selector,site_selector);

  $(document).on('change', '#client-repartitions', function()
  {
      get_sites(client_selector,site_selector)

  });

  function get_sites(client_selector,site_selector) {
    var client = client_selector.val();
    var url = Routing.generate('app_sites',{
        conteneur : 1,
        client : client
    });

    $.ajax({
      url: url,
      type : 'GET',
      data: {},
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
        } else {
            return 0;
        }
        
      }

    })

  }


    var pie               = $('#pie-repartitions');

    $('#btn-go-repartitions').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        go_repartitions();
     });

    /**
     * Fonction du boutton GO
     */
    function go_repartitions() {

    	var client = client_selector.val();
    	var exercice = exercice_selector.val();
      var site = site_selector.val();

    	if (client == '' || exercice == '') {
            show_info('Champs non Remplis', 'Veuillez Verifiez les Champs');
    		return false;	
    	} else {
    		var url = Routing.generate('general_repartitions',{
                client  : client,
                exercice: exercice,
                site: site
    		});

    		$.ajax({
            url     : url,
            type    : 'GET',
            dataType: 'json',
            success : function(response) {
      				instance_pie(response);
      			}
    		});
    	}
    }

    /**
     * Instance highcharts type 'pie'
     */
    function instance_pie(data) {

      var subtitle = '';
      var name     = 'Dossier';

    	if ($( "#client-repartitions option:selected" ).text() == 'Tous') {
        name     = 'Client';
    	} else {
    		var client = '';
    		if (data.length != 0) {
    			client = data[0].client;
    		}
    	}

    	var width = pie.closest('.panel-body').width() - 200;

    	var legend = {
            align        : 'right',
            verticalAlign: 'middle',
            layout       : 'vertical'
        };

    	var options =  {
          	chart: {
               plotBackgroundColor: null,
               plotBorderWidth    : null,
               plotShadow         : false,
               type               : 'pie',
          	},
          	scrollbar: {
            	enabled: true
    		},
          	legend: legend,
          	title: {
                text : 'Répartition',
                align: 'left'
          	},
          	plotOptions: {
            	pie: {
              		allowPointSelect: true,
              		cursor: 'pointer',
              		dataLabels: {
                		enabled: false
              		},
              		showInLegend: true
            	}
          	},
            tooltip: {
              headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
              pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> Images<br/>'
            },
          	series: [{
                name        : name,
                colorByPoint: true,
                data        : data
          	}],
          	credits: false,
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
        };

        var chart = pie.highcharts();

        if (chart = undefined) {
        	var pie_chart = Highcharts.chart('pie-repartitions', options);
        	return pie_chart
        } else {
        	  delete chart;
            var chart     = pie.highcharts();
            var pie_chart = Highcharts.chart('pie-repartitions', options);
        	return pie_chart
        }
    }

    $('#export-print-repartitions').click(function () {
        export_to('print');
    }); 

    $('#export-png-repartitions').click(function () {
        export_to('png');
    });

    $('#export-jpeg-repartitions').click(function () {
        export_to('jpg');
    });  

    $('#export-pdf-repartitions').click(function () {
        export_to('pdf');
    }); 

    $('#export-csv-repartitions').click(function () {
        export_to('csv');
    }); 

    $('#export-xls-repartitions').click(function () {
        export_to('xls');
    });

    /**
     * Exportation et impression de la Chart répartitions 
     * en format png, jpg, pdf, csv, xls
     */
    function export_to(filetype) {

      if (pie.highcharts() == undefined) {
          show_info("Echec d\'exportation", "Graphe vide", "error");
          return false;
      }

      switch(filetype) {
        case 'print':
           pie.highcharts()
              .print();
           break;
       case 'png':
           pie.highcharts()
              .exportChart();
           break;
       case 'jpg':
           pie.highcharts()
              .exportChart({
                  type: 'jpg',
              });
           break;
       case 'pdf':
           pie.highcharts()
              .exportChart({
                  type: 'application/pdf',
              });
           break;
       case 'csv':
           pie.highcharts()
              .downloadCSV();
           break;
       case 'xls':
           pie.highcharts()
              .downloadXLS();
           break;
      }
    }

});