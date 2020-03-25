<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfCategorieDossier
 *
 * @ORM\Table(name="ndf_categorie_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"libelle", "dossier_id"})}, indexes={@ORM\Index(name="fk_ndf_categorie_dossier_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_categorie_dossier_pcc1_idx", columns={"pcc_charge"}), @ORM\Index(name="fkndf_categorie_dossier_pcc2_idx", columns={"pcc_tva"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NdfCategorieDossierRepository")
 */
class NdfCategorieDossier
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
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="tva_rec", type="integer", nullable=true)
     */
    private $tvaRec;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_tva", referencedColumnName="id")
     * })
     */
    private $pccTva;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_charge", referencedColumnName="id")
     * })
     */
    private $pccCharge;

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
     * @return NdfCategorieDossier
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
     * @return NdfCategorieDossier
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
     * Set status
     *
     * @param integer $status
     *
     * @return NdfCategorieDossier
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set tvaRec
     *
     * @param integer $tvaRec
     *
     * @return NdfCategorieDossier
     */
    public function setTvaRec($tvaRec)
    {
        $this->tvaRec = $tvaRec;

        return $this;
    }

    /**
     * Get tvaRec
     *
     * @return integer
     */
    public function getTvaRec()
    {
        return $this->tvaRec;
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
     * Set pccTva
     *
     * @param \AppBundle\Entity\Pcc $pccTva
     *
     * @return NdfCategorieDossier
     */
    public function setPccTva(\AppBundle\Entity\Pcc $pccTva = null)
    {
        $this->pccTva = $pccTva;

        return $this;
    }

    /**
     * Get pccTva
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccTva()
    {
        return $this->pccTva;
    }

    /**
     * Set pccCharge
     *
     * @param \AppBundle\Entity\Pcc $pccCharge
     *
     * @return NdfCategorieDossier
     */
    public function setPccCharge(\AppBundle\Entity\Pcc $pccCharge = null)
    {
        $this->pccCharge = $pccCharge;

        return $this;
    }

    /**
     * Get pccCharge
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPccCharge()
    {
        return $this->pccCharge;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfCategorieDossier
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
