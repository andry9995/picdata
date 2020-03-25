/**
 * Created by SITRAKA on 17/06/2019.
 */

var can_restart = false,
    index_sens = 0,
    index_pas_piece;

$(document).ready(function(){
    $(document).on('click','#id_show_exception',function(){
        var dossier_text = $('#dossier option:selected').text().trim().toUpperCase();

        if (dossier_text === '' || dossier_text === 'TOUS')
        {
            $('#dossier').closest('.form-group').addClass('has-error');
            return;
        }

        $('#dossier').closest('.form-group').removeClass('has-error');

        $.ajax({
            data: { },
            url: Routing.generate('banque_pm_exceptions_container'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_modal(data,'Paramètrages Clés/PM',undefined,'modal-lg');
                $.ajax({
                    data: { dossier: $('#dossier').val() },
                    url: Routing.generate('banque_pm_exceptions'),
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        var table_selected = $('#id_tb_exception_cles'),
                            w = table_selected.parent().width(),
                            h = $(window).height() - 300;

                        jQuery('#id_tb_exception_cles').jqGrid({
                            data: data,
                            datatype: 'local',
                            height: h,
                            width: w,
                            rowNum: 10000000,
                            rowList: [10,20,30],
                            colNames: model_cle_exception(),
                            colModel: model_cle_exception(w),
                            viewrecords: true,
                            footerrow: true,
                            userDataOnFooter: true,
                            userData: { /*'db': tot_debit, 'cr': tot_credit*/ }
                        });
                    }
                });
            }
        });
    });

    $(document).on('click','#id_tb_exception_cles .cl_cle',function(){
        charger_exceptions($(this));
    });

    $('#modal').on('hidden.bs.modal', function(){
        if (can_restart) go();
    }).on('show.bs.modal', function(){
        can_restart = ($('#id_container_exceptions').length > 0 || $('#id_releve_manquants').length > 0);
    });

    $(document).on('change','.cl_input_exception',function(){
        var tr = $(this).closest('tr'),
            cle_dossier = tr.attr('id'),
            pas_piece = tr.find('.cl_pas_piece').is(':checked') ? 1 : 0,
            mot_cle = tr.find('.cl_mot_cle').val().trim(),
            debit = tr.find('.s1').find('.cl_debit').is(':checked'),
            credit = tr.find('.s1').find('.cl_credit').is(':checked'),
            debit_2 = tr.find('.s2').find('.cl_debit').is(':checked'),
            credit_2 = tr.find('.s2').find('.cl_credit').is(':checked'),
            sens = 0,
            sens_2 = 0,
            formule = tr.find('.f1').find('.cl_formule').val().trim(),
            formule_2 = tr.find('.f2').find('.cl_formule').val().trim();

        if (debit && !credit) sens = 1;
        else if (!debit && credit) sens = 2;

        if (debit_2 && !credit_2) sens_2 = 1;
        else if (!debit_2 && credit_2) sens_2 = 2;

        console.log(mot_cle);

        $.ajax({
            data: {
                cle_dossier: cle_dossier,
                pas_piece: pas_piece,
                mot_cle: mot_cle,
                sens: sens,
                formule: formule,
                sens_2: sens_2,
                formule_2: formule_2
            },
            url: Routing.generate('banque_pm_exception_save'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                $('#ex_test').html(data);
            }
        });
    });
});

function charger_exceptions(el)
{
    var cle_dossier = el.closest('tr').attr('id'),
        html = '<table id="id_exception_params"></table>';
    $('#id_container_exceptions').html(html);

    $.ajax({
        data: { cle_dossier: cle_dossier },
        url: Routing.generate('banque_pm_exception_params'),
        type: 'POST',
        dataType: 'json',
        success: function(data){
            var table_selected = $('#id_exception_params'),
                w = table_selected.parent().width(),
                h = $(window).height() - 300;

            jQuery('#id_exception_params').jqGrid({
                data: data,
                datatype: 'local',
                height: h,
                width: w,
                rowNum: 10000000,
                rowList: [10,20,30],
                colNames: ['Inférieur à','Sens'],
                colModel: [
                    { name:'min', index:'min', sortable:true, width: 100 * w/100, formatter:function(v){ return limit_formatter(v) } },
                    { name:'sens', index:'sens', hidden:true, sortable:true, width: 100 * w/100, formatter:function(v){ return sens_formatter(v) } },
                    /*{ name:'x', index:'x', sortable:true, width: 100 * w/100 }*/
                ],
                viewrecords: true,
                footerrow: true,
                userDataOnFooter: true,
                userData: { /*'db': tot_debit, 'cr': tot_credit*/ }
            });
        }
    });
}

function model_cle_exception(w)
{
    var colM = [];
    if(typeof w !== 'undefined')
    {
        colM.push({ name:'cle', index:'cle', sortable:true, width: 20 * w/100, classes:'' });
        colM.push({ name:'pp', index:'pp', sortable:true, width: 10 * w/100, classes:'', align:'center', formatter:function(v){ return pas_piece_formatter(v) } });
        colM.push({ name:'mc', index:'mc', sortable:true, width: 10 * w/100, classes:'', formatter:function(v){ return mot_cle_formatter(v) } });
        colM.push({ name:'s', index:'s', sortable:true, width: 13 * w/100, classes:'s1', formatter:function(v){ return sens_formatter(v) } });
        colM.push({ name:'f', index:'f', sortable:true, width: 16 * w/100, classes:'f1', formatter:function(v){ return formule_formatter(v) } });
        colM.push({ name:'s2', index:'s2', sortable:true, width: 13 * w/100, classes:'s2', formatter:function(v){ return sens_formatter(v) } });
        colM.push({ name:'f2', index:'f2', sortable:true, width: 16 * w/100, classes:'f2', formatter:function(v){ return formule_formatter(v) } });
    }
    else
    {
        colM.push('Clé');
        colM.push('Pas de pièce');
        colM.push('Mot Clé');
        colM.push('Sens');
        colM.push('Formule');
        colM.push('Sens');
        colM.push('Formule');
    }

    return colM;
}

function pas_piece_formatter(v)
{
    return '' +
        '<div class="checkbox checkbox-inline">' +
            '<input type="checkbox" class="cl_input_exception cl_pas_piece" id="id_pp_'+index_pas_piece+'" '+((v === 1) ? 'checked' : '')+'>' +
            '<label for="id_pp_'+index_pas_piece+'"></label>' +
        '</div>';
}

function formule_formatter(v)
{
    return '<input type="text" class="cl_input_exception input-in-jqgrid cl_formule" placeholder="" value="'+v+'">';
}

function sens_formatter(v)
{
    v = parseInt(v);
    index_sens++;
    return '' +
        '<div class="checkbox checkbox-success checkbox-inline">' +
            '<input type="checkbox" class="cl_input_exception cl_debit" id="id_d_'+index_sens+'" '+(([0,1].in_array(v)) ? 'checked' : '')+'>' +
            '<label for="id_d_'+index_sens+'">D</label>' +
        '</div>' +
        '<div class="checkbox checkbox-danger checkbox-inline">' +
            '<input type="checkbox" class="cl_input_exception cl_credit" id="id_c_'+index_sens+'" '+(([0,2].in_array(v)) ? 'checked' : '')+'>' +
            '<label for="id_c_'+index_sens+'">C</label>' +
        '</div>';
}

function mot_cle_formatter(v)
{
    return '<input type="text" class="cl_input_exception input-in-jqgrid cl_mot_cle" placeholder="" value="'+v+'">';
}
