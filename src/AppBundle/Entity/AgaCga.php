<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgaCga
 *
 * @ORM\Table(name="aga_cga", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE", columns={"dossier_id"})})
 * @ORM\Entity
 */
class AgaCga
{
    /**
     * @var integer
     *
     * @ORM\Column(name="adherant", type="integer", nullable=false)
     */
    private $adherant;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="siren", type="string", length=45, nullable=true)
     */
    private $siren;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_adhesion", type="string", length=45, nullable=true)
     */
    private $numeroAdhesion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_adhesion", type="date", nullable=true)
     */
    private $dateAdhesion;

    /**
     * @var string
     *
     * @ORM\Column(name="num_rue", type="string", length=100, nullable=true)
     */
    private $numRue;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=45, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=45, nullable=true)
     */
    private $pays;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set adherant
     *
     * @param integer $adherant
     *
     * @return AgaCga
     */
    public function setAdherant($adherant)
    {
        $this->adherant = $adherant;

        return $this;
    }

    /**
     * Get adherant
     *
     * @return integer
     */
    public function getAdherant()
    {
        return $this->adherant;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return AgaCga
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
     * Set siren
     *
     * @param string $siren
     *
     * @return AgaCga
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set numeroAdhesion
     *
     * @param string $numeroAdhesion
     *
     * @return AgaCga
     */
    public function setNumeroAdhesion($numeroAdhesion)
    {
        $this->numeroAdhesion = $numeroAdhesion;

        return $this;
    }

    /**
     * Get numeroAdhesion
     *
     * @return string
     */
    public function getNumeroAdhesion()
    {
        return $this->numeroAdhesion;
    }

    /**
     * Set dateAdhesion
     *
     * @param \DateTime $dateAdhesion
     *
     * @return AgaCga
     */
    public function setDateAdhesion($dateAdhesion)
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }

    /**
     * Get dateAdhesion
     *
     * @return \DateTime
     */
    public function getDateAdhesion()
    {
        return $this->dateAdhesion;
    }

    /**
     * Set numRue
     *
     * @param string $numRue
     *
     * @return AgaCga
     */
    public function setNumRue($numRue)
    {
        $this->numRue = $numRue;

        return $this;
    }

    /**
     * Get numRue
     *
     * @return string
     */
    public function getNumRue()
    {
        return $this->numRue;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return AgaCga
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return AgaCga
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return AgaCga
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return AgaCga
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }
}
