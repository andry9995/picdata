/**
 * Created by SITRAKA on 12/04/2019.
 */
var modal_param = 0;

$(document).ready(function(){
    $(document).on('click','#id_show_params',function(){
        show_params();
    });

    $(document).on('click','.cl_add_param',function(){
        save_param($(this));
    });

    $(document).on('change','.cl_edit',function(){
        save_param($(this));
    });

    $('#modal').on('hidden.bs.modal', function(){
        if (parseInt(modal_param) === 1) charger_banque();
    }).on('show.bs.modal', function(){
        modal_param = ($('#modal-param').length > 0) ? 1 : 0;
    });
});

function show_params()
{
    var dossier_text = $('#dossier option:selected').text().trim().toUpperCase();

    if (dossier_text === '' || dossier_text === 'TOUS')
    {
        show_info('Error','Choisir le dossier','error');
        return;
    }

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            action: 0
        },
        type: 'POST',
        url: Routing.generate('jnl_bq_params'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            show_modal(data,'Parametrage Compte/JNL',undefined,'modal-lg');
        }
    });
}

function save_param(el)
{
    var action = parseInt(el.attr('data-action')),
        type = parseInt(el.closest('.ibox').attr('data-type')),
        entity = $('#js_zero_boost').val(),
        code = '',
        libelle = '';

    if (action === 1)
    {
        code = el.closest('.ibox').find('.cl_add.cl_compte').val().trim();
        libelle = el.closest('.ibox').find('.cl_add.cl_intitule').val().trim();
    }
    else if (action === 2)
    {
        code = el.closest('tr').find('.cl_compte').val().trim();
        libelle = el.closest('tr').find('.cl_intitule').val().trim();
        entity = el.closest('tr').attr('data-id');
    }

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            action: action,
            type: type,
            entity: entity,
            code: code,
            libelle: libelle
        },
        type: 'POST',
        url: Routing.generate('jnl_bq_params'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var res = parseInt(data);

            if (res === -1)
            {
                var message = '';
                if (type === 0) message = 'Ce compte éxiste déja';
                else message = 'Ce journal éxiste déja';

                show_info('Erreur',message,'error');
            }
            else if (res === 1)
            {
                show_info('Success','Modification bien enregistrée');
            }
            else
            {
                if (el.closest('.ibox').find('table.table tbody').length > 0)
                    el.closest('.ibox').find('table.table tbody').prepend(data);
                else
                    el.closest('.ibox').find('table.table').prepend(data);
            }
        }
    });
}