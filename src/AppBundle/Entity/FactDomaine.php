<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactDomaine
 *
 * @ORM\Table(name="fact_domaine", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactDomaineRepository")
 */
class FactDomaine
{
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

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
     * Set code
     *
     * @param integer $code
     *
     * @return FactDomaine
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return FactDomaine
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

   
    /**
     * @var array
     */
    private $prestationGens = array();

    /**
     * @param $prestationGens
     * @return $this
     */
    public function setPrestationGens($prestationGens)
    {
        $this->prestationGens = $prestationGens;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrestationGens()
    {
        return $this->prestationGens;
    }


}
