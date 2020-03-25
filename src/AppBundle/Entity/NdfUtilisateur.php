<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfUtilisateur
 *
 * @ORM\Table(name="ndf_utilisateur", indexes={@ORM\Index(name="fk_ndf_utilisateur_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_utilisateur_manager_idx", columns={"mananger"})})
 * @ORM\Entity
 */
class NdfUtilisateur
{
    /**
     * @var integer
     *
     * @ORM\Column(name="is_manager", type="integer", nullable=false)
     */
    private $isManager = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="matricule", type="string", length=45, nullable=true)
     */
    private $matricule;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=45, nullable=true)
     */
    private $mail;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfUtilisateur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfUtilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mananger", referencedColumnName="id")
     * })
     */
    private $mananger;

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
     * Set isManager
     *
     * @param integer $isManager
     *
     * @return NdfUtilisateur
     */
    public function setIsManager($isManager)
    {
        $this->isManager = $isManager;

        return $this;
    }

    /**
     * Get isManager
     *
     * @return integer
     */
    public function getIsManager()
    {
        return $this->isManager;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     *
     * @return NdfUtilisateur
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return NdfUtilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return NdfUtilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return NdfUtilisateur
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return NdfUtilisateur
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set mananger
     *
     * @param \AppBundle\Entity\NdfUtilisateur $mananger
     *
     * @return NdfUtilisateur
     */
    public function setMananger(\AppBundle\Entity\NdfUtilisateur $mananger = null)
    {
        $this->mananger = $mananger;

        return $this;
    }

    /**
     * Get mananger
     *
     * @return \AppBundle\Entity\NdfUtilisateur
     */
    public function getMananger()
    {
        return $this->mananger;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfUtilisateur
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
