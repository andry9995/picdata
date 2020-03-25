<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfChampCategorieDossier
 *
 * @ORM\Table(name="ndf_champ_categorie_dossier", indexes={@ORM\Index(name="fk_ndf_champ_categorie_dossier_ndf_cat_doss1_idx", columns={"ndf_categorie_dossier_id"})})
 * @ORM\Entity
 */
class NdfChampCategorieDossier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="titre", type="integer", nullable=false)
     */
    private $titre = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="image", type="integer", nullable=false)
     */
    private $image = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="mode_reglement", type="integer", nullable=false)
     */
    private $modeReglement = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=false)
     */
    private $tva = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="description", type="integer", nullable=false)
     */
    private $description = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="marchand", type="integer", nullable=false)
     */
    private $marchand = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfCategorieDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfCategorieDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_categorie_dossier_id", referencedColumnName="id")
     * })
     */
    private $ndfCategorieDossier;



    /**
     * Set titre
     *
     * @param integer $titre
     *
     * @return NdfChampCategorieDossier
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return integer
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set image
     *
     * @param integer $image
     *
     * @return NdfChampCategorieDossier
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return integer
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set modeReglement
     *
     * @param integer $modeReglement
     *
     * @return NdfChampCategorieDossier
     */
    public function setModeReglement($modeReglement)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return integer
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set tva
     *
     * @param integer $tva
     *
     * @return NdfChampCategorieDossier
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return integer
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set description
     *
     * @param integer $description
     *
     * @return NdfChampCategorieDossier
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return integer
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set marchand
     *
     * @param integer $marchand
     *
     * @return NdfChampCategorieDossier
     */
    public function setMarchand($marchand)
    {
        $this->marchand = $marchand;

        return $this;
    }

    /**
     * Get marchand
     *
     * @return integer
     */
    public function getMarchand()
    {
        return $this->marchand;
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
     * Set ndfCategorieDossier
     *
     * @param \AppBundle\Entity\NdfCategorieDossier $ndfCategorieDossier
     *
     * @return NdfChampCategorieDossier
     */
    public function setNdfCategorieDossier(\AppBundle\Entity\NdfCategorieDossier $ndfCategorieDossier = null)
    {
        $this->ndfCategorieDossier = $ndfCategorieDossier;

        return $this;
    }

    /**
     * Get ndfCategorieDossier
     *
     * @return \AppBundle\Entity\NdfCategorieDossier
     */
    public function getNdfCategorieDossier()
    {
        return $this->ndfCategorieDossier;
    }
}
