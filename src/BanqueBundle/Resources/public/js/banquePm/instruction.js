/**
 * Created by SITRAKA on 30/11/2018.
 */

var tr_edited = 'tr_edited';
$(document).ready(function(){
    $(document).on('change','.cl_instruction',function(){
        change_instruction($(this));
    });

    $('#modal').on('hidden.bs.modal', function () {
        update_row();
    });

    $(document).on('change','.cl_observation',function(){
        var observation = $(this).val().trim(),
            releve = $(this).closest('tr').attr('id');
        $.ajax({
            data: {
                releve: releve,
                observation: observation
            },
            url: Routing.generate('banque_pm_observation_save'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_info('SUCCES','Enregistrement bien Enregistrée avec succès');
            }
        });
    });
});

function update_row()
{
    if ($('.' + tr_edited).length <= 0) return;

    var tab_element = null;
    $('#id_tabs').find('.tab-content').find('.tab-pane').each(function(){
        if ($(this).hasClass('active')) tab_element = $(this);
    });
    var type = parseInt(tab_element.attr('data-type')),
        id = $('.' + tr_edited).attr('id');
    $.ajax({
        data: {
            releve: id,
            dossier: $('#dossier').val(),
            exercice: $('#exercice').val(),
            banque: $('#js_banque').val(),
            banque_compte: $('#js_banque_compte').val(),
            type: type
        },
        url: Routing.generate('banque_pm_tr_edited'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var newData = $.parseJSON(data),
                table_selected = $('#table_pm_'+type);
            newData.id = id;
            table_selected.jqGrid('setRowData', id, newData);
            //close_modal();
            show_info('SUCCES','Enregistrement bien Enregistrée avec succès');
        }
    });
}

function change_instruction(select)
{
    $('.'+tr_edited).removeClass(tr_edited);
    select.closest('tr').addClass(tr_edited);
    var instruction = parseInt(select.val());

    if ([0,1,4].in_array(instruction))
    {
        $.ajax({
            data: {
                releve: select.closest('tr').attr('id'),
                instruction: instruction
            },
            url: Routing.generate('banque_pm_imputation'),
            type: 'POST',
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                show_info('Succès','Modification enregistrée avec succès');
                /*show_modal(data,'imputation',undefined,'modal-lg');
                set_compte_select();*/
            }
        });
    }
    else
    {
        show_imputation_directe(0,{releve:select.closest('tr').attr('id')});
    }
}

function decision_formatter(value)
{
    //if (value.c.trim() !== '') return '<span class="text-info">'+value.c+'</span>';
    var v = parseInt(value);
    /*var decisions =
        [
            'Laisser en l\'état',
            'Passer l\'ecriture sans la pièce avec déduction TVA',
            'Passer l\'ecriture sans la pièce en TTC',
            'Pièce à demander au client'
        ];


    return '<span class="">'+decisions[v]+'</span>';*/
    return '' +
        '<select class="cl_instruction no-moze" style="width: 100%; border: none">' +
        '<option value="0" '+(v === 0 ? 'selected' : '')+'></option>' +
        '<option value="1" '+(v === 1 ? 'selected' : '')+'>Laisser en l\'état</option>' +
        '<option value="2" '+(v === 2 ? 'selected' : '')+'>Passer l\'ecriture sans la pièce avec déduction TVA</option>' +
        '<option value="3" '+(v === 3 ? 'selected' : '')+'>Passer l\'ecriture sans la pièce en TTC</option>' +
        '<option value="4" '+(v === 4 ? 'selected' : '')+'>Pièce à demander au client</option>' +
        '</select>';
}

function observation_formatter(v)
{
    return '<input type="text" class="cl_observation input-in-jqgrid" value="'+v+'">';
}
