<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Replication
 *
 * @ORM\Table(name="replication")
 * @ORM\Entity
 */
class Replication
{
    /**
     * @var string
     *
     * @ORM\Column(name="request", type="text", length=65535, nullable=false)
     */
    private $request;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_request", type="date", nullable=false)
     */
    private $dateRequest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_request", type="time", nullable=false)
     */
    private $heureRequest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_replication", type="date", nullable=true)
     */
    private $dateReplication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_replication", type="time", nullable=true)
     */
    private $heureReplication;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_erreur", type="date", nullable=true)
     */
    private $dateErreur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_erreur", type="time", nullable=true)
     */
    private $heureErreur;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set request
     *
     * @param string $request
     *
     * @return Replication
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set dateRequest
     *
     * @param \DateTime $dateRequest
     *
     * @return Replication
     */
    public function setDateRequest($dateRequest)
    {
        $this->dateRequest = $dateRequest;

        return $this;
    }

    /**
     * Get dateRequest
     *
     * @return \DateTime
     */
    public function getDateRequest()
    {
        return $this->dateRequest;
    }

    /**
     * Set heureRequest
     *
     * @param \DateTime $heureRequest
     *
     * @return Replication
     */
    public function setHeureRequest($heureRequest)
    {
        $this->heureRequest = $heureRequest;

        return $this;
    }

    /**
     * Get heureRequest
     *
     * @return \DateTime
     */
    public function getHeureRequest()
    {
        return $this->heureRequest;
    }

    /**
     * Set dateReplication
     *
     * @param \DateTime $dateReplication
     *
     * @return Replication
     */
    public function setDateReplication($dateReplication)
    {
        $this->dateReplication = $dateReplication;

        return $this;
    }

    /**
     * Get dateReplication
     *
     * @return \DateTime
     */
    public function getDateReplication()
    {
        return $this->dateReplication;
    }

    /**
     * Set heureReplication
     *
     * @param \DateTime $heureReplication
     *
     * @return Replication
     */
    public function setHeureReplication($heureReplication)
    {
        $this->heureReplication = $heureReplication;

        return $this;
    }

    /**
     * Get heureReplication
     *
     * @return \DateTime
     */
    public function getHeureReplication()
    {
        return $this->heureReplication;
    }

    /**
     * Set dateErreur
     *
     * @param \DateTime $dateErreur
     *
     * @return Replication
     */
    public function setDateErreur($dateErreur)
    {
        $this->dateErreur = $dateErreur;

        return $this;
    }

    /**
     * Get dateErreur
     *
     * @return \DateTime
     */
    public function getDateErreur()
    {
        return $this->dateErreur;
    }

    /**
     * Set heureErreur
     *
     * @param \DateTime $heureErreur
     *
     * @return Replication
     */
    public function setHeureErreur($heureErreur)
    {
        $this->heureErreur = $heureErreur;

        return $this;
    }

    /**
     * Get heureErreur
     *
     * @return \DateTime
     */
    public function getHeureErreur()
    {
        return $this->heureErreur;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Replication
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
