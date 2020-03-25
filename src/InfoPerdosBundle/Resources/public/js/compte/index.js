/**
 * Created by SITRAKA on 21/10/2016.
 */
$(document).ready(function(){
    charger_site();
       
    $(document).on('change','#dossier',function(){
        //charger_pccs();
        charger_pcc_combow();
    });
});
