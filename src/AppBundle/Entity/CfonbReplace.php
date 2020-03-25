<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CfonbReplace
 *
 * @ORM\Table(name="cfonb_replace", uniqueConstraints={@ORM\UniqueConstraint(name="uk_cfonb_replace_recherche", columns={"recherche"})})
 * @ORM\Entity
 */
class CfonbReplace
{
    /**
     * @var string
     *
     * @ORM\Column(name="recherche", type="string", length=45, nullable=false)
     */
    private $recherche;

    /**
     * @var string
     *
     * @ORM\Column(name="remplace", type="string", length=45, nullable=true)
     */
    private $remplace;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set recherche
     *
     * @param string $recherche
     *
     * @return CfonbReplace
     */
    public function setRecherche($recherche)
    {
        $this->recherche = $recherche;

        return $this;
    }

    /**
     * Get recherche
     *
     * @return string
     */
    public function getRecherche()
    {
        return $this->recherche;
    }

    /**
     * Set remplace
     *
     * @param string $remplace
     *
     * @return CfonbReplace
     */
    public function setRemplace($remplace)
    {
        $this->remplace = $remplace;

        return $this;
    }

    /**
     * Get remplace
     *
     * @return string
     */
    public function getRemplace()
    {
        return $this->remplace;
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
