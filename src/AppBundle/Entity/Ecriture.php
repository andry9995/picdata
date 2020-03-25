<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ecriture
 *
 * @ORM\Table(name="ecriture", indexes={@ORM\Index(name="fk_ecriture_pcg_dossier1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_ecriture_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_ecriture_journal_dossier1_idx", columns={"journal_dossier_id"}), @ORM\Index(name="fk_ecriture_tiers1_idx", columns={"tiers_id"}), @ORM\Index(name="fk_ecriture_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ecriture_historique_upload1_idx", columns={"historique_upload_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EcritureRepository")
 */
class Ecriture
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ecr", type="date", nullable=false)
     */
    private $dateEcr;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=300, nullable=false)
     */
    private $libelle;

    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float", precision=10, scale=0, nullable=true)
     */
    private $debit;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float", precision=10, scale=0, nullable=true)
     */
    private $credit;

    /**
     * @var string
     *
     * @ORM\Column(name="analytique", type="string", length=50, nullable=true)
     */
    private $analytique;

    /**
     * @var string
     *
     * @ORM\Column(name="analytique2", type="string", length=50, nullable=true)
     */
    private $analytique2;

    /**
     * @var string
     *
     * @ORM\Column(name="analytique3", type="string", length=50, nullable=true)
     */
    private $analytique3;

    /**
     * @var string
     *
     * @ORM\Column(name="devis", type="string", length=10, nullable=true)
     */
    private $devis;

    /**
     * @var integer
     *
     * @ORM\Column(name="reel_budget", type="integer", nullable=true)
     */
    private $reelBudget;

    /**
     * @var string
     *
     * @ORM\Column(name="lettrage", type="string", length=10, nullable=true)
     */
    private $lettrage;

    /**
     * @var string
     *
     * @ORM\Column(name="image_str", type="string", length=150, nullable=true)
     */
    private $imageStr;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;

    /**
     * @var \AppBundle\Entity\JournalDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JournalDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="journal_dossier_id", referencedColumnName="id")
     * })
     */
    private $journalDossier;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set dateEcr
     *
     * @param \DateTime $dateEcr
     *
     * @return Ecriture
     */
    public function setDateEcr($dateEcr)
    {
        $this->dateEcr = $dateEcr;

        return $this;
    }

    /**
     * Get dateEcr
     *
     * @return \DateTime
     */
    public function getDateEcr()
    {
        return $this->dateEcr;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Ecriture
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
     * Set debit
     *
     * @param float $debit
     *
     * @return Ecriture
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param float $credit
     *
     * @return Ecriture
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set analytique
     *
     * @param string $analytique
     *
     * @return Ecriture
     */
    public function setAnalytique($analytique)
    {
        $this->analytique = $analytique;

        return $this;
    }

    /**
     * Get analytique
     *
     * @return string
     */
    public function getAnalytique()
    {
        return $this->analytique;
    }

    /**
     * Set analytique2
     *
     * @param string $analytique2
     *
     * @return Ecriture
     */
    public function setAnalytique2($analytique2)
    {
        $this->analytique2 = $analytique2;

        return $this;
    }

    /**
     * Get analytique2
     *
     * @return string
     */
    public function getAnalytique2()
    {
        return $this->analytique2;
    }

    /**
     * Set analytique3
     *
     * @param string $analytique3
     *
     * @return Ecriture
     */
    public function setAnalytique3($analytique3)
    {
        $this->analytique3 = $analytique3;

        return $this;
    }

    /**
     * Get analytique3
     *
     * @return string
     */
    public function getAnalytique3()
    {
        return $this->analytique3;
    }

    /**
     * Set devis
     *
     * @param string $devis
     *
     * @return Ecriture
     */
    public function setDevis($devis)
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * Get devis
     *
     * @return string
     */
    public function getDevis()
    {
        return $this->devis;
    }

    /**
     * Set reelBudget
     *
     * @param integer $reelBudget
     *
     * @return Ecriture
     */
    public function setReelBudget($reelBudget)
    {
        $this->reelBudget = $reelBudget;

        return $this;
    }

    /**
     * Get reelBudget
     *
     * @return integer
     */
    public function getReelBudget()
    {
        return $this->reelBudget;
    }

    /**
     * Set lettrage
     *
     * @param string $lettrage
     *
     * @return Ecriture
     */
    public function setLettrage($lettrage)
    {
        $this->lettrage = $lettrage;

        return $this;
    }

    /**
     * Get lettrage
     *
     * @return string
     */
    public function getLettrage()
    {
        return $this->lettrage;
    }

    /**
     * Set imageStr
     *
     * @param string $imageStr
     *
     * @return Ecriture
     */
    public function setImageStr($imageStr)
    {
        $this->imageStr = $imageStr;

        return $this;
    }

    /**
     * Get imageStr
     *
     * @return string
     */
    public function getImageStr()
    {
        return $this->imageStr;
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return Ecriture
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return Ecriture
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return Ecriture
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set journalDossier
     *
     * @param \AppBundle\Entity\JournalDossier $journalDossier
     *
     * @return Ecriture
     */
    public function setJournalDossier(\AppBundle\Entity\JournalDossier $journalDossier = null)
    {
        $this->journalDossier = $journalDossier;

        return $this;
    }

    /**
     * Get journalDossier
     *
     * @return \AppBundle\Entity\JournalDossier
     */
    public function getJournalDossier()
    {
        return $this->journalDossier;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Ecriture
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set historiqueUpload
     *
     * @param \AppBundle\Entity\HistoriqueUpload $historiqueUpload
     *
     * @return Ecriture
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Ecriture
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
