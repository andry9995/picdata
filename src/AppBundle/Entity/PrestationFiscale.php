<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrestationFiscale
 *
 * @ORM\Table(name="prestation_fiscale", indexes={@ORM\Index(name="fk_prestation_fiscale_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrestationFiscaleRepository")
 */
class PrestationFiscale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="liasse", type="integer", nullable=true)
     */
    private $liasse;

    /**
     * @var integer
     *
     * @ORM\Column(name="acompte_is", type="integer", nullable=true)
     */
    private $acompteIs;

    /**
     * @var integer
     *
     * @ORM\Column(name="cfe", type="integer", nullable=true)
     */
    private $cfe;

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
     * @ORM\Column(name="cice", type="integer", nullable=true)
     */
    private $cice;

    /**
     * @var integer
     *
     * @ORM\Column(name="dividende", type="integer", nullable=true)
     */
    private $dividende;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva", type="integer", nullable=true)
     */
    private $tva;


    /**
     * @var integer
     *
     * @ORM\Column(name="deb", type="integer", nullable=true)
     */
    private $deb;

    /**
     * @var integer
     *
     * @ORM\Column(name="dej", type="integer", nullable=true)
     */
    private $dej;
    /**
     * @var integer
     *
     * @ORM\Column(name="teledeclaration_liasse", type="integer", nullable=true)
     */
    private $teledeclarationLiasse;

    /**
     * @var integer
     *
     * @ORM\Column(name="teledeclaration_autre", type="integer", nullable=true)
     */
    private $teledeclarationAutre;

    /**
     * @var string
     *
     * @ORM\Column(name="autres", type="string", length=100, nullable=true)
     */
    private $autres;

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
     * Set liasse
     *
     * @param integer $liasse
     *
     * @return PrestationFiscale
     */
    public function setLiasse($liasse)
    {
        $this->liasse = $liasse;

        return $this;
    }

    /**
     * Get liasse
     *
     * @return integer
     */
    public function getLiasse()
    {
        return $this->liasse;
    }

    /**
     * Set acompteIs
     *
     * @param integer $acompteIs
     *
     * @return PrestationFiscale
     */
    public function setAcompteIs($acompteIs)
    {
        $this->acompteIs = $acompteIs;

        return $this;
    }

    /**
     * Get acompteIs
     *
     * @return integer
     */
    public function getAcompteIs()
    {
        return $this->acompteIs;
    }

    /**
     * Set cfe
     *
     * @param integer $cfe
     *
     * @return PrestationFiscale
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
     * Set cvae
     *
     * @param integer $cvae
     *
     * @return PrestationFiscale
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
     * @return PrestationFiscale
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
     * @return PrestationFiscale
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
     * Set cice
     *
     * @param integer $cice
     *
     * @return PrestationFiscale
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
     * Set dividende
     *
     * @param integer $dividende
     *
     * @return PrestationFiscale
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
     * Set tva
     *
     * @param integer $tva
     *
     * @return PrestationFiscale
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
     * @param $deb
     * @return $this
     */
    public function setDeb($deb)
    {
        $this->deb = $deb;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeb()
    {
        return $this->deb;
    }

    /**
     * @param $dej
     * @return $this
     */
    public function setDej($dej)
    {
        $this->dej = $dej;

        return $this;
    }

    /**
     * @return int
     */
    public function getDej()
    {
        return $this->dej;
    }

    /**
     * Set teledeclarationLiasse
     *
     * @param integer $teledeclarationLiasse
     *
     * @return PrestationFiscale
     */
    public function setTeledeclarationLiasse($teledeclarationLiasse)
    {
        $this->teledeclarationLiasse = $teledeclarationLiasse;

        return $this;
    }

    /**
     * Get teledeclarationLiasse
     *
     * @return integer
     */
    public function getTeledeclarationLiasse()
    {
        return $this->teledeclarationLiasse;
    }

    /**
     * Set teledeclarationAutre
     *
     * @param integer $teledeclarationAutre
     *
     * @return PrestationFiscale
     */
    public function setTeledeclarationAutre($teledeclarationAutre)
    {
        $this->teledeclarationAutre = $teledeclarationAutre;

        return $this;
    }

    /**
     * Get teledeclarationAutre
     *
     * @return integer
     */
    public function getTeledeclarationAutre()
    {
        return $this->teledeclarationAutre;
    }

    /**
     * Set autres
     *
     * @param string $autres
     *
     * @return PrestationFiscale
     */
    public function setAutres($autres)
    {
        $this->autres = $autres;

        return $this;
    }

    /**
     * Get autres
     *
     * @return string
     */
    public function getAutres()
    {
        return $this->autres;
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
     * @return PrestationFiscale
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
