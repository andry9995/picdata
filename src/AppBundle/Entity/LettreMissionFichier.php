<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LettreMissionFichier
 *
 * @ORM\Table(name="lettre_mission_fichier", indexes={@ORM\Index(name="fk_lettre_mission_fichier_ldm1_idx", columns={"lettre_mission_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LettreMissionFichierRepository")
 */
class LettreMissionFichier
{
    /**
     * @var string
     *
     * @ORM\Column(name="fichier", type="text", length=65535, nullable=false)
     */
    private $fichier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\LettreMission
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LettreMission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lettre_mission_id", referencedColumnName="id")
     * })
     */
    private $lettreMission;



    /**
     * Set fichier
     *
     * @param string $fichier
     *
     * @return LettreMissionFichier
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return string
     */
    public function getFichier()
    {
        return $this->fichier;
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
     * Set lettreMission
     *
     * @param \AppBundle\Entity\LettreMission $lettreMission
     *
     * @return LettreMissionFichier
     */
    public function setLettreMission(\AppBundle\Entity\LettreMission $lettreMission = null)
    {
        $this->lettreMission = $lettreMission;

        return $this;
    }

    /**
     * Get lettreMission
     *
     * @return \AppBundle\Entity\LettreMission
     */
    public function getLettreMission()
    {
        return $this->lettreMission;
    }
}
