/**
 * Created by SITRAKA on 22/02/2019.
 */
$(document).ready(function(){
    $(document).on('click','.cl_export_xls',function(){
        var dossier = $(this).closest('tr').attr('id'),
            exercice = parseInt($('#exercice').val()) + parseInt($(this).attr('data-variation')) ;
    });
});

function charger_export()
{
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val()
        },
        type: 'POST',
        url: Routing.generate('import_export_exports'),
        dataType: 'json',
        success: function(data) {
            set_table_export(data);
        }
    });
}

function set_table_export(data)
{
    //$('#id_tabs_content').find('.tab-pane.active .panel-body').html(data);return;
    $('#id_tabs_content').find('.tab-pane.active .panel-body').html('<table id="id_table_export"></table>');
    var table_selected = $('#id_table_export'),
        w = table_selected.parent().width(),
        h = $(window).height() - 250,
        tot_debit = 0,
        tot_credit = 0;

    jQuery('#id_table_export').jqGrid({
        data: data,
        datatype: 'local',
        height: h,
        width: w,
        rowNum: 10000000,
        rowList: [10,20,30],
        colNames:['Dossier','Dernier upload','Status','','Dernier upload','status',''],
        colModel:[
            { name:'nom', index:'nom', width: 40 },
            { name:'du_n1', index:'du_n1', width: 13, align:'center' },
            { name:'s_n1', index:'s_n1', width: 13 },
            { name:'x_n1', index:'x_n1', width: 4, align:'center', formatter: function() { return '<i class="fa fa-file-excel-o pointer cl_export_xls" data-variation="-1" aria-hidden="true"></i>' } },
            { name:'du_n', index:'du_n', width: 13, align:'center' },
            { name:'s_n', index:'s_n', width: 13 },
            { name:'x_n', index:'x_n', width: 4, align:'center', formatter: function() { return '<i class="fa fa-file-excel-o pointer cl_export_xls" data-variation="0" aria-hidden="true"></i>' } }
        ],
        viewrecords: true,
        footerrow: true,
        userDataOnFooter: true,
        userData: { 'db': tot_debit, 'cr': tot_credit }
    });

    var n = parseInt($('#exercice').val());
    jQuery('#id_table_export').jqGrid('setGroupHeaders', {
        useColSpanStyle: false,
        groupHeaders:[
            { startColumnName: 'nom', numberOfColumns: 1, titleText: '' },
            { startColumnName: 'du_n1', numberOfColumns: 3, titleText: '<strong>' + (n-1) + '</strong>' },
            { startColumnName: 'du_n', numberOfColumns: 3, titleText: '<strong>' + n + '</strong>' }
        ]
    });
}

/*
$(document).ready(function(){
    $(document).on('mouseenter ','#id_table_export tr',function(){
        $('.tr-over').removeClass('tr-over');
        var group = parseInt($(this).find('.js_group_journal').text());
        $('.js_g_'+group).each(function(){
            $(this).closest('tr').addClass('tr-over');
        });
    });
});

function charger_export()
{
    var dos_el = $('#dossier'),
        dossier_text = dos_el.find('option:selected').text().trim().toUpperCase();

    set_table_export([]);
    if (dossier_text === '' || dossier_text === 'TOUS')
    {
        dos_el.closest('.form-group').addClass('has-error');
        return;
    }
    else dos_el.closest('.form-group').removeClass('has-error');

    $.ajax({
        data: {
            dossier: dos_el.val(),
            exercice: $('#exercice').val()
        },
        type: 'POST',
        url: Routing.generate('import_export_exports'),
        dataType: 'json',
        success: function(data) {
            set_table_export(data);
        }
    });
}

function set_table_export(data)
{
    $('#id_tabs_content').find('.tab-pane.active .panel-body').html('<table id="id_table_export"></table>');
    var table_selected = $('#id_table_export'),
        w = table_selected.parent().width(),
        h = $(window).height() - 250,
        tot_debit = 0,
        tot_credit = 0;

    for (var i = 0; i < data.length; i++)
    {
        tot_debit += data[i].db;
        tot_credit += data[i].cr;
    }

    jQuery('#id_table_export').jqGrid({
        data: data,
        datatype: 'local',
        height: h,
        width: w,
        rowNum: 10000000,
        rowList: [10,20,30],
        colNames:['Date','Pièce','Compte','Libellé','Débit','Crédit','Groupe'],
        colModel:[
            { name:'dt', index:'dt' },
            { name:'pi', index:'pi', align:'center', formatter: function(v) { return image_formatter(v) } },
            { name:'cp', index:'cp' },
            { name:'lb', index:'lb' },
            { name:'db', index:'db', align:'right', formatter: function(v) { return number_format(v, 2, ',', ' ') } },
            { name:'cr', index:'cr', align:'right', classes:'text-danger', formatter: function(v) { return number_format(v, 2, ',', ' ') } },
            { name:'gr', index:'gr', hidden: true, formatter:function(v){ return '<span class="js_group_journal js_g_'+v+'">'+v+'</span>' } }
        ],
        viewrecords: true,
        footerrow: true,
        userDataOnFooter: true,
        userData: { 'db': tot_debit, 'cr': tot_credit }
    });
}*/