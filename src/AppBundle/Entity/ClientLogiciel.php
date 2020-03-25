<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientLogiciel
 *
 * @ORM\Table(name="client_logiciel", indexes={@ORM\Index(name="fk_client_logiciel_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_client_logiciel_logiciel1_idx", columns={"logiciel_id"})})
 * @ORM\Entity
 */
class ClientLogiciel
{
    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=45, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=45, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="implantation", type="string", length=45, nullable=true)
     */
    private $implantation;

    /**
     * @var string
     *
     * @ORM\Column(name="mode_travail", type="string", length=45, nullable=true)
     */
    private $modeTravail;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Logiciel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Logiciel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="logiciel_id", referencedColumnName="id")
     * })
     */
    private $logiciel;

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
     * Set ip
     *
     * @param string $ip
     *
     * @return ClientLogiciel
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return ClientLogiciel
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
     * @return ClientLogiciel
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
     * Set implantation
     *
     * @param string $implantation
     *
     * @return ClientLogiciel
     */
    public function setImplantation($implantation)
    {
        $this->implantation = $implantation;

        return $this;
    }

    /**
     * Get implantation
     *
     * @return string
     */
    public function getImplantation()
    {
        return $this->implantation;
    }

    /**
     * Set modeTravail
     *
     * @param string $modeTravail
     *
     * @return ClientLogiciel
     */
    public function setModeTravail($modeTravail)
    {
        $this->modeTravail = $modeTravail;

        return $this;
    }

    /**
     * Get modeTravail
     *
     * @return string
     */
    public function getModeTravail()
    {
        return $this->modeTravail;
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
     * Set logiciel
     *
     * @param \AppBundle\Entity\Logiciel $logiciel
     *
     * @return ClientLogiciel
     */
    public function setLogiciel(\AppBundle\Entity\Logiciel $logiciel = null)
    {
        $this->logiciel = $logiciel;

        return $this;
    }

    /**
     * Get logiciel
     *
     * @return \AppBundle\Entity\Logiciel
     */
    public function getLogiciel()
    {
        return $this->logiciel;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ClientLogiciel
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
}
