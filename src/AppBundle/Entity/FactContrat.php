<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FactContrat
 *
 * @ORM\Table(name="fact_contrat", uniqueConstraints={@ORM\UniqueConstraint(name="client_id_UNIQUE", columns={"client_id"})}, indexes={@ORM\Index(name="fk_fact_contrat_client_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactContratRepository")
 */
class FactContrat
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_signature", type="date", nullable=true)
     */
    private $dateSignature;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoriser_modif", type="boolean", nullable=false)
     */
    private $autoriserModif = false;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FactContratFichier", mappedBy="factContrat")
     */
    private $factContratFichiers;



    /**
     * Set dateSignature
     *
     * @param \DateTime $dateSignature
     *
     * @return FactContrat
     */
    public function setDateSignature($dateSignature)
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    /**
     * Get dateSignature
     *
     * @return \DateTime
     */
    public function getDateSignature()
    {
        return $this->dateSignature;
    }

    /**
     * Set autoriserModif
     *
     * @param boolean $autoriserModif
     *
     * @return FactContrat
     */
    public function setAutoriserModif($autoriserModif)
    {
        $this->autoriserModif = $autoriserModif;

        return $this;
    }

    /**
     * Get autoriserModif
     *
     * @return boolean
     */
    public function getAutoriserModif()
    {
        return $this->autoriserModif;
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
     * @return FactContrat
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
     * Constructor
     */
    public function __construct()
    {
        $this->factContratFichiers = new ArrayCollection();
    }

    /**
     * Add factContratFichier
     *
     * @param \AppBundle\Entity\FactContratFichier $factContratFichier
     *
     * @return FactContrat
     */
    public function addFactContratFichier(\AppBundle\Entity\FactContratFichier $factContratFichier)
    {
        $this->factContratFichiers[] = $factContratFichier;

        return $this;
    }

    /**
     * Remove factContratFichier
     *
     * @param \AppBundle\Entity\FactContratFichier $factContratFichier
     */
    public function removeFactContratFichier(\AppBundle\Entity\FactContratFichier $factContratFichier)
    {
        $this->factContratFichiers->removeElement($factContratFichier);
    }

    /**
     * Get factContratFichiers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactContratFichiers()
    {
        return $this->factContratFichiers;
    }
}
