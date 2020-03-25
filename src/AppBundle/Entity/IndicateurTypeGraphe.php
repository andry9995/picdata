<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTypeGraphe
 *
 * @ORM\Table(name="indicateur_type_graphe", indexes={@ORM\Index(name="fk_indicateur_type_graphe_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_indicateur_type_graphe_type_graphe1_idx", columns={"type_graphe_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTypeGrapheRepository")
 */
class IndicateurTypeGraphe
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
     * @var \AppBundle\Entity\TypeGraphe
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeGraphe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_graphe_id", referencedColumnName="id")
     * })
     */
    private $typeGraphe;



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
     * @return IndicateurTypeGraphe
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

    /**
     * Set typeGraphe
     *
     * @param \AppBundle\Entity\TypeGraphe $typeGraphe
     *
     * @return IndicateurTypeGraphe
     */
    public function setTypeGraphe(\AppBundle\Entity\TypeGraphe $typeGraphe = null)
    {
        $this->typeGraphe = $typeGraphe;

        return $this;
    }

    /**
     * Get typeGraphe
     *
     * @return \AppBundle\Entity\TypeGraphe
     */
    public function getTypeGraphe()
    {
        return $this->typeGraphe;
    }
}
