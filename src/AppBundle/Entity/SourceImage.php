<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SourceImage
 *
 * @ORM\Table(name="source_image", uniqueConstraints={@ORM\UniqueConstraint(name="source_UNIQUE", columns={"source"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SourceImageRepository")
 */
class SourceImage
{
    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=45, nullable=false)
     */
    private $source;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set source
     *
     * @param string $source
     *
     * @return SourceImage
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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
