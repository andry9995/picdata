<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageFlague
 *
 * @ORM\Table(name="image_flague", indexes={@ORM\Index(name="fk_image_flague_tiers_idx", columns={"tiers_id"}), @ORM\Index(name="fk_image_flague_pcc_idx", columns={"pcc_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageFlagueRepository")
 */
class ImageFlague
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=false)
     */
    private $dateCreation = '0000-00-00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_devalidation", type="date", nullable=true)
     */
    private $dateDevalidation;

    /**
     * @var string
     *
     * @ORM\Column(name="lettre", type="string", length=5, nullable=true)
     */
    private $lettre;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Tiers
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tiers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiers_id", referencedColumnName="id")
     * })
     */
    private $tiers;

    /**
     * @var \AppBundle\Entity\Pcc
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pcc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pcc_id", referencedColumnName="id")
     * })
     */
    private $pcc;



    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return ImageFlague
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateDevalidation
     *
     * @param \DateTime $dateDevalidation
     *
     * @return ImageFlague
     */
    public function setDateDevalidation($dateDevalidation)
    {
        $this->dateDevalidation = $dateDevalidation;

        return $this;
    }

    /**
     * Get dateDevalidation
     *
     * @return \DateTime
     */
    public function getDateDevalidation()
    {
        return $this->dateDevalidation;
    }

    /**
     * Set lettre
     *
     * @param string $lettre
     *
     * @return ImageFlague
     */
    public function setLettre($lettre)
    {
        $this->lettre = $lettre;

        return $this;
    }

    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre()
    {
        return $this->lettre;
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
     * Set tiers
     *
     * @param \AppBundle\Entity\Tiers $tiers
     *
     * @return ImageFlague
     */
    public function setTiers(\AppBundle\Entity\Tiers $tiers = null)
    {
        $this->tiers = $tiers;

        return $this;
    }

    /**
     * Get tiers
     *
     * @return \AppBundle\Entity\Tiers
     */
    public function getTiers()
    {
        return $this->tiers;
    }

    /**
     * Set pcc
     *
     * @param \AppBundle\Entity\Pcc $pcc
     *
     * @return ImageFlague
     */
    public function setPcc(\AppBundle\Entity\Pcc $pcc = null)
    {
        $this->pcc = $pcc;

        return $this;
    }

    /**
     * Get pcc
     *
     * @return \AppBundle\Entity\Pcc
     */
    public function getPcc()
    {
        return $this->pcc;
    }
}
