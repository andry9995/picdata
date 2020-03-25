<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneDevis
 *
 * @ORM\Table(name="one_devis", indexes={@ORM\Index(name="fk_devis_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_devis_one_contact_client1_idx", columns={"one_contact_client_id"}), @ORM\Index(name="fk_devis_one_reglement1_idx", columns={"one_reglement_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneDevisRepository")
 */
class OneDevis
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    private $code;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_devis", type="date", nullable=false)
     */
    private $dateDevis;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin_validite", type="date", nullable=false)
     */
    private $finValidite;
    
    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;
    
    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float", precision=10, scale=0, nullable=false)
     */
    private $remise = '0';
    
    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", precision=10, scale=0, nullable=false)
     */
    private $montant = '0';
    
    /**
     *
     * @var datetime
     * 
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;
    
    /**
     *
     * @var datetime
     * @ORM\Column(name="modifie_le", type="datetime", nullable=true)
     */
    private $modifieLe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;


    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="text", length=65535, nullable=true)
     */
    private $fichier;

    /**
     * @var \AppBundle\Entity\OneReglement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_reglement_id", referencedColumnName="id")
     * })
     */
    private $oneReglement;

    /**
     * @var \AppBundle\Entity\OneContactClient
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneContactClient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_contact_client_id", referencedColumnName="id")
     * })
     */
    private $oneContactClient;


    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



    /**
     * Set dateDevis
     *
     * @param \DateTime $dateDevis
     *
     * @return OneDevis
     */
    public function setDateDevis($dateDevis)
    {
        $this->dateDevis = $dateDevis;

        return $this;
    }

    /**
     * Get dateDevis
     *
     * @return \DateTime
     */
    public function getDateDevis()
    {
        return $this->dateDevis;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return OneDevis
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
     * Set finValidite
     *
     * @param \DateTime $finValidite
     *
     * @return OneDevis
     */
    public function setFinValidite($finValidite)
    {
        $this->finValidite = $finValidite;

        return $this;
    }

    /**
     * Get finValidite
     *
     * @return \DateTime
     */
    public function getFinValidite()
    {
        return $this->finValidite;
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
     * Set oneReglement
     *
     * @param \AppBundle\Entity\OneReglement $oneReglement
     *
     * @return OneDevis
     */
    public function setOneReglement(\AppBundle\Entity\OneReglement $oneReglement = null)
    {
        $this->oneReglement = $oneReglement;

        return $this;
    }

    /**
     * Get oneReglement
     *
     * @return \AppBundle\Entity\OneReglement
     */
    public function getOneReglement()
    {
        return $this->oneReglement;
    }

    /**
     * Set oneContactClient
     *
     * @param \AppBundle\Entity\OneContactClient $oneContactClient
     *
     * @return OneDevis
     */
    public function setOneContactClient(\AppBundle\Entity\OneContactClient $oneContactClient = null)
    {
        $this->oneContactClient = $oneContactClient;

        return $this;
    }

    /**
     * Get oneContactClient
     *
     * @return \AppBundle\Entity\OneContactClient
     */
    public function getOneContactClient()
    {
        return $this->oneContactClient;
    }



    /**
     * Set note
     *
     * @param string $note
     *
     * @return OneDevis
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set remise
     *
     * @param float $remise
     *
     * @return OneDevis
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;
    
        return $this;
    }

    /**
     * Get remise
     *
     * @return float
     */
    public function getRemise()
    {
        return $this->remise;
    }

    /**
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneDevis
     */
    public function setCreeLe($creeLe)
    {
        $this->creeLe = $creeLe;
    
        return $this;
    }

    /**
     * Get creeLe
     *
     * @return \DateTime
     */
    public function getCreeLe()
    {
        return $this->creeLe;
    }

    /**
     * Set modifieLe
     *
     * @param \DateTime $modifieLe
     *
     * @return OneDevis
     */
    public function setModifieLe($modifieLe)
    {
        $this->modifieLe = $modifieLe;
    
        return $this;
    }

    /**
     * Get modifieLe
     *
     * @return \DateTime
     */
    public function getModifieLe()
    {
        return $this->modifieLe;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return OneDevis
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return OneDevis
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
    
        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param Tiers|null $tiers
     * @return $this
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * @return Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }


    /**
     * @param $exercice
     * @return $this
     */
    public function setExercice($exercice){
        $this->exercice = $exercice;
        return $this;
    }

    /**
     * @return int
     */
    public function getExercice(){
        return $this->exercice;
    }

    /**
     * @param $fichier
     * @return $this
     */
    public function setFichier($fichier){
        $this->fichier = $fichier;
        return $this;
    }

    /**
     * @return string
     */
    public function getFichier(){
        return $this->fichier;
    }

    /**
     * @param Image|null $image
     * @return $this
     */
    public function setImage(Image $image = null){
        $this->image = $image;
        return $this;
    }

    /**
     * @return Image
     */
    public function getImage(){
        return $this->image;
    }
}
