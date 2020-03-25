$(function() {


    var client_selector       = $('#client-details');
    var dossier_selector      = $('#dossier-details');
    var exercice_selector     = $('#exercice-details');
    var periode_selector      = $('#periode-details');
    var debut_date_selector   = $('#debut-date-details');
    var fin_date_selector     = $('#fin-date-detail');
    var analyse_selector      = $('#analyse-details');
    var typedate_selector     = $('#typedate-details');
    var operateur_sd_selector = $('#operateur-sd'); 
    var value_sd_selector     = $('#value-sd');
    var filtre_sd_selector    = $('#filtre-sd');
    var site_selector         = $('#site-details');

    client_selector.closest('.form-group')
                    .find('.label.label-warning')
                    .text('');

    

	// Modal fourchette
	$(document).on('change', '#periode-details', function()
    {
        if($(this).val() === '6'){
            $('#modal-fourchette-details').modal('show');
        }
        else{
            debut_date_selector.val('');
            fin_date_selector.val('');
        }
    });

    $('#btn-annuler-nb').on('click',function() {
        $("#filtre-sd").val('0').trigger('chosen:updated');
        $("#operateur-sd").val('0').trigger('chosen:updated');
        $("#value-sd").val('').trigger('chosen:updated');
    })

    $('#valider-fourchette-details').on('click',function() {
        $("#modal-fourchette-details").modal('hide');
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

	$('#btn-go-details').on('click',function() {
        go_details();
    });

    $('#btn-valider-nb').on('click',function() {
        $('#modal-selection-dossier').modal('hide');
        go_details();
    })

    function go_details() {

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
            tab         : 1,
            filtre_sd   : filtre_sd,
            operateur_sd: operateur_sd,
            value_sd    : value_sd,
            site        : site
    	});

    	$.ajax({
            url     : url,
            type    : 'GET',
            datatype: 'json',
            async   : true,
            success : function(responses) {

                $("#filtre-sd").val('0').trigger('chosen:updated');
                $("#operateur-sd").val('0').trigger('chosen:updated');
                $("#value-sd").val('').trigger('chosen:updated');

                // if (responses.count&& responses.percent) {
                //     $('#dossiers-filter').removeClass('hidden');
                //     $('#count-dossiers-filter').html(responses.count);
                //     $('#percent-dossiers-filter').html(responses.percent);
                // } else{
                //     $('#dossiers-filter').addClass('hidden');
                // }

                var nb_dossier_n =  dossier_selector.closest('.form-group')
                                    .find('.label.label-warning')
                                    .text();

                $('#count-dossiers-filter').html(nb_dossier_n);

                $('#count-dossiers-filter-n-1').html(responses.count);

                var response = responses.data;

    			var grid = instance_grid();

                if (typeof response == 'object') {
                    response = Array.from(Object.keys(response), k=>response[k]);
                }

                grid.jqGrid('setGridParam', {
                    sortname    : 'dossier',
                    sortorder   : 'asc',
                    data        : response,
                    loadComplete: function() {

                        resize_tab_details();

                        var grid = $('#grid-details');

                        if (client_selector.val != 'NlpWczV1NkdnV21qbjBObHBXY3pWMU5rZG5WMjFxYmc9PQ==') {
                            grid.jqGrid('hideCol', ["client"]);
                        }

                        var total  = 0;
                        var total1 = 0;
                        var exist  = false;
                        var rowid  = -1;
                        var m      = 0;
                        var m1     = 0;
                        var m2     = 0;
                        var m3     = 0;
                        var m4     = 0;
                        var m5     = 0;
                        var m6     = 0;
                        var m7     = 0;
                        var m8     = 0;
                        var m9     = 0;
                        var m10    = 0;
                        var m11    = 0;
                        var m12    = 0;
                        var m13    = 0;
                        var m14    = 0;
                        var m15    = 0;
                        var m16    = 0;
                        var m17    = 0;
                        var m18    = 0;
                        var m19    = 0;
                        var m20    = 0;
                        var m21    = 0;
                        var m22    = 0;
                        var m23    = 0;
                        var m24    = 0;
                        var m_     = 0;
                        var m_1    = 0;
                        var m_2    = 0;
                        var m_3    = 0;
                        var m_4    = 0;
                        var m_5    = 0;
                        var m_6    = 0;
                        var m_7    = 0;
                        var m_8    = 0;
                        var m_9    = 0;
                        var m_10   = 0;
                        var m_11   = 0;
                        var m_12   = 0;
                        var m_13   = 0;
                        var m_14   = 0;
                        var m_15   = 0;
                        var m_16   = 0;
                        var m_17   = 0;
                        var m_18   = 0;
                        var m_19   = 0;
                        var m_20   = 0;
                        var m_21   = 0;
                        var m_22   = 0;
                        var m_23   = 0;
                        var m_24   = 0;

                        var rows = grid.getDataIDs();
                       
                        rows.forEach(function(row,index) {

                            var item = grid.getRowData(rows[index]);
                            var dossier = item['dossier'];
                            var exercice = item['exercice'];


                             for (let [key, value] of Object.entries(item)) {
                              if (value == '0') {
                                grid.setCell (rows[index], key, '', {color:'transparent'});
                              }
                            }

                            if (exercice == '' && dossier != 'Total N' && dossier != 'Total N -1') {
                                grid.jqGrid('setRowData', rows[index], false, {
                                    'color'      : '#494a4a',
                                    'background' : '#efefef',
                                    'font-weight': 'bold',
                                });
                                grid.setCell (rows[index], 'dossier', dossier, {color:'transparent'});
                            }

                            if (dossier == '') {
                                grid.jqGrid('setRowData', rows[index], false, {
                                    'color'      : '#494a4a',
                                    'background' : '#efefef',
                                    'font-weight': 'bold',
                                })
                            } else {

                                if (dossier == 'Total N') {
                                    exist = true;
                                }
                                if (dossier == 'Total N -1') {
                                    exist = true;
                                }

                                if (exercice == 'N') {
                                    total += parseInt(item['total']); 
                                    m     += parseInt(item['m']); 
                                    m1    += parseInt(item['m+1']); 
                                    m2    += parseInt(item['m+2']); 
                                    m3    += parseInt(item['m+3']); 
                                    m4    += parseInt(item['m+4']); 
                                    m5    += parseInt(item['m+5']); 
                                    m6    += parseInt(item['m+6']); 
                                    m7    += parseInt(item['m+7']); 
                                    m8    += parseInt(item['m+8']); 
                                    m9    += parseInt(item['m+9']); 
                                    m10   += parseInt(item['m+10']); 
                                    m11   += parseInt(item['m+11']); 
                                    m12   += parseInt(item['m+12']); 
                                    m13   += parseInt(item['m+13']); 
                                    m14   += parseInt(item['m+14']); 
                                    m15   += parseInt(item['m+15']); 
                                    m16   += parseInt(item['m+16']); 
                                    m17   += parseInt(item['m+17']); 
                                    m18   += parseInt(item['m+18']); 
                                    m19   += parseInt(item['m+19']); 
                                    m20   += parseInt(item['m+20']); 
                                    m21   += parseInt(item['m+21']); 
                                    m22   += parseInt(item['m+22']); 
                                    m23   += parseInt(item['m+23']); 
                                    m24   += parseInt(item['m+24']);
                                } else {
                                    if (exercice == 'N - 1') {
                                        total1 += parseInt(item['total']); 
                                        m_     += parseInt(item['m']); 
                                        m_1    += parseInt(item['m+1']); 
                                        m_2    += parseInt(item['m+2']); 
                                        m_3    += parseInt(item['m+3']); 
                                        m_4    += parseInt(item['m+4']); 
                                        m_5    += parseInt(item['m+5']); 
                                        m_6    += parseInt(item['m+6']); 
                                        m_7    += parseInt(item['m+7']); 
                                        m_8    += parseInt(item['m+8']); 
                                        m_9    += parseInt(item['m+9']); 
                                        m_10   += parseInt(item['m+10']); 
                                        m_11   += parseInt(item['m+11']); 
                                        m_12   += parseInt(item['m+12']); 
                                        m_13   += parseInt(item['m+13']); 
                                        m_14   += parseInt(item['m+14']); 
                                        m_15   += parseInt(item['m+15']); 
                                        m_16   += parseInt(item['m+16']); 
                                        m_17   += parseInt(item['m+17']); 
                                        m_18   += parseInt(item['m+18']); 
                                        m_19   += parseInt(item['m+19']); 
                                        m_20   += parseInt(item['m+20']); 
                                        m_21   += parseInt(item['m+21']); 
                                        m_22   += parseInt(item['m+22']); 
                                        m_23   += parseInt(item['m+23']); 
                                        m_24   += parseInt(item['m+24']); 
                                    }
                                }
                            }
                        });

                        if (!exist && rows.length > 5) {
                            var value1 = {
                                'dossier'   : 'Total N -1',
                                'exercice'  : '',
                                'client'    : '',
                                'total'     : total1,
                                'm+24'      : Number(m_24).toLocaleString(),
                                'm'         : Number(m_).toLocaleString(),
                                'm+1'       : Number(m_1).toLocaleString(),
                                'm+2'       : Number(m_2).toLocaleString(),
                                'm+3'       : Number(m_3).toLocaleString(),
                                'm+4'       : Number(m_4).toLocaleString(),
                                'm+5'       : Number(m_5).toLocaleString(),
                                'm+6'       : Number(m_6).toLocaleString(),
                                'm+7'       : Number(m_7).toLocaleString(),
                                'm+8'       : Number(m_8).toLocaleString(),
                                'm+9'       : Number(m_9).toLocaleString(),
                                'm+10'      : Number(m_10).toLocaleString(),
                                'm+11'      : Number(m_11).toLocaleString(),
                                'm+12'      : Number(m_12).toLocaleString(),
                                'm+13'      : Number(m_13).toLocaleString(),
                                'm+14'      : Number(m_14).toLocaleString(),
                                'm+15'      : Number(m_15).toLocaleString(),
                                'm+16'      : Number(m_16).toLocaleString(),
                                'm+17'      : Number(m_17).toLocaleString(),
                                'm+18'      : Number(m_18).toLocaleString(),
                                'm+19'      : Number(m_19).toLocaleString(),
                                'm+20'      : Number(m_20).toLocaleString(),
                                'm+21'      : Number(m_21).toLocaleString(),
                                'm+22'      : Number(m_22).toLocaleString(),
                                'm+23'      : Number(m_23).toLocaleString(),
                                'totalN'    : '',
                                'totalNPrev': '',
                            }

                            for (let [key, v] of Object.entries(value1)) {
                              if (v == '0') {
                                    value1[key] = '';
                              }
                            }

                            grid.addRowData("total-row", value1, 'first');

                            var value = {
                                'dossier'   : 'Total N',
                                'exercice'  : '',
                                'client'    : '',
                                'total'     : total,
                                'm+24'      : Number(m24).toLocaleString(),
                                'm'         : Number(m).toLocaleString(),
                                'm+1'       : Number(m1).toLocaleString(),
                                'm+2'       : Number(m2).toLocaleString(),
                                'm+3'       : Number(m3).toLocaleString(),
                                'm+4'       : Number(m4).toLocaleString(),
                                'm+5'       : Number(m5).toLocaleString(),
                                'm+6'       : Number(m6).toLocaleString(),
                                'm+7'       : Number(m7).toLocaleString(),
                                'm+8'       : Number(m8).toLocaleString(),
                                'm+9'       : Number(m9).toLocaleString(),
                                'm+10'      : Number(m10).toLocaleString(),
                                'm+11'      : Number(m11).toLocaleString(),
                                'm+12'      : Number(m12).toLocaleString(),
                                'm+13'      : Number(m13).toLocaleString(),
                                'm+14'      : Number(m14).toLocaleString(),
                                'm+15'      : Number(m15).toLocaleString(),
                                'm+16'      : Number(m16).toLocaleString(),
                                'm+17'      : Number(m17).toLocaleString(),
                                'm+18'      : Number(m18).toLocaleString(),
                                'm+19'      : Number(m19).toLocaleString(),
                                'm+20'      : Number(m20).toLocaleString(),
                                'm+21'      : Number(m21).toLocaleString(),
                                'm+22'      : Number(m22).toLocaleString(),
                                'm+23'      : Number(m23).toLocaleString(),
                                'totalN'    : '',
                                'totalNPrev': '',
                            }

                            for (let [key, v] of Object.entries(value)) {
                              if (v == '0') {
                                    value[key] = '';
                              }
                            }

                            grid.addRowData("total-n-n1-row", value, 'first');
                        }
                    }
                }).trigger('reloadGrid', [{
                    page: 1,
                    current: true
                }]);
    		}
    	});
    }

    function isNumber(value) {

        if (typeof value == "string") {

            if (value.includes('-') || value == '' || value == 'NaN' || value == '""') {
                return false;
            }
            else{
                
                return Number(value);
            }
        }
        else{
            return Number(value);
        }
    }

    function cell_number_formatter(cell_value, options, row_object) {

        if (cell_value == undefined) {
            return '';
        }
        
        var new_value = cell_value;

        if (isNumber(cell_value)) {
            
            new_value = isNumber(cell_value).toLocaleString();

        }

        return new_value;
    }

    function instance_grid() {
        var colNames = ['Dossiers', 'Exercice', 'Clients', 'Total images', '< m', 'm1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7', 'm8', 'm9', 'm10', 'm11', 'm12', 'm13', 'm14', 'm15', 'm16', 'm17', 'm18', 'm19', 'm20', 'm21', 'm22', 'm23', 'm24', 'totalN', 'totalNPrev'];
    	
    	var colModel = [{
                    name    : 'dossier',
                    index   : 'dossier',
                    align   : 'left',
                    editable: false,
                    sortable: false,
                    width   : 150,
                    classes : 'js-dossier'
                }, {
                    name    : 'exercice',
                    index   : 'exercice',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 75,
                    classes : 'js-exercice'
                }, {
                    name    : 'client',
                    index   : 'client',
                    align   : 'left',
                    editable: false,
                    sortable: false,
                    width   : 125,
                    classes : 'js-client'
                }, {
                    name    : 'total',
                    index   : 'total',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 100,
                    classes : 'js-total',
                    formatter: 'number', 
                    formatoptions: { decimalPlaces: 0, defaultValue: '' }
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+24',
                    index   : 'm+24',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 100,
                    classes : 'js-m-25',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm',
                    index   : 'm',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+1',
                    index   : 'm+1',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-1',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+2',
                    index   : 'm+2',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-2',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+3',
                    index   : 'm+3',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-3',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+4',
                    index   : 'm+4',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-4',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+5',
                    index   : 'm+5',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-5',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+6',
                    index   : 'm+6',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-6',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+7',
                    index   : 'm+7',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-7',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+8',
                    index   : 'm+8',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-8',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+9',
                    index   : 'm+9',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-9',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+10',
                    index   : 'm+10',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-10',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+11',
                    index   : 'm+11',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-11',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+12',
                    index   : 'm+12',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-12',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+13',
                    index   : 'm+13',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-13',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+14',
                    index   : 'm+14',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-14',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+15',
                    index   : 'm+15',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-15',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+16',
                    index   : 'm+16',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-16',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+17',
                    index   : 'm+17',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-17',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+18',
                    index   : 'm+18',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-18',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+19',
                    index   : 'm+19',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-19',
                    // formatter: cell_number_formatter
                }
                , {
                    name    : 'm+20',
                    index   : 'm+20',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-20',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+21',
                    index   : 'm+21',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-21',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+22',
                    index   : 'm+22',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-22',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'm+23',
                    index   : 'm+23',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 50,
                    classes : 'js-m-23',
                    // formatter: cell_number_formatter
                }, {
                    name    : 'totalN',
                    index   : 'totalN',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 1,
                    classes : 'js-totalN'
                }, {
                    name    : 'totalNPrev',
                    index   : 'totalNPrev',
                    align   : 'center',
                    editable: false,
                    sortable: false,
                    width   : 1,
                    classes : 'js-totalNPrev'
                },

            ];

        var options = {
            datatype   : 'local',
            height     : 600,
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

        var tableau_grid = $('#grid-details');

        if (tableau_grid[0].grid == undefined) {
            tableau_grid.jqGrid(options);
        } else {
            delete tableau_grid;
            $('#grid-details').GridUnload('#grid-details');
            tableau_grid = $('#grid-details');
            tableau_grid.jqGrid(options);
        }

        tableau_grid.jqGrid('hideCol', ["totalN", "totalNPrev"]);

        var window_height = window.innerHeight - 300;

        if (window_height < 400) {
            tableau_grid.jqGrid('setGridHeight', 400);
        } else {
            tableau_grid.jqGrid('setGridHeight', window_height);
        }

        return tableau_grid;
    }

    $(window).resize(function() {
        
        resize_tab_details();

    });

    function resize_tab_details(argument) {
        setTimeout(function() {
                var tableau_grid = $('#grid-details');
                var window_height = window.innerHeight - 300;

                var width = tableau_grid.closest(".panel-body").width() + 25;

                tableau_grid.jqGrid("setGridWidth", width);

                if (window_height < 400) {
                    tableau_grid.jqGrid('setGridHeight', 400);
                } else {
                    tableau_grid.jqGrid('setGridHeight', window_height);
                }

            }, 600);
    }

});