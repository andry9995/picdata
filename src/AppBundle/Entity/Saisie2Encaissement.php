<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Saisie2Encaissement
 *
 * @ORM\Table(name="saisie2_encaissement", indexes={@ORM\Index(name="fk_saisie2_encaissement_image1_idx", columns={"image_id"})})
 * @ORM\Entity
 */
class Saisie2Encaissement
{
    /**
     * @var float
     *
     * @ORM\Column(name="espece_tva1", type="float", precision=10, scale=0, nullable=true)
     */
    private $especeTva1;

    /**
     * @var float
     *
     * @ORM\Column(name="espece_tva2", type="float", precision=10, scale=0, nullable=true)
     */
    private $especeTva2;

    /**
     * @var float
     *
     * @ORM\Column(name="espece_tva3", type="float", precision=10, scale=0, nullable=true)
     */
    private $especeTva3;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque_tva1", type="float", precision=10, scale=0, nullable=true)
     */
    private $chequeTva1;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque_tva2", type="float", precision=10, scale=0, nullable=true)
     */
    private $chequeTva2;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque_tva3", type="float", precision=10, scale=0, nullable=true)
     */
    private $chequeTva3;

    /**
     * @var float
     *
     * @ORM\Column(name="cb_tva1", type="float", precision=10, scale=0, nullable=true)
     */
    private $cbTva1;

    /**
     * @var float
     *
     * @ORM\Column(name="cb_tva2", type="float", precision=10, scale=0, nullable=true)
     */
    private $cbTva2;

    /**
     * @var float
     *
     * @ORM\Column(name="cb_tva3", type="float", precision=10, scale=0, nullable=true)
     */
    private $cbTva3;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque_resto", type="float", precision=10, scale=0, nullable=true)
     */
    private $chequeResto;

    /**
     * @var float
     *
     * @ORM\Column(name="cheque_kdo", type="float", precision=10, scale=0, nullable=true)
     */
    private $chequeKdo;

    /**
     * @var float
     *
     * @ORM\Column(name="autres", type="float", precision=10, scale=0, nullable=true)
     */
    private $autres;

    /**
     * @var string
     *
     * @ORM\Column(name="produit", type="string", length=100, nullable=false)
     */
    private $produit;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



    /**
     * Set especeTva1
     *
     * @param float $especeTva1
     *
     * @return Saisie2Encaissement
     */
    public function setEspeceTva1($especeTva1)
    {
        $this->especeTva1 = $especeTva1;

        return $this;
    }

    /**
     * Get especeTva1
     *
     * @return float
     */
    public function getEspeceTva1()
    {
        return $this->especeTva1;
    }

    /**
     * Set especeTva2
     *
     * @param float $especeTva2
     *
     * @return Saisie2Encaissement
     */
    public function setEspeceTva2($especeTva2)
    {
        $this->especeTva2 = $especeTva2;

        return $this;
    }

    /**
     * Get especeTva2
     *
     * @return float
     */
    public function getEspeceTva2()
    {
        return $this->especeTva2;
    }

    /**
     * Set especeTva3
     *
     * @param float $especeTva3
     *
     * @return Saisie2Encaissement
     */
    public function setEspeceTva3($especeTva3)
    {
        $this->especeTva3 = $especeTva3;

        return $this;
    }

    /**
     * Get especeTva3
     *
     * @return float
     */
    public function getEspeceTva3()
    {
        return $this->especeTva3;
    }

    /**
     * Set chequeTva1
     *
     * @param float $chequeTva1
     *
     * @return Saisie2Encaissement
     */
    public function setChequeTva1($chequeTva1)
    {
        $this->chequeTva1 = $chequeTva1;

        return $this;
    }

    /**
     * Get chequeTva1
     *
     * @return float
     */
    public function getChequeTva1()
    {
        return $this->chequeTva1;
    }

    /**
     * Set chequeTva2
     *
     * @param float $chequeTva2
     *
     * @return Saisie2Encaissement
     */
    public function setChequeTva2($chequeTva2)
    {
        $this->chequeTva2 = $chequeTva2;

        return $this;
    }

    /**
     * Get chequeTva2
     *
     * @return float
     */
    public function getChequeTva2()
    {
        return $this->chequeTva2;
    }

    /**
     * Set chequeTva3
     *
     * @param float $chequeTva3
     *
     * @return Saisie2Encaissement
     */
    public function setChequeTva3($chequeTva3)
    {
        $this->chequeTva3 = $chequeTva3;

        return $this;
    }

    /**
     * Get chequeTva3
     *
     * @return float
     */
    public function getChequeTva3()
    {
        return $this->chequeTva3;
    }

    /**
     * Set cbTva1
     *
     * @param float $cbTva1
     *
     * @return Saisie2Encaissement
     */
    public function setCbTva1($cbTva1)
    {
        $this->cbTva1 = $cbTva1;

        return $this;
    }

    /**
     * Get cbTva1
     *
     * @return float
     */
    public function getCbTva1()
    {
        return $this->cbTva1;
    }

    /**
     * Set cbTva2
     *
     * @param float $cbTva2
     *
     * @return Saisie2Encaissement
     */
    public function setCbTva2($cbTva2)
    {
        $this->cbTva2 = $cbTva2;

        return $this;
    }

    /**
     * Get cbTva2
     *
     * @return float
     */
    public function getCbTva2()
    {
        return $this->cbTva2;
    }

    /**
     * Set cbTva3
     *
     * @param float $cbTva3
     *
     * @return Saisie2Encaissement
     */
    public function setCbTva3($cbTva3)
    {
        $this->cbTva3 = $cbTva3;

        return $this;
    }

    /**
     * Get cbTva3
     *
     * @return float
     */
    public function getCbTva3()
    {
        return $this->cbTva3;
    }

    /**
     * Set chequeResto
     *
     * @param float $chequeResto
     *
     * @return Saisie2Encaissement
     */
    public function setChequeResto($chequeResto)
    {
        $this->chequeResto = $chequeResto;

        return $this;
    }

    /**
     * Get chequeResto
     *
     * @return float
     */
    public function getChequeResto()
    {
        return $this->chequeResto;
    }

    /**
     * Set chequeKdo
     *
     * @param float $chequeKdo
     *
     * @return Saisie2Encaissement
     */
    public function setChequeKdo($chequeKdo)
    {
        $this->chequeKdo = $chequeKdo;

        return $this;
    }

    /**
     * Get chequeKdo
     *
     * @return float
     */
    public function getChequeKdo()
    {
        return $this->chequeKdo;
    }

    /**
     * Set autres
     *
     * @param float $autres
     *
     * @return Saisie2Encaissement
     */
    public function setAutres($autres)
    {
        $this->autres = $autres;

        return $this;
    }

    /**
     * Get autres
     *
     * @return float
     */
    public function getAutres()
    {
        return $this->autres;
    }

    /**
     * Set produit
     *
     * @param string $produit
     *
     * @return Saisie2Encaissement
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return string
     */
    public function getProduit()
    {
        return $this->produit;
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Saisie2Encaissement
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
}
