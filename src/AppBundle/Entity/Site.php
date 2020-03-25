<?php

namespace AppBundle\Entity;

use AppBundle\Controller\Boost;
use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site", uniqueConstraints={@ORM\UniqueConstraint(name="fk_unik_site_client", columns={"nom", "client_id"})}, indexes={@ORM\Index(name="fk_site_cabinet1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 */
class Site
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
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @return Site
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
     * Set status
     *
     * @param integer $status
     *
     * @return Site
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
     * Set nbCaractere
     *
     * @param integer $nbCaractere
     *
     * @return Site
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
     * @return Site
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
     * @return Site
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
     * @return Site
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
     * @return Site
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Site
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

    /* Ajout manuel */
    private $idCrypter;

    public function getIdCrypter()
    {
        $this->idCrypter = Boost::boost($this->getId());
        return $this->idCrypter;
    }
}
