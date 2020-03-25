<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageFtpVerif
 *
 * @ORM\Table(name="image_ftp_verif", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE_client_filename", columns={"client_id", "filename"})}, indexes={@ORM\Index(name="fk_image_ftp_verif_client1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageFtpVerifRepository")
 */
class ImageFtpVerif
{
    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=150, nullable=false)
     */
    private $filename;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reception", type="date", nullable=false)
     */
    private $dateReception;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_verif", type="date", nullable=true)
     */
    private $dateVerif;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return ImageFtpVerif
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ImageFtpVerif
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
     * Set dateReception
     *
     * @param \DateTime $dateReception
     *
     * @return ImageFtpVerif
     */
    public function setDateReception($dateReception)
    {
        $this->dateReception = $dateReception;

        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime
     */
    public function getDateReception()
    {
        return $this->dateReception;
    }

    /**
     * Set dateVerif
     *
     * @param \DateTime $dateVerif
     *
     * @return ImageFtpVerif
     */
    public function setDateVerif($dateVerif)
    {
        $this->dateVerif = $dateVerif;

        return $this;
    }

    /**
     * Get dateVerif
     *
     * @return \DateTime
     */
    public function getDateVerif()
    {
        return $this->dateVerif;
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return ImageFtpVerif
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
