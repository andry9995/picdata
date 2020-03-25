<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JournalModel
 *
 * @ORM\Table(name="journal_model", indexes={@ORM\Index(name="fk_journal_journal_id_idx", columns={"journal_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JournalModelRepository")
 */
class JournalModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     */
    private $libelle;

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
     * Set code
     *
     * @param string $code
     *
     * @return JournalModel
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
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
     * @return JournalModel
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
     * @return JournalModel
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
}
