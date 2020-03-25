<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OnePaiementDetail
 *
 * @ORM\Table(name="one_paiement_detail", indexes={@ORM\Index(name="fk_paiement_detail_one_paiement1_idx", columns={"one_paiement_id"}), @ORM\Index(name="fk_paiement_detail_one_encaissement1_idx", columns={"one_encaissement_id"}), @ORM\Index(name="fk_paiement_detail_one_vente1_idx", columns={"one_avoir_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OnePaiementDetailRepository")
 */
class OnePaiementDetail
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
     * @var \AppBundle\Entity\OnePaiement
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OnePaiement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_paiement_id", referencedColumnName="id")
     * })
     */
    private $onePaiement;
    
    /**
     * @var \AppBundle\Entity\OneEncaissement
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneEncaissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_encaissement_id", referencedColumnName="id")
     * })
     */
    private $oneEncaissement;
    
    /**
     * @var \AppBundle\Entity\OneVente
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_avoir_id", referencedColumnName="id")
     * })
     */
    private $oneAvoir;


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
     * Set onePaiement
     *
     * @param integer $onePaiement
     *
     * @return OnePaiementDetail
     */
    public function setOnePaiement($onePaiement)
    {
        $this->onePaiement = $onePaiement;
    
        return $this;
    }

    /**
     * Get onePaiement
     *
     * @return integer
     */
    public function getOnePaiement()
    {
        return $this->onePaiement;
    }

    /**
     * Set oneEncaissement
     *
     * @param integer $oneEncaissement
     *
     * @return OnePaiementDetail
     */
    public function setOneEncaissement($oneEncaissement)
    {
        $this->oneEncaissement = $oneEncaissement;
    
        return $this;
    }

    /**
     * Get oneEncaissement
     *
     * @return integer
     */
    public function getOneEncaissement()
    {
        return $this->oneEncaissement;
    }

    /**
     * Set oneAvoir
     *
     * @param integer $oneAvoir
     *
     * @return OnePaiementDetail
     */
    public function setOneAvoir($oneAvoir)
    {
        $this->oneAvoir = $oneAvoir;
    
        return $this;
    }

    /**
     * Get oneAvoir
     *
     * @return integer
     */
    public function getOneAvoir()
    {
        return $this->oneAvoir;
    }
}
