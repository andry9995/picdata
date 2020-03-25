<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZendeskClient
 *
 * @ORM\Table(name="zendesk_client", indexes={@ORM\Index(name="fk_client_client_id_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ZendeskClientRepository")
 */
class ZendeskClient
{
    /**
     * @var string
     *
     * @ORM\Column(name="mail_support", type="string", length=45, nullable=true)
     */
    private $mailSupport;

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
     * Set mailSupport
     *
     * @param string $mailSupport
     *
     * @return ZendeskClient
     */
    public function setMailSupport($mailSupport)
    {
        $this->mailSupport = $mailSupport;

        return $this;
    }

    /**
     * Get mailSupport
     *
     * @return string
     */
    public function getMailSupport()
    {
        return $this->mailSupport;
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
     * @return ZendeskClient
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
