<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanqueTypePcg
 *
 * @ORM\Table(name="banque_type_pcg", uniqueConstraints={@ORM\UniqueConstraint(name="unik_banque_type_pcg", columns={"type", "pcg_id", "banque_type_id"})}, indexes={@ORM\Index(name="fk_banque_type_pcg_pg_idx", columns={"pcg_id"}), @ORM\Index(name="fk_banque_type_pcg_banque_type_idx", columns={"banque_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanqueTypePcgRepository")
 */
class BanqueTypePcg
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\BanqueType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_type_id", referencedColumnName="id")
     * })
     */
    private $banqueType;

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
     * @return BanqueTypePcg
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set banqueType
     *
     * @param \AppBundle\Entity\BanqueType $banqueType
     *
     * @return BanqueTypePcg
     */
    public function setBanqueType(\AppBundle\Entity\BanqueType $banqueType = null)
    {
        $this->banqueType = $banqueType;

        return $this;
    }

    /**
     * Get banqueType
     *
     * @return \AppBundle\Entity\BanqueType
     */
    public function getBanqueType()
    {
        return $this->banqueType;
    }

    /**
     * Set pcg
     *
     * @param \AppBundle\Entity\Pcg $pcg
     *
     * @return BanqueTypePcg
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
