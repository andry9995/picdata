/**
 * Created by SITRAKA on 02/05/2019.
 */
var modal_param = 0;

$(document).ready(function(){
    $(document).on('click','#id_show_param_rel_compl',function(){
        var dossier_text = $('#dossier option:selected').text().trim().toUpperCase();
        if (dossier_text === '' || dossier_text === 'TOUS')
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
            url: Routing.generate('banque_info_compl_params'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                show_modal(data,'Paramètrage Infos Complémentaires',undefined,'modal-lg');
                $('#id_row_tables').find('.ibox-content').height($(window).height() - 340);
                charger_info_compls();
            }
        });
    });

    $(document).on('click','#id_table_banque tr',function(){
        if ($(this).hasClass('active')) return;
        $(this).closest('table').find('tr.active').removeClass('active');
        $(this).addClass('active');
        charger_info_compls();
    });

    $(document).on('click','#id_save_cfonb_banque',function(){
        save_cfonb_banque();
    });

    $('#modal').on('hidden.bs.modal', function(){
        if (parseInt(modal_param) === 1) go();
    }).on('show.bs.modal', function(){
        modal_param = 0;
        if ($('#id_row_tables').length > 0) modal_param = 1;
    });
});

function charger_info_compls()
{
    $('#id_cfonb').empty();
    var id = $('#id_table_banque').find('tr.active').attr('data-id');
    if (typeof id === 'undefined') return;

    $.ajax({
        data: {
            banque: id
        },
        type: 'POST',
        url: Routing.generate('banque_info_compl_cfonb'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#id_cfonb').html(data);
        }
    });
}

function save_cfonb_banque()
{
    var id = $('#id_table_banque').find('tr.active').attr('data-id');
    if (typeof id === 'undefined') return;

    var cfonbs = [];
    $('#id_table_cfonb').find('.cl_chk_cfon_code').each(function(){
        var tr = $(this).closest('tr');
        cfonbs.push({
            cfonb_code: tr.attr('data-id'),
            cfonb_banque: tr.attr('data-id_cfond_banque'),
            etat: $(this).is(':checked') ? 1 : 0
        });
    });

    $.ajax({
        data: {
            banque: id,
            cfonbs: JSON.stringify(cfonbs)
        },
        type: 'POST',
        url: Routing.generate('banque_cfonb_releve_save'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_info('Succès','Modification bien enregistrée avec succès');
        }
    });
}
