<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneNotification
 *
 * @ORM\Table(name="one_notification", indexes={@ORM\Index(name="fk_one_notification_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class OneNotification
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
     * @ORM\Column(name="opportunite", type="integer", nullable=true)
     */
    private $opportunite;

    /**
     * @var integer
     *
     * @ORM\Column(name="opportunite_delais", type="integer", nullable=true)
     */
    private $opportuniteDelais;

    /**
     * @var integer
     *
     * @ORM\Column(name="opportunite_delais_type", type="integer", nullable=true)
     */
    private $opportuniteDelaisType;

    /**
     * @var integer
     *
     * @ORM\Column(name="opportunite_avant", type="integer", nullable=true)
     */
    private $opportuniteAvant;

    /**
     * @var integer
     *
     * @ORM\Column(name="tache", type="integer", nullable=true)
     */
    private $tache;

    /**
     * @var integer
     *
     * @ORM\Column(name="tache_delais", type="integer", nullable=true)
     */
    private $tacheDelais;

    /**
     * @var integer
     *
     * @ORM\Column(name="tache_delais_type", type="integer", nullable=true)
     */
    private $tacheDelaisType;


    /**
     * @var integer
     *
     * @ORM\Column(name="tache_avant", type="integer", nullable=true)
     */
    private $tacheAvant;
    /**
     * @var integer
     *
     * @ORM\Column(name="appel", type="integer", nullable=true)
     */
    private $appel;

    /**
     * @var integer
     *
     * @ORM\Column(name="appel_delais", type="integer", nullable=true)
     */
    private $appelDelais;

    /**
     * @var integer
     *
     * @ORM\Column(name="appel_delais_type", type="integer", nullable=true)
     */
    private $appelDelaisType;


    /**
     * @var integer
     *
     * @ORM\Column(name="appel_avant", type="integer", nullable=true)
     */
    private $appelAvant;

    /**
     * @var integer
     *
     * @ORM\Column(name="paiement", type="integer", nullable=true)
     */
    private $paiement;

    /**
     * @var integer
     *
     * @ORM\Column(name="paiement_delais", type="integer", nullable=true)
     */
    private $paiementDelais;

    /**
     * @var integer
     *
     * @ORM\Column(name="paiement_delais_type", type="integer", nullable=true)
     */
    private $paiementDelaisType;


    /**
     * @var integer
     *
     * @ORM\Column(name="paiement_avant", type="integer", nullable=true)
     */
    private $paiementAvant;

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
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;



    /**
     * Set status
     *
     * @param integer $status
     *
     * @return OneNotification
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
     * Set opportunite
     *
     * @param integer $opportunite
     *
     * @return OneNotification
     */
    public function setOpportunite($opportunite)
    {
        $this->opportunite = $opportunite;

        return $this;
    }

    /**
     * Get opportunite
     *
     * @return integer
     */
    public function getOpportunite()
    {
        return $this->opportunite;
    }

    /**
     * Set opportuniteDelais
     *
     * @param integer $opportuniteDelais
     *
     * @return OneNotification
     */
    public function setOpportuniteDelais($opportuniteDelais)
    {
        $this->opportuniteDelais = $opportuniteDelais;

        return $this;
    }

    /**
     * Get opportuniteDelais
     *
     * @return integer
     */
    public function getOpportuniteDelais()
    {
        return $this->opportuniteDelais;
    }

    /**
     * Set opportuniteDelaisType
     *
     * @param integer $opportuniteDelaisType
     *
     * @return OneNotification
     */
    public function setOpportuniteDelaisType($opportuniteDelaisType)
    {
        $this->opportuniteDelaisType = $opportuniteDelaisType;

        return $this;
    }

    /**
     * Get opportuniteDelaisType
     *
     * @return integer
     */
    public function getOpportuniteDelaisType()
    {
        return $this->opportuniteDelaisType;
    }

    /**
     * @param $opportuniteAvant
     * @return $this
     *
     */
    public function setOpportuniteAvant($opportuniteAvant){
        $this->opportuniteAvant = $opportuniteAvant;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpportuniteAvant(){
        return $this->opportuniteAvant;
    }

    /**
     * Set tache
     *
     * @param integer $tache
     *
     * @return OneNotification
     */
    public function setTache($tache)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return integer
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set tacheDelais
     *
     * @param integer $tacheDelais
     *
     * @return OneNotification
     */
    public function setTacheDelais($tacheDelais)
    {
        $this->tacheDelais = $tacheDelais;

        return $this;
    }

    /**
     * Get tacheDelais
     *
     * @return integer
     */
    public function getTacheDelais()
    {
        return $this->tacheDelais;
    }

    /**
     * Set tacheDelaisType
     *
     * @param integer $tacheDelaisType
     *
     * @return OneNotification
     */
    public function setTacheDelaisType($tacheDelaisType)
    {
        $this->tacheDelaisType = $tacheDelaisType;

        return $this;
    }

    /**
     * Get tacheDelaisType
     *
     * @return integer
     */
    public function getTacheDelaisType()
    {
        return $this->tacheDelaisType;
    }

    /**
     * @param $tacheAvant
     * @return $this
     */
    public function setTacheAvant($tacheAvant)
    {
        $this->tacheAvant = $tacheAvant;

        return $this;
    }

    /**
     * @return int
     */
    public function getTacheAvant()
    {
        return $this->tacheAvant;
    }

    /**
     * Set appel
     *
     * @param integer $appel
     *
     * @return OneNotification
     */
    public function setAppel($appel)
    {
        $this->appel = $appel;

        return $this;
    }

    /**
     * Get appel
     *
     * @return integer
     */
    public function getAppel()
    {
        return $this->appel;
    }

    /**
     * Set appelDelais
     *
     * @param integer $appelDelais
     *
     * @return OneNotification
     */
    public function setAppelDelais($appelDelais)
    {
        $this->appelDelais = $appelDelais;

        return $this;
    }

    /**
     * Get appelDelais
     *
     * @return integer
     */
    public function getAppelDelais()
    {
        return $this->appelDelais;
    }

    /**
     * Set appelDelaisType
     *
     * @param integer $appelDelaisType
     *
     * @return OneNotification
     */
    public function setAppelDelaisType($appelDelaisType)
    {
        $this->appelDelaisType = $appelDelaisType;

        return $this;
    }

    /**
     * Get appelDelaisType
     *
     * @return integer
     */
    public function getAppelDelaisType()
    {
        return $this->appelDelaisType;
    }

    /**
     * @param $appelAvant
     * @return $this
     */
    public function setAppelAvant($appelAvant)
    {
        $this->appelAvant = $appelAvant;

        return $this;
    }

    /**
     * @return int
     */
    public function getAppelAvant()
    {
        return $this->appelAvant;
    }

    /**
     * Set paiement
     *
     * @param integer $paiement
     *
     * @return OneNotification
     */
    public function setPaiement($paiement)
    {
        $this->paiement = $paiement;

        return $this;
    }

    /**
     * Get paiement
     *
     * @return integer
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * Set paiementDelais
     *
     * @param integer $paiementDelais
     *
     * @return OneNotification
     */
    public function setPaiementDelais($paiementDelais)
    {
        $this->paiementDelais = $paiementDelais;

        return $this;
    }

    /**
     * Get paiementDelais
     *
     * @return integer
     */
    public function getPaiementDelais()
    {
        return $this->paiementDelais;
    }

    /**
     * Set paiementDelaisType
     *
     * @param integer $paiementDelaisType
     *
     * @return OneNotification
     */
    public function setPaiementDelaisType($paiementDelaisType)
    {
        $this->paiementDelaisType = $paiementDelaisType;

        return $this;
    }

    /**
     * Get paiementDelaisType
     *
     * @return integer
     */
    public function getPaiementDelaisType()
    {
        return $this->paiementDelaisType;
    }

    /**
     * @param $paiementAvant
     * @return $this
     */
    public function setPaiementAvant($paiementAvant)
    {
        $this->paiementAvant = $paiementAvant;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaiementAvant()
    {
        return $this->paiementAvant;
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
     * @return OneNotification
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
     * @param Utilisateur|null $utilisateur
     * @return $this
     */
    public function setUtilisateur(Utilisateur $utilisateur = null){
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Utilisateur
     */
    public function getUtilisateur(){
        return $this->utilisateur;
    }
}
