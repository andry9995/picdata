<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="test")
 * @ORM\Entity
 */
class Test
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetest", type="date", nullable=true)
     */
    private $datetest;

    /**
     * @var string
     *
     * @ORM\Column(name="testcol", type="string", length=45, nullable=true)
     */
    private $testcol;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set datetest
     *
     * @param \DateTime $datetest
     *
     * @return Test
     */
    public function setDatetest($datetest)
    {
        $this->datetest = $datetest;

        return $this;
    }

    /**
     * Get datetest
     *
     * @return \DateTime
     */
    public function getDatetest()
    {
        return $this->datetest;
    }

    /**
     * Set testcol
     *
     * @param string $testcol
     *
     * @return Test
     */
    public function setTestcol($testcol)
    {
        $this->testcol = $testcol;

        return $this;
    }

    /**
     * Get testcol
     *
     * @return string
     */
    public function getTestcol()
    {
        return $this->testcol;
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
