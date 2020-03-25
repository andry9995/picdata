<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TacheDomaine
 *
 * @ORM\Table(name="tache_domaine")
 * @ORM\Entity
 */
class TacheDomaine
{
    /**
     * @var string
     *
     * @ORM\Column(name="domaine", type="string", length=150, nullable=false)
     */
    private $domaine;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set domaine
     *
     * @param string $domaine
     *
     * @return TacheDomaine
     */
    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return string
     */
    public function getDomaine()
    {
        return $this->domaine;
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
