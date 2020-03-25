<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogCegj
 *
 * @ORM\Table(name="log_cegj", indexes={@ORM\Index(name="fk_log_cegj_categorie_1_idx", columns={"old_categorie_id"}), @ORM\Index(name="fk_log_cegj_categorie_new_idx", columns={"new_categorie_id"}), @ORM\Index(name="fk_log_cegj_sous_categorie_old_idx", columns={"old_souscategorie_id"}), @ORM\Index(name="fk_log_cegj_sous_categorie_new_idx", columns={"new_souscategorie_id"}), @ORM\Index(name="fk_log_cegj_sous_sous_categorie_old_idx", columns={"old_soussouscategorie_id"}), @ORM\Index(name="fk_log_cegj_sous_sous_categorie_new_idx", columns={"new_soussouscategorie_id"}), @ORM\Index(name="fk_log_cegj_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_log_cegj_image1_idx", columns={"image_id"})})
 * @ORM\Entity
 */
class LogCegj
{
    /**
     * @var string
     *
     * @ORM\Column(name="etape", type="string", length=45, nullable=false)
     */
    private $etape;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Soussouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soussouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_soussouscategorie_id", referencedColumnName="id")
     * })
     */
    private $newSoussouscategorie;

    /**
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    /**
     * @var \AppBundle\Entity\Soussouscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Soussouscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="old_soussouscategorie_id", referencedColumnName="id")
     * })
     */
    private $oldSoussouscategorie;

    /**
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_souscategorie_id", referencedColumnName="id")
     * })
     */
    private $newSouscategorie;

    /**
     * @var \AppBundle\Entity\Categorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_categorie_id", referencedColumnName="id")
     * })
     */
    private $newCategorie;

    /**
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="old_souscategorie_id", referencedColumnName="id")
     * })
     */
    private $oldSouscategorie;

    /**
     * @var \AppBundle\Entity\Categorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="old_categorie_id", referencedColumnName="id")
     * })
     */
    private $oldCategorie;



    /**
     * Set etape
     *
     * @param string $etape
     *
     * @return LogCegj
     */
    public function setEtape($etape)
    {
        $this->etape = $etape;

        return $this;
    }

    /**
     * Get etape
     *
     * @return string
     */
    public function getEtape()
    {
        return $this->etape;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return LogCegj
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * Set newSoussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $newSoussouscategorie
     *
     * @return LogCegj
     */
    public function setNewSoussouscategorie(\AppBundle\Entity\Soussouscategorie $newSoussouscategorie = null)
    {
        $this->newSoussouscategorie = $newSoussouscategorie;

        return $this;
    }

    /**
     * Get newSoussouscategorie
     *
     * @return \AppBundle\Entity\Soussouscategorie
     */
    public function getNewSoussouscategorie()
    {
        return $this->newSoussouscategorie;
    }

    /**
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return LogCegj
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return LogCegj
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set oldSoussouscategorie
     *
     * @param \AppBundle\Entity\Soussouscategorie $oldSoussouscategorie
     *
     * @return LogCegj
     */
    public function setOldSoussouscategorie(\AppBundle\Entity\Soussouscategorie $oldSoussouscategorie = null)
    {
        $this->oldSoussouscategorie = $oldSoussouscategorie;

        return $this;
    }

    /**
     * Get oldSoussouscategorie
     *
     * @return \AppBundle\Entity\Soussouscategorie
     */
    public function getOldSoussouscategorie()
    {
        return $this->oldSoussouscategorie;
    }

    /**
     * Set newSouscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $newSouscategorie
     *
     * @return LogCegj
     */
    public function setNewSouscategorie(\AppBundle\Entity\Souscategorie $newSouscategorie = null)
    {
        $this->newSouscategorie = $newSouscategorie;

        return $this;
    }

    /**
     * Get newSouscategorie
     *
     * @return \AppBundle\Entity\Souscategorie
     */
    public function getNewSouscategorie()
    {
        return $this->newSouscategorie;
    }

    /**
     * Set newCategorie
     *
     * @param \AppBundle\Entity\Categorie $newCategorie
     *
     * @return LogCegj
     */
    public function setNewCategorie(\AppBundle\Entity\Categorie $newCategorie = null)
    {
        $this->newCategorie = $newCategorie;

        return $this;
    }

    /**
     * Get newCategorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getNewCategorie()
    {
        return $this->newCategorie;
    }

    /**
     * Set oldSouscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $oldSouscategorie
     *
     * @return LogCegj
     */
    public function setOldSouscategorie(\AppBundle\Entity\Souscategorie $oldSouscategorie = null)
    {
        $this->oldSouscategorie = $oldSouscategorie;

        return $this;
    }

    /**
     * Get oldSouscategorie
     *
     * @return \AppBundle\Entity\Souscategorie
     */
    public function getOldSouscategorie()
    {
        return $this->oldSouscategorie;
    }

    /**
     * Set oldCategorie
     *
     * @param \AppBundle\Entity\Categorie $oldCategorie
     *
     * @return LogCegj
     */
    public function setOldCategorie(\AppBundle\Entity\Categorie $oldCategorie = null)
    {
        $this->oldCategorie = $oldCategorie;

        return $this;
    }

    /**
     * Get oldCategorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getOldCategorie()
    {
        return $this->oldCategorie;
    }
}
