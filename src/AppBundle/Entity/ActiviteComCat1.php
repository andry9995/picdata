<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteComCat1
 *
 * @ORM\Table(name="activite_com_cat_1", indexes={@ORM\Index(name="fk_activite_com_cat_1_activite_com_cat1_idx", columns={"activite_com_cat_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActiviteComCat1Repository")
 */
class ActiviteComCat1
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=false)
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
     * @var \AppBundle\Entity\ActiviteComCat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ActiviteComCat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_com_cat_id", referencedColumnName="id")
     * })
     */
    private $activiteComCat;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ActiviteComCat1
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
     * @return ActiviteComCat1
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
     * Set activiteComCat
     *
     * @param \AppBundle\Entity\ActiviteComCat $activiteComCat
     *
     * @return ActiviteComCat1
     */
    public function setActiviteComCat(\AppBundle\Entity\ActiviteComCat $activiteComCat = null)
    {
        $this->activiteComCat = $activiteComCat;

        return $this;
    }

    /**
     * Get activiteComCat
     *
     * @return \AppBundle\Entity\ActiviteComCat
     */
    public function getActiviteComCat()
    {
        return $this->activiteComCat;
    }
}
