<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CfonbBanque
 *
 * @ORM\Table(name="cfonb_banque", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_banque_cfonb", columns={"banque_id", "cfonb_code_id"})}, indexes={@ORM\Index(name="fk_cfonb_banque_cfonb_code_idx", columns={"cfonb_code_id"}), @ORM\Index(name="IDX_6F52752037E080D9", columns={"banque_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CfonbBanqueRepository")
 */
class CfonbBanque
{
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
     * @var \AppBundle\Entity\Banque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Banque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_id", referencedColumnName="id")
     * })
     */
    private $banque;



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
     * @return CfonbBanque
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

    /**
     * Set banque
     *
     * @param \AppBundle\Entity\Banque $banque
     *
     * @return CfonbBanque
     */
    public function setBanque(\AppBundle\Entity\Banque $banque = null)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return \AppBundle\Entity\Banque
     */
    public function getBanque()
    {
        return $this->banque;
    }
}
