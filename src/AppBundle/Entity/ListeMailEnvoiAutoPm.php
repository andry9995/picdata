<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ListeMailEnvoiAutoPm
 *
 * @ORM\Table(name="liste_mail_envoi_auto_pm", indexes={@ORM\Index(name="fk_liste_mail_dossier_id_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class ListeMailEnvoiAutoPm
{
    /**
     * @var string
     *
     * @ORM\Column(name="tache", type="string", length=45, nullable=false)
     */
    private $tache;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="terminer", type="integer", nullable=true)
     */
    private $terminer;

    /**
     * @var integer
     *
     * @ORM\Column(name="recurrence", type="integer", nullable=true)
     */
    private $recurrence;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="type_notif", type="string", length=45, nullable=true)
     */
    private $typeNotif;

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
     * Set tache
     *
     * @param string $tache
     *
     * @return ListeMailEnvoiAutoPm
     */
    public function setTache($tache)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return string
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ListeMailEnvoiAutoPm
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
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return ListeMailEnvoiAutoPm
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set terminer
     *
     * @param integer $terminer
     *
     * @return ListeMailEnvoiAutoPm
     */
    public function setTerminer($terminer)
    {
        $this->terminer = $terminer;

        return $this;
    }

    /**
     * Get terminer
     *
     * @return integer
     */
    public function getTerminer()
    {
        return $this->terminer;
    }

    /**
     * Set recurrence
     *
     * @param integer $recurrence
     *
     * @return ListeMailEnvoiAutoPm
     */
    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * Get recurrence
     *
     * @return integer
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return ListeMailEnvoiAutoPm
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
     * Set typeNotif
     *
     * @param string $typeNotif
     *
     * @return ListeMailEnvoiAutoPm
     */
    public function setTypeNotif($typeNotif)
    {
        $this->typeNotif = $typeNotif;

        return $this;
    }

    /**
     * Get typeNotif
     *
     * @return string
     */
    public function getTypeNotif()
    {
        return $this->typeNotif;
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
     * @return ListeMailEnvoiAutoPm
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
