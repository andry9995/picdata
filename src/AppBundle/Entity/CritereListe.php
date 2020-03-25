<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CritereListe
 *
 * @ORM\Table(name="critere_liste", indexes={@ORM\Index(name="fk_critere_liste_criteres1_idx", columns={"criteres_id"}), @ORM\Index(name="fk_critere_liste_pcc1_idx", columns={"pcc_id"}), @ORM\Index(name="fk_critere_liste_pcc2_idx", columns={"pcc_autre_id"})})
 * @ORM\Entity
 */
class CritereListe
{
    /**
     * @var string
     *
     * @ORM\Column(name="critere", type="string", length=150, nullable=false)
     */
    private $critere;

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
     * @ORM\Column(name="type_critere", type="integer", nullable=true)
     */
    private $typeCritere;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \AppBundle\Entity\Criteres
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Criteres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="criteres_id", referencedColumnName="id")
     * })
     */
    private $criteres;



    /**
     * Set critere
     *
     * @param string $critere
     *
     * @return CritereListe
     */
    public function setCritere($critere)
    {
        $this->critere = $critere;

        return $this;
    }

    /**
     * Get critere
     *
     * @return string
     */
    public function getCritere()
    {
        return $this->critere;
    }

    /**
     * Set debit
     *
     * @param float $debit
     *
     * @return CritereListe
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
     * @return CritereListe
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
     * Set typeCritere
     *
     * @param integer $typeCritere
     *
     * @return CritereListe
     */
    public function setTypeCritere($typeCritere)
    {
        $this->typeCritere = $typeCritere;

        return $this;
    }

    /**
     * Get typeCritere
     *
     * @return integer
     */
    public function getTypeCritere()
    {
        return $this->typeCritere;
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
     * Set pccAutre
     *
     * @param \AppBundle\Entity\Pcc $pccAutre
     *
     * @return CritereListe
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
     * @return CritereListe
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

    /**
     * Set criteres
     *
     * @param \AppBundle\Entity\Criteres $criteres
     *
     * @return CritereListe
     */
    public function setCriteres(\AppBundle\Entity\Criteres $criteres = null)
    {
        $this->criteres = $criteres;

        return $this;
    }

    /**
     * Get criteres
     *
     * @return \AppBundle\Entity\Criteres
     */
    public function getCriteres()
    {
        return $this->criteres;
    }
}
