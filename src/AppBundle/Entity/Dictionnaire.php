<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dictionnaire
 *
 * @ORM\Table(name="dictionnaire")
 * @ORM\Entity
 */
class Dictionnaire
{
    /**
     * @var string
     *
     * @ORM\Column(name="francais", type="text", length=65535, nullable=false)
     */
    private $francais;

    /**
     * @var string
     *
     * @ORM\Column(name="anglais", type="text", length=65535, nullable=true)
     */
    private $anglais;

    /**
     * @var string
     *
     * @ORM\Column(name="espagnol", type="text", length=65535, nullable=true)
     */
    private $espagnol;

    /**
     * @var string
     *
     * @ORM\Column(name="italien", type="text", length=65535, nullable=true)
     */
    private $italien;

    /**
     * @var string
     *
     * @ORM\Column(name="malagasy", type="text", length=65535, nullable=true)
     */
    private $malagasy;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set francais
     *
     * @param string $francais
     *
     * @return Dictionnaire
     */
    public function setFrancais($francais)
    {
        $this->francais = $francais;

        return $this;
    }

    /**
     * Get francais
     *
     * @return string
     */
    public function getFrancais()
    {
        return $this->francais;
    }

    /**
     * Set anglais
     *
     * @param string $anglais
     *
     * @return Dictionnaire
     */
    public function setAnglais($anglais)
    {
        $this->anglais = $anglais;

        return $this;
    }

    /**
     * Get anglais
     *
     * @return string
     */
    public function getAnglais()
    {
        return $this->anglais;
    }

    /**
     * Set espagnol
     *
     * @param string $espagnol
     *
     * @return Dictionnaire
     */
    public function setEspagnol($espagnol)
    {
        $this->espagnol = $espagnol;

        return $this;
    }

    /**
     * Get espagnol
     *
     * @return string
     */
    public function getEspagnol()
    {
        return $this->espagnol;
    }

    /**
     * Set italien
     *
     * @param string $italien
     *
     * @return Dictionnaire
     */
    public function setItalien($italien)
    {
        $this->italien = $italien;

        return $this;
    }

    /**
     * Get italien
     *
     * @return string
     */
    public function getItalien()
    {
        return $this->italien;
    }

    /**
     * Set malagasy
     *
     * @param string $malagasy
     *
     * @return Dictionnaire
     */
    public function setMalagasy($malagasy)
    {
        $this->malagasy = $malagasy;

        return $this;
    }

    /**
     * Get malagasy
     *
     * @return string
     */
    public function getMalagasy()
    {
        return $this->malagasy;
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
