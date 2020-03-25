<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * OneOpportunite
 *
 * @ORM\Table(name="one_opportunite", indexes={@ORM\Index(name="fk_opportunite_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_opportunite_one_contact_client1_idx", columns={"one_contact_client_id"}), @ORM\Index(name="fk_opportunite_one_avancement1_idx", columns={"one_avancement_id"}), @ORM\Index(name="fk_opportunite_one_status_opp1_idx", columns={"one_status_opp_id"}), @ORM\Index(name="fk_opportunite_one_probabilite1_idx", columns={"one_probabilite_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneOpportuniteRepository")
 */
class OneOpportunite
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cloture", type="date", nullable=true)
     */
    private $cloture;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", precision=10, scale=0, nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;
    
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
     * @var \AppBundle\Entity\OneProbabilite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneProbabilite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_probabilite_id", referencedColumnName="id")
     * })
     */
    private $oneProbabilite;

    /**
     * @var \AppBundle\Entity\OneStatusOpp
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneStatusOpp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_status_opp_id", referencedColumnName="id")
     * })
     */
    private $oneStatusOpp;

    /**
     * @var \AppBundle\Entity\OneAvancement
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneAvancement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_avancement_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $oneAvancement;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return OneOpportunite
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
     * Set cloture
     *
     * @param \DateTime $cloture
     *
     * @return OneOpportunite
     */
    public function setCloture($cloture)
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * Get cloture
     *
     * @return \DateTime
     */
    public function getCloture()
    {
        return $this->cloture;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return OneOpportunite
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
     * Set note
     *
     * @param string $note
     *
     * @return OneOpportunite
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set oneProbabilite
     *
     * @param \AppBundle\Entity\OneProbabilite $oneProbabilite
     *
     * @return OneOpportunite
     */
    public function setOneProbabilite(\AppBundle\Entity\OneProbabilite $oneProbabilite = null)
    {
        $this->oneProbabilite = $oneProbabilite;

        return $this;
    }

    /**
     * Get oneProbabilite
     *
     * @return \AppBundle\Entity\OneProbabilite
     */
    public function getOneProbabilite()
    {
        return $this->oneProbabilite;
    }

    /**
     * Set oneStatusOpp
     *
     * @param \AppBundle\Entity\OneStatusOpp $oneStatusOpp
     *
     * @return OneOpportunite
     */
    public function setOneStatusOpp(\AppBundle\Entity\OneStatusOpp $oneStatusOpp = null)
    {
        $this->oneStatusOpp = $oneStatusOpp;

        return $this;
    }

    /**
     * Get oneStatusOpp
     *
     * @return \AppBundle\Entity\OneStatusOpp
     */
    public function getOneStatusOpp()
    {
        return $this->oneStatusOpp;
    }

    /**
     * Set oneAvancement
     *
     * @param \AppBundle\Entity\OneAvancement $oneAvancement
     *
     * @return OneOpportunite
     */
    public function setOneAvancement(\AppBundle\Entity\OneAvancement $oneAvancement = null)
    {
        $this->oneAvancement = $oneAvancement;

        return $this;
    }

    /**
     * Get oneAvancement
     *
     * @return \AppBundle\Entity\OneAvancement
     */
    public function getOneAvancement()
    {
        return $this->oneAvancement;
    }

    /**
     * Set oneContactClient
     *
     * @param \AppBundle\Entity\OneContactClient $oneContactClient
     *
     * @return OneOpportunite
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
     * Set creeLe
     *
     * @param \DateTime $creeLe
     *
     * @return OneOpportunite
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
     * @return OneOpportunite
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
}
