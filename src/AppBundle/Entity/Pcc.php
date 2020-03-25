<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pcc
 *
 * @ORM\Table(name="pcc", uniqueConstraints={@ORM\UniqueConstraint(name="fk_unik_pcc_compte_dossier", columns={"compte", "dossier_id"})}, indexes={@ORM\Index(name="fk_pcg_dossier_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_pcc_historique_upload1_idx", columns={"historique_upload_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PccRepository")
 */
class Pcc
{
    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=20, nullable=false)
     */
    private $compte = '';

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=150, nullable=false)
     */
    private $intitule = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

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
     * @var \AppBundle\Entity\HistoriqueUpload
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\HistoriqueUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="historique_upload_id", referencedColumnName="id")
     * })
     */
    private $historiqueUpload;

    /**
     * @var int
     *
     * @ORM\Column(name="collectif_tiers", type="integer", nullable=false)
     */
    private $collectifTiers = -1;

    /**
     * Set compte
     *
     * @param string $compte
     *
     * @return Pcc
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Pcc
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Pcc
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Pcc
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

    /**
     * Set historiqueUpload
     *
     * @param \AppBundle\Entity\HistoriqueUpload $historiqueUpload
     *
     * @return Pcc
     */
    public function setHistoriqueUpload(\AppBundle\Entity\HistoriqueUpload $historiqueUpload = null)
    {
        $this->historiqueUpload = $historiqueUpload;

        return $this;
    }

    /**
     * Get historiqueUpload
     *
     * @return \AppBundle\Entity\HistoriqueUpload
     */
    public function getHistoriqueUpload()
    {
        return $this->historiqueUpload;
    }

    /**
     * @return int
     */
    public function getCollectifTiers()
    {
        return $this->collectifTiers;
    }

    /**
     * @param $collectifTiers
     * @return $this
     */
    public function setCollectifTiers($collectifTiers)
    {
        $this->collectifTiers = $collectifTiers;
        return $this;
    }


    /*******************************************************
     *                  AJOUT MANUEL
     ******************************************************/
    /**
     * @var int
     */
    private $cochage = 0;
    /**
     * Set cochage
     *
     * @param integer $cochage
     *
     * @return Pcc
     */
    public function setCochage($cochage)
    {
        $this->cochage = $cochage;
        return $this;
    }
    /**
     * Get Cochage
     *
     * @return integer cochage
     */
    public function getCochage()
    {
        return $this->cochage;
    }

    private $idEtatCompte = 0;
    /**
     * Set idEtatCompte
     *
     * @param integer $idEtatCompte
     *
     * @return Pcc
     */
    public function setIdEtatCompte($idEtatCompte)
    {
        $this->idEtatCompte = $idEtatCompte;
        return $this;
    }
    /**
     * Get idEtatCompte
     *
     * @return integer idEtatCompte
     */
    public function getIdEtatCompte()
    {
        return $this->idEtatCompte;
    }
}
