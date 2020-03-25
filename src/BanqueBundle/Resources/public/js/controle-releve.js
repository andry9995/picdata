$(document).ready( function () {

    function releveCellAttr(rowId, val, rawObject, cm, rdata) {
        var controleIndex = -1;
        //Verifier aloha ny size an'ilay tableau: raha misy colonne dossier dia 15 raha tsisy dia 14
        if (rawObject.length == 14) {
            controleIndex = 9;
        }
        else if (rawObject.length == 15) {
            controleIndex = 10;
        }

        if (val == '0,00' || val == '0.00') {

            if (cm.name === 'releveSoldeFinal' || cm.name === 'releveSoldeInit') {


                if (rawObject[controleIndex] == undefined) {
                    return ' style="background:#fcd5b4;color:transparent;"';
                }
            }
        }

        if (val === '' || val === undefined || val === '&nbsp;' || val === '&#160;') {
            if (cm.name !== 'releveControle' && cm.name !== 'releveSoldeFinal' && cm.name !== 'releveSoldeInit') {
                return ' style="background:#fcd5b4;color:transparent;"';
            }

        }


        if (cm.name == 'releveDoublon') {
            if (val !== '&#160;') {
                return ' title="Considerer l\'image comme doublon"';
            }
        }

        if (cm.name === 'releveControle') {
            // if(val !== '&#160;'){
            //     return ' style="background:#fcd5b4;"';
            // }

            if (val === 'Relevé Manquant') {
                return ' style="color:#ed5565;font-weight:bold;"';
            }

            else if (val === 'Date à verifier') {
                return ' style="color:rgb(248, 172, 89);font-weight:bold"';
            }
            else if (val !== '&#160;') {
                return ' style="background:#fcd5b4;color:transparent;"';
            }
        }

        if (cm.name === 'releveSoldeFinal' || cm.name === 'releveSoldeInit') {
            if (controleIndex != -1) {
                if (rawObject[controleIndex] == 'Relevé Manquant' || rawObject[controleIndex] == '...') {

                    if(val == '0.00' || val == "0,00"){
                        return ' style="background:#fcd5b4;color:transparent;"';
                    }
                    // return ' style="background:#fcd5b4;color:transparent;"';
                }

            }
        }

    }


    var window_height = window.innerHeight;
    var gridWidth = releveGrid.closest("div.row").width();
    var gridHeight = window_height - 200;

    var idClient = $('#client').val();
    var idSite = $('#site').val();
    var idDossier = $('#dossier').val();
    var idBanque = $('#js_banque').val();
    var exercice = $('#js_exercice').val();

    releveGrid.jqGrid({

        datatype: 'json',
        // url: url,
        mtype: 'POST',
        loadonce: false,
        sortable: false,
        autowidth: true,
        height: gridHeight,
        width: gridWidth,
        shrinkToFit: true,
        viewrecords: true,
        pager: '#js_controle_releve_pager',
        hidegrid: false,
        colNames: ['Dossier', 'Banque', 'N° Compte', 'Mois', 'Période Début', 'Période Fin', 'N° Relevé', 'Solde Init',
            'Solde Final', 'Controle', 'Date Scan', '', 'Image', 'Doublon'/*, 'precedent', 'suivant', 'intermedaire'*/],
        colModel: [
            {
                name: 'releveDossier', index: 'releveDossier', editable: false,
                editoptions: {defaultValue: ''},
                classes: 'js-releve-dossier',
                width: 100,
                fixed: true,
                cellattr: releveCellAttr
            },
            {
                name: 'releveBanque', index: 'releveBanque', editable: false,
                editoptions: {defaultValue: ''},
                classes: 'js-releve-banque',
                cellattr: releveCellAttr
            },
            {
                name: 'releveNumCompte', index: 'releveNumCompte', editable: false,
                editoptions: {defaultValue: ''},
                classes: 'js-releve-numcompte',
                sorttype: 'text',
                cellattr: releveCellAttr
            },
            {
                name: 'releveMois', index: 'releveMois', editable: false,
                editoptions: {defaultValue: ''},
                width: 40,
                fixed: true,
                classes: 'js-releve-mois',
                sorttype: 'text',
                cellattr: releveCellAttr
            },
            {
                name: 'relevePeriodeDeb', index: 'relevePeriodeDeb', editable: false,
                editoptions: {defaultValue: ''},
                width: 100,
                fixed: true,
                classes: 'js-releve-periodedeb',
                formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                cellattr: releveCellAttr
            },
            {
                name: 'relevePeriodeFin', index: 'relevePeriodeFin', editable: false,
                editoptions: {defaultValue: ''},
                width: 100,
                fixed: true,
                align: 'center',
                classes: 'js-releve-periodefin',
                formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                cellattr: releveCellAttr

            },
            {
                name: 'releveNum', index: 'releveNum', editable: false,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                align: 'center',
                classes: 'js-releve-releve',
                cellattr: releveCellAttr
            },
            {
                name: 'releveSoldeInit', index: 'releveSoldeInit', editable: false,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                align: "right",
                formatter: "number",
                sorttype: "number",
                classes: 'js-releve-soldeinit',
                cellattr: releveCellAttr
            },
            {
                name: 'releveSoldeFinal', index: 'releveSoldeFinal', editable: false,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                align: "right",
                formatter: "number",
                sorttype: "number",
                classes: 'js-releve-soldefinal',
                cellattr: releveCellAttr
            },
            {
                name: 'releveControle', index: 'releveControle', editable: false,
                editoptions: {defaultValue: ''},
                align: "center",
                classes: 'js-releve-controle',
                cellattr: releveCellAttr
            },
            {
                name: 'releveDateScan', index: 'releveDateScan', editable: false,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                align: 'center',
                classes: 'js-releve-datescan',
                formatter: 'date',
                formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                cellattr: releveCellAttr

            },
            {
                name: 'pieceDetails', index: 'pieceDetails', editable: false,
                editoptions: {defaultValue: ''},
                width: 20,
                fixed: true,
                align: 'center',
                classes: 'js-piece-details',
                cellattr: releveCellAttr
            },
            {
                name: 'releveImage', index: 'releveImage', editable: false,
                editoptions: {defaultValue: ''},
                width: 80,
                fixed: true,
                align: 'center',
                classes: 'js-releve-image',
                cellattr: releveCellAttr

            },
            {
                name: 'releveDoublon', index: 'releveDoublon', editable: false,
                editoptions: {defaultValue: ''},
                width: 60,
                fixed: true,
                align: 'center',
                classes: 'js-releve-doublon',
                cellattr: releveCellAttr
            }
            // ,
            // {
            //     name: 'relevePrecedent', index: 'relevePrecedent', editable: false,
            //     editoptions: {defaultValue: ''},
            //     align: "center",
            //     classes: 'js-releve-controle',
            //     cellattr: releveCellAttr
            // },
            // {
            //     name: 'releveSuivant', index: 'releveSuivant', editable: false,
            //     editoptions: {defaultValue: ''},
            //     align: "center",
            //     classes: 'js-releve-controle',
            //     cellattr: releveCellAttr
            // },
            // {
            //     name: 'releveIntermediaire', index: 'releveIntermediaire', editable: false,
            //     editoptions: {defaultValue: ''},
            //     align: "center",
            //     classes: 'js-releve-controle',
            //     cellattr: releveCellAttr
            // }

        ],


        loadComplete: function (data) {


            var sumInit = releveGrid.jqGrid('getCol', 'releveSoldeInit', false, 'sum');
            var sumFinal = releveGrid.jqGrid('getCol', 'releveSoldeFinal', false, 'sum');

            releveGrid.jqGrid('footerData', 'set', {
                pieceDateFact: 'Total',
                releveSoldeInit: parseFloat(sumInit).toFixed(2),
                releveSoldeFinal: parseFloat(sumFinal).toFixed(2)
            });

            if (data.isDossier == 1) {
                releveGrid.jqGrid('hideCol', 'releveDossier');
            }
            else {
                releveGrid.jqGrid('showCol', 'releveDossier');
            }

            // releveGrid.jqGrid('setGridWidth', gridWidth);


            // var w = releveGrid.parent().width();
            var h = $(window).height() - 200;
            //
            setTableauWidth();


            releveGrid.jqGrid('setGridHeight', h);
        },

        gridComplete: function () {
            // var rows = $('#js_releve_liste').getDataIDs();
            // for (var i = 0; i < rows.length; i++)
            // {
            //     var status = '';
            //
            //     status = $('#js_releve_liste').getCell(rows[i],'releveControle');
            //
            //     if(status != '&#160;')
            //     {
            //         $('#js_releve_liste').jqGrid('setRowData',rows[i],false, {  color:'white',weightfont:'bold',background:'blue'});
            //     }
            // }
        },

        footerrow: true,
        userDataOnFooter: true,
        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    $(document).on('click', '.js-releve-doublon', function () {

        var imageId = $(this).closest('tr').attr('id');
        console.log(imageId);

        swal({
            title: 'Doublon',
            text: "Voulez-vous considérer cette image comme doublon?",
            type: 'question',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui',
            cancelButtonText: 'Non'
        }).then(function () {

            $.ajax({
                url: Routing.generate('banque_releve_doublon'),
                type: 'POST',
                contentType: "application/x-www-form-urlencoded;charset=utf-8",
                beforeSend: function (jqXHR) {
                    jqXHR.overrideMimeType('text/html;charset=utf-8');
                },
                async: true,
                data: {
                    imageId: imageId
                },
                success: function (data) {

                    idClient = $('#client').val();
                    idSite = $('#site').val();
                    idDossier = $('#dossier').val();
                    idBanque = $('#js_banque').val();
                    exercice = $('#js_exercice').val();

                    var url = Routing.generate('banque_controle_releve_grid');

                    releveGrid.jqGrid('clearGridData');

                    releveGrid.jqGrid('setGridParam', {
                        url: url,
                        postData: {
                            clientId: idClient,
                            siteId: idSite,
                            dossierId: idDossier,
                            banqueId: idBanque,
                            exercice: exercice

                        },
                        footerrow: true
                    }).trigger('reloadGrid');


                    show_info('Doublon', 'Image considérée comme doublon.', 'success');
                }
            });
        });

    });

    $(document).on('click', '.js-piece-details', function () {

        var lastsel_piece = $(this).closest('tr').attr('id');
        var height = $(window).height() * 0.95;
        var idClient = $('#client').val();
        var exercice = $('#js_exercice').val();

        $.ajax({
            data: {
                imageId: lastsel_piece,
                clientId: idClient,
                exercice: exercice,
                height: height,
                cr: 0
            },
            url: Routing.generate('consultation_piece_data_image'),
            type: 'POST',
            async: true,
            dataType: 'html',
            success: function (data) {
                var options = {modal: false, resizable: true, title: 'Détails Pièces'};
                modal_ui(options, data, undefined, 0.95, 0.85);
            }
        });

    });

});



function setTableauWidth() {

    var windowWitdh = $(window).width();

    if (windowWitdh > 1200) {

        $("#js_banque_liste").jqGrid('setGridWidth', $('#wrapper-content').width());

        $("#js_controle_releve_liste").jqGrid('setGridWidth', $('#wrapper-content').width());
    }
}

function goControleReleve(){

    var idClient = $('#client').val();
    var idSite = $('#site').val();
    var idDossier = $('#dossier').val();
    var idBanque = $('#js_banque').val();
    var exercice = $('#exercice').val();
    var numCompte = $('#js_banque_compte').val();


    var url = Routing.generate('banque_controle_releve_grid');
    releveGrid.jqGrid('clearGridData');
    releveGrid.jqGrid('setGridParam', {
        url: url,
        postData: {
            clientId: idClient,
            siteId: idSite,
            dossierId: idDossier,
            banqueId: idBanque,
            exercice: exercice,
            numCompte: numCompte
        },
        footerrow: true
    }).trigger('reloadGrid');
}



