<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactRemiseNiveau
 *
 * @ORM\Table(name="fact_remise_niveau", uniqueConstraints={@ORM\UniqueConstraint(name="niveau_UNIQUE", columns={"niveau"}), @ORM\UniqueConstraint(name="libelle_UNIQUE", columns={"libelle"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactRemiseNiveauRepository")
 */
class FactRemiseNiveau
{
    /**
     * @var integer
     *
     * @ORM\Column(name="niveau", type="integer", nullable=false)
     */
    private $niveau;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set niveau
     *
     * @param integer $niveau
     *
     * @return FactRemiseNiveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return integer
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return FactRemiseNiveau
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
