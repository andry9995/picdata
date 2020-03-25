<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneInvoiceCommande
 *
 * @ORM\Table(name="one_invoice_commande", indexes={@ORM\Index(name="fk_invoice_commande_one_commande1_idx", columns={"one_commande_id"}), @ORM\Index(name="fk_invoice_commande_one_vente1_idx", columns={"one_vente_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneInvoiceCommandeRepository")
 */
class OneInvoiceCommande
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
     * @var \AppBundle\Entity\OneVente
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneVente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_commande_id", referencedColumnName="id")
     * })
     */
    private $oneCommande;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set oneCommande
     *
     * @param integer $oneCommande
     *
     * @return OneInvoiceCommande
     */
    public function setOneCommande($oneCommande)
    {
        $this->oneCommande = $oneCommande;
    
        return $this;
    }

    /**
     * Get oneCommande
     *
     * @return integer
     */
    public function getOneCommande()
    {
        return $this->oneCommande;
    }

    /**
     * Set oneVente
     *
     * @param integer $oneVente
     *
     * @return OneInvoiceCommande
     */
    public function setOneVente($oneVente)
    {
        $this->oneVente = $oneVente;
    
        return $this;
    }

    /**
     * Get oneVente
     *
     * @return integer
     */
    public function getOneVente()
    {
        return $this->oneVente;
    }
}

