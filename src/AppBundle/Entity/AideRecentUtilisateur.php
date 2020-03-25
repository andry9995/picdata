<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AideRecentUtilisateur
 *
 * @ORM\Table(name="aide_recent_utilisateur", indexes={@ORM\Index(name="fk_aide_recent_utilisateur1_idx", columns={"utilisateur_id"}), @ORM\Index(name="fk_aide_recent_aide_31_idx", columns={"aide_3_id"})})
 * @ORM\Entity
 */
class AideRecentUtilisateur
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_consulatation", type="date", nullable=false)
     */
    private $dateConsulatation;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;

    /**
     * @var \AppBundle\Entity\Aide3
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aide3")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aide_3_id", referencedColumnName="id")
     * })
     */
    private $aide3;



    /**
     * Set dateConsulatation
     *
     * @param \DateTime $dateConsulatation
     *
     * @return AideRecentUtilisateur
     */
    public function setDateConsulatation($dateConsulatation)
    {
        $this->dateConsulatation = $dateConsulatation;

        return $this;
    }

    /**
     * Get dateConsulatation
     *
     * @return \DateTime
     */
    public function getDateConsulatation()
    {
        return $this->dateConsulatation;
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
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return AideRecentUtilisateur
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set aide3
     *
     * @param \AppBundle\Entity\Aide3 $aide3
     *
     * @return AideRecentUtilisateur
     */
    public function setAide3(\AppBundle\Entity\Aide3 $aide3 = null)
    {
        $this->aide3 = $aide3;

        return $this;
    }

    /**
     * Get aide3
     *
     * @return \AppBundle\Entity\Aide3
     */
    public function getAide3()
    {
        return $this->aide3;
    }
}
