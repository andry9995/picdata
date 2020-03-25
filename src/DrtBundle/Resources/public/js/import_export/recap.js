/**
 * Created by SITRAKA on 27/09/2019.
 */

var can_restart = false;
$(document).ready(function(){

    var lastsel_lib;

    $('#modal').on('hidden.bs.modal', function(e){
        e.preventDefault();
        if (can_restart) go();
        can_restart = false;
    });


    $(document).on('click','.cl_details',function(){
        var client = $(this).closest('tr').attr('id'),
            type;
        if ($(this).hasClass('cl_details_compta'))
            type = 1;
        else if ($(this).hasClass('cl_details_non_closed'))
        {
            type = 2;
            can_restart = true;
        }
        else {
            if ($(this).hasClass('dnp_list')) {
               type = 3
            } else {
              type = 0;      
            }
        } 

        $.ajax({
            data: {
                type: type,
                client: client
            },
            type: 'POST',
            url: Routing.generate('import_export_recaps_details'),
            dataType: 'json',
            success: function(data) {
                show_modal('<table id="id_details"></table>','details',undefined,'modal-lg');

                var table_selected = $('#id_details'),
                    w = table_selected.parent().width(),
                    h = $(window).height() - 250,
                    total = 0;

                if (type == 3) {

                    var colNames = ['Dossier','Code','Libellé','Action'];

                    var colModels = [{
                        name: 'dossier',
                        index: 'dossier',
                        classes: 'dnp-dossier'
                    },{
                        name: 'code',
                        index: 'code',
                        align: 'center'
                    },{
                        name: 'libelle',
                        index: 'libelle',
                        classes: 'dnp-lib',
                        editable: true,
                        edittype:"select",
                        editoptions:{
                            value: data.journal,
                            dataInit: function (elem) {
                                $(elem).addClass('dnp-journal-option');
                            }
                        }
                    },{
                        name: 'action',
                        index: 'action',
                        classes: 'dnp-action',
                        width: 30
                    }];

                    jQuery('#id_details').jqGrid({
                        data: data.datas,
                        datatype: 'local',
                        height: h,
                        width: w,
                        rowNum: 10000000,
                        rowList: [10,20,30],
                        colNames:colNames,
                        colModel:colModels,
                        viewrecords: true,
                        editurl: Routing.generate('dnp_edit'),
                        footerrow: true,
                        userDataOnFooter: true,
                        sortname: 'dossier',
                        grouping:true,
                        groupingView : {
                           groupField : ['dossier'],
                           // groupColumnShow : [false]
                        },
                        onSelectRow: function (id) {
                            if (id && id != lastsel_lib) {
                                jQuery('#id_details').restoreRow(lastsel_lib);
                                lastsel_lib = id;
                            }
                            jQuery('#id_details').editRow(id, false);
                        },
                        beforeSelectRow: function (rowid, e) {
                            var target = $(e.target);
                            var item_action = (target.closest('td').children('.icon-action').length > 0);
                            return !item_action;

                        },
                        ajaxRowOptions: {async: true}

                    })

                } else {
                    $.each( data.datas, function( index, value ){
                        total += parseFloat( (type !== 0) ?  1 : value.compta );
                    });

                    var colNames = [],
                        colModels = [];

                    colNames.push('Dossier');
                    colNames.push('Compta');
                    colModels.push({ name:'d_nom', index:'d_nom' });
                    colModels.push({ name:'compta', index:'compta', classes:'compta', width:25, align:'right', formatter: function(v){ return type === 0 ? number_format(v,0,',',' ',true) : v } });

                    if (type === 2)
                    {
                        colNames.push('');
                        colModels.push({ name:'x', index:'x', width:25, align:'center', formatter: function(v){ return '<span class="btn btn-white btn-xs cl_cloturer">Clôturer</span>' } });
                    }

                    jQuery('#id_details').jqGrid({
                        data: data.datas,
                        datatype: 'local',
                        height: h,
                        width: w,
                        rowNum: 10000000,
                        rowList: [10,20,30],
                        colNames:colNames,
                        colModel:colModels,
                        viewrecords: true,
                        footerrow: true,
                        userDataOnFooter: true,
                        userData: { 'd_nom': '', 'compta': '<span id="id_total_detail">'+number_format(total,0,',',' ',true)+'</span>' }
                    });
                }



            }
        });
    });

    $(document).on('change', '.dnp-journal-option', function (e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;

        var selRowId = jQuery('#id_details').jqGrid ('getGridParam', 'selrow');
        $("#id_details").setCell (selRowId,'code',valueSelected,'');



    });

    $(document).on('click', '.save-dnp', function (event) {
        event.preventDefault();
        event.stopPropagation();
        jQuery('#id_details').jqGrid('saveRow', lastsel_lib, {
            "aftersavefunc": function() {
                // reloadGrid(jQuery('#id_details'), Routing.generate('fact_domaine'));
            }
        });
    });

    $(document).on('click','.cl_cloturer',function(){
        var tr = $(this).closest('tr'),
            exercice = tr.find('.compta').text().trim(),
            dossier = tr.attr('id');

        $.ajax({
            data: {
                dossier: dossier,
                exercice: exercice
            },
            type: 'POST',
            url: Routing.generate('import_export_recaps_cloturer'),
            dataType: 'html',
            success: function(data) {
                if (parseInt(data) === 0)
                {
                    tr.remove();
                    var new_total = number_fr_to_float($('#id_total_detail').text()) - 1;
                    $('#id_total_detail').html(number_format(new_total,0,',',' ',true));
                    show_info('Succès','Compta clôturée avec succès');
                }
                else show_info('Erreur','Une erreur c est produite pendant la modification','error');
            }
        });
    });
});

