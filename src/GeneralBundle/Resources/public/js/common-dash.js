/**
 * Boutton GO
 */

var arrayMonths = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
var arrayShortMonths = ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'];
$('#btn-go-dashboard').on('click',function() {
    go();
})
var idModalInfo = 0;

/**
 * Réccupération des valeurs des tableaux
 */
function go(m = null) {
    if(m === null) m = 0; 
    var client   = client_selector.val();
    var exercice = exercice_selector.val();
    var dossier  = dossier_selector.val();
    var listDossier  = [];

    dossier_selector.find('option').each(function(){
        var dossierId = $(this).attr('value');
        if(dossierId != 0)
            listDossier.push(dossierId);
    });

    if (client == '' || exercice == '' || dossier == '') {
        show_info('Champs non Remplis', 'Veuillez Verifiez les Champs');
        return;
    } else{
        var url  = Routing.generate('dashboard_ajax');

        $.ajax({
			url     : url,
             data: {
                client  : client,
                exercice: exercice,
                dossier : dossier,
                moisData : m,
                listDossier : listDossier,
                type : parseInt($('.dash-type').attr('data-type'))
            }, 
			type: 'POST',
			datatype: 'json',
			async   : true,
			success : function(data) {
                var dashType = parseInt($('.dash-type').attr('data-type'));
                if(dashType) {
                    intance_travaux_a_realiser_grid(data.tarNames, data.tarTaches, data.date);
                }else{
                   intance_comptes_bancaires_grid(data.cb);
                    intance_bancaires_manquantes_grid(data.obm);
                    intance_bancaires_en_cours_grid(data.tbec); 
                }
                //resize_height();
                //intance_pieces_manquantes_grid(data.pm);
                //intance_travaux_en_cours_grid(data.tec);
                //intance_realisees_depassees_grid(data.rd);

                $('#expand-grid').removeClass('hidden');
            }
        });
    }
}

/**
 * Nom mois par numéro
 */
function month_num_to_name(num) {
    var months = ['Janv','Fév','Mar','Avr','Mai','Juin','Juil','Aou','Sept','Oct','Nov','Dec'];
    return months[num - 1] || '';
}

/**
 * jQGrid responsive
 */
function resize_grid(grid) {
    setTimeout(function() {
        var tableau_grid = $('#js_' + grid);
        var width        = tableau_grid.closest("#" + grid + "_container").width();
        tableau_grid.jqGrid("setGridWidth", width);
    }, 600);
}

var dashType = parseInt($('.dash-type').attr('data-type'));
if(dashType){
    var grids_name = ["travaux_a_realiser"];
}else{
   var grids_name = ["comptes_bancaires", "bancaires_manquantes", "bancaires_en_cours"]; 
}

/**
 * Redimensionner les tableaux
 */
$(window).resize(function() {
	grids_name.forEach(function(item) {
        if(item === "travaux_a_realiser"){
            var tableau_grid = $('#js_' + item);
            tableau_grid.jqGrid('destroyGroupHeader');
            resize_grid(item);
            grid_reconstruct_GroupHeaders();
        }else{
            resize_grid(item);
        }
	});
});

/**
 * Réduire ou développer les tableaux
 */
$('#expand-grid').on('click',function() {

	var class_attr = $(this).children().attr('class');

	if (class_attr == 'fa fa-angle-up') {
		$(this).children().removeClass('fa fa-angle-up');
		$(this).children().addClass('fa fa-angle-down');

		grids_name.forEach(function(item) {
			$("#js_" + item). jqGrid ('setGridState', 'hidden');
		});
	} else {
		$(this).children().removeClass('fa fa-angle-down');
		$(this).children().addClass('fa fa-angle-up');
		
		grids_name.forEach(function(item) {
			$("#js_" + item). jqGrid ('setGridState', 'visible');
		});
	}
});

$('.navbar-minimalize').on('click',function() {
    $(window).trigger('resize');
});

