$(document).ready(function() {
	$(document).on('click', '#btn-export-details',function() {
        var grid = $('#grid-details');
        var datas = grid.jqGrid('getGridParam', 'data');
        if (grid[0].grid == undefined || data.length == 0) {
        	show_info("Echec de téléchargement", "Tableau vide", "error");
            return false;
        } else {

        	$('#exp-datas').val(encodeURI(JSON.stringify(datas)));
            $('#exp-dossier').val($('#dossier-details').val());
            $('#exp-typedate').val($('#typedate-details').val());
            $('#exp-exercice').val($('#exercice-details').val());
            $('#exp-client').val($('#client-details').val());

            $('#form-export').attr('action',Routing.generate('general_details_export')).submit();
        }
		
	});
})