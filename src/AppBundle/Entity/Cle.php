<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cle
 *
 * @ORM\Table(name="cle", uniqueConstraints={@ORM\UniqueConstraint(name="cle_UNIQUE", columns={"cle"})}, indexes={@ORM\Index(name="fk_cle_banque_type_idx", columns={"banque_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleRepository")
 */
class Cle
{
    /**
     * @var string
     *
     * @ORM\Column(name="cle", type="string", length=45, nullable=false)
     */
    private $cle;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $tva;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="pas_piece", type="integer", nullable=false)
     */
    private $pasPiece = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_compta", type="integer", nullable=false)
     */
    private $typeCompta;

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
     * Set cle
     *
     * @param string $cle
     *
     * @return Cle
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
     * Set tva
     *
     * @param string $tva
     *
     * @return Cle
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Cle
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
     * Set pasPiece
     *
     * @param integer $pasPiece
     *
     * @return Cle
     */
    public function setPasPiece($pasPiece)
    {
        $this->pasPiece = $pasPiece;

        return $this;
    }

    /**
     * Get pasPiece
     *
     * @return integer
     */
    public function getPasPiece()
    {
        return $this->pasPiece;
    }

    /**
     * Set typeCompta
     *
     * @param integer $typeCompta
     *
     * @return Cle
     */
    public function setTypeCompta($typeCompta)
    {
        $this->typeCompta = $typeCompta;

        return $this;
    }

    /**
     * Get typeCompta
     *
     * @return integer
     */
    public function getTypeCompta()
    {
        return $this->typeCompta;
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
     * @return Cle
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
}