function intance_travaux_a_realiser_grid(dataNames, dataTaches, date) {
    var colNamesArray = [];
    var colModelsArray = [];
    var indexMoisModel = [];
    var nbColMoisCi = 0;
    var nbColMoisSuiv = 0;
    var debColMoisSuiv = '';
    var debColMoisCi = '';
    var debColMoisCiTest = '';
    var indexMois = 0;
    colNamesArray.push('Taches');
    $.each(dataNames, function (i,v){
        var names = v.split('+');
        if(names.length > 1){
            indexMois = names[1] - 1;
            var x = arrayShortMonths[indexMois]
            colNamesArray.push(x);
            indexMoisModel.push(names[1]);
        }else{
            colNamesArray.push(v);
        }
    });

    var z = 0;
    for (var i = 0; i < colNamesArray.length; i++) {
        var str;
        if (i === 0) {
            if(debColMoisCi === '') debColMoisCi = colNamesArray[i];
            str = {
                name: colNamesArray[i],
                index:colNamesArray[i],
                key:true,
                editable:false,
                sortable: false,
                align   : 'left',
                classes : 'tar_tache',
                width : 150
            };
        } else {
            if(isNaN(colNamesArray[i])){
                if(debColMoisSuiv === '') debColMoisSuiv = 'M+'+indexMoisModel[z];
                str = {
                    name: 'M+'+indexMoisModel[z],
                    index:'M+'+indexMoisModel[z],
                    editable:false,
                    sortable: false,
                    width : 55,
                    align   : 'center',
                    classes : 'M+'+indexMoisModel[z],
                    formatter: cell_tar_formatter
                };
                nbColMoisSuiv++; 
                z++; 
            }else{
                if(debColMoisCiTest === '') debColMoisCiTest = colNamesArray[i];
                str = {
                    name: colNamesArray[i],
                    index:colNamesArray[i],
                    editable:false,
                    sortable: false,
                    width : 50,
                    align   : 'center',
                    classes : colNamesArray[i],
                    formatter: cell_tar_formatter
                };
                nbColMoisCi++; 
            }
        }
        colModelsArray.push(str);
    }
   /* $.each(taches, function (i,v){
        colNames.push({
            name: 'list' + v.date,
            index: 'list-index' + v.date,
            align: 'right',
            editable: false,
            sortable: true,
            resizable: false,
            width: 50
        });
    });*/

    /*var total = nbColMoisSuiv + nbColMoisCi;
    if(total <= 6){
        $('.travaux_a_realiser_auto').parent().attr('class', 'col-lg-4');
        //$('.travaux_a_realiser_auto').css('width', '33.33333333%');
    }else if(total <= 10){
        $('.travaux_a_realiser_auto').parent().attr('class', 'col-lg-6');
        //$('.travaux_a_realiser_auto').css('width', '50%');
    }else if(total <= 16){
        $('.travaux_a_realiser_auto').parent().attr('class', 'col-lg-8');
        //$('.travaux_a_realiser_auto').css('width', '66.66666667%');
    }else if(total <= 22){
        $('.travaux_a_realiser_auto').parent().attr('class', 'col-lg-offset-1 col-lg-10');
        //$('.travaux_a_realiser_auto').css('width', '83.33333333%');
    }else{
        $('.travaux_a_realiser_auto').parent().attr('class', 'col-lg-12');
    }*/
   // $('.travaux_a_realiser_auto').parent().parent().remove('div');

    var tableau_grid = $('#js_travaux_a_realiser');
    var options = {
        datatype   : "jsonstring",
        datastr    : dataTaches,
        autowidth  : true,
        loadonce   : true,
        shrinkToFit: true,
        rownumbers : false,
        altRows    : false,
        colNames   : colNamesArray,
        colModel   : colModelsArray,
        viewrecords: true,
        hidegrid   : true,
        caption    : 'Nature et calendrier des tâches à réaliser',
        sortable   : false,
        treeGrid     : true,
        treeGridModel: 'adjacency',
        treedatatype : "local",
        ExpandColumn : 'name',
        /*loadComplete: function(data) {
            tableau_grid.closest('.ui-jqgrid')
                        .find('.'+debColMoisSuiv)
                        .css('border-left','1px solid #676a6c');
        },*/
    };

    if (tableau_grid[0].grid == undefined) {
        tableau_grid.jqGrid(options);
    } else {
        delete tableau_grid;
        $('#js_travaux_a_realiser').GridUnload('#js_travaux_a_realiser');
        tableau_grid = $('#js_travaux_a_realiser');
        tableau_grid.jqGrid(options);
    }

   /* tableau_grid.closest('.ui-jqgrid')
                .find('.ui-widget-header')
                .css('text-transform', 'none');*/

    tableau_grid.closest("div.ui-jqgrid-view")
                .children("div.ui-jqgrid-titlebar")
                .css("text-align", "center")
                .children("span.ui-jqgrid-title")
                .css("float", "none");

    var debMoisArrow = debColMoisCiTest

    /*tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
        groupHeaders: [ 
            { startColumnName :  debMoisArrow , numberOfColumns :  (nbColMoisCi+nbColMoisSuiv) , titleText :  '<span class="ce_mois_ci" data-mois="'+date+'"><i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+(date-2)+'"></i>&nbsp;'+arrayMonths[date-1]+'&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+date+'"></i> </span>' } , 
        ]
    });*/

    if(debMoisArrow === ''){
        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debColMoisSuiv , numberOfColumns :  nbColMoisSuiv , titleText :  '<span class="ce_mois_ci" data-mois="'+date+'"><i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+(date-2)+'" style="font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+arrayMonths[date-1]+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+date+'" style="font-size: 15px;"></i> </span>' } , 
            ]
        });

        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debColMoisSuiv , numberOfColumns :  nbColMoisSuiv , titleText :  '<span class="mois_suiv">Les mois prochains</span>' } 
            ]
        });
    }else{
        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debColMoisCi , numberOfColumns :  (nbColMoisCi+1) , titleText :  '<span class="ce_mois_ci" data-mois="'+date+'"><i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+(date-2)+'" style="font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+arrayMonths[date-1]+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+date+'" style="font-size: 15px;"></i> </span>' } , 
                { startColumnName :  debColMoisSuiv , numberOfColumns :  nbColMoisSuiv , titleText :  '<span class="mois_suiv">Les mois prochains</span>' } 
            ]
        });
    }

    $('#data-group-header').attr('data-debmois', debMoisArrow);
    $('#data-group-header').attr('data-debcolmois', debColMoisSuiv);
    $('#data-group-header').attr('data-nbcolmois', nbColMoisSuiv);
    $('#data-group-header').attr('data-date', date);
    $('#data-group-header').attr('data-dateprec', (date-2));
    $('#data-group-header').attr('data-debcolmois-ci', debColMoisCi);
    $('#data-group-header').attr('data-mois-ci', arrayMonths[date-1]);
    $('#data-group-header').attr('data-nbcolmois-ci', nbColMoisCi+1);

    var idSuiv = 'js_travaux_a_realiser_'+debColMoisSuiv;
    tableau_grid.closest('.ui-jqgrid')
                .find('table.ui-jqgrid-htable')
                .find('.jqg-third-row-header')
                .find('th[id="'+idSuiv+'"]')
                .css('border-left', '2px solid #c1bcbc');

    /*var widthTable = tableau_grid.closest('.ui-jqgrid')
                            .find('table.ui-jqgrid-htable')
                            .parent().parent().width();

    tableau_grid.closest('.ui-jqgrid')
                .find('table.ui-jqgrid-htable')
                .css('width', widthTable);*/

    tableau_grid.closest('table.ui-jqgrid-btable')
                .find('td[class="'+debColMoisSuiv+'"]')
                .attr('style', 'border-left: 2px solid #DDDDDD !important; text-align: center');

    var witdhTable = tableau_grid.closest('table.ui-jqgrid-btable')
                                .parent().parent().width();

    /*tableau_grid.closest('.ui-jqgrid')
                .find('table.ui-jqgrid-htable')
                .css('width', witdhTable);

    tableau_grid.closest('table.ui-jqgrid-btable')
                .css('width', witdhTable);*/

    /*tableau_grid.closest('table.ui-jqgrid-btable')
                .css('width', widthTable);*/

    $('.mois_suiv').parent().css('border-left', '2px solid #c1bcbc');

    renameElement(tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title'),'div'); 
    tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').css('margin-top','11px');

    var widthTab = tableau_grid.closest("#travaux_a_realiser_container").width();
    tableau_grid.closest('.ui-jqgrid').find('table.ui-jqgrid-htable').css('width', widthTab);
    tableau_grid.closest('.ui-jqgrid').find('table#js_travaux_a_realiser').css('width', widthTab);


    return tableau_grid;
}

