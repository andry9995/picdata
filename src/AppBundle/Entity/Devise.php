<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Devise
 *
 * @ORM\Table(name="devise", uniqueConstraints={@ORM\UniqueConstraint(name="nom_UNIQUE", columns={"nom"})})
 * @ORM\Entity
 */
class Devise
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=15, nullable=false)
     */
    private $nom = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=true)
     */
    private $description = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Devise
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Devise
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
