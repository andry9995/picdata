<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Controller\Boost;

/**
 * Pcg
 *
 * @ORM\Table(name="pcg", uniqueConstraints={@ORM\UniqueConstraint(name="compte_UNIQUE", columns={"compte"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PcgRepository")
 */
class Pcg
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
     * @ORM\Column(name="intitule", type="string", length=150, nullable=false)
     */
    private $intitule;

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
     * @return Pcg
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
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Pcg
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /********************************
     *          MODIF MANUEL
     ********************************/
    /**
     * @var null
     */
    private $etats = null;
    public function setEtats($etats)
    {
        $this->etats = $etats;
        return $this;
    }
    /**
     * Get etats
     *
     * @return array
     */
    public function getEtats()
    {
        return $this->etats;
    }


    /**
     * @var int
     */
    private $cochage = 0;

    /**
     * @param $cochage
     * @return $this
     */
    public function setCochage($cochage)
    {
        $this->cochage = $cochage;
        return $this;
    }

    /**
     * @return int
     */
    public function getCochage()
    {
        return $this->cochage;
    }


    /**
     * @var int
     */
    private $idEtatCompte = 0;

    /**
     * @param $idEtatCompte
     * @return $this
     */
    public function setIdEtatCompte($idEtatCompte)
    {
        $this->idEtatCompte = $idEtatCompte;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdEtatCompte()
    {
        return $this->idEtatCompte;
    }
}
