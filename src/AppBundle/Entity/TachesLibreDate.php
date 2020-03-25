<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesLibreDate
 *
 * @ORM\Table(name="taches_libre_date", indexes={@ORM\Index(name="fk_taches_libre_date_taches_libre_idx", columns={"taches_libre_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesLibreDateRepository")
 */
class TachesLibreDate
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demarrage", type="date", nullable=true)
     */
    private $demarrage;

    /**
     * @var integer
     *
     * @ORM\Column(name="periode", type="integer", nullable=false)
     */
    private $periode;

    /**
     * @var integer
     *
     * @ORM\Column(name="jour", type="integer", nullable=false)
     */
    private $jour = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="calculer_a_partir", type="integer", nullable=false)
     */
    private $calculerAPartir = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_calcul", type="date", nullable=true)
     */
    private $dateCalcul;

    /**
     * @var integer
     *
     * @ORM\Column(name="jour_semaine", type="integer", nullable=true)
     */
    private $jourSemaine = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="jalon", type="integer", nullable=false)
     */
    private $jalon = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="additif_jour", type="integer", nullable=false)
     */
    private $additifJour = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="mois_additif", type="integer", nullable=false)
     */
    private $moisAdditif = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesLibre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesLibre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_libre_id", referencedColumnName="id")
     * })
     */
    private $tachesLibre;



    /**
     * Set demarrage
     *
     * @param \DateTime $demarrage
     *
     * @return TachesLibreDate
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
     * Set periode
     *
     * @param integer $periode
     *
     * @return TachesLibreDate
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return integer
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set jour
     *
     * @param integer $jour
     *
     * @return TachesLibreDate
     */
    public function setJour($jour)
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return integer
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set calculerAPartir
     *
     * @param integer $calculerAPartir
     *
     * @return TachesLibreDate
     */
    public function setCalculerAPartir($calculerAPartir)
    {
        $this->calculerAPartir = $calculerAPartir;

        return $this;
    }

    /**
     * Get calculerAPartir
     *
     * @return integer
     */
    public function getCalculerAPartir()
    {
        return $this->calculerAPartir;
    }

    /**
     * Set dateCalcul
     *
     * @param \DateTime $dateCalcul
     *
     * @return TachesLibreDate
     */
    public function setDateCalcul($dateCalcul)
    {
        $this->dateCalcul = $dateCalcul;

        return $this;
    }

    /**
     * Get dateCalcul
     *
     * @return \DateTime
     */
    public function getDateCalcul()
    {
        return $this->dateCalcul;
    }

    /**
     * Set jourSemaine
     *
     * @param integer $jourSemaine
     *
     * @return TachesLibreDate
     */
    public function setJourSemaine($jourSemaine)
    {
        $this->jourSemaine = $jourSemaine;

        return $this;
    }

    /**
     * Get jourSemaine
     *
     * @return integer
     */
    public function getJourSemaine()
    {
        return $this->jourSemaine;
    }

    /**
     * Set jalon
     *
     * @param integer $jalon
     *
     * @return TachesLibreDate
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
     * Set additifJour
     *
     * @param integer $additifJour
     *
     * @return TachesLibreDate
     */
    public function setAdditifJour($additifJour)
    {
        $this->additifJour = $additifJour;

        return $this;
    }

    /**
     * Get additifJour
     *
     * @return integer
     */
    public function getAdditifJour()
    {
        return $this->additifJour;
    }

    /**
     * Set moisAdditif
     *
     * @param integer $moisAdditif
     *
     * @return TachesLibreDate
     */
    public function setMoisAdditif($moisAdditif)
    {
        $this->moisAdditif = $moisAdditif;

        return $this;
    }

    /**
     * Get moisAdditif
     *
     * @return integer
     */
    public function getMoisAdditif()
    {
        return $this->moisAdditif;
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
     * Set tachesLibre
     *
     * @param \AppBundle\Entity\TachesLibre $tachesLibre
     *
     * @return TachesLibreDate
     */
    public function setTachesLibre(\AppBundle\Entity\TachesLibre $tachesLibre = null)
    {
        $this->tachesLibre = $tachesLibre;

        return $this;
    }

    /**
     * Get tachesLibre
     *
     * @return \AppBundle\Entity\TachesLibre
     */
    public function getTachesLibre()
    {
        return $this->tachesLibre;
    }
}
