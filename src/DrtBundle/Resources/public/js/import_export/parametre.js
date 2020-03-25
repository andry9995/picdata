/**
 * Created by SITRAKA on 22/02/2019.
 */
var cl_param_edit = 'cl_param_edit',
    index_input = 0;
$(document).ready(function(){
    $(document).on('click','.cl_edit_frequence',function(){
        $('.'+cl_param_edit).removeClass(cl_param_edit);
        $(this).closest('tr').addClass(cl_param_edit);

        $.ajax({
            data: {
                dossier: $('.'+cl_param_edit).attr('id'),
                action: 0
            },
            type: 'POST',
            url: Routing.generate('import_export_param_edit'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Edit Paramètre');

                $('#id_ponctuel').find('input.form-control').each(function(){
                    set_datepicker($(this));
                });

                //set_datepicker($('#id_date_calcul'));
                hide_date();
            }
        });
    });

    $(document).on('change','#id_a_partir_de',function(){
        hide_date();
    });

    $(document).on('click','.cl_save_param',function(){
        save_param();
    });

    $(document).on('click','#id_add_ponctuel',function(){
        index_input++;
        var new_html = '' +
            '<div class="col-lg-4 cl_pontuel" style="margin-bottom: 2px" data-id="'+$('#js_zero_boost').val()+'">' +
                '<div class="input-group">' +
                    '<input id="new_input_'+index_input+'" type="text" class="form-control" value="">' +
                    '<span class="input-group-addon cl_delete_ponctuel"><i class="fa fa-trash-o"></i></span>' +
                '</div>' +
            '</div>';

        $(new_html).prependTo('#id_ponctuel');
        set_datepicker($('#new_input_'+index_input));
    });

    $(document).on('click','.cl_delete_ponctuel',function(){
        $(this).closest('.cl_pontuel').addClass('hidden');
    });
});

function charger_parametre()
{
    $.ajax({
        data: {
            client: $('#client').val(),
            site: $('#site').val(),
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val()
        },
        type: 'POST',
        url: Routing.generate('import_export_params'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            $('#id_tabs_content').find('.tab-pane.active .panel-body').html('<table id="id_table_param"></table>');
            var table_selected = $('#id_table_param'),
                w = table_selected.parent().width(),
                h = $(window).height() - 250,
                editurl = 'index.php';
            set_table_jqgrid($.parseJSON(data),h,col_model_param(),col_model_param(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
        }
    });
}

function col_model_param(w)
{
    var colM = [];

    if(typeof w !== 'undefined')
    {
        colM.push({ name:'cli', index:'cli', sortable:true, width: 28 * w/100 });
        colM.push({ name:'dos', index:'dos', sortable:true, width: 28 * w/100 });
        colM.push({ name:'fre', index:'fre', sortable: true, width: 10 * w/100 });
        colM.push({ name:'jrs', index:'jrs', sortable: true, width: 5 * w/100 });
        colM.push({ name:'edf', index:'edf', align:'center', width: 5 * w/100, classes:'pointer cl_edit_frequence', formatter:function(){ return '<i class="fa fa-eyedropper" aria-hidden="true"></i>'; } });
        colM.push({ name:'pon', index:'pon', sortable: true, width: 10 * w/100 });
        colM.push({ name:'imp', index:'imp', sortable: true, width: 10 * w/100 });
    }
    else
    {
        colM = [
            'Client',
            'Dossier',
            'Fréquence',
            'Jour',
            '',
            'Ponctuel',
            'Imports'
        ];
    }
    return colM;
}

function save_param()
{
    var frequence = parseInt($('#id_frequence').val()),
        jour = parseInt($('#id_jour').val()),
        a_partir_de = parseInt($('#id_a_partir_de').val()),
        date = $('#id_date_calcul').val().trim(),
        error = false,
        poncutels = [];

    /*if (frequence === -1)
    {
        $('#id_frequence').closest('.form-group').addClass('has-error');
        error = true;
    }
    else $('#id_frequence').closest('.form-group').removeClass('has-error');*/

    if (jour < 1 || jour > 31 || isNaN(jour))
    {
        $('#id_jour').closest('.form-group').addClass('has-error');
        error = true;
    }
    else $('#id_jour').closest('.form-group').removeClass('has-error');

    if (a_partir_de === 3 && date === '')
    {
        $('#id_date_calcul').closest('.form-group').addClass('has-error');
        error = true;
    }
    else $('#id_date_calcul').closest('.form-group').removeClass('has-error');

    if (error) return;

    $('#id_ponctuel').find('.cl_pontuel').each(function(){
        var date = $(this).find('input.form-control').val().trim(),
            active = 1,
            id = $(this).attr('data-id');

        if ($(this).hasClass('hidden') || date === '') active = 0;

        poncutels.push({
            id: id,
            active: active,
            date: date
        });
    });

    $.ajax({
        data: {
            dossier: $('.'+cl_param_edit).attr('id'),
            frequence: frequence,
            jour: jour,
            a_partir_de: a_partir_de,
            date: date,
            action: 1,
            ponctuels: JSON.stringify(poncutels)
        },
        type: 'POST',
        url: Routing.generate('import_export_param_edit'),
        dataType: 'json',
        success: function(data) {
            param_update_row(data);
            close_modal();
        }
    });
}

function hide_date()
{
    var div = $('#id_date_calcul').closest('.form-group');
    if (parseInt($('#id_a_partir_de').val()) === 3) div.removeClass('hidden');
    else div.addClass('hidden');
}

function param_update_row(data)
{
    var id = $('.'+cl_param_edit).attr('id');
    var newData = data;
    newData.id = id;
    $('#id_table_param').jqGrid('setRowData', id, newData);
}
