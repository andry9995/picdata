<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TvaEcheance
 *
 * @ORM\Table(name="tva_echeance")
 * @ORM\Entity
 */
class TvaEcheance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="echeance", type="integer", nullable=false)
     */
    private $echeance;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set echeance
     *
     * @param integer $echeance
     *
     * @return TvaEcheance
     */
    public function setEcheance($echeance)
    {
        $this->echeance = $echeance;

        return $this;
    }

    /**
     * Get echeance
     *
     * @return integer
     */
    public function getEcheance()
    {
        return $this->echeance;
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
