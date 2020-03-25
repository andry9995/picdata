<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FonctionClient
 *
 * @ORM\Table(name="fonction_client")
 * @ORM\Entity
 */
class FonctionClient
{
    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=250, nullable=false)
     */
    private $fonction;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set fonction
     *
     * @param string $fonction
     *
     * @return FonctionClient
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return string
     */
    public function getFonction()
    {
        return $this->fonction;
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
