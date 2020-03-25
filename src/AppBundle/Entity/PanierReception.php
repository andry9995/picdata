<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PanierReception
 *
 * @ORM\Table(name="panier_reception", indexes={@ORM\Index(name="fk_panier_reception_operateur1_idx", columns={"operateur_id"}), @ORM\Index(name="fk_panier_reception_lot1_idx", columns={"lot_id"})})
 * @ORM\Entity
 */
class PanierReception
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_panier", type="date", nullable=true)
     */
    private $datePanier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Lot
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_id", referencedColumnName="id")
     * })
     */
    private $lot;



    /**
     * Set datePanier
     *
     * @param \DateTime $datePanier
     *
     * @return PanierReception
     */
    public function setDatePanier($datePanier)
    {
        $this->datePanier = $datePanier;

        return $this;
    }

    /**
     * Get datePanier
     *
     * @return \DateTime
     */
    public function getDatePanier()
    {
        return $this->datePanier;
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
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return PanierReception
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
     * Set lot
     *
     * @param \AppBundle\Entity\Lot $lot
     *
     * @return PanierReception
     */
    public function setLot(\AppBundle\Entity\Lot $lot = null)
    {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return \AppBundle\Entity\Lot
     */
    public function getLot()
    {
        return $this->lot;
    }
}
