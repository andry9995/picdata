function charger_analyse()
{
    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val(),
            action: $('#js_id_action').val()
        },
        url: Routing.generate('banque_analyse'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);

            //$('#test').html(data);return;
            var dataObject = $.parseJSON(data),
                //max_libelle = dataObject.ml,
                datas = dataObject.d,
                // table_selected = $('#js_tb_analyse'),
                table_selected = $('#js_id_releve_liste'),
                w = table_selected.parent().width(),
                h = $(window).height() - 250,
                editurl = editurl = Routing.generate('banque_releve_edit');
            $($('#js_id_flottante_hidden').html()).insertBefore(table_selected);
            set_table_releve_jqgrid(datas,h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,false,undefined,undefined,undefined,undefined,undefined,false,undefined,false);
        }
    });
}

function get_col_model(w)
{
    var colModel1 = [],
        did = $('#dossier').val(),
        isPmAction = (parseInt($('#js_id_action').val()) == 1),
        lWidth = (isPmAction) ? 45 : 17,
        lStatus = (isPmAction) ? 20 : 10;

    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'id', index:'id', hidden:true, classes:'js_id_releve' });
        colModel1.push({ name:'i', index:'i', sortable:true, width:  w*7/100, align:'center', classes:'js_show_image_ pointer text-primary' });
        colModel1.push({ name:'imi', index:'imi', hidden:true, classes:'js_id_image' });
        colModel1.push({ name:'d', index:'d', sortable:false, width:  w*7/100, align:'center' });
        colModel1.push({ name:'l', index:'l', sortable:false, width:  w*lWidth/100 });
        colModel1.push({ name:'m', index:'m', sortable:false, width:  w*7/100, align:'right', formatter: function(v) { return '<span class="'+ ((v < 0) ? 'text-danger' : '') +'">'+ number_format(v, 2, ',', ' ') +'</span>'; } });
        colModel1.push({ name:'s', index:'s', sortable:true, width:  w*lStatus/100, classes: 'is_show_image_temp_ ', formatter: function (v) { return status_formatter(v) ; } });
        colModel1.push({ name:'ss', index:'ss', hidden:true });
        colModel1.push({ name:'iti', index:'iti', hidden:true });
        colModel1.push({name:'t', index:'t', sortable:false, width:  w*9/100, align:'center',
            hidden: isPmAction,
            editable: false,
            edittype: 'select',
            editoptions: {
                dataUrl: Routing.generate('banque_grid_combo', {json: 0, did: did})
            }});
        colModel1.push({name:'tc', index: 'tc', sortable:false, width: w*9/100,align: 'center'});
        colModel1.push({name:'c', index:'c', sortable:false, width:  w*9/100, align:'center',
            hidden: isPmAction,
            editable: false,
            edittype: 'select',
            editoptions: {
                dataUrl: Routing.generate('banque_grid_combo', {json: 1, did: did})
            }});
        colModel1.push({ name:'tv', index:'tv', sortable:false, width:  w*9/100, align:'center',
            hidden: isPmAction,
            editable: false,
            edittype: 'select',
            editoptions: {
                dataUrl: Routing.generate('banque_grid_combo',{json: 1, did: did} )
            }});
        colModel1.push({ name:'n', index:'n', sortable:false, width:  w*7/100, align:'center',
            hidden: isPmAction,
            editable: false,
            edittype: 'select',
            editoptions: {
                dataUrl: Routing.generate('banque_grid_combo', {json: 2, did: did})
            }});
        colModel1.push({ name:'sn', index:'sn', sortable:false, align:'center',
            editable: false,
            hidden: true,
            edittype: 'select',
            editoptions: {
                dataUrl: Routing.generate('banque_grid_combo', {json: 3, did: did})
            }});

        colModel1.push({ name:'is', index:'is', sortable:false, hidden: isPmAction, width:  w*7/100, align:'center', formatter: function (v) {  return (v.trim() != '') ? '<span class="pointer text-primary js_show_image_soeur">'+ v +'</span>' : '' } });
        colModel1.push({ name:'isi', index:'isi', hidden:true, classes:'js_id_image_soeur' });
        colModel1.push({ name:'e', index:'e', sortable:false, hidden: isPmAction, width:  w*7/100, align:'center', formatter: function (v) { return status_details(v) ;} });
        colModel1.push({ name:'ss', index:'ss', sortable:false, hidden:true, align:'center'});
        colModel1.push({ name:'a', index:'a', sortable:false, hidden: isPmAction, width:  w*2/100, align:'center'});
    }
    else
    {
        colModel1 = [
            'id releve',
            'Image',
            'id image',
            'Date Op\xB0',
            'Libelle',
            'Mouvements',
            'Rapprochement',
            'id statut',
            'id image temp',
            'Bilan',
            'Compte Bilan',
            'Resultat',
            'Compte Tva',
            'Nature',
            'Sous nature',
            'Num piece',
            'id piece',
            'Ecritures',
            'ss',
            '<span class="fa fa-bookmark-o" style="display:inline-block"/>'
        ];
    }
    return colModel1;
}

