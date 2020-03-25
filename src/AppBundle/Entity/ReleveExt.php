<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveExt
 *
 * @ORM\Table(name="releve_ext", indexes={@ORM\Index(name="fk_releve_ext_releve1_idx", columns={"releve_id"}), @ORM\Index(name="fk_releve_ext_cle_dossier_ext1_idx", columns={"cle_dossier_ext_id"}), @ORM\Index(name="fk_releve_ext_image_flague1_idx", columns={"image_flague_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveExtRepository")
 */
class ReleveExt
{
    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $montant = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="non_lettrable", type="text", length=65535, nullable=false)
     */
    private $nonLettrable = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Releve
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Releve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="releve_id", referencedColumnName="id")
     * })
     */
    private $releve;

    /**
     * @var \AppBundle\Entity\ImageFlague
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ImageFlague")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_flague_id", referencedColumnName="id")
     * })
     */
    private $imageFlague;

    /**
     * @var \AppBundle\Entity\CleDossierExt
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CleDossierExt")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_dossier_ext_id", referencedColumnName="id")
     * })
     */
    private $cleDossierExt;



    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return ReleveExt
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set nonLettrable
     *
     * @param string $nonLettrable
     *
     * @return ReleveExt
     */
    public function setNonLettrable($nonLettrable)
    {
        $this->nonLettrable = $nonLettrable;

        return $this;
    }

    /**
     * Get nonLettrable
     *
     * @return string
     */
    public function getNonLettrable()
    {
        return $this->nonLettrable;
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
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return ReleveExt
     */
    public function setReleve(\AppBundle\Entity\Releve $releve = null)
    {
        $this->releve = $releve;

        return $this;
    }

    /**
     * Get releve
     *
     * @return \AppBundle\Entity\Releve
     */
    public function getReleve()
    {
        return $this->releve;
    }

    /**
     * Set imageFlague
     *
     * @param \AppBundle\Entity\ImageFlague $imageFlague
     *
     * @return ReleveExt
     */
    public function setImageFlague(\AppBundle\Entity\ImageFlague $imageFlague = null)
    {
        $this->imageFlague = $imageFlague;

        return $this;
    }

    /**
     * Get imageFlague
     *
     * @return \AppBundle\Entity\ImageFlague
     */
    public function getImageFlague()
    {
        return $this->imageFlague;
    }

    /**
     * Set cleDossierExt
     *
     * @param \AppBundle\Entity\CleDossierExt $cleDossierExt
     *
     * @return ReleveExt
     */
    public function setCleDossierExt(\AppBundle\Entity\CleDossierExt $cleDossierExt = null)
    {
        $this->cleDossierExt = $cleDossierExt;

        return $this;
    }

    /**
     * Get cleDossierExt
     *
     * @return \AppBundle\Entity\CleDossierExt
     */
    public function getCleDossierExt()
    {
        return $this->cleDossierExt;
    }
}
