<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationDemande
 *
 * @ORM\Table(name="prestation_demande", indexes={@ORM\Index(name="fk_prestation_demande_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationDemandeRepository")
 */
class PrestationDemande
{
    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=true)
     */
    private $tva;

    /**
     * @var integer
     *
     * @ORM\Column(name="accompte_is_solde", type="integer", nullable=true)
     */
    private $accompteIsSolde;

    /**
     * @var integer
     *
     * @ORM\Column(name="liasse_fiscale", type="integer", nullable=true)
     */
    private $liasseFiscale;

    /**
     * @var integer
     *
     * @ORM\Column(name="cice", type="integer", nullable=true)
     */
    private $cice;

    /**
     * @var integer
     *
     * @ORM\Column(name="cvae", type="integer", nullable=true)
     */
    private $cvae;

    /**
     * @var integer
     *
     * @ORM\Column(name="tvts", type="integer", nullable=true)
     */
    private $tvts;

    /**
     * @var integer
     *
     * @ORM\Column(name="das2", type="integer", nullable=true)
     */
    private $das2;

    /**
     * @var integer
     *
     * @ORM\Column(name="cfe", type="integer", nullable=true)
     */
    private $cfe;

    /**
     * @var integer
     *
     * @ORM\Column(name="dividende", type="integer", nullable=true)
     */
    private $dividende;

    /**
     * @var integer
     *
     * @ORM\Column(name="situation", type="integer", nullable=true)
     */
    private $situation;

    /**
     * @var integer
     *
     * @ORM\Column(name="indicateur", type="integer", nullable=true)
     */
    private $indicateur;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer", nullable=true)
     */
    private $budget;

    /**
     * @var integer
     *
     * @ORM\Column(name="tableau_bord", type="integer", nullable=true)
     */
    private $tableauBord;

    /**
     * @var integer
     *
     * @ORM\Column(name="rapport_gestion", type="integer", nullable=true)
     */
    private $rapportGestion;

    /**
     * @var integer
     *
     * @ORM\Column(name="assemblee_ordinaire", type="integer", nullable=true)
     */
    private $assembleeOrdinaire;

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
     * Set tva
     *
     * @param integer $tva
     *
     * @return PrestationDemande
     */
    public function setTva($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return integer
     */
    public function getTva()
    {
        return $this->tva;
    }

    /**
     * Set accompteIsSolde
     *
     * @param integer $accompteIsSolde
     *
     * @return PrestationDemande
     */
    public function setAccompteIsSolde($accompteIsSolde)
    {
        $this->accompteIsSolde = $accompteIsSolde;

        return $this;
    }

    /**
     * Get accompteIsSolde
     *
     * @return integer
     */
    public function getAccompteIsSolde()
    {
        return $this->accompteIsSolde;
    }

    /**
     * Set liasseFiscale
     *
     * @param integer $liasseFiscale
     *
     * @return PrestationDemande
     */
    public function setLiasseFiscale($liasseFiscale)
    {
        $this->liasseFiscale = $liasseFiscale;

        return $this;
    }

    /**
     * Get liasseFiscale
     *
     * @return integer
     */
    public function getLiasseFiscale()
    {
        return $this->liasseFiscale;
    }

    /**
     * Set cice
     *
     * @param integer $cice
     *
     * @return PrestationDemande
     */
    public function setCice($cice)
    {
        $this->cice = $cice;

        return $this;
    }

    /**
     * Get cice
     *
     * @return integer
     */
    public function getCice()
    {
        return $this->cice;
    }

    /**
     * Set cvae
     *
     * @param integer $cvae
     *
     * @return PrestationDemande
     */
    public function setCvae($cvae)
    {
        $this->cvae = $cvae;

        return $this;
    }

    /**
     * Get cvae
     *
     * @return integer
     */
    public function getCvae()
    {
        return $this->cvae;
    }

    /**
     * Set tvts
     *
     * @param integer $tvts
     *
     * @return PrestationDemande
     */
    public function setTvts($tvts)
    {
        $this->tvts = $tvts;

        return $this;
    }

    /**
     * Get tvts
     *
     * @return integer
     */
    public function getTvts()
    {
        return $this->tvts;
    }

    /**
     * Set das2
     *
     * @param integer $das2
     *
     * @return PrestationDemande
     */
    public function setDas2($das2)
    {
        $this->das2 = $das2;

        return $this;
    }

    /**
     * Get das2
     *
     * @return integer
     */
    public function getDas2()
    {
        return $this->das2;
    }

    /**
     * Set cfe
     *
     * @param integer $cfe
     *
     * @return PrestationDemande
     */
    public function setCfe($cfe)
    {
        $this->cfe = $cfe;

        return $this;
    }

    /**
     * Get cfe
     *
     * @return integer
     */
    public function getCfe()
    {
        return $this->cfe;
    }

    /**
     * Set dividende
     *
     * @param integer $dividende
     *
     * @return PrestationDemande
     */
    public function setDividende($dividende)
    {
        $this->dividende = $dividende;

        return $this;
    }

    /**
     * Get dividende
     *
     * @return integer
     */
    public function getDividende()
    {
        return $this->dividende;
    }

    /**
     * Set situation
     *
     * @param integer $situation
     *
     * @return PrestationDemande
     */
    public function setSituation($situation)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get situation
     *
     * @return integer
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * Set indicateur
     *
     * @param integer $indicateur
     *
     * @return PrestationDemande
     */
    public function setIndicateur($indicateur)
    {
        $this->indicateur = $indicateur;

        return $this;
    }

    /**
     * Get indicateur
     *
     * @return integer
     */
    public function getIndicateur()
    {
        return $this->indicateur;
    }

    /**
     * Set budget
     *
     * @param integer $budget
     *
     * @return PrestationDemande
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return integer
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set tableauBord
     *
     * @param integer $tableauBord
     *
     * @return PrestationDemande
     */
    public function setTableauBord($tableauBord)
    {
        $this->tableauBord = $tableauBord;

        return $this;
    }

    /**
     * Get tableauBord
     *
     * @return integer
     */
    public function getTableauBord()
    {
        return $this->tableauBord;
    }

    /**
     * Set rapportGestion
     *
     * @param integer $rapportGestion
     *
     * @return PrestationDemande
     */
    public function setRapportGestion($rapportGestion)
    {
        $this->rapportGestion = $rapportGestion;

        return $this;
    }

    /**
     * Get rapportGestion
     *
     * @return integer
     */
    public function getRapportGestion()
    {
        return $this->rapportGestion;
    }

    /**
     * Set assembleeOrdinaire
     *
     * @param integer $assembleeOrdinaire
     *
     * @return PrestationDemande
     */
    public function setAssembleeOrdinaire($assembleeOrdinaire)
    {
        $this->assembleeOrdinaire = $assembleeOrdinaire;

        return $this;
    }

    /**
     * Get assembleeOrdinaire
     *
     * @return integer
     */
    public function getAssembleeOrdinaire()
    {
        return $this->assembleeOrdinaire;
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
     * @return PrestationDemande
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
