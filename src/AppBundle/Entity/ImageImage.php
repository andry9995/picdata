<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageImage
 *
 * @ORM\Table(name="image_image", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_image_image", columns={"image_id", "image_id_autre"})}, indexes={@ORM\Index(name="fk_image_has_image_image2_idx", columns={"image_id_autre"}), @ORM\Index(name="fk_image_has_image_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_image_image_releve_idx", columns={"releve_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageImageRepository")
 */
class ImageImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="image_type", type="integer", nullable=true)
     */
    private $imageType = '1';

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
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id_autre", referencedColumnName="id")
     * })
     */
    private $imageAutre;

    /**
     * @var \AppBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



    /**
     * Set imageType
     *
     * @param integer $imageType
     *
     * @return ImageImage
     */
    public function setImageType($imageType)
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType
     *
     * @return integer
     */
    public function getImageType()
    {
        return $this->imageType;
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
     * @return ImageImage
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
     * Set imageAutre
     *
     * @param \AppBundle\Entity\Image $imageAutre
     *
     * @return ImageImage
     */
    public function setImageAutre(\AppBundle\Entity\Image $imageAutre = null)
    {
        $this->imageAutre = $imageAutre;

        return $this;
    }

    /**
     * Get imageAutre
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImageAutre()
    {
        return $this->imageAutre;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ImageImage
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
