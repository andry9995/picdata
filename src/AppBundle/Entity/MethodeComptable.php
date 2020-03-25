<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MethodeComptable
 *
 * @ORM\Table(name="methode_comptable", indexes={@ORM\Index(name="fk_methode_comptable_convention_comptable1_idx", columns={"convention_comptable_id"}), @ORM\Index(name="fk_methode_comptable_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_methode_comptable_methode_suivi_cheque_idx", columns={"methode_suivi_cheque_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MethodeComptableRepository")
 */
class MethodeComptable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="vente", type="integer", nullable=true)
     */
    private $vente;

    /**
     * @var integer
     *
     * @ORM\Column(name="achat", type="integer", nullable=true)
     */
    private $achat;

    /**
     * @var integer
     *
     * @ORM\Column(name="banque", type="integer", nullable=true)
     */
    private $banque;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_banque", type="integer", nullable=true)
     */
    private $nbBanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie_od_paye", type="integer", nullable=true)
     */
    private $saisieOdPaye;

    /**
     * @var integer
     *
     * @ORM\Column(name="analytique", type="integer", nullable=true)
     */
    private $analytique;

    /**
     * @var integer
     *
     * @ORM\Column(name="tenue_comptablilite", type="integer", nullable=true)
     */
    private $tenueComptablilite;

    /**
     * @var integer
     *
     * @ORM\Column(name="demande_piece_manquante", type="integer", nullable=true)
     */
    private $demandePieceManquante;

    /**
     * @var integer
     *
     * @ORM\Column(name="vente_comptoir", type="integer", nullable=true)
     */
    private $venteComptoir;

    /**
     * @var integer
     *
     * @ORM\Column(name="vente_facture", type="integer", nullable=true)
     */
    private $venteFacture;

    /**
     * @var integer
     *
     * @ORM\Column(name="rapprochement_banque", type="integer", nullable=true)
     */
    private $rapprochementBanque;

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
     * @var \AppBundle\Entity\ConventionComptable
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ConventionComptable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="convention_comptable_id", referencedColumnName="id")
     * })
     */
    private $conventionComptable;

    /**
     * @var \AppBundle\Entity\MethodeSuiviCheque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MethodeSuiviCheque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="methode_suivi_cheque_id", referencedColumnName="id")
     * })
     */
    private $methodeSuiviCheque;



    /**
     * Set vente
     *
     * @param integer $vente
     *
     * @return MethodeComptable
     */
    public function setVente($vente)
    {
        $this->vente = $vente;

        return $this;
    }

    /**
     * Get vente
     *
     * @return integer
     */
    public function getVente()
    {
        return $this->vente;
    }

    /**
     * Set achat
     *
     * @param integer $achat
     *
     * @return MethodeComptable
     */
    public function setAchat($achat)
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return integer
     */
    public function getAchat()
    {
        return $this->achat;
    }

    /**
     * Set banque
     *
     * @param integer $banque
     *
     * @return MethodeComptable
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return integer
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set nbBanque
     *
     * @param integer $nbBanque
     *
     * @return MethodeComptable
     */
    public function setNbBanque($nbBanque)
    {
        $this->nbBanque = $nbBanque;

        return $this;
    }

    /**
     * Get nbBanque
     *
     * @return integer
     */
    public function getNbBanque()
    {
        return $this->nbBanque;
    }

    /**
     * Set saisieOdPaye
     *
     * @param integer $saisieOdPaye
     *
     * @return MethodeComptable
     */
    public function setSaisieOdPaye($saisieOdPaye)
    {
        $this->saisieOdPaye = $saisieOdPaye;

        return $this;
    }

    /**
     * Get saisieOdPaye
     *
     * @return integer
     */
    public function getSaisieOdPaye()
    {
        return $this->saisieOdPaye;
    }

    /**
     * Set analytique
     *
     * @param integer $analytique
     *
     * @return MethodeComptable
     */
    public function setAnalytique($analytique)
    {
        $this->analytique = $analytique;

        return $this;
    }

    /**
     * Get analytique
     *
     * @return integer
     */
    public function getAnalytique()
    {
        return $this->analytique;
    }

    /**
     * Set tenueComptablilite
     *
     * @param integer $tenueComptablilite
     *
     * @return MethodeComptable
     */
    public function setTenueComptablilite($tenueComptablilite)
    {
        $this->tenueComptablilite = $tenueComptablilite;

        return $this;
    }

    /**
     * Get tenueComptablilite
     *
     * @return integer
     */
    public function getTenueComptablilite()
    {
        return $this->tenueComptablilite;
    }

    /**
     * Set demandePieceManquante
     *
     * @param integer $demandePieceManquante
     *
     * @return MethodeComptable
     */
    public function setDemandePieceManquante($demandePieceManquante)
    {
        $this->demandePieceManquante = $demandePieceManquante;

        return $this;
    }

    /**
     * Get demandePieceManquante
     *
     * @return integer
     */
    public function getDemandePieceManquante()
    {
        return $this->demandePieceManquante;
    }

    /**
     * Set venteComptoir
     *
     * @param integer $venteComptoir
     *
     * @return MethodeComptable
     */
    public function setVenteComptoir($venteComptoir)
    {
        $this->venteComptoir = $venteComptoir;

        return $this;
    }

    /**
     * Get venteComptoir
     *
     * @return integer
     */
    public function getVenteComptoir()
    {
        return $this->venteComptoir;
    }

    /**
     * Set venteFacture
     *
     * @param integer $venteFacture
     *
     * @return MethodeComptable
     */
    public function setVenteFacture($venteFacture)
    {
        $this->venteFacture = $venteFacture;

        return $this;
    }

    /**
     * Get venteFacture
     *
     * @return integer
     */
    public function getVenteFacture()
    {
        return $this->venteFacture;
    }

    /**
     * Set rapprochementBanque
     *
     * @param integer $rapprochementBanque
     *
     * @return MethodeComptable
     */
    public function setRapprochementBanque($rapprochementBanque)
    {
        $this->rapprochementBanque = $rapprochementBanque;

        return $this;
    }

    /**
     * Get rapprochementBanque
     *
     * @return integer
     */
    public function getRapprochementBanque()
    {
        return $this->rapprochementBanque;
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
     * @return MethodeComptable
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
     * Set conventionComptable
     *
     * @param \AppBundle\Entity\ConventionComptable $conventionComptable
     *
     * @return MethodeComptable
     */
    public function setConventionComptable(\AppBundle\Entity\ConventionComptable $conventionComptable = null)
    {
        $this->conventionComptable = $conventionComptable;

        return $this;
    }

    /**
     * Get conventionComptable
     *
     * @return \AppBundle\Entity\ConventionComptable
     */
    public function getConventionComptable()
    {
        return $this->conventionComptable;
    }

    /**
     * Set methodeSuiviCheque
     *
     * @param \AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque
     *
     * @return MethodeComptable
     */
    public function setMethodeSuiviCheque(\AppBundle\Entity\MethodeSuiviCheque $methodeSuiviCheque = null)
    {
        $this->methodeSuiviCheque = $methodeSuiviCheque;

        return $this;
    }

    /**
     * Get methodeSuiviCheque
     *
     * @return \AppBundle\Entity\MethodeSuiviCheque
     */
    public function getMethodeSuiviCheque()
    {
        return $this->methodeSuiviCheque;
    }
}
