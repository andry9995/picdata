<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LettreMission
 *
 * @ORM\Table(name="lettre_mission", indexes={@ORM\Index(name="fk_lettre_mission_dossier1_idx", columns={"dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LettreMissionRepository")
 */
class LettreMission
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_lettre", type="datetime", nullable=false)
     */
    private $dateLettre;

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
     * Set dateLettre
     *
     * @param \DateTime $dateLettre
     *
     * @return LettreMission
     */
    public function setDateLettre($dateLettre)
    {
        $this->dateLettre = $dateLettre;

        return $this;
    }

    /**
     * Get dateLettre
     *
     * @return \DateTime
     */
    public function getDateLettre()
    {
        return $this->dateLettre;
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return LettreMission
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
}
