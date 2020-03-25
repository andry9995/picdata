<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatControlItem
 *
 * @ORM\Table(name="etat_control_item", indexes={@ORM\Index(name="fk_etat_control_item_etat_control1_idx", columns={"etat_control_id"}), @ORM\Index(name="fk_etat_control_item_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_etat_control_item_etat_regime_fiscal1_idx", columns={"etat_regime_fiscal_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EtatControlItemRepository")
 */
class EtatControlItem
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
     * @var \AppBundle\Entity\EtatControl
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatControl")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_control_id", referencedColumnName="id")
     * })
     */
    private $etatControl;

    /**
     * @var \AppBundle\Entity\EtatRegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatRegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $etatRegimeFiscal;

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
     * Set etatControl
     *
     * @param \AppBundle\Entity\EtatControl $etatControl
     *
     * @return EtatControlItem
     */
    public function setEtatControl(\AppBundle\Entity\EtatControl $etatControl = null)
    {
        $this->etatControl = $etatControl;

        return $this;
    }

    /**
     * Get etatControl
     *
     * @return \AppBundle\Entity\EtatControl
     */
    public function getEtatControl()
    {
        return $this->etatControl;
    }

    /**
     * Set etatRegimeFiscal
     *
     * @param \AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal
     *
     * @return EtatControlItem
     */
    public function setEtatRegimeFiscal(\AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal = null)
    {
        $this->etatRegimeFiscal = $etatRegimeFiscal;

        return $this;
    }

    /**
     * Get etatRegimeFiscal
     *
     * @return \AppBundle\Entity\EtatRegimeFiscal
     */
    public function getEtatRegimeFiscal()
    {
        return $this->etatRegimeFiscal;
    }

    /**
     * Set indicateur
     *
     * @param \AppBundle\Entity\Indicateur $indicateur
     *
     * @return EtatControlItem
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
