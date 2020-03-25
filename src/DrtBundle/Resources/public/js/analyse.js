/**
 * Created by SITRAKA on 12/03/2019.
 */
var index_table = 0,
    index_class = 0,
    index_carossel = 0;

$(document).ready(function(){

    $(document).on('click','.cl_analyser',function(){
        var echange_item = $(this).closest('tr').attr('id');
        $.ajax({
            data: { echange_item: echange_item },
            url: Routing.generate('drt_analyse'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                if (parseInt(data) === 0)
                {
                    show_info('Erreur','Fichier introuvable','error');
                    return;
                }
                else if (parseInt(data) === 1)
                {
                    show_info('Erreur','Impossible d analyser ce fichier','error');
                    return;
                }

                show_modal(data,'Analyse de la DRT',undefined,'modal-x-lg');
                //$('#modal-animated').width($(window).width());
                set_js_excels();
            }
        });
    });

    $(document).on('click','.cl_show_image_a_valider',function(){
        index_class++;
        $(this).addClass('edit-' + index_class);
        var ids = $.parseJSON(decodeURIComponent($(this).attr('data-datas').toString())),
            html = '' +
                '<div class="row">' +
                    '<div class="col-lg-12">' +
                        '<table id="table_image_'+index_table+'" data-index_class="'+index_class+'"></table>' +
                    '</div>' +
                    '<div class="col-lg-12 text-right" style="margin-top: 5px!important;">' +
                        '<span class="btn btn-sm btn-white cl_valider_lettrage" data-type="-1"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Annuler</span>' +
                        '<span class="btn btn-sm btn-white cl_valider_lettrage" data-type="0"><i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;Aucune&nbsp;pi&egrave;ce</span>' +
                        '<span class="btn btn-sm btn-white cl_valider_lettrage" data-type="1"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;Valider&nbsp;cette&nbsp;pi&egrave;ce</span>' +
                    '</div>' +
                '</div>';

        $.ajax({
            data: { images: ids, index:index_class },
            url: Routing.generate('drt_analyse_images_shows'),
            type: 'POST',
            dataType: 'json',
            success: function(data){
                var options = { modal: false, resizable: false, title: 'Liste des images trouvées' };
                modal_ui(options,html, false,0.5,0.5);

                $('#table_image_' + index_table).jqGrid({
                    data: data,
                    datatype: 'local',
                    height: $('#table_image_' + index_table).closest('.ui-widget-content').height() - 75,
                    autowidth: true,
                    shrinkToFit: true,
                    rowNum: 100000,
                    colNames: ['','Pièce', 'Tiers', 'Bilan', 'TVA', 'Résultat', 'Montant Ht', 'Montant TVA', 'Montant TTc', ''],
                    colModel: [
                        { name: 'i', index: 'i', sortable: false, align:'center', width: 50, formatter: function(v){ return radio_formatter(v) } },
                        { name: 'image', index: 'image', sortable:true, align:'center', classes:'js_show_image_ pointer text-primary' },
                        { name: 'libelle', index: 'libelle', sortable:true },
                        { name: 'bilan', index: 'bilan', sortable:true, formatter: function (v) { return compte_formatter(v) ; } },
                        { name: 'tva', index: 'tva', sortable:true, formatter: function (v) { return compte_formatter(v) ; } },
                        { name: 'resultat', index: 'resultat', sortable:true, formatter: function (v) { return compte_formatter(v) ; } },
                        { name: 'mHT', index: 'mHT', sortable:true, sorttype: 'number', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } },
                        { name: 'mTVA', index: 'mTVA', sortable:true, sorttype: 'number', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } },
                        { name: 'mTTC', index: 'mTTC', sortable:true, sorttype: 'number', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } },
                        { name: 'imi', index: 'imi', hidden:true, classes:'js_id_image' }
                    ],
                    viewrecords: true,
                    hidegrid: false
                });

                index_table++;
            }
        });
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    $(document).on('click','.cl_valider_lettrage',function(){
        var type = parseInt($(this).attr('data-type')),
            btn_close = $(this).closest('.ui-dialog').find('button.ui-dialog-titlebar-close');
        if (type === -1) btn_close.click();
        else
        {
            var edit = parseInt($(this).closest('.ui-dialog').find('.ui-jqgrid-btable').attr('data-index_class')),
                tr = $('.edit-' + edit).closest('tr');

            if (type === 0)
            {
                tr.removeClass('row-red');
                btn_close.click();
            }
            else
            {
                var cocher = false;
                $(this).closest('.ui-dialog').find('.cl_radio').each(function(){
                    if ($(this).is(':checked')) cocher = true;
                });

                if (cocher)
                {
                    tr.addClass('row-red');
                    $(this).closest('.ui-dialog').find('button.ui-dialog-titlebar-close').click();
                }
                else show_info('Notice','Veuillez selectionner la pièce','error');
            }
        }
    });

    $(document).on('click','#id_save_as_excel',function(){
        var pages = [],
            names = [],
            stylesPages = [];

        $('#id_tabs_container').find('.cl_body_excel').each(function(){
            var rows = [],
                styles = [];
            $(this).find('.jexcel-content').find('tr').each(function(){
                if (!$(this).hasClass('row-red'))
                {
                    var row = [],
                        style = [];
                    $(this).find('td').each(function(){
                        if (!$(this).hasClass('jexcel_label'))
                        {
                            row.push($(this).text());
                            style.push({
                                color: hex($(this).css('color')),
                                bgColor: hex($(this).css('background-color')),
                                colSpan: ($(this).attr('colspan') !== undefined) ? $(this).attr('colspan') : 1
                            });
                        }
                    });
                    rows.push(row);
                    styles.push(style);
                }
            });
            pages.push(rows);
            names.push($(this).attr('data-name'));
            stylesPages.push(styles);
        });

        /*$.ajax({
            data: {
                styles: encodeURI(JSON.stringify(stylesPages))
            },
            url: Routing.generate('drt_analyse_generate_xls'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                var options = { modal: false, resizable: false, title: 'Liste des images trouvées' };
                modal_ui(options,data, false,0.5,0.5);
            }
        });
        return;*/
        var type = $(this).attr('data-type'),
            params = ''
                + '<input type="hidden" name="styles" value="'+encodeURI(JSON.stringify(stylesPages))+'">'
                + '<input type="hidden" name="echange_item" value="'+encodeURI($('#id_tabs_container').attr('data-echange_item').toString())+'">'
                + '<input type="hidden" name="names" value="'+encodeURI(JSON.stringify(names))+'">'
                + '<input type="hidden" name="pages" value="'+encodeURI(JSON.stringify(pages))+'">';

        $('#id_export').attr('action',Routing.generate('drt_analyse_generate_xls')).html(params).submit();
    });


    $(document).on('click','#id_show_reponses',function(){
        index_carossel++;
        var echange_item = $(this).attr('data-echange_item');
        $.ajax({
            data: {
                echange_item: echange_item,
                index: index_carossel
            },
            url: Routing.generate('drt_analyse_charger_reponse'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                var options = { modal: false, resizable: false, title: 'Liste des images trouvées' };
                modal_ui(options,data, false,0.5,0.5);
            }
        });
    });
    /*$(document).on('click','.cl_compte_detail',function(){
        var id = $(this).attr('data-id'),
            type = parseInt($(this).attr('data-type'));

        var exercices = [],
            mois = [],
            cloture = parseInt($('#id_cloture_dossier').attr('data-cloture')),
            titre = 'Grand Livre';
        if (isNaN(cloture)) cloture = 12;
        var periodes = get_periodes(cloture);
        exercices.push($('#exercice').val());
        show_compte_details(id,type,exercices,mois,periodes,titre);
        eb_set_class_table(exercices);
    });*/
});

