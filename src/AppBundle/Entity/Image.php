<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_image_nom_page", columns={"nom", "num_page", "lot_id"})}, indexes={@ORM\Index(name="fk_image_source_image1_idx", columns={"source_image_id"}), @ORM\Index(name="fk_image_lot1_idx", columns={"lot_id"}), @ORM\Index(name="fk_image_code_analytique1_idx", columns={"code_analytique_id"}), @ORM\Index(name="index_nom", columns={"nom"}), @ORM\Index(name="index_nomtemp", columns={"nom_temp"}), @ORM\Index(name="fk_image_mode_reglement1_idx", columns={"mode_reglement_id"}), @ORM\Index(name="fk_image_fusion_id_idx", columns={"image_id_fusion"}), @ORM\Index(name="index_image_originale", columns={"originale"}), @ORM\Index(name="fk_image_image_flague_idx", columns={"image_flague_id"}), @ORM\Index(name="fk_image_commentaire_dossier_idx", columns={"commentaire_dossier_id"}), @ORM\Index(name="index_download", columns={"download"}), @ORM\Index(name="index_supprimer", columns={"supprimer"}), @ORM\Index(name="fk_image_echange_reponse1_idx", columns={"echange_reponse_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image
{
	
//    //====== AJOUT MANUEL ==============
//
//    /**
//     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SaisieControle", mappedBy="image")
//     */
//    private $saisieControles;
//
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ControleNoteFrais", mappedBy="image")
     */
    private $controleNoteFrais;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ControleVenteComptoir", mappedBy="image")
     */
    private $controleVenteComptoirs;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ControleCaisse", mappedBy="image")
     */
    private $controleCaisses;

    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->saisieControles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->controleNoteFrais = new \Doctrine\Common\Collections\ArrayCollection();
        $this->controleVenteComptoirs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->controleCaisses = new \Doctrine\Common\Collections\ArrayCollection();
    }
