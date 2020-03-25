<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneAppelTelephonique
 *
 * @ORM\Table(name="one_appel_telephonique", indexes={@ORM\Index(name="fk_one_appel_telephonique_one_contact_client1_idx", columns={"one_contact_client_id"}), @ORM\Index(name="fk_one_appel_telephonique_opportunite1_idx", columns={"opportunite_id"}), @ORM\Index(name="fk_one_appel_telephonique_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_one_appel_telephonique_one_qualification1_idx", columns={"one_qualification_id"}), @ORM\Index(name="fk_one_appel_telephonique_one_projet1_idx", columns={"one_projet_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneAppelTelephoniqueRepository")
 */
class OneAppelTelephonique
{
    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string", length=50, nullable=false)
     */
    private $sujet;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="echeance", type="date", nullable=false)
     */
    private $echeance;
    
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
     * @var \AppBundle\Entity\OneQualificationAppel
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneQualificationAppel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_qualification_id", referencedColumnName="id")
     * })
     */
    private $oneQualification;



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
     * @var \AppBundle\Entity\OneOpportunite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneOpportunite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opportunite_id", referencedColumnName="id")
     * })
     */
    private $opportunite;

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
     * @var \AppBundle\Entity\OneProjet
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneProjet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_projet_id", referencedColumnName="id")
     * })
     */
    private $oneProjet;



    /**
     * Set sujet
     *
     * @param string $sujet
     *
     * @return OneAppelTelephonique
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return OneAppelTelephonique
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
     * Set status
     *
     * @param integer $status
     *
     * @return OneAppelTelephonique
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
     * Set echeance
     *
     * @param \DateTime $echeance
     *
     * @return OneAppelTelephonique
     */
    public function setEcheance($echeance)
    {
        $this->echeance = $echeance;

        return $this;
    }

    /**
     * Get echeance
     *
     * @return \DateTime
     */
    public function getEcheance()
    {
        return $this->echeance;
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
     * Set oneQualification
     *
     * @param \AppBundle\Entity\OneQualificationAppel $oneQualification
     *
     * @return OneAppelTelephonique
     */
    public function setOneQualification(\AppBundle\Entity\OneQualificationAppel $oneQualification = null)
    {
        $this->oneQualification = $oneQualification;

        return $this;
    }

    /**
     * Get oneQualification
     *
     * @return \AppBundle\Entity\OneQualificationAppel
     */
    public function getOneQualification()
    {
        return $this->oneQualification;
    }


    /**
     * Set opportunite
     *
     * @param \AppBundle\Entity\OneOpportunite $opportunite
     *
     * @return OneAppelTelephonique
     */
    public function setOpportunite(\AppBundle\Entity\OneOpportunite $opportunite = null)
    {
        $this->opportunite = $opportunite;

        return $this;
    }

    /**
     * Get opportunite
     *
     * @return \AppBundle\Entity\OneOpportunite
     */
    public function getOpportunite()
    {
        return $this->opportunite;
    }

    /**
     * Set oneContactClient
     *
     * @param \AppBundle\Entity\OneContactClient $oneContactClient
     *
     * @return OneAppelTelephonique
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
     * @return OneAppelTelephonique
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
     * @return OneAppelTelephonique
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
     * Set oneProjet
     *
     * @param \AppBundle\Entity\OneProjet $oneProjet
     *
     * @return OneAppelTelephonique
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
}
