var lauch_charger_dossier = true,
    dossier_depend_exercice = false,
    texte_dossier_tous = false,
    texte_site_tous = false,
    dossier_all_exercice = false;

/********************************
 *          EVENEMENTS
* ******************************/
//datepicker
$(document).on('click','.js_dp_exercice',function(){
    dp_exercice_change($(this));
});
$(document).on('click','.js_dp_trimestre',function(){
    dp_trimestre_change_status($(this));
});
$(document).on('click','.js_dp_mois',function(){
    dp_mois_change_status($(this));
});
$(document).on('change','#client',function(){
    charger_site();
});
$(document).on('change','#site',function(){
    charger_dossier();
});
$(document).on('change','#exercice',function(){
    if (dossier_depend_exercice) charger_dossier();
});


/********************************
 *          FONCTIONS
 * ******************************/
//charger site
function charger_site()
{
    var client = $('#client').val(),
        lien = Routing.generate('app_sites')+'/0/'+client+'/1',
        client_text = $('#client option:selected').text().trim().toUpperCase();

    if (client_text === 'TOUS' || client_text === '')
    {
        var html_site = '' +
            '<div class="form-horizontal">' +
                '<div class="form-group">' +
                    '<label class="control-label col-lg-2">' +
                        '<span>Site</span>' +
                    '</label>' +
                    '<div class="col-lg-10">' +
                        '<select data-ref="" class="site form-control" id="site">' +
                            '<option value="'+$('#js_zero_boost').val()+'" selected="selected">Tous</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>';
        $('#js_conteneur_site').html(html_site);

        var html_dossier = '' +
            '<div class="form-horizontal">' +
                '<div class="form-group">' +
                    '<label class="control-label col-lg-2">' +
                        '<span>Dossier</span>' +
                    '</label>' +
                    '<div class="col-lg-10">' +
                        '<select data-ref="" class="dossier form-control" id="dossier">' +
                            '<option value="'+$('#js_zero_boost').val()+'" selected="selected">Tous</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>';
        $('#js_conteneur_dossier').html(html_dossier);

        if(typeof after_change_client_tous === 'function') after_change_client_tous();
        return;
    }

    $('#js_conteneur_site').empty();

    verrou_fenetre(true);
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_conteneur_site').html(data).change();
            if(lauch_charger_dossier) charger_dossier();
            if(typeof after_charged_site === 'function') after_charged_site();
        }
    });
}
//charger dossier a partir du site
function charger_dossier(tous)
{
    if($('#js_conteneur_dossier').length > 0)
    {
        $('#js_conteneur_dossier').empty();
        var exercice = parseInt($('#exercice').val());

        //if (isNaN(exercice)) exercice = 0;
        if (dossier_depend_exercice)
        {
            if (exercice === 0 || isNaN(exercice))
            {
                show_info('Exercice','Choisir un exercice','error');
                return;
            }
        }
        else exercice = 0;
        if (dossier_all_exercice) exercice = 2010;

        tous = (typeof tous !== 'undefined') ? tous : 0;
        if (texte_dossier_tous) tous = 1;

        var site = $('#site').val(),
            client = $('#client').val(),
            lien = Routing.generate('app_dossiers')+'/0/'+site+'/'+tous+'/'+client+'?exercice='+exercice;

        verrou_fenetre(true);
        $.ajax({
            data: { },
            url: lien,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                test_security(data);
                $('#js_conteneur_dossier').html(data);
                charger_exercice(0);
                charger_date_picker();

                if(typeof after_load_dossier === 'function') after_load_dossier();

                if($('#dossier option:selected').text().trim() !== '')
                {
                    if(typeof after_charged_dossier === 'function') after_charged_dossier();
                    if(typeof charger_periode === 'function') charger_periode();
                    if(typeof charger_journaux === 'function') charger_journaux();
                }
                else
                {
                    if(typeof after_charged_dossier_not_select === 'function') after_charged_dossier_not_select();
                }
            }
        });
    }
}
//charger exercice
function charger_exercice(tous)
{

}
//charger date_picker
function charger_date_picker()
{
    var dossier = $('#dossier').val(),
        lien = Routing.generate('app_date_picker')+'/'+dossier;

    verrou_fenetre(true);
    $.ajax({
        data: {},
        url: lien,
        async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            $('#js_conteneur_date_picker').html(data);
            $("[data-toggle=popover]").popover({html:true});
        }
    });
}

