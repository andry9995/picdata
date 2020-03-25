<?php
 
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Controller\Boost;

/**
 * Client
 *
 * @ORM\Table(name="client", uniqueConstraints={@ORM\UniqueConstraint(name="nom_UNIQUE", columns={"nom"})}, indexes={@ORM\Index(name="fk_client_forme_juridique1_idx", columns={"forme_juridique_id"}), @ORM\Index(name="index_client_nom", columns={"nom"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=150, nullable=false)
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_client", type="integer", nullable=false)
     */
    private $typeClient = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="siren", type="string", length=45, nullable=true)
     */
    private $siren;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_siege", type="string", length=255, nullable=true)
     */
    private $adresseSiege;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_fixe", type="string", length=20, nullable=true)
     */
    private $telFixe;

    /**
     * @var string
     *
     * @ORM\Column(name="site_web", type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="instruction", type="text", length=65535, nullable=true)
     */
    private $instruction;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=250, nullable=true)
     */
    private $logo;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_caractere", type="integer", nullable=false)
     */
    private $nbCaractere = '9';

    /**
     * @var string
     *
     * @ORM\Column(name="dernier_num", type="string", length=20, nullable=false)
     */
    private $dernierNum = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="dernier_num_local", type="string", length=20, nullable=false)
     */
    private $dernierNumLocal = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2, nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="code_local", type="string", length=1, nullable=true)
     */
    private $codeLocal = 'Z';

    /**
     * @var string
     *
     * @ORM\Column(name="image_ftp_separator", type="string", length=5, nullable=false)
     */
    private $imageFtpSeparator = '.';

    /**
     * @var bool
     * @ORM\Column(name="send_notification_image", type="boolean", nullable=false)
     */
    private $sendNotificationImage = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rs_ste", type="string", length=85, nullable=true)
     */
    private $rsSte;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="status_update", type="date", nullable=true)
     */
    private $statusUpdate;

    /**
     * @var string
     *
     * @ORM\Column(name="num_rue", type="string", length=100, nullable=true)
     */
    private $numRue;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=45, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=45, nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=true)
     */
    private $ville;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FormeJuridique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FormeJuridique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="forme_juridique_id", referencedColumnName="id")
     * })
     */
    private $formeJuridique;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=150, nullable=false)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="contrat", type="string", length=150, nullable=false)
     */
    private $contrat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signature", type="date", nullable=false)
     */
    private $signature;


    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Client
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
     * Set type
     *
     * @param integer $type
     *
     * @return Client
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $typeClient
     * @return $this
     */
    public function setTypeClient($typeClient)
    {
        $this->typeClient = $typeClient;

        return $this;
    }


    /**
     * @return int
     */
    public function getTypeClient()
    {
        return $this->typeClient;
    }

    /**
     * Set siren
     *
     * @param string $siren
     *
     * @return Client
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set adresseSiege
     *
     * @param string $adresseSiege
     *
     * @return Client
     */
    public function setAdresseSiege($adresseSiege)
    {
        $this->adresseSiege = $adresseSiege;

        return $this;
    }

    /**
     * Get adresseSiege
     *
     * @return string
     */
    public function getAdresseSiege()
    {
        return $this->adresseSiege;
    }

    /**
     * Set telFixe
     *
     * @param string $telFixe
     *
     * @return Client
     */
    public function setTelFixe($telFixe)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * Get telFixe
     *
     * @return string
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return Client
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Client
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set instruction
     *
     * @param string $instruction
     *
     * @return Client
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;

        return $this;
    }

    /**
     * Get instruction
     *
     * @return string
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Client
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set nbCaractere
     *
     * @param integer $nbCaractere
     *
     * @return Client
     */
    public function setNbCaractere($nbCaractere)
    {
        $this->nbCaractere = $nbCaractere;

        return $this;
    }

    /**
     * Get nbCaractere
     *
     * @return integer
     */
    public function getNbCaractere()
    {
        return $this->nbCaractere;
    }

    /**
     * Set dernierNum
     *
     * @param string $dernierNum
     *
     * @return Client
     */
    public function setDernierNum($dernierNum)
    {
        $this->dernierNum = $dernierNum;

        return $this;
    }

    /**
     * Get dernierNum
     *
     * @return string
     */
    public function getDernierNum()
    {
        return $this->dernierNum;
    }

    /**
     * Set dernierNumLocal
     *
     * @param string $dernierNumLocal
     *
     * @return Client
     */
    public function setDernierNumLocal($dernierNumLocal)
    {
        $this->dernierNumLocal = $dernierNumLocal;

        return $this;
    }

    /**
     * Get dernierNumLocal
     *
     * @return string
     */
    public function getDernierNumLocal()
    {
        return $this->dernierNumLocal;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Client
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set codeLocal
     *
     * @param string $codeLocal
     *
     * @return Client
     */
    public function setCodeLocal($codeLocal)
    {
        $this->codeLocal = $codeLocal;

        return $this;
    }

    /**
     * Get codeLocal
     *
     * @return string
     */
    public function getCodeLocal()
    {
        return $this->codeLocal;
    }

    /**
     * Set imageFtpSeparator
     *
     * @param string $imageFtpSeparator
     *
     * @return Client
     */
    public function setImageFtpSeparator($imageFtpSeparator)
    {
        $this->imageFtpSeparator = $imageFtpSeparator;

        return $this;
    }

    /**
     * Get imageFtpSeparator
     *
     * @return string
     */
    public function getImageFtpSeparator()
    {
        return $this->imageFtpSeparator;
    }

    /**
     * @return boolean
     */
    public function getSendNotificationImage()
    {
        return $this->sendNotificationImage;
    }

    /**
     * @param bool $sendNotificationImage
     *
     * @return \AppBundle\Entity\Client
     */
    public function setSendNotificationImage($sendNotificationImage)
    {
        $this->sendNotificationImage = $sendNotificationImage;
        return $this;
    }

    /**
     * Set rsSte
     *
     * @param string $rsSte
     *
     * @return Client
     */
    public function setRsSte($rsSte)
    {
        $this->rsSte = $rsSte;

        return $this;
    }

    /**
     * Get rsSte
     *
     * @return string
     */
    public function getRsSte()
    {
        return $this->rsSte;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Client
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
     * Set statusUpdate
     *
     * @param \DateTime $statusUpdate
     *
     * @return Client
     */
    public function setStatusUpdate($statusUpdate)
    {
        $this->statusUpdate = $statusUpdate;

        return $this;
    }

    /**
     * Get statusUpdate
     *
     * @return \DateTime
     */
    public function getStatusUpdate()
    {
        return $this->statusUpdate;
    }

    /**
     * Set numRue
     *
     * @param string $numRue
     *
     * @return Client
     */
    public function setNumRue($numRue)
    {
        $this->numRue = $numRue;

        return $this;
    }

    /**
     * Get numRue
     *
     * @return string
     */
    public function getNumRue()
    {
        return $this->numRue;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Client
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Client
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Client
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
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
     * Set formeJuridique
     *
     * @param \AppBundle\Entity\FormeJuridique $formeJuridique
     *
     * @return Client
     */
    public function setFormeJuridique(\AppBundle\Entity\FormeJuridique $formeJuridique = null)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \AppBundle\Entity\FormeJuridique
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Client
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set contrat
     *
     * @param string $contrat
     *
     * @return Client
     */
    public function setContrat($contrat)
    {
        $this->contrat = $contrat;

        return $this;
    }

    /**
     * Get contrat
     *
     * @return string
     */
    public function getContrat()
    {
        return $this->contrat;
    }

    /**
     * Set signature
     *
     * @param \DateTime $signature
     *
     * @return Client
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /* Ajout Manuel */
    public function getIdCrypter()
    {
        return Boost::boost($this->id);
    }

    /**
     * Get signature
     *
     * @return \DateTime
     */
    public function getSignature()
    {
        if ($this->signature !== null)
            return $this->signature->format('d/m/Y');
        else
            return null;
    }
}
