<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneAchat
 *
 * @ORM\Table(name="one_achat", indexes={@ORM\Index(name="fk_one_achat_contact_fournisseur1_idx", columns={"contact_id"}), @ORM\Index(name="fk_one_achat_one_reglement1_idx", columns={"one_reglement_id"}), @ORM\Index(name="fk_one_achat_one_fournisseur1_idx", columns={"one_fournisseur_id"}), @ORM\Index(name="fk_one_achat_tiers1_idx", columns={"tiers_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneAchatRepository")
 */
class OneAchat
{
    /**
     * @var string
     *
     * @ORM\Column(name="ref_fournisseur", type="string", length=45, nullable=true)
     */
    private $refFournisseur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_facture", type="date", nullable=true)
     */
    private $dateFacture;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_facture", type="integer", nullable=true)
     */
    private $statusFacture;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="date", nullable=true)
     */
    private $dateLivraison;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifie_le", type="datetime", nullable=true)
     */
    private $modifieLe;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var float
     *
     * @ORM\Column(name="remise", type="float", precision=10, scale=0, nullable=true)
     */
    private $remise;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\OneFournisseur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneFournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_fournisseur_id", referencedColumnName="id")
     * })
     */
    private $oneFournisseur;

    /**
     * @var \AppBundle\Entity\OneMoyenPaiement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneReglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_reglement_id", referencedColumnName="id")
     * })
     */
    private $oneReglement;

    /**
     * @var \AppBundle\Entity\OneContactFournisseur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneContactFournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     * })
     */
    private $contact;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    private $code;



    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="text", nullable=true)
     */
    private $fichier;

    /**
     * Set refFournisseur
     *
     * @param string $refFournisseur
     *
     * @return OneAchat
     */
    public function setRefFournisseur($refFournisseur)
    {
        $this->refFournisseur = $refFournisseur;

        return $this;
    }

    /**
     * Get refFournisseur
     *
     * @return string
     */
    public function getRefFournisseur()
    {
        return $this->refFournisseur;
    }

    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     *
     * @return OneAchat
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
     * @return OneAchat
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
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     *
     * @return OneAchat
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return \DateTime
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
    }

    /**
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneAchat
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
     * @param $modifieLe
     * @return $this
     */
    public function setModifieLe($modifieLe){
        $this->modifieLe = $modifieLe;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModifieLe(){
        return $this->modifieLe;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return OneAchat
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
     * @return OneAchat
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return OneAchat
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set oneFournisseur
     *
     * @param \AppBundle\Entity\OneFournisseur $oneFournisseur
     *
     * @return OneAchat
     */
    public function setOneFournisseur(\AppBundle\Entity\OneFournisseur $oneFournisseur = null)
    {
        $this->oneFournisseur = $oneFournisseur;

        return $this;
    }

    /**
     * Get oneFournisseur
     *
     * @return \AppBundle\Entity\OneFournisseur
     */
    public function getOneFournisseur()
    {
        return $this->oneFournisseur;
    }

    /**
     * @param OneReglement|null $oneReglement
     * @return $this
     */
    public function setOneReglement(\AppBundle\Entity\OneReglement $oneReglement = null)
    {
        $this->oneReglement = $oneReglement;

        return $this;
    }

    /**
     * Get oneReglement
     *
     * @return \AppBundle\Entity\OneMoyenPaiement
     */
    public function getOneReglement()
    {
        return $this->oneReglement;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\OneContactFournisseur $contact
     *
     * @return OneAchat
     */
    public function setContact(\AppBundle\Entity\OneContactFournisseur $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\OneContactFournisseur
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type){
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(){
        return $this->type;
    }


    /**
     * @param $code
     * @return $this
     */
    public function setCode($code){
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode(){
        return $this->code;
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
}
