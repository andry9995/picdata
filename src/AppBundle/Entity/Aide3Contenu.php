<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aide3Contenu
 *
 * @ORM\Table(name="aide_3_contenu", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE", columns={"aide_3_id", "type_contenu"})}, indexes={@ORM\Index(name="fk_aide_3_contenu_aide_3_idx", columns={"aide_3_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Aide3ContenuRepository")
 */
class Aide3Contenu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_contenu", type="integer", nullable=true)
     */
    private $typeContenu;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Aide3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_3_id", referencedColumnName="id")
     * })
     */
    private $aide3;



    /**
     * Set typeContenu
     *
     * @param integer $typeContenu
     *
     * @return Aide3Contenu
     */
    public function setTypeContenu($typeContenu)
    {
        $this->typeContenu = $typeContenu;

        return $this;
    }

    /**
     * Get typeContenu
     *
     * @return integer
     */
    public function getTypeContenu()
    {
        return $this->typeContenu;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Aide3Contenu
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
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
     * Set aide3
     *
     * @param \AppBundle\Entity\Aide3 $aide3
     *
     * @return Aide3Contenu
     */
    public function setAide3(\AppBundle\Entity\Aide3 $aide3 = null)
    {
        $this->aide3 = $aide3;

        return $this;
    }

    /**
     * Get aide3
     *
     * @return \AppBundle\Entity\Aide3
     */
    public function getAide3()
    {
        return $this->aide3;
    }
}
