<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesSynchroMoov
 *
 * @ORM\Table(name="taches_synchro_moov", indexes={@ORM\Index(name="fk_taches_synchro_moov_operateur_idx", columns={"operateur_id"}), @ORM\Index(name="fk_taches_synchro_moov_taches_synchro_idx", columns={"taches_synchro_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesSynchroMoovRepository")
 */
class TachesSynchroMoov
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesSynchro
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesSynchro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_synchro_id", referencedColumnName="id")
     * })
     */
    private $tachesSynchro;

    /**
     * @var \AppBundle\Entity\Operateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Operateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     * })
     */
    private $operateur;



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TachesSynchroMoov
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * Set tachesSynchro
     *
     * @param \AppBundle\Entity\TachesSynchro $tachesSynchro
     *
     * @return TachesSynchroMoov
     */
    public function setTachesSynchro(\AppBundle\Entity\TachesSynchro $tachesSynchro = null)
    {
        $this->tachesSynchro = $tachesSynchro;

        return $this;
    }

    /**
     * Get tachesSynchro
     *
     * @return \AppBundle\Entity\TachesSynchro
     */
    public function getTachesSynchro()
    {
        return $this->tachesSynchro;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return TachesSynchroMoov
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }
}
