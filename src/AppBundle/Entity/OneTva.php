<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneTva
 *
 * @ORM\Table(name="one_tva")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneTvaRepository")
 */
class OneTva
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="taux", type="float")
     */
    private $taux;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


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
     * Set taux
     *
     * @param float $taux
     *
     * @return OneTva
     */
    public function setTaux($taux)
    {
        $this->taux = $taux;
    
        return $this;
    }

    /**
     * Get taux
     *
     * @return float
     */
    public function getTaux()
    {
        return $this->taux;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return OneTva
     */
    public function setActif($actif)
    {
        $this->actif = $actif;
    
        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }
}

