/**
 * Created by SITRAKA on 19/07/2019.
 */

var cl_compte_change = 'compte-changed';
$(document).ready(function(){
    $(document).on('click','.cl_imp_banque_type',function(){
        $(this).closest('ul').find('.cl_imp_banque_type').removeClass('active');
        $(this).addClass('active');
        $('#id_banque_libelle').text($(this).text().trim());

        charger_trs();
    });

    $(document).on('click','.cl_save_imputation_directe',function(){
        var banque_type = $('#id_banque_type').find('.cl_imp_banque_type.active').attr('data-id'),
            montant_ttc = parseFloat($('#js_id_m').attr('data-m')),
            total_montant = 0,
            imputations = [];

        $('#id_trs').find('tr').each(function(){
            var compte = null,
                montant = parseFloat($(this).find('.cl_montant_imputation').val().trim().replace(/\s/g,'')),
                type = 0,
                type_compte = 0;
            $(this).find('.cl_imputation_compte').each(function(){
                if ($(this).find('option:selected').text().trim() !== '')
                {
                    compte = $(this).val();
                    type = parseInt($(this).find('option:selected').attr('data-type'));
                    type_compte = parseInt($(this).attr('data-type_compte'));
                }
            });

            if (compte !== null && !isNaN(montant) && montant !== 0)
            {
                total_montant += montant;
                imputations.push({ m:montant, c:compte, t:type, type_compte:type_compte });
            }
        });

        if (imputations.length > 0 && (total_montant + montant_ttc !== 0))
        {
            show_info('Erreur','Imputation déséquilibrée','error');
            return;
        }

        $.ajax({
            data: {
                releve: $('#js_releve_selected').attr('data-id'),
                banque_type: banque_type,
                imputations: JSON.stringify(imputations)
            },
            type: 'POST',
            url: Routing.generate('imputation_directe_save'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#id_test').html(data);
            }
        });
    });

    $(document).on('change','.cl_imputation_compte',function(){
        $('.'+cl_compte_change).removeClass(cl_compte_change);
        $(this).addClass(cl_compte_change);

        $(this).closest('tr').find('.cl_imputation_compte').each(function(){
            if (!$(this).hasClass(cl_compte_change))
            {
                $(this).find('option:first').prop('selected', true);
                $(this).find('option:first').attr('selected','selected');
            }
        });
    });
});

function show_imputation_directe(type,adds)
{
    type = typeof type !== 'undefined' ? type : -1;
    adds = typeof adds !== 'undefined' ? adds : null;

    $.ajax({
        data: {
            type: type,
            adds: JSON.stringify(adds)
        },
        type: 'POST',
        url: Routing.generate('imputation_directe'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Imputation',undefined,'modal-lg');
            charger_trs();
        }
    });
}


function charger_trs()
{
    var releve = $('#id_datas').attr('data-releve'),
        banque_type = $('#id_banque_type').find('.cl_imp_banque_type.active');

    $.ajax({
        data: {
            releve: releve,
            banque_type: banque_type.attr('data-id'),
            dossier: $('#dossier').val()
        },
        type: 'POST',
        url: Routing.generate('imputation_directe_tr'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#id_trs').html(data);
        }
    });
}