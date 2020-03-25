<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * OneEncaissement
 *
 * @ORM\Table(name="one_encaissement", indexes={@ORM\Index(name="fk_encaissement_one_type_encaissement1_idx", columns={"one_type_encaissement_id"}), @ORM\Index(name="fk_encaissement_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_encaissement_one_moyen_paiement_idx", columns={"one_moyen_paiement_id"}), @ORM\Index(name="fk_one_encaissement_one_projet1_idx", columns={"one_projet_id"}), @ORM\Index(name="fk_one_encaissement_banque_compte1_idx", columns={"banque_compte_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneEncaissementRepository")
 */
class OneEncaissement
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
     * @ORM\Column(name="date_encaissement", type="date", nullable=false)
     */
    private $dateEncaissement;

    /**
     * @var string
     *
     * @ORM\Column(name="id_transaction", type="string", length=50, nullable=true)
     */
    private $idTransaction;

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
     * @var \AppBundle\Entity\OneMoyenPaiement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneMoyenPaiement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_moyen_paiement_id", referencedColumnName="id")
     * })
     */
    private $oneMoyenPaiement;


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
     * @var \AppBundle\Entity\OneTypeEncaissement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneTypeEncaissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_type_encaissement_id", referencedColumnName="id")
     * })
     */
    private $oneTypeEncaissement;
    
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
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;


    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;


    /**
     * Set dateEncaissement
     *
     * @param \DateTime $dateEncaissement
     *
     * @return OneEncaissement
     */
    public function setDateEncaissement($dateEncaissement)
    {
        $this->dateEncaissement = $dateEncaissement;

        return $this;
    }

    /**
     * Get dateEncaissement
     *
     * @return \DateTime
     */
    public function getDateEncaissement()
    {
        return $this->dateEncaissement;
    }

    /**
     * Set idTransaction
     *
     * @param string $idTransaction
     *
     * @return OneEncaissement
     */
    public function setIdTransaction($idTransaction)
    {
        $this->idTransaction = $idTransaction;

        return $this;
    }

    /**
     * Get idTransaction
     *
     * @return string
     */
    public function getIdTransaction()
    {
        return $this->idTransaction;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return OneEncaissement
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
     * @return OneEncaissement
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * Set oneTypeEncaissement
     *
     * @param \AppBundle\Entity\OneTypeEncaissement $oneTypeEncaissement
     *
     * @return OneEncaissement
     */
    public function setOneTypeEncaissement(\AppBundle\Entity\OneTypeEncaissement $oneTypeEncaissement = null)
    {
        $this->oneTypeEncaissement = $oneTypeEncaissement;

        return $this;
    }

    /**
     * Get oneTypeEncaissement
     *
     * @return \AppBundle\Entity\OneTypeEncaissement
     */
    public function getOneTypeEncaissement()
    {
        return $this->oneTypeEncaissement;
    }

    /**
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneEncaissement
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
     * @return OneEncaissement
     */
    public function setModifieLe($modifieLe)
    {
        $this->modifieLe = $modifieLe;
    
        return $this;
    }

    /**
     * Get modifieLe
     *
     * @return datetime
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
     * @return OneEncaissement
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
     * Set oneMoyenPaiement
     *
     * @param \AppBundle\Entity\OneMoyenPaiement $oneMoyenPaiement
     *
     * @return OneEncaissement
     */
    public function setOneMoyenPaiement(\AppBundle\Entity\OneMoyenPaiement $oneMoyenPaiement = null)
    {
        $this->oneMoyenPaiement = $oneMoyenPaiement;
    
        return $this;
    }

    /**
     * Get oneMoyenPaiement
     *
     * @return \AppBundle\Entity\OneMoyenPaiement
     */
    public function getOneMoyenPaiement()
    {
        return $this->oneMoyenPaiement;
    }

    /**
     * Set oneProjet
     *
     * @param \AppBundle\Entity\OneProjet $oneProjet
     *
     * @return OneEncaissement
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
     * @param BanqueCompte|null $banqueCompte
     * @return $this
     */
    public function setBanqueCompte(BanqueCompte $banqueCompte = null){
        $this->banqueCompte = $banqueCompte;
        return $this;
    }


    /**
     * Get oneProjet
     *
     * @return \AppBundle\Entity\BanqueCompte
     */
    public function getBanqueCompte()
    {
        return $this->banqueCompte;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return OneEncaissement
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
}
