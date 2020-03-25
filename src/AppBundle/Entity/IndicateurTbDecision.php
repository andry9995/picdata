<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurTbDecision
 *
 * @ORM\Table(name="indicateur_tb_decision", indexes={@ORM\Index(name="fk_indicateur_tb_decision_indicateur_tb1_idx", columns={"indicateur_tb_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurTbDecisionRepository")
 */
class IndicateurTbDecision
{
    /**
     * @var string
     *
     * @ORM\Column(name="condition_tb", type="string", length=100, nullable=false)
     */
    private $conditionTb = '';

    /**
     * @var float
     *
     * @ORM\Column(name="point", type="float", precision=10, scale=0, nullable=false)
     */
    private $point = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=50, nullable=false)
     */
    private $icon = 'twa twa-1f644';

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=false)
     */
    private $commentaire = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\IndicateurTb
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurTb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_tb_id", referencedColumnName="id")
     * })
     */
    private $indicateurTb;



    /**
     * Set conditionTb
     *
     * @param string $conditionTb
     *
     * @return IndicateurTbDecision
     */
    public function setConditionTb($conditionTb)
    {
        $this->conditionTb = $conditionTb;

        return $this;
    }

    /**
     * Get conditionTb
     *
     * @return string
     */
    public function getConditionTb()
    {
        return $this->conditionTb;
    }

    /**
     * Set point
     *
     * @param float $point
     *
     * @return IndicateurTbDecision
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return float
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return IndicateurTbDecision
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return IndicateurTbDecision
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
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
     * Set indicateurTb
     *
     * @param \AppBundle\Entity\IndicateurTb $indicateurTb
     *
     * @return IndicateurTbDecision
     */
    public function setIndicateurTb(\AppBundle\Entity\IndicateurTb $indicateurTb = null)
    {
        $this->indicateurTb = $indicateurTb;

        return $this;
    }

    /**
     * Get indicateurTb
     *
     * @return \AppBundle\Entity\IndicateurTb
     */
    public function getIndicateurTb()
    {
        return $this->indicateurTb;
    }
}
