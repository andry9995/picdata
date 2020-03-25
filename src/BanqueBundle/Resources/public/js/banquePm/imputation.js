/**
 * Created by SITRAKA on 04/12/2018.
 */
$(document).ready(function(){
    $(document).on('change','input[name="radio-type-compta"]',function(){
        set_compte_select();
    });

    $(document).on('change','.cl_type_in_tr',function(){
        charger_option_tr($(this).closest('tr'));
    });

    $(document).on('click','#id_valider_imputation',function(){
        save_imputation_manuel();
    });

    $(document).on('click','.cl_remove_imput',function(){
        var tr = $(this).closest('tr'),
            index = parseInt(tr.attr('data-index'));

        tr.find('input[name="radio-type-'+index+'"]:checked').prop('checked',false);
        tr.find('.cl_montant').val('0');
        tr.find('.cl_select_compte').val('0-0');
    });
});

function set_compte_select()
{
    var methode = parseInt($('input[name="radio-type-compta"]:checked').val());
    $('#id_tbody_imputation div.radio').each(function(){
        if (parseInt($(this).attr('data-type')) === methode) $(this).removeClass('hidden');
        else
        {
            if ($(this).find('input[type="radio"]').is(':checked')) $(this).find('input[type="radio"]').prop('checked',false);
            $(this).addClass('hidden');
        }
    });
    charger_option_imputation();
}

function charger_option_imputation()
{
    $('#id_tbody_imputation tr').each(function(){
        charger_option_tr($(this));
    });
}

function charger_option_tr(tr)
{
    var index = parseInt(tr.attr('data-index')),
        radio_checked = parseInt(tr.find('input[name="radio-type-'+index+'"]:checked').val());

    if (!isNaN(radio_checked))
        tr
            .find('.cl_select_compte')
            .html($('#id_options_hidden').find('.cl_option_' + radio_checked).html())
            .val(tr.attr('data-id_compte'));
    else
        tr
            .find('.cl_select_compte').empty();
}

function save_imputation_manuel()
{
    var montant = 0,
        montant_releve = number_fr_to_float($('#js_id_m').text()),
        releve_details = [],
        has_error = false;

    $('#id_tbody_imputation').find('tr').each(function(){
        var index = parseInt($(this).attr('data-index')),
            radio_checked = parseInt($(this).find('input[name="radio-type-'+index+'"]:checked').val()),
            m = parseFloat($(this).find('.cl_montant').val());

        if (!isNaN(radio_checked))
        {
            if (isNaN(m))
            {
                $(this).addClass('has-error');
                has_error = true;
            }
            else
            {
                montant += m;
                $(this).removeClass('has-error');

                var compte = $(this).find('.cl_select_compte').val(),
                    releve_detail = $(this).attr('data-id_releve_detail');

                if (compte === '0' || compte === '0-0')
                {
                    $(this).addClass('has-error');
                    has_error = true;
                }

                releve_details.push({ compte_id: compte, releve_detail_id: releve_detail, type:radio_checked, montant:m });
            }
        }
    });

    if (releve_details.length !== 0 && (montant !== montant_releve || has_error))
    {
        show_info('Erreur','Votre imputation est déséquilibrée OU compte non selectionné','error');
        return;
    }

    $.ajax({
        data: { releve:$('#js_releve_selected').attr('data-id'), releve_details: JSON.stringify(releve_details) },
        type: 'POST',
        url: Routing.generate('banque2_releve_compte_save'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            close_modal();
        }
    });
}

function number_fr_to_float(s)
{
    return parseFloat(s.replace(/&nbsp;/gi, '').replace(/ /g,"").replace(/,/,'.'));
}