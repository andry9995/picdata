<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfNote
 *
 * @ORM\Table(name="ndf_note", indexes={@ORM\Index(name="fk_ndf_note_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_note_ndf_utilisateur1_idx", columns={"ndf_utilisateur_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NdfNoteRepository")
 */
class NdfNote
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=true)
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(name="mois", type="text", length=65535, nullable=true)
     */
    private $mois;

    /**
     * @var integer
     *
     * @ORM\Column(name="regul", type="integer", nullable=true)
     */
    private $regul = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_utilisateur_id", referencedColumnName="id")
     * })
     */
    private $ndfUtilisateur;

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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return NdfNote
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
     * Set description
     *
     * @param string $description
     *
     * @return NdfNote
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
     * Set annee
     *
     * @param integer $annee
     *
     * @return NdfNote
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set mois
     *
     * @param string $mois
     *
     * @return NdfNote
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return string
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * Set regul
     *
     * @param integer $regul
     *
     * @return NdfNote
     */
    public function setRegul($regul)
    {
        $this->regul = $regul;

        return $this;
    }

    /**
     * Get regul
     *
     * @return integer
     */
    public function getRegul()
    {
        return $this->regul;
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
     * Set ndfUtilisateur
     *
     * @param \AppBundle\Entity\NdfUtilisateur $ndfUtilisateur
     *
     * @return NdfNote
     */
    public function setNdfUtilisateur(\AppBundle\Entity\NdfUtilisateur $ndfUtilisateur = null)
    {
        $this->ndfUtilisateur = $ndfUtilisateur;

        return $this;
    }

    /**
     * Get ndfUtilisateur
     *
     * @return \AppBundle\Entity\NdfUtilisateur
     */
    public function getNdfUtilisateur()
    {
        return $this->ndfUtilisateur;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfNote
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
