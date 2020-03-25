/**
 * Created by SITRAKA on 21/11/2016.
 */
$(document).ready(function(){
   charger_site();

   $(document).on('change','#dossier',function(){
      charger_packs();
      set_cloture();
   });
   $(document).on('click','.js_count_column',function(){
      change_count_column($(this));
   });
   $(document).on('click','.js_tabe_pack',function(){
      charger_active_pack();
   });
});

function after_charged_dossier()
{
    charger_packs();
    set_cloture();
}

function charger_active_pack()
{
   var pack_actif = null;
   $('#js_conteneur_pack').find('.js_tab_pack').each(function(){
      if($(this).hasClass('active')) pack_actif = $(this);
   });

   if(pack_actif == null)
   {
      show_info('Erreur','LE DOSSIER N EST PAS ENCORE CLASSE','error');
      return;
   }

   //initialisation
   $('#js_conteneur_pack').find('.js_indicateur_item').each(function(){
      set_anciennete($(this));
      initialise_indicateur($(this));
   });

   //periode actives
   pack_actif.find('.js_indicateur_item').each(function(){
      //initialise_indicateur($(this));
      charger_graphe($(this));
   });
}

/**
 *
 * @param div_current
 */
function initialise_indicateur(div_current)
{
   //initialise enabled periode
   var periode_actives_spliter = div_current.find('.js_hidden_periode_binary').val().split(''),
       annuel = (parseInt(periode_actives_spliter[0]) == 1),
       semestre = (parseInt(periode_actives_spliter[1]) == 1),
       trimestre = (parseInt(periode_actives_spliter[2]) == 1),
       mois = (parseInt(periode_actives_spliter[3]) == 1);
   div_current.find('.js_date_picker_hidden').find('.js_dpk_periode').each(function(){
      var niveau = parseInt($(this).attr('data-niveau'));
      if(niveau == 0)
      {
         if(!annuel)
         {
            $(this).removeClass(dpkGetActiveDatePicker());
            $(this).addClass('disabled-element');
         }
         else
         {
            $(this).addClass('pointer');
            if(!semestre && !trimestre && !mois) $(this).addClass(dpkGetActiveDatePicker());
         }
      }
      if(niveau == 1)
      {
         if(!semestre)
         {
             $(this).removeClass(dpkGetActiveDatePicker());
             $(this).addClass('disabled-element');
         }
         else
         {
             $(this).addClass('pointer');
             if(!trimestre && !mois) $(this).addClass(dpkGetActiveDatePicker());
         }
      }
      if(niveau == 2)
      {
         if(!trimestre)
         {
             $(this).removeClass(dpkGetActiveDatePicker());
             $(this).addClass('disabled-element');
         }
         else
         {
             $(this).addClass('pointer');
             if(!mois) $(this).addClass(dpkGetActiveDatePicker());
         }
      }
      if(niveau == 3)
      {
         if(!mois)
         {
             $(this).addClass('disabled-element');
             $(this).removeClass(dpkGetActiveDatePicker());
         }
         else $(this).addClass('pointer');
      }
   });

   //initialise affichage
   var init_text = $.parseJSON(div_current.find('.js_last_show_hidden').text().trim());
   if(init_text != null)
   {
        var initialisations = $.parseJSON(div_current.find('.js_last_show_hidden').text()),
            code_graphe = initialisations.codeGraphe.trim(),
            exercices = initialisations.exercices,
            analyse = initialisations.analyse,
            periodesTemps = initialisations.periodes,
            periodes = [];

        $.each(periodesTemps, function( k, val ) {
            if(!periodes.in_array(val)) periodes.push(val);
        });

        //code graphe
        var existe = false;
        div_current.find('.js_ul_graphe').find('.js_graphe').each(function(){
         if($(this).attr('data-code').trim() == code_graphe)
         {
            $(this).addClass('active');
            existe = true;
         }
         else $(this).removeClass('active');
        });
        if(!existe) div_current.find('.js_ul_graphe').find('.js_graphe:first').addClass('active');

        //analyse
        existe = false;
        div_current.find('.js_ul_analyse').find('.js_analyse').each(function(){
         if(parseInt($(this).attr('data-type')) == analyse)
         {
            $(this).addClass('active');
            existe = true;
         }
         else $(this).removeClass('active');
        });
        if(!existe) div_current.find('.js_ul_analyse').find('.js_analyse:first').addClass('active');

        //exercice
        existe = false;
        div_current.find('.js_date_picker_hidden').find('.js_dpk_exercice').each(function(){
            if(exercices.in_array($(this).text().trim()))
            {
                $(this).addClass(dpkGetActiveDatePicker());
                existe = true;
            }
            else $(this).removeClass(dpkGetActiveDatePicker());
        });
        if(!existe) div_current.find('.js_date_picker_hidden').find('.js_dpk_exercice:first').addClass(dpkGetActiveDatePicker());

        //periodes
        if(periodes.length > 0)
        {
            div_current.find('.js_date_picker_hidden').find('.js_dpk_periode').each(function(){
                if(periodes.in_array($(this).text().trim())) $(this).addClass(dpkGetActiveDatePicker());
                else $(this).removeClass(dpkGetActiveDatePicker());
            });
        }
   }

   div_current.find('.js_periode').attr('data-content',div_current.find('.js_date_picker_hidden').html());
}

