<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfSouscategorieDossier
 *
 * @ORM\Table(name="ndf_souscategorie_dossier", indexes={@ORM\Index(name="fk_ndf_souscategorie_dossier_pcc1_idx", columns={"pcc_charge"}), @ORM\Index(name="fk_ndf_souscategorie_dossier_pcc2_idx", columns={"pcc_tva"}), @ORM\Index(name="fk_ndf_souscategorie_dossier_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_souscategorie_dossier_ndf_souscategorie1_idx", columns={"ndf_souscategorie_id"}), @ORM\Index(name="fk_ndf_souscategorie_dossier_tva_taux1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity
 */
class NdfSouscategorieDossier
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_rec", type="integer", nullable=true)
     */
    private $tvaRec;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_rec2", type="integer", nullable=true)
     */
    private $tvaRec2;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_tva", referencedColumnName="id")
     * })
     */
    private $pccTva;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_charge", referencedColumnName="id")
     * })
     */
    private $pccCharge;

    /**
     * @var \AppBundle\Entity\NdfSouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfSouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_souscategorie_id", referencedColumnName="id")
     * })
     */
    private $ndfSouscategorie;

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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return NdfSouscategorieDossier
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set tvaRec
     *
     * @param integer $tvaRec
     *
     * @return NdfSouscategorieDossier
     */
    public function setTvaRec($tvaRec)
    {
        $this->tvaRec = $tvaRec;

        return $this;
    }

    /**
     * Get tvaRec
     *
     * @return integer
     */
    public function getTvaRec()
    {
        return $this->tvaRec;
    }

    /**
     * Set tvaRec2
     *
     * @param integer $tvaRec2
     *
     * @return NdfSouscategorieDossier
     */
    public function setTvaRec2($tvaRec2)
    {
        $this->tvaRec2 = $tvaRec2;

        return $this;
    }

    /**
     * Get tvaRec2
     *
     * @return integer
     */
    public function getTvaRec2()
    {
        return $this->tvaRec2;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return NdfSouscategorieDossier
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return NdfSouscategorieDossier
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set pccTva
     *
     * @param \AppBundle\Entity\Pcc $pccTva
     *
     * @return NdfSouscategorieDossier
     */
    public function setPccTva(\AppBundle\Entity\Pcc $pccTva = null)
    {
        $this->pccTva = $pccTva;

        return $this;
    }

    /**
     * Get pccTva
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccTva()
    {
        return $this->pccTva;
    }

    /**
     * Set pccCharge
     *
     * @param \AppBundle\Entity\Pcc $pccCharge
     *
     * @return NdfSouscategorieDossier
     */
    public function setPccCharge(\AppBundle\Entity\Pcc $pccCharge = null)
    {
        $this->pccCharge = $pccCharge;

        return $this;
    }

    /**
     * Get pccCharge
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccCharge()
    {
        return $this->pccCharge;
    }

    /**
     * Set ndfSouscategorie
     *
     * @param \AppBundle\Entity\NdfSouscategorie $ndfSouscategorie
     *
     * @return NdfSouscategorieDossier
     */
    public function setNdfSouscategorie(\AppBundle\Entity\NdfSouscategorie $ndfSouscategorie = null)
    {
        $this->ndfSouscategorie = $ndfSouscategorie;

        return $this;
    }

    /**
     * Get ndfSouscategorie
     *
     * @return \AppBundle\Entity\NdfSouscategorie
     */
    public function getNdfSouscategorie()
    {
        return $this->ndfSouscategorie;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfSouscategorieDossier
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
