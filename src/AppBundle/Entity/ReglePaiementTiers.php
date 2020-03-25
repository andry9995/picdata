<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReglePaiementTiers
 *
 * @ORM\Table(name="regle_paiement_tiers", indexes={@ORM\Index(name="fk_regle_paiement_tiers_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_regle_paiement_tiers_tiers1_idx", columns={"tiers_id"})})
 * @ORM\Entity
 */
class ReglePaiementTiers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_date", type="integer", nullable=true)
     */
    private $typeDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_jour", type="integer", nullable=true)
     */
    private $nbreJour;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_le", type="integer", nullable=true)
     */
    private $dateLe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;



    /**
     * Set typeDate
     *
     * @param integer $typeDate
     *
     * @return ReglePaiementTiers
     */
    public function setTypeDate($typeDate)
    {
        $this->typeDate = $typeDate;

        return $this;
    }

    /**
     * Get typeDate
     *
     * @return integer
     */
    public function getTypeDate()
    {
        return $this->typeDate;
    }

    /**
     * Set nbreJour
     *
     * @param integer $nbreJour
     *
     * @return ReglePaiementTiers
     */
    public function setNbreJour($nbreJour)
    {
        $this->nbreJour = $nbreJour;

        return $this;
    }

    /**
     * Get nbreJour
     *
     * @return integer
     */
    public function getNbreJour()
    {
        return $this->nbreJour;
    }

    /**
     * Set dateLe
     *
     * @param integer $dateLe
     *
     * @return ReglePaiementTiers
     */
    public function setDateLe($dateLe)
    {
        $this->dateLe = $dateLe;

        return $this;
    }

    /**
     * Get dateLe
     *
     * @return integer
     */
    public function getDateLe()
    {
        return $this->dateLe;
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return ReglePaiementTiers
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return ReglePaiementTiers
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }
}
