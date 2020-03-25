<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/02/2017
 * Time: 16:32
 */

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UtilisateurExistantValidator
 *
 * Permet de valider si un email
 * est déjà utilisé par un utilisateur
 *
 * @package AppBundle\Validator\Consraints
 */
class UtilisateurExistantValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $email = $this->em
            ->getRepository('AppBundle:Utilisateur')
            ->findOneBy(array(
                'email' => $value,
            ));
        if ($email) {
            $this->context->addViolation(
                $constraint->message,
                array('%email%' => $value)
            );
        }
    }
}