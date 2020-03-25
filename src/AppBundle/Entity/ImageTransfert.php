<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageTransfert
 *
 * @ORM\Table(name="image_transfert", indexes={@ORM\Index(name="fk_image_transfert_image1_idx", columns={"image_id"}), @ORM\Index(name="fk_image_transfert_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_image_transfert_lot1_idx", columns={"lot_id"}), @ORM\Index(name="fk_image_transfert_lot2_idx", columns={"lot_id_old"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageTransfertRepository")
 */
class ImageTransfert
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=false)
     */
    private $exercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="exercice_old", type="integer", nullable=false)
     */
    private $exerciceOld;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_tranfert", type="datetime", nullable=false)
     */
    private $dateTranfert;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var \AppBundle\Entity\Lot
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_id_old", referencedColumnName="id")
     * })
     */
    private $lotOld;

    /**
     * @var \AppBundle\Entity\Lot
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Lot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot_id", referencedColumnName="id")
     * })
     */
    private $lot;

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
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return ImageTransfert
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
    }

    /**
     * Set exerciceOld
     *
     * @param integer $exerciceOld
     *
     * @return ImageTransfert
     */
    public function setExerciceOld($exerciceOld)
    {
        $this->exerciceOld = $exerciceOld;

        return $this;
    }

    /**
     * Get exerciceOld
     *
     * @return integer
     */
    public function getExerciceOld()
    {
        return $this->exerciceOld;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ImageTransfert
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
     * Set dateTranfert
     *
     * @param \DateTime $dateTranfert
     *
     * @return ImageTransfert
     */
    public function setDateTranfert($dateTranfert)
    {
        $this->dateTranfert = $dateTranfert;

        return $this;
    }

    /**
     * Get dateTranfert
     *
     * @return \DateTime
     */
    public function getDateTranfert()
    {
        return $this->dateTranfert;
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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return ImageTransfert
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set lotOld
     *
     * @param \AppBundle\Entity\Lot $lotOld
     *
     * @return ImageTransfert
     */
    public function setLotOld(\AppBundle\Entity\Lot $lotOld = null)
    {
        $this->lotOld = $lotOld;

        return $this;
    }

    /**
     * Get lotOld
     *
     * @return \AppBundle\Entity\Lot
     */
    public function getLotOld()
    {
        return $this->lotOld;
    }

    /**
     * Set lot
     *
     * @param \AppBundle\Entity\Lot $lot
     *
     * @return ImageTransfert
     */
    public function setLot(\AppBundle\Entity\Lot $lot = null)
    {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return \AppBundle\Entity\Lot
     */
    public function getLot()
    {
        return $this->lot;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ImageTransfert
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
