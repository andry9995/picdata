<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfFraisKilometrique
 *
 * @ORM\Table(name="ndf_frais_kilometrique", indexes={@ORM\Index(name="fk_ndf_frais_kilometrique_ndf_type_vehicule1_idx", columns={"ndf_type_vehicule_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NdfFraisKilometriqueRepository")
 */
class NdfFraisKilometrique
{
    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=false)
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="puissance_min", type="integer", nullable=true)
     */
    private $puissanceMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="puissance_max", type="integer", nullable=true)
     */
    private $puissanceMax;

    /**
     * @var float
     *
     * @ORM\Column(name="fois_1", type="float", precision=10, scale=0, nullable=true)
     */
    private $fois1;

    /**
     * @var float
     *
     * @ORM\Column(name="plus_1", type="float", precision=10, scale=0, nullable=true)
     */
    private $plus1;

    /**
     * @var float
     *
     * @ORM\Column(name="fois_2", type="float", precision=10, scale=0, nullable=true)
     */
    private $fois2;

    /**
     * @var float
     *
     * @ORM\Column(name="plus_2", type="float", precision=10, scale=0, nullable=true)
     */
    private $plus2;

    /**
     * @var float
     *
     * @ORM\Column(name="fois_3", type="float", precision=10, scale=0, nullable=true)
     */
    private $fois3;

    /**
     * @var float
     *
     * @ORM\Column(name="plus_3", type="float", precision=10, scale=0, nullable=true)
     */
    private $plus3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfTypeVehicule
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfTypeVehicule")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_type_vehicule_id", referencedColumnName="id")
     * })
     */
    private $ndfTypeVehicule;



    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return NdfFraisKilometrique
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return NdfFraisKilometrique
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
     * Set puissanceMin
     *
     * @param integer $puissanceMin
     *
     * @return NdfFraisKilometrique
     */
    public function setPuissanceMin($puissanceMin)
    {
        $this->puissanceMin = $puissanceMin;

        return $this;
    }

    /**
     * Get puissanceMin
     *
     * @return integer
     */
    public function getPuissanceMin()
    {
        return $this->puissanceMin;
    }

    /**
     * Set puissanceMax
     *
     * @param integer $puissanceMax
     *
     * @return NdfFraisKilometrique
     */
    public function setPuissanceMax($puissanceMax)
    {
        $this->puissanceMax = $puissanceMax;

        return $this;
    }

    /**
     * Get puissanceMax
     *
     * @return integer
     */
    public function getPuissanceMax()
    {
        return $this->puissanceMax;
    }

    /**
     * Set fois1
     *
     * @param float $fois1
     *
     * @return NdfFraisKilometrique
     */
    public function setFois1($fois1)
    {
        $this->fois1 = $fois1;

        return $this;
    }

    /**
     * Get fois1
     *
     * @return float
     */
    public function getFois1()
    {
        return $this->fois1;
    }

    /**
     * Set plus1
     *
     * @param float $plus1
     *
     * @return NdfFraisKilometrique
     */
    public function setPlus1($plus1)
    {
        $this->plus1 = $plus1;

        return $this;
    }

    /**
     * Get plus1
     *
     * @return float
     */
    public function getPlus1()
    {
        return $this->plus1;
    }

    /**
     * Set fois2
     *
     * @param float $fois2
     *
     * @return NdfFraisKilometrique
     */
    public function setFois2($fois2)
    {
        $this->fois2 = $fois2;

        return $this;
    }

    /**
     * Get fois2
     *
     * @return float
     */
    public function getFois2()
    {
        return $this->fois2;
    }

    /**
     * Set plus2
     *
     * @param float $plus2
     *
     * @return NdfFraisKilometrique
     */
    public function setPlus2($plus2)
    {
        $this->plus2 = $plus2;

        return $this;
    }

    /**
     * Get plus2
     *
     * @return float
     */
    public function getPlus2()
    {
        return $this->plus2;
    }

    /**
     * Set fois3
     *
     * @param float $fois3
     *
     * @return NdfFraisKilometrique
     */
    public function setFois3($fois3)
    {
        $this->fois3 = $fois3;

        return $this;
    }

    /**
     * Get fois3
     *
     * @return float
     */
    public function getFois3()
    {
        return $this->fois3;
    }

    /**
     * Set plus3
     *
     * @param float $plus3
     *
     * @return NdfFraisKilometrique
     */
    public function setPlus3($plus3)
    {
        $this->plus3 = $plus3;

        return $this;
    }

    /**
     * Get plus3
     *
     * @return float
     */
    public function getPlus3()
    {
        return $this->plus3;
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
     * Set ndfTypeVehicule
     *
     * @param \AppBundle\Entity\NdfTypeVehicule $ndfTypeVehicule
     *
     * @return NdfFraisKilometrique
     */
    public function setNdfTypeVehicule(\AppBundle\Entity\NdfTypeVehicule $ndfTypeVehicule = null)
    {
        $this->ndfTypeVehicule = $ndfTypeVehicule;

        return $this;
    }

    /**
     * Get ndfTypeVehicule
     *
     * @return \AppBundle\Entity\NdfTypeVehicule
     */
    public function getNdfTypeVehicule()
    {
        return $this->ndfTypeVehicule;
    }
}
