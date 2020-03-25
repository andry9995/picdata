<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrespSirenFormeJuridique
 *
 * @ORM\Table(name="corresp_siren_forme_juridique")
 * @ORM\Entity
 */
class CorrespSirenFormeJuridique
{
    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=150, nullable=false)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="nj", type="string", length=4, nullable=false)
     */
    private $nj;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return CorrespSirenFormeJuridique
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set nj
     *
     * @param string $nj
     *
     * @return CorrespSirenFormeJuridique
     */
    public function setNj($nj)
    {
        $this->nj = $nj;

        return $this;
    }

    /**
     * Get nj
     *
     * @return string
     */
    public function getNj()
    {
        return $this->nj;
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
