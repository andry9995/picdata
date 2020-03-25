/**
 * Created by SITRAKA on 31/03/2017.
 */
$(document).on('change','#js_is_general',function(){
   if($(this).is(':checked'))
   {
       $('.js_type_client_container').removeClass('hidden');
       $('.js_cl_contener_dossier').addClass('hidden');
   }
   else
   {
       $('.js_type_client_container').addClass('hidden');
       $('.js_cl_contener_dossier').removeClass('hidden');
   }
   charger_etats();
});

$(document).on('change','#client',function(){ charger_etats(); });
$(document).on('change','#dossier',function(){ charger_etats(); });
$(document).on('change','#js_type_client',function(){ charger_etats(); });