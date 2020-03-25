<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactAnnee
 *
 * @ORM\Table(name="fact_annee", uniqueConstraints={@ORM\UniqueConstraint(name="annee_UNIQUE", columns={"annee"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactAnneeRepository")
 */
class FactAnnee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=false)
     */
    private $annee;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return FactAnnee
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
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
