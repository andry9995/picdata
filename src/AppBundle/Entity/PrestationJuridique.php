<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationJuridique
 *
 * @ORM\Table(name="prestation_juridique", indexes={@ORM\Index(name="fk_prestation_juridique_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationJuridiqueRepository")
 */
class PrestationJuridique
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ass_ord_annuelle", type="integer", nullable=true)
     */
    private $assOrdAnnuelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="rapport_gestion", type="integer", nullable=true)
     */
    private $rapportGestion;

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
     * Set assOrdAnnuelle
     *
     * @param integer $assOrdAnnuelle
     *
     * @return PrestationJuridique
     */
    public function setAssOrdAnnuelle($assOrdAnnuelle)
    {
        $this->assOrdAnnuelle = $assOrdAnnuelle;

        return $this;
    }

    /**
     * Get assOrdAnnuelle
     *
     * @return integer
     */
    public function getAssOrdAnnuelle()
    {
        return $this->assOrdAnnuelle;
    }

    /**
     * Set rapportGestion
     *
     * @param integer $rapportGestion
     *
     * @return PrestationJuridique
     */
    public function setRapportGestion($rapportGestion)
    {
        $this->rapportGestion = $rapportGestion;

        return $this;
    }

    /**
     * Get rapportGestion
     *
     * @return integer
     */
    public function getRapportGestion()
    {
        return $this->rapportGestion;
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
     * @return PrestationJuridique
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
}
