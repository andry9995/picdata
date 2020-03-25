<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AideAssocie
 *
 * @ORM\Table(name="aide_associe", indexes={@ORM\Index(name="fk_aide_associe_aide_31_idx", columns={"aide_3_id_parent"}), @ORM\Index(name="fk_aide_associe_aide_32_idx", columns={"aide_3_id_associe"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AideAssocieRepository")
 */
class AideAssocie
{
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
     *   @ORM\JoinColumn(name="aide_3_id_associe", referencedColumnName="id")
     * })
     */
    private $aide3Associe;

    /**
     * @var \AppBundle\Entity\Aide3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_3_id_parent", referencedColumnName="id")
     * })
     */
    private $aide3Parent;



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
     * Set aide3Associe
     *
     * @param \AppBundle\Entity\Aide3 $aide3Associe
     *
     * @return AideAssocie
     */
    public function setAide3Associe(\AppBundle\Entity\Aide3 $aide3Associe = null)
    {
        $this->aide3Associe = $aide3Associe;

        return $this;
    }

    /**
     * Get aide3Associe
     *
     * @return \AppBundle\Entity\Aide3
     */
    public function getAide3Associe()
    {
        return $this->aide3Associe;
    }

    /**
     * Set aide3Parent
     *
     * @param \AppBundle\Entity\Aide3 $aide3Parent
     *
     * @return AideAssocie
     */
    public function setAide3Parent(\AppBundle\Entity\Aide3 $aide3Parent = null)
    {
        $this->aide3Parent = $aide3Parent;

        return $this;
    }

    /**
     * Get aide3Parent
     *
     * @return \AppBundle\Entity\Aide3
     */
    public function getAide3Parent()
    {
        return $this->aide3Parent;
    }
}
