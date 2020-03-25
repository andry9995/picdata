/********************************
 *      EVENEMENTS
********************************/
//enregistrer etat item in modal
$(document).on('click','#js_enregistrer_etat',function(){
    save_etat();
});
//click on etat item
$(document).on('click','.js_etat_item',function(){
   charger_compte($(this));
});

/********************************
 *      FONCTIONS
********************************/
//save etat
function save_etat()
{
    etat_id = parseInt($('#js_etat_id').val());
    etat_libelle = $('#js_etat_libelle').val().trim();
    calcul = parseInt($('#js_etat_status div.i-checks div.checked input.js_etat_calcule').val());
    parent = 0;
    action = 1;
    rang = 0;

    parent = parseInt($('#js_etat_parent').val());

    edit_etat(etat_id,action,rang,calcul,parent,etat_libelle);
    close_modal();
    if (parent != 0) $('#regime_fiscal').change();
}

//edit etat item
function edit_etat_item(key,a)
{
    //$etat = id_etat , $action = 0(show) 1(edit) 2(edit only rang) 3(remove) , $rang = rang etat
    etat  = 0;
    action = 0;
    rang = 1000;
    calcul = 4;
    parent = 0;
    libelle = 'l';
    $('.tr_edit').removeClass('tr_edit');

    if(key == 3)
    {
        etat = parseInt(a.parent().parent().attr('data-id'));
        a.parent().parent().addClass('tr_edit');
        edit_etat(etat,action,rang,calcul,parent,libelle);
        return;
    }

    if(key == 0 || key == 1 || key == 2)
    {
        parent = parseInt(a.parent().parent().attr('data-parent'));
        
        if(a.hasClass('js_niveau_1'))
        {
            i = '';
            class_a = 'js_niveau_1';
            class_td = 'niveau-1';
        }
        if(a.hasClass('js_niveau_2'))
        {
            i = '<i class="fa fa-circle"></i>&nbsp;';
            class_a = 'js_niveau_2';
            class_td = 'niveau-2';
        }
        if(a.hasClass('js_niveau_3'))
        {
            i = '<i class="fa fa-circle-o"></i>&nbsp;';
            class_a = 'js_niveau_3';
            class_td = 'niveau-3';
        }
        if(a.hasClass('js_niveau_4'))
        {
            i = '<i class="fa fa-caret-right"></i>&nbsp;';
            class_a = 'js_niveau_4';
            class_td = 'niveau-4';
        }
        if(a.hasClass('js_niveau_5'))
        {
            i = '<i class="fa fa-angle-right"></i>&nbsp;';
            class_a = 'js_niveau_5';
            class_td = 'niveau-5';
        }

        if(key == 2)
        {
            if(a.hasClass('js_niveau_1'))
            {
                i = '<i class="fa fa-circle"></i>&nbsp;';
                class_a = 'js_niveau_2';
                class_td = 'niveau-2';
            }
            if(a.hasClass('js_niveau_2'))
            {
                i = '<i class="fa fa-circle-o"></i>&nbsp;';
                class_a = 'js_niveau_3';
                class_td = 'niveau-3';
            }
            if(a.hasClass('js_niveau_3'))
            {
                i = '<i class="fa fa-caret-right"></i>&nbsp;';
                class_a = 'js_niveau_4';
                class_td = 'niveau-4';
            }
            if(a.hasClass('js_niveau_4'))
            {
                i = '<i class="fa fa-caret-right"></i>&nbsp;';
                class_a = 'js_niveau_5';
                class_td = 'niveau-5';
            }

            parent = parseInt(a.parent().parent().attr('data-id'));
        }
        new_tr = '<tr class="js_etat_item pointer tr_edit" data-id="0" data-parent="'+parent+'">'+
                    '<td class="'+class_td+'">'+i+'<span class="js_libelle">NOUVELLE LIGNE</span></td>'+
                    '<td class="text-right"><a class="js_menu_context label label-default '+class_a+'"><i class="fa fa-wrench"></i></a></td>'+
                 '</tr>';

        if(key == 0)
        {
            $(new_tr).insertBefore(a.parent().parent());
            edit_etat(etat,action,rang,calcul,parent,libelle);
        }
        else
        {
            if(key == 2 && !a.hasClass('js_niveau_5')) parent = parseInt(a.parent().parent().attr('data-id'));
            $(new_tr).insertAfter(a.parent().parent());
            edit_etat(etat,action,rang,calcul,parent,libelle);
        }
        
        return;
    }

    if(key == 4)
    {
        etat = parseInt(a.parent().parent().attr('data-id'));
        action = 3;
        a.parent().parent().addClass('tr_edit');
        edit_etat(etat,action,rang,calcul,parent,libelle);
    }
}

