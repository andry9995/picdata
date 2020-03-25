<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrancheEffectif
 *
 * @ORM\Table(name="tranche_effectif")
 * @ORM\Entity
 */
class TrancheEffectif
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_insee", type="string", length=45, nullable=false)
     */
    private $libelleInsee;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TrancheEffectif
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelleInsee
     *
     * @param string $libelleInsee
     *
     * @return TrancheEffectif
     */
    public function setLibelleInsee($libelleInsee)
    {
        $this->libelleInsee = $libelleInsee;

        return $this;
    }

    /**
     * Get libelleInsee
     *
     * @return string
     */
    public function getLibelleInsee()
    {
        return $this->libelleInsee;
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
