/**
 * Created by SITRAKA on 25/04/2019.
 */
var can_restart = false;

$(document).ready(function(){
    $(document).on('click','.cl_cle_admin',function(){
        $.ajax({
            data: { },
            type: 'POST',
            url: Routing.generate('ind_cles'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                can_restart = true;
                show_modal(data,'');
            }
        });
    });

    $(document).on('click','#id_add_cle',function(){
        edit_cle($(this));
    });

    $(document).on('change','.cl_edit_cle',function(){
        edit_cle($(this));
    });

    $(document).on('change','.cl_sens',function(){
        edit_cle($(this));
    });

    $(document).on('click','.cl_delete_cle',function(){
        edit_cle($(this));
    });

    $('#modal').on('hidden.bs.modal', function(e){
        e.preventDefault();
        //if (can_restart && parseInt($('#id_tb_type').val()) === 1) go();
        can_restart = false;
    });

    $(document).on('click','#js_id_table td.cl_liste_occurence',function(){
        var dossier = $(this).closest('tr').attr('id'),
            exercice = parseInt($(this).closest('tr').find('.exo').text().trim()),
            occurence = $(this).text().trim();

        if (occurence === '') return;

        $.ajax({
            data: {
                dossier: dossier,
                exercice: exercice
            },
            type: 'POST',
            url: Routing.generate('ind_cle_occurence_details'),
            dataType: 'json',
            success: function(data) {
                show_modal('<table id="id_table_occurence_detail"></table>','Détails',undefined,'modal-lg');

                var editurl = 'test.php',
                    table_selected = $('#id_table_occurence_detail'),
                    w = table_selected.parent().width(),
                    h = $(window).height() - 250;
                set_table_jqgrid(data,h,table_get_col_model_occurence(),table_get_col_model_occurence(w),table_selected,'hidden',w,editurl,true,undefined,undefined,undefined,undefined,undefined,false,undefined,false,true);
            }
        });
    });
});

function edit_cle(el)
{
    var id = $('#js_zero_boost').val(),
        cle = $('#id_cle').val().trim(),
        action = 0,
        sens = 0;

    if (!el.hasClass('cl_add'))
    {
        id = el.closest('tr').attr('data-id');
        cle = el.closest('tr').find('.cl_edit_cle').val().trim();
        action = parseInt(el.attr('data-action'));

        var is_debit = el.closest('tr').find('.cl_is_debit').is(':checked'),
            is_credit = el.closest('tr').find('.cl_is_credit').is(':checked');

        if (is_debit && is_credit) sens = 0;
        else if (is_debit) sens = 1;
        else if (is_credit) sens = 2;
    }

    if (cle === '' && action === 0)
    {
        el.closest('.form-group').addClass('has-error');
        return;
    }
    else el.closest('.form-group').removeClass('has-error');

    $.ajax({
        data: {
            id: id,
            cle: cle,
            action: action,
            sens: sens
        },
        type: 'POST',
        url: Routing.generate('ind_cle_save'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            if (parseInt(data) === -1)
            {
                show_info('Erreur','Clé déja existante','error');
                return;
            }

            if (id === $('#js_zero_boost').val())
            {
                $(data).insertAfter($('#id_table_cles').find('.first-tr'));
                $('#id_cle').val('');
            }
            else if (action === 1)
            {
                el.closest('tr').remove();
            }
            show_info('Succès','Modification bien enregistrée');
        }
    });
}

function table_get_col_model_occurence(w)
{
    var colModel1 = [];
    if(typeof w !== 'undefined')
    {
        colModel1.push({ name:'dat', index:'dat', sortable:true, width:  10*w / 100 });
        colModel1.push({ name:'lib', index:'lib', sortable:true, width:  55*w / 100 });
        colModel1.push({ name:'deb', index:'deb', sortable:true, width:  10*w / 100, align:'right', formatter: function(v) { return number_format(v,2,',',' ',true) } });
        colModel1.push({ name:'cre', index:'cre', sortable:true, width:  10*w / 100, align:'right', formatter: function(v) { return number_format(v,2,',',' ',true) }, classes:'text-danger' });
        colModel1.push({ name:'cle', index:'cle', sortable:true, width:  10*w / 100 });
    }
    else
    {
        colModel1.push('Date');
        colModel1.push('Libellé');
        colModel1.push('Débit');
        colModel1.push('Crédit');
        colModel1.push('Clé');
    }
    return colModel1;
}
