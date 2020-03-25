/**
 * Created by SITRAKA on 31/05/2018.
 */
$(document).on('click','.cl_stat',function(){
    if ($(this).hasClass('tag_active')) $(this).removeClass('tag_active');
    else $(this).addClass('tag_active');

    hide_stat();
});

function charger_stat()
{
    $('#id_stat').empty();

    /**
     * 0 : a categorise
     * 1 : flaguer piece
     * 2 : flaguer cle
     * 3 : piece trouve
     * 4 : cle trouve
     */

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
        affecter_cle = 0,
        imputer = 0,
        cle_piece_a_valider = 0,
        desiquilibre = 0;

    $('#js_id_releve_liste').find('tr').each(function(){
        if (!$(this).hasClass('.jqgfirstrow'))
        {
            if (parseInt($(this).find('.in_stat').text()) === 1)
            {
                var s = parseInt($(this).find('.cl_ss').text().trim());
                if (s === 0) a_categoriser++;
                else if (s === 1) affecter_avec_piece++;
                else if (s === 2) affecter_cle++;
                else if (s === 3) piece_a_affecter++;
                else if (s === 4) cle_a_valider++;
                else if (s === 5)
                {
                    if ($(this).find('.cl_soeurs').length > 0) affecter_avec_piece++;
                    else a_categoriser++;
                }

                var s_cp = parseInt($(this).find('.cl_cleWP').text());
                if (s_cp === 1) cle_piece_a_valider++;

                if (s === 1 || s === 2 || s === 5) imputer++;

                if (s === 1 && $(this).find('.cl_desiquilibre').length > 0) desiquilibre++;
            }
        }
    });

    $('.cl_stat').each(function(){
        var type = parseInt($(this).attr('data-type'));
        if (type === 0) $(this).find('.badge').text(number_format(a_categoriser,0,',',' '));
        else if (type === 1) $(this).find('.badge').text(number_format(piece_manquante,0,',',' '));
        else if (type === 2) $(this).find('.badge').text(number_format(affecter_avec_piece,0,',',' '));
        else if (type === 3)
        {
            var text = number_format(piece_a_affecter,0,',',' ');
            if (cle_piece_a_valider > 0)
                text += ' + ' + number_format(cle_piece_a_valider,0,',',' ');

            $(this).find('.badge').html(text);
        }
        else if (type === 4) $(this).find('.badge').text(number_format(cle_a_valider,0,',',' '));
        else if (type === 5) $(this).find('.badge').text(number_format(affecter_cle,0,',',' '));
        else if (type === 9) $(this).find('.badge').text(number_format(desiquilibre,0,',',' '))
    });

    if (desiquilibre === 0) $('#id_desiquilibre').addClass('hidden');
    else $('#id_desiquilibre').removeClass('hidden');

    var total = a_categoriser + affecter_avec_piece + affecter_cle + piece_a_affecter + cle_a_valider + piece_manquante;

    $('#id_total_ligne').find('.badge').text(number_format(total,0,',',' '));
    $('#id_total_rapprocher').find('.badge').text(number_format(imputer / total * 100,0,',',' ') + '%');

    hide_stat();
}

function hide_stat()
{
    var checkeds = [],
        uncheckeds = [];

    $('#id_stat_container').find('.cl_stat').each(function(){
        if ($(this).hasClass('tag_active')) checkeds.push(parseInt($(this).attr('data-type')));
        else uncheckeds.push(parseInt($(this).attr('data-type')));
    });

    if (checkeds.length === 0) checkeds = uncheckeds;

    $('#js_id_releve_liste').find('tr').each(function(){
        if (!$(this).hasClass('jqgfirstrow'))
        {
            var s = parseInt($(this).find('.cl_ss').text()),
                cle_wp = parseInt($(this).find('.cl_cleWP').text());

            /**
             * 0 : a categorise
             * 1 : flaguer piece
             * 2 : flaguer cle
             * 3 : piece trouve
             * 4 : cle trouve
             * 5 : PM
             */

            /*<span class="simple_tag cl_stat pointer" data-type="0">A&nbsp;catégoriser&nbsp;<span class="badge badge-info">0</span></span>
            <span class="simple_tag cl_stat pointer" data-type="1">Pièce&nbsp;manquante&nbsp;<span class="badge badge-info">0</span></span>
            <span class="simple_tag cl_stat pointer" data-type="2">Affécter&nbsp;avec&nbsp;pièce&nbsp;<span class="badge badge-info">0</span></span>
            <span class="simple_tag cl_stat pointer" data-type="3">Pièce&nbsp;à&nbsp;valider&nbsp;<span class="badge badge-info">0</span></span>
            <span class="simple_tag cl_stat pointer" data-type="4">Clé&nbsp;à&nbsp;valider&nbsp;<span class="badge badge-info">0</span></span>
            <span class="simple_tag cl_stat pointer" data-type="5">Affécter&nbsp;par&nbsp;Clé&nbsp;<span class="badge badge-info">0</span></span>*/

            if (s === 0)
            {
                if (checkeds.in_array(0) && !$(this).hasClass('cl_soeurs')) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 1)
            {
                if (checkeds.in_array(2)) $(this).removeClass('hidden');
                else if (checkeds.in_array(9) && $(this).find('.cl_desiquilibre').length > 0) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 2)
            {
                if (checkeds.in_array(5) || (checkeds.in_array(3) && cle_wp === 1)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 3)
            {
                if (checkeds.in_array(3)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 4)
            {
                if (checkeds.in_array(4)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else if (s === 5)
            {
                if (checkeds.in_array(0) && !$(this).hasClass('cl_soeurs')) $(this).removeClass('hidden');
                else if (checkeds.in_array(2) && !$(this).hasClass('cl_soeurs')) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
            else
            {
                if (checkeds.in_array(1)) $(this).removeClass('hidden');
                else $(this).addClass('hidden');
            }
        }
    });
}

