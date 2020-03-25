<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 29/11/2016
 * Time: 15:48
 */

namespace IndicateurBundle\Controller;


class StyleCell
{
    public
        $fontFamily,
        $fontBold,
        $fontItalic,
        $color,
        $bgColor,
        $textAlign,
        $indent,
        $border;

    /**
     * StyleCell constructor.
     * @param string $fontFamily
     * @param string $fontBold
     * @param string $fontItalic
     * @param null $color
     * @param null $bgColor
     * @param string $textAlign
     * @param int $indent
     * @param int $border
     */
    function __construct($fontFamily = '',$fontBold = 'normal',$fontItalic = 'normal',$color = null,$bgColor = null,$textAlign = 'left',$indent = 0,$border = 0)
    {
        $this->fontFamily = $fontFamily;
        $this->fontBold = $fontBold;
        $this->fontItalic = $fontItalic;
        $this->color = $color;
        $this->bgColor = $bgColor;
        $this->textAlign = $textAlign;
        $this->indent = $indent;

        //border
        $borderBin = decbin($border);
        $nbComplement = 8 - strlen($borderBin);
        for($i = 0;$i < $nbComplement; $i++) $borderBin = '0'.$borderBin;
        $borderSpliter = str_split($borderBin);
        $borderClass = '';
        if($borderSpliter[0] == 1) $borderClass .= ' cell_border_4';
        if($borderSpliter[1] == 1) $borderClass .= ' cell_border_5';
        if($borderSpliter[2] == 1) $borderClass .= ' cell_border_6';
        if($borderSpliter[3] == 1) $borderClass .= ' cell_border_7';
        if($borderSpliter[4] == 1) $borderClass .= ' cell_border_0';
        if($borderSpliter[5] == 1) $borderClass .= ' cell_border_1';
        if($borderSpliter[6] == 1) $borderClass .= ' cell_border_2';
        if($borderSpliter[7] == 1) $borderClass .= ' cell_border_3';

        $this->border = $borderClass;
    }
}