<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneClientProspect
 *
 * @ORM\Table(name="one_client_prospect", indexes={@ORM\Index(name="fk_one_client_prospect_one_employe1_idx", columns={"one_employe_id"}), @ORM\Index(name="fk_one_client_prospect_one_qualification1_idx", columns={"one_qualification_id"}), @ORM\Index(name="fk_one_client_prospect_one_reglement1_idx", columns={"one_reglement_id"}), @ORM\Index(name="fk_one_client_prospect_one_famille_prix1_idx", columns={"one_famille_prix_id"}), @ORM\Index(name="fk_one_client_prospect_tva_taux1_idx", columns={"taux_tva_id"}), @ORM\Index(name="fk_one_client_prospect_forme_juridique1_idx", columns={"forme_juridique_id"}), @ORM\Index(name="fk_one_client_prospect_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_one_client_prospect_activite1_idx", columns={"activite_id"}), @ORM\Index(name="FK_FDFEB4C73E8810BA_idx", columns={"pays_livraison"}), @ORM\Index(name="FK_FDFEB4C7B99C1FA3_idx", columns={"pays_facturation"}), @ORM\Index(name="fk_one_client_prospect_one_prospect_origine1_idx", columns={"one_prospect_origine_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneClientProspectRepository")
 */
class OneClientProspect
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_prospect", type="boolean", nullable=false)
     */
    private $isProspect;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=50, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_facturation1", type="string", length=50, nullable=true)
     */
    private $adresseFacturation1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_facturation2", type="string", length=50, nullable=true)
     */
    private $adresseFacturation2;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal_facturation", type="string", length=20, nullable=true)
     */
    private $codePostalFacturation;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_facturation", type="string", length=50, nullable=true)
     */
    private $villeFacturation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adresse_livraison_identique", type="boolean", nullable=false)
     */
    private $adresseLivraisonIdentique = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_livraison1", type="string", length=50, nullable=true)
     */
    private $adresseLivraison1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_livraison2", type="string", length=50, nullable=true)
     */
    private $adresseLivraison2;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal_livraison", type="string", length=20, nullable=true)
     */
    private $codePostalLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_livraison", type="string", length=50, nullable=true)
     */
    private $villeLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="one_client_prospectcol", type="string", length=50, nullable=true)
     */
    private $oneClientProspectcol;

    /**
     * @var string
     *
     * @ORM\Column(name="site_web", type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="one_client_prospectcol1", type="string", length=50, nullable=true)
     */
    private $oneClientProspectcol1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tva_prioritaire", type="boolean", nullable=false)
     */
    private $tvaPrioritaire = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="numero_client", type="string", length=50, nullable=true)
     */
    private $numeroClient;

    /**
     * @var boolean
     *
     * @ORM\Column(name="emailing_autorise", type="boolean", nullable=false)
     */
    private $emailingAutorise = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_entreprise", type="string", length=50, nullable=true)
     */
    private $nomEntreprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_salarie", type="integer", nullable=true)
     */
    private $nbSalarie = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=50, nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="tva_intracom", type="string", length=50, nullable=true)
     */
    private $tvaIntracom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_visible", type="string", length=50, nullable=true)
     */
    private $nomVisible;

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
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taux_tva_id", referencedColumnName="id")
     * })
     */
    private $tauxTva;

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
     * @var \AppBundle\Entity\OneQualification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneQualification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_qualification_id", referencedColumnName="id")
     * })
     */
    private $oneQualification;

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
     * @var \AppBundle\Entity\OneEmploye
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneEmploye")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_employe_id", referencedColumnName="id")
     * })
     */
    private $oneEmploye;

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
     * @var \AppBundle\Entity\OneActivite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneActivite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_id", referencedColumnName="id")
     * })
     */
    private $activite;

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
     * @var integer
     *
     * @ORM\Column(name="tiers_id", type="integer", nullable=false)
     */
    private $tiersId;



    /**
     * Set isProspect
     *
     * @param boolean $isProspect
     *
     * @return OneClientProspect
     */
    public function setIsProspect($isProspect)
    {
        $this->isProspect = $isProspect;

        return $this;
    }

    /**
     * Get isProspect
     *
     * @return boolean
     */
    public function getIsProspect()
    {
        return $this->isProspect;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return OneClientProspect
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
     * Set email
     *
     * @param string $email
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set adresseFacturation1
     *
     * @param string $adresseFacturation1
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set oneClientProspectcol
     *
     * @param string $oneClientProspectcol
     *
     * @return OneClientProspect
     */
    public function setOneClientProspectcol($oneClientProspectcol)
    {
        $this->oneClientProspectcol = $oneClientProspectcol;

        return $this;
    }

    /**
     * Get oneClientProspectcol
     *
     * @return string
     */
    public function getOneClientProspectcol()
    {
        return $this->oneClientProspectcol;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return OneClientProspect
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
     * Set oneClientProspectcol1
     *
     * @param string $oneClientProspectcol1
     *
     * @return OneClientProspect
     */
    public function setOneClientProspectcol1($oneClientProspectcol1)
    {
        $this->oneClientProspectcol1 = $oneClientProspectcol1;

        return $this;
    }

    /**
     * Get oneClientProspectcol1
     *
     * @return string
     */
    public function getOneClientProspectcol1()
    {
        return $this->oneClientProspectcol1;
    }

    /**
     * Set tvaPrioritaire
     *
     * @param boolean $tvaPrioritaire
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set siret
     *
     * @param string $siret
     *
     * @return OneClientProspect
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
     * Set tvaIntracom
     *
     * @param string $tvaIntracom
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set nomVisible
     *
     * @param string $nomVisible
     *
     * @return OneClientProspect
     */
    public function setNomVisible($nomVisible)
    {
        $this->nomVisible = $nomVisible;

        return $this;
    }

    /**
     * Get nomVisible
     *
     * @return string
     */
    public function getNomVisible()
    {
        return $this->nomVisible;
    }

    /**
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tauxTva
     *
     * @param \AppBundle\Entity\TvaTaux $tauxTva
     *
     * @return OneClientProspect
     */
    public function setTauxTva(\AppBundle\Entity\TvaTaux $tauxTva = null)
    {
        $this->tauxTva = $tauxTva;

        return $this;
    }

    /**
     * Get tauxTva
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTauxTva()
    {
        return $this->tauxTva;
    }

    /**
     * Set formeJuridique
     *
     * @param \AppBundle\Entity\FormeJuridique $formeJuridique
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set oneQualification
     *
     * @param \AppBundle\Entity\OneQualification $oneQualification
     *
     * @return OneClientProspect
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
     * Set oneReglement
     *
     * @param \AppBundle\Entity\OneReglement $oneReglement
     *
     * @return OneClientProspect
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
     * @return OneClientProspect
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
     * Set oneEmploye
     *
     * @param \AppBundle\Entity\OneEmploye $oneEmploye
     *
     * @return OneClientProspect
     */
    public function setOneEmploye(\AppBundle\Entity\OneEmploye $oneEmploye = null)
    {
        $this->oneEmploye = $oneEmploye;

        return $this;
    }

    /**
     * Get oneEmploye
     *
     * @return \AppBundle\Entity\OneEmploye
     */
    public function getOneEmploye()
    {
        return $this->oneEmploye;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return OneClientProspect
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
     * Set activite
     *
     * @param \AppBundle\Entity\OneActivite $activite
     *
     * @return OneClientProspect
     */
    public function setActivite(\AppBundle\Entity\OneActivite $activite = null)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return \AppBundle\Entity\OneActivite
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set oneProspectOrigine
     *
     * @param \AppBundle\Entity\OneProspectOrigine $oneProspectOrigine
     *
     * @return OneClientProspect
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
     * Set premierContact
     *
     * @param \DateTime $premierContact
     *
     * @return OneClientProspect
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


    public function getTiersId(){
        return $this->tiersId;
    }

    public function setTiersId($tiersId){
        $this->tiersId = $tiersId;
        return $this;
    }



}

