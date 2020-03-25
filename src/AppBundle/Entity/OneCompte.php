<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneCompte
 *
 * @ORM\Table(name="one_compte")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneCompteRepository")
 */
class OneCompte
{
    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=20, nullable=false)
     */
    private $compte;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=true)
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
     * Set compte
     *
     * @param string $compte
     *
     * @return OneCompte
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return OneCompte
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
