<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BanqueObManquante
 *
 * @ORM\Table(name="banque_ob_manquante", indexes={@ORM\Index(name="fk_dossier_dossier_id_idx", columns={"dossier_id"}), @ORM\Index(name="fk_banque_banque_compte_id_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_souscategorie_souscategorie_id_idx", columns={"souscategorie_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanqueObManquanteRepository")
 */
class BanqueObManquante
{
    /**
     * @var string
     *
     * @ORM\Column(name="mois", type="text", length=65535, nullable=true)
     */
    private $mois;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;

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
     *   @ORM\JoinColumn(name="souscategorie_id", referencedColumnName="id")
     * })
     */
    private $souscategorie;

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
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;



    /**
     * Set mois
     *
     * @param string $mois
     *
     * @return BanqueObManquante
     */
    public function setMois($mois)
    {
        if (gettype($mois === "string")) {
            $this->mois = $mois;
        } elseif (gettype($mois) === "array") {
            $this->mois = json_encode($mois);
        }
        return $this;
    }

    /**
     * Get mois
     *
     * @return array
     */
    public function getMois()
    {
        return json_decode($this->mois);
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return BanqueObManquante
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
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
     * Set souscategorie
     *
     * @param \AppBundle\Entity\Souscategorie $souscategorie
     *
     * @return BanqueObManquante
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
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return BanqueObManquante
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return BanqueObManquante
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
