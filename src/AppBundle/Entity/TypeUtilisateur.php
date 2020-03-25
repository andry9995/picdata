<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeUtilisateur
 *
 * @ORM\Table(name="type_utilisateur", uniqueConstraints={@ORM\UniqueConstraint(name="type_UNIQUE", columns={"type"})})
 * @ORM\Entity
 */
class TypeUtilisateur
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=150, nullable=false)
     */
    private $type = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set type
     *
     * @param string $type
     *
     * @return TypeUtilisateur
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
