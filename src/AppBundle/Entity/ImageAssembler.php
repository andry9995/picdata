<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageAssembler
 *
 * @ORM\Table(name="image_assembler", indexes={@ORM\Index(name="fk_image_id_idx", columns={"image_id"})})
 * @ORM\Entity
 */
class ImageAssembler
{
    /**
     * @var string
     *
     * @ORM\Column(name="page_sequence", type="string", length=100, nullable=false)
     */
    private $pageSequence;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set pageSequence
     *
     * @param string $pageSequence
     *
     * @return ImageAssembler
     */
    public function setPageSequence($pageSequence)
    {
        $this->pageSequence = $pageSequence;

        return $this;
    }

    /**
     * Get pageSequence
     *
     * @return string
     */
    public function getPageSequence()
    {
        return $this->pageSequence;
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
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ImageAssembler
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
