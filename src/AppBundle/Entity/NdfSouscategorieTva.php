<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfSouscategorieTva
 *
 * @ORM\Table(name="ndf_souscategorie_tva", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE", columns={"ndf_souscategorie_id", "compte"})}, indexes={@ORM\Index(name="fk_ndf_souscategorie_tva_ndf_soucategorie1_idx", columns={"ndf_souscategorie_id"})})
 * @ORM\Entity
 */
class NdfSouscategorieTva
{
    /**
     * @var string
     *
     * @ORM\Column(name="compte", type="string", length=9, nullable=false)
     */
    private $compte;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfSouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfSouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_souscategorie_id", referencedColumnName="id")
     * })
     */
    private $ndfSouscategorie;



    /**
     * Set compte
     *
     * @param string $compte
     *
     * @return NdfSouscategorieTva
     */
    public function setCompte($compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return string
     */
    public function getCompte()
    {
        return $this->compte;
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
     * Set ndfSouscategorie
     *
     * @param \AppBundle\Entity\NdfSouscategorie $ndfSouscategorie
     *
     * @return NdfSouscategorieTva
     */
    public function setNdfSouscategorie(\AppBundle\Entity\NdfSouscategorie $ndfSouscategorie = null)
    {
        $this->ndfSouscategorie = $ndfSouscategorie;

        return $this;
    }

    /**
     * Get ndfSouscategorie
     *
     * @return \AppBundle\Entity\NdfSouscategorie
     */
    public function getNdfSouscategorie()
    {
        return $this->ndfSouscategorie;
    }
}
