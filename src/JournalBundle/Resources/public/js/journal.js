function remarqueCellAttr(rowId, val, rawObject, cm, rdata) {

    if(val === 'Compta # Robot' ){
        return ' style="background:#f8ac59;color:transparent;"';
    }else{
        return '';
    }
}


$(document).ready(function() {
    $('#exercice').val((new Date()).getFullYear());
    dossier_depend_exercice = true;
    charger_site();

    var journalGrid = $('#journal-list'),
        dataExport = [],
        centralisateurGrid = $('#centralisateur-list'),
        wc = centralisateurGrid.parent().width(),
        w = centralisateurGrid.parent().width()
    ;

    journalGrid.jqGrid({
        datatype: 'json',
        loadonce: true,
        sortable: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: false,
        footerrow: true,
        userDataOnFooter: true,
        mtype: 'POST',
        caption: '',
        colNames: [
            'Date', 'Image', 'Image ID', 'Journal', 'Compte', 'Libelle', 'Débit', 'Crédit', 'Devise',  'Lettrage',
            'Remarque', 'Remarque type', 'Image ID NC', 'Compte ID', 'Type compte', 'Journal Dossier ID'
        ],
        colModel: [
            {
                name: 'j_date',
                index: 'j_date',
                align: 'center',
                formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                fixed: true,
                width: 10 * w / 100,
                editable: false,
                sortable: true
            },
            {
                name: 'j_image',
                index: 'j_image',
                align: 'center',
                fixed: true,
                width: 8 * w / 100,
                editable: false,
                sortable: true,
                classes: 'j_image'
            },
            {
                name: 'j_image_id',
                index: 'j_image_id',
                align: 'center',
                classes: 'j_image_id',
                hidden: true
            },
            {
                name: 'j_journal',
                index: 'j_journal',
                align: 'center',
                fixed: true,
                width: 4 * w / 100,
                editable: false,
                sortable: true
            },
            {
                name: 'j_compte',
                index: 'j_compte',
                align: 'center',
                fixed: true,
                width: 10 * w / 100,
                editable: false,
                sortable: true
            },
            {
                name: 'j_libelle',
                index: 'j_libelle',
                align: 'left',
                editable: false,
                width: 29 * w / 100,
                sortable: true,
                classes: 'j_libelle'
            },
            {
                name: 'j_debit',
                index: 'j_debit',
                align: 'right',
                // formatter: "number",
                sorttype: "number",
                fixed: true,
                width: 10 * w / 100,
                editable: false,
                sortable: true,
                // classes: 'text-primary'
                formatter: function(v) { return '<b class="text-primary">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'j_credit',
                index: 'j_credit',
                align: 'right',
                // formatter: "number",
                sorttype: "number",
                fixed: true,
                width: 10 * w / 100,
                editable: false,
                sortable: true,
                // classes: 'text-danger',
                formatter: function(v) { return '<b class="text-danger">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'j_devise',
                index: 'j_devise',
                align: 'center',
                editable: false,
                width: 5 * w / 100,
                sortable: true
            },
            {
                name: 'j_lettre',
                index: 'j_lettre',
                fixed: true,
                width: 4 * w / 100,
                editable: false,
                sortable: true,
                align: 'center',
                hidden: true
            },
            {
                name: 'j_remarque',
                index: 'j_remarque',
                align: 'center',
                fixed: true,
                width: 14 * w / 100,
                editable: false,
                sortable: true,
                classes: 'j_remarque',
                cellattr: remarqueCellAttr
            },
            {
                name: 'j_remarque_type',
                index: 'j_remarque_type',
                hidden: true,
                editable: false,
                sortable: false,
                align: 'center',
                classes: 'j_remarque_type'
            },
            {
                name: 'j_image_id_nc',
                index: 'j_image_id_nc',
                hidden: true,
                editable: false,
                sortable: false,
                align: 'center',
                classes: 'j_image_id_nc'
            },
            {
                name: 'j_compte_id',
                index: 'j_compte_id',
                hidden: true,
                editable: false,
                sortable: false,
                align: 'center',
                classes: 'j_compte_id'
            },
            {
                name: 'j_type_compte',
                index: 'j_type_compte',
                hidden: true,
                editable: false,
                sortable: false,
                align: 'center',
                classes: 'j_type_compte'
            },
            {
                name: 'j_journal_dossier_id',
                index: 'j_journal_dossier_id',
                hidden: true,
                editable: false,
                sortable: false,
                align: 'center'
            },
        ],
        loadComplete: function (data) {
            $('#journal-list').jqGrid('setGridHeight', $(window).height() - 300);
            dataExport = data.rows;
        }

    });


    centralisateurGrid.jqGrid({
        datatype: 'json',
        loadonce: true,
        sortable: true,
        shrinkToFit: true,
        viewrecords: true,
        hidegrid: false,
        footerrow: true,
        userDataOnFooter: true,
        mtype: 'POST',
        caption: '',
        colNames: [
            'Date', 'Journal', 'Libellé Journal', 'Débit Compta', 'Crédit Compta', 'Débit Picdoc', 'Crédit Picdoc', 'Remarque'
        ],
        colModel: [
            {
                name: 'c_date',
                index: 'c_date',
                align: 'center',
                formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                fixed: true,
                width: 10 * wc / 100,
                editable: false,
                sortable: true
            },

            {
                name: 'c_journal',
                index: 'c_journal',
                align: 'center',
                fixed: true,
                width: 5 * wc / 100,
                editable: false,
                sortable: true
            },
            {
                name: 'c_libelle',
                index: 'c_libelle',
                align: 'left',
                editable: false,
                width: 15 * wc / 100,
                sortable: true
            },
            {
                name: 'c_debit_compta',
                index: 'c_debit_compta',
                align: 'right',
                sorttype: "number",
                fixed: true,
                width: 15 * wc / 100,
                editable: false,
                sortable: true,
                formatter: function(v) { return '<b class="text-primary">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'c_credit_compta',
                index: 'c_credit_compta',
                align: 'right',
                sorttype: "number",
                fixed: true,
                width: 15 * wc / 100,
                editable: false,
                sortable: true,
                formatter: function(v) { return '<b class="text-danger">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'c_debit_picdoc',
                index: 'c_debit_picdoc',
                align: 'right',
                sorttype: "number",
                fixed: true,
                width: 15 * wc / 100,
                editable: false,
                sortable: true,
                formatter: function(v) { return '<b class="text-primary">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'c_credit_picdoc',
                index: 'c_credit_picdoc',
                align: 'right',
                sorttype: "number",
                fixed: true,
                width: 15 * wc / 100,
                editable: false,
                sortable: true,
                formatter: function(v) { return '<b class="text-danger">'+ number_format(v, 2, ',', ' ') +'</b>'; }
            },
            {
                name: 'c_remarque',
                index: 'c_remarque',
                align: 'center',
                editable: false,
                width: 10 * wc / 100,
                sortable: true,
                cellattr: remarqueCellAttr
            },

        ],
        loadComplete: function () {
            $('#centralisateur-list').jqGrid('setGridHeight', $(window).height() - 300);
        }

    });

    $(document).on('change', '#dossier', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var exercice = $('#exercice').val();

        charger_periode_pop_over();
        chargerJournalDossier($(this).val(), exercice);
        historiqueUpload($(this).val(), exercice);
    });

    $(document).on('click', '#btn-go', function (e) {
        e.preventDefault();
        e.stopPropagation();
        go(true, true);
    });

    $(document).on('click', '#btn-go-centralisateur', function (e) {
        e.preventDefault();
        e.stopPropagation();
        go(false, true);
    });

    $(document).on('mouseover', '#journal-list tr[role="row"]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var image = $(this).find('.j_image_id_nc').html(),
            trs = $(this).closest('tbody').find('tr[role="row"]')
        ;

        trs.each(function () {
            var tmp = $(this).find('.j_image_id_nc').html();

            if (tmp === image) {
                $(this).css("background-color", "#e2efda");
            }
        });
    });

    $(document).on('mouseout', '#journal-list tr[role="row"]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var image = $(this).find('.j_image_id_nc').html(),
            trs = $(this).closest('tbody').find('tr[role="row"]')
        ;

        trs.each(function () {
            var tmp = $(this).find('.j_image_id_nc').html();

            if (tmp === image) {
                $(this).css("background-color", "");
            }
        });
    });

    $(document).on('click', '.j_image', function(e){
        e.preventDefault();
        e.stopPropagation();

        $.ajax({
           url: Routing.generate('journal_image'),
            type: 'GET',
            data: {image: $(this).closest('tr').find('.j_image').text()},
            success: function(data){
                show_image_pop_up(data);
            }
        });


    });

    $(document).on('click', '.cl_export', function(e){
       e.preventDefault();
       e.stopPropagation();

        if (dataExport.length === 0)
        {
            show_info('Vide','Pas de données à exporter','error');
            return;
        }

        var type = $(this).attr('data-type'),
            params = ''
                + '<input type="hidden" name="exp_dossier" value="'+$('#dossier').val()+'">'
                + '<input type="hidden" name="extension" value="'+type+'">'
                + '<input type="hidden" name="exp_exercice" value="'+$('#exercice').val()+'">'
                + '<input type="hidden" name="exp_journal_dossier" value="'+$('#journal-dossier').val()+'">'
                + '<input type="hidden" name="datas" value="'+encodeURI(JSON.stringify(dataExport))+'">';

        $('#export').attr('action',Routing.generate('journal_export')).html(params).submit();

    });

    $(document).on('click', '.j_remarque', function(e){
       e.preventDefault();
       e.stopPropagation();

       var tr = $(this).closest('tr'),
           remarqueType = tr.find('.j_remarque_type').text(),
           imageid = tr.find('.j_image_id_nc').text(),
           datas = [];

        $(this).closest('tbody').find('tr[role="row"]').each(function () {

            if ($(this).find('.j_image_id_nc').text() === imageid) {
                datas.push($('#journal-list').jqGrid ('getRowData', $(this).attr('id')));
            }
        });


       if(parseInt(remarqueType) === 0){
           swal({
               title: '',
               text: "Voulez vous ajouter cette les lignes de cette image dans les ecritures?",
               type: 'question',
               showCancelButton: true,
               reverseButtons: true,
               confirmButtonColor: '#3085d6',
               cancelButtonColor: '#d33',
               confirmButtonText: 'Oui',
               cancelButtonText: 'Non'
           }).then(function () {
               $.ajax({
                  url: Routing.generate('journal_ecriture'),
                  type: 'POST',
                  data: {
                      datas: JSON.stringify(datas)
                  },
                  success: function(data){
                      show_info('', data.message, data.type);
                      if(data.type === 'success'){
                          go(true, false);
                      }
                  }
               });
               },
               function (dismiss) {
                   if (dismiss === 'cancel') {

                   } else {
                       throw dismiss;
                   }
               }
           );
       }
       else if(parseInt(remarqueType) === 1) {


           var journalPicDocGrid = $('#journal-picdoc-list'),

               modal = $('#picdoc-modal'),
               periodes = [],
               moiss = [],
               dossier = $('#dossier').val(),
               exercice = $('#exercice').val(),
               fromPicDoc = $('#from-picdoc').is(':checked'),
               fromCompta = $('#from-compta').is(':checked');

           modal.modal('show');
           modal.draggable();

           var wp = journalPicDocGrid.parent().width();

           $('#journal-picdoc-list').jqGrid('GridUnload');

           $('#journal-picdoc-list').jqGrid({
               datatype: 'json',
               loadonce: true,
               sortable: true,
               shrinkToFit: true,
               viewrecords: true,
               hidegrid: false,
               footerrow: true,
               userDataOnFooter: true,
               mtype: 'POST',
               url: Routing.generate('journal_details'),
               postData: {
                   dossier: dossier,
                   exercice: exercice,
                   journalDossier: $('#journal-dossier').val(),
                   periode: JSON.stringify({p: periodes, m: moiss}),
                   frompicdoc: fromPicDoc,
                   fromcompta: fromCompta,
                   image: imageid
               },
               caption: '',
               colNames: [
                   'Date', 'Image','Journal', 'Compte', 'Libelle', 'Débit', 'Crédit'
               ],
               colModel: [
                   {
                       name: 'j_date',
                       index: 'j_date',
                       align: 'center',
                       formatter: 'date', formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'},
                       fixed: true,
                       width: 10 * wp / 100,
                       editable: false,
                       sortable: true
                   },
                   {
                       name: 'j_image',
                       index: 'j_image',
                       align: 'center',
                       fixed: true,
                       width: 10 * w / 100,
                       editable: false,
                       sortable: true,
                       classes: 'j_image'
                   },

                   {
                       name: 'j_journal',
                       index: 'j_journal',
                       align: 'center',
                       fixed: true,
                       width: 10 * wp / 100,
                       editable: false,
                       sortable: true
                   },
                   {
                       name: 'j_compte',
                       index: 'j_compte',
                       align: 'center',
                       fixed: true,
                       width: 15 * wp / 100,
                       editable: false,
                       sortable: true
                   },
                   {
                       name: 'j_libelle',
                       index: 'j_libelle',
                       align: 'left',
                       editable: false,
                       width: 30 * wp / 100,
                       sortable: true,
                       classes: 'j_libelle'
                   },
                   {
                       name: 'j_debit',
                       index: 'j_debit',
                       align: 'right',
                       // formatter: "number",
                       sorttype: "number",
                       fixed: true,
                       width: 10 * wp / 100,
                       editable: false,
                       sortable: true,
                       // classes: 'text-primary'
                       formatter: function (v) {
                           return '<b class="text-primary">' + number_format(v, 2, ',', ' ') + '</b>';
                       }
                   },
                   {
                       name: 'j_credit',
                       index: 'j_credit',
                       align: 'right',
                       // formatter: "number",
                       sorttype: "number",
                       fixed: true,
                       width: 10 * wp / 100,
                       editable: false,
                       sortable: true,
                       // classes: 'text-danger',
                       formatter: function (v) {
                           return '<b class="text-danger">' + number_format(v, 2, ',', ' ') + '</b>';
                       }
                   }
               ],
               loadComplete: function (data) {
                   $('#journal-picdoc-list').jqGrid('setGridHeight', 200);
               }

           }).trigger('reloadGrid', {fromServer: true, page: 1});

       }

    });
});

function chargerJournalDossier(dossier, exercice){
    $.ajax({
        url: Routing.generate('journal_dossier'),
        data: {dossierid: dossier, exercice: exercice},
        type: 'GET',
        success: function(data){
            $('#journal-dossier').html(data);
        }
    })
}

function go(journal, centralisateur){
    var periodes = [],
        moiss = [],
        div_hidden = $('.js_date_picker_hidden'),
        dossier = $('#dossier').val(),
        exercice = $('#exercice').val(),
        fromPicDoc = $('#from-picdoc').is(':checked'),
        fromCompta = $('#from-compta').is(':checked')
    ;

    div_hidden.find('.js_dpk_periode').each(function(){
        if($(this).hasClass('js_dpk_mois'))
        {
            var m = $(this).attr('data-value').trim();
            moiss.push(((m.length === 1) ? '0' : '') + m);
        }
        if($(this).hasClass(dpkGetActiveDatePicker()))
        {
            var array_mois = [],
                value = parseInt($(this).attr('data-val')),
                niveau = parseInt($(this).attr('data-niveau'));

            //mois
            if(niveau === 3)
            {
                var mois_val = $(this).attr('data-value').trim();
                periodes.push({'libelle':$(this).text().trim(), 'moiss':[((mois_val.length === 1) ? '0' : '') + mois_val]});
            }
            //trimestre; semestre; annee
            else if(niveau === 2 || niveau === 1 || niveau === 0)
            {
                //each moiss
                div_hidden.find('.js_dpk_mois').each(function(){
                    var mere = -2;
                    if(niveau === 2) mere = parseInt($(this).attr('data-mere-trimestre'));
                    else if(niveau === 1) mere = parseInt($(this).attr('data-mere-semestre'));
                    else if(niveau === 0) mere = parseInt($(this).attr('data-mere-annee'));
                    if(!$(this).hasClass(dpkGetActiveDatePicker()) && mere === value)
                    {
                        var mois_val = $(this).attr('data-value').trim();
                        array_mois.push(((mois_val.length === 1) ? '0' : '') + mois_val);
                    }
                });
                periodes.push({'libelle':$(this).text().trim(), 'moiss':array_mois});
            }
        }
    });//moiss; periodes{libelle, moiss}


    if(journal === true) {
        $('#journal-list').jqGrid('setGridParam', {
            url: Routing.generate('journal_details'),
            postData: {
                dossier: dossier,
                exercice: exercice,
                journalDossier: $('#journal-dossier').val(),
                periode: JSON.stringify({p: periodes, m: moiss}),
                frompicdoc: fromPicDoc,
                fromcompta: fromCompta
            },
            datatype: 'json'
        })
            .trigger('reloadGrid', {fromServer: true, page: 1});
    }
    if(centralisateur === true){
        $('#centralisateur-list').jqGrid('setGridParam', {
            url: Routing.generate('journal_centralisateur'),
            postData: {
                dossier: dossier,
                exercice: exercice,
                periode: JSON.stringify({p: periodes, m: moiss}),
            },
            datatype: 'json'
        })
            .trigger('reloadGrid', {fromServer: true, page: 1});
    }
}

function historiqueUpload(dossier, exercice){
    $.ajax({
        url: Routing.generate('journal_historique'),
        type: 'GET',
        data: { dossier: dossier, exercice: exercice},
        success: function(res) {
            var span =
        '<span class="simple_tag white-bg" id="id_import_historique"><i class="fa fa-info-circle"></i>&nbsp;Imports</span>';


            $('#btn-historique').html(span);

            var
                exercice = parseInt($('#exercice').val()),
                import_html = '<table class="table table-bordered">';

            import_html += '' +
                '<tr>' +
                '<th>Ex.</th>' +
                '<th>Clôture</th>' +
                '<th>Import</th>' +
                '<th>Statut</th>' +
                '</tr>';
            var statusN = 'Pas d import';
            if (res.importN_1 !== null) {
                if (parseInt(res.importN_1.s) === 1) statusN = 'Cloturé';
                else if (res.importN_1.dv !== null) statusN = 'Projet ' + res.importN_1.dv;
            }
            import_html += '' +
                '<tr>' +
                '<td>' + (exercice - 1) + '</td>' +
                '<td>' + res.dcN_1 + '</td>' +
                '<td>' + ((res.importN_1 !== null && res.importN_1.du !== null) ? res.importN_1.du : '') + '</td>' +
                '<td>' + statusN + '</td>' +
                '</tr>';
            statusN = 'Pas d import';
            if (res.importN !== null) {
                if (parseInt(res.importN.s) === 1) statusN = 'Cloturé';
                else if (res.importN.dv !== null) statusN = 'Projet ' + res.importN.dv;
            }
            import_html += '' +
                '<tr>' +
                '<td>' + exercice + '</td>' +
                '<td>' + res.dc + '</td>' +
                '<td>' + ((res.importN !== null && res.importN.du !== null) ? res.importN.du : '') + '</td>' +
                '<td>' + statusN + '</td>' +
                '</tr>';
            import_html += '</table>';

            var position = { my: 'top right', at: 'bottom left' };
            $('#id_import_historique').qtip({
                content: {
                    text: function (event, api) {
                        return import_html;
                    }
                },
                position: position,
                style: {
                    classes: 'qtip-dark qtip-shadow'
                }
            });
        }
    })
}
