<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/02/2017
 * Time: 16:27
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UtilisateurExistant
 *
 * Permert de connaitre si un email est déjà
 * utilisé par un utilisateur
 *
 * @Annotation
 *
 * @package AppBundle\Validator\Consraints
 */
class UtilisateurExistant extends Constraint
{
    public $message = "Cet email est déjà utilisé";
}