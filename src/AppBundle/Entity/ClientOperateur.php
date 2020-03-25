<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOperateur
 *
 * @ORM\Table(name="client_operateur", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_client_operateur", columns={"client_id", "operateur_id"})}, indexes={@ORM\Index(name="fk_client_has_operateur_operateur1_idx", columns={"operateur_id"}), @ORM\Index(name="fk_client_has_operateur_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_client_operateur_fonction_client1_idx", columns={"fonction_client_id"})})
 * @ORM\Entity
 */
class ClientOperateur
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
     * @var \AppBundle\Entity\FonctionClient
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FonctionClient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fonction_client_id", referencedColumnName="id")
     * })
     */
    private $fonctionClient;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     * })
     */
    private $operateur;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fonctionClient
     *
     * @param \AppBundle\Entity\FonctionClient $fonctionClient
     *
     * @return ClientOperateur
     */
    public function setFonctionClient(\AppBundle\Entity\FonctionClient $fonctionClient = null)
    {
        $this->fonctionClient = $fonctionClient;

        return $this;
    }

    /**
     * Get fonctionClient
     *
     * @return \AppBundle\Entity\FonctionClient
     */
    public function getFonctionClient()
    {
        return $this->fonctionClient;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return ClientOperateur
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ClientOperateur
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
