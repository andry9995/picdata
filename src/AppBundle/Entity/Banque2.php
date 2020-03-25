<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banque2
 *
 * @ORM\Table(name="banque2", uniqueConstraints={@ORM\UniqueConstraint(name="unique_codebanque", columns={"codebanque"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Banque2Repository")
 */
class Banque2
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=150, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="codebanque", type="string", length=10, nullable=false)
     */
    private $codebanque;

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
     * @return Banque2
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
     * Set codebanque
     *
     * @param string $codebanque
     *
     * @return Banque2
     */
    public function setCodebanque($codebanque)
    {
        $this->codebanque = $codebanque;

        return $this;
    }

    /**
     * Get codebanque
     *
     * @return string
     */
    public function getCodebanque()
    {
        return $this->codebanque;
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
