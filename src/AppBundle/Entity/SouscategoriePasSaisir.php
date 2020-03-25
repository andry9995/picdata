<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SouscategoriePasSaisir
 *
 * @ORM\Table(name="souscategorie_pas_saisir", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_sous_categorie_dossier", columns={"dossier_id", "souscategorie"})}, indexes={@ORM\Index(name="fk_souscategorie_a_saisir_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_souscategorie_a_saisir_souscategorie1_idx", columns={"souscategorie"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SouscategoriePasSaisirRepository")
 */
class SouscategoriePasSaisir
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Souscategorie
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Souscategorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="souscategorie", referencedColumnName="id")
     * })
     */
    private $souscategorie;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set souscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $souscategorie
     *
     * @return SouscategoriePasSaisir
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

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return SouscategoriePasSaisir
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
