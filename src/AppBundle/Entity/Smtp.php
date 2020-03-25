<?php

namespace AppBundle\Entity;

use AppBundle\Controller\Boost;
use Doctrine\ORM\Mapping as ORM;

/**
 * Smtp
 *
 * @ORM\Table(name="smtp", indexes={@ORM\Index(name="fk_param_smtp_client1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SmtpRepository")
 */
class Smtp
{
    /**
     * @var string
     *
     * @ORM\Column(name="smtp", type="string", length=50, nullable=false)
     */
    private $smtp;

    /**
     * @var integer
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port;

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
     * @ORM\Column(name="copie", type="string", length=500, nullable=false)
     */
    private $copie;

    /**
     * @var string
     *
     * @ORM\Column(name="certificate", type="string", length=45, nullable=true)
     */
    private $certificate = '';

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
     * Set smtp
     *
     * @param string $smtp
     *
     * @return Smtp
     */
    public function setSmtp($smtp)
    {
        $this->smtp = $smtp;

        return $this;
    }

    /**
     * Get smtp
     *
     * @return string
     */
    public function getSmtp()
    {
        return $this->smtp;
    }

    /**
     * Set port
     *
     * @param integer $port
     *
     * @return Smtp
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return Smtp
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
     * @return Smtp
     */
    public function setPassword($password)
    {
        $this->password = Boost::boost($password);

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return Boost::deboostWithoutController($this->password);
    }


    /**
     * Set password
     *
     * @param string $copie
     *
     * @return Smtp
     */
    public function setCopie($copie)
    {
        $this->copie = $copie;

        return $this;
    }

    /**
     * Get copie
     *
     * @return string
     */
    public function getCopie()
    {
       return $this->copie;
    }

    /**
     * Set certificate
     *
     * @param string $certificate
     *
     * @return Smtp
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * Get certificate
     *
     * @return string
     */
    public function getCertificate()
    {
        return $this->certificate;
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
     * @return Smtp
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
