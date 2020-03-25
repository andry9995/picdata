<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nature
 *
 * @ORM\Table(name="nature", indexes={@ORM\Index(name="fk_nature_organisme1_idx", columns={"organisme_id"}), @ORM\Index(name="fk_nature_categorieid1_idx", columns={"categorie_id"})})
 * @ORM\Entity
 */
class Nature
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="actif", type="integer", nullable=true)
     */
    private $actif = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Organisme
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organisme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisme_id", referencedColumnName="id")
     * })
     */
    private $organisme;

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
     * @return Nature
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
     * Set actif
     *
     * @param integer $actif
     *
     * @return Nature
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organisme
     *
     * @param \AppBundle\Entity\Organisme $organisme
     *
     * @return Nature
     */
    public function setOrganisme(\AppBundle\Entity\Organisme $organisme = null)
    {
        $this->organisme = $organisme;

        return $this;
    }

    /**
     * Get organisme
     *
     * @return \AppBundle\Entity\Organisme
     */
    public function getOrganisme()
    {
        return $this->organisme;
    }

    /**
     * Set categorie
     *
     * @param \AppBundle\Entity\Categorie $categorie
     *
     * @return Nature
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
