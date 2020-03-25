/**
 * Created by SITRAKA on 21/03/2019.
 */
var index_table = 0,
    index_class = 0,
    index_carossel = 0,
    cl_tr_udated = 'tr_updated',
    datas = [],
    dossier_index = 0;

$(document).ready(function(){
    $(document).on('change','.cl_instruction',function(){
        $('.'+cl_tr_udated).removeClass(cl_tr_udated);
        $(this).closest('tr').addClass(cl_tr_udated);
        change_instruction($(this));
    });

    $(document).on('click','.cl_valider_lettrage',function(){
        var type = parseInt($(this).attr('data-type')),
            tr = $('.' + cl_tr_udated),
            image = $('#js_zero_boost').val();

        if (type !== 0)
        {
            $(this).closest('.modal-body').find('.cl_radio').each(function(){
                if ($(this).is(':checked')) image = $(this).closest('tr').attr('id')
            });

            if (image === $('#js_zero_boost').val())
            {
                show_info('Erreur','Choisir la pièce','error');
                return;
            }
        }

        $.ajax({
            data: {
                echange_ecriture: tr.attr('id'),
                image: image
            },
            url: Routing.generate('drt_ecriture_lettrer'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                update_tr($.parseJSON(data));
            }
        });
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    $(document).on('click','.cl_ecriture_show_image_a_valider',function(){
        $('.'+cl_tr_udated).removeClass(cl_tr_udated);
        $(this).closest('tr').addClass(cl_tr_udated);
        index_class++;
        $(this).addClass('edit-' + index_class);
        var ids = $(this).attr('data-datas').toString(),
            html = '' +
                '<div class="row">' +
                    '<div class="col-lg-12">' +
                    '<table id="table_image_'+index_table+'" data-index_class="'+index_class+'"></table>' +
                    '</div>' +
                    '<div class="col-lg-12 text-right" style="margin-top: 5px!important;">' +
                    '<span class="btn btn-sm btn-white js_close_modal"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Annuler</span>' +
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

                show_modal(html,'Liste des images trouvées');

                /*var options = { modal: false, resizable: false, title: 'Liste des images trouvées' };
                modal_ui(options,html, false,0.5,0.5);*/

                $('#table_image_' + index_table).jqGrid({
                    data: data,
                    datatype: 'local',
                    height: $(window).height() - 300,
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

    $('#modal').on('hidden.bs.modal', function () {
        update_tr();
    });
});

function ecritures()
{
    var dos_el = $('#dossier'),
        dossier_text = dos_el.find('option:selected').text().trim().toUpperCase(),
        dossiers = [];

    /*if (dossier_text === '' || dossier_text === 'TOUS')
    {
        dos_el.closest('.form-group').addClass('has-error');
        $('.cl_export').addClass('hidden');
        set_table_analyse([]);
        return;
    }
    else
    {
        $('.cl_export').removeClass('hidden');
        dos_el.closest('.form-group').removeClass('has-error');
    }*/

    $.ajax({
        data: {
            client: $('#client').val(),
            dossier: dos_el.val(),
            exercice: $('#exercice').val(),
            echange_type: $('input[name="show-filter-item"]:checked').val()
        },
        url: Routing.generate('drt_ecriture'),
        type: 'POST',
        dataType: 'json',
        success: function(data){
            //$('#tab-analyse').find('.cl_container_analyse').html(data); return;
            set_table_analyse(data);
        }
    });
}

function set_table_analyse(data)
{
    var table = '<table id="id_table_ecriture"></table>';
    $('#tab-analyse').find('.cl_container_analyse').html(table);
    var w = $('#id_table_ecriture').parent().width();

    var dossier_text = $('#dossier').find('option:selected').text().trim().toUpperCase();

    var col_names = ['Dossier','Page','Date','Jnl','Compte','Piéce','Libellé','Débit','Crédit','Solde',''],
        col_model = [
        { name: 'dossier', index: 'dossier', hidden:!(dossier_text === '' || dossier_text === 'TOUS'), width:((dossier_text === '' || dossier_text === 'TOUS') ? 10 : 0)*w/100 ,sortable:true, classes:'c_dossier' },
        { name: 'page', index: 'page', sortable:true, hidden:true, classes:'c_page' },
        { name: 'date', index: 'date', width:9*w/100, sortable:true, classes:'c_date' },
        { name: 'jnl', index: 'jnl', width:4*w/100, sortable:true, classes:'c_jnl' },
        { name: 'compte', index: 'compte', width:9*w/100, sortable:true, classes:'c_compte' },
        { name: 'piece', index: 'piece', width:9*w/100, sortable:true, classes:'c_piece' },
        { name: 'libelle', index: 'libelle', width:((dossier_text === '' || dossier_text === 'TOUS') ? 24 : 34)*w/100, sortable:true, classes:'c_libelle' },
        { name: 'credit', index: 'credit', width:8*w/100, sortable:true, classes:'text-primary c_debit', align:'right', formatter: function(v) { return '<b>'+ number_format(v, 2, ',', ' ') +'</b>'; } },
        { name: 'debit', index: 'debit', width:8*w/100, sortable:true, classes:'text-danger c_credit', align:'right', formatter: function(v) { return '<b>'+ number_format(v, 2, ',', ' ') +'</b>'; } },
        { name: 'solde', index: 'solde', width:8*w/100, sortable:true, classes:'c_solde', align:'right', formatter: function(v) { return '<b class="'+ ((v < 0) ? 'text-danger' : 'text-primary') +'">'+ number_format(v, 2, ',', ' ') +'</b>'; } },
        { name: 'images', index: 'images', width:11*w/100, sortable:true, classes:'c_instruction', formatter:function(v) { return instruction_formatter(v) } }];

    $('#id_table_ecriture').jqGrid({
        data: data,
        datatype: 'local',
        width: w,
        height: $(window).height() - 105,
        autowidth: false,
        shrinkToFit: true,
        rowNum: 100000,
        colNames: col_names,
        colModel: col_model,
        viewrecords: true,
        hidegrid: false
    });
}

function instruction_formatter(value)
{
    if (value.image !== null)
        return '<span class="pointer js_show_image text-info" data-id_image="'+value.image.id+'">'+value.image.n+'</span>';
    else if (parseInt(value.c) > 0)
        return '<i class="fa fa-file-pdf-o cl_ecriture_show_image_a_valider pointer" aria-hidden="true" data-datas="'+value.ids+'"></i>';
    else
    {
        var v = value.etat;
        return '' +
            '<select class="cl_instruction no-moze" style="width: 100%; border: none">' +
                '<option value="0" '+(v === 0 ? 'selected' : '')+'></option>' +
                '<option value="1" '+(v === 1 ? 'selected' : '')+'>Répondue</option>' +
                '<option value="2" '+(v === 2 ? 'selected' : '')+'>Envoyer la pièce</option>' +
            '</select>';
    }
}

function change_instruction(select)
{
    var echange_ecriture = select.closest('tr').attr('id'),
        instruction = parseInt(select.val());

    if (instruction === 2)
    {
        $.ajax({
            data: { echange_ecriture: echange_ecriture },
            type: 'POST',
            url: Routing.generate('drt_ecriture_image_uploader'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Charger l\'image');

                var defaultMessage =
                    'CLIQUER pour sélectionner la PIECE ou DEPOSER la ici.';
                $('#id_image')
                    .fileinput({
                        language: 'fr',
                        theme: 'fa',
                        uploadAsync: false,
                        showPreview: true,
                        showUpload: true,
                        showBrowse: false,
                        showRemove: false,
                        showCancel: false,
                        maxFilePreviewSize: 0,
                        uploadUrl: Routing.generate('drt_ecriture_lettrer_image'),
                        dropZoneTitle: defaultMessage,
                        browseOnZoneClick: true,
                        uploadExtraData: function(){
                            var echange_ecriture = $('.' + cl_tr_udated).attr('id');
                            return { echange_ecriture : echange_ecriture }
                        }
                    })
                    .on('filebatchselected', function(event, files) { })
                    .on('fileselect', function(event, numFiles, label) { })
                    .on('fileloaded', function(event, file, previewId, index, reader) { })
                    .on('filebatchuploadsuccess', function(event, data) {
                        var form = data.form, files = data.files, extra = data.extra,
                            response = data.response.toString(), reader = data.reader;

                        if (parseInt(response) === -1)
                        {
                            show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer l\' image","error");
                        }
                        else
                        {
                            close_modal();
                            show_info("Envoi images","Les images sont envoyées avec succès.");
                        }
                    })
                    .on('filebatchuploaderror', function(event, data, msg) {
                        var form = data.form, files = data.files, extra = data.extra,
                            response = data.response, reader = data.reader;
                        show_info("Une erreur est survenue pendant l'envoi","Veuillez renvoyer l\' image","error");
                    });
            }
        });
        return;
    }

    $.ajax({
        data: {
            echange_ecriture: echange_ecriture,
            instruction: instruction
        },
        url: Routing.generate('drt_ecriture_change_isntruction'),
        type: 'POST',
        dataType: 'json',
        success: function(data){
            show_info('SUCCES','Modification enregistrée avec succès');
            update_tr(data);
        }
    });
}

function analyse_formatter(v)
{
    return '<i class="fa fa-th-list" aria-hidden="true"></i>';
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

function update_tr(data)
{
    if ($('.' + cl_tr_udated).length > 0)
    {
        var id = $('.' + cl_tr_udated).attr('id');
        if (typeof data !== 'undefined')
        {
            var newData = data;
            newData.id = id;
            $('#id_table_ecriture').jqGrid('setRowData', id, newData);
            close_modal();
        }
        else
        {
            $.ajax({
                data: {
                    echange_ecriture: id
                },
                url: Routing.generate('drt_ecriture_updated'),
                type: 'POST',
                dataType: 'html',
                success: function(data){
                    var newData = $.parseJSON(data);
                    newData.id = id;
                    $('#id_table_ecriture').jqGrid('setRowData', id, newData);
                    close_modal();
                }
            });
        }
    }
}
