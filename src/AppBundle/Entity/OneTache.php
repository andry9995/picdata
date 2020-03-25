<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneTache
 *
 * @ORM\Table(name="one_tache", indexes={@ORM\Index(name="fk_one_tache_one_client_prospect1_idx", columns={"one_client_prospect_id"}), @ORM\Index(name="fk_one_tache_one_contact_client1_idx", columns={"one_contact_client_id"}), @ORM\Index(name="fk_one_tache_opportunite1_idx", columns={"opportunite_id"}), @ORM\Index(name="fk_one_tache_one_projet1_idx", columns={"one_projet_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneTacheRepository")
 */
class OneTache
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
     * @ORM\Column(name="memo", type="text", length=65535, nullable=true)
     */
    private $memo;

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
     * Set sujet
     *
     * @param string $sujet
     *
     * @return OneTache
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
     * Set memo
     *
     * @param string $memo
     *
     * @return OneTache
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return OneTache
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
     * @return OneTache
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
     * Set fichier
     *
     * @param string $fichier
     *
     * @return OneTache
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
     * Set opportunite
     *
     * @param \AppBundle\Entity\OneOpportunite $opportunite
     *
     * @return OneTache
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
     * @return OneTache
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
     * @return OneTache
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
     * @return OneTache
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
     * @return OneTache
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
