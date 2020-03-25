<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanqueCompteSouscategorie
 *
 * @ORM\Table(name="banque_compte_souscategorie", indexes={@ORM\Index(name="banque_compte_souscategorie_banque_compte1_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_banque_compte_souscategorie1_idx", columns={"souscategorie_id"})})
 * @ORM\Entity
 */
class BanqueCompteSouscategorie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="avec", type="integer", nullable=true)
     */
    private $avec;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;

    /**
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="souscategorie_id", referencedColumnName="id")
     * })
     */
    private $souscategorie;



    /**
     * Set avec
     *
     * @param integer $avec
     *
     * @return BanqueCompteSouscategorie
     */
    public function setAvec($avec)
    {
        $this->avec = $avec;

        return $this;
    }

    /**
     * Get avec
     *
     * @return integer
     */
    public function getAvec()
    {
        return $this->avec;
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
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return BanqueCompteSouscategorie
     */
    public function setBanqueCompte(\AppBundle\Entity\BanqueCompte $banqueCompte = null)
    {
        $this->banqueCompte = $banqueCompte;

        return $this;
    }

    /**
     * Get banqueCompte
     *
     * @return \AppBundle\Entity\BanqueCompte
     */
    public function getBanqueCompte()
    {
        return $this->banqueCompte;
    }

    /**
     * Set souscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $souscategorie
     *
     * @return BanqueCompteSouscategorie
     */
    public function setSouscategorie(\AppBundle\Entity\Souscategorie $souscategorie = null)
    {
        $this->souscategorie = $souscategorie;

        return $this;
    }

    /**
     * Get souscategorie
     *
     * @return \AppBundle\Entity\Souscategorie
     */
    public function getSouscategorie()
    {
        return $this->souscategorie;
    }
}
