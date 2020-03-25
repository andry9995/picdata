<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageATraiter
 *
 * @ORM\Table(name="image_a_traiter", uniqueConstraints={@ORM\UniqueConstraint(name="image_UNIQUE", columns={"image_id"})}, indexes={@ORM\Index(name="fk_image_a_traiter_image1_idx", columns={"image_id"})})
 * @ORM\Entity
 */
class ImageATraiter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie1", type="integer", nullable=false)
     */
    private $saisie1 = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="saisie2", type="integer", nullable=false)
     */
    private $saisie2 = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="decouper", type="boolean", nullable=false)
     */
    private $decouper = '0';

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
     * Set status
     *
     * @param integer $status
     *
     * @return ImageATraiter
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
     * Set saisie1
     *
     * @param integer $saisie1
     *
     * @return ImageATraiter
     */
    public function setSaisie1($saisie1)
    {
        $this->saisie1 = $saisie1;

        return $this;
    }

    /**
     * Get saisie1
     *
     * @return integer
     */
    public function getSaisie1()
    {
        return $this->saisie1;
    }

    /**
     * Set saisie2
     *
     * @param integer $saisie2
     *
     * @return ImageATraiter
     */
    public function setSaisie2($saisie2)
    {
        $this->saisie2 = $saisie2;

        return $this;
    }

    /**
     * Get saisie2
     *
     * @return integer
     */
    public function getSaisie2()
    {
        return $this->saisie2;
    }

    /**
     * Set decouper
     *
     * @param boolean $decouper
     *
     * @return ImageATraiter
     */
    public function setDecouper($decouper)
    {
        $this->decouper = $decouper;

        return $this;
    }

    /**
     * Get decouper
     *
     * @return boolean
     */
    public function getDecouper()
    {
        return $this->decouper;
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
     * @return ImageATraiter
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
