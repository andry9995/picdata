<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstructionDossier
 *
 * @ORM\Table(name="instruction_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"client_id"})}, indexes={@ORM\Index(name="fk_isntruction_dossier_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_instruction_dossier_methode_suivi_cheque_idx", columns={"methode_suivi_cheque_id"}), @ORM\Index(name="fk_instruction_dossier_gestion_date_ecriture1_idx", columns={"gestion_date_ecriture_id"}), @ORM\Index(name="fk_instruction_dossier_note_frais_idx", columns={"note_frais"}), @ORM\Index(name="fk_instruction_dossier_logiciel_idx", columns={"logiciel_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InstructionDossierRepository")
 */
class InstructionDossier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rapprochement_banque", type="integer", nullable=true)
     */
    private $rapprochementBanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="petite_depense", type="integer", nullable=true)
     */
    private $petiteDepense;

    /**
     * @var integer
     *
     * @ORM\Column(name="immobilisation", type="integer", nullable=true)
     */
    private $immobilisation;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=true)
     */
    private $tva;

    /**
     * @var integer
     *
     * @ORM\Column(name="caisse", type="integer", nullable=true)
     */
    private $caisse;

    /**
     * @var integer
     *
     * @ORM\Column(name="banque", type="integer", nullable=true)
     */
    private $banque;

    /**
     * @var integer
     *
     * @ORM\Column(name="vehicule", type="integer", nullable=true)
     */
    private $vehicule;

    /**
     * @var integer
     *
     * @ORM\Column(name="frais_representation", type="integer", nullable=true)
     */
    private $fraisRepresentation;

    /**
     * @var integer
     *
     * @ORM\Column(name="restaurant", type="integer", nullable=true)
     */
    private $restaurant;

    /**
     * @var integer
     *
     * @ORM\Column(name="hebergement", type="integer", nullable=true)
     */
    private $hebergement;

    /**
     * @var integer
     *
     * @ORM\Column(name="deplacement", type="integer", nullable=true)
     */
    private $deplacement;

    /**
     * @var integer
     *
     * @ORM\Column(name="cadeau_entreprise", type="integer", nullable=true)
     */
    private $cadeauEntreprise;

    /**
     * @var integer
     *
     * @ORM\Column(name="logement", type="integer", nullable=true)
     */
    private $logement;

    /**
     * @var string
     *
     * @ORM\Column(name="instruction", type="text", length=65535, nullable=true)
     */
    private $instruction;

    /**
     * @var integer
     *
     * @ORM\Column(name="creation_tiers", type="integer", nullable=true)
     */
    private $creationTiers;

    /**
     * @var string
     *
     * @ORM\Column(name="piece_jointe", type="text", length=65535, nullable=true)
     */
    private $pieceJointe;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NoteDeFrais
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NoteDeFrais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="note_frais", referencedColumnName="id")
     * })
     */
    private $noteFrais;

    /**
     * @var \AppBundle\Entity\MethodeSuiviCheque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MethodeSuiviCheque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="methode_suivi_cheque_id", referencedColumnName="id")
     * })
     */
    private $methodeSuiviCheque;

    /**
     * @var \AppBundle\Entity\Logiciel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Logiciel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="logiciel_id", referencedColumnName="id")
     * })
     */
    private $logiciel;

    /**
     * @var \AppBundle\Entity\GestionDateEcriture
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GestionDateEcriture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gestion_date_ecriture_id", referencedColumnName="id")
     * })
     */
    private $gestionDateEcriture;

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
     * Set rapprochementBanque
     *
     * @param integer $rapprochementBanque
     *
     * @return InstructionDossier
     */
    public function setRapprochementBanque($rapprochementBanque)
    {
        $this->rapprochementBanque = $rapprochementBanque;

        return $this;
    }

    /**
     * Get rapprochementBanque
     *
     * @return integer
     */
    public function getRapprochementBanque()
    {
        return $this->rapprochementBanque;
    }

    /**
     * Set petiteDepense
     *
     * @param integer $petiteDepense
     *
     * @return InstructionDossier
     */
    public function setPetiteDepense($petiteDepense)
    {
        $this->petiteDepense = $petiteDepense;

        return $this;
    }

    /**
     * Get petiteDepense
     *
     * @return integer
     */
    public function getPetiteDepense()
    {
        return $this->petiteDepense;
    }

    /**
     * Set immobilisation
     *
     * @param integer $immobilisation
     *
     * @return InstructionDossier
     */
    public function setImmobilisation($immobilisation)
    {
        $this->immobilisation = $immobilisation;

        return $this;
    }

    /**
     * Get immobilisation
     *
     * @return integer
     */
    public function getImmobilisation()
    {
        return $this->immobilisation;
    }

    /**
     * Set tva
     *
     * @param integer $tva
     *
     * @return InstructionDossier
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
     * Set caisse
     *
     * @param integer $caisse
     *
     * @return InstructionDossier
     */
    public function setCaisse($caisse)
    {
        $this->caisse = $caisse;

        return $this;
    }

    /**
     * Get caisse
     *
     * @return integer
     */
    public function getCaisse()
    {
        return $this->caisse;
    }

    /**
     * Set banque
     *
     * @param integer $banque
     *
     * @return InstructionDossier
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return integer
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set vehicule
     *
     * @param integer $vehicule
     *
     * @return InstructionDossier
     */
    public function setVehicule($vehicule)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return integer
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set fraisRepresentation
     *
     * @param integer $fraisRepresentation
     *
     * @return InstructionDossier
     */
    public function setFraisRepresentation($fraisRepresentation)
    {
        $this->fraisRepresentation = $fraisRepresentation;

        return $this;
    }

    /**
     * Get fraisRepresentation
     *
     * @return integer
     */
    public function getFraisRepresentation()
    {
        return $this->fraisRepresentation;
    }

    /**
     * Set restaurant
     *
     * @param integer $restaurant
     *
     * @return InstructionDossier
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return integer
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set hebergement
     *
     * @param integer $hebergement
     *
     * @return InstructionDossier
     */
    public function setHebergement($hebergement)
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    /**
     * Get hebergement
     *
     * @return integer
     */
    public function getHebergement()
    {
        return $this->hebergement;
    }

    /**
     * Set deplacement
     *
     * @param integer $deplacement
     *
     * @return InstructionDossier
     */
    public function setDeplacement($deplacement)
    {
        $this->deplacement = $deplacement;

        return $this;
    }

    /**
     * Get deplacement
     *
     * @return integer
     */
    public function getDeplacement()
    {
        return $this->deplacement;
    }

    /**
     * Set cadeauEntreprise
     *
     * @param integer $cadeauEntreprise
     *
     * @return InstructionDossier
     */
    public function setCadeauEntreprise($cadeauEntreprise)
    {
        $this->cadeauEntreprise = $cadeauEntreprise;

        return $this;
    }

    /**
     * Get cadeauEntreprise
     *
     * @return integer
     */
    public function getCadeauEntreprise()
    {
        return $this->cadeauEntreprise;
    }

    /**
     * Set logement
     *
     * @param integer $logement
     *
     * @return InstructionDossier
     */
    public function setLogement($logement)
    {
        $this->logement = $logement;

        return $this;
    }

    /**
     * Get logement
     *
     * @return integer
     */
    public function getLogement()
    {
        return $this->logement;
    }

    /**
     * Set instruction
     *
     * @param string $instruction
     *
     * @return InstructionDossier
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;

        return $this;
    }

    /**
     * Get instruction
     *
     * @return string
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * Set pieceJointe
     *
     * @param string $pieceJointe
     *
     * @return InstructionDossier
     */
    public function setPieceJointe($pieceJointe)
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    /**
     * Get pieceJointe
     *
     * @return string
     */
    public function getPieceJointe()
    {
        return $this->pieceJointe;
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
     * Set noteFrais
     *
     * @param \AppBundle\Entity\NoteDeFrais $noteFrais
     *
     * @return InstructionDossier
     */
    public function setNoteFrais(\AppBundle\Entity\NoteDeFrais $noteFrais = null)
    {
        $this->noteFrais = $noteFrais;

        return $this;
    }

    /**
     * Get noteFrais
     *
     * @return \AppBundle\Entity\NoteDeFrais
     */
    public function getNoteFrais()
    {
        return $this->noteFrais;
    }

    /**
     * Set methodeSuiviCheque
     *
     * @param \AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque
     *
     * @return InstructionDossier
     */
    public function setMethodeSuiviCheque(\AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque = null)
    {
        $this->methodeSuiviCheque = $methodeSuiviCheque;

        return $this;
    }

    /**
     * Get methodeSuiviCheque
     *
     * @return \AppBundle\Entity\MethodeSuiviCheque
     */
    public function getMethodeSuiviCheque()
    {
        return $this->methodeSuiviCheque;
    }

    /**
     * Set logiciel
     *
     * @param \AppBundle\Entity\Logiciel $logiciel
     *
     * @return InstructionDossier
     */
    public function setLogiciel(\AppBundle\Entity\Logiciel $logiciel = null)
    {
        $this->logiciel = $logiciel;

        return $this;
    }

    /**
     * Get logiciel
     *
     * @return \AppBundle\Entity\Logiciel
     */
    public function getLogiciel()
    {
        return $this->logiciel;
    }

    /**
     * Set gestionDateEcriture
     *
     * @param \AppBundle\Entity\GestionDateEcriture $gestionDateEcriture
     *
     * @return InstructionDossier
     */
    public function setGestionDateEcriture(\AppBundle\Entity\GestionDateEcriture $gestionDateEcriture = null)
    {
        $this->gestionDateEcriture = $gestionDateEcriture;

        return $this;
    }

    /**
     * Get gestionDateEcriture
     *
     * @return \AppBundle\Entity\GestionDateEcriture
     */
    public function getGestionDateEcriture()
    {
        return $this->gestionDateEcriture;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return InstructionDossier
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

    /**
     * @return int
     */
    public function getCreationTiers(){
        return $this->creationTiers;
    }

    /**
     * @param $creationTiers
     * @return $this
     */
    public function setCreationTiers($creationTiers){
        $this->creationTiers = $creationTiers;
        return $this;
    }
}
