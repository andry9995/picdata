<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurFormatCol
 *
 * @ORM\Table(name="indicateur_format_col", uniqueConstraints={@ORM\UniqueConstraint(name="fk_uniq_indicateur_col", columns={"col", "indicateur_id"})}, indexes={@ORM\Index(name="fk_indicateur_format_col_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_indicateur_format_col_etat_regime_fiscal1_idx", columns={"etat_regime_fiscal_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurFormatColRepository")
 */
class IndicateurFormatCol
{
    /**
     * @var integer
     *
     * @ORM\Column(name="format", type="integer", nullable=false)
     */
    private $format = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="avec_decimal", type="integer", nullable=false)
     */
    private $avecDecimal = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="col", type="integer", nullable=false)
     */
    private $col;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set format
     *
     * @param integer $format
     *
     * @return IndicateurFormatCol
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return integer
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set avecDecimal
     *
     * @param integer $avecDecimal
     *
     * @return IndicateurFormatCol
     */
    public function setAvecDecimal($avecDecimal)
    {
        $this->avecDecimal = $avecDecimal;

        return $this;
    }

    /**
     * Get avecDecimal
     *
     * @return integer
     */
    public function getAvecDecimal()
    {
        return $this->avecDecimal;
    }

    /**
     * Set col
     *
     * @param integer $col
     *
     * @return IndicateurFormatCol
     */
    public function setCol($col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * Get col
     *
     * @return integer
     */
    public function getCol()
    {
        return $this->col;
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
     * Set etatRegimeFiscal
     *
     * @param \AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal
     *
     * @return IndicateurFormatCol
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
     * @return IndicateurFormatCol
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
