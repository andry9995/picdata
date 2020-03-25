/**
 * Created by SITRAKA on 31/05/2018.
 */
$(document).on('click','.cl_stat',function(){
    if ($(this).hasClass('white-bg')) $(this).removeClass('white-bg');
    else $(this).addClass('white-bg');

    hide_stat();
});

function charger_stat()
{
    $('#id_stat').empty();
    /**
     * 0 : a categoriser
     * 1 : piece manquante (affecte sans piece)
     * 2 : inconnu
     * 3 : piece affecter (affecte avec piece) releve_detail
     * 4 : piece a affecter (montant trouve)
     * 5 : cle a valider (cle trouve)
     * 6 : cle valider (sans piece)
     * 7 : affecter a une image
     * 8 : cle annuler et a revalider
     * 9 : affecter a des images par cle
     * 10 : piece manquante : pas de piece a affecter
     */
    var a_categoriser = 0,
        piece_manquante = 0,
        affecter_avec_piece = 0,
        piece_a_affecter = 0,
        cle_a_valider = 0,
        affecter_cle = 0;

    $('#js_id_releve_liste').find('tr').each(function(){
        if (!$(this).hasClass('.jqgfirstrow'))
        {
            var s = parseInt($(this).find('.cl_ss').text().trim());
            if (s === 0 || s === 2 || s === 8) a_categoriser++;
            else if (s === 1 || s === 10) piece_manquante++;
            else if (s === 3 || s === 7 || s === 9) affecter_avec_piece++;
            else if (s === 4) piece_a_affecter++;
            else if (s === 5) cle_a_valider++;
            else if (s === 6) affecter_cle++;
        }
    });

    $('.cl_stat').each(function(){
        var type = parseInt($(this).attr('data-type'));
        if (type === 0) $(this).find('.badge').text(number_format(a_categoriser,0,',',' '));
        else if (type === 1) $(this).find('.badge').text(number_format(piece_manquante,0,',',' '));
        else if (type === 2) $(this).find('.badge').text(number_format(affecter_avec_piece,0,',',' '));
        else if (type === 3) $(this).find('.badge').text(number_format(piece_a_affecter,0,',',' '));
        else if (type === 4) $(this).find('.badge').text(number_format(cle_a_valider,0,',',' '));
        else if (type === 5) $(this).find('.badge').text(number_format(affecter_cle,0,',',' '));
    });

    hide_stat();
}

function hide_stat()
{
    var checkeds = [],
        uncheckeds = [];

    $('#id_stat_container').find('.cl_stat').each(function(){
        if ($(this).hasClass('white-bg')) checkeds.push(parseInt($(this).attr('data-type')));
        else uncheckeds.push(parseInt($(this).attr('data-type')));
    });

    if (checkeds.length === 0) checkeds = uncheckeds;

    $('#js_id_releve_liste').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow'))
        {
            var s = parseInt($(this).find('.cl_ss').text());
            /**
             * 0 : a categoriser
             * 1 : piece manquante (affecte sans piece)
             * 2 : inconnu
             * 3 : piece affecter (affecte avec piece) releve_detail
             * 4 : piece a affecter (montant trouve)
             * 5 : cle a valider (cle trouve)
             * 6 : cle valider (sans piece)
             * 7 : affecter a une image
             * 8 : cle annuler et a revalider
             * 9 : affecter a des images par cle
             * 10 : piece manquante : pas de piece a affecter
             */

            /*0">A&nbsp;cat&eacute;goriser&nbsp;<span class="badge badge-info">0</span></span>
            1">Pi&egrave;ce&nbsp;manquante&nbsp;<span class="badge badge-info">0</span></span>
            2">Aff&eacute;cter&nbsp;avec&nbsp;pi&egrave;ce&nbsp;<span class="badge badge-info">0</span></span>
            3">Pi&egrave;ce&nbsp;&agrave;&nbsp;valider&nbsp;<span class="badge badge-info">0</span></span>
            4">Cl&eacute;&nbsp;&agrave;&nbsp;valider&nbsp;<span class="badge badge-info">0</span></span>
            5">Aff&eacute;cter&nbsp;par&nbsp;Cl&eacute;&nbsp;<span class="badge badge-info">0</span></span>*/
            if (s === 0 || s === 2 || s === 8)
            {
                if (checkeds.in_array(0)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 1 || s === 10)
            {
                if (checkeds.in_array(1)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 3 || s === 7 || s === 9)
            {
                if (checkeds.in_array(2)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 4)
            {
                if (checkeds.in_array(3)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 5)
            {
                if (checkeds.in_array(4)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else
            {
                if (checkeds.in_array(5)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
        }
    });

    //jQuery("#js_id_releve_liste").trigger("reloadGrid");
}
