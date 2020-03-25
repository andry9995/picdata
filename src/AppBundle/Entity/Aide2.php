<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aide2
 *
 * @ORM\Table(name="aide_2", indexes={@ORM\Index(name="fk_aide_2_aide_1_idx", columns={"aide_1_id"})})
 * @ORM\Entity
 */
class Aide2
{
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
     * @ORM\Column(name="rang", type="integer", nullable=true)
     */
    private $rang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Aide1
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide1")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_1_id", referencedColumnName="id")
     * })
     */
    private $aide1;



    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Aide2
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
     * @return Aide2
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
     * Set rang
     *
     * @param integer $rang
     *
     * @return Aide2
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set aide1
     *
     * @param \AppBundle\Entity\Aide1 $aide1
     *
     * @return Aide2
     */
    public function setAide1(\AppBundle\Entity\Aide1 $aide1 = null)
    {
        $this->aide1 = $aide1;

        return $this;
    }

    /**
     * Get aide1
     *
     * @return \AppBundle\Entity\Aide1
     */
    public function getAide1()
    {
        return $this->aide1;
    }
}