function cell_tar_formatter(cell_value, options, row_object) {
    var new_val = '';
    var tache = row_object['Taches'];
    if(cell_value == undefined || cell_value == null || cell_value == '')
        return new_val;
    if(tache === 'Nb de taches' || tache === 'Images à traiter')
        return cell_value;
    return '<span class="pointer click_count_tache">'+cell_value+'</span>';
}

$(document).on('click', '.click_count_tache', function() {
    idModalInfo = idModalInfo + 1;
    var tacheDate = $(this).parent().attr('class');
    var tache = $(this).parent().parent().attr('id');
    var client   = client_selector.val();
    var exercice = exercice_selector.val();
    var dossier  = dossier_selector.val();
    var m  = $('.ce_mois_ci').attr('data-mois');
    var moisC = '';
    var tacheDateName = tacheDate.split('+');
    if(tacheDateName.length > 1){
        var kIndexMois = tacheDateName[1] - 1;
        moisC = arrayMonths[kIndexMois];
    }else{
        moisC = arrayMonths[m-1];
    }

    var url  = Routing.generate('dashboard_get_info_tache');
     $.ajax({
        url     : url,
        type    : 'POST',
        data: {
            client  : client,
            exercice: exercice,
            dossier : dossier,
            tacheDate: tacheDate,
            tache   : tache,
            moisData : m,
            moisC  : moisC,
            idModal  : idModalInfo
        }, 
        datatype: 'json',
        async   : true,
        success : function(data) {
            var tacheDateName = tacheDate.split('+');
            if(tacheDateName.length > 1){
                var kIndexMois = tacheDateName[1] - 1;
                tacheDateName = $('#jqgh_js_travaux_a_realiser_'+tacheDate).val();
                show_modal(data, tache +', échéance le mois de ' + arrayMonths[kIndexMois],  undefined, 'modal-x-lg');
            }else{
                show_modal(data, tache +', échéance le ' + tacheDate,  undefined, 'modal-x-lg');
            }
            console.log(idModalInfo);
            var titre = $('.info-'+ idModalInfo).attr('data-titre');
            console.log(titre);
            $('.modal-title').html(titre);
        }
    });
});

