/**
 * Created by SITRAKA on 01/02/2019.
 */
$(document).ready(function(){
    $(document).on('change','#id_pcc_banque_compte',function(){
        $.ajax({
            data: {
                pcc: $('#id_pcc_banque_compte').val(),
                banque_compte: $('#js_banque_compte').val()
            },
            type: 'POST',
            url: Routing.generate('jnl_bq_compte_comptable_edit'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                if (parseInt(data) === 0)
                {
                    $('#id_pcc_banque_compte').closest('.form-group').addClass('has-error');
                    show_info('Compte Comptable','Associé d abord le Compte Banque à un compte 512xxx','error');
                    vider_table();
                    return;
                }
                go();
            }
        });
    });

    $(document).on('change','#id_pcc_journal_dossier',function(){
        $.ajax({
            data: {
                journal_dossier: $('#id_pcc_journal_dossier').val(),
                banque_compte: $('#js_banque_compte').val()
            },
            type: 'POST',
            url: Routing.generate('jnl_bq_journal_dossier_edit'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                if (parseInt(data) === 0)
                {
                    $('#id_pcc_journal_dossier').closest('.form-group').addClass('has-error');
                    show_info('Compte Comptable','Associé d abord le Compte Banque à un CODE JOURNAL','error');
                    vider_table();
                    return;
                }
                go();
            }
        });
    });
});

function charger_compte_comptable()
{
    var bc_element = $('#js_banque_compte'),
        bc_text = bc_element.find('option:selected').text().trim().toUpperCase();
    $('#id_pcc_admin').empty();

    if (bc_text !== '' && bc_text !== 'TOUS')
    {
        $.ajax({
            data: { banque_compte: bc_element.val() },
            type: 'POST',
            url: Routing.generate('jnl_bq_compte_comptable'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#id_container_pcc_bc').html(data);
                var pcc_text = $('#id_pcc_banque_compte option:selected').text().trim().toUpperCase();
                if (pcc_text === '' && pcc_text === 'TOUS') $('#id_pcc_banque_compte').closest('.form-group').addClass('has-error');
                else $('#id_pcc_banque_compte').closest('.form-group').removeClass('has-error');
            }
        });
    }
}
