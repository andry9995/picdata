/***************************
 *       EVENEMENTS
***************************/
$(document).ready(function(){
   activer_checkbox();   
   height = 250;
   initialisation();
});



/***************************
 *       FONCTIONS
***************************/
//initialisation
function initialisation()
{
   menu_active();
   if($('#table_clients').length != 0) set_table_clients();
   if($('#table_utilisateurs').length != 0) set_table_utilisateurs();
}

