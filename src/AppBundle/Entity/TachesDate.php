<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesDate
 *
 * @ORM\Table(name="taches_date", indexes={@ORM\Index(name="fk_tables_date_tache_action_idx", columns={"taches_action_id"}), @ORM\Index(name="fk_tables_date_client_idx", columns={"client_id"}), @ORM\Index(name="fk_tables_date_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesDateRepository")
 */
class TachesDate
{
    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=150, nullable=false)
     */
    private $formule;

    /**
     * @var integer
     *
     * @ORM\Column(name="periode", type="integer", nullable=false)
     */
    private $periode = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="infoperdos", type="integer", nullable=false)
     */
    private $infoperdos = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="jour", type="integer", nullable=false)
     */
    private $jour = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="clotures", type="string", length=150, nullable=false)
     */
    private $clotures = '12';

    /**
     * @var string
     *
     * @ORM\Column(name="negations", type="string", length=150, nullable=false)
     */
    private $negations = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="mois", type="integer", nullable=false)
     */
    private $mois = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesAction
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_action_id", referencedColumnName="id")
     * })
     */
    private $tachesAction;

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return TachesDate
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set periode
     *
     * @param integer $periode
     *
     * @return TachesDate
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
     * Set infoperdos
     *
     * @param integer $infoperdos
     *
     * @return TachesDate
     */
    public function setInfoperdos($infoperdos)
    {
        $this->infoperdos = $infoperdos;

        return $this;
    }

    /**
     * Get infoperdos
     *
     * @return integer
     */
    public function getInfoperdos()
    {
        return $this->infoperdos;
    }

    /**
     * Set jour
     *
     * @param integer $jour
     *
     * @return TachesDate
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
     * Set clotures
     *
     * @param string $clotures
     *
     * @return TachesDate
     */
    public function setClotures($clotures)
    {
        $this->clotures = $clotures;

        return $this;
    }

    /**
     * Get clotures
     *
     * @return string
     */
    public function getClotures()
    {
        return $this->clotures;
    }

    /**
     * Set negations
     *
     * @param string $negations
     *
     * @return TachesDate
     */
    public function setNegations($negations)
    {
        $this->negations = $negations;

        return $this;
    }

    /**
     * Get negations
     *
     * @return string
     */
    public function getNegations()
    {
        return $this->negations;
    }

    /**
     * Set mois
     *
     * @param integer $mois
     *
     * @return TachesDate
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tachesAction
     *
     * @param \AppBundle\Entity\TachesAction $tachesAction
     *
     * @return TachesDate
     */
    public function setTachesAction(\AppBundle\Entity\TachesAction $tachesAction = null)
    {
        $this->tachesAction = $tachesAction;

        return $this;
    }

    /**
     * Get tachesAction
     *
     * @return \AppBundle\Entity\TachesAction
     */
    public function getTachesAction()
    {
        return $this->tachesAction;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TachesDate
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return TachesDate
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
