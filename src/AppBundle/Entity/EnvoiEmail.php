<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnvoiEmail
 *
 * @ORM\Table(name="envoi_email", indexes={@ORM\Index(name="fk_envoi_email_tache_dossier1_idx", columns={"tache_dossier_id"})})
 * @ORM\Entity
 */
class EnvoiEmail
{
    /**
     * @var string
     *
     * @ORM\Column(name="destinataire", type="text", length=65535, nullable=false)
     */
    private $destinataire;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=false)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="recurrent", type="integer", nullable=false)
     */
    private $recurrent = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="periode", type="integer", nullable=true)
     */
    private $periode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut_envoi", type="date", nullable=false)
     */
    private $debutEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dernier_envoi", type="date", nullable=true)
     */
    private $dernierEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prochain_envoi", type="date", nullable=false)
     */
    private $prochainEnvoi;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_contact", type="string", length=250, nullable=false)
     */
    private $nomContact;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TacheDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TacheDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_dossier_id", referencedColumnName="id")
     * })
     */
    private $tacheDossier;



    /**
     * Set destinataire
     *
     * @param string $destinataire
     *
     * @return EnvoiEmail
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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return EnvoiEmail
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
     * Set recurrent
     *
     * @param integer $recurrent
     *
     * @return EnvoiEmail
     */
    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;

        return $this;
    }

    /**
     * Get recurrent
     *
     * @return integer
     */
    public function getRecurrent()
    {
        return $this->recurrent;
    }

    /**
     * Set periode
     *
     * @param integer $periode
     *
     * @return EnvoiEmail
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return integer
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
     * @return EnvoiEmail
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
     * Set dernierEnvoi
     *
     * @param \DateTime $dernierEnvoi
     *
     * @return EnvoiEmail
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
     * Set prochainEnvoi
     *
     * @param \DateTime $prochainEnvoi
     *
     * @return EnvoiEmail
     */
    public function setProchainEnvoi($prochainEnvoi)
    {
        $this->prochainEnvoi = $prochainEnvoi;

        return $this;
    }

    /**
     * Get prochainEnvoi
     *
     * @return \DateTime
     */
    public function getProchainEnvoi()
    {
        return $this->prochainEnvoi;
    }

    /**
     * Set nomContact
     *
     * @param string $nomContact
     *
     * @return EnvoiEmail
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tacheDossier
     *
     * @param \AppBundle\Entity\TacheDossier $tacheDossier
     *
     * @return EnvoiEmail
     */
    public function setTacheDossier(\AppBundle\Entity\TacheDossier $tacheDossier = null)
    {
        $this->tacheDossier = $tacheDossier;

        return $this;
    }

    /**
     * Get tacheDossier
     *
     * @return \AppBundle\Entity\TacheDossier
     */
    public function getTacheDossier()
    {
        return $this->tacheDossier;
    }
}
