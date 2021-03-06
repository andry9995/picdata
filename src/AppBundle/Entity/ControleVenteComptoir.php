<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ControleVenteComptoir
 *
 * @ORM\Table(name="controle_vente_comptoir", indexes={@ORM\Index(name="fk_controle_vente_comptoir_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_controle_vente_comptoir_tva_taux1_idx", columns={"tva_taux_id"}), @ORM\Index(name="fk_controle_vente_comptoir_caisse_nature1_idx", columns={"caisse_nature_id"}), @ORM\Index(name="fk_controle_ventre_controir_caisse_type1_idx", columns={"caisse_type_id"})})
 * @ORM\Entity
 */
class ControleVenteComptoir
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=true)
     */
    private $libelle;

    /**
     * @var float
     *
     * @ORM\Column(name="total_ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $totalTtc;

    /**
     * @var float
     *
     * @ORM\Column(name="entrer_tva", type="float", precision=10, scale=0, nullable=true)
     */
    private $entrerTva;

    /**
     * @var float
     *
     * @ORM\Column(name="ht", type="float", precision=10, scale=0, nullable=true)
     */
    private $ht;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\CaisseType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CaisseType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="caisse_type_id", referencedColumnName="id")
     * })
     */
    private $caisseType;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;


    /**
     * @var \AppBundle\Entity\CaisseNature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CaisseNature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="caisse_nature_id", referencedColumnName="id")
     * })
     */
    private $caisseNature;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ControleVenteComptoir
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set totalTtc
     *
     * @param float $totalTtc
     *
     * @return ControleVenteComptoir
     */
    public function setTotalTtc($totalTtc)
    {
        $this->totalTtc = $totalTtc;

        return $this;
    }

    /**
     * Get totalTtc
     *
     * @return float
     */
    public function getTotalTtc()
    {
        return $this->totalTtc;
    }

    /**
     * Set entrerTva
     *
     * @param float $entrerTva
     *
     * @return ControleVenteComptoir
     */
    public function setEntrerTva($entrerTva)
    {
        $this->entrerTva = $entrerTva;

        return $this;
    }

    /**
     * Get entrerTva
     *
     * @return float
     */
    public function getEntrerTva()
    {
        return $this->entrerTva;
    }

    /**
     * Set ht
     *
     * @param float $ht
     *
     * @return ControleVenteComptoir
     */
    public function setHt($ht)
    {
        $this->ht = $ht;

        return $this;
    }

    /**
     * Get ht
     *
     * @return float
     */
    public function getHt()
    {
        return $this->ht;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ControleVenteComptoir
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * Set caisseType
     *
     * @param \AppBundle\Entity\CaisseType $caisseType
     *
     * @return ControleVenteComptoir
     */
    public function setCaisseType(\AppBundle\Entity\CaisseType $caisseType = null)
    {
        $this->caisseType = $caisseType;

        return $this;
    }

    /**
     * Get caisseType
     *
     * @return \AppBundle\Entity\CaisseType
     */
    public function getCaisseType()
    {
        return $this->caisseType;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return ControleVenteComptoir
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ControleVenteComptoir
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set caisseNature
     *
     * @param \AppBundle\Entity\CaisseNature $caisseNature
     *
     * @return ControleVenteComptoir
     */
    public function setCaisseNature(\AppBundle\Entity\CaisseNature $caisseNature = null)
    {
        $this->caisseNature = $caisseNature;

        return $this;
    }

    /**
     * Get caisseNature
     *
     * @return \AppBundle\Entity\CaisseNature
     */
    public function getCaisseNature()
    {
        return $this->caisseNature;
    }

    //============AJOUT MANUEL=================
    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="controleVenteComptoirs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    //==================FIN AJOUT MANUEL=======================
}
