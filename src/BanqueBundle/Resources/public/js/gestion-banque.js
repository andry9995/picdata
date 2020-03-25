$(document).ready(function () {

    var lastsel_banque;
    var url = Routing.generate('banque_gestion_banque_grid');

    banqueGrid.jqGrid({

        datatype: 'json',
        url: url,
        mtype: 'POST',
        loadonce: false,
        sortable: false,
        autowidth: true,
        // height: gridHeight,
        // width: gridWidth,
        shrinkToFit: true,
        viewrecords: true,
        pager: '#js_banque_pager',
        hidegrid: false,
        caption: " ",
        colNames: ['Banque','Code banque','Compte', 'Solde', 'Dernier MAJ', 'Compte comptable', 'Journal banque', '<i class="fa fa-edit">Action</i>'],
        colModel: [
            {
                name: 'banque-nom',
                index: 'banque-nom',
                editable: true,
                width: 300,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('info_perdos_banque', {json: 0}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    },
                    dataEvents:[{
                        type: 'change',
                        fn: function(e){
                            var selected = parseInt($(e.target).val());

                            if(selected != -1){
                                $.ajax({
                                    data: {
                                        banqueId: $(e.target).val()
                                    },

                                    url:Routing.generate('info_perdos_banque_code'),
                                    type: 'POST',
                                    async: true,
                                    dataType: 'html',
                                    success: function (data) {

                                        console.log(data);

                                        if(data != '-1') {
                                            $('#' + lastsel_banque + '_banque-code').val(data);
                                        }
                                        else{
                                            $('#' + lastsel_banque + '_banque-code').val('');
                                        }
                                    }

                                });
                            }
                        }
                    }]
                },

                classes: 'banque-nom'

            },
            {
                name: 'banque-code',
                index: 'banque-code',
                editable: true,
                edittype: 'text',
                editoptions: {
                    dataInit: function (e) {
                        e.style.textAlign = 'right';
                    },
                    disabled: true
                },
                classes: 'banque-code',
                align: "right"
            },
            {
                name: 'banque-compte',
                index: 'banque-compte',
                editable: true,
                width: 200,
                edittype: 'text',
                editoptions: {
                    dataInit: function (e) {
                        e.style.textAlign = 'right';
                    }
                },
                classes: 'banque-code',
                align: "right"
            },
            {
                name: 'banque-solde',
                index: 'banque-solde',
                editable: true,
                editoptions: {defaultValue: ''},
                width: 100,
                fixed: true,
                align: "right",
                formatter: "number",
                sorttype: "number",
                classes: 'banque-solde'
            },
            {
                name: 'banque-maj',
                index: 'banque-maj',
                editable: false,
                editoptions: {defaultValue: ''},
                width: 100,
                fixed: true,
                classes: 'banque-maj',
                formatter: 'date',
                formatoptions: {srcformat: 'ISO8601Short', newformat: 'd/m/Y'}
            },
            {
                name: 'banque-compte-comptable',
                index: 'banque-compte-comptable',
                editable: true,
                edittype: 'select',
                editoptions: {
                    // dataUrl: Routing.generate('banque_compte_comptable', {json: dossierId}),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },
                classes: 'banque-compte-comptable'
            },
            {
                name: 'banque-journal',
                index: 'banque-journal',
                editable: true,
                width: 100,
                edittype: 'select',
                editoptions: {
                    dataUrl: Routing.generate('banque_journal'),
                    dataInit: function (elem) {
                        $(elem).width(100);
                    }
                },

                classes: 'banque-journal'

            },
            {
                name: 'action',
                index: 'action',
                width: 60,
                align: "center", sortable: false,
                editoptions:
                    {defaultValue: '<i class="fa fa-save icon-action js-save-banqueCompte" title="Enregistrer"></i><i class="fa fa-trash icon-action js-remove-banqueCompte" title="Supprimer"></i>'},
                classes: 'js-banque-action'
            }

        ],

        onSelectRow: function (id) {
            if (id && id !== lastsel_banque) {
                banqueGrid.restoreRow(lastsel_banque);
                lastsel_banque = id;
            }
            banqueGrid.editRow(id, false);

            console.log(lastsel_banque);
        },

        beforeSelectRow: function (rowid, e) {
            var target = $(e.target);

            var item_action = (target.closest('td').children('.icon-action').length > 0);

            return !item_action;


        },


        loadComplete: function (data) {

            var dossierId = $('#dossier').val();

            var dossierSelectdText = $('#dossier option:selected').text().trim().toUpperCase();

            var $self = $(this),
                cm = $self.jqGrid("getColProp", "banque-compte-comptable");

            cm.editoptions = {
                dataUrl: Routing.generate('banque_compte_comptable', {json: dossierId}),
                dataInit: function (elem) {
                    $(elem).width(100);
                }
            };


            if(dossierSelectdText != "TOUS" && dossierSelectdText!= "") {
                if ($("#btn-add-banqueCompte").length == 0) {
                    $('#js_banque_liste').closest('.ui-jqgrid').find('.ui-jqgrid-title').after('<div class="pull-right" style="line-height: 40px; margin-right: 20px;">' +
                        '<button id="btn-add-banqueCompte" class="btn btn-outline btn-primary btn-xs" style="margin-right: 20px;">Ajouter</button></div>');
                }
            }
            else{
                $('#btn-add-banqueCompte').remove();
            }

            setTableauWidth();


        },

        footerrow: true,
        userDataOnFooter: true,
        ajaxRowOptions: {async: true},
        reloadGridOptions: {fromServer: true}

    });

    $(document).on('click', '#btn-add-banqueCompte', function (event) {

        if(canAddRow(banqueGrid)) {
            event.preventDefault();
            banqueGrid.jqGrid('addRow', {
                rowID: "new_row",
                initData: {},
                position: "first",
                useDefValues: true,
                useFormatter: true,
                addRowParams: {}
            });
            $("#" + "new_row", "#js_banque_liste").effect("highlight", 20000);
        }
    });

    $(document).on('click', '.js-save-banqueCompte', function () {

        event.preventDefault();
        event.stopPropagation();
        banqueGrid.jqGrid('saveRow', lastsel_banque, {
            "aftersavefunc": function() {

                var idDossier = $('#dossier').val();

                banqueGrid.jqGrid('clearGridData');
                banqueGrid.jqGrid('setGridParam', {
                    postData: {
                        dossierId: idDossier
                    },
                    editurl: Routing.generate('banque_compte_edit', {dossierId: idDossier}),
                    footerrow: true
                }).trigger('reloadGrid');
            }
        });

    });

    $(document).on('click', '.js-remove-banqueCompte', function (event) {

        event.stopPropagation();
        event.preventDefault();
        var rowid = $(this).closest('tr').attr('id');

        if(rowid =='new_row') {
            $(this).closest('tr').remove();
            return;
        }

        $('#js_banque_liste').jqGrid('delGridRow', rowid, {
            url: Routing.generate('banque_compte_remove'),
            top: 200,
            left: 400,
            width: 400,
            mtype: 'DELETE',
            closeOnEscape: true,
            modal: true,
            msg: 'Supprimer cet enregistrement ?'
        });
    });

});

function goGestionBanque(){
    var idDossier = $('#dossier').val();
    banqueGrid.jqGrid('clearGridData');
    banqueGrid.jqGrid('setGridParam', {
        postData: {
            dossierId: idDossier
        },
        editurl: Routing.generate('banque_compte_edit', {dossierId: idDossier}),
        footerrow: true
    }).trigger('reloadGrid');
}

