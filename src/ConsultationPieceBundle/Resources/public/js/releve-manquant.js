//
// var selectBanque = 0,
//     releveGrid = $('#js_releve_liste');
//
// $(document).ready( function () {
//
//     function releveCellAttr(rowId, val, rawObject, cm, rdata) {
//
//
//         var controleIndex = -1;
//         //Verifier aloha ny size an'ilay tableau: raha misy colonne dossier dia 15 raha tsisy dia 14
//         if(rawObject.length  == 14) {
//             controleIndex = 9;
//         }
//         else if (rawObject.length  == 15) {
//             controleIndex = 10;
//         }
//
//
//         if(val == '0,00' || val == '0.00') {
//
//             if(cm.name === 'releveSoldeFinal' || cm.name === 'releveSoldeInit'){
//
//
//                 if(rawObject[controleIndex] == undefined){
//                     return ' style="background:#fcd5b4;color:transparent;"';
//                 }
//             }
//
//
//
//         }
//
//         if(val === '' || val === undefined || val === '&nbsp;' || val === '&#160;'){
//             if(cm.name !== 'releveControle' && cm.name !== 'releveSoldeFinal' && cm.name !== 'releveSoldeInit') {
//                 return ' style="background:#fcd5b4;color:transparent;"';
//             }
//
//         }
//
//
//
//
//         if(cm.name == 'releveDoublon'){
//             if(val !== '&#160;' ){
//                 return ' title="Considerer l\'image comme doublon"';
//             }
//         }
//
//         if(cm.name === 'releveControle'){
//             // if(val !== '&#160;'){
//             //     return ' style="background:#fcd5b4;"';
//             // }
//
//             if(val === 'Relevé Manquant'){
//                 return ' style="color:#ed5565;font-weight:bold;"';
//             }
//
//             else if(val === 'Date à verifier'){
//                 return ' style="color:rgb(248, 172, 89);font-weight:bold"';
//             }
//             else if(val !== '&#160;'){
//                 return ' style="background:#fcd5b4;color:transparent;"';
//             }
//         }
//
//         if(cm.name === 'releveSoldeFinal' || cm.name === 'releveSoldeInit'){
//             if(controleIndex != -1) {
//                 if (rawObject[controleIndex] == 'Relevé Manquant' || rawObject[controleIndex] == '...') {
//                     return ' style="background:#fcd5b4;color:transparent;"';
//                 }
//
//             }
//         }
//
//     }
//
//     charger_site_consultation();
//     var window_height = window.innerHeight;
//     var gridWidth = releveGrid.closest("div.row").width();
//     var gridHeight = window_height - 250;
//
//     var idClient = $('#client').val();
//     var idSite = $('#site').val();
//     var idDossier = $('#dossier').val();
//     var idBanque = $('#js_banque').val();
//     var exercice = $('#js_exercice').val();
//
//     var numCompte = '';
//
//     var clickFiltre = 0;
//     var url = Routing.generate('consultation_piece_releve_grid');
//
//     releveGrid.jqGrid({
//
//         datatype: 'json',
//         url: url,
//         mtype: 'POST',
//         loadonce: false,
//         sortable: false,
//         autowidth: true,
//         height: gridHeight,
//         width: gridWidth,
//         shrinkToFit: true,
//         viewrecords: true,
//         pager: '#js_releve_pager',
//         hidegrid: false,
//         colNames: ['Dossier','Banque', 'N° Compte', 'Mois', 'Période Début', 'Période Fin', 'N° Relevé', 'Solde Init',
//             'Solde Final', 'Controle', 'Date Scan', '', 'Image', 'Doublon'/*, 'precedent', 'suivant', 'intermedaire'*/],
//         colModel: [
//             {
//                 name: 'releveDossier', index: 'releveDossier', editable: false,
//                 editoptions: {defaultValue: ''},
//                 classes: 'js-releve-dossier',
//                 width: 100,
//                 fixed: true,
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveBanque', index: 'releveBanque', editable: false,
//                 editoptions: {defaultValue: ''},
//                 classes: 'js-releve-banque',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveNumCompte', index: 'releveNumCompte', editable: false,
//                 editoptions: {defaultValue: ''},
//                 classes: 'js-releve-numcompte',
//                 sorttype: 'text',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveMois', index: 'releveMois', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 40,
//                 fixed: true,
//                 classes: 'js-releve-mois',
//                 sorttype: 'text',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'relevePeriodeDeb', index: 'relevePeriodeDeb', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 100,
//                 fixed: true,
//                 classes: 'js-releve-periodedeb',
//                 formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'relevePeriodeFin', index: 'relevePeriodeFin', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 100,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-releve-periodefin',
//                 formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
//                 cellattr: releveCellAttr
//
//             },
//             {
//                 name: 'releveNum', index: 'releveNum', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 80,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-releve-releve',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveSoldeInit', index: 'releveSoldeInit', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 80,
//                 fixed: true,
//                 align: "right",
//                 formatter: "number",
//                 sorttype: "number",
//                 classes: 'js-releve-soldeinit',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveSoldeFinal', index: 'releveSoldeFinal', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 80,
//                 fixed: true,
//                 align: "right",
//                 formatter: "number",
//                 sorttype: "number",
//                 classes: 'js-releve-soldefinal',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveControle', index: 'releveControle', editable: false,
//                 editoptions: {defaultValue: ''},
//                 align: "center",
//                 classes: 'js-releve-controle',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveDateScan', index: 'releveDateScan', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 80,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-releve-datescan',
//                 formatter: 'date',
//                 formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
//                 cellattr: releveCellAttr
//
//             },
//             {
//                 name: 'pieceDetails', index: 'pieceDetails', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 20,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-piece-details',
//                 cellattr: releveCellAttr
//             },
//             {
//                 name: 'releveImage', index: 'releveImage', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 80,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-releve-image',
//                 cellattr: releveCellAttr
//
//             },
//             {
//                 name: 'releveDoublon', index: 'releveDoublon', editable: false,
//                 editoptions: {defaultValue: ''},
//                 width: 60,
//                 fixed: true,
//                 align: 'center',
//                 classes: 'js-releve-doublon',
//                 cellattr: releveCellAttr
//             }
//             // ,
//             // {
//             //     name: 'relevePrecedent', index: 'relevePrecedent', editable: false,
//             //     editoptions: {defaultValue: ''},
//             //     align: "center",
//             //     classes: 'js-releve-controle',
//             //     cellattr: releveCellAttr
//             // },
//             // {
//             //     name: 'releveSuivant', index: 'releveSuivant', editable: false,
//             //     editoptions: {defaultValue: ''},
//             //     align: "center",
//             //     classes: 'js-releve-controle',
//             //     cellattr: releveCellAttr
//             // },
//             // {
//             //     name: 'releveIntermediaire', index: 'releveIntermediaire', editable: false,
//             //     editoptions: {defaultValue: ''},
//             //     align: "center",
//             //     classes: 'js-releve-controle',
//             //     cellattr: releveCellAttr
//             // }
//
//         ],
//
//
//         loadComplete: function (data) {
//
//
//             var sumInit = releveGrid.jqGrid('getCol', 'releveSoldeInit', false, 'sum');
//             var sumFinal = releveGrid.jqGrid('getCol', 'releveSoldeFinal', false, 'sum');
//
//             releveGrid.jqGrid('footerData', 'set', {
//                 pieceDateFact: 'Total',
//                 releveSoldeInit: parseFloat(sumInit).toFixed(2),
//                 releveSoldeFinal: parseFloat(sumFinal).toFixed(2)
//             });
//
//             if(data.isDossier == 1){
//                 releveGrid.jqGrid('hideCol','releveDossier');
//             }
//             else{
//                 releveGrid.jqGrid('showCol','releveDossier');
//             }
//
//             // releveGrid.jqGrid('setGridWidth', gridWidth);
//
//
//             // var w = releveGrid.parent().width();
//             var h = $(window).height() - 290;
//             //
//             setTableauWidth();
//
//
//             releveGrid.jqGrid('setGridHeight', h);
//         },
//
//         gridComplete: function()
//         {
//             // var rows = $('#js_releve_liste').getDataIDs();
//             // for (var i = 0; i < rows.length; i++)
//             // {
//             //     var status = '';
//             //
//             //     status = $('#js_releve_liste').getCell(rows[i],'releveControle');
//             //
//             //     if(status != '&#160;')
//             //     {
//             //         $('#js_releve_liste').jqGrid('setRowData',rows[i],false, {  color:'white',weightfont:'bold',background:'blue'});
//             //     }
//             // }
//         },
//
//         footerrow: true,
//         userDataOnFooter: true,
//         ajaxRowOptions: {async: true},
//         reloadGridOptions: {fromServer: true}
//
//     });
//
//     $(document).on('change', '#client', function () {
//         releveGrid.jqGrid('clearGridData');
//         banqueGrid.jqGrid('clearGridData');
//
//         $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
//
//         selectBanque = 0;
//
//     });
//
//     $(document).on('change','#site',function () {
//         charger_dossier_consultation();
//
//         releveGrid.jqGrid('clearGridData');
//         banqueGrid.jqGrid('clearGridData');
//
//
//         releveGrid.jqGrid('setGridParam',{
//             footerrow: false
//         });
//
//         selectBanque = 0;
//
//         $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
//     });
//
//     $(document).on('change', '#dossier', function () {
//         releveGrid.jqGrid('clearGridData');
//
//         releveGrid.jqGrid('setGridParam',{
//             footerrow: false
//         });
//
//         selectBanque = 0;
//
//         $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
//
//         charger_banque($('#dossier').val());
//
//
//         banqueGrid.jqGrid('clearGridData');
//
//         banqueGrid.jqGrid('setGridParam',{
//             footerrow: false
//         });
//
//     });
//
//     $(document).on('change', '#js_exercice', function () {
//         releveGrid.jqGrid('clearGridData');
//
//         releveGrid.jqGrid('setGridParam',{
//             footerrow: false
//         });
//
//
//         if(selectBanque == 0){
//             verifierMultipleNumCompte();
//         }
//     });
//
//     $(document).on('change', '#js_banque', function(){
//         releveGrid.jqGrid('clearGridData');
//
//         releveGrid.jqGrid('setGridParam',{
//             footerrow: false
//         });
//
//         $('#js_num_compte_hidden').val("").attr('data-id',$('#js_zero_boost').val());
//         selectBanque = 0;
//
//         verifierMultipleNumCompte();
//
//     });
//
//     /**
//      * GO
//      */
//     $(document).on('click', '#btn-go-releve-manquant', function() {
//         go();
//
//         $('#js_id_container_tabs a[href="#tab-principal"]').tab('show');
//
//         lineChart();
//
//     });
//     $(document).on('click','#js_id_container_tabs .nav-tabs li',function(){
//         go();
//     });
//
//     $(document).on('click', '#btn-select-num-compte', function(){
//         $('#js_num_compte_hidden').val($('#js_num_compte').val()).attr('data-id',$('#js_num_compte option:selected').attr('data-id'));
//         $('#num-compte-modal').modal('hide');
//
//         selectBanque = 1;
//     });
//
//
//     $(document).on('click', '.js-releve-doublon', function(){
//
//         var imageId = $(this).closest('tr').attr('id');
//         console.log(imageId);
//
//         swal({
//             title: 'Doublon',
//             text: "Voulez-vous considérer cette image comme doublon?",
//             type: 'question',
//             showCancelButton: true,
//             reverseButtons: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Oui',
//             cancelButtonText: 'Non'
//         }).then(function () {
//
//             $.ajax({
//                 url: Routing.generate('consultation_piece_releve_banque_doublon'),
//                 type: 'POST',
//                 contentType: "application/x-www-form-urlencoded;charset=utf-8",
//                 beforeSend: function (jqXHR) {
//                     jqXHR.overrideMimeType('text/html;charset=utf-8');
//                 },
//                 async: true,
//                 data: {
//                     imageId: imageId
//                 },
//                 success: function(data) {
//
//                     idClient = $('#client').val();
//                     idSite = $('#site').val();
//                     idDossier = $('#dossier').val();
//                     idBanque = $('#js_banque').val();
//                     exercice = $('#js_exercice').val();
//
//
//                     releveGrid.jqGrid('clearGridData');
//
//                     releveGrid.jqGrid('setGridParam', {
//                         postData: {
//                             clientId: idClient,
//                             siteId: idSite,
//                             dossierId: idDossier,
//                             banqueId: idBanque,
//                             exercice: exercice
//
//                         },
//                         footerrow: true
//                     }).trigger('reloadGrid');
//
//
//                     show_info('Doublon', 'Image considérée comme doublon.', 'success');
//                 }
//             });
//         });
//
//     });
//
//
//     $(document).on('click', '#js_banque',function () {
//         clickFiltre++;
//         if (clickFiltre == 2) {
//             $(this).change();
//             clickFiltre = 0;
//         }
//     });
//
//
//     $(document).on('click', '.navbar-minimalize', function () {
//         setTimeout(function () {
//                 setTableauWidth();
//         }, 1000);
//     });
//
//
//     $(window).on('resize', function() {
//             setTableauWidth();
//
//         setChartHeight();
//     });
// });
//
// function go()
// {
//     var tab_active = parseInt($('#js_id_container_tabs .nav-tabs li.active').attr('data-val'));
//     selectBanque = 0;
//     var idClient = $('#client').val(),
//         idSite = $('#site').val(),
//         idDossier = $('#dossier').val(),
//         idBanque = $('#js_banque').val(),
//         exercice = $('#js_exercice').val(),
//         numCompte = $('#js_num_compte_hidden').val();
//
//     if(tab_active == 0){
//
//         if( $('#dossier option:selected').text().trim() == '' ||
//             $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
//             $('#dossier').length <= 0 ||
//             parseInt($('#js_exercice').val()) == 0
//         ){
//             show_info('NOTICE','CHOISIR UN DOSSIER ET UN EXERCICE','error');
//             return;
//         }
//
//         lineChart();
//     }
//
//     if(tab_active == 1){
//
//         if( $('#dossier option:selected').text().trim() == '' ||
//             $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
//             $('#dossier').length <= 0 ||
//             parseInt($('#js_exercice').val()) == 0
//         ){
//             show_info('NOTICE','CHOISIR UN DOSSIER ET UN EXERCICE','error');
//             return;
//         }
//
//         banqueGrid.jqGrid('clearGridData');
//         banqueGrid.jqGrid('setGridParam', {
//             postData: {
//                 dossierId: idDossier
//             },
//             editurl: Routing.generate('banque_compte_edit', {dossierId: idDossier}),
//             footerrow: true
//         }).trigger('reloadGrid');
//     }
//
//     else if(tab_active == 2)
//     {
//         if( $('#dossier option:selected').text().trim() == '' ||
//             $('#dossier option:selected').text().trim().toUpperCase() == 'TOUS' ||
//             $('#dossier').length <= 0 ||
//             parseInt($('#js_exercice').val()) == 0
//         ){
//             show_info('NOTICE','CHOISIR UN DOSSIER ET UN EXERCICE','error');
//             return;
//         }
//
//
//         releveGrid.jqGrid('clearGridData');
//         releveGrid.jqGrid('setGridParam', {
//             postData: {
//                 clientId: idClient,
//                 siteId: idSite,
//                 dossierId: idDossier,
//                 banqueId: idBanque,
//                 exercice: exercice,
//                 numCompte: numCompte
//             },
//             footerrow: true
//         }).trigger('reloadGrid');
//     }
//     else if(tab_active == 3)
//     {
//         charger_analyse();
//     }
// }
//
// function setTableauWidth() {
//
//     var windowWitdh = $(window).width();
//
//     if (windowWitdh > 1200) {
//
//         $("#js_banque_liste").jqGrid('setGridWidth', $('#wrapper-content').width());
//
//         $("#js_releve_liste").jqGrid('setGridWidth', $('#wrapper-content').width());
//     }
// }
//
// function setChartHeight(){
//
//     $('#lineChart').css({height: '300px'});
//
// }
//
//
// function lineChart(){
//     var lineData = {
//         labels: ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
//         datasets: [
//             {
//                 label: "Example dataset",
//                 fillColor: "rgba(220,220,220,0.5)",
//                 strokeColor: "rgba(220,220,220,1)",
//                 pointColor: "rgba(220,220,220,1)",
//                 pointStrokeColor: "#fff",
//                 pointHighlightFill: "#fff",
//                 pointHighlightStroke: "rgba(220,220,220,1)",
//                 data: [65, 59, 40, 51, 36, 25, 40, 48, 48, 60, 39, 56, 37, 30]
//             },
//             {
//                 label: "Example dataset",
//                 fillColor: "rgba(26,179,148,0.5)",
//                 strokeColor: "rgba(26,179,148,0.7)",
//                 pointColor: "rgba(26,179,148,1)",
//                 pointStrokeColor: "#fff",
//                 pointHighlightFill: "#fff",
//                 pointHighlightStroke: "rgba(26,179,148,1)",
//                 data: [48, 48, 60, 39, 56, 37, 30, 65, 59, 40, 51, 36, 25, 40]
//             }
//         ]
//     };
//
//     var lineOptions = {
//         scaleShowGridLines: true,
//         scaleGridLineColor: "rgba(0,0,0,.05)",
//         scaleGridLineWidth: 1,
//         bezierCurve: true,
//         bezierCurveTension: 0.4,
//         pointDot: true,
//         pointDotRadius: 4,
//         pointDotStrokeWidth: 1,
//         pointHitDetectionRadius: 20,
//         datasetStroke: true,
//         datasetStrokeWidth: 2,
//         datasetFill: true,
//         responsive: true
//     };
//
//
//     var ctx = document.getElementById("lineChart").getContext("2d");
//     var myNewChart = new Chart(ctx).Line(lineData, lineOptions);
//     setChartHeight();
//     $('#js_releve_mois_encours').html('5 241 &euro;');
//     $('#js_nb_piece_affecter').html('540');
//     $('#js_nb_ligne_valider').html('785');
//
// }
//
// function verifierMultipleNumCompte () {
//     var idBanque = $('#js_banque').val();
//     var idDossier = $('#dossier').val();
//     $.ajax({
//         data: {
//             dossierId: idDossier,
//             banqueId: idBanque
//         },
//         url: Routing.generate('consultation_piece_releve_num_compte'),
//         type: 'POST',
//         async: true,
//         dataType: 'html',
//         success: function (data) {
//             var res = JSON.parse(data);
//             if(res.length > 1){
//                 $('#num-compte-modal').modal('show');
//                 $('#js_num_compte').children().remove().end().append('<option value="">Tous</option>');
//
//                 $.each(res, function (index,value) {
//                     $('<option>').val(value.compte).text(value.compte).attr('data-id',value.id).appendTo('#js_num_compte');
//                 });
//             }
//         }
//     })
// }
//
