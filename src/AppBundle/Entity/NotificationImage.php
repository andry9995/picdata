<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationImage
 *
 * @ORM\Table(name="notification_image", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE_dossier", columns={"dossier_id"})}, indexes={@ORM\Index(name="fk_param_email_image_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationImageRepository")
 */
class NotificationImage
{
    /**
     * @var string
     *
     * @ORM\Column(name="destinataire", type="text", length=65535, nullable=true)
     */
    private $destinataire;

    /**
     * @var string
     *
     * @ORM\Column(name="copie", type="text", length=65535, nullable=true)
     */
    private $copie;


    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="text", length=65535, nullable=true)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var string
     *
     * @ORM\Column(name="periode", type="string", length=5, nullable=false)
     */
    private $periode = 'M';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut_envoi", type="date", nullable=true)
     */
    private $debutEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dernier_envoi_n", type="date", nullable=true)
     */
    private $dernierEnvoiN;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prochain_envoi_n", type="date", nullable=true)
     */
    private $prochainEnvoiN;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dernier_envoi_n_1", type="date", nullable=true)
     */
    private $dernierEnvoiN1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prochain_envoi_n_1", type="date", nullable=true)
     */
    private $prochainEnvoiN1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="envoi_n", type="boolean", nullable=false)
     */
    private $envoiN = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="envoi_n_1", type="boolean", nullable=false)
     */
    private $envoiN1 = '0';

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
     * Set destinataire
     *
     * @param string $destinataire
     *
     * @return NotificationImage
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
     * @return NotificationImage
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
     * Set objet
     *
     * @param string $objet
     *
     * @return NotificationImage
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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return NotificationImage
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
     * Set periode
     *
     * @param string $periode
     *
     * @return NotificationImage
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return string
     */
    public function getPeriode()
    {
        return $this->periode;
    }

    /**
     * Set debutEnvoi
     *
     * @param \DateTime $debutEnvoi
     *
     * @return NotificationImage
     */
    public function setDebutEnvoi($debutEnvoi)
    {
        $this->debutEnvoi = $debutEnvoi;

        return $this;
    }

    /**
     * Get debutEnvoi
     *
     * @return \DateTime
     */
    public function getDebutEnvoi()
    {
        return $this->debutEnvoi;
    }

    /**
     * Set dernierEnvoiN
     *
     * @param \DateTime $dernierEnvoiN
     *
     * @return NotificationImage
     */
    public function setDernierEnvoiN($dernierEnvoiN)
    {
        $this->dernierEnvoiN = $dernierEnvoiN;

        return $this;
    }

    /**
     * Get dernierEnvoiN
     *
     * @return \DateTime
     */
    public function getDernierEnvoiN()
    {
        return $this->dernierEnvoiN;
    }

    /**
     * Set prochainEnvoiN
     *
     * @param \DateTime $prochainEnvoiN
     *
     * @return NotificationImage
     */
    public function setProchainEnvoiN($prochainEnvoiN)
    {
        $this->prochainEnvoiN = $prochainEnvoiN;

        return $this;
    }

    /**
     * Get prochainEnvoiN
     *
     * @return \DateTime
     */
    public function getProchainEnvoiN()
    {
        return $this->prochainEnvoiN;
    }

    /**
     * Set dernierEnvoiN1
     *
     * @param \DateTime $dernierEnvoiN1
     *
     * @return NotificationImage
     */
    public function setDernierEnvoiN1($dernierEnvoiN1)
    {
        $this->dernierEnvoiN1 = $dernierEnvoiN1;

        return $this;
    }

    /**
     * Get dernierEnvoiN1
     *
     * @return \DateTime
     */
    public function getDernierEnvoiN1()
    {
        return $this->dernierEnvoiN1;
    }

    /**
     * Set prochainEnvoiN1
     *
     * @param \DateTime $prochainEnvoiN1
     *
     * @return NotificationImage
     */
    public function setProchainEnvoiN1($prochainEnvoiN1)
    {
        $this->prochainEnvoiN1 = $prochainEnvoiN1;

        return $this;
    }

    /**
     * Get prochainEnvoiN1
     *
     * @return \DateTime
     */
    public function getProchainEnvoiN1()
    {
        return $this->prochainEnvoiN1;
    }

    /**
     * Set envoiN
     *
     * @param boolean $envoiN
     *
     * @return NotificationImage
     */
    public function setEnvoiN($envoiN)
    {
        $this->envoiN = $envoiN;

        return $this;
    }

    /**
     * Get envoiN
     *
     * @return boolean
     */
    public function getEnvoiN()
    {
        return $this->envoiN;
    }

    /**
     * Set envoiN1
     *
     * @param boolean $envoiN1
     *
     * @return NotificationImage
     */
    public function setEnvoiN1($envoiN1)
    {
        $this->envoiN1 = $envoiN1;

        return $this;
    }

    /**
     * Get envoiN1
     *
     * @return boolean
     */
    public function getEnvoiN1()
    {
        return $this->envoiN1;
    }

    /**
     * Set nomContact
     *
     * @param string $nomContact
     *
     * @return NotificationImage
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
     * @return NotificationImage
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
     * @return NotificationImage
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
