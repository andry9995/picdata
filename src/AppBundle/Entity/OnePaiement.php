<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OnePaiement
 *
 * @ORM\Table(name="one_paiement", indexes={@ORM\Index(name="fk_paiement_one_moyen_paiement1_idx", columns={"one_moyen_paiement_id"}), @ORM\Index(name="fk_paiement_banque_compte1_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_paiement_one_vente1_idx", columns={"one_vente_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OnePaiementRepository")
 */
class OnePaiement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reception", type="datetime", nullable=false)
     */
    private $dateReception;

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
     * @var float
     *
     * @ORM\Column(name="montant", type="float", nullable=false)
     */
    private $montant;

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
     * @var string
     *
     * @ORM\Column(name="ref_banque", type="string", length=255, nullable=true)
     */
    private $refBanque;
    
    /**
     * @var \AppBundle\Entity\OneVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_vente_id", referencedColumnName="id")
     * })
     */
    private $oneVente;
    
     /**
     * @var float
     *
     * @ORM\Column(name="retard", type="integer", nullable=true)
     */
    private $retard;


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
     * Set dateReception
     *
     * @param \DateTime $dateReception
     *
     * @return OnePaiement
     */
    public function setDateReception($dateReception)
    {
        $this->dateReception = $dateReception;
    
        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime
     */
    public function getDateReception()
    {
        return $this->dateReception;
    }

    /**
     * Set oneMoyenPaiement
     *
     * @param integer $oneMoyenPaiement
     *
     * @return OnePaiement
     */
    public function setOneMoyenPaiement($oneMoyenPaiement)
    {
        $this->oneMoyenPaiement = $oneMoyenPaiement;
    
        return $this;
    }

    /**
     * Get oneMoyenPaiement
     *
     * @return integer
     */
    public function getOneMoyenPaiement()
    {
        return $this->oneMoyenPaiement;
    }

    /**
     * Set montant
     *
     * @param float $montant
     *
     * @return OnePaiement
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
     * Set banqueCompte
     *
     * @param integer $banqueCompte
     *
     * @return OnePaiement
     */
    public function setBanqueCompte($banqueCompte)
    {
        $this->banqueCompte = $banqueCompte;
    
        return $this;
    }

    /**
     * Get banqueCompte
     *
     * @return integer
     */
    public function getBanqueCompte()
    {
        return $this->banqueCompte;
    }

    /**
     * Set refBanque
     *
     * @param string $refBanque
     *
     * @return OnePaiement
     */
    public function setRefBanque($refBanque)
    {
        $this->refBanque = $refBanque;
    
        return $this;
    }

    /**
     * Get refBanque
     *
     * @return string
     */
    public function getRefBanque()
    {
        return $this->refBanque;
    }

    /**
     * Set oneVente
     *
     * @return OnePaiement
     */
    public function setOneVente($oneVente)
    {
        $this->oneVente = $oneVente;
    
        return $this;
    }

    /**
     * Get oneVente
     *
     * @return \AppBundle\Entity\OneVente
     */
    public function getOneVente()
    {
        return $this->oneVente;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return OnePaiement
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
     * Set retard
     *
     * @param \interger $retard
     *
     * @return OnePaiement
     */
    public function setRetard($retard)
    {
        $this->retard = $retard;
    
        return $this;
    }

    /**
     * Get retard
     *
     * @return \interger
     */
    public function getRetard()
    {
        return $this->retard;
    }
}
