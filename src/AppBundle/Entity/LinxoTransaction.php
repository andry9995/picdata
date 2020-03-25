<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinxoTransaction
 *
 * @ORM\Table(name="linxo_transaction", indexes={@ORM\Index(name="fk_linxo_transaction_linxo_dossier_idx", columns={"linxo_dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinxoTransactionRepository")
 */
class LinxoTransaction
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=false)
     */
    private $dateFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_recuperation", type="date", nullable=false)
     */
    private $dateRecuperation;

    /**
     * @var string
     *
     * @ORM\Column(name="solde_debut", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $soldeDebut = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="solde_fin", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $soldeFin = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\LinxoDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LinxoDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="linxo_dossier_id", referencedColumnName="id")
     * })
     */
    private $linxoDossier;



    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return LinxoTransaction
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return LinxoTransaction
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateRecuperation
     *
     * @param \DateTime $dateRecuperation
     *
     * @return LinxoTransaction
     */
    public function setDateRecuperation($dateRecuperation)
    {
        $this->dateRecuperation = $dateRecuperation;

        return $this;
    }

    /**
     * Get dateRecuperation
     *
     * @return \DateTime
     */
    public function getDateRecuperation()
    {
        return $this->dateRecuperation;
    }

    /**
     * Set soldeDebut
     *
     * @param string $soldeDebut
     *
     * @return LinxoTransaction
     */
    public function setSoldeDebut($soldeDebut)
    {
        $this->soldeDebut = $soldeDebut;

        return $this;
    }

    /**
     * Get soldeDebut
     *
     * @return string
     */
    public function getSoldeDebut()
    {
        return $this->soldeDebut;
    }

    /**
     * Set soldeFin
     *
     * @param string $soldeFin
     *
     * @return LinxoTransaction
     */
    public function setSoldeFin($soldeFin)
    {
        $this->soldeFin = $soldeFin;

        return $this;
    }

    /**
     * Get soldeFin
     *
     * @return string
     */
    public function getSoldeFin()
    {
        return $this->soldeFin;
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
     * Set linxoDossier
     *
     * @param \AppBundle\Entity\LinxoDossier $linxoDossier
     *
     * @return LinxoTransaction
     */
    public function setLinxoDossier(\AppBundle\Entity\LinxoDossier $linxoDossier = null)
    {
        $this->linxoDossier = $linxoDossier;

        return $this;
    }

    /**
     * Get linxoDossier
     *
     * @return \AppBundle\Entity\LinxoDossier
     */
    public function getLinxoDossier()
    {
        return $this->linxoDossier;
    }
}
