<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NdfContact
 *
 * @ORM\Table(name="ndf_contact", indexes={@ORM\Index(name="fk_ndf_contact_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_ndf_contact_ndf_contact_type1_idx", columns={"ndf_contact_type_id"})})
 * @ORM\Entity
 */
class NdfContact
{
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
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=45, nullable=true)
     */
    private $fonction;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\NdfContactType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NdfContactType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndf_contact_type_id", referencedColumnName="id")
     * })
     */
    private $ndfContactType;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return NdfContact
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
     * @return NdfContact
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
     * @return NdfContact
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
     * Set telephone
     *
     * @param string $telephone
     *
     * @return NdfContact
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set fonction
     *
     * @param string $fonction
     *
     * @return NdfContact
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return string
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return NdfContact
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
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
     * Set ndfContactType
     *
     * @param \AppBundle\Entity\NdfContactType $ndfContactType
     *
     * @return NdfContact
     */
    public function setNdfContactType(\AppBundle\Entity\NdfContactType $ndfContactType = null)
    {
        $this->ndfContactType = $ndfContactType;

        return $this;
    }

    /**
     * Get ndfContactType
     *
     * @return \AppBundle\Entity\NdfContactType
     */
    public function getNdfContactType()
    {
        return $this->ndfContactType;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return NdfContact
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
