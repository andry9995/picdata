<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatRegimeFiscal
 *
 * @ORM\Table(name="etat_regime_fiscal", uniqueConstraints={@ORM\UniqueConstraint(name="unique_etat_regime_fiscal", columns={"etat_id", "dossier_id"})}, indexes={@ORM\Index(name="fk_etat_regime_fiscal_etat1_idx", columns={"etat_id"}), @ORM\Index(name="fk_etat_regime_fiscal_client1_idx", columns={"client_id"}), @ORM\Index(name="fk_etat_regime_fiscal_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_etat_regime_fiscal_regime_fiscal1_idx", columns={"regime_fiscal_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EtatRegimeFiscalRepository")
 */
class EtatRegimeFiscal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="row_number", type="integer", nullable=false)
     */
    private $rowNumber = '5';

    /**
     * @var integer
     *
     * @ORM\Column(name="col_number", type="integer", nullable=false)
     */
    private $colNumber = '10';

    /**
     * @var integer
     *
     * @ORM\Column(name="type_societe", type="integer", nullable=false)
     */
    private $typeSociete = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="valider", type="integer", nullable=false)
     */
    private $valider = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="periode", type="integer", nullable=false)
     */
    private $periode = '15';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Etat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Etat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_id", referencedColumnName="id")
     * })
     */
    private $etat;

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @var \AppBundle\Entity\RegimeFiscal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RegimeFiscal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="regime_fiscal_id", referencedColumnName="id")
     * })
     */
    private $regimeFiscal;



    /**
     * Set rowNumber
     *
     * @param integer $rowNumber
     *
     * @return EtatRegimeFiscal
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    /**
     * Get rowNumber
     *
     * @return integer
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Set colNumber
     *
     * @param integer $colNumber
     *
     * @return EtatRegimeFiscal
     */
    public function setColNumber($colNumber)
    {
        $this->colNumber = $colNumber;

        return $this;
    }

    /**
     * Get colNumber
     *
     * @return integer
     */
    public function getColNumber()
    {
        return $this->colNumber;
    }

    /**
     * Set typeSociete
     *
     * @param integer $typeSociete
     *
     * @return EtatRegimeFiscal
     */
    public function setTypeSociete($typeSociete)
    {
        $this->typeSociete = $typeSociete;

        return $this;
    }

    /**
     * Get typeSociete
     *
     * @return integer
     */
    public function getTypeSociete()
    {
        return $this->typeSociete;
    }

    /**
     * Set valider
     *
     * @param integer $valider
     *
     * @return EtatRegimeFiscal
     */
    public function setValider($valider)
    {
        $this->valider = $valider;

        return $this;
    }

    /**
     * Get valider
     *
     * @return integer
     */
    public function getValider()
    {
        return $this->valider;
    }

    /**
     * Set periode
     *
     * @param integer $periode
     *
     * @return EtatRegimeFiscal
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;

        return $this;
    }

    /**
     * Get periode
     *
     * @return integer
     */
    public function getPeriode()
    {
        return $this->periode;
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
     * Set etat
     *
     * @param \AppBundle\Entity\Etat $etat
     *
     * @return EtatRegimeFiscal
     */
    public function setEtat(\AppBundle\Entity\Etat $etat = null)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return \AppBundle\Entity\Etat
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return EtatRegimeFiscal
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return EtatRegimeFiscal
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set regimeFiscal
     *
     * @param \AppBundle\Entity\RegimeFiscal $regimeFiscal
     *
     * @return EtatRegimeFiscal
     */
    public function setRegimeFiscal(\AppBundle\Entity\RegimeFiscal $regimeFiscal = null)
    {
        $this->regimeFiscal = $regimeFiscal;

        return $this;
    }

    /**
     * Get regimeFiscal
     *
     * @return \AppBundle\Entity\RegimeFiscal
     */
    public function getRegimeFiscal()
    {
        return $this->regimeFiscal;
    }
}
