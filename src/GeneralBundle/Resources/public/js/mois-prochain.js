// $(document).ready(function() {

// 	intance_mois_prochain_grid();

// 	function intance_mois_prochain_grid() {

// 		var now = new Date();

// 		var tab_mois = new Array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

// 		var m = now.getMonth();

// 		var m_1 = (m + 1 <= 11) ? m + 1 : m + 1 - 12;
// 		var m_2 = (m + 2 <= 11) ? m + 2 : m + 2 - 12;
// 		var m_3 = (m + 3 <= 11) ? m + 3 : m + 3 - 12;
// 		var m_4 = (m + 4 <= 11) ? m + 4 : m + 4 - 12;
// 		var m_5 = (m + 5 <= 11) ? m + 5 : m + 5 - 12;
// 		var m_6 = (m + 6 <= 11) ? m + 6 : m + 6 - 12;

// 		var colNames= ['',tab_mois[m_1], tab_mois[m_2], tab_mois[m_3], tab_mois[m_4], tab_mois[m_5], tab_mois[m_6]];

// 		var colModel= [{
// 			name    : 'list',
//             index   : 'list',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-list'
// 		}, {
// 			name    : 'm-1',
//             index   : 'm-1',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-1'
// 		}, {
// 			name    : 'm-2',
//             index   : 'm-2',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-2'
// 		}, {
// 			name    : 'm-3',
//             index   : 'm-3',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-3'
// 		}, {
// 			name    : 'm-4',
//             index   : 'm-4',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-4'
// 		}, {
// 			name    : 'm-5',
//             index   : 'm-5',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-5'
// 		}, {
// 			name    : 'm-6',
//             index   : 'm-6',
//             align   : 'left',
//             editable: false,
//             sortable: true,
//             width   : 125,
//             classes : 'js-m-6'
// 		}];

// 		var options = {
//             datatype: 'local',
//             height: 200,
//             autowidth: true,
//             loadonce: true,
//             shrinkToFit: true,
//             rownumbers: false,
//             altRows: false,
//             colNames: colNames,
//             colModel: colModel,
//             viewrecords: true,
//             hidegrid: true,
//         	caption: 'MOIS PROCHAIN',
//         	sortable: true

//         };

//         var tableau_grid = $('#js_mois_prochain');

//         if (tableau_grid[0].grid == undefined) {

//             tableau_grid.jqGrid(options);

//         } else {
//             delete tableau_grid;
//             $('#js_mois_prochain').GridUnload('#js_mois_prochain');
//             tableau_grid = $('#js_mois_prochain');
//             tableau_grid.jqGrid(options);
//         }

//         return tableau_grid;

// 	}

//     function resize_tab_mois_prochain() {
//         setTimeout(function() {
//             var tableau_grid = $('#js_mois_prochain');

//             var width = tableau_grid.closest("#mois_prochain_container").width();

//             tableau_grid.jqGrid("setGridWidth", width);

//         }, 600);
//     }

//     $(window).resize(function() {
//         resize_tab_mois_prochain();
//     });

// });