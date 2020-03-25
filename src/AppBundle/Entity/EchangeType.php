<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EchangeType
 *
 * @ORM\Table(name="echange_type", uniqueConstraints={@ORM\UniqueConstraint(name="nom_UNIQUE", columns={"nom"})})
 * @ORM\Entity
 */
class EchangeType
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reponse", type="boolean", nullable=false)
     */
    private $reponse = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return EchangeType
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set reponse
     *
     * @param boolean $reponse
     *
     * @return EchangeType
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return boolean
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
