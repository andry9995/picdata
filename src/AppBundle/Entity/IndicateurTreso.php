<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTreso
 *
 * @ORM\Table(name="indicateur_treso", uniqueConstraints={@ORM\UniqueConstraint(name="indicateur_id_UNIQUE", columns={"indicateur_id"})})
 * @ORM\Entity
 */
class IndicateurTreso
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
     * @var \AppBundle\Entity\Indicateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Indicateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_id", referencedColumnName="id")
     * })
     */
    private $indicateur;



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
     * Set indicateur
     *
     * @param \AppBundle\Entity\Indicateur $indicateur
     *
     * @return IndicateurTreso
     */
    public function setIndicateur(\AppBundle\Entity\Indicateur $indicateur = null)
    {
        $this->indicateur = $indicateur;

        return $this;
    }

    /**
     * Get indicateur
     *
     * @return \AppBundle\Entity\Indicateur
     */
    public function getIndicateur()
    {
        return $this->indicateur;
    }
}
