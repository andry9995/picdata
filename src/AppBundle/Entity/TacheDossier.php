<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TacheDossier
 *
 * @ORM\Table(name="tache_dossier", indexes={@ORM\Index(name="fk_tache_client_tache_liste1_idx", columns={"tache_id"}), @ORM\Index(name="fk_tache_dossier_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class TacheDossier
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_scan", type="date", nullable=true)
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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

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
     * Set periode
     *
     * @param string $periode
     *
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * @return TacheDossier
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
     * Set dateScan
     *
     * @param \DateTime $dateScan
     *
     * @return TacheDossier
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TacheDossier
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
     * Set tache
     *
     * @param \AppBundle\Entity\Tache $tache
     *
     * @return TacheDossier
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
}
