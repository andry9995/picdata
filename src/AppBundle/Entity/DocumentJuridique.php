<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentJuridique
 *
 * @ORM\Table(name="document_juridique", indexes={@ORM\Index(name="fk_document_juridique_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class DocumentJuridique
{
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="text", length=65535, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="kbis", type="text", length=65535, nullable=true)
     */
    private $kbis;

    /**
     * @var string
     *
     * @ORM\Column(name="baux", type="text", length=65535, nullable=true)
     */
    private $baux;

    /**
     * @var string
     *
     * @ORM\Column(name="emprunt", type="text", length=65535, nullable=true)
     */
    private $emprunt;

    /**
     * @var string
     *
     * @ORM\Column(name="assurance", type="text", length=65535, nullable=true)
     */
    private $assurance;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_baux", type="text", length=65535, nullable=true)
     */
    private $creditBaux;

    /**
     * @var string
     *
     * @ORM\Column(name="loc_mob", type="text", length=65535, nullable=true)
     */
    private $locMob;

    /**
     * @var string
     *
     * @ORM\Column(name="autre", type="text", length=65535, nullable=true)
     */
    private $autre;

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
     * Set status
     *
     * @param string $status
     *
     * @return DocumentJuridique
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set kbis
     *
     * @param string $kbis
     *
     * @return DocumentJuridique
     */
    public function setKbis($kbis)
    {
        $this->kbis = $kbis;

        return $this;
    }

    /**
     * Get kbis
     *
     * @return string
     */
    public function getKbis()
    {
        return $this->kbis;
    }

    /**
     * Set baux
     *
     * @param string $baux
     *
     * @return DocumentJuridique
     */
    public function setBaux($baux)
    {
        $this->baux = $baux;

        return $this;
    }

    /**
     * Get baux
     *
     * @return string
     */
    public function getBaux()
    {
        return $this->baux;
    }

    /**
     * Set emprunt
     *
     * @param string $emprunt
     *
     * @return DocumentJuridique
     */
    public function setEmprunt($emprunt)
    {
        $this->emprunt = $emprunt;

        return $this;
    }

    /**
     * Get emprunt
     *
     * @return string
     */
    public function getEmprunt()
    {
        return $this->emprunt;
    }

    /**
     * Set assurance
     *
     * @param string $assurance
     *
     * @return DocumentJuridique
     */
    public function setAssurance($assurance)
    {
        $this->assurance = $assurance;

        return $this;
    }

    /**
     * Get assurance
     *
     * @return string
     */
    public function getAssurance()
    {
        return $this->assurance;
    }

    /**
     * Set creditBaux
     *
     * @param string $creditBaux
     *
     * @return DocumentJuridique
     */
    public function setCreditBaux($creditBaux)
    {
        $this->creditBaux = $creditBaux;

        return $this;
    }

    /**
     * Get creditBaux
     *
     * @return string
     */
    public function getCreditBaux()
    {
        return $this->creditBaux;
    }

    /**
     * Set locMob
     *
     * @param string $locMob
     *
     * @return DocumentJuridique
     */
    public function setLocMob($locMob)
    {
        $this->locMob = $locMob;

        return $this;
    }

    /**
     * Get locMob
     *
     * @return string
     */
    public function getLocMob()
    {
        return $this->locMob;
    }

    /**
     * Set autre
     *
     * @param string $autre
     *
     * @return DocumentJuridique
     */
    public function setAutre($autre)
    {
        $this->autre = $autre;

        return $this;
    }

    /**
     * Get autre
     *
     * @return string
     */
    public function getAutre()
    {
        return $this->autre;
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
     * @return DocumentJuridique
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
