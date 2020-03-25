<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactIndice
 *
 * @ORM\Table(name="fact_indice", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactIndiceRepository")
 */
class FactIndice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_indice", type="date", nullable=false)
     */
    private $dateIndice;

    /**
     * @var integer
     *
     * @ORM\Column(name="index_indice", type="integer", nullable=true)
     */
    private $indexIndice;

    /**
     * @var double
     *
     * @ORM\Column(name="indice", type="decimal", precision=10, scale=20, nullable=true)
     */
    private $indice;

    /**
     * @var double
     *
     * @ORM\Column(name="pourcentage", type="float", precision=4, scale=2, nullable=true)
     */
    private $pourcentage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set code
     *
     * @param integer $code
     *
     * @return FactIndice
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set dateIndice
     *
     * @param \DateTime $dateIndice
     *
     * @return FactIndice
     */
    public function setDateIndice($dateIndice)
    {
        $this->dateIndice = $dateIndice;

        return $this;
    }

    /**
     * Get dateIndice
     *
     * @return \DateTime
     */
    public function getDateIndice()
    {
        return $this->dateIndice;
    }

    /**
     * Set indexIndice
     *
     * @param integer $indexIndice
     *
     * @return FactIndice
     */
    public function setIndexIndice($indexIndice)
    {
        $this->indexIndice = $indexIndice;

        return $this;
    }

    /**
     * Get indexIndice
     *
     * @return integer
     */
    public function getIndexIndice()
    {
        return $this->indexIndice;
    }

    /**
     * Set indice
     *
     * @param float $indice
     *
     * @return FactIndice
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return float
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set pourcentage
     *
     * @param float $pourcentage
     *
     * @return FactIndice
     */
    public function setPourcentage($pourcentage)
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    /**
     * Get pourcentage
     *
     * @return float
     */
    public function getPourcentage()
    {
        return $this->pourcentage;
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
