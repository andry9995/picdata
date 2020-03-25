<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurCell
 *
 * @ORM\Table(name="indicateur_cell", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_indicateur_cell_row_col", columns={"row", "col", "indicateur_id"})}, indexes={@ORM\Index(name="fk_indicateur_cell_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_indicateur_cell_etat_regime_fiscal1_idx", columns={"etat_regime_fiscal_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurCellRepository")
 */
class IndicateurCell
{
    /*****************************************
     *             MODIF MANUEL
     ****************************************/
    /**
     * @var array
     */
    private $operandes = array();

    /**
     * @return array
     */
    public function getOperandes()
    {
        return $this->operandes;
    }

    /**
     * @param array $operandes
     * @return $this
     */
    public function setOperandes($operandes = array())
    {
        $this->operandes = $operandes;
        return $this;
    }

    /**
     * @var string
     */
    private $borderBinary = '00000000';

    /**
     * @return string
     */
    public function getBorderBinary()
    {
        return $this->borderBinary;
    }

    /**
     * @return $this
     */
    public function setBorderBinary()
    {
        $border_binary = decbin($this->border);
        while (strlen($border_binary) < 8) $border_binary = '0'.$border_binary;
        $this->borderBinary = $border_binary;
        return $this;
    }

    /**
     * @var null
     */
    private $stylesObject = null;

    /**
     * @return $this
     */
    public function setStylesObject()
    {
        $jsonDecoded = json_decode($this->styles);
        if(json_last_error() == JSON_ERROR_NONE) $this->stylesObject = $jsonDecoded;
        else $this->stylesObject = null;
        return $this;
    }

    /**
     * @return null
     */
    public function getStylesObject()
    {
        return $this->stylesObject;
    }































    /**
     * @var integer
     *
     * @ORM\Column(name="is_formule", type="integer", nullable=false)
     */
    private $isFormule = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=150, nullable=true)
     */
    private $formule;

    /**
     * @var integer
     *
     * @ORM\Column(name="row", type="integer", nullable=false)
     */
    private $row = '100';

    /**
     * @var integer
     *
     * @ORM\Column(name="col", type="integer", nullable=false)
     */
    private $col = '100';

    /**
     * @var string
     *
     * @ORM\Column(name="font_family", type="string", length=45, nullable=true)
     */
    private $fontFamily;

    /**
     * @var string
     *
     * @ORM\Column(name="font_bold", type="string", length=45, nullable=false)
     */
    private $fontBold = 'normal';

    /**
     * @var string
     *
     * @ORM\Column(name="font_italic", type="string", length=45, nullable=false)
     */
    private $fontItalic = 'normal';

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=45, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="bg_color", type="string", length=45, nullable=true)
     */
    private $bgColor;

    /**
     * @var string
     *
     * @ORM\Column(name="text_align", type="string", length=45, nullable=false)
     */
    private $textAlign = 'left';

    /**
     * @var integer
     *
     * @ORM\Column(name="indent", type="integer", nullable=false)
     */
    private $indent = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="border", type="integer", nullable=false)
     */
    private $border = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="styles", type="string", length=500, nullable=false)
     */
    private $styles;

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
     * @var \AppBundle\Entity\EtatRegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatRegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $etatRegimeFiscal;



    /**
     * Set isFormule
     *
     * @param integer $isFormule
     *
     * @return IndicateurCell
     */
    public function setIsFormule($isFormule)
    {
        $this->isFormule = $isFormule;

        return $this;
    }

    /**
     * Get isFormule
     *
     * @return integer
     */
    public function getIsFormule()
    {
        return $this->isFormule;
    }

    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return IndicateurCell
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set row
     *
     * @param integer $row
     *
     * @return IndicateurCell
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row
     *
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set col
     *
     * @param integer $col
     *
     * @return IndicateurCell
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
     * Set fontFamily
     *
     * @param string $fontFamily
     *
     * @return IndicateurCell
     */
    public function setFontFamily($fontFamily)
    {
        $this->fontFamily = $fontFamily;

        return $this;
    }

    /**
     * Get fontFamily
     *
     * @return string
     */
    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    /**
     * Set fontBold
     *
     * @param string $fontBold
     *
     * @return IndicateurCell
     */
    public function setFontBold($fontBold)
    {
        $this->fontBold = $fontBold;

        return $this;
    }

    /**
     * Get fontBold
     *
     * @return string
     */
    public function getFontBold()
    {
        return $this->fontBold;
    }

    /**
     * Set fontItalic
     *
     * @param string $fontItalic
     *
     * @return IndicateurCell
     */
    public function setFontItalic($fontItalic)
    {
        $this->fontItalic = $fontItalic;

        return $this;
    }

    /**
     * Get fontItalic
     *
     * @return string
     */
    public function getFontItalic()
    {
        return $this->fontItalic;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return IndicateurCell
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set bgColor
     *
     * @param string $bgColor
     *
     * @return IndicateurCell
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    /**
     * Get bgColor
     *
     * @return string
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * Set textAlign
     *
     * @param string $textAlign
     *
     * @return IndicateurCell
     */
    public function setTextAlign($textAlign)
    {
        $this->textAlign = $textAlign;

        return $this;
    }

    /**
     * Get textAlign
     *
     * @return string
     */
    public function getTextAlign()
    {
        return $this->textAlign;
    }

    /**
     * Set indent
     *
     * @param integer $indent
     *
     * @return IndicateurCell
     */
    public function setIndent($indent)
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * Get indent
     *
     * @return integer
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Set border
     *
     * @param integer $border
     *
     * @return IndicateurCell
     */
    public function setBorder($border)
    {
        $this->border = $border;

        return $this;
    }

    /**
     * Get border
     *
     * @return integer
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Set styles
     *
     * @param string $styles
     *
     * @return IndicateurCell
     */
    public function setStyles($styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * Get styles
     *
     * @return string
     */
    public function getStyles()
    {
        return $this->styles;
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
     * Set indicateur
     *
     * @param \AppBundle\Entity\Indicateur $indicateur
     *
     * @return IndicateurCell
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
     * Set etatRegimeFiscal
     *
     * @param \AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal
     *
     * @return IndicateurCell
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
}
