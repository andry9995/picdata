<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EchangeEcriture
 *
 * @ORM\Table(name="echange_ecriture", indexes={@ORM\Index(name="fk_echange_ecriture_echange_item_idx", columns={"echange_item_id"}), @ORM\Index(name="fk_echange_ecriture_image_idx", columns={"image_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EchangeEcritureRepository")
 */
class EchangeEcriture
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="journal", type="string", length=5, nullable=true)
     */
    private $journal;

    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=20, nullable=true)
     */
    private $compte;

    /**
     * @var integer
     *
     * @ORM\Column(name="compte_type", type="integer", nullable=false)
     */
    private $compteType = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=200, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="debit", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $debit = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="credit", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $credit = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="page", type="string", length=50, nullable=true)
     */
    private $page;

    /**
     * @var string
     *
     * @ORM\Column(name="solde", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $solde = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="piece", type="string", length=20, nullable=true)
     */
    private $piece;

    /**
     * @var integer
     *
     * @ORM\Column(name="pas_piece", type="integer", nullable=false)
     */
    private $pasPiece = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_calcul_a_lettrer", type="date", nullable=true)
     */
    private $dateCalculALettrer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\EchangeItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EchangeItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_item_id", referencedColumnName="id")
     * })
     */
    private $echangeItem;



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return EchangeEcriture
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
     * Set journal
     *
     * @param string $journal
     *
     * @return EchangeEcriture
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
     * Set compte
     *
     * @param string $compte
     *
     * @return EchangeEcriture
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
     * Set compteType
     *
     * @param integer $compteType
     *
     * @return EchangeEcriture
     */
    public function setCompteType($compteType)
    {
        $this->compteType = $compteType;

        return $this;
    }

    /**
     * Get compteType
     *
     * @return integer
     */
    public function getCompteType()
    {
        return $this->compteType;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return EchangeEcriture
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
     * @param string $debit
     *
     * @return EchangeEcriture
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return string
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param string $credit
     *
     * @return EchangeEcriture
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return EchangeEcriture
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
     * Set page
     *
     * @param string $page
     *
     * @return EchangeEcriture
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set solde
     *
     * @param string $solde
     *
     * @return EchangeEcriture
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return string
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set piece
     *
     * @param string $piece
     *
     * @return EchangeEcriture
     */
    public function setPiece($piece)
    {
        $this->piece = $piece;

        return $this;
    }

    /**
     * Get piece
     *
     * @return string
     */
    public function getPiece()
    {
        return $this->piece;
    }

    /**
     * Set pasPiece
     *
     * @param integer $pasPiece
     *
     * @return EchangeEcriture
     */
    public function setPasPiece($pasPiece)
    {
        $this->pasPiece = $pasPiece;

        return $this;
    }

    /**
     * Get pasPiece
     *
     * @return integer
     */
    public function getPasPiece()
    {
        return $this->pasPiece;
    }

    /**
     * Set dateCalculALettrer
     *
     * @param \DateTime $dateCalculALettrer
     *
     * @return EchangeEcriture
     */
    public function setDateCalculALettrer($dateCalculALettrer)
    {
        $this->dateCalculALettrer = $dateCalculALettrer;

        return $this;
    }

    /**
     * Get dateCalculALettrer
     *
     * @return \DateTime
     */
    public function getDateCalculALettrer()
    {
        return $this->dateCalculALettrer;
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return EchangeEcriture
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
     * Set echangeItem
     *
     * @param \AppBundle\Entity\EchangeItem $echangeItem
     *
     * @return EchangeEcriture
     */
    public function setEchangeItem(\AppBundle\Entity\EchangeItem $echangeItem = null)
    {
        $this->echangeItem = $echangeItem;

        return $this;
    }

    /**
     * Get echangeItem
     *
     * @return \AppBundle\Entity\EchangeItem
     */
    public function getEchangeItem()
    {
        return $this->echangeItem;
    }
}
