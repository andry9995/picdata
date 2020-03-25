<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleBanque
 *
 * @ORM\Table(name="cle_banque", uniqueConstraints={@ORM\UniqueConstraint(name="unik_cle_banque", columns={"cle_id", "banque_id"})}, indexes={@ORM\Index(name="fk_cle_banque_cle_idx", columns={"cle_id"}), @ORM\Index(name="fk_cle_banque_banque_idx", columns={"banque_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleBanqueRepository")
 */
class CleBanque
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
     * @var \AppBundle\Entity\Cle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_id", referencedColumnName="id")
     * })
     */
    private $cle;

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
     * Set cle
     *
     * @param \AppBundle\Entity\Cle $cle
     *
     * @return CleBanque
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
     * Set banque
     *
     * @param \AppBundle\Entity\Banque $banque
     *
     * @return CleBanque
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
