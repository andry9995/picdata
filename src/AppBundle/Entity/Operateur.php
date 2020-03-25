<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operateur
 *
 * @ORM\Table(name="operateur", indexes={@ORM\Index(name="fk_operateur_acces_idx", columns={"acces_operateur_id"}), @ORM\Index(name="fk_operateur_fonction_idx", columns={"fonction_id"}), @ORM\Index(name="fk_operateur_poste1_idx", columns={"poste_id"})})
 * @ORM\Entity
 */
class Operateur
{
    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=50, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=250, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=250, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="text", length=65535, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=45, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=1, nullable=true)
     */
    private $sexe;

    /**
     * @var integer
     *
     * @ORM\Column(name="supprimer", type="integer", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="matricule", type="string", length=45, nullable=true)
     */
    private $matricule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Poste
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Poste")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="poste_id", referencedColumnName="id")
     * })
     */
    private $poste;

    /**
     * @var \AppBundle\Entity\Fonction
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Fonction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fonction_id", referencedColumnName="id")
     * })
     */
    private $fonction;

    /**
     * @var \AppBundle\Entity\AccesOperateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AccesOperateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="acces_operateur_id", referencedColumnName="id")
     * })
     */
    private $accesOperateur;



    /**
     * Set login
     *
     * @param string $login
     *
     * @return Operateur
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Operateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Operateur
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
     * @return Operateur
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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Operateur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Operateur
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Operateur
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set supprimer
     *
     * @param integer $supprimer
     *
     * @return Operateur
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return integer
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Operateur
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
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
     * Set poste
     *
     * @param \AppBundle\Entity\Poste $poste
     *
     * @return Operateur
     */
    public function setPoste(\AppBundle\Entity\Poste $poste = null)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return \AppBundle\Entity\Poste
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set fonction
     *
     * @param \AppBundle\Entity\Fonction $fonction
     *
     * @return Operateur
     */
    public function setFonction(\AppBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \AppBundle\Entity\Fonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set accesOperateur
     *
     * @param \AppBundle\Entity\AccesOperateur $accesOperateur
     *
     * @return Operateur
     */
    public function setAccesOperateur(\AppBundle\Entity\AccesOperateur $accesOperateur = null)
    {
        $this->accesOperateur = $accesOperateur;

        return $this;
    }

    /**
     * Get accesOperateur
     *
     * @return \AppBundle\Entity\AccesOperateur
     */
    public function getAccesOperateur()
    {
        return $this->accesOperateur;
    }
}
