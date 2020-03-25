<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TacheDetail
 *
 * @ORM\Table(name="tache_detail", indexes={@ORM\Index(name="fk_tache_detail_tache_dossier1_idx", columns={"tache_dossier_id"}), @ORM\Index(name="fk_tache_detail_tache_status1_idx", columns={"tache_status_id"})})
 * @ORM\Entity
 */
class TacheDetail
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_tache", type="date", nullable=false)
     */
    private $dateTache;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_termine", type="date", nullable=true)
     */
    private $dateTermine;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TacheStatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TacheStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_status_id", referencedColumnName="id")
     * })
     */
    private $tacheStatus;

    /**
     * @var \AppBundle\Entity\TacheDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TacheDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_dossier_id", referencedColumnName="id")
     * })
     */
    private $tacheDossier;



    /**
     * Set dateTache
     *
     * @param \DateTime $dateTache
     *
     * @return TacheDetail
     */
    public function setDateTache($dateTache)
    {
        $this->dateTache = $dateTache;

        return $this;
    }

    /**
     * Get dateTache
     *
     * @return \DateTime
     */
    public function getDateTache()
    {
        return $this->dateTache;
    }

    /**
     * Set dateTermine
     *
     * @param \DateTime $dateTermine
     *
     * @return TacheDetail
     */
    public function setDateTermine($dateTermine)
    {
        $this->dateTermine = $dateTermine;

        return $this;
    }

    /**
     * Get dateTermine
     *
     * @return \DateTime
     */
    public function getDateTermine()
    {
        return $this->dateTermine;
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
     * Set tacheStatus
     *
     * @param \AppBundle\Entity\TacheStatus $tacheStatus
     *
     * @return TacheDetail
     */
    public function setTacheStatus(\AppBundle\Entity\TacheStatus $tacheStatus = null)
    {
        $this->tacheStatus = $tacheStatus;

        return $this;
    }

    /**
     * Get tacheStatus
     *
     * @return \AppBundle\Entity\TacheStatus
     */
    public function getTacheStatus()
    {
        return $this->tacheStatus;
    }

    /**
     * Set tacheDossier
     *
     * @param \AppBundle\Entity\TacheDossier $tacheDossier
     *
     * @return TacheDetail
     */
    public function setTacheDossier(\AppBundle\Entity\TacheDossier $tacheDossier = null)
    {
        $this->tacheDossier = $tacheDossier;

        return $this;
    }

    /**
     * Get tacheDossier
     *
     * @return \AppBundle\Entity\TacheDossier
     */
    public function getTacheDossier()
    {
        return $this->tacheDossier;
    }
}
