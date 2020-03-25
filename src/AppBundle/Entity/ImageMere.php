<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageMere
 *
 * @ORM\Table(name="image_mere", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_image_mere_nom_dossier_id", columns={"nom", "dossier_id"})}, indexes={@ORM\Index(name="fk_image_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class ImageMere
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom = '';

    /**
     * @var string
     *
     * @ORM\Column(name="originale", type="string", length=150, nullable=false)
     */
    private $originale = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datescan", type="date", nullable=false)
     */
    private $datescan;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="download", type="integer", nullable=false)
     */
    private $download = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=45, nullable=false)
     */
    private $source = 'site';

    /**
     * @var integer
     *
     * @ORM\Column(name="valider", type="integer", nullable=true)
     */
    private $valider = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lot", type="integer", nullable=false)
     */
    private $lot = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="decouper", type="integer", nullable=false)
     */
    private $decouper = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ext_image", type="string", length=5, nullable=false)
     */
    private $extImage = 'pdf';

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
     * Set nom
     *
     * @param string $nom
     *
     * @return ImageMere
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set originale
     *
     * @param string $originale
     *
     * @return ImageMere
     */
    public function setOriginale($originale)
    {
        $this->originale = $originale;

        return $this;
    }

    /**
     * Get originale
     *
     * @return string
     */
    public function getOriginale()
    {
        return $this->originale;
    }

    /**
     * Set datescan
     *
     * @param \DateTime $datescan
     *
     * @return ImageMere
     */
    public function setDatescan($datescan)
    {
        $this->datescan = $datescan;

        return $this;
    }

    /**
     * Get datescan
     *
     * @return \DateTime
     */
    public function getDatescan()
    {
        return $this->datescan;
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return ImageMere
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
     * Set download
     *
     * @param integer $download
     *
     * @return ImageMere
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * Get download
     *
     * @return integer
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return ImageMere
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set valider
     *
     * @param integer $valider
     *
     * @return ImageMere
     */
    public function setValider($valider)
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider
     *
     * @return integer
     */
    public function getValider()
    {
        return $this->valider;
    }

    /**
     * Set lot
     *
     * @param integer $lot
     *
     * @return ImageMere
     */
    public function setLot($lot)
    {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return integer
     */
    public function getLot()
    {
        return $this->lot;
    }

    /**
     * Set decouper
     *
     * @param integer $decouper
     *
     * @return ImageMere
     */
    public function setDecouper($decouper)
    {
        $this->decouper = $decouper;

        return $this;
    }

    /**
     * Get decouper
     *
     * @return integer
     */
    public function getDecouper()
    {
        return $this->decouper;
    }

    /**
     * Set extImage
     *
     * @param string $extImage
     *
     * @return ImageMere
     */
    public function setExtImage($extImage)
    {
        $this->extImage = $extImage;

        return $this;
    }

    /**
     * Get extImage
     *
     * @return string
     */
    public function getExtImage()
    {
        return $this->extImage;
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
     * @return ImageMere
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