function activer_qTip()
{
    $(document).find('.js_tooltip').qtip({
        content: {
            text: function (event, api) {
                return $(this).attr('data-tooltip');
            }
        },
        title: $(this).find('div').text().trim(),
        position: {
            my: 'top right', // Position my top left...
            at: 'bottom right' // at the bottom right of...

        },
        style: {
            classes: 'qtip-youtube'
        }
    });
}
//datepicker
function dp_class_exercice(active)
{
    active = typeof active !== 'undefined' ? active : true;
    return (active) ? 'success' : '';
}
function dp_class_trimestre()
{
    return 'success';
}
function dp_class_mois()
{
    return 'warning';
}
//exercice date picker change
function dp_exercice_change(th)
{
    if(!th.hasClass(dp_class_exercice())) th.addClass(dp_class_exercice());
    else
    {
        var nbr = 0;
        th.parent().find('.' + dp_class_exercice()).each(function(){
            nbr++;
        });
        if(nbr > 1) th.removeClass(dp_class_exercice());
    }
}
//trimestre
function dp_trimestre_change_status(th)
{
    var trimestre = parseInt(th.closest('tr.js_dp_tr_trimestre').attr('data-trimestre')),
        tbody = th.closest('tbody');
    if(!th.hasClass(dp_class_trimestre()))
    {
        tbody.find('tr.js_dp_tr_trimestre').each(function(){
            var trimestre_current = parseInt($(this).attr('data-trimestre'));
            if(trimestre_current <= trimestre)
            {
                $(this).find('.js_dp_trimestre').addClass(dp_class_trimestre());
                $(this).find('.js_dp_mois').addClass(dp_class_mois());
            }
            else
            {
                if(trimestre_current >= trimestre)
                {
                    $(this).find('.js_dp_trimestre').removeClass(dp_class_trimestre());
                    $(this).find('.js_dp_mois').removeClass(dp_class_mois());
                }
            }
        });
    }
    else
    {
        tbody.find('tr.js_dp_tr_trimestre').each(function(){
            var trimestre_current = parseInt($(this).attr('data-trimestre'));
            if(trimestre_current >= trimestre)
            {
                $(this).find('.js_dp_trimestre').removeClass(dp_class_trimestre());
                $(this).find('.js_dp_mois').removeClass(dp_class_mois());
            }
        });
    }
}
//mois
function dp_mois_change_status(td)
{
    var position = parseInt(td.attr('data-position'));
    if(!td.hasClass(dp_class_mois()))
    {
        td.closest('tbody').find('.js_dp_mois').each(function(){
            var position_current = parseInt($(this).attr('data-position'));
            if(position_current <= position)
            {
                $(this).addClass(dp_class_mois());
            }
        });
    }
    else
    {
        td.closest('tbody').find('.js_dp_mois').each(function() {
            var position_current = parseInt($(this).attr('data-position'));
            if(position_current >= position)
            {
                $(this).removeClass(dp_class_mois());
            }
        });
    }

    //trimestre
    td.closest('tbody').find('.js_dp_tr_trimestre').each(function(){
        var count_mois = 0;
        $(this).find('.js_dp_mois').each(function(){
            if($(this).hasClass(dp_class_mois())) count_mois++;
        });
        if(count_mois === 3) $(this).find('.js_dp_trimestre').addClass(dp_class_trimestre());
        else $(this).find('.js_dp_trimestre').removeClass(dp_class_trimestre());
    });

    return;

    if(td.hasClass(dp_class_mois())) td.removeClass(dp_class_mois());
    else td.addClass(dp_class_mois());

    var nbr = 0;
    td.parent().find('.'+dp_class_mois()).each(function(){
        nbr++;
    });

    /*$('#date_picker tr[data-trimestre="'+td.parent().attr('data-trimestre')+'"] td.js_dp_mois').each(function(){
        if($(this).hasClass(dp_class_mois())) nbr++;
    });*/

    if(nbr == 3) td.parent().find('th.js_dp_trimestre').addClass(dp_class_trimestre());
    else td.parent().find('th.js_dp_trimestre').removeClass(dp_class_trimestre());
}

//fonction get cloture MOIS
function get_clotureDossier()
{
    var result = 0,
        dossier_id = $('#dossier').val(),
        lien = Routing.generate('app_cloture_dossier')+'/'+dossier_id;
    $.ajax({
        data: {},
        url: lien,
        async:false,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR){
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            test_security(data);
            result = parseInt(data.trim());
        }
    });

    return result;
}

function get_fin_mois(mois_param,annee_param)
{
    var date_new = new Date(annee_param,mois_param,1);
    date_new.setDate(date_new.getDate() - 1);
    return date_new;
}

function test_security(response)
{
    if(response.trim().toLowerCase() === 'security') location.reload();
}

function set_cloture()
{
    if($('#dossier').length > 0) $('#js_cloture').text('cloture: ' + getMoisLettre(get_clotureDossier(),true,true));
}