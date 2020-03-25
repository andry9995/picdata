<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/02/2017
 * Time: 14:50
 */

namespace UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordRequestType extends AbstractType
{
    /**
     * Formulaire pour demande de changement mot de passe
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, array(
            'invalid_message' => 'Donner un email valide',
            'attr' => array(
                'label' => 'Email',
                'autofocus' => true
            ),
            'constraints' => array(
                new Assert\Email(),
            )
        ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}