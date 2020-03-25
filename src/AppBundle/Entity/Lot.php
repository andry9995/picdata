<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lot
 *
 * @ORM\Table(name="lot", indexes={@ORM\Index(name="fk_lot_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_lot_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_lot_code_analytique1_idx", columns={"code_analytique_id"}), @ORM\Index(name="fk_lot_lot_group1_idx", columns={"lot_group_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LotRepository")
 */
class Lot
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_scan", type="date", nullable=false)
     */
    private $dateScan;

    /**
     * @var integer
     *
     * @ORM\Column(name="lot", type="integer", nullable=false)
     */
    private $lot = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="priorite", type="integer", nullable=false)
     */
    private $priorite = '100';

    /**
     * @var string
     *
     * @ORM\Column(name="message_urgent", type="string", length=150, nullable=true)
     */
    private $messageUrgent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_telechargement", type="date", nullable=true)
     */
    private $dateTelechargement;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var \AppBundle\Entity\LotGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LotGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_group_id", referencedColumnName="id")
     * })
     */
    private $lotGroup;

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
     * @var \AppBundle\Entity\CodeAnalytique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CodeAnalytique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_analytique_id", referencedColumnName="id")
     * })
     */
    private $codeAnalytique;



    /**
     * Set dateScan
     *
     * @param \DateTime $dateScan
     *
     * @return Lot
     */
    public function setDateScan($dateScan)
    {
        $this->dateScan = $dateScan;

        return $this;
    }

    /**
     * Get dateScan
     *
     * @return \DateTime
     */
    public function getDateScan()
    {
        return $this->dateScan;
    }

    /**
     * Set lot
     *
     * @param integer $lot
     *
     * @return Lot
     */
    public function setLot($lot)
    {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return integer
     */
    public function getLot()
    {
        return $this->lot;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Lot
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return Lot
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return integer
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set messageUrgent
     *
     * @param string $messageUrgent
     *
     * @return Lot
     */
    public function setMessageUrgent($messageUrgent)
    {
        $this->messageUrgent = $messageUrgent;

        return $this;
    }

    /**
     * Get messageUrgent
     *
     * @return string
     */
    public function getMessageUrgent()
    {
        return $this->messageUrgent;
    }

    /**
     * Set dateTelechargement
     *
     * @param \DateTime $dateTelechargement
     *
     * @return Lot
     */
    public function setDateTelechargement($dateTelechargement)
    {
        $this->dateTelechargement = $dateTelechargement;

        return $this;
    }

    /**
     * Get dateTelechargement
     *
     * @return \DateTime
     */
    public function getDateTelechargement()
    {
        return $this->dateTelechargement;
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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return Lot
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set lotGroup
     *
     * @param \AppBundle\Entity\LotGroup $lotGroup
     *
     * @return Lot
     */
    public function setLotGroup(\AppBundle\Entity\LotGroup $lotGroup = null)
    {
        $this->lotGroup = $lotGroup;

        return $this;
    }

    /**
     * Get lotGroup
     *
     * @return \AppBundle\Entity\LotGroup
     */
    public function getLotGroup()
    {
        return $this->lotGroup;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Lot
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
     * Set codeAnalytique
     *
     * @param \AppBundle\Entity\CodeAnalytique $codeAnalytique
     *
     * @return Lot
     */
    public function setCodeAnalytique(\AppBundle\Entity\CodeAnalytique $codeAnalytique = null)
    {
        $this->codeAnalytique = $codeAnalytique;

        return $this;
    }

    /**
     * Get codeAnalytique
     *
     * @return \AppBundle\Entity\CodeAnalytique
     */
    public function getCodeAnalytique()
    {
        return $this->codeAnalytique;
    }
}
