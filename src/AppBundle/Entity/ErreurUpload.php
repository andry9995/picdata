<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErreurUpload
 *
 * @ORM\Table(name="erreur_upload", indexes={@ORM\Index(name="fk_erreur_upload_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class ErreurUpload
{
    /**
     * @var string
     *
     * @ORM\Column(name="erreur", type="string", length=250, nullable=false)
     */
    private $erreur;

    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="string", length=250, nullable=false)
     */
    private $fichier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_erreur", type="datetime", nullable=false)
     */
    private $dateErreur;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_dossier", type="string", length=250, nullable=true)
     */
    private $nomDossier;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type = '4';

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
     * Set erreur
     *
     * @param string $erreur
     *
     * @return ErreurUpload
     */
    public function setErreur($erreur)
    {
        $this->erreur = $erreur;

        return $this;
    }

    /**
     * Get erreur
     *
     * @return string
     */
    public function getErreur()
    {
        return $this->erreur;
    }

    /**
     * Set fichier
     *
     * @param string $fichier
     *
     * @return ErreurUpload
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * Set dateErreur
     *
     * @param \DateTime $dateErreur
     *
     * @return ErreurUpload
     */
    public function setDateErreur($dateErreur)
    {
        $this->dateErreur = $dateErreur;

        return $this;
    }

    /**
     * Get dateErreur
     *
     * @return \DateTime
     */
    public function getDateErreur()
    {
        return $this->dateErreur;
    }

    /**
     * Set nomDossier
     *
     * @param string $nomDossier
     *
     * @return ErreurUpload
     */
    public function setNomDossier($nomDossier)
    {
        $this->nomDossier = $nomDossier;

        return $this;
    }

    /**
     * Get nomDossier
     *
     * @return string
     */
    public function getNomDossier()
    {
        return $this->nomDossier;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return ErreurUpload
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
     * @return ErreurUpload
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
