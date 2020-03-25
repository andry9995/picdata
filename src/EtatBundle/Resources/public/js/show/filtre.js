/**
 * Created by SITRAKA on 18/04/2017.
 */
$(document).on('change','#dossier',function(){
    set_active_etat();
    charger_periode();
});

$(document).on('change','#client',function(){
    index_table_etat++;
    var tab_active = null,
        new_table = '<table id="table_etat_'+index_table_etat+'"></table>';
    $('#js_id_container_etat').find('.js_cl_tab_etat').each(function(){
        if($(this).hasClass('active')) tab_active = $(this);
    });
    tab_active.find('.js_cl_container_etat').html(new_table);
});

$(document).on('change','#site',function(){
    index_table_etat++;
    var tab_active = null,
        new_table = '<table id="table_etat_'+index_table_etat+'"></table>';
    $('#js_id_container_etat').find('.js_cl_tab_etat').each(function(){
        if($(this).hasClass('active')) tab_active = $(this);
    });
    tab_active.find('.js_cl_container_etat').html(new_table);
});

$(document).on('click','.js_dpk_valider',function(){
    valider_exercice($(this));
});

function valider_exercice(btn)
{
    var new_html = btn.closest('.popover-content').html();
    $('#js_conteneur_periode .js_periode').attr('data-content',new_html).click();
    $('.js_date_picker_hidden').html(new_html);
    go();
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

function set_active_etat() {
    var etats = [];
    $('#js_id_container_etat div.tab-content div.js_cl_tab_etat').each(function(){
        etats.push($(this).attr('data-id'));
    });

    $.ajax({
        data: {
            dossier: $('#dossier').val(),
            etat: parseInt($('#js_et').val()),
            etats: JSON.stringify(etats)
        },
        url: Routing.generate('etat_status_etat'),
        type: 'POST',
        async: false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            var status = $.parseJSON(data),
                index = 0;
            /**
             * li
             */
            $('#js_id_container_etat ul.nav-tabs .js_li_etat').each(function(){
                if(parseInt(status[index].status) == 1) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
                index++;
            });

            index = 0;
            /**
             * content
             */
            $('#js_id_container_etat div.tab-content div.js_cl_tab_etat').each(function(){
                if(parseInt(status[index].status) == 1) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
                index++;
            });

            /**
             * change if active is hidden
             */
            if($('#js_id_container_etat ul.nav-tabs li.active').hasClass('hidden'))
            {
                var hasActive = false;
                $('#js_id_container_etat ul.nav-tabs .js_li_etat').each(function(){
                    if(!$(this).hasClass('hidden') && !hasActive)
                    {
                        $(this).find('a').click();
                        hasActive = true;
                    }
                });
            }
        }
    });
}

function after_charged_dossier()
{
    set_active_etat();
}