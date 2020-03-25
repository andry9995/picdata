<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TresoCategoriePcg
 *
 * @ORM\Table(name="treso_categorie_pcg", indexes={@ORM\Index(name="fk_treso_categorie_pcg_treso_categorie1_idx", columns={"treso_categorie_id"}), @ORM\Index(name="fk_treso_categorie_pcg_pcg1_idx", columns={"pcg_id"}), @ORM\Index(name="uniq_treso_categorie_pcg_tresorerie_categorie", columns={"treso_categorie_id", "pcg_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TresoCategoriePcgRepository")
 */
class TresoCategoriePcg
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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TresoCategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TresoCategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="treso_categorie_id", referencedColumnName="id")
     * })
     */
    private $tresoCategorie;

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
     * @var integer
     *
     * @ORM\Column(name="type_compte", type="integer", nullable=true)
     */
    private $typeCompte = '0';



    /**
     * Set solde
     *
     * @param integer $solde
     *
     * @return TresoCategoriePcg
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
     * @return TresoCategoriePcg
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tresoCategorie
     *
     * @param \AppBundle\Entity\TresoCategorie $tresoCategorie
     *
     * @return TresoCategoriePcg
     */
    public function setTresoCategorie(\AppBundle\Entity\TresoCategorie $tresoCategorie = null)
    {
        $this->tresoCategorie = $tresoCategorie;

        return $this;
    }

    /**
     * Get tresoCategorie
     *
     * @return \AppBundle\Entity\TresoCategorie
     */
    public function getTresoCategorie()
    {
        return $this->tresoCategorie;
    }

    /**
     * Set pcg
     *
     * @param \AppBundle\Entity\Pcg $pcg
     *
     * @return TresoCategoriePcg
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

    /**
     * Set typeCompte
     *
     * @param integer $typeCompte
     *
     * @return TresoCategoriePcg
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
}
