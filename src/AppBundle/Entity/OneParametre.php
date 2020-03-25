<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneParametre
 *
 * @ORM\Table(name="one_parametre", indexes={@ORM\Index(name="fk_one_parametre_one_pays1_idx", columns={"company_pays"}), @ORM\Index(name="fk_one_parametre_one_devise1_idx", columns={"comptable_devise"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneParametreRepository")
 */
class OneParametre
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="company_nom", type="string", length=255)
     */
    private $companyNom;

    /**
     * @var string
     *
     * @ORM\Column(name="company_adresse", type="string", length=255)
     */
    private $companyAdresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="company_code_postal", type="integer")
     */
    private $companyCodePostal;

    /**
     * @var \AppBundle\Entity\OnePays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OnePays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_pays", referencedColumnName="id")
     * })
     */
    private $companyPays;

    /**
     * @var string
     *
     * @ORM\Column(name="company_tel", type="string", length=255)
     */
    private $companyTel;

    /**
     * @var string
     *
     * @ORM\Column(name="company_fax", type="string", length=255)
     */
    private $companyFax;

    /**
     * @var string
     *
     * @ORM\Column(name="company_mail", type="string", length=255)
     */
    private $companyMail;

    /**
     * @var string
     *
     * @ORM\Column(name="company_siteweb", type="string", length=255)
     */
    private $companySiteweb;

    /**
     * @var string
     *
     * @ORM\Column(name="company_capital_social", type="string", length=255)
     */
    private $companyCapitalSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="company_siret", type="string", length=255)
     */
    private $companySiret;

    /**
     * @var string
     *
     * @ORM\Column(name="company_tva_intracom", type="string", length=255)
     */
    private $companyTvaIntracom;

    /**
     * @var string
     *
     * @ORM\Column(name="company_rcs_rm", type="string", length=255)
     */
    private $companyRcsRm;

    /**
     * @var string
     *
     * @ORM\Column(name="company_ape", type="string", length=255)
     */
    private $companyApe;

    /**
     * @var integer
     *
     * @ORM\Column(name="reglement_client_type", type="integer")
     */
    private $reglementClientType;

    /**
     * @var integer
     *
     * @ORM\Column(name="reglement_client_jours", type="integer")
     */
    private $reglementClientJours;

    /**
     * @var \string
     *
     * @ORM\Column(name="reglement_client_date", type="string", length=255)
     */
    private $reglementClientDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="reglement_fournisseur_type", type="integer")
     */
    private $reglementFournisseurType;

    /**
     * @var integer
     *
     * @ORM\Column(name="reglement_fournisseur_jours", type="integer")
     */
    private $reglementFournisseurJours;

    /**
     * @var \string
     *
     * @ORM\Column(name="reglement_fournisseur_date", type="string", length=255)
     */
    private $reglementFournisseurDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="vente_paiement_caisse", type="integer")
     */
    private $ventePaiementCaisse;

    /**
     * @var integer
     *
     * @ORM\Column(name="vente_tva_type", type="integer")
     */
    private $venteTvaType;

    /**
     * @var string
     *
     * @ORM\Column(name="comptable_date_cloture", type="string", length=255)
     */
    private $comptableDateCloture;

    /**
     * @var \AppBundle\Entity\OneDevise
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneDevise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comptable_devise", referencedColumnName="id")
     * })
     */
    private $comptableDevise;


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
     * Set companyNom
     *
     * @param string $companyNom
     *
     * @return OneParametre
     */
    public function setCompanyNom($companyNom)
    {
        $this->companyNom = $companyNom;
    
        return $this;
    }

    /**
     * Get companyNom
     *
     * @return string
     */
    public function getCompanyNom()
    {
        return $this->companyNom;
    }

    /**
     * Set companyAdresse
     *
     * @param string $companyAdresse
     *
     * @return OneParametre
     */
    public function setCompanyAdresse($companyAdresse)
    {
        $this->companyAdresse = $companyAdresse;
    
        return $this;
    }

    /**
     * Get companyAdresse
     *
     * @return string
     */
    public function getCompanyAdresse()
    {
        return $this->companyAdresse;
    }

    /**
     * Set companyCodePostal
     *
     * @param integer $companyCodePostal
     *
     * @return OneParametre
     */
    public function setCompanyCodePostal($companyCodePostal)
    {
        $this->companyCodePostal = $companyCodePostal;
    
        return $this;
    }

    /**
     * Get companyCodePostal
     *
     * @return integer
     */
    public function getCompanyCodePostal()
    {
        return $this->companyCodePostal;
    }

    /**
     * Set companyPays
     *
     * @param \AppBundle\Entity\OnePays $companyPays
     *
     * @return OneParametre
     */
    public function setCompanyPays(\AppBundle\Entity\OnePays $companyPays = null)
    {
        $this->companyPays = $companyPays;
    
        return $this;
    }

    /**
     * Get companyPays
     *
     * @return integer
     */
    public function getCompanyPays()
    {
        return $this->companyPays;
    }

    /**
     * Set companyTel
     *
     * @param string $companyTel
     *
     * @return OneParametre
     */
    public function setCompanyTel($companyTel)
    {
        $this->companyTel = $companyTel;
    
        return $this;
    }

    /**
     * Get companyTel
     *
     * @return string
     */
    public function getCompanyTel()
    {
        return $this->companyTel;
    }

    /**
     * Set companyFax
     *
     * @param string $companyFax
     *
     * @return OneParametre
     */
    public function setCompanyFax($companyFax)
    {
        $this->companyFax = $companyFax;
    
        return $this;
    }

    /**
     * Get companyFax
     *
     * @return string
     */
    public function getCompanyFax()
    {
        return $this->companyFax;
    }

    /**
     * Set companyMail
     *
     * @param string $companyMail
     *
     * @return OneParametre
     */
    public function setCompanyMail($companyMail)
    {
        $this->companyMail = $companyMail;
    
        return $this;
    }

    /**
     * Get companyMail
     *
     * @return string
     */
    public function getCompanyMail()
    {
        return $this->companyMail;
    }

    /**
     * Set companySiteweb
     *
     * @param string $companySiteweb
     *
     * @return OneParametre
     */
    public function setCompanySiteweb($companySiteweb)
    {
        $this->companySiteweb = $companySiteweb;
    
        return $this;
    }

    /**
     * Get companySiteweb
     *
     * @return string
     */
    public function getCompanySiteweb()
    {
        return $this->companySiteweb;
    }

    /**
     * Set companyCapitalSocial
     *
     * @param string $companyCapitalSocial
     *
     * @return OneParametre
     */
    public function setCompanyCapitalSocial($companyCapitalSocial)
    {
        $this->companyCapitalSocial = $companyCapitalSocial;
    
        return $this;
    }

    /**
     * Get companyCapitalSocial
     *
     * @return string
     */
    public function getCompanyCapitalSocial()
    {
        return $this->companyCapitalSocial;
    }

    /**
     * Set companySiret
     *
     * @param string $companySiret
     *
     * @return OneParametre
     */
    public function setCompanySiret($companySiret)
    {
        $this->companySiret = $companySiret;
    
        return $this;
    }

    /**
     * Get companySiret
     *
     * @return string
     */
    public function getCompanySiret()
    {
        return $this->companySiret;
    }

    /**
     * Set companyTvaIntracom
     *
     * @param string $companyTvaIntracom
     *
     * @return OneParametre
     */
    public function setCompanyTvaIntracom($companyTvaIntracom)
    {
        $this->companyTvaIntracom = $companyTvaIntracom;
    
        return $this;
    }

    /**
     * Get companyTvaIntracom
     *
     * @return string
     */
    public function getCompanyTvaIntracom()
    {
        return $this->companyTvaIntracom;
    }

    /**
     * Set companyRcsRm
     *
     * @param string $companyRcsRm
     *
     * @return OneParametre
     */
    public function setCompanyRcsRm($companyRcsRm)
    {
        $this->companyRcsRm = $companyRcsRm;
    
        return $this;
    }

    /**
     * Get companyRcsRm
     *
     * @return string
     */
    public function getCompanyRcsRm()
    {
        return $this->companyRcsRm;
    }

    /**
     * Set companyApe
     *
     * @param string $companyApe
     *
     * @return OneParametre
     */
    public function setCompanyApe($companyApe)
    {
        $this->companyApe = $companyApe;
    
        return $this;
    }

    /**
     * Get companyApe
     *
     * @return string
     */
    public function getCompanyApe()
    {
        return $this->companyApe;
    }

    /**
     * Set reglementClientType
     *
     * @param integer $reglementClientType
     *
     * @return OneParametre
     */
    public function setReglementClientType($reglementClientType)
    {
        $this->reglementClientType = $reglementClientType;
    
        return $this;
    }

    /**
     * Get reglementClientType
     *
     * @return integer
     */
    public function getReglementClientType()
    {
        return $this->reglementClientType;
    }

    /**
     * Set reglementClientJours
     *
     * @param integer $reglementClientJours
     *
     * @return OneParametre
     */
    public function setReglementClientJours($reglementClientJours)
    {
        $this->reglementClientJours = $reglementClientJours;
    
        return $this;
    }

    /**
     * Get reglementClientJours
     *
     * @return integer
     */
    public function getReglementClientJours()
    {
        return $this->reglementClientJours;
    }

    /**
     * Set reglementClientDate
     *
     * @param \DateTime $reglementClientDate
     *
     * @return OneParametre
     */
    public function setReglementClientDate($reglementClientDate)
    {
        $this->reglementClientDate = $reglementClientDate;
    
        return $this;
    }

    /**
     * Get reglementClientDate
     *
     * @return \DateTime
     */
    public function getReglementClientDate()
    {
        return $this->reglementClientDate;
    }

    /**
     * Set reglementFournisseurType
     *
     * @param integer $reglementFournisseurType
     *
     * @return OneParametre
     */
    public function setReglementFournisseurType($reglementFournisseurType)
    {
        $this->reglementFournisseurType = $reglementFournisseurType;
    
        return $this;
    }

    /**
     * Get reglementFournisseurType
     *
     * @return integer
     */
    public function getReglementFournisseurType()
    {
        return $this->reglementFournisseurType;
    }

    /**
     * Set reglementFournisseurJours
     *
     * @param integer $reglementFournisseurJours
     *
     * @return OneParametre
     */
    public function setReglementFournisseurJours($reglementFournisseurJours)
    {
        $this->reglementFournisseurJours = $reglementFournisseurJours;
    
        return $this;
    }

    /**
     * Get reglementFournisseurJours
     *
     * @return integer
     */
    public function getReglementFournisseurJours()
    {
        return $this->reglementFournisseurJours;
    }

    /**
     * Set reglementFournisseurDate
     *
     * @param \DateTime $reglementFournisseurDate
     *
     * @return OneParametre
     */
    public function setReglementFournisseurDate($reglementFournisseurDate)
    {
        $this->reglementFournisseurDate = $reglementFournisseurDate;
    
        return $this;
    }

    /**
     * Get reglementFournisseurDate
     *
     * @return \DateTime
     */
    public function getReglementFournisseurDate()
    {
        return $this->reglementFournisseurDate;
    }

    /**
     * Set ventePaiementCaisse
     *
     * @param integer $ventePaiementCaisse
     *
     * @return OneParametre
     */
    public function setVentePaiementCaisse($ventePaiementCaisse)
    {
        $this->ventePaiementCaisse = $ventePaiementCaisse;
    
        return $this;
    }

    /**
     * Get ventePaiementCaisse
     *
     * @return integer
     */
    public function getVentePaiementCaisse()
    {
        return $this->ventePaiementCaisse;
    }

    /**
     * Set venteTvaType
     *
     * @param integer $venteTvaType
     *
     * @return OneParametre
     */
    public function setVenteTvaType($venteTvaType)
    {
        $this->venteTvaType = $venteTvaType;
    
        return $this;
    }

    /**
     * Get venteTvaType
     *
     * @return integer
     */
    public function getVenteTvaType()
    {
        return $this->venteTvaType;
    }

    /**
     * Set comptableDateCloture
     *
     * @param string $comptableDateCloture
     *
     * @return OneParametre
     */
    public function setComptableDateCloture($comptableDateCloture)
    {
        $this->comptableDateCloture = $comptableDateCloture;
    
        return $this;
    }

    /**
     * Get comptableDateCloture
     *
     * @return string
     */
    public function getComptableDateCloture()
    {
        return $this->comptableDateCloture;
    }

    /**
     * Set comptableDevise
     *
     * @param \AppBundle\Entity\OneDevise $comptableDevise
     *
     * @return OneParametre
     */
    public function setComptableDevise(\AppBundle\Entity\OneDevise $comptableDevise = null)
    {
        $this->comptableDevise = $comptableDevise;
    
        return $this;
    }

    /**
     * Get comptableDevise
     *
     * @return string
     */
    public function getComptableDevise()
    {
        return $this->comptableDevise;
    }
}

