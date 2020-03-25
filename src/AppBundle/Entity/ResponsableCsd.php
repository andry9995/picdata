<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResponsableCsd
 *
 * @ORM\Table(name="responsable_csd", indexes={@ORM\Index(name="fk_responsable_csd_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_responsable_csd_site1_idx", columns={"site_id"}), @ORM\Index(name="fk_responsable_csd_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_responsable_csd_mandataire1_idx", columns={"mandataire_id"}), @ORM\Index(name="fk_responsable_csd_mandataire_statut1_idx", columns={"mandataire_statut_id"}), @ORM\Index(name="fk_responsable_csd_fk_responsable_csd_titre1_idx", columns={"responsable_csd_titre_id"}), @ORM\Index(name="fk_responsable_csd_regime_suivi1_idx", columns={"regime_suivi_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResponsableCsdRepository")
 */
class ResponsableCsd
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
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_responsable", type="integer", nullable=true)
     */
    private $typeResponsable;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_csd", type="integer", nullable=true)
     */
    private $typeCsd;

    /**
     * @var integer
     *
     * @ORM\Column(name="complementaire", type="integer", nullable=true)
     */
    private $complementaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="envoi_mail", type="integer", nullable=true)
     */
    private $envoiMail;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_portable", type="string", length=45, nullable=true)
     */
    private $telPortable;

    /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=45, nullable=true)
     */
    private $skype;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\RegimeSuivi
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeSuivi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_suivi_id", referencedColumnName="id")
     * })
     */
    private $regimeSuivi;

    /**
     * @var \AppBundle\Entity\Site
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;

    /**
     * @var \AppBundle\Entity\MandataireStatut
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MandataireStatut")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mandataire_statut_id", referencedColumnName="id")
     * })
     */
    private $mandataireStatut;

    /**
     * @var \AppBundle\Entity\Mandataire
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mandataire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mandataire_id", referencedColumnName="id")
     * })
     */
    private $mandataire;

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
     * @var \AppBundle\Entity\ResponsableCsdTitre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ResponsableCsdTitre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable_csd_titre_id", referencedColumnName="id")
     * })
     */
    private $responsableCsdTitre;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return ResponsableCsd
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return ResponsableCsd
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ResponsableCsd
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set typeResponsable
     *
     * @param integer $typeResponsable
     *
     * @return ResponsableCsd
     */
    public function setTypeResponsable($typeResponsable)
    {
        $this->typeResponsable = $typeResponsable;

        return $this;
    }

    /**
     * Get typeResponsable
     *
     * @return integer
     */
    public function getTypeResponsable()
    {
        return $this->typeResponsable;
    }

    /**
     * Set typeCsd
     *
     * @param integer $typeCsd
     *
     * @return ResponsableCsd
     */
    public function setTypeCsd($typeCsd)
    {
        $this->typeCsd = $typeCsd;

        return $this;
    }

    /**
     * Get typeCsd
     *
     * @return integer
     */
    public function getTypeCsd()
    {
        return $this->typeCsd;
    }

    /**
     * Set complementaire
     *
     * @param integer $complementaire
     *
     * @return ResponsableCsd
     */
    public function setComplementaire($complementaire)
    {
        $this->complementaire = $complementaire;

        return $this;
    }

    /**
     * Get complementaire
     *
     * @return integer
     */
    public function getComplementaire()
    {
        return $this->complementaire;
    }

    /**
     * Set envoiMail
     *
     * @param integer $envoiMail
     *
     * @return ResponsableCsd
     */
    public function setEnvoiMail($envoiMail)
    {
        $this->envoiMail = $envoiMail;

        return $this;
    }

    /**
     * Get envoiMail
     *
     * @return integer
     */
    public function getEnvoiMail()
    {
        return $this->envoiMail;
    }

    /**
     * Set telPortable
     *
     * @param string $telPortable
     *
     * @return ResponsableCsd
     */
    public function setTelPortable($telPortable)
    {
        $this->telPortable = $telPortable;

        return $this;
    }

    /**
     * Get telPortable
     *
     * @return string
     */
    public function getTelPortable()
    {
        return $this->telPortable;
    }

    /**
     * Set skype
     *
     * @param string $skype
     *
     * @return ResponsableCsd
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
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
     * Set regimeSuivi
     *
     * @param \AppBundle\Entity\RegimeSuivi $regimeSuivi
     *
     * @return ResponsableCsd
     */
    public function setRegimeSuivi(\AppBundle\Entity\RegimeSuivi $regimeSuivi = null)
    {
        $this->regimeSuivi = $regimeSuivi;

        return $this;
    }

    /**
     * Get regimeSuivi
     *
     * @return \AppBundle\Entity\RegimeSuivi
     */
    public function getRegimeSuivi()
    {
        return $this->regimeSuivi;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return ResponsableCsd
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set mandataireStatut
     *
     * @param \AppBundle\Entity\MandataireStatut $mandataireStatut
     *
     * @return ResponsableCsd
     */
    public function setMandataireStatut(\AppBundle\Entity\MandataireStatut $mandataireStatut = null)
    {
        $this->mandataireStatut = $mandataireStatut;

        return $this;
    }

    /**
     * Get mandataireStatut
     *
     * @return \AppBundle\Entity\MandataireStatut
     */
    public function getMandataireStatut()
    {
        return $this->mandataireStatut;
    }

    /**
     * Set mandataire
     *
     * @param \AppBundle\Entity\Mandataire $mandataire
     *
     * @return ResponsableCsd
     */
    public function setMandataire(\AppBundle\Entity\Mandataire $mandataire = null)
    {
        $this->mandataire = $mandataire;

        return $this;
    }

    /**
     * Get mandataire
     *
     * @return \AppBundle\Entity\Mandataire
     */
    public function getMandataire()
    {
        return $this->mandataire;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return ResponsableCsd
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
     * Set responsableCsdTitre
     *
     * @param \AppBundle\Entity\ResponsableCsdTitre $responsableCsdTitre
     *
     * @return ResponsableCsd
     */
    public function setResponsableCsdTitre(\AppBundle\Entity\ResponsableCsdTitre $responsableCsdTitre = null)
    {
        $this->responsableCsdTitre = $responsableCsdTitre;

        return $this;
    }

    /**
     * Get responsableCsdTitre
     *
     * @return \AppBundle\Entity\ResponsableCsdTitre
     */
    public function getResponsableCsdTitre()
    {
        return $this->responsableCsdTitre;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ResponsableCsd
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
