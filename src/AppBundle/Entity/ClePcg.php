<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClePcg
 *
 * @ORM\Table(name="cle_pcg", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_cle_pcg", columns={"cle_id", "pcg_id"})}, indexes={@ORM\Index(name="fk_cle_pcg_cle_idx", columns={"cle_id"}), @ORM\Index(name="fk_cle_pcg_pcg_idx", columns={"pcg_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClePcgRepository")
 */
class ClePcg
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="part_auxilliaire", type="string", length=45, nullable=true)
     */
    private $partAuxilliaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Cle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_id", referencedColumnName="id")
     * })
     */
    private $cle;

    /**
     * @var \AppBundle\Entity\Pcg
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcg_id", referencedColumnName="id")
     * })
     */
    private $pcg;



    /**
     * Set type
     *
     * @param integer $type
     *
     * @return ClePcg
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set partAuxilliaire
     *
     * @param string $partAuxilliaire
     *
     * @return ClePcg
     */
    public function setPartAuxilliaire($partAuxilliaire)
    {
        $this->partAuxilliaire = $partAuxilliaire;

        return $this;
    }

    /**
     * Get partAuxilliaire
     *
     * @return string
     */
    public function getPartAuxilliaire()
    {
        return $this->partAuxilliaire;
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
     * Set cle
     *
     * @param \AppBundle\Entity\Cle $cle
     *
     * @return ClePcg
     */
    public function setCle(\AppBundle\Entity\Cle $cle = null)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return \AppBundle\Entity\Cle
     */
    public function getCle()
    {
        return $this->cle;
    }

    /**
     * Set pcg
     *
     * @param \AppBundle\Entity\Pcg $pcg
     *
     * @return ClePcg
     */
    public function setPcg(\AppBundle\Entity\Pcg $pcg = null)
    {
        $this->pcg = $pcg;

        return $this;
    }

    /**
     * Get pcg
     *
     * @return \AppBundle\Entity\Pcg
     */
    public function getPcg()
    {
        return $this->pcg;
    }
}
