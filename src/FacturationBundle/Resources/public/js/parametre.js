/**
 * Created by MAHARO on 08/11/2016.
 */
$(function() {

    //var width100 = $("#table-domaine").parent().width();
    //var hauter = $(window).height() * 0.6;
    tableToGrid('#js_prestation',{});//, {height:hauter,width:width100});
});

$(document).ready(function () {
    charger_domaine();
    charger_unite();
    charger_remisev();
    charger_modele();
    charger_indice();
    charger_prestationGen();
    });

$(document).on('click','#js_add_domaine_fact',function(){
    show_edit_domaine($(this));
});

$(document).on('click','#js_btn_save_domaine',function(){
    save_domaine();
});

$(document).on('click','#js_add_indice_fact', function () {
    show_edit_indice();
});

$(document).on('click','#js_btn_save_indice',function () {
    save_indice();
});

$(document).on('click','#js_add_modele_fact', function () {
    show_edit_modele()
});

$(document).on('click','#js_btn_save_modele',function () {
    save_modele();
});

$(document).on('click','#js_add_remisev_fact',function () {
    show_edit_remisev()
});

$(document).on('click','#js_btn_save_remisev',function () {
    save_remisev();
});

$(document).on('click','#js_add_unite_fact', function () {
    show_edit_unite();
});

$(document).on('click','#js_btn_save_unite',function(){
    save_unite();
});

$(document).on('click','#js_add_prestationGen_fact',function(){
    show_edit_prestationGen();
});

$(document).on('click','#js_btn_save_prestGen',function(){
    save_prestationGen();
});

function charger_domaine()
{
    $('#js_panel_body_domaine').empty();

    lien = Routing.generate('fact_domaine');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#js_panel_body_domaine').html(data);
            activer_qTip();
        }
    });
}

function show_edit_domaine(btn)
{
    lien = Routing.generate('fact_dom_edit');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend:
            function(jqXHR)
            {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
        dataType: 'html',
        success:
            function (data)
            {
                titre ='Nouvelle domaine';
                animated = 'bounceInRight';
                show_modal(data,titre,animated);
            }
    });
}

function save_domaine()
{
    libelle = $('#js_domaine_libelle').val();

    lien = Routing.generate('fact_dom_edit');
    $.ajax({
        data: {libelle:libelle, action:1},
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                charger_domaine();
                close_modal();

            }
        }
    });
}

function charger_unite()
{
    $('#js_panel_body_unite').empty();

    lien = Routing.generate('fact_unite');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            $('#js_panel_body_unite').html(data);
            activer_qTip();
        }
    });
}

function show_edit_unite(btn)
{
    lien = Routing.generate('fact_un_edit');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        datatype: 'html',
        success: function (data) {
            titre = 'Nouvelle unité';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
        }
    });

}

function save_unite()
{
    libelle =$('#js_unite_libelle').val();
    lien = Routing.generate('fact_un_edit');
    $.ajax({
        data: {libelle:libelle, action:1},
        url: lien,
        type: 'POST',
        contentType:"application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            if(data==0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                close_modal();
                charger_unite();

            }
        }
    });
}

function charger_remisev() {
    lien = Routing.generate('fact_remise_vol');
    $('#js_panel_body_remisev').empty();
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            $('#js_panel_body_remisev').html(data);
            activer_qTip();
        }
    });

}

function show_edit_remisev(btn)
{
    lien = Routing.generate('fact_remv_edit');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        datatype: 'html',
        success: function (data) {
            titre = 'Nouvelle remise volume';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
        }
    });

}

function save_remisev()
{
    tranche1 = $('#js_remisev_tranche1').val();
    tranche2 = $('#js_remisev_tranche2').val();
    pourcentage = $('#js_remisev_pourcentage').val();

    lien = Routing.generate('fact_remv_edit');
    $.ajax({
        url: lien,
        data: {tranche1:tranche1,tranche2:tranche2,pourcentage:pourcentage,action:1},
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        datatype: 'html',
        success: function (data) {
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                charger_remisev();
                close_modal();
            }
        }
    });
}

function charger_modele()
{
    $('#js_panel_body_modele').empty();
    lien = Routing.generate('fact_modele');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR){
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            $('#js_panel_body_modele').html(data);
            activer_qTip();
        }

    });

}

function show_edit_modele(btn)
{
    lien = Routing.generate('fact_mod_edit');
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            titre = 'Nouvelle modèle de tarification';
            animated  = 'bounceInRight';
            show_modal(data,titre,animated);
        }
    });
}

function save_modele()
{
    libelle = $('#js_modele_libelle').val();
    lien = Routing.generate('fact_mod_edit');
    $.ajax({
        data: {libelle:libelle,action:1},
        url: lien,
        type: 'POST',
        dataType: 'html',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                charger_modele();
                close_modal();

            }
        }
    });

}

function charger_indice()
{
    lien = Routing.generate('fact_indice');
    $('#js_panel_body_indice').empty();
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            $('#js_panel_body_indice').html(data);
            activer_qTip();
        }

    });
}

function show_edit_indice(btn)
{
    lien = Routing.generate('fact_ind_edit');

    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            titre = 'Nouvelle indice';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);
            $('#js_indice_date_pick').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "dd/mm/yyyy"
            });
        }
    });
}

function save_indice()
{
    index_indice = $('#js_indice_index').val();
    indice = $('#js_indice_indice').val();
    pourcentage = $('#js_indice_pourcentage').val();
    date =$('#js_indice_date').val();

    lien = Routing.generate('fact_ind_edit');
    $.ajax({
        data: {index_indice:index_indice,indice:indice,pourcentage:pourcentage,date:date,action:1},
        url: lien,
        type: 'POST',
        dataType: 'html',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                charger_indice();
                close_modal();

            }
        }
    });
}

function charger_prestationGen()
{
    lien = Routing.generate('fact_prestGen');
    $('#js_panel_body_prestgen').empty();
    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            $('#js_panel_body_prestgen').html(data);
            activer_qTip();
        }

    });
}

function show_edit_prestationGen(btn) {

    lien = Routing.generate('fact_prestGen_edit');

    $.ajax({
        url: lien,
        type: 'POST',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function (data) {
            titre = 'Nouvelle prestation générale';
            animated = 'bounceInRight';
            show_modal(data,titre,animated);

            $('#js_indice_date_pick').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "dd/mm/yyyy"
            });
        }
    });
}

function save_prestationGen()
{
    domaine = $('#js_prestgen_domaine').val();
    code = $('#js_prestgen_code').val();
    libelle = $('#js_prestgen_libelle').val();
    unite =$('#js_prestgen_unite').val();
    typecalcul = $('#js_prestgen_typecalc').val();
    calcIndice = $('#js_prestgen_calc_indice').val();

    lien = Routing.generate('fact_prestGen_edit');
    $.ajax({
        data: {domaine:domaine,code:code,libelle:libelle,unite:unite,typecalcul:typecalcul,calcIndice:calcIndice,action:1},
        url: lien,
        type: 'POST',
        dataType: 'html',
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function (jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        success: function (data) {
            if(data == 0)
            {
                show_info('ERREUR','CE NOM EXISTE DEJA','error');
            }
            else
            {
                show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                charger_prestationGen()
                close_modal();

            }
        }
    });
}
