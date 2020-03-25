<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Linxo
 *
 * @ORM\Table(name="linxo", uniqueConstraints={@ORM\UniqueConstraint(name="login_UNIQUE", columns={"login"}), @ORM\UniqueConstraint(name="id_compte_linxo_UNIQUE", columns={"id_compte_linxo"})}, indexes={@ORM\Index(name="fk_linxo_client_idx", columns={"client_id"}), @ORM\Index(name="fk_linxo_site_idx", columns={"site_id"}), @ORM\Index(name="fk_linxo_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinxoRepository")
 */
class Linxo
{
    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=45, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="mp", type="string", length=45, nullable=false)
     */
    private $mp = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="entreprise", type="string", length=45, nullable=true)
     */
    private $entreprise;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

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
     * @ORM\Column(name="json_code", type="text", nullable=true)
     */
    private $jsonCode;

    /**
     * @var string
     *
     * @ORM\Column(name="id_compte_linxo", type="string", length=45, nullable=true)
     */
    private $idCompteLinxo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

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
     * Set login
     *
     * @param string $login
     *
     * @return Linxo
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
     * Set mp
     *
     * @param string $mp
     *
     * @return Linxo
     */
    public function setMp($mp)
    {
        $this->mp = $mp;

        return $this;
    }

    /**
     * Get mp
     *
     * @return string
     */
    public function getMp()
    {
        return $this->mp;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Linxo
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
     * Set entreprise
     *
     * @param string $entreprise
     *
     * @return Linxo
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Linxo
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Linxo
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
     * @return Linxo
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
     * Set jsonCode
     *
     * @param string $jsonCode
     *
     * @return Linxo
     */
    public function setJsonCode($jsonCode)
    {
        $this->jsonCode = $jsonCode;

        return $this;
    }

    /**
     * Get jsonCode
     *
     * @return string
     */
    public function getJsonCode()
    {
        return $this->jsonCode;
    }

    /**
     * Set idCompteLinxo
     *
     * @param string $idCompteLinxo
     *
     * @return Linxo
     */
    public function setIdCompteLinxo($idCompteLinxo)
    {
        $this->idCompteLinxo = $idCompteLinxo;

        return $this;
    }

    /**
     * Get idCompteLinxo
     *
     * @return string
     */
    public function getIdCompteLinxo()
    {
        return $this->idCompteLinxo;
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
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     *
     * @return Linxo
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Linxo
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Linxo
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
}
