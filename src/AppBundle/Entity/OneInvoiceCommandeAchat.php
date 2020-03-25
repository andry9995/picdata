<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneInvoiceCommande
 *
 * @ORM\Table(name="one_invoice_commande_achat", indexes={@ORM\Index(name="fk_invoice_commande_one_commande1_idx", columns={"one_commande_id"}), @ORM\Index(name="fk_invoice_commande_one_achat1_idx", columns={"one_achat_id"})})
 * @ORM\Entity
 */
class OneInvoiceCommandeAchat
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
     * @var \AppBundle\Entity\OneAchat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneAchat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_commande_id", referencedColumnName="id")
     * })
     */
    private $oneCommande;

    /**
     * @var \AppBundle\Entity\OneAchat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneAchat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_achat_id", referencedColumnName="id")
     * })
     */
    private $oneAchat;


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
     * @param $oneCommande
     * @return $this
     */
    public function setOneCommande($oneCommande)
    {
        $this->oneCommande = $oneCommande;

        return $this;
    }


    /**
     * @return OneAchat
     */
    public function getOneCommande()
    {
        return $this->oneCommande;
    }

    /**
     * @param $oneAchat
     * @return $this
     */
    public function setOneAchat($oneAchat)
    {
        $this->oneAchat= $oneAchat;

        return $this;
    }

    /**
     * @return OneAchat
     */
    public function getOneAchat()
    {
        return $this->oneAchat;
    }
}

