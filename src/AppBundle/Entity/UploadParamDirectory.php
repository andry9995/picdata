<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UploadParamDirectory
 *
 * @ORM\Table(name="upload_param_directory")
 * @ORM\Entity
 */
class UploadParamDirectory
{
    /**
     * @var string
     *
     * @ORM\Column(name="dirsource", type="string", length=250, nullable=false)
     */
    private $dirsource;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set dirsource
     *
     * @param string $dirsource
     *
     * @return UploadParamDirectory
     */
    public function setDirsource($dirsource)
    {
        $this->dirsource = $dirsource;

        return $this;
    }

    /**
     * Get dirsource
     *
     * @return string
     */
    public function getDirsource()
    {
        return $this->dirsource;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return UploadParamDirectory
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
