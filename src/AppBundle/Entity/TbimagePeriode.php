<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TbimagePeriode
 *
 * @ORM\Table(name="tbimage_periode", uniqueConstraints={@ORM\UniqueConstraint(name="dossier_id_UNIQUE", columns={"dossier_id"})}, indexes={@ORM\Index(name="fk_tbimage_periode_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TbimagePeriodeRepository")
 */
class TbimagePeriode
{
    /**
 * @var string
 *
 * @ORM\Column(name="periode", type="string", length=1, nullable=false)
 */
    private $periode = 'M';

    /**
     * @var string
     *
     * @ORM\Column(name="periode_piece", type="string", length=1, nullable=true)
     */
    private $periodePiece;

    /**
     * @var integer
     *
     * @ORM\Column(name="mois_plus", type="integer", nullable=true)
     */
    private $moisPlus = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="jour", type="integer", nullable=true)
     */
    private $jour = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="demarrage", type="date", nullable=true)
     */
    private $demarrage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="premiere_cloture", type="date", nullable=true)
     */
    private $premiereCloture;

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
     * Une TbimagePeriode a un et un seul dossier
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Dossier", inversedBy="tbimagePeriode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set periode
     *
     * @param string $periode
     *
     * @return TbimagePeriode
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
     * Set periodePiece
     *
     * @param string $periodePiece
     *
     * @return TbimagePeriode
     */
    public function setPeriodePiece($periodePiece)
    {
        $this->periodePiece = $periodePiece;

        return $this;
    }

    /**
     * Get periodePiece
     *
     * @return string
     */
    public function getPeriodePiece()
    {
        if (!$this->periodePiece) {
            return $this->periode;
        }
        return $this->periodePiece;
    }

    /**
     * Set moisPlus
     *
     * @param integer $moisPlus
     *
     * @return TbimagePeriode
     */
    public function setMoisPlus($moisPlus)
    {
        $this->moisPlus = $moisPlus;

        return $this;
    }

    /**
     * Get moisPlus
     *
     * @return integer
     */
    public function getMoisPlus()
    {
        return $this->moisPlus;
    }

    /**
     * Set jour
     *
     * @param integer $jour
     *
     * @return TbimagePeriode
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
     * Set demarrage
     *
     * @param \DateTime $demarrage
     *
     * @return TbimagePeriode
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
        if (is_null($this->demarrage)) $this->dossier->getDebutActivite();
        return  $this->demarrage;
        //return $this->dossier->getDebutActivite();
    }

    /**
     * Set premiereCloture
     *
     * @param \DateTime $premiereCloture
     *
     * @return TbimagePeriode
     */
    public function setPremiereCloture($premiereCloture)
    {
        $this->premiereCloture = $premiereCloture;

        return $this;
    }

    /**
     * Get premiereCloture
     *
     * @return \DateTime
     */
    public function getPremiereCloture()
    {
        if (is_null($this->premiereCloture)) $this->dossier->getDateCloture();
        return  $this->premiereCloture;
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
     * @return TbimagePeriode
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
