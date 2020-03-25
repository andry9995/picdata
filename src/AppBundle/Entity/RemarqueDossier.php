<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemarqueDossier
 *
 * @ORM\Table(name="remarque_dossier", indexes={@ORM\Index(name="fk_remarque_dossier_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class RemarqueDossier
{
    /**
     * @var string
     *
     * @ORM\Column(name="information_dossier", type="text", length=65535, nullable=true)
     */
    private $informationDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="methode_comptable", type="text", length=65535, nullable=true)
     */
    private $methodeComptable;

    /**
     * @var string
     *
     * @ORM\Column(name="prestation_demande", type="text", length=65535, nullable=true)
     */
    private $prestationDemande;

    /**
     * @var string
     *
     * @ORM\Column(name="prestation_comptable", type="text", length=65535, nullable=true)
     */
    private $prestationComptable;

    /**
     * @var string
     *
     * @ORM\Column(name="piece_a_envoyer", type="text", length=65535, nullable=true)
     */
    private $pieceAEnvoyer;

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
     * Set informationDossier
     *
     * @param string $informationDossier
     *
     * @return RemarqueDossier
     */
    public function setInformationDossier($informationDossier)
    {
        $this->informationDossier = $informationDossier;

        return $this;
    }

    /**
     * Get informationDossier
     *
     * @return string
     */
    public function getInformationDossier()
    {
        return $this->informationDossier;
    }

    /**
     * Set methodeComptable
     *
     * @param string $methodeComptable
     *
     * @return RemarqueDossier
     */
    public function setMethodeComptable($methodeComptable)
    {
        $this->methodeComptable = $methodeComptable;

        return $this;
    }

    /**
     * Get methodeComptable
     *
     * @return string
     */
    public function getMethodeComptable()
    {
        return $this->methodeComptable;
    }

    /**
     * Set prestationDemande
     *
     * @param string $prestationDemande
     *
     * @return RemarqueDossier
     */
    public function setPrestationDemande($prestationDemande)
    {
        $this->prestationDemande = $prestationDemande;

        return $this;
    }

    /**
     * Get prestationDemande
     *
     * @return string
     */
    public function getPrestationDemande()
    {
        return $this->prestationDemande;
    }

    /**
     * Set prestationComptable
     *
     * @param string $prestationComptable
     *
     * @return RemarqueDossier
     */
    public function setPrestationComptable($prestationComptable)
    {
        $this->prestationComptable = $prestationComptable;

        return $this;
    }

    /**
     * Get prestationComptable
     *
     * @return string
     */
    public function getPrestationComptable()
    {
        return $this->prestationComptable;
    }

    /**
     * Set pieceAEnvoyer
     *
     * @param string $pieceAEnvoyer
     *
     * @return RemarqueDossier
     */
    public function setPieceAEnvoyer($pieceAEnvoyer)
    {
        $this->pieceAEnvoyer = $pieceAEnvoyer;

        return $this;
    }

    /**
     * Get pieceAEnvoyer
     *
     * @return string
     */
    public function getPieceAEnvoyer()
    {
        return $this->pieceAEnvoyer;
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
     * @return RemarqueDossier
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