/*function get_periodes(cloture)
{
    var periodes = [],
        first_mois = cloture;

    while (periodes.length < 12)
    {
        first_mois++;
        if (first_mois === 13) first_mois = 1;
        var mois_str = ((first_mois < 10) ? '0' : '') + first_mois.toString();
        periodes.push({'libelle':get_mois_libelle(first_mois),'moiss':[mois_str]});
    }

    return periodes;
}*/

function analyse_formatter(v)
{
    return '<i class="fa fa-th-list" aria-hidden="true"></i>';
}

function set_js_excels()
{
    //return;

    $('#id_tabs_container').find('.cl_body_excel').each(function(){
        var index = parseInt($(this).attr('data-index')),
            id = 'id_js_excel_'+index,
            width = $(this).width(),
            max_col = parseInt($(this).attr('data-max_col')),
            data = $.parseJSON($(this).html('<div class="cl_excel" id="'+id+'"></div>').attr('data-datas')),
            i,columns = [];

        var customEditor = {
            setValue : function(cell, value) {
                var v = [];
                try { v = $.parseJSON(decodeURIComponent(value)) }
                catch (e) { v = [] }

                var html = '';
                if (v.length > 0)
                {
                    var ids = [];
                    for (var j = 0; j < v.length; j++) ids.push(v.id);

                    html = '<i class="fa fa-file-pdf-o cl_show_image_a_valider pointer" aria-hidden="true" data-datas="'+encodeURIComponent(value)+'"></i>';
                }

                $(cell)
                    .html(html);
                return true;
            }
        };

        for (i = 0; i < max_col + 5; i++)
        {
            if (i === max_col - 1) columns.push({ editor:customEditor, readOnly:true  });
            else columns.push({});
        }

        $('#'+id).jexcel({
            data:data,
            tableOverflow:true,
            tableHeight:'300px',
            tableWidth: width,
            minDimensions:[max_col + 5,data.length + 25],
            columns: columns,
            allowComments:true,
            toolbar:[
                { type:'i', content:'undo', method:undo },
                { type:'i', content:'redo', method:redo },
                //{ type:'i', content:'save', method:function (instance, selectedCell) { $(instance).jexcel('download'); } },
                { type:'select', k:'font-family', v:['Arial','Verdana'] },
                { type:'select', k:'font-size', v:['9px','10px','11px','12px','13px','14px'] },
                { type:'i', content:'format_align_left', k:'text-align', v:'left' },
                { type:'i', content:'format_align_center', k:'text-align', v:'center' },
                { type:'i', content:'format_align_right', k:'text-align', v:'right' },
                { type:'i', content:'format_bold', k:'font-weight', v:'bold' },
                { type:'spectrum', content:'format_color_text', k:'color' },
                { type:'spectrum', content:'format_color_fill', k:'background-color' }
            ]
        });

        var merges = $.parseJSON($(this).attr('data-merges'));
        for (var p = 0; p < merges.length; p++)
        {
            $('#' + id).find('.jexcel-content')
                .find('tbody tr:nth-child('+(merges[p].row + 1)+')')
                .find('td:nth-child('+(merges[p].start + 2)+')')
                .attr('colspan',merges[p].end);
        }

        var styles = $.parseJSON($(this).attr('data-styles'));
        var row = 0;
        $('#' + id).find('.jexcel-content').find('tbody tr').each(function(){
            var cell = 0;
            $(this).find('td').each(function(){
                if (!$(this).hasClass('jexcel_label'))
                {
                    if (styles[row] !== undefined)
                        if (styles[row][cell] !== undefined)
                        {
                            var color = styles[row][cell].color,
                                bgColor = styles[row][cell].bgColor;
                            if (color === bgColor && bgColor === '000000') bgColor = 'FFFFFF';

                            $(this)
                                .css('color','#' + color)
                                .css('background-color', '#' + bgColor);
                        }
                    cell++;
                }
            });
            row++;
        });

        /*$('#'+id).jexcel('setStyle', [ { A1: 'font-weight: bold; color:red;' }, { B2: 'background-color: yellow;' }, { C1: 'text-decoration: underline;' }, { D2: 'text-align:left;' } ]);*/
    });
}

function compte_formatter(v)
{
    if (v === null) return '';
    return '<span class="pointer cl_compte_detail" data-id="'+v.id+'" data-type="'+v.t+'">'+(v.l.toString().trim() === '' ? '-' : v.l)+'</span>';
}

function radio_formatter(v)
{
    var name = 'radio-'+ index_table + '-' + v;
    return '<input type="radio" name="'+name+'" class="cl_radio">';
}

function undo(instance, selectedCell)
{
    $(instance).jexcel('undo');
}

function redo(instance, selectedCell)
{
    $(instance).jexcel('redo');
}

function hex( c )
{
    var m = /rgba?\((\d+), (\d+), (\d+)/.exec( c );
    return m
        ? '#' + ( m[1] << 16 | m[2] << 8 | m[3] ).toString(16)
        : c;
}
