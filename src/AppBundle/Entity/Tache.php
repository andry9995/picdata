<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tache
 *
 * @ORM\Table(name="tache", indexes={@ORM\Index(name="fk_tache_liste_tache_domaine1_idx", columns={"tache_domaine_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TacheRepository")
 */
class Tache
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=150, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="jalon", type="boolean", nullable=false)
     */
    private $jalon = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TacheDomaine
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TacheDomaine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_domaine_id", referencedColumnName="id")
     * })
     */
    private $tacheDomaine;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Tache
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tache
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set jalon
     *
     * @param boolean $jalon
     *
     * @return Tache
     */
    public function setJalon($jalon)
    {
        $this->jalon = $jalon;

        return $this;
    }

    /**
     * Get jalon
     *
     * @return boolean
     */
    public function getJalon()
    {
        return $this->jalon;
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
     * Set tacheDomaine
     *
     * @param \AppBundle\Entity\TacheDomaine $tacheDomaine
     *
     * @return Tache
     */
    public function setTacheDomaine(\AppBundle\Entity\TacheDomaine $tacheDomaine = null)
    {
        $this->tacheDomaine = $tacheDomaine;

        return $this;
    }

    /**
     * Get tacheDomaine
     *
     * @return \AppBundle\Entity\TacheDomaine
     */
    public function getTacheDomaine()
    {
        return $this->tacheDomaine;
    }
}
