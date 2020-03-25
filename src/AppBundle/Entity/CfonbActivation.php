<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CfonbActivation
 *
 * @ORM\Table(name="cfonb_activation", indexes={@ORM\Index(name="fk_cfonb_activation_cfonb_code_idx", columns={"cfonb_code_id"})})
 * @ORM\Entity
 */
class CfonbActivation
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\CfonbCode
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CfonbCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cfonb_code_id", referencedColumnName="id")
     * })
     */
    private $cfonbCode;



    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CfonbActivation
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
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
     * Set cfonbCode
     *
     * @param \AppBundle\Entity\CfonbCode $cfonbCode
     *
     * @return CfonbActivation
     */
    public function setCfonbCode(\AppBundle\Entity\CfonbCode $cfonbCode = null)
    {
        $this->cfonbCode = $cfonbCode;

        return $this;
    }

    /**
     * Get cfonbCode
     *
     * @return \AppBundle\Entity\CfonbCode
     */
    public function getCfonbCode()
    {
        return $this->cfonbCode;
    }
}