function packs_can_load()
{
   if($('#dossier option:selected').text().trim() == '')
   {
      show_info('NOTICE','CHOISIR LE DOSSIER','error');
      return false;
   }
   return true;
}

function charger_packs()
{
   var height = $(window).height() * 0.8;
   $('#js_conteneur_pack').empty();
   if(!packs_can_load()) return;
   var lien = Routing.generate('ind_affichage_pack');
   $.ajax({
      data: { dossier:$('#dossier').val(), count_column:$('#js_count_column').find('.active').text().trim(), height:height },
      url: lien,
      type: 'POST',
      contentType: "application/x-www-form-urlencoded;charset=utf-8",
      beforeSend: function(jqXHR) {
         jqXHR.overrideMimeType('text/html;charset=utf-8');
      },
      dataType: 'html',
      success: function(data){
         test_security(data);
         $('#js_conteneur_pack').html(data);
         $("[data-toggle=popover]").popover({html:true});

         //set collapse
         $('#js_conteneur_pack .collapse-link').click(function () {
            var ibox = $(this).closest('div.ibox');
            var button = $(this).find('i');
            var content = ibox.find('div.ibox-content');
            content.slideToggle(200);
            button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
            ibox.toggleClass('').toggleClass('border-bottom');
            setTimeout(function () {
               ibox.resize();
               ibox.find('[id^=map-]').resize();
            }, 50);
         });

         // Fullscreen ibox function
         $('#js_conteneur_pack .fullscreen-link').click(function() {
            var ibox = $(this).closest('div.ibox');
            var button = $(this).find('i');
            $('body').toggleClass('fullscreen-ibox-mode');
            button.toggleClass('fa-expand').toggleClass('fa-compress');
            ibox.toggleClass('fullscreen');
            setTimeout(function() {
               $(window).trigger('resize');
            }, 100);
         });

         charger_active_pack();
         drag_conteneur();
      }
   });
}

function change_count_column(btn)
{
   $('.js_count_column').removeClass('active');
   btn.addClass('active');
   charger_packs();
}


function drag_conteneur()
{
   var element = "[class*=col]";
   var handle = ".ibox-title";
   var connect = "[class*=col]";
   $('#js_conteneur_pack').find(element).sortable(
       {
          handle: handle,
          connectWith: connect,
          tolerance: 'pointer',
          forcePlaceholderSize: true,
          opacity: 0.8
       }).disableSelection();
}