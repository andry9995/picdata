<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogInfoperdos
 *
 * @ORM\Table(name="log_infoperdos", indexes={@ORM\Index(name="fk_log_infoperdos_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_log_infoperdos_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class LogInfoperdos
{
    /**
     * @var string
     *
     * @ORM\Column(name="champ", type="string", length=45, nullable=false)
     */
    private $champ;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur_ancien", type="text", length=65535, nullable=false)
     */
    private $valeurAncien;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur_nouveau", type="text", length=65535, nullable=false)
     */
    private $valeurNouveau;

    /**
     * @var integer
     *
     * @ORM\Column(name="mail", type="integer", nullable=false)
     */
    private $mail = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="tab", type="integer", nullable=false)
     */
    private $tab;

    /**
     * @var integer
     *
     * @ORM\Column(name="bloc", type="integer", nullable=false)
     */
    private $bloc;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

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
     * Set champ
     *
     * @param string $champ
     *
     * @return LogInfoperdos
     */
    public function setChamp($champ)
    {
        $this->champ = $champ;

        return $this;
    }

    /**
     * Get champ
     *
     * @return string
     */
    public function getChamp()
    {
        return $this->champ;
    }

    /**
     * Set valeurAncien
     *
     * @param string $valeurAncien
     *
     * @return LogInfoperdos
     */
    public function setValeurAncien($valeurAncien)
    {
        $this->valeurAncien = $valeurAncien;

        return $this;
    }

    /**
     * Get valeurAncien
     *
     * @return string
     */
    public function getValeurAncien()
    {
        return $this->valeurAncien;
    }

    /**
     * Set valeurNouveau
     *
     * @param string $valeurNouveau
     *
     * @return LogInfoperdos
     */
    public function setValeurNouveau($valeurNouveau)
    {
        $this->valeurNouveau = $valeurNouveau;

        return $this;
    }

    /**
     * Get valeurNouveau
     *
     * @return string
     */
    public function getValeurNouveau()
    {
        return $this->valeurNouveau;
    }

    /**
     * Set mail
     *
     * @param integer $mail
     *
     * @return LogInfoperdos
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return integer
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return LogInfoperdos
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
     * Set tab
     *
     * @param integer $tab
     *
     * @return LogInfoperdos
     */
    public function setTab($tab)
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * Get tab
     *
     * @return integer
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Set bloc
     *
     * @param integer $bloc
     *
     * @return LogInfoperdos
     */
    public function setBloc($bloc)
    {
        $this->bloc = $bloc;

        return $this;
    }

    /**
     * Get bloc
     *
     * @return integer
     */
    public function getBloc()
    {
        return $this->bloc;
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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return LogInfoperdos
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return LogInfoperdos
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
