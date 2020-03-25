<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationVersion
 *
 * @ORM\Table(name="application_version")
 * @ORM\Entity
 */
class ApplicationVersion
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=10, nullable=false)
     */
    private $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_version", type="date", nullable=false)
     */
    private $dateVersion;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="text", length=65535, nullable=true)
     */
    private $remarque;

    /**
     * @var string
     *
     * @ORM\Column(name="chemin", type="text", length=65535, nullable=true)
     */
    private $chemin;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return ApplicationVersion
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return ApplicationVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set dateVersion
     *
     * @param \DateTime $dateVersion
     *
     * @return ApplicationVersion
     */
    public function setDateVersion($dateVersion)
    {
        $this->dateVersion = $dateVersion;

        return $this;
    }

    /**
     * Get dateVersion
     *
     * @return \DateTime
     */
    public function getDateVersion()
    {
        return $this->dateVersion;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ApplicationVersion
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
     * Set remarque
     *
     * @param string $remarque
     *
     * @return ApplicationVersion
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
     * Set chemin
     *
     * @param string $chemin
     *
     * @return ApplicationVersion
     */
    public function setChemin($chemin)
    {
        $this->chemin = $chemin;

        return $this;
    }

    /**
     * Get chemin
     *
     * @return string
     */
    public function getChemin()
    {
        return $this->chemin;
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
