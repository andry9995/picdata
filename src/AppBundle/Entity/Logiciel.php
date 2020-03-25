<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logiciel
 *
 * @ORM\Table(name="logiciel", uniqueConstraints={@ORM\UniqueConstraint(name="libelle_UNIQUE", columns={"libelle"})})
 * @ORM\Entity
 */
class Logiciel
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", nullable=true)
     */
    private $rang;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_caractere", type="integer", nullable=true)
     */
    private $nbCaractere;

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
     * @return Logiciel
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
     * Set rang
     *
     * @param integer $rang
     *
     * @return Logiciel
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
     * @param $nbCaractere
     * @return $this
     */
    public function setNbCaractere($nbCaractere)
    {
        $this->nbCaractere = $nbCaractere;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbCaractere()
    {
        return $this->nbCaractere;
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
