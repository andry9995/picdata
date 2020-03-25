<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cle2
 *
 * @ORM\Table(name="cle_2", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_cle_2_cle_cle_id", columns={"cle", "cle_id"})}, indexes={@ORM\Index(name="fk_cle_2_cle_idx", columns={"cle_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Cle2Repository")
 */
class Cle2
{
    /**
     * @var string
     *
     * @ORM\Column(name="cle", type="string", length=45, nullable=false)
     */
    private $cle;

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
    private $cle2;



    /**
     * Set cle
     *
     * @param string $cle
     *
     * @return Cle2
     */
    public function setCle($cle)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return string
     */
    public function getCle()
    {
        return $this->cle;
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
     * Set cle2
     *
     * @param \AppBundle\Entity\Cle $cle2
     *
     * @return Cle2
     */
    public function setCle2(\AppBundle\Entity\Cle $cle2 = null)
    {
        $this->cle2 = $cle2;

        return $this;
    }

    /**
     * Get cle2
     *
     * @return \AppBundle\Entity\Cle
     */
    public function getCle2()
    {
        return $this->cle2;
    }
}
