/**
 * Created by SITRAKA on 06/12/2018.
 */
$(document).ready(function(){
    $(document).on('change','.cl_nature_tiers_sel',function(){
        var id_tiers = parseInt($(this).val());
        $('.'+tr_edited).removeClass(tr_edited);
        $(this).closest('tr').addClass(tr_edited);

        if (id_tiers === -1) show_edit_compte($(this));
        else tiers_compte_change($(this));
    });

    $(document).on('click','.cl_add_tiers',function(){
        addTiers($(this));
    });

    $(document).on('change','input[name="radio-type-tiers"]',function(){
        set_model();
    });
});

function nature_tiers_formatter(v,t)
{
    var select = '<select data-type="'+t+'" class="cl_nature_tiers_sel no-moze" style="border:none;width: 100%">',
        i;
    //tiers
    if (t === 0)
    {
        select += '<option value="-1">Nouveau Tiers</option>';
        select += '<option value="0-0" '+((v === '0-0') ? 'selected' : '')+'></option>';
        for (i = 0; i < tiers.length; i++)
        {
            var tier = tiers[i],
                val = '1-'+tier.id;
            select += '<option value="'+val+'" '+((v === val) ? 'selected' : '')+'>'+(tier.c + ' - ' + tier.i)+'</option>';
        }

        select += $('#id_options_hidden').find('.cl_option_1').html();
    }
    else
        select += '<option value="0"></option>';
    select += '</select>';
    return select;
}

function show_edit_compte()
{
    $.ajax({
        data: {
            releve: $('.'+tr_edited).attr('id'),
            dossier: $('#dossier').val()
        },
        url: Routing.generate('banque_pm_show_edit_tiers'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            show_modal(data,'Creation nouveau tiers');
            set_model();
        }
    });
}

function set_model()
{
    var type_tiers = parseInt($('input[name="radio-type-tiers"]:checked').val());
    $('#id_start_compte').html($('.cl_tiers_'+type_tiers).attr('data-model'));
}

function tiers_compte_change(select)
{
    $.ajax({
        data: {
            releve: $('.'+tr_edited).attr('id'),
            tiers: select.val()
        },
        url: Routing.generate('banque_pm_tiers_change'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            show_info('Succès','Modification bien enregistrée avec succès');
        }
    });
}

function addTiers(span)
{
    var action = parseInt(span.attr('data-type')),
        type = parseInt($('input[name="radio-type-tiers"]:checked').val()),
        compte = $('#id_compte_str').val().trim(),
        intitule = $('#id_intitule').val().trim(),
        releve = $('.'+tr_edited).removeClass(tr_edited).attr('id');

    if (isNaN(type) || type === -1) return;
    if (compte === '')
    {
        $('#id_compte_str').closest('.form-group').addClass('has-error');
        return;
    }
    else $('#id_compte_str').closest('.form-group').removeClass('has-error');

    if (intitule === '')
    {
        $('#id_intitule').closest('.form-group').addClass('has-error');
        return;
    }
    else $('#id_intitule').closest('.form-group').removeClass('has-error');

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            releve: releve,
            action: action,
            type: type,
            compte: compte,
            intitule: intitule
        },
        url: Routing.generate('banque_pm_save_tiers'),
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            close_modal();
            charger_comptes();
        }
    });
}