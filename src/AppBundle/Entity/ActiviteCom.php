<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteCom
 *
 * @ORM\Table(name="activite_com", indexes={@ORM\Index(name="fk_activite_com_activite_com_cat_21_idx", columns={"activite_com_cat_3_id"})})
 * @ORM\Entity
 */
class ActiviteCom
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
     * @ORM\Column(name="code_ape", type="string", length=15, nullable=false)
     */
    private $codeApe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ActiviteComCat3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ActiviteComCat3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_com_cat_3_id", referencedColumnName="id")
     * })
     */
    private $activiteComCat3;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ActiviteCom
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
     * Set codeApe
     *
     * @param string $codeApe
     *
     * @return ActiviteCom
     */
    public function setCodeApe($codeApe)
    {
        $this->codeApe = $codeApe;

        return $this;
    }

    /**
     * Get codeApe
     *
     * @return string
     */
    public function getCodeApe()
    {
        return $this->codeApe;
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
     * Set activiteComCat3
     *
     * @param \AppBundle\Entity\ActiviteComCat3 $activiteComCat3
     *
     * @return ActiviteCom
     */
    public function setActiviteComCat3(\AppBundle\Entity\ActiviteComCat3 $activiteComCat3 = null)
    {
        $this->activiteComCat3 = $activiteComCat3;

        return $this;
    }

    /**
     * Get activiteComCat3
     *
     * @return \AppBundle\Entity\ActiviteComCat3
     */
    public function getActiviteComCat3()
    {
        return $this->activiteComCat3;
    }
}
