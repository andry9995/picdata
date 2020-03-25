<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JournalDossier
 *
 * @ORM\Table(name="journal_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="fk_journal_dossier_compte_dossier", columns={"code", "dossier_id"})}, indexes={@ORM\Index(name="fk_journal_dossier_journal1_idx", columns={"journal_id"}), @ORM\Index(name="fk_journal_dossier_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_journal_dossier_historique_upload1_idx", columns={"historique_upload_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JournalDossierRepository")
 */
class JournalDossier
{
    /**
     * @var binary
     *
     * @ORM\Column(name="code", type="binary", nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=false)
     */
    private $libelle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="code_str", type="string", length=20, nullable=false)
     */
    private $codeStr;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set code
     *
     * @param binary $code
     *
     * @return JournalDossier
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return binary
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return JournalDossier
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
     * Set codeStr
     *
     * @param string $codeStr
     *
     * @return JournalDossier
     */
    public function setCodeStr($codeStr)
    {
        $this->codeStr = $codeStr;

        return $this;
    }

    /**
     * Get codeStr
     *
     * @return string
     */
    public function getCodeStr()
    {
        return $this->codeStr;
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
     * Set journal
     *
     * @param \AppBundle\Entity\Journal $journal
     *
     * @return JournalDossier
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
     * Set historiqueUpload
     *
     * @param \AppBundle\Entity\HistoriqueUpload $historiqueUpload
     *
     * @return JournalDossier
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
     * @return JournalDossier
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
