<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartageStatus
 *
 * @ORM\Table(name="partage_status", indexes={@ORM\Index(name="fk_partage_status_partage1_idx", columns={"partage_id"})})
 * @ORM\Entity
 */
class PartageStatus
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
     * @ORM\Column(name="date_status", type="date", nullable=false)
     */
    private $dateStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="text", length=65535, nullable=true)
     */
    private $remarque;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Partage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="partage_id", referencedColumnName="id")
     * })
     */
    private $partage;



    /**
     * Set status
     *
     * @param integer $status
     *
     * @return PartageStatus
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
     * Set dateStatus
     *
     * @param \DateTime $dateStatus
     *
     * @return PartageStatus
     */
    public function setDateStatus($dateStatus)
    {
        $this->dateStatus = $dateStatus;

        return $this;
    }

    /**
     * Get dateStatus
     *
     * @return \DateTime
     */
    public function getDateStatus()
    {
        return $this->dateStatus;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     *
     * @return PartageStatus
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string
     */
    public function getRemarque()
    {
        return $this->remarque;
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
     * Set partage
     *
     * @param \AppBundle\Entity\Partage $partage
     *
     * @return PartageStatus
     */
    public function setPartage(\AppBundle\Entity\Partage $partage = null)
    {
        $this->partage = $partage;

        return $this;
    }

    /**
     * Get partage
     *
     * @return \AppBundle\Entity\Partage
     */
    public function getPartage()
    {
        return $this->partage;
    }
}
