<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveDetail
 *
 * @ORM\Table(name="releve_detail", indexes={@ORM\Index(name="fk_releve_id_idx", columns={"releve_id"}), @ORM\Index(name="fk_relevdetail_tiers_id_idx", columns={"compte_tiers_id"}), @ORM\Index(name="fk_relevdetail_chg_id_idx", columns={"compte_chg_id"}), @ORM\Index(name="fk_relevdetail_tva_id_idx", columns={"compte_tva_id"}), @ORM\Index(name="fk_releve_detail_bilan_pcc_id_idx", columns={"compte_bilan_pcc_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveDetailRepository")
 */
class ReleveDetail
{
    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float", precision=10, scale=0, nullable=true)
     */
    private $debit = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float", precision=10, scale=0, nullable=true)
     */
    private $credit = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="compte_tiers", type="string", length=45, nullable=true)
     */
    private $compteTiers;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_chg", type="string", length=45, nullable=true)
     */
    private $compteChg;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_tva", type="string", length=45, nullable=true)
     */
    private $compteTva;

    /**
     * @var integer
     *
     * @ORM\Column(name="ligne_principale", type="integer", nullable=true)
     */
    private $lignePrincipale = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tva_id", referencedColumnName="id")
     * })
     */
    private $compteTva2;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_tiers_id", referencedColumnName="id")
     * })
     */
    private $compteTiers2;

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
     *   @ORM\JoinColumn(name="compte_chg_id", referencedColumnName="id")
     * })
     */
    private $compteChg2;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_bilan_pcc_id", referencedColumnName="id")
     * })
     */
    private $compteBilanPcc;



    /**
     * Set debit
     *
     * @param float $debit
     *
     * @return ReleveDetail
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
     * @return ReleveDetail
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
     * Set compteTiers
     *
     * @param string $compteTiers
     *
     * @return ReleveDetail
     */
    public function setCompteTiers($compteTiers)
    {
        $this->compteTiers = $compteTiers;

        return $this;
    }

    /**
     * Get compteTiers
     *
     * @return string
     */
    public function getCompteTiers()
    {
        return $this->compteTiers;
    }

    /**
     * Set compteChg
     *
     * @param string $compteChg
     *
     * @return ReleveDetail
     */
    public function setCompteChg($compteChg)
    {
        $this->compteChg = $compteChg;

        return $this;
    }

    /**
     * Get compteChg
     *
     * @return string
     */
    public function getCompteChg()
    {
        return $this->compteChg;
    }

    /**
     * Set compteTva
     *
     * @param string $compteTva
     *
     * @return ReleveDetail
     */
    public function setCompteTva($compteTva)
    {
        $this->compteTva = $compteTva;

        return $this;
    }

    /**
     * Get compteTva
     *
     * @return string
     */
    public function getCompteTva()
    {
        return $this->compteTva;
    }

    /**
     * Set lignePrincipale
     *
     * @param integer $lignePrincipale
     *
     * @return ReleveDetail
     */
    public function setLignePrincipale($lignePrincipale)
    {
        $this->lignePrincipale = $lignePrincipale;

        return $this;
    }

    /**
     * Get lignePrincipale
     *
     * @return integer
     */
    public function getLignePrincipale()
    {
        return $this->lignePrincipale;
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
     * Set compteTva2
     *
     * @param \AppBundle\Entity\Pcc $compteTva2
     *
     * @return ReleveDetail
     */
    public function setCompteTva2(\AppBundle\Entity\Pcc $compteTva2 = null)
    {
        $this->compteTva2 = $compteTva2;

        return $this;
    }

    /**
     * Get compteTva2
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteTva2()
    {
        return $this->compteTva2;
    }

    /**
     * Set compteTiers2
     *
     * @param \AppBundle\Entity\Tiers $compteTiers2
     *
     * @return ReleveDetail
     */
    public function setCompteTiers2(\AppBundle\Entity\Tiers $compteTiers2 = null)
    {
        $this->compteTiers2 = $compteTiers2;

        return $this;
    }

    /**
     * Get compteTiers2
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getCompteTiers2()
    {
        return $this->compteTiers2;
    }

    /**
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return ReleveDetail
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
     * Set compteChg2
     *
     * @param \AppBundle\Entity\Pcc $compteChg2
     *
     * @return ReleveDetail
     */
    public function setCompteChg2(\AppBundle\Entity\Pcc $compteChg2 = null)
    {
        $this->compteChg2 = $compteChg2;

        return $this;
    }

    /**
     * Get compteChg2
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteChg2()
    {
        return $this->compteChg2;
    }

    /**
     * Set compteBilanPcc
     *
     * @param \AppBundle\Entity\Pcc $compteBilanPcc
     *
     * @return ReleveDetail
     */
    public function setCompteBilanPcc(\AppBundle\Entity\Pcc $compteBilanPcc = null)
    {
        $this->compteBilanPcc = $compteBilanPcc;

        return $this;
    }

    /**
     * Get compteBilanPcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getCompteBilanPcc()
    {
        return $this->compteBilanPcc;
    }
}