//
//    /**
//     * Add saisieControle
//     *
//     * @param \AppBundle\Entity\SaisieControle $saisieControle
//     *
//     * @return Image
//     */
//    public function addSaisieControle(\AppBundle\Entity\SaisieControle $saisieControle)
//    {
//        $this->saisieControles[] = $saisieControle;
//
//        return $this;
//    }
//
//    /**
//     * Remove saisieControle
//     *
//     * @param \AppBundle\Entity\SaisieControle $saisieControle
//     */
//    public function removeSaisieControle(\AppBundle\Entity\SaisieControle $saisieControle)
//    {
//        $this->saisieControles->removeElement($saisieControle);
//    }
//
//    /**
//     * Get saisieControles
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getSaisieControles()
//    {
//        return $this->saisieControles;
//    }
//
    /**
     * Add controleNoteFrai
     *
     * @param \AppBundle\Entity\ControleNoteFrais $controleNoteFrai
     *
     * @return Image
     */
    public function addControleNoteFrais(\AppBundle\Entity\ControleNoteFrais $controleNoteFrai)
    {
        $this->controleNoteFrais[] = $controleNoteFrai;

        return $this;
    }

    /**
     * Remove controleNoteFrai
     *
     * @param \AppBundle\Entity\ControleNoteFrais $controleNoteFrai
     */
    public function removeControleNoteFrais(\AppBundle\Entity\ControleNoteFrais $controleNoteFrai)
    {
        $this->controleNoteFrais->removeElement($controleNoteFrai);
    }

    /**
     * Get controleNoteFrais
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControleNoteFrais()
    {
        return $this->controleNoteFrais;
    }

    /**
     * Add controleVenteComptoir
     *
     * @param \AppBundle\Entity\ControleVenteComptoir $controleVenteComptoir
     *
     * @return Image
     */
    public function addControleVenteComptoir(\AppBundle\Entity\ControleVenteComptoir $controleVenteComptoir)
    {
        $this->controleVenteComptoirs[] = $controleVenteComptoir;

        return $this;
    }

    /**
     * Remove controleVenteComptoir
     *
     * @param \AppBundle\Entity\ControleVenteComptoir $controleVenteComptoir
     */
    public function removeControleVenteComptoir(\AppBundle\Entity\ControleVenteComptoir $controleVenteComptoir)
    {
        $this->controleVenteComptoirs->removeElement($controleVenteComptoir);
    }

    /**
     * Get controleVenteComptoirs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControleVenteComptoirs()
    {
        return $this->controleVenteComptoirs;
    }

    /**
     * Add controleCaiss
     *
     * @param \AppBundle\Entity\ControleCaisse $controleCaiss
     *
     * @return Image
     */
    public function addControleCaisse(\AppBundle\Entity\ControleCaisse $controleCaiss)
    {
        $this->controleCaisses[] = $controleCaiss;

        return $this;
    }

    /**
     * Remove controleCaiss
     *
     * @param \AppBundle\Entity\ControleCaisse $controleCaiss
     */
    public function removeControleCaisse(\AppBundle\Entity\ControleCaisse $controleCaiss)
    {
        $this->controleCaisses->removeElement($controleCaiss);
    }

    /**
     * Get controleCaisses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControleCaisses()
    {
        return $this->controleCaisses;
    }

    //====== FIN AJOUT MANUEL ==========	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom = '';

    /**
     * @var string
     *
     * @ORM\Column(name="originale", type="string", length=150, nullable=false)
     */
    private $originale = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="download", type="datetime", nullable=true)
     */
    private $download;

    /**
     * @var integer
     *
     * @ORM\Column(name="valider", type="integer", nullable=false)
     */
    private $valider = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ext_image", type="string", length=5, nullable=false)
     */
    private $extImage = 'pdf';

    /**
     * @var integer
     *
     * @ORM\Column(name="renommer", type="integer", nullable=false)
     */
    private $renommer = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="repertoire_local", type="string", length=250, nullable=true)
     */
    private $repertoireLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="repertoire_picdata", type="string", length=250, nullable=true)
     */
    private $repertoirePicdata;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbpage", type="integer", nullable=false)
     */
    private $nbpage = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="supprimer", type="integer", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="num_page", type="integer", nullable=false)
     */
    private $numPage = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="a_ne_pas_traiter", type="integer", nullable=false)
     */
    private $aNePasTraiter = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="a_remonter", type="integer", nullable=false)
     */
    private $aRemonter = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="decouper", type="integer", nullable=false)
     */
    private $decouper = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="nom_temp", type="string", length=100, nullable=true)
     */
    private $nomTemp;

    /**
     * @var integer
     *
     * @ORM\Column(name="numerotation_local", type="integer", nullable=false)
     */
    private $numerotationLocal = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie1", type="integer", nullable=false)
     */
    private $saisie1 = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie2", type="integer", nullable=false)
     */
    private $saisie2 = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="ctrl_saisie", type="integer", nullable=false)
     */
    private $ctrlSaisie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="imputation", type="integer", nullable=false)
     */
    private $imputation = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="ctrl_imputation", type="integer", nullable=false)
     */
    private $ctrlImputation = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="image_id_fusion", type="integer", nullable=true)
     */
    private $imageIdFusion;

    /**
     * @var integer
     *
     * @ORM\Column(name="flaguer", type="integer", nullable=false)
     */
    private $flaguer = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="sent_to_ftp", type="boolean", nullable=false)
     */
    private $sentToFtp = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="universelle", type="integer", nullable=false)
     */
    private $universelle = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="a_lettrer", type="integer", nullable=false)
     */
    private $aLettrer = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\SourceImage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SourceImage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_image_id", referencedColumnName="id")
     * })
     */
    private $sourceImage;

    /**
     * @var \AppBundle\Entity\Lot
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_id", referencedColumnName="id")
     * })
     */
    private $lot;

    /**
     * @var \AppBundle\Entity\ImageFlague
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImageFlague")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_flague_id", referencedColumnName="id")
     * })
     */
    private $imageFlague;

    /**
     * @var \AppBundle\Entity\CommentaireDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CommentaireDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commentaire_dossier_id", referencedColumnName="id")
     * })
     */
    private $commentaireDossier;

    /**
     * @var \AppBundle\Entity\EchangeReponse
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EchangeReponse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_reponse_id", referencedColumnName="id")
     * })
     */
    private $echangeReponse;

    /**
     * @var \AppBundle\Entity\CodeAnalytique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CodeAnalytique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_analytique_id", referencedColumnName="id")
     * })
     */
    private $codeAnalytique;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Image
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
     * @return Image
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
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return Image
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
     * @param \DateTime $download
     *
     * @return Image
     */
    public function setDownload($download)
    {
        $this->download = $download;

        return $this;
    }

    /**
     * Get download
     *
     * @return \DateTime
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Set valider
     *
     * @param integer $valider
     *
     * @return Image
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
     * Set extImage
     *
     * @param string $extImage
     *
     * @return Image
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
     * Set renommer
     *
     * @param integer $renommer
     *
     * @return Image
     */
    public function setRenommer($renommer)
    {
        $this->renommer = $renommer;

        return $this;
    }

    /**
     * Get renommer
     *
     * @return integer
     */
    public function getRenommer()
    {
        return $this->renommer;
    }

    /**
     * Set repertoireLocal
     *
     * @param string $repertoireLocal
     *
     * @return Image
     */
    public function setRepertoireLocal($repertoireLocal)
    {
        $this->repertoireLocal = $repertoireLocal;

        return $this;
    }

    /**
     * Get repertoireLocal
     *
     * @return string
     */
    public function getRepertoireLocal()
    {
        return $this->repertoireLocal;
    }

    /**
     * Set repertoirePicdata
     *
     * @param string $repertoirePicdata
     *
     * @return Image
     */
    public function setRepertoirePicdata($repertoirePicdata)
    {
        $this->repertoirePicdata = $repertoirePicdata;

        return $this;
    }

    /**
     * Get repertoirePicdata
     *
     * @return string
     */
    public function getRepertoirePicdata()
    {
        return $this->repertoirePicdata;
    }

    /**
     * Set nbpage
     *
     * @param integer $nbpage
     *
     * @return Image
     */
    public function setNbpage($nbpage)
    {
        $this->nbpage = $nbpage;

        return $this;
    }

    /**
     * Get nbpage
     *
     * @return integer
     */
    public function getNbpage()
    {
        return $this->nbpage;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Image
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
     * Set supprimer
     *
     * @param integer $supprimer
     *
     * @return Image
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return integer
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    /**
     * Set numPage
     *
     * @param integer $numPage
     *
     * @return Image
     */
    public function setNumPage($numPage)
    {
        $this->numPage = $numPage;

        return $this;
    }

    /**
     * Get numPage
     *
     * @return integer
     */
    public function getNumPage()
    {
        return $this->numPage;
    }

    /**
     * Set aNePasTraiter
     *
     * @param integer $aNePasTraiter
     *
     * @return Image
     */
    public function setANePasTraiter($aNePasTraiter)
    {
        $this->aNePasTraiter = $aNePasTraiter;

        return $this;
    }

    /**
     * Get aNePasTraiter
     *
     * @return integer
     */
    public function getANePasTraiter()
    {
        return $this->aNePasTraiter;
    }

    /**
     * Set aRemonter
     *
     * @param integer $aRemonter
     *
     * @return Image
     */
    public function setARemonter($aRemonter)
    {
        $this->aRemonter = $aRemonter;

        return $this;
    }

    /**
     * Get aRemonter
     *
     * @return integer
     */
    public function getARemonter()
    {
        return $this->aRemonter;
    }

    /**
     * Set decouper
     *
     * @param integer $decouper
     *
     * @return Image
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
     * Set nomTemp
     *
     * @param string $nomTemp
     *
     * @return Image
     */
    public function setNomTemp($nomTemp)
    {
        $this->nomTemp = $nomTemp;

        return $this;
    }

    /**
     * Get nomTemp
     *
     * @return string
     */
    public function getNomTemp()
    {
        return $this->nomTemp;
    }

    /**
     * Set numerotationLocal
     *
     * @param integer $numerotationLocal
     *
     * @return Image
     */
    public function setNumerotationLocal($numerotationLocal)
    {
        $this->numerotationLocal = $numerotationLocal;

        return $this;
    }

    /**
     * Get numerotationLocal
     *
     * @return integer
     */
    public function getNumerotationLocal()
    {
        return $this->numerotationLocal;
    }

    /**
     * Set saisie1
     *
     * @param integer $saisie1
     *
     * @return Image
     */
    public function setSaisie1($saisie1)
    {
        $this->saisie1 = $saisie1;

        return $this;
    }

    /**
     * Get saisie1
     *
     * @return integer
     */
    public function getSaisie1()
    {
        return $this->saisie1;
    }

    /**
     * Set saisie2
     *
     * @param integer $saisie2
     *
     * @return Image
     */
    public function setSaisie2($saisie2)
    {
        $this->saisie2 = $saisie2;

        return $this;
    }

    /**
     * Get saisie2
     *
     * @return integer
     */
    public function getSaisie2()
    {
        return $this->saisie2;
    }

    /**
     * Set ctrlSaisie
     *
     * @param integer $ctrlSaisie
     *
     * @return Image
     */
    public function setCtrlSaisie($ctrlSaisie)
    {
        $this->ctrlSaisie = $ctrlSaisie;

        return $this;
    }

    /**
     * Get ctrlSaisie
     *
     * @return integer
     */
    public function getCtrlSaisie()
    {
        return $this->ctrlSaisie;
    }

    /**
     * Set imputation
     *
     * @param integer $imputation
     *
     * @return Image
     */
    public function setImputation($imputation)
    {
        $this->imputation = $imputation;

        return $this;
    }

    /**
     * Get imputation
     *
     * @return integer
     */
    public function getImputation()
    {
        return $this->imputation;
    }

    /**
     * Set ctrlImputation
     *
     * @param integer $ctrlImputation
     *
     * @return Image
     */
    public function setCtrlImputation($ctrlImputation)
    {
        $this->ctrlImputation = $ctrlImputation;

        return $this;
    }

    /**
     * Get ctrlImputation
     *
     * @return integer
     */
    public function getCtrlImputation()
    {
        return $this->ctrlImputation;
    }

    /**
     * Set imageIdFusion
     *
     * @param integer $imageIdFusion
     *
     * @return Image
     */
    public function setImageIdFusion($imageIdFusion)
    {
        $this->imageIdFusion = $imageIdFusion;

        return $this;
    }

    /**
     * Get imageIdFusion
     *
     * @return integer
     */
    public function getImageIdFusion()
    {
        return $this->imageIdFusion;
    }

    /**
     * Set flaguer
     *
     * @param integer $flaguer
     *
     * @return Image
     */
    public function setFlaguer($flaguer)
    {
        $this->flaguer = $flaguer;

        return $this;
    }

    /**
     * Get flaguer
     *
     * @return integer
     */
    public function getFlaguer()
    {
        return $this->flaguer;
    }

    /**
     * Set sentToFtp
     *
     * @param boolean $sentToFtp
     *
     * @return Image
     */
    public function setSentToFtp($sentToFtp)
    {
        $this->sentToFtp = $sentToFtp;

        return $this;
    }

    /**
     * Get sentToFtp
     *
     * @return boolean
     */
    public function getSentToFtp()
    {
        return $this->sentToFtp;
    }

    /**
     * Set universelle
     *
     * @param integer $universelle
     *
     * @return Image
     */
    public function setUniverselle($universelle)
    {
        $this->universelle = $universelle;

        return $this;
    }

    /**
     * Get universelle
     *
     * @return integer
     */
    public function getUniverselle()
    {
        return $this->universelle;
    }

    /**
     * Set aLettrer
     *
     * @param integer $aLettrer
     *
     * @return Image
     */
    public function setALettrer($aLettrer)
    {
        $this->aLettrer = $aLettrer;

        return $this;
    }

    /**
     * Get aLettrer
     *
     * @return integer
     */
    public function getALettrer()
    {
        return $this->aLettrer;
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
     * Set modeReglement
     *
     * @param \AppBundle\Entity\ModeReglement $modeReglement
     *
     * @return Image
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
     * Set sourceImage
     *
     * @param \AppBundle\Entity\SourceImage $sourceImage
     *
     * @return Image
     */
    public function setSourceImage(\AppBundle\Entity\SourceImage $sourceImage = null)
    {
        $this->sourceImage = $sourceImage;

        return $this;
    }

    /**
     * Get sourceImage
     *
     * @return \AppBundle\Entity\SourceImage
     */
    public function getSourceImage()
    {
        return $this->sourceImage;
    }

    /**
     * Set lot
     *
     * @param \AppBundle\Entity\Lot $lot
     *
     * @return Image
     */
    public function setLot(\AppBundle\Entity\Lot $lot = null)
    {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return \AppBundle\Entity\Lot
     */
    public function getLot()
    {
        return $this->lot;
    }

    /**
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return Image
     */
    public function setImageFlague(\AppBundle\Entity\ImageFlague $imageFlague = null)
    {
        $this->imageFlague = $imageFlague;

        return $this;
    }

    /**
     * Get imageFlague
     *
     * @return \AppBundle\Entity\ImageFlague
     */
    public function getImageFlague()
    {
        return $this->imageFlague;
    }

    /**
     * Set commentaireDossier
     *
     * @param \AppBundle\Entity\CommentaireDossier $commentaireDossier
     *
     * @return Image
     */
    public function setCommentaireDossier(\AppBundle\Entity\CommentaireDossier $commentaireDossier = null)
    {
        $this->commentaireDossier = $commentaireDossier;

        return $this;
    }

    /**
     * Get commentaireDossier
     *
     * @return \AppBundle\Entity\CommentaireDossier
     */
    public function getCommentaireDossier()
    {
        return $this->commentaireDossier;
    }

    /**
     * Set echangeReponse
     *
     * @param \AppBundle\Entity\EchangeReponse $echangeReponse
     *
     * @return Image
     */
    public function setEchangeReponse(\AppBundle\Entity\EchangeReponse $echangeReponse = null)
    {
        $this->echangeReponse = $echangeReponse;

        return $this;
    }

    /**
     * Get echangeReponse
     *
     * @return \AppBundle\Entity\EchangeReponse
     */
    public function getEchangeReponse()
    {
        return $this->echangeReponse;
    }

    /**
     * Set codeAnalytique
     *
     * @param \AppBundle\Entity\CodeAnalytique $codeAnalytique
     *
     * @return Image
     */
    public function setCodeAnalytique(\AppBundle\Entity\CodeAnalytique $codeAnalytique = null)
    {
        $this->codeAnalytique = $codeAnalytique;

        return $this;
    }

    /**
     * Get codeAnalytique
     *
     * @return \AppBundle\Entity\CodeAnalytique
     */
    public function getCodeAnalytique()
    {
        return $this->codeAnalytique;
    }
}
