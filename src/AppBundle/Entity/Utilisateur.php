<?php

namespace AppBundle\Entity;

use AppBundle\Controller\Boost;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="login_UNIQUE", columns={"login"}), @ORM\UniqueConstraint(name="email_UNIQUE", columns={"email"})}, indexes={@ORM\Index(name="fk_utilisateur_type_utilisateur1_idx", columns={"type_utilisateur_id"}), @ORM\Index(name="fk_utilisateur_acces_utilisateur1_idx", columns={"acces_utilisateur_id"}), @ORM\Index(name="fk_utilisateur_client1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UtilisateurRepository")
 */
class Utilisateur implements UserInterface, AdvancedUserInterface
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
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=50, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", length=65535, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=20, nullable=true)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=255, nullable=true)
     */
    private $societe;

    /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=50, nullable=true)
     */
    private $skype;

    /**
     * @var bool
     *
     * @ORM\Column(name="supprimer", type="boolean", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=250, nullable=true)
     */
    private $photo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_request_date", type="date", nullable=true)
     */
    private $passwordRequestDate;

    /**
     * @var string
     *
     * @ORM\Column(name="password_request_token", type="text", length=65535, nullable=true)
     */
    private $passwordRequestToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_dossier_demo", type="boolean", nullable=false)
     */
    private $showDossierDemo = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="credentials_expired_on", type="datetime", nullable=true)
     */
    private $credentialsExpiredOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TypeUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_utilisateur_id", referencedColumnName="id")
     * })
     */
    private $typeUtilisateur;

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
     * @var \AppBundle\Entity\AccesUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AccesUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="acces_utilisateur_id", referencedColumnName="id")
     * })
     */
    private $accesUtilisateur;


    /* Ajout manuel: relation OneToMany */
    /**
     * Listes des clients d'un utilisateur
     * Un utilisateur peut avoir +sieurs clients
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UtilisateurClient", mappedBy="utilisateur")
     */
    private $clients;

    /**
     * Liste des sites d'un utilisateur
     * Un utilisateur peut avoir +sieurs sites
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UtilisateurSite", mappedBy="utilisateur")
     */
    private $sites;

    /**
     * Listes des dossiers d'un utilisateur
     * Un utilisateur peut avoir +sieurs dossiers
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UtilisateurDossier", mappedBy="utilisateur")
     */
    private $dossiers;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;


    /**
     * Initialiser les valeurs des entités inverses
     *
     * Utilisateur constructor.
     */
    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->sites = new ArrayCollection();
        $this->dossiers = new ArrayCollection();
    }

    /** METHODES POUR LOGIN */
    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $role = $this->getAccesUtilisateur()->getCode();
        return array($role);
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }
    /* FIN METHODES POUR LOGIN */

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Set showInDemo
     *
     * @param boolean $showInDemo
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function setShowDossierDemo($showInDemo)
    {
        $this->showDossierDemo = $showInDemo;

        return $this;
    }

    /**
     * Get showInDemo
     *
     * @return boolean
     */
    public function getShowDossierDemo()
    {
        return $this->showDossierDemo;
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
     * @return Utilisateur
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
     * @return Utilisateur
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
     * Set login
     *
     * @param string $login
     *
     * @return Utilisateur
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
     * @return Utilisateur
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
     * Set tel
     *
     * @param string $tel
     *
     * @return Utilisateur
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
     * Set societe
     *
     * @param string $societe
     *
     * @return Utilisateur
     */
    public function setSociete($societe)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return string
     */
    public function getSociete()
    {
        return $this->societe;
    }

    /**
     * Set skype
     *
     * @param string $skype
     *
     * @return Utilisateur
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
     * Set supprimer
     *
     * @param bool $supprimer
     *
     * @return Utilisateur
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return bool
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Utilisateur
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return Utilisateur
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set passwordRequestDate
     *
     * @param \DateTime $passwordRequestDate
     *
     * @return Utilisateur
     */
    public function setPasswordRequestDate($passwordRequestDate)
    {
        $this->passwordRequestDate = $passwordRequestDate;

        return $this;
    }

    /**
     * Get passwordRequestDate
     *
     * @return \DateTime
     */
    public function getPasswordRequestDate()
    {
        return $this->passwordRequestDate;
    }

    /**
     * Set passwordRequestToken
     *
     * @param string $passwordRequestToken
     *
     * @return Utilisateur
     */
    public function setPasswordRequestToken($passwordRequestToken)
    {
        $this->passwordRequestToken = $passwordRequestToken;

        return $this;
    }

    /**
     * Get passwordRequestToken
     *
     * @return string
     */
    public function getPasswordRequestToken()
    {
        return $this->passwordRequestToken;
    }

    /**
     * Set credentialsExpiredOn
     *
     * @param \DateTime $credentialsExpiredOn
     *
     * @return Utilisateur
     */
    public function setCredentialsExpiredOn($credentialsExpiredOn)
    {
        $this->credentialsExpiredOn = $credentialsExpiredOn;

        return $this;
    }

    /**
     * Get credentialsExpiredOn
     *
     * @return \DateTime
     */
    public function getCredentialsExpiredOn()
    {
        return $this->credentialsExpiredOn;
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
     * Set typeUtilisateur
     *
     * @param \AppBundle\Entity\TypeUtilisateur $typeUtilisateur
     *
     * @return Utilisateur
     */
    public function setTypeUtilisateur(\AppBundle\Entity\TypeUtilisateur $typeUtilisateur = null)
    {
        $this->typeUtilisateur = $typeUtilisateur;

        return $this;
    }

    /**
     * Get typeUtilisateur
     *
     * @return \AppBundle\Entity\TypeUtilisateur
     */
    public function getTypeUtilisateur()
    {
        return $this->typeUtilisateur;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Utilisateur
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

    /**
     * Set accesUtilisateur
     *
     * @param \AppBundle\Entity\AccesUtilisateur $accesUtilisateur
     *
     * @return Utilisateur
     */
    public function setAccesUtilisateur(\AppBundle\Entity\AccesUtilisateur $accesUtilisateur = null)
    {
        $this->accesUtilisateur = $accesUtilisateur;

        return $this;
    }

    /**
     * Get accesUtilisateur
     *
     * @return \AppBundle\Entity\AccesUtilisateur
     */
    public function getAccesUtilisateur()
    {
        return $this->accesUtilisateur;
    }

    /* Ajout manuel */
    /**
     * Nom complet de l'utilisateur
     *
     * @return string
     */
    public function getNomComplet()
    {
        $the_prenom = $this->prenom ? $this->prenom : "";
        $the_nom = mb_strtoupper($this->nom ? $this->nom : "", 'UTF-8');
        $nom_complet = trim($the_nom . " " . $the_prenom) != "" ? trim($the_nom . " " . $the_prenom) : $this->email;
        return $nom_complet;
    }

    public function getIdCrypter()
    {
        return Boost::boost($this->id);
    }

    /**
     * Add client
     *
     * @param \AppBundle\Entity\UtilisateurClient $client
     *
     * @return Utilisateur
     */
    public function addClient(\AppBundle\Entity\UtilisateurClient $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client
     *
     * @param \AppBundle\Entity\UtilisateurClient $client
     */
    public function removeClient(\AppBundle\Entity\UtilisateurClient $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Add site
     *
     * @param \AppBundle\Entity\UtilisateurSite $site
     *
     * @return Utilisateur
     */
    public function addSite(\AppBundle\Entity\UtilisateurSite $site)
    {
        $this->sites[] = $site;

        return $this;
    }

    /**
     * Remove site
     *
     * @param \AppBundle\Entity\UtilisateurSite $site
     */
    public function removeSite(\AppBundle\Entity\UtilisateurSite $site)
    {
        $this->sites->removeElement($site);
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Add dossier
     *
     * @param \AppBundle\Entity\UtilisateurDossier $dossier
     *
     * @return Utilisateur
     */
    public function addDossier(\AppBundle\Entity\UtilisateurDossier $dossier)
    {
        $this->dossiers[] = $dossier;

        return $this;
    }

    /**
     * Remove dossier
     *
     * @param \AppBundle\Entity\UtilisateurDossier $dossier
     */
    public function removeDossier(\AppBundle\Entity\UtilisateurDossier $dossier)
    {
        $this->dossiers->removeElement($dossier);
    }

    /**
     * Get dossiers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDossiers()
    {
        return $this->dossiers;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        // TODO: Implement isAccountNonExpired() method.
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        // TODO: Implement isAccountNonLocked() method.
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        if (!$this->getCredentialsExpiredOn()) {
            return TRUE;
        }
        $now = new \DateTime();
        return $this->getCredentialsExpiredOn() >= $now;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        if($this->getClient() !== null) {
            if ($this->getClient()->getStatus() !== 1)
                return false;
        }

        return !$this->getSupprimer();
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Utilisateur
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    } 
}
