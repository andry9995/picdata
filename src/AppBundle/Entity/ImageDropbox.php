<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageDropbox
 *
 * @ORM\Table(name="image_dropbox", indexes={@ORM\Index(name="fk_image_dropbox_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_image_dropbox_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_image_dropbox_image1_idx", columns={"image_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageDropboxRepository")
 */
class ImageDropbox
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_scan", type="date", nullable=false)
     */
    private $dateScan;

    /**
     * @var string
     *
     * @ORM\Column(name="original", type="string", length=255, nullable=false)
     */
    private $original;

    /**
     * @var string
     *
     * @ORM\Column(name="path_dropbox", type="string", length=255, nullable=false)
     */
    private $pathDropbox;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_traitement", type="date", nullable=true)
     */
    private $dateTraitement;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_tmp", type="string", length=255, nullable=true)
     */
    private $nomTmp;

    /**
     * @var string
     *
     * @ORM\Column(name="dossier_tmp", type="string", length=255, nullable=true)
     */
    private $dossierTmp;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return ImageDropbox
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
     * Set dateScan
     *
     * @param \DateTime $dateScan
     *
     * @return ImageDropbox
     */
    public function setDateScan($dateScan)
    {
        $this->dateScan = $dateScan;

        return $this;
    }

    /**
     * Get dateScan
     *
     * @return \DateTime
     */
    public function getDateScan()
    {
        return $this->dateScan;
    }

    /**
     * Set original
     *
     * @param string $original
     *
     * @return ImageDropbox
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set pathDropbox
     *
     * @param string $pathDropbox
     *
     * @return ImageDropbox
     */
    public function setPathDropbox($pathDropbox)
    {
        $this->pathDropbox = $pathDropbox;

        return $this;
    }

    /**
     * Get pathDropbox
     *
     * @return string
     */
    public function getPathDropbox()
    {
        return $this->pathDropbox;
    }

    /**
     * Set dateTraitement
     *
     * @param \DateTime $dateTraitement
     *
     * @return ImageDropbox
     */
    public function setDateTraitement($dateTraitement)
    {
        $this->dateTraitement = $dateTraitement;

        return $this;
    }

    /**
     * Get dateTraitement
     *
     * @return \DateTime
     */
    public function getDateTraitement()
    {
        return $this->dateTraitement;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ImageDropbox
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
     * Set nomTmp
     *
     * @param string $nomTmp
     *
     * @return ImageDropbox
     */
    public function setNomTmp($nomTmp)
    {
        $this->nomTmp = $nomTmp;

        return $this;
    }

    /**
     * Get nomTmp
     *
     * @return string
     */
    public function getNomTmp()
    {
        return $this->nomTmp;
    }


    /**
     * @param $dossierTmp
     * @return $this
     */
    public function setDossierTmp($dossierTmp)
    {
        $this->dossierTmp = $dossierTmp;

        return $this;
    }

    /**
     * @return string
     */
    public function getDossierTmp()
    {
        return $this->dossierTmp;
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
     * @return ImageDropbox
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return ImageDropbox
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ImageDropbox
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
