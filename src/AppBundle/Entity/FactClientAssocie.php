<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactClientAssocie
 *
 * @ORM\Table(name="fact_client_associe", indexes={@ORM\Index(name="fk_client_associe_client1_idx", columns={"client_principale"}), @ORM\Index(name="fk_client_associe_client2_idx", columns={"client_autre"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactClientAssocieRepository")
 */
class FactClientAssocie
{
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
     *   @ORM\JoinColumn(name="client_autre", referencedColumnName="id")
     * })
     */
    private $clientAutre;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_principal", referencedColumnName="id")
     * })
     */
    private $clientPrincipal;



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
     * Set clientAutre
     *
     * @param \AppBundle\Entity\Client $clientAutre
     *
     * @return FactClientAssocie
     */
    public function setClientAutre(\AppBundle\Entity\Client $clientAutre = null)
    {
        $this->clientAutre = $clientAutre;

        return $this;
    }

    /**
     * Get clientAutre
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClientAutre()
    {
        return $this->clientAutre;
    }

    /**
     * Set clientPrincipale
     *
     * @param \AppBundle\Entity\Client $clientPrincipal
     *
     * @return FactClientAssocie
     */
    public function setClientPrincipal(\AppBundle\Entity\Client $clientPrincipal = null)
    {
        $this->clientPrincipal = $clientPrincipal;

        return $this;
    }

    /**
     * Get clientPrincipal
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClientPrincipal()
    {
        return $this->clientPrincipal;
    }
}
