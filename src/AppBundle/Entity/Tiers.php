<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tiers
 *
 * @ORM\Table(name="tiers", indexes={@ORM\Index(name="fk_tiers_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_tiers_historique_upload1_idx", columns={"historique_upload_id"}), @ORM\Index(name="fk_tiers_pcc1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_tiers_tva_taux1_idx", columns={"tva_taux_id"}), @ORM\Index(name="fk_tiers_forme_juridique1_idx", columns={"forme_juridique_id"}), @ORM\Index(name="fk_tiers_pays1_idx", columns={"pays_livraison"}), @ORM\Index(name="fk_tiers_pays2_idx", columns={"pays_facturation"}), @ORM\Index(name="fk_tiers_one_activite1_idx", columns={"one_activite_id"}), @ORM\Index(name="fk_tiers_one_famille_prix1_idx", columns={"one_famille_prix_id"}), @ORM\Index(name="fk_tiers_one_reglement1_idx", columns={"one_reglement_id"}), @ORM\Index(name="fk_tiers_one_qualification1_idx", columns={"one_qualification_id"}), @ORM\Index(name="fk_tiers_one_prospect_origine1_idx", columns={"one_prospect_origine_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TiersRepository")
 */
class Tiers
{
    /**
     * @var binary
     *
     * @ORM\Column(name="compte", type="binary", nullable=false)
     */
    private $compte = '';

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=150, nullable=false)
     */
    private $intitule = '';

    /**
     * @var string
     *
     * @ORM\Column(name="abrev", type="string", length=20, nullable=true)
     */
    private $abrev = '';

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=20, nullable=true)
     */
    private $siret = '';

    /**
     * @var string
     *
     * @ORM\Column(name="siren", type="string", length=20, nullable=true)
     */
    private $siren = '';

    /**
     * @var string
     *
     * @ORM\Column(name="rcs", type="string", length=50, nullable=true)
     */
    private $rcs = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '3';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="compte_str", type="string", length=100, nullable=false)
     */
    private $compteStr;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=45, nullable=true)
     */
    private $skype;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_facturation1", type="string", length=45, nullable=true)
     */
    private $adresseFacturation1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_facturation2", type="string", length=45, nullable=true)
     */
    private $adresseFacturation2;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal_facturation", type="string", length=45, nullable=true)
     */
    private $codePostalFacturation;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_facturation", type="string", length=45, nullable=true)
     */
    private $villeFacturation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adresse_livraison_identique", type="boolean", nullable=true)
     */
    private $adresseLivraisonIdentique;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_livraison1", type="string", length=45, nullable=true)
     */
    private $adresseLivraison1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_livraison2", type="string", length=45, nullable=true)
     */
    private $adresseLivraison2;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal_livraison", type="string", length=45, nullable=true)
     */
    private $codePostalLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_livraison", type="string", length=45, nullable=true)
     */
    private $villeLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="site_web", type="string", length=45, nullable=true)
     */
    private $siteWeb;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tva_prioritaire", type="boolean", nullable=true)
     */
    private $tvaPrioritaire;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_client", type="string", length=45, nullable=true)
     */
    private $numeroClient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="emailing_autorise", type="boolean", nullable=true)
     */
    private $emailingAutorise;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_entreprise", type="string", length=45, nullable=true)
     */
    private $nomEntreprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_salarie", type="integer", nullable=true)
     */
    private $nbSalarie;

    /**
     * @var string
     *
     * @ORM\Column(name="tva_intracom", type="string", length=45, nullable=true)
     */
    private $tvaIntracom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifie_le", type="datetime", nullable=true)
     */
    private $modifieLe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="premier_contact", type="datetime", nullable=true)
     */
    private $premierContact;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="particulier_entreprise", type="integer", nullable=false)
     */
    private $particulierEntreprise;


    /**
     * @var integer
     *
     * @ORM\Column(name="archive", type="integer",  nullable=false)
     */
    private $archive;

    /**
     * @var \AppBundle\Entity\OneProspectOrigine
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneProspectOrigine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_prospect_origine_id", referencedColumnName="id")
     * })
     */
    private $oneProspectOrigine;

    /**
     * @var \AppBundle\Entity\OneQualification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneQualification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_qualification_id", referencedColumnName="id")
     * })
     */
    private $oneQualification;

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
     * @var \AppBundle\Entity\HistoriqueUpload
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\HistoriqueUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="historique_upload_id", referencedColumnName="id")
     * })
     */
    private $historiqueUpload;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;

    /**
     * @var \AppBundle\Entity\OneReglement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_reglement_id", referencedColumnName="id")
     * })
     */
    private $oneReglement;

    /**
     * @var \AppBundle\Entity\OneFamillePrix
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneFamillePrix")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_famille_prix_id", referencedColumnName="id")
     * })
     */
    private $oneFamillePrix;

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
     * @var \AppBundle\Entity\Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays_livraison", referencedColumnName="id")
     * })
     */
    private $paysLivraison;

    /**
     * @var \AppBundle\Entity\Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays_facturation", referencedColumnName="id")
     * })
     */
    private $paysFacturation;

    /**
     * @var \AppBundle\Entity\OneActivite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneActivite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_activite_id", referencedColumnName="id")
     * })
     */
    private $oneActivite;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;



    /**
     * Set compte
     *
     * @param binary $compte
     *
     * @return Tiers
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return binary
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Tiers
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set abrev
     *
     * @param string $abrev
     *
     * @return Tiers
     */
    public function setAbrev($abrev)
    {
        $this->abrev = $abrev;

        return $this;
    }

    /**
     * Get abrev
     *
     * @return string
     */
    public function getAbrev()
    {
        return $this->abrev;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return Tiers
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set siren
     *
     * @param string $siren
     *
     * @return Tiers
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
     * Set rcs
     *
     * @param string $rcs
     *
     * @return Tiers
     */
    public function setRcs($rcs)
    {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * Get rcs
     *
     * @return string
     */
    public function getRcs()
    {
        return $this->rcs;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Tiers
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
     * Set status
     *
     * @param integer $status
     *
     * @return Tiers
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
     * Set compteStr
     *
     * @param string $compteStr
     *
     * @return Tiers
     */
    public function setCompteStr($compteStr)
    {
        $this->compteStr = $compteStr;

        return $this;
    }

    /**
     * Get compteStr
     *
     * @return string
     */
    public function getCompteStr()
    {
        return $this->compteStr;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Tiers
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
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Tiers
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param $skype
     * @return $this
     */
    public function setSkype($skype){
        $this->skype = $skype;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkype(){
        return $this->skype;
    }

    /**
     * Set adresseFacturation1
     *
     * @param string $adresseFacturation1
     *
     * @return Tiers
     */
    public function setAdresseFacturation1($adresseFacturation1)
    {
        $this->adresseFacturation1 = $adresseFacturation1;

        return $this;
    }

    /**
     * Get adresseFacturation1
     *
     * @return string
     */
    public function getAdresseFacturation1()
    {
        return $this->adresseFacturation1;
    }

    /**
     * Set adresseFacturation2
     *
     * @param string $adresseFacturation2
     *
     * @return Tiers
     */
    public function setAdresseFacturation2($adresseFacturation2)
    {
        $this->adresseFacturation2 = $adresseFacturation2;

        return $this;
    }

    /**
     * Get adresseFacturation2
     *
     * @return string
     */
    public function getAdresseFacturation2()
    {
        return $this->adresseFacturation2;
    }

    /**
     * Set codePostalFacturation
     *
     * @param string $codePostalFacturation
     *
     * @return Tiers
     */
    public function setCodePostalFacturation($codePostalFacturation)
    {
        $this->codePostalFacturation = $codePostalFacturation;

        return $this;
    }

    /**
     * Get codePostalFacturation
     *
     * @return string
     */
    public function getCodePostalFacturation()
    {
        return $this->codePostalFacturation;
    }

    /**
     * Set villeFacturation
     *
     * @param string $villeFacturation
     *
     * @return Tiers
     */
    public function setVilleFacturation($villeFacturation)
    {
        $this->villeFacturation = $villeFacturation;

        return $this;
    }

    /**
     * Get villeFacturation
     *
     * @return string
     */
    public function getVilleFacturation()
    {
        return $this->villeFacturation;
    }

    /**
     * Set adresseLivraisonIdentique
     *
     * @param boolean $adresseLivraisonIdentique
     *
     * @return Tiers
     */
    public function setAdresseLivraisonIdentique($adresseLivraisonIdentique)
    {
        $this->adresseLivraisonIdentique = $adresseLivraisonIdentique;

        return $this;
    }

    /**
     * Get adresseLivraisonIdentique
     *
     * @return boolean
     */
    public function getAdresseLivraisonIdentique()
    {
        return $this->adresseLivraisonIdentique;
    }

    /**
     * Set adresseLivraison1
     *
     * @param string $adresseLivraison1
     *
     * @return Tiers
     */
    public function setAdresseLivraison1($adresseLivraison1)
    {
        $this->adresseLivraison1 = $adresseLivraison1;

        return $this;
    }

    /**
     * Get adresseLivraison1
     *
     * @return string
     */
    public function getAdresseLivraison1()
    {
        return $this->adresseLivraison1;
    }

    /**
     * Set adresseLivraison2
     *
     * @param string $adresseLivraison2
     *
     * @return Tiers
     */
    public function setAdresseLivraison2($adresseLivraison2)
    {
        $this->adresseLivraison2 = $adresseLivraison2;

        return $this;
    }

    /**
     * Get adresseLivraison2
     *
     * @return string
     */
    public function getAdresseLivraison2()
    {
        return $this->adresseLivraison2;
    }

    /**
     * Set codePostalLivraison
     *
     * @param string $codePostalLivraison
     *
     * @return Tiers
     */
    public function setCodePostalLivraison($codePostalLivraison)
    {
        $this->codePostalLivraison = $codePostalLivraison;

        return $this;
    }

    /**
     * Get codePostalLivraison
     *
     * @return string
     */
    public function getCodePostalLivraison()
    {
        return $this->codePostalLivraison;
    }

    /**
     * Set villeLivraison
     *
     * @param string $villeLivraison
     *
     * @return Tiers
     */
    public function setVilleLivraison($villeLivraison)
    {
        $this->villeLivraison = $villeLivraison;

        return $this;
    }

    /**
     * Get villeLivraison
     *
     * @return string
     */
    public function getVilleLivraison()
    {
        return $this->villeLivraison;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return Tiers
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
     * Set tvaPrioritaire
     *
     * @param boolean $tvaPrioritaire
     *
     * @return Tiers
     */
    public function setTvaPrioritaire($tvaPrioritaire)
    {
        $this->tvaPrioritaire = $tvaPrioritaire;

        return $this;
    }

    /**
     * Get tvaPrioritaire
     *
     * @return boolean
     */
    public function getTvaPrioritaire()
    {
        return $this->tvaPrioritaire;
    }

    /**
     * Set numeroClient
     *
     * @param string $numeroClient
     *
     * @return Tiers
     */
    public function setNumeroClient($numeroClient)
    {
        $this->numeroClient = $numeroClient;

        return $this;
    }

    /**
     * Get numeroClient
     *
     * @return string
     */
    public function getNumeroClient()
    {
        return $this->numeroClient;
    }

    /**
     * Set emailingAutorise
     *
     * @param boolean $emailingAutorise
     *
     * @return Tiers
     */
    public function setEmailingAutorise($emailingAutorise)
    {
        $this->emailingAutorise = $emailingAutorise;

        return $this;
    }

    /**
     * Get emailingAutorise
     *
     * @return boolean
     */
    public function getEmailingAutorise()
    {
        return $this->emailingAutorise;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Tiers
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set nomEntreprise
     *
     * @param string $nomEntreprise
     *
     * @return Tiers
     */
    public function setNomEntreprise($nomEntreprise)
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    /**
     * Get nomEntreprise
     *
     * @return string
     */
    public function getNomEntreprise()
    {
        return $this->nomEntreprise;
    }

    /**
     * Set nbSalarie
     *
     * @param integer $nbSalarie
     *
     * @return Tiers
     */
    public function setNbSalarie($nbSalarie)
    {
        $this->nbSalarie = $nbSalarie;

        return $this;
    }

    /**
     * Get nbSalarie
     *
     * @return integer
     */
    public function getNbSalarie()
    {
        return $this->nbSalarie;
    }

    /**
     * Set tvaIntracom
     *
     * @param string $tvaIntracom
     *
     * @return Tiers
     */
    public function setTvaIntracom($tvaIntracom)
    {
        $this->tvaIntracom = $tvaIntracom;

        return $this;
    }

    /**
     * Get tvaIntracom
     *
     * @return string
     */
    public function getTvaIntracom()
    {
        return $this->tvaIntracom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Tiers
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
     * @return Tiers
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
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return Tiers
     */
    public function setCreeLe($creeLe)
    {
        $this->creeLe = $creeLe;

        return $this;
    }

    /**
     * Get creeLe
     *
     * @return \DateTime
     */
    public function getCreeLe()
    {
        return $this->creeLe;
    }

    /**
     * Set modifieLe
     *
     * @param \DateTime $modifieLe
     *
     * @return Tiers
     */
    public function setModifieLe($modifieLe)
    {
        $this->modifieLe = $modifieLe;

        return $this;
    }

    /**
     * Get modifieLe
     *
     * @return \DateTime
     */
    public function getModifieLe()
    {
        return $this->modifieLe;
    }

    /**
     * Set premierContact
     *
     * @param \DateTime $premierContact
     *
     * @return Tiers
     */
    public function setPremierContact($premierContact)
    {
        $this->premierContact = $premierContact;

        return $this;
    }

    /**
     * Get premierContact
     *
     * @return \DateTime
     */
    public function getPremierContact()
    {
        return $this->premierContact;
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
     * Set oneProspectOrigine
     *
     * @param \AppBundle\Entity\OneProspectOrigine $oneProspectOrigine
     *
     * @return Tiers
     */
    public function setOneProspectOrigine(\AppBundle\Entity\OneProspectOrigine $oneProspectOrigine = null)
    {
        $this->oneProspectOrigine = $oneProspectOrigine;

        return $this;
    }

    /**
     * Get oneProspectOrigine
     *
     * @return \AppBundle\Entity\OneProspectOrigine
     */
    public function getOneProspectOrigine()
    {
        return $this->oneProspectOrigine;
    }

    /**
     * Set oneQualification
     *
     * @param \AppBundle\Entity\OneQualification $oneQualification
     *
     * @return Tiers
     */
    public function setOneQualification(\AppBundle\Entity\OneQualification $oneQualification = null)
    {
        $this->oneQualification = $oneQualification;

        return $this;
    }

    /**
     * Get oneQualification
     *
     * @return \AppBundle\Entity\OneQualification
     */
    public function getOneQualification()
    {
        return $this->oneQualification;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Tiers
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
     * Set historiqueUpload
     *
     * @param \AppBundle\Entity\HistoriqueUpload $historiqueUpload
     *
     * @return Tiers
     */
    public function setHistoriqueUpload(\AppBundle\Entity\HistoriqueUpload $historiqueUpload = null)
    {
        $this->historiqueUpload = $historiqueUpload;

        return $this;
    }

    /**
     * Get historiqueUpload
     *
     * @return \AppBundle\Entity\HistoriqueUpload
     */
    public function getHistoriqueUpload()
    {
        return $this->historiqueUpload;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return Tiers
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }

    /**
     * Set oneReglement
     *
     * @param \AppBundle\Entity\OneReglement $oneReglement
     *
     * @return Tiers
     */
    public function setOneReglement(\AppBundle\Entity\OneReglement $oneReglement = null)
    {
        $this->oneReglement = $oneReglement;

        return $this;
    }

    /**
     * Get oneReglement
     *
     * @return \AppBundle\Entity\OneReglement
     */
    public function getOneReglement()
    {
        return $this->oneReglement;
    }

    /**
     * Set oneFamillePrix
     *
     * @param \AppBundle\Entity\OneFamillePrix $oneFamillePrix
     *
     * @return Tiers
     */
    public function setOneFamillePrix(\AppBundle\Entity\OneFamillePrix $oneFamillePrix = null)
    {
        $this->oneFamillePrix = $oneFamillePrix;

        return $this;
    }

    /**
     * Get oneFamillePrix
     *
     * @return \AppBundle\Entity\OneFamillePrix
     */
    public function getOneFamillePrix()
    {
        return $this->oneFamillePrix;
    }

    /**
     * Set formeJuridique
     *
     * @param \AppBundle\Entity\FormeJuridique $formeJuridique
     *
     * @return Tiers
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
     * Set paysLivraison
     *
     * @param \AppBundle\Entity\Pays $paysLivraison
     *
     * @return Tiers
     */
    public function setPaysLivraison(\AppBundle\Entity\Pays $paysLivraison = null)
    {
        $this->paysLivraison = $paysLivraison;

        return $this;
    }

    /**
     * Get paysLivraison
     *
     * @return \AppBundle\Entity\Pays
     */
    public function getPaysLivraison()
    {
        return $this->paysLivraison;
    }

    /**
     * Set paysFacturation
     *
     * @param \AppBundle\Entity\Pays $paysFacturation
     *
     * @return Tiers
     */
    public function setPaysFacturation(\AppBundle\Entity\Pays $paysFacturation = null)
    {
        $this->paysFacturation = $paysFacturation;

        return $this;
    }

    /**
     * Get paysFacturation
     *
     * @return \AppBundle\Entity\Pays
     */
    public function getPaysFacturation()
    {
        return $this->paysFacturation;
    }

    /**
     * Set oneActivite
     *
     * @param \AppBundle\Entity\OneActivite $oneActivite
     *
     * @return Tiers
     */
    public function setOneActivite(\AppBundle\Entity\OneActivite $oneActivite = null)
    {
        $this->oneActivite = $oneActivite;

        return $this;
    }

    /**
     * Get oneActivite
     *
     * @return \AppBundle\Entity\OneActivite
     */
    public function getOneActivite()
    {
        return $this->oneActivite;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return Tiers
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * @param $particulierEntreprise
     * @return $this
     */
    public function setParticulierEntreprise($particulierEntreprise){
        $this->particulierEntreprise = $particulierEntreprise;
        return $this;
    }

    /**
     * @return int
     */
    public function getParticulierEntreprise(){
        return $this->particulierEntreprise;
    }

    /**
     * @param $archive
     * @return $this
     */
    public function setArchive($archive){
        $this->archive = $archive;
        return $this;
    }

    /**
     * @return int
     */
    public function getArchive(){
        return $this->archive;

    }
}
