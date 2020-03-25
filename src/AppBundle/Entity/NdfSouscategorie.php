<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfSouscategorie
 *
 * @ORM\Table(name="ndf_souscategorie", indexes={@ORM\Index(name="fk_ndf_souscategorie_ndf_categorie_idx", columns={"ndf_categorie_id"}), @ORM\Index(name="fk_ndf_souscategorie_soussouscategorie_idx", columns={"soussouscategorie_id"}), @ORM\Index(name="fk_ndf_souscategorie_tva_taux1_idx", columns={"tva_taux_id"})})
 * @ORM\Entity
 */
class NdfSouscategorie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tva_rec", type="integer", nullable=true)
     */
    private $tvaRec;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_tva", type="string", length=9, nullable=true)
     */
    private $compteTva;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TvaTaux
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TvaTaux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_taux_id", referencedColumnName="id")
     * })
     */
    private $tvaTaux;

    /**
     * @var \AppBundle\Entity\Soussouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soussouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="soussouscategorie_id", referencedColumnName="id")
     * })
     */
    private $soussouscategorie;

    /**
     * @var \AppBundle\Entity\NdfCategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfCategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_categorie_id", referencedColumnName="id")
     * })
     */
    private $ndfCategorie;



    /**
     * Set tvaRec
     *
     * @param integer $tvaRec
     *
     * @return NdfSouscategorie
     */
    public function setTvaRec($tvaRec)
    {
        $this->tvaRec = $tvaRec;

        return $this;
    }

    /**
     * Get tvaRec
     *
     * @return integer
     */
    public function getTvaRec()
    {
        return $this->tvaRec;
    }

    /**
     * Set compteTva
     *
     * @param string $compteTva
     *
     * @return NdfSouscategorie
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tvaTaux
     *
     * @param \AppBundle\Entity\TvaTaux $tvaTaux
     *
     * @return NdfSouscategorie
     */
    public function setTvaTaux(\AppBundle\Entity\TvaTaux $tvaTaux = null)
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * Get tvaTaux
     *
     * @return \AppBundle\Entity\TvaTaux
     */
    public function getTvaTaux()
    {
        return $this->tvaTaux;
    }

    /**
     * Set soussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $soussouscategorie
     *
     * @return NdfSouscategorie
     */
    public function setSoussouscategorie(\AppBundle\Entity\Soussouscategorie $soussouscategorie = null)
    {
        $this->soussouscategorie = $soussouscategorie;

        return $this;
    }

    /**
     * Get soussouscategorie
     *
     * @return \AppBundle\Entity\Soussouscategorie
     */
    public function getSoussouscategorie()
    {
        return $this->soussouscategorie;
    }

    /**
     * Set ndfCategorie
     *
     * @param \AppBundle\Entity\NdfCategorie $ndfCategorie
     *
     * @return NdfSouscategorie
     */
    public function setNdfCategorie(\AppBundle\Entity\NdfCategorie $ndfCategorie = null)
    {
        $this->ndfCategorie = $ndfCategorie;

        return $this;
    }

    /**
     * Get ndfCategorie
     *
     * @return \AppBundle\Entity\NdfCategorie
     */
    public function getNdfCategorie()
    {
        return $this->ndfCategorie;
    }
}
