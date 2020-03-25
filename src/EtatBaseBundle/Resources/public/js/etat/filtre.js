/**
 * Created by SITRAKA on 03/07/2017.
 */
$(document).on('click','.js_dpk_valider',function(){
    valider_exercice($(this));
});

$(document).on('change','#dossier',function(){
    charger_pccs_tiers();
    charger_journaux();
    charger_periode();
});

function after_charged_dossier()
{
    charger_pccs_tiers();
}

var pccs = [],
    tiers = [];
function charger_pccs_tiers()
{
    $.ajax({
        data: { dossier: $('#dossier').val() },
        url: Routing.generate('etat_pcc_tiers'),
        type: 'POST',
        async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var datas = $.parseJSON(data);
            pccs = datas.pccs;
            tiers = datas.tiers;
            charger_fourchette_compte();
        }
    });
}

var old_etat = 0;
function charger_fourchette_compte()
{
    //0:bl, 1:bc, 2:bf, 3:gl, 4:jnl, 5:jc
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat')),
        option = parseInt($('#js_container_tabs .tab-content div.active input.js_option:checked').val()),
        options = '<option data-type="2" value="'+ $('#js_zero_boost').val() +'"></option>',
        i;

    if (etat === 1 || etat === 2 || (etat === 3 && (option === 0 || option === 1)))
    {
        for (i = 0; i < tiers.length; i++)
        {
            var tier = tiers[i];
            if (tier.t === 0 && etat === 2 || tier.t === 1 && etat === 1 ||
                tier.t === 0 && etat === 3 && option === 0 || tier.t === 1 && etat === 3 && option === 1)
                options += '<option value="'+ tier.id +'" data-type="'+ tier.t +'">'+ tier.c + ' - ' + tier.i +'</option>';
        }
    }
    else
    {
        for (i = 0; i < pccs.length; i++)
        {
            var pcc = pccs[i];
            options += '<option value="'+ pcc.id +'" data-type="2">'+ pcc.c + ' - ' + pcc.i +'</option>';
        }
    }

    $('#id_compte_de').html(options);
    $('#id_compte_a').html(options);

    if (etat === 5) $('#id_compte_de').closest('.input-daterange').addClass('hidden');
    else $('#id_compte_de').closest('.input-daterange').removeClass('hidden');
}

function valider_exercice(btn)
{
    var new_html = btn.closest('.popover-content').html();
    $('#js_conteneur_periode .js_periode').attr('data-content',new_html).click();
    $('.js_date_picker_hidden').html(new_html);
    charger_date_annciennete();
    go();
}

function charger_journaux()
{
    var etat = parseInt($('#js_container_tabs .nav-tabs li.active').attr('data-etat'));
    $.ajax({
        data: {
            dossier: $('#dossier').val()
        },
        url: Routing.generate('etat_b_journaux'),
        type: 'POST',
        async: (etat != 4) ,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_journal').html(data);
        }
    });
}

function charger_periode()
{
    var lien = Routing.generate('etat_periodes_get'),
        indicateurs = [];

    $('.js_cl_tab_etat').each(function(){
        indicateurs.push($(this).attr('data-id'));
    });

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            indicateurs: JSON.stringify(indicateurs)
        },
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_conteneur_periode').html(data);
            $('.js_date_picker_hidden').html($('#js_conteneur_periode .js_periode').attr('data-content'));
            $("[data-toggle=popover]").popover({ html:true });
            reglage_periode();
            charger_date_annciennete();
            if($("#dossier option:selected").text().trim().toUpperCase() != 'TOUS' && $("#dossier option:selected").text().trim() != '')
                go();
        }
    });
}

function reglage_periode()
{
    var periodes = $.parseJSON($('.js_cl_hide_periode_other').val().trim()),
        index = 0;
    $('.js_cl_tab_etat').each(function(){
        var periode = periodes[index].trim();
        $(this).find('.js_per_hidden').val(periode);

        if($(this).hasClass('active'))
        {
            var periodeSpliter = periode.split(''),
                anneeActivate = parseInt(periodeSpliter[0]) == 1,
                semestreActivate = parseInt(periodeSpliter[1]) == 1,
                trimetreActivate = parseInt(periodeSpliter[2]) == 1,
                moisActivate = parseInt(periodeSpliter[3]) == 1,
                a_activer = 0;

            if(anneeActivate) a_activer = 0;
            else if(semestreActivate) a_activer = 1;
            else if(trimetreActivate) a_activer = 2;
            else a_activer = 3;

            $('.js_date_picker_hidden .table-dpk .js_dpk_periode').each(function(){
                var niveau = parseInt($(this).attr('data-niveau'));

                if(niveau == a_activer) $(this).addClass(dpkGetActiveDatePicker());
                else $(this).removeClass(dpkGetActiveDatePicker());

                if(niveau == 0)
                {
                    if(!anneeActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau == 1)
                {
                    if(!semestreActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau == 2)
                {
                    if(!trimetreActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }

                if(niveau == 3)
                {
                    if(!moisActivate) $(this).addClass('disabled-element');
                    else $(this).removeClass('disabled-element');
                }
            });
            $('#js_conteneur_periode .js_periode').attr('data-content',$('.js_date_picker_hidden').html());
        }
        index++;
    });
}

function charger_date_annciennete()
{
    var div_hidden = $('.js_date_picker_hidden'),
        exercice = 0;
    div_hidden.find('.js_dpk_exercice').each(function(){
        if($(this).hasClass(dpkGetActiveDatePicker()))
        {
            exercice = parseInt($(this).text().trim());
            return false;
        }
    });
    $.ajax({
        data: { dossier: $('#dossier').val() , exercice:exercice },
        url: Routing.generate('app_date_anciennete_calcule'),
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            var t = $.parseJSON(data).date.split(/[- :]/),
                date_anciennete = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));

            $('.js_cl_date_anciennete').each(function(){
                $(this).val(date_anciennete.toMysqlFormat());
            });
        }
    });
}
