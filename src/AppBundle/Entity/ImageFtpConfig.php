<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageFtpConfig
 *
 * @ORM\Table(name="image_ftp_config", indexes={@ORM\Index(name="fk_client_image_ftp_config1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageFtpConfigRepository")
 */
class ImageFtpConfig
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="multi", type="boolean", nullable=false)
     */
    private $multi = '0';

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
     * Set multi
     *
     * @param boolean $multi
     *
     * @return ImageFtpConfig
     */
    public function setMulti($multi)
    {
        $this->multi = $multi;

        return $this;
    }

    /**
     * Get multi
     *
     * @return boolean
     */
    public function getMulti()
    {
        return $this->multi;
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
     * @return ImageFtpConfig
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
