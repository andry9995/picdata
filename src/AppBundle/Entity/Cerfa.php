<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cerfa
 *
 * @ORM\Table(name="cerfa", uniqueConstraints={@ORM\UniqueConstraint(name="numero_UNIQUE", columns={"numero"})})
 * @ORM\Entity
 */
class Cerfa
{
    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=100, nullable=true)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=45, nullable=true)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Cerfa
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Cerfa
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
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
