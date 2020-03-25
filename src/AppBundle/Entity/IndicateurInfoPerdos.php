<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurInfoPerdos
 *
 * @ORM\Table(name="indicateur_info_perdos")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurInfoPerdosRepository")
 */
class IndicateurInfoPerdos
{
    /**
     * @var string
     *
     * @ORM\Column(name="champ", type="string", length=45, nullable=false)
     */
    private $champ;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="string", length=45, nullable=false)
     */
    private $header = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="tables_child", type="string", length=300, nullable=true)
     */
    private $tablesChild;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set champ
     *
     * @param string $champ
     *
     * @return IndicateurInfoPerdos
     */
    public function setChamp($champ)
    {
        $this->champ = $champ;

        return $this;
    }

    /**
     * Get champ
     *
     * @return string
     */
    public function getChamp()
    {
        return $this->champ;
    }

    /**
     * Set header
     *
     * @param string $header
     *
     * @return IndicateurInfoPerdos
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return IndicateurInfoPerdos
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set tablesChild
     *
     * @param string $tablesChild
     *
     * @return IndicateurInfoPerdos
     */
    public function setTablesChild($tablesChild)
    {
        $this->tablesChild = $tablesChild;

        return $this;
    }

    /**
     * Get tablesChild
     *
     * @return string
     */
    public function getTablesChild()
    {
        return $this->tablesChild;
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
