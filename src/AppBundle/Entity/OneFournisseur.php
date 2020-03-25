<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneFournisseur
 *
 * @ORM\Table(name="one_fournisseur", indexes={@ORM\Index(name="fk_one_fournisseur_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_one_fournisseur_pcc_idx", columns={"pcc_id"}), @ORM\Index(name="fk_one_fournisseur_one_type_impot_idx", columns={"one_type_impot_id"}), @ORM\Index(name="fk_one_fournisseur_pays1_idx", columns={"pays_id"}), @ORM\Index(name="fk_one_fournisseur_one_reglement1_idx", columns={"one_reglement_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneFournisseurRepository")
 */
class OneFournisseur
{

    /**
     * @var string
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

    /**
     * @var  string
     *
     *@ORM\Column(name="adresse", type="string", length=45, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_visible", type="string", length=45, nullable=false)
     */
    private $nomVisible;


    /**
     * @var string
     *
     * @ORM\Column(name="nom_entreprise", type="string", length=45, nullable=true)
     */
    private $nomEntreprise;



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
     * @ORM\Column(name="site_web", type="string", length=45, nullable=true)
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone2", type="string", length=45, nullable=true)
     */
    private $telephone2;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=20, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=15, nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_fournisseur", type="string", length=45, nullable=true)
     */
    private $numeroFournisseur;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays_id", referencedColumnName="id")
     * })
     */
    private $pays;

    /**
     * @var \AppBundle\Entity\OneTypeImpot
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneTypeImpot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_type_impot_id", referencedColumnName="id")
     * })
     */
    private $oneTypeImpot;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return OneFournisseur
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
     * @return OneFournisseur
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
     * @return OneFournisseur
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
     * @return OneFournisseur
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
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return OneFournisseur
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
     * Set telephone2
     *
     * @param string $telephone2
     *
     * @return OneFournisseur
     */
    public function setTelephone2($telephone2)
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    /**
     * Get telephone2
     *
     * @return string
     */
    public function getTelephone2()
    {
        return $this->telephone2;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return OneFournisseur
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
     * Set ville
     *
     * @param string $ville
     *
     * @return OneFournisseur
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
     * Set siret
     *
     * @param string $siret
     *
     * @return OneFournisseur
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
     * Set numeroFournisseur
     *
     * @param string $numeroFournisseur
     *
     * @return OneFournisseur
     */
    public function setNumeroFournisseur($numeroFournisseur)
    {
        $this->numeroFournisseur = $numeroFournisseur;

        return $this;
    }

    /**
     * Get numeroFournisseur
     *
     * @return string
     */
    public function getNumeroFournisseur()
    {
        return $this->numeroFournisseur;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return OneFournisseur
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return OneFournisseur
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
     * Set pays
     *
     * @param \AppBundle\Entity\Pays $pays
     *
     * @return OneFournisseur
     */
    public function setPays(\AppBundle\Entity\Pays $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \AppBundle\Entity\Pays
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set oneTypeImpot
     *
     * @param \AppBundle\Entity\OneTypeImpot $oneTypeImpot
     *
     * @return OneFournisseur
     */
    public function setOneTypeImpot(\AppBundle\Entity\OneTypeImpot $oneTypeImpot = null)
    {
        $this->oneTypeImpot = $oneTypeImpot;

        return $this;
    }

    /**
     * Get oneTypeImpot
     *
     * @return \AppBundle\Entity\OneTypeImpot
     */
    public function getOneTypeImpot()
    {
        return $this->oneTypeImpot;
    }

    /**
     * Set oneReglement
     *
     * @param \AppBundle\Entity\OneReglement $oneReglement
     *
     * @return OneFournisseur
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return OneFournisseur
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
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneFournisseur
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
     * Set nomVisible
     *
     * @param string $nomVisible
     *
     * @return OneFournisseur
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
     * Set nomEntreprise
     *
     * @param string $nomEntreprise
     *
     * @return OneFournisseur
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
     * @param $adresse
     * @return $this
     */
    public function setAdresse($adresse){
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdresse(){
        return $this->adresse;
    }

    /**
     * @param $note
     * @return $this
     */
    public function setNote($note){
        $this->note = $note;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(){
        return $this->note;
    }

}
