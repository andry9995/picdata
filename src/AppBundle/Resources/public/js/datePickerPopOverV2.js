/**
 * Created by SITRAKA on 07/12/2016.
 */
//selectuer js_dpk_..
//if one exercice
var oneExercice = false,
    periodeDependant = false,
    onePeriode = false,
    periodeEquivalent = false,
    containerSelecteur = '.modal-body';

//auto hide pop over date picker
$(document).click(function(event) {
    var element = $(event.target);
    //hide pop over
    if (element.data('toggle') !== 'popover' &&
        element.parent().data('toggle') !== 'popover' &&
        element.parent().parent().data('toggle') !== 'popover' &&
        element.parents('.popover.in').length === 0) {
        $('[data-toggle="popover"]').popover('hide');
    }
});

/**
 * exercice
 */
$(document).on('click','.js_dpk_exercice',function(){
    dpk_change_status_exercice($(this));
});
function dpk_change_status_exercice(td)
{
    if(td.hasClass(dpkGetActiveDatePicker()))
    {
        var count_active = 0;
        td.parent().find('.js_dpk_exercice').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker())) count_active++;
        });
        if(count_active === 1) show_info('ERREUR',"AU MOINS UN EXERCICE DOIT ETRE SELECTIONNE",'error');
        else td.removeClass(dpkGetActiveDatePicker());
    }
    else
    {
        td.addClass(dpkGetActiveDatePicker());
        if(oneExercice)
        {
            td.parent().find('.js_dpk_exercice').each(function(){
                $(this).removeClass(dpkGetActiveDatePicker());
            });
            td.addClass(dpkGetActiveDatePicker());
        }
    }
}

/**
 * periode
 */
$(document).on('click','.js_dpk_periode',function(){
    dpk_change_status_periode($(this));
});
function dpk_change_status_periode(td)
{
    if (onePeriode)
    {
        td.closest('table').find('.js_dpk_periode').removeClass(dpkGetActiveDatePicker());
        td.addClass(dpkGetActiveDatePicker());

        if (periodeEquivalent)
        {
            var container = td.closest(containerSelecteur),
                exercice1 = parseInt(container.find('.js_cl_interval_start_container table thead tr .td-active').text().trim()),
                exercice2 = parseInt(container.find('.js_cl_interval_end_container table thead tr .td-active').text().trim());

            if (exercice1 !== exercice2)
            {
                var niveau = parseInt(td.attr('data-niveau')),
                    val = parseInt(td.attr('data-val'));

                container.find('.js_dpk_periode').each(function(){
                    var niveauI = parseInt($(this).attr('data-niveau')),
                        valI = parseInt($(this).attr('data-val'));

                    if (niveau === niveauI && val === valI) $(this).addClass(dpkGetActiveDatePicker());
                    else $(this).removeClass(dpkGetActiveDatePicker());
                });
            }
        }
        return;
    }

    var valeur = parseInt(td.attr('data-val')),
        mere_annee = parseInt(td.attr('data-mere-annee')),
        mere_semestre = parseInt(td.attr('data-mere-semestre')),
        mere_trimestre = parseInt(td.attr('data-mere-trimestre')),
        niveau = parseInt(td.attr('data-niveau')),
        value = (niveau === 3) ? parseInt(td.attr('data-value')) : valeur;

    /**
     * periode dependante de la precedente
     */
    if(periodeDependant)
    {
        td.closest('tbody').find('.js_dpk_periode').each(function(){
            var valeur_current = parseInt($(this).attr('data-val')),
                niveau_current = parseInt($(this).attr('data-niveau')),
                value_current = (niveau_current === 3) ? parseInt($(this).attr('data-value')) : valeur_current;

            if(niveau_current === niveau && value_current <=  value) $(this).addClass(dpkGetActiveDatePicker());
            else $(this).removeClass(dpkGetActiveDatePicker());
        });
        return;
    }

    /**
     * deselectionner
     */
    if(td.hasClass(dpkGetActiveDatePicker()))
    {
        var count_active = 0;
        td.closest('tbody').find('.js_dpk_periode').each(function(){
            if($(this).hasClass(dpkGetActiveDatePicker())) count_active++;
        });
        if(count_active === 1) show_info('ERREUR',"AU MOINS UNE PERIODE DOIT ETRE SELECTIONNEE",'error');
        else td.removeClass(dpkGetActiveDatePicker());
    }
    /**
     * selectionner
     */
    else
    {
        td.addClass(dpkGetActiveDatePicker());
        td.closest('tbody').find('.js_dpk_periode').each(function(){
            var valeur_current = parseInt($(this).attr('data-val')),
                mere_annee_current = parseInt($(this).attr('data-mere-annee')),
                mere_semestre_current = parseInt($(this).attr('data-mere-semestre')),
                mere_trimestre_current = parseInt($(this).attr('data-mere-trimestre')),
                niveau_current = parseInt($(this).attr('data-niveau'));

            /**
             * deselectionner meres
             */
            if( niveau_current === 0 && mere_annee === valeur_current ||
                niveau_current === 1 && mere_semestre === valeur_current ||
                niveau_current === 2 && mere_trimestre === valeur_current ) $(this).removeClass(dpkGetActiveDatePicker());

            /**
             * deselectionner filles
             */
            if( niveau === 0 && mere_annee_current === valeur ||
                niveau === 1 && mere_semestre_current === valeur ||
                niveau === 2 && mere_trimestre_current === valeur ) $(this).removeClass(dpkGetActiveDatePicker());
        });
    }
}

/**
 *
 * @returns {string}
 */
function dpkGetActiveDatePicker()
{
    return 'td-active';
}