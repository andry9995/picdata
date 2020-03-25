/**
 * Created by SITRAKA on 08/07/2016.
 */
$(document).ready(function(){
    row = col = 0;
    rafraichir_indicateur = false;

    $('.dd').nestable({
        group: 1
    });
    activer_qTip();
});