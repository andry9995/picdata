<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErreurUploadItem
 *
 * @ORM\Table(name="erreur_upload_item", indexes={@ORM\Index(name="fk_erreur_upload_item_erreur_upload_idx", columns={"erreur_upload_id"})})
 * @ORM\Entity
 */
class ErreurUploadItem
{
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=45, nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="journal", type="string", length=45, nullable=true)
     */
    private $journal;

    /**
     * @var string
     *
     * @ORM\Column(name="client", type="string", length=45, nullable=true)
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=45, nullable=true)
     */
    private $fournisseur;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="erreur", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $erreur;

    /**
     * @var \AppBundle\Entity\ErreurUpload
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ErreurUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="erreur_upload_id", referencedColumnName="id")
     * })
     */
    private $erreurUpload;



    /**
     * Set image
     *
     * @param string $image
     *
     * @return ErreurUploadItem
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set journal
     *
     * @param string $journal
     *
     * @return ErreurUploadItem
     */
    public function setJournal($journal)
    {
        $this->journal = $journal;

        return $this;
    }

    /**
     * Get journal
     *
     * @return string
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * Set client
     *
     * @param string $client
     *
     * @return ErreurUploadItem
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return ErreurUploadItem
     */
    public function setFournisseur($fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ErreurUploadItem
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
     * Get erreur
     *
     * @return integer
     */
    public function getErreur()
    {
        return $this->erreur;
    }

    /**
     * Set erreurUpload
     *
     * @param \AppBundle\Entity\ErreurUpload $erreurUpload
     *
     * @return ErreurUploadItem
     */
    public function setErreurUpload(\AppBundle\Entity\ErreurUpload $erreurUpload = null)
    {
        $this->erreurUpload = $erreurUpload;

        return $this;
    }

    /**
     * Get erreurUpload
     *
     * @return \AppBundle\Entity\ErreurUpload
     */
    public function getErreurUpload()
    {
        return $this->erreurUpload;
    }
}
