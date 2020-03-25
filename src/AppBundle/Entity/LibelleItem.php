<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibelleItem
 *
 * @ORM\Table(name="libelle_item", uniqueConstraints={@ORM\UniqueConstraint(name="intitule_UNIQUE", columns={"intitule"})})
 * @ORM\Entity
 */
class LibelleItem
{
    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=45, nullable=false)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="champ", type="string", length=45, nullable=true)
     */
    private $champ;

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
     * Set intitule
     *
     * @param string $intitule
     *
     * @return LibelleItem
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
     * @param $nbCaractere
     * @return $this
     */
    public function setNbCaractere($nbCaractere){
        $this->nbCaractere = $nbCaractere;

        return $this;
    }


    public function getNbCaractere(){
        return $this->nbCaractere;
    }

    /**
     * @param $champ
     * @return $this
     */
    public function setChamp($champ){
        $this->champ = $champ;

        return $this;
    }

    /**
     * @return string
     */
    public function getChamp(){
        return $this->champ;
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
