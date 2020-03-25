<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesSynchro
 *
 * @ORM\Table(name="taches_synchro", indexes={@ORM\Index(name="fk_taches_synchro_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_taches_synchro_taches_date_idx", columns={"taches_date_id"}), @ORM\Index(name="fk_taches_synchro_taches_libre_date_libre_date_idx", columns={"taches_libre_date_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesSynchroRepository")
 */
class TachesSynchro
{
    /**
     * @var string
     *
     * @ORM\Column(name="id_google", type="string", length=45, nullable=false)
     */
    private $idGoogle = 'NONE';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datefait", type="date", nullable=true)
     */
    private $datefait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesLibreDate
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesLibreDate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_libre_date_id", referencedColumnName="id")
     * })
     */
    private $tachesLibreDate;

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
     * Set idGoogle
     *
     * @param string $idGoogle
     *
     * @return TachesSynchro
     */
    public function setIdGoogle($idGoogle)
    {
        $this->idGoogle = $idGoogle;

        return $this;
    }

    /**
     * Get idGoogle
     *
     * @return string
     */
    public function getIdGoogle()
    {
        return $this->idGoogle;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TachesSynchro
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return TachesSynchro
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
     * Set datefait
     *
     * @param \DateTime $datefait
     *
     * @return TacheSynchro
     */
    public function setDatefait($datefait)
    {
        $this->datefait = $datefait;

        return $this;
    }

    /**
     * Get datefait
     *
     * @return \DateTime
     */
    public function getDatefait()
    {
        return $this->datefait;
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
     * Set tachesLibreDate
     *
     * @param \AppBundle\Entity\TachesLibreDate $tachesLibreDate
     *
     * @return TachesSynchro
     */
    public function setTachesLibreDate(\AppBundle\Entity\TachesLibreDate $tachesLibreDate = null)
    {
        $this->tachesLibreDate = $tachesLibreDate;

        return $this;
    }

    /**
     * Get tachesLibreDate
     *
     * @return \AppBundle\Entity\TachesLibreDate
     */
    public function getTachesLibreDate()
    {
        return $this->tachesLibreDate;
    }

    /**
     * Set tachesDate
     *
     * @param \AppBundle\Entity\TachesDate $tachesDate
     *
     * @return TachesSynchro
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
     * @return TachesSynchro
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
