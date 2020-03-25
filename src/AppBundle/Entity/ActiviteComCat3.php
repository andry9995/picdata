<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteComCat3
 *
 * @ORM\Table(name="activite_com_cat_3", indexes={@ORM\Index(name="fk_activite_com_cat_3_activite_com_cat_2_idx", columns={"activite_com_cat_2_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActiviteComCat3Repository")
 */
class ActiviteComCat3
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
     * @var \AppBundle\Entity\ActiviteComCat2
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ActiviteComCat2")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activite_com_cat_2_id", referencedColumnName="id")
     * })
     */
    private $activiteComCat2;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ActiviteComCat3
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
     * @return ActiviteComCat3
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
     * Set activiteComCat2
     *
     * @param \AppBundle\Entity\ActiviteComCat2 $activiteComCat2
     *
     * @return ActiviteComCat3
     */
    public function setActiviteComCat2(\AppBundle\Entity\ActiviteComCat2 $activiteComCat2 = null)
    {
        $this->activiteComCat2 = $activiteComCat2;

        return $this;
    }

    /**
     * Get activiteComCat2
     *
     * @return \AppBundle\Entity\ActiviteComCat2
     */
    public function getActiviteComCat2()
    {
        return $this->activiteComCat2;
    }
}
