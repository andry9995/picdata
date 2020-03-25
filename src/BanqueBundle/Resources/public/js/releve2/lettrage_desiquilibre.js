/**
 * Created by SITRAKA on 28/08/2019.
 */

var cl_r_desi = 'releve_desequilibre';
$(document).ready(function(){
    $(document).on('click','.cl_desiquilibre',function(){
        $('.' + class_tr_edited).removeClass(class_tr_edited);
        $(this).closest('tr').addClass(class_tr_edited);

        $('.'+cl_r_desi).removeClass(cl_r_desi);
        var id = $(this).closest('tr').addClass(cl_r_desi).attr('id');

        $.ajax({
            data: {
                releve: id
            },
            url: Routing.generate('banque2_show_lettrage_desequilibre'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);

                if (parseInt(data) === -1)
                {
                    show_info('erreur','parametrer le journal dossier pour le numero de compte');
                    $('#id_show_param_import').click();
                    return;
                }

                show_modal(data,'lettrage',undefined,'modal-lg');
                calcul_equilibrage();
            }
        });
    });

    $(document).on('click','.cl_action_in_desiquilibre',function(){
        var action = parseInt($(this).attr('data-action')),
            type = parseInt($(this).closest('tr').attr('data-type')),
            id_image = $(this).closest('tr').attr('data-image_id');

        if (action === 0)
        {
            if (type === 0) $(this).closest('tr').addClass('hidden');
            else $('.cl_image_sel_' + id_image).addClass('hidden');
        }
        else
        {
            $('.a_deplacer').removeClass('a_deplacer');
            if (type === 0)
            {
                $(this).closest('tr').addClass('a_deplacer').find('.cl_td_btn').html(
                    '<span class="btn btn-white btn-xs cl_action_in_desiquilibre" data-action="0">' +
                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                    '</span>'
                );
            }
            else
            {
                $(this).closest('tbody').find('.cl_image_sel_' + id_image).each(function(){
                    $(this).addClass('a_deplacer').find('.cl_td_btn').html(
                        '<span class="btn btn-white btn-xs cl_action_in_desiquilibre" data-action="0">' +
                        '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                        '</span>'
                    );
                });
            }

            $('.a_deplacer').each(function(){
                $('#id-table-desiquilibre').find('tbody').append($(this));
            });
        }

        calcul_equilibrage();
    });

    $(document).on('click','#id_lanch_search',function(){
        var type = parseInt($('#id_recherche_type').val().trim()),
            image = $('#id_recherche_piece').val().trim(),
            montant = parseFloat($('#id_recherche_montant').val().trim());

        if (isNaN(montant)) montant = 0;
        if (image === '' && montant === 0)
        {
            show_info('Erreur','L image vide OU/ET montant invalide','error');
            return;
        }

        $.ajax({
            data: {
                type: type,
                image: image,
                montant: montant,
                dossier: $('#dossier').val(),
                releve: $('#id-image_flague').attr('data-releve')
            },
            url: Routing.generate('banque2_lettrage_search_by_image_montant'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                $('#id_container_table_desiquilibre_new').html(data);
            }
        });
    });

    $(document).on('click','#id_equilibre_lettrage',function(){
        var solde = parseFloat($('#id_controle_solde').attr('data-solde')),
            a_equilibres = [];

        if (solde !== 0)
        {
            show_info('Lettrage désiquilibré','error');
            return;
        }

        $('#id-table-desiquilibre').find('tbody').find('tr').each(function(){
            if (!$(this).hasClass('hidden')) a_equilibres.push({
                id: $(this).attr('data-id'),
                type: $(this).attr('data-type')
            });
        });

        $.ajax({
            data: {
                image_flague: $('#id-image_flague').val().trim(),
                items: JSON.stringify(a_equilibres)
            },
            url: Routing.generate('banque2_lettrage_equilibrer'),
            type: 'POST',
            dataType: 'html',
            success: function(data){
                test_security(data);
                if (parseInt(data) === 1)
                {
                    show_info('Succes','Modification bien enregistrée');
                    close_modal();
                    update_row();
                }
            }
        });
    });
});

function calcul_equilibrage()
{
    var solde = 0;
    $('#id-table-desiquilibre').find('tbody').find('tr').each(function(){
        if (!$(this).hasClass('hidden'))
        {
            solde += parseFloat($(this).find('.cl_debit').attr('data-m')) - parseFloat($(this).find('.cl_credit').attr('data-m'));
        }
    });

    $('#id_controle_solde').attr('data-solde',solde).text('Solde = ' + number_format(solde,2,',',' ',false));
    return true;
}