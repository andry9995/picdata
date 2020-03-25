<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationPm
 *
 * @ORM\Table(name="notification_pm", indexes={@ORM\Index(name="fk_notification_pm_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationPmRepository")
 */
class NotificationPm
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom_contact", type="string", length=250, nullable=true)
     */
    private $nomContact;

    /**
     * @var integer
     *
     * @ORM\Column(name="titre_contact", type="integer", nullable=false)
     */
    private $titreContact = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="destinataire", type="text", nullable=true)
     */
    private $destinataire;

    /**
     * @var string
     *
     * @ORM\Column(name="copie", type="text", nullable=true)
     */
    private $copie;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", nullable=true)
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="text", nullable=true)
     */
    private $objet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dernier_envoi", type="date", nullable=true)
     */
    private $dernierEnvoi;

    /**
     * @var string
     *
     * @ORM\Column(name="param_envoi_auto", type="string", length=100, nullable=true)
     */
    private $paramEnvoiAuto;

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
     * Set nomContact
     *
     * @param string $nomContact
     *
     * @return NotificationPm
     */
    public function setNomContact($nomContact)
    {
        $this->nomContact = $nomContact;

        return $this;
    }

    /**
     * Get nomContact
     *
     * @return string
     */
    public function getNomContact()
    {
        return $this->nomContact;
    }

    /**
     * Set titreContact
     *
     * @param integer $titreContact
     *
     * @return NotificationPm
     */
    public function setTitreContact($titreContact)
    {
        $this->titreContact = $titreContact;

        return $this;
    }

    /**
     * Get titreContact
     *
     * @return integer
     */
    public function getTitreContact()
    {
        return $this->titreContact;
    }

    /**
     * Set destinataire
     *
     * @param string $destinataire
     *
     * @return NotificationPm
     */
    public function setDestinataire($destinataire)
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    /**
     * Get destinataire
     *
     * @return string
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * Set copie
     *
     * @param string $copie
     *
     * @return NotificationPm
     */
    public function setCopie($copie)
    {
        $this->copie = $copie;

        return $this;
    }

    /**
     * Get copie
     *
     * @return string
     */
    public function getCopie()
    {
        return $this->copie;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return NotificationPm
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
     * Set objet
     *
     * @param string $objet
     *
     * @return NotificationPm
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set dernierEnvoi
     *
     * @param \DateTime $dernierEnvoi
     *
     * @return NotificationPm
     */
    public function setDernierEnvoi($dernierEnvoi)
    {
        $this->dernierEnvoi = $dernierEnvoi;

        return $this;
    }

    /**
     * Get dernierEnvoi
     *
     * @return \DateTime
     */
    public function getDernierEnvoi()
    {
        return $this->dernierEnvoi;
    }

    /**
     * Set paramEnvoiAuto
     *
     * @param string $paramEnvoiAuto
     *
     * @return NotificationPm
     */
    public function setParamEnvoiAuto($paramEnvoiAuto)
    {
        $this->paramEnvoiAuto = $paramEnvoiAuto;

        return $this;
    }

    /**
     * Get paramEnvoiAuto
     *
     * @return string
     */
    public function getParamEnvoiAuto()
    {
        return $this->paramEnvoiAuto;
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
     * @return NotificationPm
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
