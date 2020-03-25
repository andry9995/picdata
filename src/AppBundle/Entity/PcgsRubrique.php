<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PcgsRubrique
 *
 * @ORM\Table(name="pcgs_rubrique", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_pcgs_rubrique", columns={"rubrique_id", "pcg_id"})}, indexes={@ORM\Index(name="fk_pcgs_rubrique_rubrique1_idx", columns={"rubrique_id"}), @ORM\Index(name="fk_pcgs_rubrique_pcg1_idx", columns={"pcg_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PcgsRubriqueRepository")
 */
class PcgsRubrique
{
    /**
     * @var integer
     *
     * @ORM\Column(name="solde", type="integer", nullable=false)
     */
    private $solde = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="negation", type="integer", nullable=false)
     */
    private $negation = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_compte", type="integer", nullable=true)
     */
    private $typeCompte = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Rubrique
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rubrique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rubrique_id", referencedColumnName="id")
     * })
     */
    private $rubrique;

    /**
     * @var \AppBundle\Entity\Pcg
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcg_id", referencedColumnName="id")
     * })
     */
    private $pcg;



    /**
     * Set solde
     *
     * @param integer $solde
     *
     * @return PcgsRubrique
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return integer
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set negation
     *
     * @param integer $negation
     *
     * @return PcgsRubrique
     */
    public function setNegation($negation)
    {
        $this->negation = $negation;

        return $this;
    }

    /**
     * Get negation
     *
     * @return integer
     */
    public function getNegation()
    {
        return $this->negation;
    }

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     *
     * @return PcgsRubrique
     */
    public function setTypeCompte($typeCompte)
    {
        $this->typeCompte = $typeCompte;

        return $this;
    }

    /**
     * Get typeCompte
     *
     * @return integer
     */
    public function getTypeCompte()
    {
        return $this->typeCompte;
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
     * Set rubrique
     *
     * @param \AppBundle\Entity\Rubrique $rubrique
     *
     * @return PcgsRubrique
     */
    public function setRubrique(\AppBundle\Entity\Rubrique $rubrique = null)
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    /**
     * Get rubrique
     *
     * @return \AppBundle\Entity\Rubrique
     */
    public function getRubrique()
    {
        return $this->rubrique;
    }

    /**
     * Set pcg
     *
     * @param \AppBundle\Entity\Pcg $pcg
     *
     * @return PcgsRubrique
     */
    public function setPcg(\AppBundle\Entity\Pcg $pcg = null)
    {
        $this->pcg = $pcg;

        return $this;
    }

    /**
     * Get pcg
     *
     * @return \AppBundle\Entity\Pcg
     */
    public function getPcg()
    {
        return $this->pcg;
    }
}
