<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModeReglement
 *
 * @ORM\Table(name="mode_reglement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModeReglementRepository")
 */
class ModeReglement
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=true)
     */
    private $libelle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="caisse", type="integer", nullable=true)
     */
    private $caisse = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=true)
     */
    private $rang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ModeReglement
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ModeReglement
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set caisse
     *
     * @param integer $caisse
     *
     * @return ModeReglement
     */
    public function setCaisse($caisse)
    {
        $this->caisse = $caisse;

        return $this;
    }

    /**
     * Get caisse
     *
     * @return integer
     */
    public function getCaisse()
    {
        return $this->caisse;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return ModeReglement
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
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
