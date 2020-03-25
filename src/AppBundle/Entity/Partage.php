<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partage
 *
 * @ORM\Table(name="partage", indexes={@ORM\Index(name="fk_partage_priorite1_idx", columns={"priorite_id"}), @ORM\Index(name="fk_partage_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_partage_operateur1_idx", columns={"operateur_id"}), @ORM\Index(name="fk_partage_operateur2_idx", columns={"operateur_id1"})})
 * @ORM\Entity
 */
class Partage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_scan", type="date", nullable=false)
     */
    private $dateScan;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Priorite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Priorite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="priorite_id", referencedColumnName="id")
     * })
     */
    private $priorite;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id1", referencedColumnName="id")
     * })
     */
    private $operateur1;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     * })
     */
    private $operateur;

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
     * Set status
     *
     * @param integer $status
     *
     * @return Partage
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
     * Set dateScan
     *
     * @param \DateTime $dateScan
     *
     * @return Partage
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set priorite
     *
     * @param \AppBundle\Entity\Priorite $priorite
     *
     * @return Partage
     */
    public function setPriorite(\AppBundle\Entity\Priorite $priorite = null)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return \AppBundle\Entity\Priorite
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set operateur1
     *
     * @param \AppBundle\Entity\Operateur $operateur1
     *
     * @return Partage
     */
    public function setOperateur1(\AppBundle\Entity\Operateur $operateur1 = null)
    {
        $this->operateur1 = $operateur1;

        return $this;
    }

    /**
     * Get operateur1
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur1()
    {
        return $this->operateur1;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Partage
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Partage
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
