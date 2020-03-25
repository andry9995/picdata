<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OnePays
 *
 * @ORM\Table(name="one_pays")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OnePaysRepository")
 */
class OnePays
{
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", length=3, nullable=false)
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alpha2", type="string", length=2, nullable=false)
     */
    private $alpha2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alpha3", type="string", length=3, nullable=false)
     */
    private $alpha3;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

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
     * @return OnePays
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param integer $code
     *
     * @return OnePays
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
     * Set alpha2
     *
     * @param string $alpha2
     *
     * @return OnePays
     */
    public function setAlpha2($alpha2)
    {
        $this->alpha2 = $alpha2;
    
        return $this;
    }

    /**
     * Get alpha2
     *
     * @return string
     */
    public function getAlpha2()
    {
        return $this->alpha2;
    }

    /**
     * Set alpha3
     *
     * @param string $alpha3
     *
     * @return OnePays
     */
    public function setAlpha3($alpha3)
    {
        $this->alpha3 = $alpha3;
    
        return $this;
    }

    /**
     * Get alpha3
     *
     * @return string
     */
    public function getAlpha3()
    {
        return $this->alpha3;
    }
}
