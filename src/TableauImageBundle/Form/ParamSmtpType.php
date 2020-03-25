<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 18/12/2017
 * Time: 17:23
 */

namespace TableauImageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ParamSmtpType extends AbstractType
{

    /**
     * Formulaire Parametrage Smtp par client
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smtp', TextType::class, array(
                'translation_domain' => 'label',
                'label' => 'label.smtp',
                'constraints' => array(new Assert\NotBlank(array(
                    'message' => "Le SMTP ne doit pas être vide",
                )))
            ))
        ->add('port', NumberType::class, array(
            'translation_domain' => 'label',
            'label' => 'label.smtp.port',
            'constraints' => array(new Assert\NotBlank(array(
                'message' => "Le Port ne doit pas être vide",
            )))
        ))
        ->add('login', TextType::class, array(
            'translation_domain' => 'label',
            'label' => 'label.smtp.login',
            'constraints' => array(new Assert\NotBlank(array(
                'message' => "Le Login ne doit pas être vide"
            )))
        ))
        ->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques.',
            'translation_domain' => 'label',
            'options' => array('attr' => array('class' => 'smtp-password')),
            'required' => true,
            'first_options' => array('label' => 'label.smtp.password'),
            'second_options' => array('label' => 'label.smtp.password.confirm'),
            'constraints' => array(
                new Assert\NotBlank(array(
                    'message' => "Le mot de passe ne doit pas être vide."
                )),
            ),
        ))
        ->add('certificate', ChoiceType::class, array(
            'translation_domain' => 'label',
            'choices' => array(
                '' => 'label.smtp.none',
                'tls' => 'label.smtp.tls',
                'ssl' => 'label.smtp.ssl'
            ),
            'label' => 'label.smtp.certificate'
        ))
        ->add('copie', TextType::class, array(
            'translation_domain' => 'label',
            'label' => 'label.smtp.copie',
        ))


        ;
    }
}