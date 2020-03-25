/**
 * Created by SITRAKA on 17/10/2018.
 */
$(document).ready(function(){
    $(document).on('click','#js_add_commentaire',function(){
        add_commentaire();
    });

    $(document).on('change','.js_input_comment',function(){
        edit_commentaire($(this));
    });

    $(document).on('click','.js_remove_commentaire',function(){
        remove_commentaire($(this));
    });
});

function show_edit_commentaires()
{
    var titre = '<i class="fa fa-commenting" aria-hidden="true"></i></i>&nbsp;<span>Commentaires</span>',
        animated = 'bounceInRight';
    show_modal('<div class="row"><div class="col-lg-12" id="js_liste_code_analytique"></div></div>',titre,animated);
    charger_commentaires();
}

function charger_commentaires()
{
    if($('#js_liste_code_analytique').length > 0)
    {
        $.ajax({
            data: { dossier:$('#dossier').val() },
            type: 'POST',
            url: Routing.generate('commentaire_dossier_liste'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);
                $('#js_liste_code_analytique').html(data);
            }
        });
    }
}

function add_commentaire()
{
    commentaire_action(0,$('#js_zero_boost').val(),$('#js_code_commentaire').val(),$('#js_libelle_commentaire').val());
}

function edit_commentaire(input)
{
    var tr = input.closest('tr');
    commentaire_action(1,tr.attr('data-id').trim(),tr.find('.js_code_commentaire').val(),tr.find('.js_libelle_commentaire').val(),input);
}

function remove_commentaire(span)
{
    var tr = span.closest('tr');
    commentaire_action(2,tr.attr('data-id').trim(),tr.find('.js_code_commentaire').val(),tr.find('.js_libelle_commentaire').val(),span);
}

function commentaire_action(action,commentaire_dossier,code,libelle,element)
{
    if(commentaire_is_valid(element) || action === 2)
    {
        $.ajax({
            data: { dossier:$('#dossier').val(), action:action, commentaire_dossier:commentaire_dossier, code:code, libelle:libelle },
            type: 'POST',
            url: Routing.generate('commentaire_dossier_edit'),
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            beforeSend: function(jqXHR) {
                jqXHR.overrideMimeType('text/html;charset=utf-8');
            },
            dataType: 'html',
            success: function(data) {
                test_security(data);

                if(parseInt(data) === 1)
                {
                    if(action === 0) charger_commentaires();
                    else if(action === 2) element.closest('tr').remove();
                    show_info('SUCCES','MODIFICATION BIEN ENREGISTREE');
                    if(typeof dossier_selected === 'function') dossier_selected();
                }
                else
                {
                    charger_code_analytiques();
                    show_info('ERREUR','VERIFIER SI CE CODE EXISTE DEJA OU REESSAYER','error');
                }
            }
        });
    }
}

function commentaire_is_valid(element)
{
    var element_code = $('#js_code_commentaire'),
        element_libelle = $('#js_libelle_commentaire');

    if(typeof element !== 'undefined')
    {
        element_code = element.closest('tr').find('.js_code_commentaire');
        element_libelle = element.closest('tr').find('.js_libelle_commentaire');
    }

    if(element_code.val().trim() === '')
    {
        show_info('erreur','CODE VIDE','error');
        return false;
    }
    if(element_code.val().trim().length > 20)
    {
        show_info('erreur','le code ne doit pas depasser 20 DIGITS','error');
        return false;
    }
    if(element_libelle.val().trim() === '')
    {
        show_info('erreur','Commentaire VIDE','error');
        return false;
    }

    return true;
}
