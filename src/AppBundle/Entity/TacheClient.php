<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TacheClient
 *
 * @ORM\Table(name="tache_client", indexes={@ORM\Index(name="fk_tache_client_tache_liste1_idx", columns={"tache_id"}), @ORM\Index(name="fk_tache_client_client1_idx", columns={"client_id"})})
 * @ORM\Entity
 */
class TacheClient
{
    /**
     * @var string
     *
     * @ORM\Column(name="periode", type="string", length=5, nullable=false)
     */
    private $periode;

    /**
     * @var integer
     *
     * @ORM\Column(name="mois", type="integer", nullable=true)
     */
    private $mois;

    /**
     * @var integer
     *
     * @ORM\Column(name="mois_plus", type="integer", nullable=false)
     */
    private $moisPlus = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="plus_tard", type="integer", nullable=false)
     */
    private $plusTard;

    /**
     * @var integer
     *
     * @ORM\Column(name="realiser_avant", type="integer", nullable=false)
     */
    private $realiserAvant = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="responsable", type="integer", nullable=false)
     */
    private $responsable = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="jalon", type="integer", nullable=false)
     */
    private $jalon = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demarrage", type="date", nullable=false)
     */
    private $demarrage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Tache
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tache")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_id", referencedColumnName="id")
     * })
     */
    private $tache;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set periode
     *
     * @param string $periode
     *
     * @return TacheClient
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return string
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set mois
     *
     * @param integer $mois
     *
     * @return TacheClient
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return integer
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * Set moisPlus
     *
     * @param integer $moisPlus
     *
     * @return TacheClient
     */
    public function setMoisPlus($moisPlus)
    {
        $this->moisPlus = $moisPlus;

        return $this;
    }

    /**
     * Get moisPlus
     *
     * @return integer
     */
    public function getMoisPlus()
    {
        return $this->moisPlus;
    }

    /**
     * Set plusTard
     *
     * @param integer $plusTard
     *
     * @return TacheClient
     */
    public function setPlusTard($plusTard)
    {
        $this->plusTard = $plusTard;

        return $this;
    }

    /**
     * Get plusTard
     *
     * @return integer
     */
    public function getPlusTard()
    {
        return $this->plusTard;
    }

    /**
     * Set realiserAvant
     *
     * @param integer $realiserAvant
     *
     * @return TacheClient
     */
    public function setRealiserAvant($realiserAvant)
    {
        $this->realiserAvant = $realiserAvant;

        return $this;
    }

    /**
     * Get realiserAvant
     *
     * @return integer
     */
    public function getRealiserAvant()
    {
        return $this->realiserAvant;
    }

    /**
     * Set responsable
     *
     * @param integer $responsable
     *
     * @return TacheClient
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
     * Set jalon
     *
     * @param integer $jalon
     *
     * @return TacheClient
     */
    public function setJalon($jalon)
    {
        $this->jalon = $jalon;

        return $this;
    }

    /**
     * Get jalon
     *
     * @return integer
     */
    public function getJalon()
    {
        return $this->jalon;
    }

    /**
     * Set demarrage
     *
     * @param \DateTime $demarrage
     *
     * @return TacheClient
     */
    public function setDemarrage($demarrage)
    {
        $this->demarrage = $demarrage;

        return $this;
    }

    /**
     * Get demarrage
     *
     * @return \DateTime
     */
    public function getDemarrage()
    {
        return $this->demarrage;
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
     * Set tache
     *
     * @param \AppBundle\Entity\Tache $tache
     *
     * @return TacheClient
     */
    public function setTache(\AppBundle\Entity\Tache $tache = null)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return \AppBundle\Entity\Tache
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return TacheClient
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
