<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfDepense
 *
 * @ORM\Table(name="ndf_depense", indexes={@ORM\Index(name="ndf_depense_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_depense_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_ndf_depense_pays_1_idx", columns={"pays_id"}), @ORM\Index(name="fk_ndf_depense_devise_1_idx", columns={"devise_id"}), @ORM\Index(name="fk_ndf_depense_ndf_categorie1_idx", columns={"ndf_categorie_dossier_id"}), @ORM\Index(name="fk_ndf_depense_ndf_note_1_idx", columns={"ndf_note_id"}), @ORM\Index(name="fk_ndf_depense_affaire_1_idx", columns={"ndf_affaire_id"}), @ORM\Index(name="fk_ndf_depense_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_ndf_depense_ndf_depense_souscategorie1_idx", columns={"ndf_souscategorie_dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NdfDepenseRepository")
 */
class NdfDepense
{
    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=45, nullable=true)
     */
    private $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_reglement", type="integer", nullable=true)
     */
    private $typeReglement;

    /**
     * @var float
     *
     * @ORM\Column(name="ttc", type="float", precision=10, scale=0, nullable=true)
     */
    private $ttc;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="remboursable", type="integer", nullable=true)
     */
    private $remboursable = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="facturable", type="integer", nullable=true)
     */
    private $facturable = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pj", type="integer", nullable=true)
     */
    private $pj = '1';

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
     * @var \AppBundle\Entity\NdfNote
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfNote")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_note_id", referencedColumnName="id")
     * })
     */
    private $ndfNote;

    /**
     * @var \AppBundle\Entity\Pays
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays_id", referencedColumnName="id")
     * })
     */
    private $pays;

    /**
     * @var \AppBundle\Entity\NdfAffaire
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfAffaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_affaire_id", referencedColumnName="id")
     * })
     */
    private $ndfAffaire;

    /**
     * @var \AppBundle\Entity\ModeReglement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModeReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mode_reglement_id", referencedColumnName="id")
     * })
     */
    private $modeReglement;

    /**
     * @var \AppBundle\Entity\Devise
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Devise")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devise_id", referencedColumnName="id")
     * })
     */
    private $devise;

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
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    /**
     * @var \AppBundle\Entity\NdfSouscategorieDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfSouscategorieDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_souscategorie_dossier_id", referencedColumnName="id")
     * })
     */
    private $ndfSouscategorieDossier;



    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return NdfDepense
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return NdfDepense
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
     * Set typeReglement
     *
     * @param integer $typeReglement
     *
     * @return NdfDepense
     */
    public function setTypeReglement($typeReglement)
    {
        $this->typeReglement = $typeReglement;

        return $this;
    }

    /**
     * Get typeReglement
     *
     * @return integer
     */
    public function getTypeReglement()
    {
        return $this->typeReglement;
    }

    /**
     * Set ttc
     *
     * @param float $ttc
     *
     * @return NdfDepense
     */
    public function setTtc($ttc)
    {
        $this->ttc = $ttc;

        return $this;
    }

    /**
     * Get ttc
     *
     * @return float
     */
    public function getTtc()
    {
        return $this->ttc;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NdfDepense
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set remboursable
     *
     * @param integer $remboursable
     *
     * @return NdfDepense
     */
    public function setRemboursable($remboursable)
    {
        $this->remboursable = $remboursable;

        return $this;
    }

    /**
     * Get remboursable
     *
     * @return integer
     */
    public function getRemboursable()
    {
        return $this->remboursable;
    }

    /**
     * Set facturable
     *
     * @param integer $facturable
     *
     * @return NdfDepense
     */
    public function setFacturable($facturable)
    {
        $this->facturable = $facturable;

        return $this;
    }

    /**
     * Get facturable
     *
     * @return integer
     */
    public function getFacturable()
    {
        return $this->facturable;
    }

    /**
     * Set pj
     *
     * @param integer $pj
     *
     * @return NdfDepense
     */
    public function setPj($pj)
    {
        $this->pj = $pj;

        return $this;
    }

    /**
     * Get pj
     *
     * @return integer
     */
    public function getPj()
    {
        return $this->pj;
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
     * @return NdfDepense
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

    /**
     * Set ndfNote
     *
     * @param \AppBundle\Entity\NdfNote $ndfNote
     *
     * @return NdfDepense
     */
    public function setNdfNote(\AppBundle\Entity\NdfNote $ndfNote = null)
    {
        $this->ndfNote = $ndfNote;

        return $this;
    }

    /**
     * Get ndfNote
     *
     * @return \AppBundle\Entity\NdfNote
     */
    public function getNdfNote()
    {
        return $this->ndfNote;
    }

    /**
     * Set pays
     *
     * @param \AppBundle\Entity\Pays $pays
     *
     * @return NdfDepense
     */
    public function setPays(\AppBundle\Entity\Pays $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \AppBundle\Entity\Pays
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set ndfAffaire
     *
     * @param \AppBundle\Entity\NdfAffaire $ndfAffaire
     *
     * @return NdfDepense
     */
    public function setNdfAffaire(\AppBundle\Entity\NdfAffaire $ndfAffaire = null)
    {
        $this->ndfAffaire = $ndfAffaire;

        return $this;
    }

    /**
     * Get ndfAffaire
     *
     * @return \AppBundle\Entity\NdfAffaire
     */
    public function getNdfAffaire()
    {
        return $this->ndfAffaire;
    }

    /**
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return NdfDepense
     */
    public function setModeReglement(\AppBundle\Entity\ModeReglement $modeReglement = null)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return \AppBundle\Entity\ModeReglement
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set devise
     *
     * @param \AppBundle\Entity\Devise $devise
     *
     * @return NdfDepense
     */
    public function setDevise(\AppBundle\Entity\Devise $devise = null)
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return \AppBundle\Entity\Devise
     */
    public function getDevise()
    {
        return $this->devise;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfDepense
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return NdfDepense
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
     * Set ndfSouscategorieDossier
     *
     * @param \AppBundle\Entity\NdfSouscategorieDossier $ndfSouscategorieDossier
     *
     * @return NdfDepense
     */
    public function setNdfSouscategorieDossier(\AppBundle\Entity\NdfSouscategorieDossier $ndfSouscategorieDossier = null)
    {
        $this->ndfSouscategorieDossier = $ndfSouscategorieDossier;

        return $this;
    }

    /**
     * Get ndfSouscategorieDossier
     *
     * @return \AppBundle\Entity\NdfSouscategorieDossier
     */
    public function getNdfSouscategorieDossier()
    {
        return $this->ndfSouscategorieDossier;
    }
}
