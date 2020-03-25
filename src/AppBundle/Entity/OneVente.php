<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneVente
 *
 * @ORM\Table(name="one_vente", indexes={@ORM\Index(name="fk_vente_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_vente_one_contact_client1_idx", columns={"contact"}), @ORM\Index(name="fk_vente_one_reglement1_idx", columns={"one_reglement_id"}), @ORM\Index(name="fk_vente_one_contact_client2_idx", columns={"contact_livraison"}), @ORM\Index(name="fk_one_vente_one_projet1_idx", columns={"one_projet_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneVenteRepository")
 */
class OneVente
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
     * @ORM\Column(name="date_facture", type="date", nullable=false)
     */
    private $dateFacture;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_facture", type="integer", nullable=true)
     */
    private $statusFacture;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_bon_commande", type="integer", nullable=true)
     */
    private $statusBonCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="text", length=65535, nullable=true)
     */
    private $fichier;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_client", type="string", length=50, nullable=true)
     */
    private $refClient;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_expedition", type="date", nullable=true)
     */
    private $dateExpedition;
    
    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float", precision=10, scale=0, nullable=false)
     */
    private $remise = '0';
    
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
     * @var \AppBundle\Entity\OneContactClient
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneContactClient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_livraison", referencedColumnName="id")
     * })
     */
    private $contactLivraison;

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
     *   @ORM\JoinColumn(name="contact", referencedColumnName="id")
     * })
     */
    private $contact;

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
     * @var \AppBundle\Entity\OneProjet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneProjet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_projet_id", referencedColumnName="id")
     * })
     */
    private $oneProjet;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     *
     */
    private $image;



    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return OneVente
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }

    /**
     * Set statusFacture
     *
     * @param integer $statusFacture
     *
     * @return OneVente
     */
    public function setStatusFacture($statusFacture)
    {
        $this->statusFacture = $statusFacture;

        return $this;
    }

    /**
     * Get statusFacture
     *
     * @return integer
     */
    public function getStatusFacture()
    {
        return $this->statusFacture;
    }

    /**
     * Set statusBonCommande
     *
     * @param integer $statusBonCommande
     *
     * @return OneVente
     */
    public function setStatusBonCommande($statusBonCommande)
    {
        $this->statusBonCommande = $statusBonCommande;

        return $this;
    }

    /**
     * Get statusBonCommande
     *
     * @return integer
     */
    public function getStatusBonCommande()
    {
        return $this->statusBonCommande;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return OneVente
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
     * Set fichier
     *
     * @param string $fichier
     *
     * @return OneVente
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
     * Set type
     *
     * @param integer $type
     *
     * @return OneVente
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
     * Set refClient
     *
     * @param string $refClient
     *
     * @return OneVente
     */
    public function setRefClient($refClient)
    {
        $this->refClient = $refClient;

        return $this;
    }

    /**
     * Get refClient
     *
     * @return string
     */
    public function getRefClient()
    {
        return $this->refClient;
    }

    /**
     * Set dateExpedition
     *
     * @param \DateTime $dateExpedition
     *
     * @return OneVente
     */
    public function setDateExpedition($dateExpedition)
    {
        $this->dateExpedition = $dateExpedition;

        return $this;
    }

    /**
     * Get dateExpedition
     *
     * @return \DateTime
     */
    public function getDateExpedition()
    {
        return $this->dateExpedition;
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
     * Set contactLivraison
     *
     * @param \AppBundle\Entity\OneContactClient $contactLivraison
     *
     * @return OneVente
     */
    public function setContactLivraison(\AppBundle\Entity\OneContactClient $contactLivraison = null)
    {
        $this->contactLivraison = $contactLivraison;

        return $this;
    }

    /**
     * Get contactLivraison
     *
     * @return \AppBundle\Entity\OneContactClient
     */
    public function getContactLivraison()
    {
        return $this->contactLivraison;
    }

    /**
     * Set oneReglement
     *
     * @param \AppBundle\Entity\OneReglement $oneReglement
     *
     * @return OneVente
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
     * Set contact
     *
     * @param \AppBundle\Entity\OneContactClient $contact
     *
     * @return OneVente
     */
    public function setContact(\AppBundle\Entity\OneContactClient $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\OneContactClient
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneVente
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
     * @return OneVente
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
     * @return OneVente
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
     * Set remise
     *
     * @param float $remise
     *
     * @return OneVente
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
     * Set oneProjet
     *
     * @param \AppBundle\Entity\OneProjet $oneProjet
     *
     * @return OneVente
     */
    public function setOneProjet(\AppBundle\Entity\OneProjet $oneProjet = null)
    {
        $this->oneProjet = $oneProjet;
    
        return $this;
    }

    /**
     * Get oneProjet
     *
     * @return \AppBundle\Entity\OneProjet
     */
    public function getOneProjet()
    {
        return $this->oneProjet;
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
