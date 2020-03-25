/**
 * Created by SITRAKA on 30/07/2018.
 */
var pccObjects = [],
    tiersObjects = [],
    tiersString = '',
    chargeString = '',
    tvaString = '',
    banque_type_status = [],
    scroll_position,
    methode_dossier = 0,
    usc = false;

$(document).ready(function(){
    dossier_depend_exercice = true;
    usc = (parseInt($('#id_us').val()) === 1);
    banque_type_status = $.parseJSON($('#js_id_banque_type_status').val());
    $('#exercice').val((new Date()).getFullYear());
    charger_site();

    $(document).on('change','#dossier',function(){
        charger_methode_comptable();
        charger_banque();
    });

    $(document).on('change','#js_banque',function(){
        charger_banque_compte();
    });

    $(document).on('change','#js_banque_compte',function(){
        go();
    });

    $(document).on('change','#exercice',function(){
        //go();
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    //<editor-fold > desc="Refresh All"
    $(document).on('click','#id_refresh_all',function(){
        go();
    });
    $(document).on('mouseover','#id_refresh_all',function(){
        $(this).addClass('fa-spin');
    });
    $(document).on('mouseout','#id_refresh_all',function(){
        $(this).removeClass('fa-spin');
    });
    //</editor-fold>

    $(document).on('click','.cl_methode_dossier',function(){
        set_methode(parseInt($(this).attr('data-type')));
    });
});

function charger_banque()
{
    if($('#dossier option:selected').text().trim() === '' || $('#dossier option:selected').text().trim().toUpperCase() === 'TOUS')
    {
        //show_info('NOTICE','Choisir le Dossier','error');
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
            '                        <option value="'+$('#js_zero_boost').val()+'"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );
        $('#js_id_conteneur_compte').html(
            '        <div class="form-horizontal">' +
            '            <div class="form-group">' +
            '                <label class="control-label col-lg-2" for="js_banque_compte">' +
            '                    <span>N&deg;&nbsp;Cpt</span>' +
            '                    <span class="label label-warning">0</span>' +
            '                </label>' +
            '                <div class="col-lg-10">' +
            '                    <select class="form-control disabled" id="js_banque_compte">' +
            '                        <option value="'+$('#js_zero_boost').val()+'"></option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>'
        );

        vider_table();
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque_dossier'),
        dataType: 'html',
        success: function(data) {
            test_security(data);
            $('#js_id_conteneur_banque').html(data);
            charger_pccs_tiers();
        }
    });
}

function charger_pccs_tiers()
{
    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque2_pcc_tier'),
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data) {
            test_security(data);
            var dataObject = $.parseJSON(data),i;
            tiersObjects = dataObject.tiers;
            pccObjects = dataObject.pccs;

            charger_banque_compte();
        }
    });
}

function charger_banque_compte()
{
    $.ajax({
        data: { dossier:$('#dossier').val(), banque:$('#js_banque').val(), tous:1 },
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
            go();
        }
    });
}

function after_load_dossier()
{
    charger_methode_comptable();
    charger_banque();
}

function vider_table()
{
    set_table([]);
}

function charger_methode_comptable()
{
    if ($('#dossier option:selected').text().trim() !== '')
    {
        $.ajax({
            data: { dossier:$('#dossier').val() },
            type: 'POST',
            url: Routing.generate('banque2_methode_comptable'),
            dataType: 'html',
            success: function(data) {
                test_security(data);
                var methode = parseInt(data);
                set_methode(methode);
            }
        });
    }
}

function set_methode(methode)
{
    methode_dossier = methode;
    $('#id_methode_dossier_container .cl_methode_dossier').each(function(){
        if (parseInt($(this).attr('data-type')) === methode)
        {
            $(this).addClass('active');
            $('#id_methode_dossier_container .libelle').text($(this).text());
        }
        else $(this).removeClass('active');
    });
}
