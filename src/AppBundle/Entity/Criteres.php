<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Criteres
 *
 * @ORM\Table(name="criteres", indexes={@ORM\Index(name="fk_criteres_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_criteres_banque1_idx", columns={"banque_id"})})
 * @ORM\Entity
 */
class Criteres
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
     * @var \AppBundle\Entity\Banque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Banque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_id", referencedColumnName="id")
     * })
     */
    private $banque;



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
     * @return Criteres
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
     * Set banque
     *
     * @param \AppBundle\Entity\Banque $banque
     *
     * @return Criteres
     */
    public function setBanque(\AppBundle\Entity\Banque $banque = null)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return \AppBundle\Entity\Banque
     */
    public function getBanque()
    {
        return $this->banque;
    }
}