$(document).on('click', '.mois-suiv', function() {
    var msuiv = $(this).attr('data-suiv');
    getTarByDate(msuiv);

});

$(document).on('click', '.mois-prec', function() {
    var mprec = $(this).attr('data-prec');
    getTarByDate(mprec);
});

function getTarByDate(m) {
    m = parseInt(m);
    var ms = m + 1;
    var mp = m - 1;
    if(ms >= 12){
        ms = 0;
    }
    if(mp < 0){
        mp = 11;
    }
    $('.ce_mois_ci').html('<i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+mp+'" style="font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+arrayMonths[m]+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+ms+'" style="font-size: 15px;"></i>');
    $('.ce_mois_ci').attr('data-mois', m+1);
    go(m+1);
}

function renameElement($element,newElement){
    $element.wrap("<"+newElement+">");
    $newElement = $element.parent();

    $.each($element.prop('attributes'), function() {
        $newElement.attr(this.name,this.value);
    });

    $element.contents().unwrap();

     return $newElement;
}

function resize_height(argument) {
    $('.travaux_a_realiser_auto .ibox-contents').removeAttr("style"); 
    $('.ibox-banque-manquante .ibox-contents').removeAttr("style"); 
    $('.ibox-compte-banque .ibox-contents').removeAttr("style"); 
    $('.ibox-bancaire-en-cours .ibox-contents').removeAttr("style"); 

    var heightIboxTaf  = $('.travaux_a_realiser_auto').height();
    var heightIboxMan  = $('.ibox-banque-manquante').height();
    var heightIboxCpt  = $('.ibox-compte-banque').height();
    var heightIboxCour = $('.ibox-bancaire-en-cours').height();
    var heightIbox     = 0;

    heightIbox = (heightIboxTaf > heightIboxMan) ? heightIboxTaf : heightIboxMan;

    $('.travaux_a_realiser_auto .ibox-contents').css({'height': heightIbox, 'background': '#fff', 'margin-bottom': '25px'});
    $('.ibox-banque-manquante .ibox-contents').css({'height': heightIbox + 1, 'background': '#fff', 'margin-bottom': '25px'});
    $('.ibox-compte-banque .ibox-contents').css({'height': heightIbox + 1, 'background': '#fff', 'margin-bottom': '25px'});
    $('.ibox-bancaire-en-cours .ibox-contents').css({'height': heightIbox + 1, 'background': '#fff', 'margin-bottom': '25px'});
}

