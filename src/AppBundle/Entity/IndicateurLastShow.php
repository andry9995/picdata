<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndicateurLastShow
 *
 * @ORM\Table(name="indicateur_last_show", indexes={@ORM\Index(name="fk_indicateur_last_show_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_indicateur_last_show_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_indicateur_last_show_type_graphe1_idx", columns={"type_graphe_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurLastShowRepository")
 */
class IndicateurLastShow
{
    /**
     * @var string
     *
     * @ORM\Column(name="exercices", type="string", length=50, nullable=false)
     */
    private $exercices;

    /**
     * @var integer
     *
     * @ORM\Column(name="analyse", type="integer", nullable=false)
     */
    private $analyse;

    /**
     * @var string
     *
     * @ORM\Column(name="periode", type="string", length=500, nullable=true)
     */
    private $periode;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TypeGraphe
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeGraphe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_graphe_id", referencedColumnName="id")
     * })
     */
    private $typeGraphe;

    /**
     * @var \AppBundle\Entity\Indicateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Indicateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicateur_id", referencedColumnName="id")
     * })
     */
    private $indicateur;

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
     * Set exercices
     *
     * @param string $exercices
     *
     * @return IndicateurLastShow
     */
    public function setExercices($exercices)
    {
        $this->exercices = $exercices;

        return $this;
    }

    /**
     * Get exercices
     *
     * @return string
     */
    public function getExercices()
    {
        return $this->exercices;
    }

    /**
     * Set analyse
     *
     * @param integer $analyse
     *
     * @return IndicateurLastShow
     */
    public function setAnalyse($analyse)
    {
        $this->analyse = $analyse;

        return $this;
    }

    /**
     * Get analyse
     *
     * @return integer
     */
    public function getAnalyse()
    {
        return $this->analyse;
    }

    /**
     * Set periode
     *
     * @param string $periode
     *
     * @return IndicateurLastShow
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return string
     */
    public function getPeriode()
    {
        return $this->periode;
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
     * Set typeGraphe
     *
     * @param \AppBundle\Entity\TypeGraphe $typeGraphe
     *
     * @return IndicateurLastShow
     */
    public function setTypeGraphe(\AppBundle\Entity\TypeGraphe $typeGraphe = null)
    {
        $this->typeGraphe = $typeGraphe;

        return $this;
    }

    /**
     * Get typeGraphe
     *
     * @return \AppBundle\Entity\TypeGraphe
     */
    public function getTypeGraphe()
    {
        return $this->typeGraphe;
    }

    /**
     * Set indicateur
     *
     * @param \AppBundle\Entity\Indicateur $indicateur
     *
     * @return IndicateurLastShow
     */
    public function setIndicateur(\AppBundle\Entity\Indicateur $indicateur = null)
    {
        $this->indicateur = $indicateur;

        return $this;
    }

    /**
     * Get indicateur
     *
     * @return \AppBundle\Entity\Indicateur
     */
    public function getIndicateur()
    {
        return $this->indicateur;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return IndicateurLastShow
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
