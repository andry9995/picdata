<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatControlCell
 *
 * @ORM\Table(name="etat_control_cell", indexes={@ORM\Index(name="fk_etat_control_cell_indicateur_cell1_idx", columns={"indicateur_cell_id"}), @ORM\Index(name="fk_etat_control_cell_rubrique1_idx", columns={"rubrique_id"}), @ORM\Index(name="fk_etat_control_cell_etat_control_item1_idx", columns={"etat_control_item_id"})})
 * @ORM\Entity
 */
class EtatControlCell
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
     * @var \AppBundle\Entity\EtatControlItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatControlItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_control_item_id", referencedColumnName="id")
     * })
     */
    private $etatControlItem;

    /**
     * @var \AppBundle\Entity\Rubrique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rubrique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rubrique_id", referencedColumnName="id")
     * })
     */
    private $rubrique;

    /**
     * @var \AppBundle\Entity\IndicateurCell
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurCell")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_cell_id", referencedColumnName="id")
     * })
     */
    private $indicateurCell;



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
     * Set etatControlItem
     *
     * @param \AppBundle\Entity\EtatControlItem $etatControlItem
     *
     * @return EtatControlCell
     */
    public function setEtatControlItem(\AppBundle\Entity\EtatControlItem $etatControlItem = null)
    {
        $this->etatControlItem = $etatControlItem;

        return $this;
    }

    /**
     * Get etatControlItem
     *
     * @return \AppBundle\Entity\EtatControlItem
     */
    public function getEtatControlItem()
    {
        return $this->etatControlItem;
    }

    /**
     * Set rubrique
     *
     * @param \AppBundle\Entity\Rubrique $rubrique
     *
     * @return EtatControlCell
     */
    public function setRubrique(\AppBundle\Entity\Rubrique $rubrique = null)
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    /**
     * Get rubrique
     *
     * @return \AppBundle\Entity\Rubrique
     */
    public function getRubrique()
    {
        return $this->rubrique;
    }

    /**
     * Set indicateurCell
     *
     * @param \AppBundle\Entity\IndicateurCell $indicateurCell
     *
     * @return EtatControlCell
     */
    public function setIndicateurCell(\AppBundle\Entity\IndicateurCell $indicateurCell = null)
    {
        $this->indicateurCell = $indicateurCell;

        return $this;
    }

    /**
     * Get indicateurCell
     *
     * @return \AppBundle\Entity\IndicateurCell
     */
    public function getIndicateurCell()
    {
        return $this->indicateurCell;
    }
}
