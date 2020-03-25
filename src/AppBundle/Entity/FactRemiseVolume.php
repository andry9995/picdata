<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactRemiseVolume
 *
 * @ORM\Table(name="fact_remise_volume", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"})}, indexes={@ORM\Index(name="fk_remise_volume_remise1_idx", columns={"fact_remise_niveau_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactRemiseVolumeRepository")
 */
class FactRemiseVolume
{
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="tranche1", type="integer", nullable=false)
     */
    private $tranche1;

    /**
     * @var integer
     *
     * @ORM\Column(name="tranche2", type="integer", nullable=false)
     */
    private $tranche2;

    /**
     * @var float
     *
     * @ORM\Column(name="pourcentage", type="float", precision=4, scale=2, nullable=true)
     */
    private $pourcentage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactRemiseNiveau
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactRemiseNiveau")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_remise_niveau_id", referencedColumnName="id")
     * })
     */
    private $factRemiseNiveau;



    /**
     * Set code
     *
     * @param integer $code
     *
     * @return FactRemiseVolume
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set tranche1
     *
     * @param integer $tranche1
     *
     * @return FactRemiseVolume
     */
    public function setTranche1($tranche1)
    {
        $this->tranche1 = $tranche1;

        return $this;
    }

    /**
     * Get tranche1
     *
     * @return integer
     */
    public function getTranche1()
    {
        return $this->tranche1;
    }

    /**
     * Set tranche2
     *
     * @param integer $tranche2
     *
     * @return FactRemiseVolume
     */
    public function setTranche2($tranche2)
    {
        $this->tranche2 = $tranche2;

        return $this;
    }

    /**
     * Get tranche2
     *
     * @return integer
     */
    public function getTranche2()
    {
        return $this->tranche2;
    }

    /**
     * Set pourcentage
     *
     * @param float $pourcentage
     *
     * @return FactRemiseVolume
     */
    public function setPourcentage($pourcentage)
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    /**
     * Get pourcentage
     *
     * @return float
     */
    public function getPourcentage()
    {
        return $this->pourcentage;
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
     * Set factRemiseNiveau
     *
     * @param \AppBundle\Entity\FactRemiseNiveau $factRemiseNiveau
     *
     * @return FactRemiseVolume
     */
    public function setFactRemiseNiveau(\AppBundle\Entity\FactRemiseNiveau $factRemiseNiveau = null)
    {
        $this->factRemiseNiveau = $factRemiseNiveau;

        return $this;
    }

    /**
     * Get factRemiseNiveau
     *
     * @return \AppBundle\Entity\FactRemiseNiveau
     */
    public function getFactRemiseNiveau()
    {
        return $this->factRemiseNiveau;
    }
}
