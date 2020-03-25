/**
 * Created by INFO on 26/10/2017.
 */
var selectBanque = 0;
var releveGrid = $('#js_controle_releve_liste');
var banqueGrid = $('#js_banque_liste');

dossier_depend_exercice = true;

$(document).ready(function(){

    set_current_exercice();
    charger_site();

    $(document).on('change', '#client', function(){
        releveGrid.jqGrid('clearGridData');
    });

    $(document).on('change','#dossier',function(){
        charger_banque();
        releveGrid.jqGrid('clearGridData');
    });

    $(document).on('change','#js_banque',function(){
        charger_banque_compte();

    });

    $(document).on('change','#js_banque_compte',function(){
        goControleReleve();
    });


    $(document).on('change','#exercice',function(){
        // goControleReleve();
    });

});


function charger_banque()
{
    if($('#dossier option:selected').text().trim() == '')
    {
        show_info('NOTICE','Choisir le Dossier','error');
        $('#dossier').closest('.form-group').addClass('has-error');
        $('#js_id_conteneur_banque').html(
            '        <div class="form-horizontal">' +
            '            <div class="form-group">' +
            '                <label class="control-label col-lg-2" for="js_banque">' +
            '                    <span>Bq</span>' +
            '                    <span class="label label-warning">0</span>' +
            '                </label>' +
            '                <div class="col-lg-10">' +
            '                    <select class="form-control disabled" id="js_banque">' +
            '                        <option value="{{ 0|boost }}"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );
        $('#js_id_conteneur_compte').html(
            '        <div class="form-horizontal">' +
            '            <div class="form-group">' +
            '                <label class="control-label col-lg-2" for="js_banque_compte">' +
            '                    <span>Compte</span>' +
            '                    <span class="label label-warning">0</span>' +
            '                </label>' +
            '                <div class="col-lg-10">' +
            '                    <select class="form-control disabled" id="js_banque_compte">' +
            '                        <option value="{{ 0|boost }}"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque_dossier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_conteneur_banque').html(data);
            charger_banque_compte();
        }
    });








}

function charger_banque_compte()
{
    $.ajax({
        data: { dossier:$('#dossier').val(), banque:$('#js_banque').val() },
        type: 'POST',
        url: Routing.generate('banque_compte_dossier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_conteneur_compte').html(data);
           goControleReleve();
        }
    });
}

function set_current_exercice()
{
    $('#exercice').val((new Date()).getFullYear());
}

function after_charged_dossier()
{
    charger_banque();
}
function after_charged_dossier_not_select()
{
    charger_banque();
}