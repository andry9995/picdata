<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationGestion
 *
 * @ORM\Table(name="prestation_gestion", indexes={@ORM\Index(name="fk_prestation_gestion_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationGestionRepository")
 */
class PrestationGestion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="situation", type="integer", nullable=true)
     */
    private $situation;

    /**
     * @var integer
     *
     * @ORM\Column(name="tableau_bord", type="integer", nullable=true)
     */
    private $tableauBord;

    /**
     * @var integer
     *
     * @ORM\Column(name="indicateur", type="integer", nullable=true)
     */
    private $indicateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer", nullable=true)
     */
    private $budget;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set situation
     *
     * @param integer $situation
     *
     * @return PrestationGestion
     */
    public function setSituation($situation)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation
     *
     * @return integer
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * Set tableauBord
     *
     * @param integer $tableauBord
     *
     * @return PrestationGestion
     */
    public function setTableauBord($tableauBord)
    {
        $this->tableauBord = $tableauBord;

        return $this;
    }

    /**
     * Get tableauBord
     *
     * @return integer
     */
    public function getTableauBord()
    {
        return $this->tableauBord;
    }

    /**
     * Set indicateur
     *
     * @param integer $indicateur
     *
     * @return PrestationGestion
     */
    public function setIndicateur($indicateur)
    {
        $this->indicateur = $indicateur;

        return $this;
    }

    /**
     * Get indicateur
     *
     * @return integer
     */
    public function getIndicateur()
    {
        return $this->indicateur;
    }

    /**
     * Set budget
     *
     * @param integer $budget
     *
     * @return PrestationGestion
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return integer
     */
    public function getBudget()
    {
        return $this->budget;
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return PrestationGestion
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }
}
