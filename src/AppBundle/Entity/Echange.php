<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Echange
 *
 * @ORM\Table(name="echange", uniqueConstraints={@ORM\UniqueConstraint(name="uk_echange_dossier_exercice", columns={"exercice", "dossier_id"})}, indexes={@ORM\Index(name="fk_echange_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_echange_echange_type_idx", columns={"echange_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EchangeRepository")
 */
class Echange
{
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="date", nullable=false)
     */
    private $dateEnvoi;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\EchangeType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EchangeType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_type_id", referencedColumnName="id")
     * })
     */
    private $echangeType;

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
     * Set exercice
     *
     * @param integer $exercice
     *
     * @return Echange
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer
     */
    public function getExercice()
    {
        return $this->exercice;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     *
     * @return Echange
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
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
     * Set echangeType
     *
     * @param \AppBundle\Entity\EchangeType $echangeType
     *
     * @return Echange
     */
    public function setEchangeType(\AppBundle\Entity\EchangeType $echangeType = null)
    {
        $this->echangeType = $echangeType;

        return $this;
    }

    /**
     * Get echangeType
     *
     * @return \AppBundle\Entity\EchangeType
     */
    public function getEchangeType()
    {
        return $this->echangeType;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Echange
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
