<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Souscategorie
 *
 * @ORM\Table(name="souscategorie", indexes={@ORM\Index(name="fk_souscategorie_categorie1_idx", columns={"categorie_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SouscategorieRepository")
 */
class Souscategorie
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=true)
     */
    private $libelle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_new", type="string", length=150, nullable=true)
     */
    private $libelleNew = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="actif", type="integer", nullable=true)
     */
    private $actif = '0';

    /**
     * @var \AppBundle\Entity\Categorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $categorie;



    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Souscategorie
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set libelleNew
     *
     * @param string $libelleNew
     *
     * @return Souscategorie
     */
    public function setLibelleNew($libelleNew)
    {
        $this->libelleNew = $libelleNew;

        return $this;
    }

    /**
     * Get libelleNew
     *
     * @return string
     */
    public function getLibelleNew()
    {
        return $this->libelleNew;
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
     * Set actif
     *
     * @param integer $actif
     *
     * @return Souscategorie
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return integer
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set categorie
     *
     * @param \AppBundle\Entity\Categorie $categorie
     *
     * @return Souscategorie
     */
    public function setCategorie(\AppBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
