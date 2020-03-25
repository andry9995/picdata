<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentComptable
 *
 * @ORM\Table(name="document_comptable", indexes={@ORM\Index(name="fk_document_comptable_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class DocumentComptable
{
    /**
     * @var string
     *
     * @ORM\Column(name="plan_comptable", type="text", length=65535, nullable=true)
     */
    private $planComptable;

    /**
     * @var string
     *
     * @ORM\Column(name="archive_info", type="text", length=65535, nullable=true)
     */
    private $archiveInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="grand_livre", type="text", length=65535, nullable=true)
     */
    private $grandLivre;

    /**
     * @var string
     *
     * @ORM\Column(name="journal", type="text", length=65535, nullable=true)
     */
    private $journal;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="text", length=65535, nullable=true)
     */
    private $balance;

    /**
     * @var string
     *
     * @ORM\Column(name="rapproch_bq", type="text", length=65535, nullable=true)
     */
    private $rapprochBq;

    /**
     * @var string
     *
     * @ORM\Column(name="immobilisation", type="text", length=65535, nullable=true)
     */
    private $immobilisation;

    /**
     * @var string
     *
     * @ORM\Column(name="liasse", type="text", length=65535, nullable=true)
     */
    private $liasse;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="text", length=65535, nullable=true)
     */
    private $tva;

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
     * Set planComptable
     *
     * @param string $planComptable
     *
     * @return DocumentComptable
     */
    public function setPlanComptable($planComptable)
    {
        $this->planComptable = $planComptable;

        return $this;
    }

    /**
     * Get planComptable
     *
     * @return string
     */
    public function getPlanComptable()
    {
        return $this->planComptable;
    }

    /**
     * Set archiveInfo
     *
     * @param string $archiveInfo
     *
     * @return DocumentComptable
     */
    public function setArchiveInfo($archiveInfo)
    {
        $this->archiveInfo = $archiveInfo;

        return $this;
    }

    /**
     * Get archiveInfo
     *
     * @return string
     */
    public function getArchiveInfo()
    {
        return $this->archiveInfo;
    }

    /**
     * Set grandLivre
     *
     * @param string $grandLivre
     *
     * @return DocumentComptable
     */
    public function setGrandLivre($grandLivre)
    {
        $this->grandLivre = $grandLivre;

        return $this;
    }

    /**
     * Get grandLivre
     *
     * @return string
     */
    public function getGrandLivre()
    {
        return $this->grandLivre;
    }

    /**
     * Set journal
     *
     * @param string $journal
     *
     * @return DocumentComptable
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get journal
     *
     * @return string
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return DocumentComptable
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set rapprochBq
     *
     * @param string $rapprochBq
     *
     * @return DocumentComptable
     */
    public function setRapprochBq($rapprochBq)
    {
        $this->rapprochBq = $rapprochBq;

        return $this;
    }

    /**
     * Get rapprochBq
     *
     * @return string
     */
    public function getRapprochBq()
    {
        return $this->rapprochBq;
    }

    /**
     * Set immobilisation
     *
     * @param string $immobilisation
     *
     * @return DocumentComptable
     */
    public function setImmobilisation($immobilisation)
    {
        $this->immobilisation = $immobilisation;

        return $this;
    }

    /**
     * Get immobilisation
     *
     * @return string
     */
    public function getImmobilisation()
    {
        return $this->immobilisation;
    }

    /**
     * Set liasse
     *
     * @param string $liasse
     *
     * @return DocumentComptable
     */
    public function setLiasse($liasse)
    {
        $this->liasse = $liasse;

        return $this;
    }

    /**
     * Get liasse
     *
     * @return string
     */
    public function getLiasse()
    {
        return $this->liasse;
    }

    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return DocumentComptable
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva()
    {
        return $this->tva;
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
     * @return DocumentComptable
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
