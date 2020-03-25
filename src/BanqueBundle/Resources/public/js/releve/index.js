var pccObjects = [],
    tiersObjects = [],
    tiersString = '',
    chargeString = '',
    tvaString = '',
    banque_type_status = [];


$(document).ready(function(){
    banque_type_status = $.parseJSON($('#js_id_banque_type_status').val());
    set_current_exercice();
    charger_site();

    $(document).click(function(e) {
        var el = e.target;
        if (!(el.closest('td[aria-describedby="js_id_releve_liste_t"]') !== null ||
            el.closest('td[aria-describedby="js_id_releve_liste_c"]') !== null ||
            el.closest('td[aria-describedby="js_id_releve_liste_tva"]') !== null))
        {
            $("#js_id_releve_liste").jqGrid('restoreRow',lastsel); // cancel edit
        }
    });


    $(document).on('change','#dossier',function(){
        charger_banque();
    });

    $(document).on('change','#js_banque',function(){
        charger_banque_compte();
    });

    $(document).on('change','#js_banque_compte',function(){
        go();
    });

    $(document).on('click','.js_show_image_',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image').text());
    });

    $(document).on('click','.js_show_image_soeur',function(){
        show_image_pop_up($(this).closest('tr').find('.js_id_image_soeur').text());
    });

    $(document).on('click', '.is_show_image_temp_', function(){
       var iti = $.trim($(this).closest('tr').find('td[aria-describedby="js_id_releve_liste_iti"]').text());
        if(iti != ''){
           show_image_pop_up(iti);
       }
    });

    $(document).on('change','#exercice',function(){
        go();
    });


    $(document).on('click', '.js_edit_releve', function(event){
        event.preventDefault();
        event.stopPropagation();

        var lastSel = $(this).closest('tr').attr('id');

        $('#js_id_releve_liste').jqGrid('saveRow', lastSel, {
            "aftersavefunc": function(data) {

            }
        });
    });

    $(document).on('click', '#js_save_tiers', function(){

        var intitule = $('#js_intitule').val();
        var did = $('#dossier').val();
        var id = $(this).attr('data-id');
        id = id + '_t';

        $.ajax({
            url: Routing.generate('banque_releve_tiers_edit'),
            type: 'POST',
            async: true,
            data: {
                rid: $(this).attr('data-id'),
                did: did,
                intitule: intitule
            },
            success: function(data){
                console.log(data);
                $('#js_tiers_modal').modal('hide');

                if(data.output == 'insere')
                {
                    var opt = $('select[id="' + id + '"] option[value="-1"]');
                    opt.text(data.intitule);
                    opt.val(data.id);
                }
                else
                {
                    show_info("Attention", data.output, 'warning');
                }
            }
        });
    });
});

function vider_table()
{
    $('#js_id_releve_liste').closest('.bande').html('<table id="js_id_releve_liste"></table>');
    var table_selected = $('#js_id_releve_liste'),
        w = table_selected.parent().width(),
        h = $(window).height() - 250,
        editurl = Routing.generate('banque_releve_edit');
    set_table_jqgrid([],h,get_col_model(),get_col_model(w),table_selected,'hidden',w,editurl,false,50,undefined,undefined,undefined,undefined,false,undefined,false);
    $('#id_stat').empty();
}

function go()
{
    if ($('#dossier option:selected').text().trim() == '')
    {
        $('#dossier').closest('.form-group').addClass('has-error');
        show_info('NOTICE','Choisir le dossier','error');
        vider_table();
        return;
    }
    else $('#dossier').closest('.form-group').removeClass('has-error');

    /*if ($('#js_banque_compte option:selected').text().trim() == '' || $('#js_banque_compte option:selected').text().toUpperCase().trim() == 'TOUS')
    {
        $('#js_banque_compte').closest('.form-group').addClass('has-error');
        show_info('NOTICE','Choisir le numero de COMPTE','error');
        vider_table();
        return;
    }
    else $('#js_banque_compte').closest('.form-group').removeClass('has-error');*/

    scroll_position = 0;
    charger_analyse();
    //charger_control();
}

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
            charger_pccs_tiers();
        }
    });
}

function charger_pccs_tiers()
{
    $.ajax({
        data: { dossier:$('#dossier').val() },
        type: 'POST',
        url: Routing.generate('banque_pcc_tier'),
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
            tiersString = '0-0:;';
            chargeString = '0-0:;';
            tvaString = '0-0:;';
            for (i = 0; i < pccObjects.length; i++)
            {
                if (pccObjects[i].c.length >= 3 && pccObjects[i].c.substr(0,3) == '445') tvaString += pccObjects[i].id + ':' + pccObjects[i].c + ' - ' + pccObjects[i].i + ((i == pccObjects.length - 1) ? '' : ';');
                if (pccObjects[i].c >= 1 && parseInt(pccObjects[i].c.substr(0,1)) >= 6) chargeString += pccObjects[i].id + ':' + pccObjects[i].c + ' - ' + pccObjects[i].i + ((i == pccObjects.length - 1) ? '' : ';');
                if (pccObjects[i].c >= 1 && parseInt(pccObjects[i].c.substr(0,1)) <= 6) tiersString += pccObjects[i].id + ':' + pccObjects[i].c + ' - ' + pccObjects[i].i + ((i == pccObjects.length - 1) ? '' : ';');
            }
            for (i = 0; i < tiersObjects.length; i++)
            {
                tiersString += tiersObjects[i].id + ':' + tiersObjects[i].c + ' - ' + tiersObjects[i].i + ((i == tiersObjects.length - 1) ? '' : ';');
            }
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