<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportParam
 *
 * @ORM\Table(name="import_param", indexes={@ORM\Index(name="fk_import_param_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImportParamRepository")
 */
class ImportParam
{
    /**
     * @var integer
     *
     * @ORM\Column(name="periode", type="integer", nullable=false)
     */
    private $periode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demarrage", type="date", nullable=true)
     */
    private $demarrage;

    /**
     * @var integer
     *
     * @ORM\Column(name="calculer_a_partir", type="integer", nullable=false)
     */
    private $calculerAPartir = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="jour", type="integer", nullable=false)
     */
    private $jour = '0';

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
     * Set periode
     *
     * @param integer $periode
     *
     * @return ImportParam
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return integer
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set demarrage
     *
     * @param \DateTime $demarrage
     *
     * @return ImportParam
     */
    public function setDemarrage($demarrage)
    {
        $this->demarrage = $demarrage;

        return $this;
    }

    /**
     * Get demarrage
     *
     * @return \DateTime
     */
    public function getDemarrage()
    {
        return $this->demarrage;
    }

    /**
     * Set calculerAPartir
     *
     * @param integer $calculerAPartir
     *
     * @return ImportParam
     */
    public function setCalculerAPartir($calculerAPartir)
    {
        $this->calculerAPartir = $calculerAPartir;

        return $this;
    }

    /**
     * Get calculerAPartir
     *
     * @return integer
     */
    public function getCalculerAPartir()
    {
        return $this->calculerAPartir;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ImportParam
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set jour
     *
     * @param integer $jour
     *
     * @return ImportParam
     */
    public function setJour($jour)
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return integer
     */
    public function getJour()
    {
        return $this->jour;
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
     * @return ImportParam
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
