/**
 * Created by SITRAKA on 05/12/2019.
 */

$(document).ready(function(){
    $(document).on('click','#id_show_param_import',function(){
        show_mode_import_param();
    });

    $(document).on('click','.cl_edit_row_mode_import',function(){
        change_row_to_editable($(this));
    });

    $(document).on('change','.cl_mode',function(){
        var type = $(this).hasClass('cl_mode_pcc') ? 0 : 1,
            banque_compte = $(this).closest('tr').attr('data-id'),
            val = $(this).val();

        if ($(this).hasClass('cl_mode_journal_dossier'))
            type = 2;

        $.ajax({
            data: {
                banque_compte: banque_compte,
                type: type,
                val: val
            },
            type: 'POST',
            url: Routing.generate('banque_import_param_save'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_info('Succès','Modification bien enregistrée avec succès');
            }
        });
    });

    $(document).on('click','.cl_mode',function(e){
        e.stopPropagation();
    });

    $(document).on('click','#id_banque_compte_add',function(){
        var compte = $('#id_banque_compte_num').val().trim().replace(/\s/g, "");

        if (compte === '' && compte.length < 7)
        {
            show_info('Erreur','Vérifier le numéro de compte','error');
            return;
        }

        $.ajax({
            data: {
                compte: compte,
                dossier: $('#dossier').val()
            },
            type: 'POST',
            url: Routing.generate('banque_import_param_save_bc'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                if (parseInt(data) === -1) show_info('Erreur','Ce N° de compte existe déja', 'error');
                else if (parseInt(data) === -2) show_info('Erreur code Banque','Les 5 premiers chiffres doivent être le CODE de la BANQUE','error');
                else
                {
                    $('#id_tbody_bc').append(data);
                }
            }
        });
    });
});

function show_mode_import_param()
{
    var dossier_nom = $('#dossier option:selected').text().trim().toUpperCase();

    if (dossier_nom === '' || dossier_nom === 'TOUS')
    {
        show_info('Erreur','Choisir le dossier','error');
        return;
    }

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val()
        },
        type: 'POST',
        url: Routing.generate('banque_import_param_show'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data, 'Paramètrage',undefined,'modal-lg');
        }
    });
}

function change_row_to_editable(row)
{
    var sel_pcc = $('#id_sel_pcc_banque'),
        sel_mode = $('#id_sel_mode_saisie'),
        sel_j = $('#id_sel_jnl_dossier'),
        id = 0,
        texte = '';

    if (sel_pcc.length > 0)
    {
        id = sel_pcc.val();
        texte = sel_pcc.find('option:selected').text();
        sel_pcc.closest('td').attr('data-id',id).html(texte);
    }
    if (sel_mode.length > 0)
    {
        id = sel_mode.val();
        texte = sel_mode.find('option:selected').text();
        sel_mode.closest('td').attr('data-type',id).html(texte);
    }
    if (sel_j.length > 0)
    {
        id = sel_j.val();
        texte = sel_j.find('option:selected').text();
        sel_j.closest('td').attr('data-jd', id).html(texte);
    }

    $.ajax({
        data: {
            banque_compte: row.attr('data-id')
        },
        type: 'POST',
        url: Routing.generate('banque_import_param_bc'),
        dataType: 'json',
        success: function(data) {
            //test_security(data);
            var options = '',
                options_jds = '';
            $('#id_hidden_pcc_banque').find('option').each(function(){
                if (!data.a_enlever.in_array(parseInt($(this).attr('value')))) options += '<option value="'+$(this).attr('value')+'">'+$(this).text()+'</option>';
            });

            $('#id_hidden_journal_dossier').find('option').each(function(){
                if (!data.aEnleverJds.in_array(parseInt($(this).attr('value')))) options_jds += '<option value="'+$(this).attr('value')+'">'+$(this).text()+'</option>';
            });

            row.find('.cl_mode_pcc_container').html('<select id="id_sel_pcc_banque" class="cl_mode cl_mode_pcc input-in-jqgrid"></select>');
            row.find('.cl_mode_pcc_container').find('select').html(options);

            row.find('.cl_mode_jnl_container').html('<select id="id_sel_jnl_dossier" class="cl_mode cl_mode_journal_dossier input-in-jqgrid"></select>');
            row.find('.cl_mode_jnl_container').find('select').html(options_jds);

            row.find('.cl_mode_saisie_container').html('<select id="id_sel_mode_saisie" class="cl_mode input-in-jqgrid"></select>');
            row.find('.cl_mode_saisie_container').find('select').html($('#id_hidden_mode_saisie').html());

            $('#id_sel_pcc_banque').val(data.pcc);
            $('#id_sel_jnl_dossier').val(data.jd);
            $('#id_sel_mode_saisie').val(data.mi);
        }
    });
}