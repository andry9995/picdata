<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactContratFichier
 *
 * @ORM\Table(name="fact_contrat_fichier", indexes={@ORM\Index(name="fk_fact_contrat1_idx", columns={"fact_contrat_id"})})
 * @ORM\Entity
 */
class FactContratFichier
{
    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="text", length=65535, nullable=false)
     */
    private $fichier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactContrat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactContrat", inversedBy="factContratFichiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_contrat_id", referencedColumnName="id")
     * })
     */
    private $factContrat;



    /**
     * Set fichier
     *
     * @param string $fichier
     *
     * @return FactContratFichier
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string
     */
    public function getFichier()
    {
        return $this->fichier;
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
     * Set factContrat
     *
     * @param \AppBundle\Entity\FactContrat $factContrat
     *
     * @return FactContratFichier
     */
    public function setFactContrat(\AppBundle\Entity\FactContrat $factContrat = null)
    {
        $this->factContrat = $factContrat;

        return $this;
    }

    /**
     * Get factContrat
     *
     * @return \AppBundle\Entity\FactContrat
     */
    public function getFactContrat()
    {
        return $this->factContrat;
    }
}
