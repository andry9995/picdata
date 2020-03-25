<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aide1
 *
 * @ORM\Table(name="aide_1")
 * @ORM\Entity
 */
class Aide1
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=true)
     */
    private $rang;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=100, nullable=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_aide", type="integer", nullable=false)
     */
    private $typeAide = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return Aide1
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Aide1
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Aide1
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set typeAide
     *
     * @param integer $typeAide
     *
     * @return Aide1
     */
    public function setTypeAide($typeAide)
    {
        $this->typeAide = $typeAide;

        return $this;
    }

    /**
     * Get typeAide
     *
     * @return integer
     */
    public function getTypeAide()
    {
        return $this->typeAide;
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
