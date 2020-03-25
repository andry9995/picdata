
$(document).ready(function() {
    $(document).on('click', '#btn-export', function(){
        var tableau_grid = $('#js_tableau'),
            rows = tableau_grid.jqGrid('getGridParam', 'data'),
            datas = [];

        $.each(rows, function (index,value) {

            // var dossier = value['tableau-dossier'],
            //     dossierTxt = (dossier === undefined) ? '' : dossier,
            //     imagen = value['tableau-img-n'],
            //     imagen1 = value['tableau-img-n-1'];
            //
            // if(typeof dossier === 'string'){
            //     if(dossier.indexOf('span') >= 0){
            //         dossierTxt = $(dossier).text();
            //     }
            // }
            //
            // if(imagen === undefined){
            //     imagen = '';
            // }
            // else{
            //     if(typeof imagen === 'string') {
            //         if (imagen.indexOf('encours') >= 0) {
            //             imagen = $(imagen).text();
            //         }
            //     }
            // }
            //
            // if(imagen1 === undefined){
            //     imagen1 = '';
            // }
            // else{
            //     if(typeof imagen1 === 'string') {
            //         if (imagen1.indexOf('encours') >= 0) {
            //             imagen1 = $(imagen1).text();
            //         }
            //     }
            // }
            //
            //
            //
            //     var dataTmp = {
            //         'dossier': dossierTxt,
            //         'cloture': (value['tableau-cloture'] === undefined) ? '' : value['tableau-cloture'],
            //         'imagen': imagen,
            //         'imagen1': imagen1,
            //         'm1': (value['tableau-m1'] === undefined) ? '' : value['tableau-m1'],
            //         'm2': (value['tableau-m2'] === undefined) ? '' : value['tableau-m2'],
            //         'm3': (value['tableau-m3'] === undefined) ? '' : value['tableau-m3'],
            //         'm4': (value['tableau-m4'] === undefined) ? '' : value['tableau-m4'],
            //         'm5': (value['tableau-m5'] === undefined) ? '' : value['tableau-m5'],
            //         'm6': (value['tableau-m6'] === undefined) ? '' : value['tableau-m6'],
            //         'm7': (value['tableau-m7'] === undefined) ? '' : value['tableau-m7'],
            //         'm8': (value['tableau-m9'] === undefined) ? '' : value['tableau-m8'],
            //         'm9': (value['tableau-m9'] === undefined) ? '' : value['tableau-m9'],
            //         'm10': (value['tableau-m10'] === undefined) ? '' : value['tableau-m10'],
            //         'm11': (value['tableau-m11'] === undefined) ? '' : value['tableau-m11'],
            //         'm12': (value['tableau-m12'] === undefined) ? '' : value['tableau-m12'],
            //         'm13': (value['tableau-m13'] === undefined) ? '' : value['tableau-m13'],
            //         'm14': (value['tableau-m14'] === undefined) ? '' : value['tableau-m14'],
            //         'm15': (value['tableau-m15'] === undefined) ? '' : value['tableau-m15'],
            //         'm16': (value['tableau-m16'] === undefined) ? '' : value['tableau-m16'],
            //         'm17': (value['tableau-m17'] === undefined) ? '' : value['tableau-m17'],
            //         'm18': (value['tableau-m18'] === undefined) ? '' : value['tableau-m18'],
            //         'm19': (value['tableau-m19'] === undefined) ? '' : value['tableau-m19'],
            //         'm20': (value['tableau-m20'] === undefined) ? '' : value['tableau-m20'],
            //         'm21': (value['tableau-m21'] === undefined) ? '' : value['tableau-m21'],
            //         'm22': (value['tableau-m22'] === undefined) ? '' : value['tableau-m22'],
            //         'm23': (value['tableau-m23'] === undefined) ? '' : value['tableau-m23'],
            //         'm24': (value['tableau-m24'] === undefined) ? '' : value['tableau-m24']
            //     };


            var val = value['cell'],
                dossier = val[1],
                dossierTxt = (dossier === undefined) ? '' : dossier,
                imagen = val[7],
                imagen1 = val[8];

            if(typeof dossier === 'string'){
                if(dossier.indexOf('span') >= 0){
                    dossierTxt = $(dossier).text();
                }
            }

            if(imagen === undefined){
                imagen = '';
            }
            else{
                if(typeof imagen === 'string') {
                    if (imagen.indexOf('encours') >= 0) {
                        imagen = $(imagen).text();
                    }
                }
            }

            if(imagen1 === undefined){
                imagen1 = '';
            }
            else{
                if(typeof imagen1 === 'string') {
                    if (imagen1.indexOf('encours') >= 0) {
                        imagen1 = $(imagen1).text();
                    }
                }
            }



            var dataTmp = {
                'dossier': dossierTxt,
                'cloture': (val[6] === undefined) ? '' : val[6],
                'imagen': imagen,
                'imagen1': imagen1,
                'm1': (val[10] === undefined) ? '' : val[10],
                'm2': (val[11] === undefined) ? '' : val[11],
                'm3': (val[12] === undefined) ? '' : val[12],
                'm4': (val[13] === undefined) ? '' : val[13],
                'm5': (val[14] === undefined) ? '' : val[14],
                'm6': (val[15] === undefined) ? '' : val[15],
                'm7': (val[16] === undefined) ? '' : val[16],
                'm8': (val[17] === undefined) ? '' : val[17],
                'm9': (val[18] === undefined) ? '' : val[18],
                'm10': (val[19] === undefined) ? '' : val[19],
                'm11': (val[20] === undefined) ? '' : val[20],
                'm12': (val[21] === undefined) ? '' : val[21],
                'm13': (val[22] === undefined) ? '' : val[22],
                'm14': (val[23] === undefined) ? '' : val[23],
                'm15': (val[24] === undefined) ? '' : val[24],
                'm16': (val[25] === undefined) ? '' : val[25],
                'm17': (val[26] === undefined) ? '' : val[26],
                'm18': (val[27] === undefined) ? '' : val[27],
                'm19': (val[28] === undefined) ? '' : val[28],
                'm20': (val[29] === undefined) ? '' : val[29],
                'm21': (val[30] === undefined) ? '' : val[30],
                'm22': (val[31] === undefined) ? '' : val[31],
                'm23': (val[32] === undefined) ? '' : val[32],
                'm24': (val[33] === undefined) ? '' : val[33]
            };

            datas.push(dataTmp);
        });

        $('#exp_client').val($('#tableau-client').val());
        $('#exp_exercice').val($('#tableau-exercice').val());
        $('#exp_datas').val(encodeURI(JSON.stringify(datas)));
        $('#form-export').attr('action',Routing.generate('tableau_export')).submit();


    });


    $(document).on('click', '#eto', function(){

    })
});

