<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneInvoiceDevis
 *
 * @ORM\Table(name="one_invoice_devis", indexes={@ORM\Index(name="fk_invoice_devis_one_devis1_idx", columns={"one_devis_id"}), @ORM\Index(name="fk_invoice_devis_one_vente1_idx", columns={"one_vente_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneInvoiceDevisRepository")
 */
class OneInvoiceDevis
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
     * @var \AppBundle\Entity\OneDevis
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OneDevis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="one_devis_id", referencedColumnName="id")
     * })
     */
    private $oneDevis;

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
     * Set oneDevis
     *
     * @param integer $oneDevis
     *
     * @return OneInvoiceDevis
     */
    public function setOneDevis($oneDevis)
    {
        $this->oneDevis = $oneDevis;
    
        return $this;
    }

    /**
     * Get oneDevis
     *
     * @return integer
     */
    public function getOneDevis()
    {
        return $this->oneDevis;
    }

    /**
     * Set oneVente
     *
     * @param integer $oneVente
     *
     * @return OneInvoiceDevis
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