//function show menu
function show_menu(a)
{
    $('.js_menu_context').removeClass('label-primary');
    a.addClass('label-primary');
}

//charger les comptes
function charger_compte(tr)
{
    $('.js_etat_item').removeClass(get_active_tr());
    tr.addClass(get_active_tr());
    etat = tr.attr('data-id');
    lien = Routing.generate('etat_financier_compte')+'/'+etat;

    etat_select = parseInt($('#etat_select').val());
    if(etat_select != 4)
    {
        charger_compte_item(lien,1,etat_select);
    }
    else
    {
        alert('visualisation gobale');
    }
}

function charger_compte_item(lien,brut,etat_select)
{
    verrou_fenetre(true);
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR){
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data){
            if(brut == 1)
            {
                $('#compte_brut').html(data);
                if(etat_select == 0) charger_compte_item(lien+'/'+0,0,etat_select);
            }
            else $('#compte_amort').html(data);
            affichage_compte(parseInt($('#js_tous_compte').val().trim()));
            activer_qTip();
        }
    });
}

//edition etat
function edit_etat(etat,action,rang,calcul,parent,libelle)
{
    etat_select = parseInt($('#etat_select').val());

    if(etat == parent)
    {
        show_info('NOTICE','Choisir un autre parent!!','warning');
        return;
    }

    regime = ($('#regime_fiscal').length > 0) ? $('#regime_fiscal').val() : 0;
    dossier = ($('#dossier').length > 0) ? $('#dossier').val() : 0;
    lien = Routing.generate('etat_financier_edit')+'/'+etat+'/'+action+'/'+rang+'/'+calcul+'/'+parent+'/'+etat_select+'/'+regime+'/'+dossier+'/'+libelle;

    verrou_fenetre(true);
    $.ajax({
        data: {},
        url: lien,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        beforeSend: function(jqXHR) {
            jqXHR.overrideMimeType('text/html;charset=utf-8');
        },
        dataType: 'html',
        success: function(data)
        {
            if(etat == 0)
            {
                $('.tr_edit').attr('data-id',parseInt(data.trim()));
                charger_compte($('.tr_edit'));
                $('.tr_edit').removeClass('tr_edit');

                rang = 0;
                $('#etat table tbody tr').each(function(){
                    rang++;
                    etat = $(this).attr('data-id');
                    action = 2;
                    edit_etat(etat,action,rang,0,0,'l');
                });
                show_info('SUCCES','Une ligne a été ajoutée');
            }
            else
            {
                if(action == 0)
                {
                    titre = '<i class="fa fa-pencil-square-o"></i> <span>Modification</span>';
                    animated = 'bounceInRight';
                    show_modal(data,titre,animated);
                    activer_checkbox();
                    $('#js_etat_parent').val($('#js_etat_parent_id').val());
                }
                if(action == 1)
                {
                    $('.tr_edit').find('td span.js_libelle').text(libelle);
                    $('.tr_edit').removeClass('tr_edit');
                    show_info('SUCCES','Ligne modifiée avec succès');
                }
                if(action == 3)
                {
                    $('.tr_edit').remove();
                    show_info('SUCCES','Ligne supprimée avec succès');
                }
            }
        },
        error: function(){
            if(action == 3)
            {
                show_info('ERREUR: Ligne non supprimée',"Existence d'une sous ligne",'error');
            }
        }
    });     
}