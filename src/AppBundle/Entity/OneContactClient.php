<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneContactClient
 *
 * @ORM\Table(name="one_contact_client", indexes={@ORM\Index(name="fk_contact_client_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_contact_client_one_pays1_idx", columns={"one_pays_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneContactClientRepository")
 */
class OneContactClient
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
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
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_portable", type="string", length=20, nullable=true)
     */
    private $telPortable;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_pro", type="string", length=20, nullable=true)
     */
    private $telPro;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_perso", type="string", length=20, nullable=true)
     */
    private $telPerso;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse1", type="string", length=50, nullable=true)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse2", type="string", length=50, nullable=true)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=50, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=20, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=50, nullable=true)
     */
    private $service;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=50, nullable=true)
     */
    private $fonction;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\OnePays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OnePays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_pays_id", referencedColumnName="id")
     * })
     */
    private $onePays;


    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return OneContactClient
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
     * @return OneContactClient
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
     * @return OneContactClient
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
     * Set telPortable
     *
     * @param string $telPortable
     *
     * @return OneContactClient
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
     * Set telPro
     *
     * @param string $telPro
     *
     * @return OneContactClient
     */
    public function setTelPro($telPro)
    {
        $this->telPro = $telPro;

        return $this;
    }

    /**
     * Get telPro
     *
     * @return string
     */
    public function getTelPro()
    {
        return $this->telPro;
    }

    /**
     * Set telPerso
     *
     * @param string $telPerso
     *
     * @return OneContactClient
     */
    public function setTelPerso($telPerso)
    {
        $this->telPerso = $telPerso;

        return $this;
    }

    /**
     * Get telPerso
     *
     * @return string
     */
    public function getTelPerso()
    {
        return $this->telPerso;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return OneContactClient
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return OneContactClient
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return OneContactClient
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
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return OneContactClient
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
     * Set service
     *
     * @param string $service
     *
     * @return OneContactClient
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set fonction
     *
     * @param string $fonction
     *
     * @return OneContactClient
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return string
     */
    public function getFonction()
    {
        return $this->fonction;
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
     * Set onePays
     *
     * @param \AppBundle\Entity\OnePays $onePays
     *
     * @return OneContactClient
     */
    public function setOnePays(\AppBundle\Entity\OnePays $onePays = null)
    {
        $this->onePays = $onePays;

        return $this;
    }

    /**
     * Get onePays
     *
     * @return \AppBundle\Entity\OnePays
     */
    public function getOnePays()
    {
        return $this->onePays;
    }


    /**
     * @param Tiers|null $tiers
     * @return $this
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * @return Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }



    /**
     * Set pays
     *
     * @param \AppBundle\Entity\Pays $pays
     *
     * @return OneContactClient
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
}
