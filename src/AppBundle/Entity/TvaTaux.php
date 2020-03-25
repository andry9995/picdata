<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaTaux
 *
 * @ORM\Table(name="tva_taux")
 * @ORM\Entity
 */
class TvaTaux
{
    /**
     * @var float
     *
     * @ORM\Column(name="taux", type="float", precision=10, scale=0, nullable=false)
     */
    private $taux;

    /**
     * @var integer
     *
     * @ORM\Column(name="actif", type="integer", nullable=false)
     */
    private $actif = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set taux
     *
     * @param float $taux
     *
     * @return TvaTaux
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
     * @param integer $actif
     *
     * @return TvaTaux
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return integer
     */
    public function getActif()
    {
        return $this->actif;
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
