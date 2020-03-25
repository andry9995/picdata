<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProfessionLiberale
 *
 * @ORM\Table(name="profession_liberale", indexes={@ORM\Index(name="fk_profession_liberale_profession_liberale_cat1_idx", columns={"profession_liberale_cat_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfessionLiberaleRepository")
 */
class ProfessionLiberale
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="alpha", type="string", length=1, nullable=false)
     */
    private $alpha;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ProfessionLiberaleCat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProfessionLiberaleCat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profession_liberale_cat_id", referencedColumnName="id")
     * })
     */
    private $professionLiberaleCat;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ProfessionLiberale
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
     * Set alpha
     *
     * @param string $alpha
     *
     * @return ProfessionLiberale
     */
    public function setAlpha($alpha)
    {
        $this->alpha = $alpha;

        return $this;
    }

    /**
     * Get alpha
     *
     * @return string
     */
    public function getAlpha()
    {
        return $this->alpha;
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
     * Set professionLiberaleCat
     *
     * @param \AppBundle\Entity\ProfessionLiberaleCat $professionLiberaleCat
     *
     * @return ProfessionLiberale
     */
    public function setProfessionLiberaleCat(\AppBundle\Entity\ProfessionLiberaleCat $professionLiberaleCat = null)
    {
        $this->professionLiberaleCat = $professionLiberaleCat;

        return $this;
    }

    /**
     * Get professionLiberaleCat
     *
     * @return \AppBundle\Entity\ProfessionLiberaleCat
     */
    public function getProfessionLiberaleCat()
    {
        return $this->professionLiberaleCat;
    }
}
