<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTbInfoPerdos
 *
 * @ORM\Table(name="indicateur_tb_info_perdos", indexes={@ORM\Index(name="fk_indicateur_tb_info_perdos_indicateur_info_perdos1_idx", columns={"indicateur_info_perdos_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbInfoPerdosRepository")
 */
class IndicateurTbInfoPerdos
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
     * @var \AppBundle\Entity\IndicateurInfoPerdos
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurInfoPerdos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_info_perdos_id", referencedColumnName="id")
     * })
     */
    private $indicateurInfoPerdos;



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
     * Set indicateurInfoPerdos
     *
     * @param \AppBundle\Entity\IndicateurInfoPerdos $indicateurInfoPerdos
     *
     * @return IndicateurTbInfoPerdos
     */
    public function setIndicateurInfoPerdos(\AppBundle\Entity\IndicateurInfoPerdos $indicateurInfoPerdos = null)
    {
        $this->indicateurInfoPerdos = $indicateurInfoPerdos;

        return $this;
    }

    /**
     * Get indicateurInfoPerdos
     *
     * @return \AppBundle\Entity\IndicateurInfoPerdos
     */
    public function getIndicateurInfoPerdos()
    {
        return $this->indicateurInfoPerdos;
    }
}