function grid_reconstruct_GroupHeaders() {
    var debmois = $('#data-group-header').attr('data-debmois');
    var debcolmois = $('#data-group-header').attr('data-debcolmois');
    var nbcolmois = $('#data-group-header').attr('data-nbcolmois');
    var date = $('#data-group-header').attr('data-date');
    var dateprec = $('#data-group-header').attr('data-dateprec');
    var moi = $('#data-group-header').attr('data-mois-ci');
    var debcolmois_ci = $('#data-group-header').attr('data-debcolmois-ci');
    var nbcolmois_ci = $('#data-group-header').attr('data-nbcolmois-ci');
    var tableau_grid = $('#js_travaux_a_realiser');
    if(debmois === ''){
        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debcolmois , numberOfColumns :  nbcolmois , titleText :  '<span class="ce_mois_ci" data-mois="'+date+'"><i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+dateprec+'" style="font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+moi+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+date+'" style="font-size: 15px;"></i> </span>' } , 
            ]
        });

        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debcolmois , numberOfColumns :  nbcolmois , titleText :  '<span class="mois_suiv">Les mois prochains</span>' } 
            ]
        });
    }else{
        tableau_grid. jqGrid('setGroupHeaders',{ 
        useColSpanStyle: false,  
            groupHeaders: [ 
                { startColumnName :  debcolmois_ci , numberOfColumns :  nbcolmois_ci , titleText :  '<span class="ce_mois_ci" data-mois="'+date+'"><i class="fa fa-chevron-left pointer mois-prec" data-prec= "'+dateprec+'" style="font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+moi+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right pointer mois-suiv" data-suiv= "'+date+'" style="font-size: 15px;"></i> </span>' } , 
                { startColumnName :  debcolmois , numberOfColumns :  nbcolmois , titleText :  '<span class="mois_suiv">Les mois prochains</span>' } 
            ]
        });
    }

    var idSuiv = 'js_travaux_a_realiser_'+debcolmois;
    tableau_grid.closest('.ui-jqgrid')
                .find('table.ui-jqgrid-htable')
                .find('.jqg-third-row-header')
                .find('th[id="'+idSuiv+'"]')
                .css('border-left', '2px solid #c1bcbc');

    tableau_grid.closest('table.ui-jqgrid-btable')
                .find('td[class="'+debcolmois+'"]')
                .attr('style', 'border-left: 2px solid #DDDDDD !important; text-align: center');

    var witdhTable = tableau_grid.closest('table.ui-jqgrid-btable')
                                .parent().parent().width();

    $('.mois_suiv').parent().css('border-left', '2px solid #c1bcbc');

    renameElement(tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title'),'div'); 
    tableau_grid.closest('.ui-jqgrid').find('.ui-jqgrid-title').css('margin-top','11px');

    var widthTab = tableau_grid.closest("#travaux_a_realiser_container").width();
    tableau_grid.closest('.ui-jqgrid').find('table.ui-jqgrid-htable').css('width', widthTab);
    tableau_grid.closest('.ui-jqgrid').find('table#js_travaux_a_realiser').css('width', widthTab);
}