function status_formatter(v)
{
    if($.isNumeric(v)) {
        var status = [
            '<span class="js_parcourir_pm pointer">Libelle&nbsp;non&nbsp;identifi&eacute;</span>',
            '<span class="text-success js_parcourir_pm pointer">Pi&egrave;ce&nbsp;manquante</span>',
            'Inconnu', '<span class="text-primary pointer">Pi&egrave;ce&nbsp;affect&eacute;e</span>',
            '<span class="label label-danger pointer js_show_image_a_affecter pointer">Pi&egrave;ce&nbsp;&agrave;&nbsp;affecter</span>'
        ];

        return status[v];
    }
    else{
        return v;
    }
}

function status_details(v)
{
    return (v == 3) ? '<i class="js_show_details pointer fa fa-eyedropper" aria-hidden="true"></i>' : '';
}

function show_details_releve(td)
{
    var releve = td.closest('tr').find('.js_id_releve').text().trim();
    $.ajax({
        data: {
            releve: releve
        },
        url: Routing.generate('banque_details_releve'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
        }
    });
}

function set_table_releve_jqgrid(mydata, height, colNames, colModel, table, caption, width, editurl, rownumbers, rowNum, grouping, groupingView, firstSort, firtsColSorter, shrinkToFit, userdata, autoContent,sortable) {
    var id_table = table.attr('id');
    $('#' + id_table).after('<table id="' + id_table + '_temp"></table>')
        .jqGrid("clearGridData")
        .jqGrid('GridDestroy')
        .remove();
    $('#' + id_table + '_pager').remove();
    $('#' + id_table + '_temp').attr('id', id_table);
    $('#' + id_table).after('<table id="' + id_table + '_temp"></table>')
        .after('<div id="' + id_table + '_pager"></div>');

    var isClicked = false;

    var id_pager = '';
    if (typeof rowNum !== 'undefined') id_pager = "#" + id_table + '_pager';
    else rowNum = 1000000;

    grouping = (typeof grouping === 'undefined') ? false : grouping;
    groupingView = (typeof groupingView === 'undefined') ? null : groupingView;
    rownumbers = (typeof rownumbers === 'undefined') ? true : rownumbers;
    shrinkToFit = (typeof shrinkToFit === 'undefined') ? true : shrinkToFit;
    autoContent = (typeof autoContent === 'undefined') ? false : autoContent;
    sortable = (typeof sortable === 'undefined') ? false : sortable;

    var footerRow = (typeof userdata !== 'undefined'),
        userDataOnFooter = (typeof userdata !== 'undefined');
    userdata = (typeof userdata !== 'undefined') ? userdata : [];


    if (typeof editurl === 'undefined') editurl = '';

    var current_jqgrid = $('#' + id_table).jqGrid({
        data: mydata,
        datatype: "local",
        rownumbers: rownumbers,
        firstsortorder: 'asc',
        height: height,
        width: width,
        autowidth: false,
        shrinkToFit: shrinkToFit,
        rowNum: rowNum,
        rowList: [20, 50, 100, rowNum],
        colNames: colNames,
        colModel: colModel,
        viewrecords: true,
        caption: caption,
        hidegrid: true,
        pager: id_pager,
        editurl: editurl,
        grouping: grouping,
        groupingView: groupingView,
        ajaxRowOptions: {async: true},
        footerrow: footerRow,
        userDataOnFooter: userDataOnFooter,
        userdata:userdata,
        sortable: sortable,
        //frozenStaticCols : true,
        onSelectRow: function (id) {

            if (id && id !== lastsel) {
                $('#' + id_table).restoreRow(lastsel);
                lastsel = id;
            }
            $('#' + id_table).editRow(id, false);

        },
        beforeSelectRow: function (id, e) {

            if(id != null && id != lastsel){
                $('#' + id_table).restoreRow(lastsel);
            }

            var row = $('#' + id_table).jqGrid('getRowData', id);
            var selected = 0;

            if (row.ss == 0) {

                if(id != lastsel) {

                    isClicked = false;

                    $('#' + id_table).setColProp('c', {editable: true});

                    $('#' + id_table).setColProp('t', {
                        editable: true,
                        editoptions: {
                            dataEvents: [{
                                type: 'change',
                                fn: function (e) {
                                    selected = parseInt($(e.target).val());
                                    var cpteStr = $(this).closest('tr').find('td[aria-describedby="js_id_releve_liste_tc"]');
                                    //-1: Nouveau tiers
                                    if (selected == -1) {
                                        $('#js_tiers_modal').modal('show');
                                        $('#js_save_tiers').attr('data-id', id);
                                        cpteStr.text("");
                                    }
                                    //Tokony miova nt cmpte_str rehefa miova ny if
                                    else{

                                        $.ajax({
                                            url: Routing.generate('banque_grid_tiers_cpte_str', {tid: selected}),
                                            type: 'html',
                                            async: true,
                                            success: function (data) {
                                                cpteStr.text(data);
                                            }
                                        })

                                    }
                                }
                            }]
                        }
                    });

                    $('#' + id_table).setColProp('tv', {editable: true});

                    $('#' + id_table).setColProp('n', {
                        editable: true,
                        editoptions: {
                            dataEvents: [{
                                type: 'change',
                                fn: function (e) {
                                    var nid = parseInt($(e.target).val());
                                    var did = $('#dossier').val();
                                    var rid = $(e.target).closest('tr').attr('id');
                                    var sn = $('tr[id="' + id + '"] td[aria-describedby="js_id_releve_liste_sn"] select');
                                    sn.empty();
                                    $.ajax({
                                        url: Routing.generate('banque_grid_combo', {json: 3, did: did, nid: nid}),
                                        type: 'html',
                                        async: true,
                                        success: function (data) {
                                            sn.append(data);
                                        }
                                    })
                                }
                            },
                                {
                                    type: 'click',
                                    fn: function (e) {
                                        if (isClicked == false) {
                                            isClicked = true;
                                            var nid = parseInt($(e.target).val());
                                            var did = $('#dossier').val();
                                            var rid = $(e.target).closest('tr').attr('id');
                                            var sn = $('tr[id="' + id + '"] td[aria-describedby="js_id_releve_liste_sn"] select');
                                            sn.empty();
                                            $.ajax({
                                                url: Routing.generate('banque_grid_combo', {
                                                    json: 3,
                                                    did: did,
                                                    nid: nid
                                                }),
                                                type: 'html',
                                                async: true,
                                                success: function (data) {
                                                    sn.append(data);
                                                }
                                            })
                                        }
                                    }
                                }
                            ]
                        }
                    });

                    $('#' + id_table).setColProp('sn', {
                        editable: true,
                        editoptions:{
                            dataEvents: [{
                                type: 'click',
                                fn: function (e) {
                                    if (isClicked == false) {
                                        isClicked = true;
                                        var nid = parseInt($(e.target).val());
                                        var did = $('#dossier').val();
                                        var sn = $('tr[id="' + id + '"] td[aria-describedby="js_id_releve_liste_sn"] select');
                                        sn.empty();
                                        $.ajax({
                                            url: Routing.generate('banque_grid_combo', {
                                                json: 4,
                                                did: did,
                                                nid: nid
                                            }),
                                            type: 'html',
                                            async: true,
                                            success: function (data) {
                                                sn.append(data);
                                            }
                                        })
                                    }
                                }
                            }]
                        }
                    });
                }
            }
            else {
                $('#' + id_table).setColProp('c', {editable: false});
                $('#' + id_table).setColProp('t', {editable: false});
                $('#' + id_table).setColProp('tv', {editable: false});
                $('#' + id_table).setColProp('n', {editable: false});
                $('#' + id_table).setColProp('sn', {editable: false});
            }

            var target = $(e.target);
            var item_action = (target.closest('td').children('.js_edit_releve').length > 0);
            return !item_action;
        },

        aftersavefunc: function () {
            //alert('test');

        },
        loadComplete: function () {
            if(autoContent)
            {
                var $this = $(this), iCol, iRow, rows, row, cm, colWidth,
                    $cells = $this.find(">tbody>tr>td"),
                    $colHeaders = $(this.grid.hDiv).find(">.ui-jqgrid-hbox>.ui-jqgrid-htable>thead>.ui-jqgrid-labels>.ui-th-column>div"),
                    colModel = $this.jqGrid("getGridParam", "colModel"),
                    n = $.isArray(colModel) ? colModel.length : 0,
                    idColHeadPrexif = "jqgh_" + this.id + "_";

                $cells.wrapInner("<span class='mywrapping'></span>");
                $colHeaders.wrapInner("<span class='mywrapping'></span>");

                for (iCol = 0; iCol < n; iCol++) {
                    cm = colModel[iCol];
                    colWidth = $("#" + idColHeadPrexif + $.jgrid.jqID(cm.name) + ">.mywrapping").outerWidth() + 25; // 25px for sorting icons
                    for (iRow = 0, rows = this.rows; iRow < rows.length; iRow++) {
                        row = rows[iRow];
                        if ($(row).hasClass("jqgrow"))
                        {
                            colWidth = Math.max(colWidth, $(row.cells[iCol]).find(".mywrapping").outerWidth());
                        }
                    }
                    $this.jqGrid("setColWidth", iCol, colWidth - 13);
                }
                $this.jqGrid('setGridWidth',width - 20);
            }

            if(typeof loadCompleteJQgrid == 'function') loadCompleteJQgrid();
        }
    });

    if (typeof firstSort !== 'undefined') {
        current_jqgrid.sortGrid(firtsColSorter);
        if (firstSort == 'asc') current_jqgrid.sortGrid(firtsColSorter);
    }
    if (caption == 'hidden') $('#gview_' + id_table + ' div.ui-jqgrid-caption').remove();

    //current_jqgrid.jqGrid('setFrozenColumns');
}
