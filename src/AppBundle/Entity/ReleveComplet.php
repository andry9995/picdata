<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveComplet
 *
 * @ORM\Table(name="releve_complet", indexes={@ORM\Index(name="fk_releve_complet_banque_compte1_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_releve_complet_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class ReleveComplet
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;

    /**
     * @var string
     *
     * @ORM\Column(name="mois", type="text", length=65535, nullable=true)
     */
    private $mois;

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
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;



    /**
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return ReleveComplet
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
     * Set mois
     *
     * @param string $mois
     *
     * @return ReleveComplet
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
     * @return ReleveComplet
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

    /**
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return ReleveComplet
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
}
