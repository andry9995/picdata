<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DownParamDirectory
 *
 * @ORM\Table(name="down_param_directory")
 * @ORM\Entity
 */
class DownParamDirectory
{
    /**
     * @var string
     *
     * @ORM\Column(name="dirdest", type="string", length=250, nullable=true)
     */
    private $dirdest;

    /**
     * @var string
     *
     * @ORM\Column(name="dirtemp", type="string", length=250, nullable=true)
     */
    private $dirtemp;

    /**
     * @var string
     *
     * @ORM\Column(name="dirlog", type="string", length=250, nullable=true)
     */
    private $dirlog;

    /**
     * @var string
     *
     * @ORM\Column(name="diraffecter", type="string", length=250, nullable=true)
     */
    private $diraffecter;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
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
     * Set dirdest
     *
     * @param string $dirdest
     *
     * @return DownParamDirectory
     */
    public function setDirdest($dirdest)
    {
        $this->dirdest = $dirdest;

        return $this;
    }

    /**
     * Get dirdest
     *
     * @return string
     */
    public function getDirdest()
    {
        return $this->dirdest;
    }

    /**
     * Set dirtemp
     *
     * @param string $dirtemp
     *
     * @return DownParamDirectory
     */
    public function setDirtemp($dirtemp)
    {
        $this->dirtemp = $dirtemp;

        return $this;
    }

    /**
     * Get dirtemp
     *
     * @return string
     */
    public function getDirtemp()
    {
        return $this->dirtemp;
    }

    /**
     * Set dirlog
     *
     * @param string $dirlog
     *
     * @return DownParamDirectory
     */
    public function setDirlog($dirlog)
    {
        $this->dirlog = $dirlog;

        return $this;
    }

    /**
     * Get dirlog
     *
     * @return string
     */
    public function getDirlog()
    {
        return $this->dirlog;
    }

    /**
     * Set diraffecter
     *
     * @param string $diraffecter
     *
     * @return DownParamDirectory
     */
    public function setDiraffecter($diraffecter)
    {
        $this->diraffecter = $diraffecter;

        return $this;
    }

    /**
     * Get diraffecter
     *
     * @return string
     */
    public function getDiraffecter()
    {
        return $this->diraffecter;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return DownParamDirectory
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
