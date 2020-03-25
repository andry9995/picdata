<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestNumerotation
 *
 * @ORM\Table(name="test_numerotation")
 * @ORM\Entity
 */
class TestNumerotation
{
    /**
     * @var string
     *
     * @ORM\Column(name="last_num", type="string", length=250, nullable=true)
     */
    private $lastNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="local", type="integer", nullable=true)
     */
    private $local;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image", type="integer", nullable=true)
     */
    private $idImage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set lastNum
     *
     * @param string $lastNum
     *
     * @return TestNumerotation
     */
    public function setLastNum($lastNum)
    {
        $this->lastNum = $lastNum;

        return $this;
    }

    /**
     * Get lastNum
     *
     * @return string
     */
    public function getLastNum()
    {
        return $this->lastNum;
    }

    /**
     * Set local
     *
     * @param integer $local
     *
     * @return TestNumerotation
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return integer
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set idImage
     *
     * @param integer $idImage
     *
     * @return TestNumerotation
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;

        return $this;
    }

    /**
     * Get idImage
     *
     * @return integer
     */
    public function getIdImage()
    {
        return $this->idImage;
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
