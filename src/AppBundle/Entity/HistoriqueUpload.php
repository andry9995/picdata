<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoriqueUpload
 *
 * @ORM\Table(name="historique_upload", indexes={@ORM\Index(name="fk_historique_upload_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="index_historique_upload_dossier_exercice", columns={"dossier_id", "exercice"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoriqueUploadRepository")
 */
class HistoriqueUpload
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upload", type="datetime", nullable=false)
     */
    private $dateUpload;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_verification", type="datetime", nullable=true)
     */
    private $dateVerification;

    /**
     * @var integer
     *
     * @ORM\Column(name="cloture", type="integer", nullable=false)
     */
    private $cloture = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="resultat", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $resultat = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice = '0';

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
     * Set dateUpload
     *
     * @param \DateTime $dateUpload
     *
     * @return HistoriqueUpload
     */
    public function setDateUpload($dateUpload)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * Get dateUpload
     *
     * @return \DateTime
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return HistoriqueUpload
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateVerification
     *
     * @param \DateTime $dateVerification
     *
     * @return HistoriqueUpload
     */
    public function setDateVerification($dateVerification)
    {
        $this->dateVerification = $dateVerification;

        return $this;
    }

    /**
     * Get dateVerification
     *
     * @return \DateTime
     */
    public function getDateVerification()
    {
        return $this->dateVerification;
    }

    /**
     * Set cloture
     *
     * @param integer $cloture
     *
     * @return HistoriqueUpload
     */
    public function setCloture($cloture)
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * Get cloture
     *
     * @return integer
     */
    public function getCloture()
    {
        return $this->cloture;
    }

    /**
     * Set resultat
     *
     * @param string $resultat
     *
     * @return HistoriqueUpload
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;

        return $this;
    }

    /**
     * Get resultat
     *
     * @return string
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return HistoriqueUpload
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
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
     * @return HistoriqueUpload
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
