<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanqueCompte
 *
 * @ORM\Table(name="banque_compte", indexes={@ORM\Index(name="fk_banque_compte_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_banque_compte_banque1_idx", columns={"banque_id"}), @ORM\Index(name="fk_banque_compte_journal2_idx", columns={"journal_id"}), @ORM\Index(name="fk_banque_compte_pcc1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_banque_compte_source_image_idx", columns={"source_image_id"}), @ORM\Index(name="fk_banque_compte_journal_dossier1_idx", columns={"journal_dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanqueCompteRepository")
 */
class BanqueCompte
{
    /**
     * @var string
     *
     * @ORM\Column(name="numcompte", type="string", length=30, nullable=true)
     */
    private $numcompte;

    /**
     * @var string
     *
     * @ORM\Column(name="numcb", type="string", length=45, nullable=true)
     */
    private $numcb;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=45, nullable=true)
     */
    private $iban;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="date", nullable=true)
     */
    private $dateModification;

    /**
     * @var float
     *
     * @ORM\Column(name="solde", type="float", precision=10, scale=0, nullable=true)
     */
    private $solde;

    /**
     * @var integer
     *
     * @ORM\Column(name="old_banque_id", type="integer", nullable=true)
     */
    private $oldBanqueId;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="avec_frais", type="integer", nullable=true)
     */
    private $avecFrais;

    /**
     * @var integer
     *
     * @ORM\Column(name="ob_a_saisir", type="integer", nullable=true)
     */
    private $obASaisir;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="mode_saisie", type="integer", nullable=false)
     */
    private $modeSaisie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;

    /**
     * @var \AppBundle\Entity\Journal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Journal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="journal_id", referencedColumnName="id")
     * })
     */
    private $journal;

    /**
     * @var \AppBundle\Entity\SourceImage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SourceImage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_image_id", referencedColumnName="id")
     * })
     */
    private $sourceImage;

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
     * @var \AppBundle\Entity\Banque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Banque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_id", referencedColumnName="id")
     * })
     */
    private $banque;



    /**
     * Set numcompte
     *
     * @param string $numcompte
     *
     * @return BanqueCompte
     */
    public function setNumcompte($numcompte)
    {
        $this->numcompte = $numcompte;

        return $this;
    }

    /**
     * Get numcompte
     *
     * @return string
     */
    public function getNumcompte()
    {
        return $this->numcompte;
    }

    /**
     * Set numcb
     *
     * @param string $numcb
     *
     * @return BanqueCompte
     */
    public function setNumcb($numcb)
    {
        $this->numcb = $numcb;

        return $this;
    }

    /**
     * Get numcb
     *
     * @return string
     */
    public function getNumcb()
    {
        return $this->numcb;
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return BanqueCompte
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return BanqueCompte
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set solde
     *
     * @param float $solde
     *
     * @return BanqueCompte
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return float
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set oldBanqueId
     *
     * @param integer $oldBanqueId
     *
     * @return BanqueCompte
     */
    public function setOldBanqueId($oldBanqueId)
    {
        $this->oldBanqueId = $oldBanqueId;

        return $this;
    }

    /**
     * Get oldBanqueId
     *
     * @return integer
     */
    public function getOldBanqueId()
    {
        return $this->oldBanqueId;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return BanqueCompte
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
     * Set avecFrais
     *
     * @param integer $avecFrais
     *
     * @return BanqueCompte
     */
    public function setAvecFrais($avecFrais)
    {
        $this->avecFrais = $avecFrais;

        return $this;
    }

    /**
     * Get avecFrais
     *
     * @return integer
     */
    public function getAvecFrais()
    {
        return $this->avecFrais;
    }

    /**
     * Set obASaisir
     *
     * @param integer $obASaisir
     *
     * @return BanqueCompte
     */
    public function setObASaisir($obASaisir)
    {
        $this->obASaisir = $obASaisir;

        return $this;
    }

    /**
     * Get obASaisir
     *
     * @return integer
     */
    public function getObASaisir()
    {
        return $this->obASaisir;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     *
     * @return BanqueCompte
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return integer
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set modeSaisie
     *
     * @param integer $modeSaisie
     *
     * @return BanqueCompte
     */
    public function setModeSaisie($modeSaisie)
    {
        $this->modeSaisie = $modeSaisie;

        return $this;
    }

    /**
     * Get modeSaisie
     *
     * @return integer
     */
    public function getModeSaisie()
    {
        return $this->modeSaisie;
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
     * Set journalDossier
     *
     * @param \AppBundle\Entity\JournalDossier $journalDossier
     *
     * @return BanqueCompte
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
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return BanqueCompte
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
     * Set journal
     *
     * @param \AppBundle\Entity\Journal $journal
     *
     * @return BanqueCompte
     */
    public function setJournal(\AppBundle\Entity\Journal $journal = null)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get journal
     *
     * @return \AppBundle\Entity\Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set sourceImage
     *
     * @param \AppBundle\Entity\SourceImage $sourceImage
     *
     * @return BanqueCompte
     */
    public function setSourceImage(\AppBundle\Entity\SourceImage $sourceImage = null)
    {
        $this->sourceImage = $sourceImage;

        return $this;
    }

    /**
     * Get sourceImage
     *
     * @return \AppBundle\Entity\SourceImage
     */
    public function getSourceImage()
    {
        return $this->sourceImage;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return BanqueCompte
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
     * Set banque
     *
     * @param \AppBundle\Entity\Banque $banque
     *
     * @return BanqueCompte
     */
    public function setBanque(\AppBundle\Entity\Banque $banque = null)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return \AppBundle\Entity\Banque
     */
    public function getBanque()
    {
        return $this->banque;
    }
}
