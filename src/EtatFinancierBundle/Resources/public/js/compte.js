/*************************
 *      EVENEMENTS
*************************/
//click on debit or credit
$(document).on('click','table.js_table_compte tbody tr td span.js_compte',function(){
    compte_status_change($(this));
});
//affichage tous compte
$(document).on('click','#js_tous_compte',function(){
    valeur = (parseInt($(this).val().trim()) == 0) ? 1 : 0;
    affichage_compte(valeur);
});



/*************************
 *      FONCTIONS
*************************/
//change etat compte
function compte_status_change(span)
{
    debit = span.hasClass('js_debit');
    active = span.hasClass(getClassDC(true,debit));
    span.removeClass(getClassDC(active,debit));
    span.addClass(getClassDC(!active,debit));

    id_compte = parseInt(span.parent().parent().attr('data-id'));
    id_etat_compte = parseInt(span.parent().parent().attr('data-id_compte_etat'));
    id_etat = $('#etat table tbody tr.' + get_active_tr()).attr('data-id');
    status_debit = (span.parent().parent().find('span.js_debit').hasClass(getClassDC(true,true))) ? 1 : 0;
    status_credit = (span.parent().parent().find('span.js_credit').hasClass(getClassDC(true,false))) ? 1 : 0;
    
    brut = span.parent().parent().parent().parent().attr('data-brut');

    edit_etat_compte(span,id_compte,id_etat_compte,id_etat,status_debit,status_credit,brut);
}

//function edit compte
function edit_etat_compte(span,id_compte,id_etat_compte,id_etat,status_debit,status_credit,brut)
{
    lien = Routing.generate('etat_financier_compte_edit')+'/'+id_compte+'/'+id_etat_compte+'/'+id_etat+'/'+status_debit+'/'+status_credit+'/'+brut;
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR){
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            if(id_etat_compte == 0)
            {
                span.parent().parent().attr('data-id_compte_etat',data.trim());
                span.parent().parent().removeClass('js_non_cocher');
            }
            else if(status_debit == 0 && status_credit == 0)
            {
                span.parent().parent().attr('data-id_compte_etat',0);
                span.parent().parent().addClass('js_non_cocher');
            }
        }
    });
}

//affichage complet
function affichage_compte(valeur)
{
    $('#js_tous_compte').val(valeur);
    if(valeur) $('tr.js_non_cocher').removeClass('hidden');
    else $('tr.js_non_cocher').addClass('hidden');
}

//get class
function getClassDC(active,debit)
{
    defaut = 'btn-outline btn-default';
    return (active) ? ((debit) ? 'btn-primary' : 'btn-warning') : defaut;
}