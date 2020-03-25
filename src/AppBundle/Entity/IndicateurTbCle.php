<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTbCle
 *
 * @ORM\Table(name="indicateur_tb_cle", uniqueConstraints={@ORM\UniqueConstraint(name="cle_UNIQUE", columns={"cle"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbCleRepository")
 */
class IndicateurTbCle
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
     * @ORM\Column(name="sens", type="integer", nullable=false)
     */
    private $sens = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set cle
     *
     * @param string $cle
     *
     * @return IndicateurTbCle
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
     * Set sens
     *
     * @param integer $sens
     *
     * @return IndicateurTbCle
     */
    public function setSens($sens)
    {
        $this->sens = $sens;

        return $this;
    }

    /**
     * Get sens
     *
     * @return integer
     */
    public function getSens()
    {
        return $this->sens;
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
}
