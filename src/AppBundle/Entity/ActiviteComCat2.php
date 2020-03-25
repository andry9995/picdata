<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteComCat2
 *
 * @ORM\Table(name="activite_com_cat_2", indexes={@ORM\Index(name="fk_activite_com_cat_2_activite_com_cat_11_idx", columns={"activite_com_cat_1_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActiviteComCat2Repository")
 */
class ActiviteComCat2
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
     * @ORM\Column(name="code", type="string", length=15, nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ActiviteComCat1
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ActiviteComCat1")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_com_cat_1_id", referencedColumnName="id")
     * })
     */
    private $activiteComCat1;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ActiviteComCat2
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
     * Set code
     *
     * @param string $code
     *
     * @return ActiviteComCat2
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * Set activiteComCat1
     *
     * @param \AppBundle\Entity\ActiviteComCat1 $activiteComCat1
     *
     * @return ActiviteComCat2
     */
    public function setActiviteComCat1(\AppBundle\Entity\ActiviteComCat1 $activiteComCat1 = null)
    {
        $this->activiteComCat1 = $activiteComCat1;

        return $this;
    }

    /**
     * Get activiteComCat1
     *
     * @return \AppBundle\Entity\ActiviteComCat1
     */
    public function getActiviteComCat1()
    {
        return $this->activiteComCat1;
    }
}