function charger_recap()
{
    $.ajax({
        data: {

        },
        type: 'POST',
        url: Routing.generate('import_export_recaps'),
        dataType: 'json',
        success: function(data) {
            $('#id_tabs_content').find('.tab-pane.active .panel-body').html('<table id="id_table_recap"></table>');

            var table_selected = $('#id_table_recap'),
                w = table_selected.parent().width(),
                h = $(window).height() - 250;

            var current_jqgrid = jQuery('#id_table_recap').jqGrid({
                data: data.datas,
                datatype: 'local',
                height: h,
                width: w,
                rowNum: 10000000,
                rowList: [10,20,30],
                colNames:['Client','Dossiers non paramètrés','Dossier avec compta cloturé','Compta cloturé','Compta non cloturé'],
                colModel:[
                    { name:'n', index:'n', width: 40 },
                    { name:'dnp', index:'dnp', sorttype: 'number', classes:'cl_details pointer dnp_list', width: 13, align:'right', formatter:function(v){ return number_format(v,0,',',' ',true); } },
                    { name:'d', index:'d', sorttype: 'number', classes:'cl_details pointer', width: 13, align:'right', formatter:function(v){ return number_format(v,0,',',' ',true); } },
                    { name:'c', index:'c', sorttype: 'number', classes:'cl_details pointer cl_details_compta', width: 13, align:'right', formatter:function(v){ return number_format(v,0,',',' ',true); } },
                    { name:'nc', index:'nc', sorttype: 'number', classes:'cl_details pointer cl_details_non_closed', width: 13, align:'right', formatter:function(v){ return number_format(v,0,',',' ',true); } }
                ],
                viewrecords: true,
                footerrow: true,
                userDataOnFooter: true,
                userData: { 'd': number_format(data.td,0,',',' ',true), 'c': number_format(data.tc,0,',',' ',true), 'nc':number_format(data.tnc,0,',',' ',true)}
            });

            current_jqgrid.sortGrid('n');
            current_jqgrid.sortGrid('n');
        }
    });
}
