<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatError
 *
 * @ORM\Table(name="etat_error", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_etat_error_indicateur", columns={"indicateur_id", "exercice", "periode", "dossier_id", "exercice_choose"}), @ORM\UniqueConstraint(name="uniq_etat_error_etat_regime_fiscal", columns={"etat_regime_fiscal_id", "exercice", "periode", "dossier_id", "exercice_choose"})}, indexes={@ORM\Index(name="fk_etat_error_etat_regime_fiscal1_idx", columns={"etat_regime_fiscal_id"}), @ORM\Index(name="fk_etat_error_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_etat_error_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EtatErrorRepository")
 */
class EtatError
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var string
     *
     * @ORM\Column(name="periode", type="string", length=3, nullable=false)
     */
    private $periode = 'A';

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice_choose", type="integer", nullable=false)
     */
    private $exerciceChoose;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\EtatRegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EtatRegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $etatRegimeFiscal;

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
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return EtatError
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
    }

    /**
     * Set periode
     *
     * @param string $periode
     *
     * @return EtatError
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
     * Set exerciceChoose
     *
     * @param integer $exerciceChoose
     *
     * @return EtatError
     */
    public function setExerciceChoose($exerciceChoose)
    {
        $this->exerciceChoose = $exerciceChoose;

        return $this;
    }

    /**
     * Get exerciceChoose
     *
     * @return integer
     */
    public function getExerciceChoose()
    {
        return $this->exerciceChoose;
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
     * Set indicateur
     *
     * @param \AppBundle\Entity\Indicateur $indicateur
     *
     * @return EtatError
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
     * Set etatRegimeFiscal
     *
     * @param \AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal
     *
     * @return EtatError
     */
    public function setEtatRegimeFiscal(\AppBundle\Entity\EtatRegimeFiscal $etatRegimeFiscal = null)
    {
        $this->etatRegimeFiscal = $etatRegimeFiscal;

        return $this;
    }

    /**
     * Get etatRegimeFiscal
     *
     * @return \AppBundle\Entity\EtatRegimeFiscal
     */
    public function getEtatRegimeFiscal()
    {
        return $this->etatRegimeFiscal;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return EtatError
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
