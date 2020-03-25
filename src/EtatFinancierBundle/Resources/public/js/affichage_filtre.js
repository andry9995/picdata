/*******************************
*       EVENEMENTS
* *****************************/
$(document).ready(function(){
    cloture = 12;
    client = 0;
    site = 0;
    dossier = 0;
    //exercice_old = new Array();
    exercice = new Array();
    mois = new Array();

    //balance agee
    periode_agee = new Array();
    date_anciennete = new Date();
    recharger_date_ancienne = true;

    gerer_height();
    initialisation();
});

$(document).on('change','#dossier',function(e){
    close_pop_over(e);
    current_cloture = get_clotureDossier();
    if(cloture != current_cloture)
    {
        charger_date_picker();
        cloture = current_cloture;
    }
    go();
});
$(document).on('click','#js_dp_valider',function(){
    $('#date_picker_hidden').html($('.popover-content').html());
    recharger_date_ancienne = false;
    go();
    $('#dp_button').attr('data-content',$('.popover-content').html());
    $('#dp_button').click();
});


/*******************************
 *       FONCTIONS
 * *****************************/
function initialisation()
{
    charger_site();
    if($('#js_eb_a_exporter').length > 0) eb_reset_periode_agee();
}
function set_parametre()
{
    client = 0;
    site = 0;
    dossier = 0;
    exercice = new Array();
    mois = new Array();

    client = $('#client').val();
    site = $('#site').val();
    dossier = $('#dossier').val();

    if(!($('#date_picker_hidden th.'+dp_class_exercice()).length > 0))
    {
        year_now = (new Date()).getFullYear();
        $('#date_picker_hidden .js_dp_exercice').each(function(){
            if($(this).text().trim() == year_now) $(this).addClass(dp_class_exercice());
        });
    }
    $('#date_picker_hidden th.js_dp_exercice').each(function(){
        if($(this).hasClass(dp_class_exercice())) exercice.push($(this).text().trim());
    });

    exercice.reverse();

    if(!($('#date_picker_hidden td.'+dp_class_mois()).length > 0))
    {
        $('#date_picker_hidden td.js_dp_mois').addClass(dp_class_mois());
        $('#date_picker_hidden th.js_dp_trimestre').addClass(dp_class_trimestre());
    }

    $('#date_picker_hidden td.js_dp_mois').each(function(){
        if($(this).hasClass(dp_class_mois()))
        {
            mois.push((($(this).attr('data-val').trim().length == 1) ? '0' : '') + $(this).attr('data-val').trim());
        }
    });

    $('#dp_button').attr('data-content',$('#date_picker_hidden').html());
    $('#dp_button').html('<i class="fa fa-calendar"></i>' + '  ' + exercice.toString());

    if($("#dossier option:selected").text().trim().toUpperCase() == 'TOUS' || $("#dossier option:selected").text().trim() == '')
    {
        show_info('Erreur','Choisir un DOSSIER','warning');
        $('#dossier').parent().parent().addClass('has-warning');
        return false;
    }
    else $('#dossier').parent().parent().removeClass('has-warning');

    if(exercice.length == 0)
    {
        show_info('Erreur','Choisir la PERIODE','warning');
        $('#dp_button').parent().parent().addClass('has-warning');
        return false;
    }
    else $('#dp_button').parent().parent().removeClass('has-warning');

    //date anciennete
    charger_date_annciennete();
    return true;
}

function charger_date_annciennete()
{
    etat = parseInt($('#js_etat').val());
    if(recharger_date_ancienne && (etat == 3 || etat == 4))
    {
        lien = Routing.generate('app_date_anciennete');
        $.ajax({
            data: { dossier:$('#dossier').val() , exercice:exercice[0] },
            url: lien,
            type: 'POST',
            async: false,
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data){
                date_object = $.parseJSON(data);
                var t = date_object.date.split(/[- :]/);
                date_anciennete = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
            }
        });
    }
}