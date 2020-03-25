<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReglePaiementClient
 *
 * @ORM\Table(name="regle_paiement_client", indexes={@ORM\Index(name="fk_regle_paiement_client_client1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReglePaiementClientRepository")
 */
class ReglePaiementClient
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_date", type="integer", nullable=true)
     */
    private $typeDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_jour", type="integer", nullable=true)
     */
    private $nbreJour;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_le", type="integer", nullable=true)
     */
    private $dateLe;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_tiers", type="integer", nullable=true)
     */
    private $typeTiers;

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
     * Set typeDate
     *
     * @param integer $typeDate
     *
     * @return ReglePaiementClient
     */
    public function setTypeDate($typeDate)
    {
        $this->typeDate = $typeDate;

        return $this;
    }

    /**
     * Get typeDate
     *
     * @return integer
     */
    public function getTypeDate()
    {
        return $this->typeDate;
    }

    /**
     * Set nbreJour
     *
     * @param integer $nbreJour
     *
     * @return ReglePaiementClient
     */
    public function setNbreJour($nbreJour)
    {
        $this->nbreJour = $nbreJour;

        return $this;
    }

    /**
     * Get nbreJour
     *
     * @return integer
     */
    public function getNbreJour()
    {
        return $this->nbreJour;
    }

    /**
     * Set dateLe
     *
     * @param integer $dateLe
     *
     * @return ReglePaiementClient
     */
    public function setDateLe($dateLe)
    {
        $this->dateLe = $dateLe;

        return $this;
    }

    /**
     * Get dateLe
     *
     * @return integer
     */
    public function getDateLe()
    {
        return $this->dateLe;
    }

    /**
     * Set typeTiers
     *
     * @param integer $typeTiers
     *
     * @return ReglePaiementClient
     */
    public function setTypeTiers($typeTiers)
    {
        $this->typeTiers = $typeTiers;

        return $this;
    }

    /**
     * Get typeTiers
     *
     * @return integer
     */
    public function getTypeTiers()
    {
        return $this->typeTiers;
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
     * @return ReglePaiementClient
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
