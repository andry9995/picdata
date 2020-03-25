<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emails
 *
 * @ORM\Table(name="emails", indexes={@ORM\Index(name="fk_email_a_envoyer_smtp1_idx", columns={"smtp_id"}), @ORM\Index(name="fk_email_a_envoyer_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_emails_echange_item_idx", columns={"echange_item_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmailsRepository")
 */
class Emails
{
    /**
     * @var string
     *
     * @ORM\Column(name="to_address", type="text", length=65535, nullable=false)
     */
    private $toAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="from_address", type="text", length=65535, nullable=true)
     */
    private $fromAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="from_label", type="string", length=255, nullable=true)
     */
    private $fromLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="text", length=65535, nullable=true)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="text", length=65535, nullable=true)
     */
    private $bcc;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string", length=255, nullable=false)
     */
    private $sujet;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=false)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="date", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_tentative_envoi", type="integer", nullable=true)
     */
    private $nbTentativeEnvoi = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="last_error", type="text", length=65535, nullable=true)
     */
    private $lastError;

    /**
     * @var string
     *
     * @ORM\Column(name="type_email", type="string", length=50, nullable=false)
     */
    private $typeEmail = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Smtp
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Smtp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="smtp_id", referencedColumnName="id")
     * })
     */
    private $smtp;

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
     * @var \AppBundle\Entity\EchangeItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EchangeItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_item_id", referencedColumnName="id")
     * })
     */
    private $echangeItem;



    /**
     * Set toAddress
     *
     * @param string $toAddress
     *
     * @return Emails
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    /**
     * Get toAddress
     *
     * @return string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * Set fromAddress
     *
     * @param string $fromAddress
     *
     * @return Emails
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    /**
     * Get fromAddress
     *
     * @return string
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * Set fromLabel
     *
     * @param string $fromLabel
     *
     * @return Emails
     */
    public function setFromLabel($fromLabel)
    {
        $this->fromLabel = $fromLabel;

        return $this;
    }

    /**
     * Get fromLabel
     *
     * @return string
     */
    public function getFromLabel()
    {
        return $this->fromLabel;
    }

    /**
     * Set cc
     *
     * @param string $cc
     *
     * @return Emails
     */
    public function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set bcc
     *
     * @param string $bcc
     *
     * @return Emails
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * Get bcc
     *
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Set sujet
     *
     * @param string $sujet
     *
     * @return Emails
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Emails
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Emails
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Emails
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     *
     * @return Emails
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set nbTentativeEnvoi
     *
     * @param integer $nbTentativeEnvoi
     *
     * @return Emails
     */
    public function setNbTentativeEnvoi($nbTentativeEnvoi)
    {
        $this->nbTentativeEnvoi = $nbTentativeEnvoi;

        return $this;
    }

    /**
     * Get nbTentativeEnvoi
     *
     * @return integer
     */
    public function getNbTentativeEnvoi()
    {
        return $this->nbTentativeEnvoi;
    }

    /**
     * Set lastError
     *
     * @param string $lastError
     *
     * @return Emails
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;

        return $this;
    }

    /**
     * Get lastError
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Set typeEmail
     *
     * @param string $typeEmail
     *
     * @return Emails
     */
    public function setTypeEmail($typeEmail)
    {
        $this->typeEmail = $typeEmail;

        return $this;
    }

    /**
     * Get typeEmail
     *
     * @return string
     */
    public function getTypeEmail()
    {
        return $this->typeEmail;
    }

    /**
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return Emails
     */
    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return \DateTime
     */
    public function getDateEcheance()
    {
        return $this->dateEcheance;
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
     * Set smtp
     *
     * @param \AppBundle\Entity\Smtp $smtp
     *
     * @return Emails
     */
    public function setSmtp(\AppBundle\Entity\Smtp $smtp = null)
    {
        $this->smtp = $smtp;

        return $this;
    }

    /**
     * Get smtp
     *
     * @return \AppBundle\Entity\Smtp
     */
    public function getSmtp()
    {
        return $this->smtp;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Emails
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
     * Set echangeItem
     *
     * @param \AppBundle\Entity\EchangeItem $echangeItem
     *
     * @return Emails
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
