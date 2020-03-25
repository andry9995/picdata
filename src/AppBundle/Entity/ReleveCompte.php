<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveCompte
 *
 * @ORM\Table(name="releve_compte", indexes={@ORM\Index(name="fk_releve_compte_pcc1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_releve_compte_pcc2_idx", columns={"pcc_autre_id"}), @ORM\Index(name="fk_releve_compte_releve1_idx", columns={"releve_id"})})
 * @ORM\Entity
 */
class ReleveCompte
{
    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float", precision=10, scale=0, nullable=true)
     */
    private $debit;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float", precision=10, scale=0, nullable=true)
     */
    private $credit;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Releve
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Releve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="releve_id", referencedColumnName="id")
     * })
     */
    private $releve;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_autre_id", referencedColumnName="id")
     * })
     */
    private $pccAutre;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;



    /**
     * Set debit
     *
     * @param float $debit
     *
     * @return ReleveCompte
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param float $credit
     *
     * @return ReleveCompte
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
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
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return ReleveCompte
     */
    public function setReleve(\AppBundle\Entity\Releve $releve = null)
    {
        $this->releve = $releve;

        return $this;
    }

    /**
     * Get releve
     *
     * @return \AppBundle\Entity\Releve
     */
    public function getReleve()
    {
        return $this->releve;
    }

    /**
     * Set pccAutre
     *
     * @param \AppBundle\Entity\Pcc $pccAutre
     *
     * @return ReleveCompte
     */
    public function setPccAutre(\AppBundle\Entity\Pcc $pccAutre = null)
    {
        $this->pccAutre = $pccAutre;

        return $this;
    }

    /**
     * Get pccAutre
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccAutre()
    {
        return $this->pccAutre;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return ReleveCompte
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }
}
