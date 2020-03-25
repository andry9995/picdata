<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleCompte
 *
 * @ORM\Table(name="cle_compte", indexes={@ORM\Index(name="fk_cle_compte_cle_idx", columns={"cle_id"}), @ORM\Index(name="fk_cle_compte_pcg_idx", columns={"pcg_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleCompteRepository")
 */
class CleCompte
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
     * @var \AppBundle\Entity\Pcg
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcg_id", referencedColumnName="id")
     * })
     */
    private $pcg;

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
     * Set type
     *
     * @param integer $type
     *
     * @return CleCompte
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
     * Set pcg
     *
     * @param \AppBundle\Entity\Pcg $pcg
     *
     * @return CleCompte
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

    /**
     * Set cle
     *
     * @param \AppBundle\Entity\Cle $cle
     *
     * @return CleCompte
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
}
