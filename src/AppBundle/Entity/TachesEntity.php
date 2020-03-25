<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesEntity
 *
 * @ORM\Table(name="taches_entity", indexes={@ORM\Index(name="fk_taches_entity_taches_date_idx", columns={"taches_date_id"}), @ORM\Index(name="fk_taches_entity_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class TachesEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="jour_additif", type="integer", nullable=false)
     */
    private $jourAdditif = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="responsable", type="integer", nullable=false)
     */
    private $responsable = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="responsable_updated", type="integer", nullable=false)
     */
    private $responsableUpdated = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesDate
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesDate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_date_id", referencedColumnName="id")
     * })
     */
    private $tachesDate;

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
     * @return TachesEntity
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
     * Set jourAdditif
     *
     * @param integer $jourAdditif
     *
     * @return TachesEntity
     */
    public function setJourAdditif($jourAdditif)
    {
        $this->jourAdditif = $jourAdditif;

        return $this;
    }

    /**
     * Get jourAdditif
     *
     * @return integer
     */
    public function getJourAdditif()
    {
        return $this->jourAdditif;
    }

    /**
     * Set responsable
     *
     * @param integer $responsable
     *
     * @return TachesEntity
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return integer
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set responsableUpdated
     *
     * @param integer $responsableUpdated
     *
     * @return TachesEntity
     */
    public function setResponsableUpdated($responsableUpdated)
    {
        $this->responsableUpdated = $responsableUpdated;

        return $this;
    }

    /**
     * Get responsableUpdated
     *
     * @return integer
     */
    public function getResponsableUpdated()
    {
        return $this->responsableUpdated;
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
     * Set tachesDate
     *
     * @param \AppBundle\Entity\TachesDate $tachesDate
     *
     * @return TachesEntity
     */
    public function setTachesDate(\AppBundle\Entity\TachesDate $tachesDate = null)
    {
        $this->tachesDate = $tachesDate;

        return $this;
    }

    /**
     * Get tachesDate
     *
     * @return \AppBundle\Entity\TachesDate
     */
    public function getTachesDate()
    {
        return $this->tachesDate;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TachesEntity
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
