<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneProbabilite
 *
 * @ORM\Table(name="one_probabilite")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneProbabiliteRepository")
 */
class OneProbabilite
{
    /**
     * @var float
     *
     * @ORM\Column(name="pourcentage", type="float", precision=10, scale=0, nullable=false)
     */
    private $pourcentage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set pourcentage
     *
     * @param float $pourcentage
     *
     * @return OneProbabilite
     */
    public function setPourcentage($pourcentage)
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    /**
     * Get pourcentage
     *
     * @return float
     */
    public function getPourcentage()
    {
        return $this->pourcentage;
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
