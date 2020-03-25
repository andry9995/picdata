<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MentionManuscrite
 *
 * @ORM\Table(name="mention_manuscrite", uniqueConstraints={@ORM\UniqueConstraint(name="libelle_UNIQUE", columns={"libelle"})})
 * @ORM\Entity
 */
class MentionManuscrite
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="personnel", type="integer", nullable=false)
     */
    private $personnel = '0';

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
     * @return MentionManuscrite
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
     * Set personnel
     *
     * @param integer $personnel
     *
     * @return MentionManuscrite
     */
    public function setPersonnel($personnel)
    {
        $this->personnel = $personnel;

        return $this;
    }

    /**
     * Get personnel
     *
     * @return integer
     */
    public function getPersonnel()
    {
        return $this->personnel;
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
