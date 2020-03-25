<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleSlave
 *
 * @ORM\Table(name="cle_slave", indexes={@ORM\Index(name="fk_cle_slave_cle1_idx", columns={"cle_id"}), @ORM\Index(name="fk_cle_slave_cle2_idx", columns={"cle_slave_id"}), @ORM\Index(name="fk_cle_slave_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleSlaveRepository")
 */
class CleSlave
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Dossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \AppBundle\Entity\Cle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_slave_id", referencedColumnName="id")
     * })
     */
    private $cleSlave;

    /**
     * @var \AppBundle\Entity\Cle
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_id", referencedColumnName="id")
     * })
     */
    private $cle;



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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return CleSlave
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }

    /**
     * Set cleSlave
     *
     * @param \AppBundle\Entity\Cle $cleSlave
     *
     * @return CleSlave
     */
    public function setCleSlave(\AppBundle\Entity\Cle $cleSlave = null)
    {
        $this->cleSlave = $cleSlave;

        return $this;
    }

    /**
     * Get cleSlave
     *
     * @return \AppBundle\Entity\Cle
     */
    public function getCleSlave()
    {
        return $this->cleSlave;
    }

    /**
     * Set cle
     *
     * @param \AppBundle\Entity\Cle $cle
     *
     * @return CleSlave
     */
    public function setCle(\AppBundle\Entity\Cle $cle = null)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return \AppBundle\Entity\Cle
     */
    public function getCle()
    {
        return $this->cle;
    }
}
