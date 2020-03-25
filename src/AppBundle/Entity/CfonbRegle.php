<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CfonbRegle
 *
 * @ORM\Table(name="cfonb_regle", indexes={@ORM\Index(name="fk_cfonb_regle_cfonb_activation_id_idx", columns={"cfonb_activation_id"})})
 * @ORM\Entity
 */
class CfonbRegle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="debut", type="integer", nullable=true)
     */
    private $debut;

    /**
     * @var integer
     *
     * @ORM\Column(name="fin", type="integer", nullable=true)
     */
    private $fin;

    /**
     * @var integer
     *
     * @ORM\Column(name="longueur", type="integer", nullable=true)
     */
    private $longueur;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\CfonbActivation
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CfonbActivation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cfonb_activation_id", referencedColumnName="id")
     * })
     */
    private $cfonbActivation;



    /**
     * Set debut
     *
     * @param integer $debut
     *
     * @return CfonbRegle
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return integer
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param integer $fin
     *
     * @return CfonbRegle
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return integer
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set longueur
     *
     * @param integer $longueur
     *
     * @return CfonbRegle
     */
    public function setLongueur($longueur)
    {
        $this->longueur = $longueur;

        return $this;
    }

    /**
     * Get longueur
     *
     * @return integer
     */
    public function getLongueur()
    {
        return $this->longueur;
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
     * Set cfonbActivation
     *
     * @param \AppBundle\Entity\CfonbActivation $cfonbActivation
     *
     * @return CfonbRegle
     */
    public function setCfonbActivation(\AppBundle\Entity\CfonbActivation $cfonbActivation = null)
    {
        $this->cfonbActivation = $cfonbActivation;

        return $this;
    }

    /**
     * Get cfonbActivation
     *
     * @return \AppBundle\Entity\CfonbActivation
     */
    public function getCfonbActivation()
    {
        return $this->cfonbActivation;
    }
}
