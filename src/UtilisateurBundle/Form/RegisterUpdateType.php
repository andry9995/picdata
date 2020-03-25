<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 15/02/2017
 * Time: 15:33
 */

namespace UtilisateurBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUpdateType extends AbstractType
{
    /**
     * Formulaire pour mettre à jour un utilisateur
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
                'translation_domain' => 'label',
                'label' => 'label.nom_famille',
                'attr' => array(
                    'placeholder' => 'label.nom_famille'
                ),
                'constraints' => array(new Assert\NotBlank(array(
                    'message' => "Le nom ne doit pas être vide"
                )))
            ))
            ->add('prenom', TextType::class, array(
                'translation_domain' => 'label',
                'label' => "label.prenom",
                'attr' => array(
                    'placeholder' => "label.prenom"
                ),
                'required' => false,
            ))
            ->add('email', EmailType::class, array(
                'translation_domain' => 'label',
                'label' => "label.email",
                'attr' => array(
                    'placeholder' => "label.email"
                ),
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => "L'email ne doit pas être vide"
                    )),
                    new Assert\Email(array(
                        'message' => "Cette email n'est pas valide",
                    )),
                )
            ))
            ->add('societe', TextType::class, array(
                'translation_domain' => 'label',
                'label' => "label.societe",
                'attr' => array(
                    'placeholder' => "label.societe"
                ),
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => "La société ne doit pas être vide"
                    )),
                ),
                'required' => false,
            ))
            ->add('telephone', TextType::class, array(
                'translation_domain' => 'label',
                'label' => "label.telephone",
                'attr' => array(
                    'placeholder' => "label.telephone"
                ),
                'required' => false,
            ))
            ->add('skype', TextType::class, array(
                'translation_domain' => 'label',
                'label' => "label.skype",
                'attr' => array(
                    'placeholder' => "label.skype"
                ),
                'required' => false,
            ));
    }
}