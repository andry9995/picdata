<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EchangeItem
 *
 * @ORM\Table(name="echange_item", uniqueConstraints={@ORM\UniqueConstraint(name="uk_echange_item_echange_numero", columns={"numero", "echange_id"})}, indexes={@ORM\Index(name="fk_echange_item_echange_idx", columns={"echange_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EchangeItemRepository")
 */
class EchangeItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="nom_fichier", type="string", length=100, nullable=false)
     */
    private $nomFichier;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="supprimer", type="boolean", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="telecharger", type="boolean", nullable=true)
     */
    private $telecharger = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Echange
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Echange")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_id", referencedColumnName="id")
     * })
     */
    private $echange;

    /**
     * @var \AppBundle\Entity\EchangeItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EchangeItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="echange_item_id", referencedColumnName="id")
     * })
     */
    private $echangeItem;



    /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return EchangeItem
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set nomFichier
     *
     * @param string $nomFichier
     *
     * @return EchangeItem
     */
    public function setNomFichier($nomFichier)
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    /**
     * Get nomFichier
     *
     * @return string
     */
    public function getNomFichier()
    {
        return $this->nomFichier;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return EchangeItem
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
     * Set supprimer
     *
     * @param boolean $supprimer
     *
     * @return EchangeItem
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return boolean
     */
    public function getSupprimer()
    {
        return $this->supprimer;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return EchangeItem
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return EchangeItem
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set telecharger
     *
     * @param boolean $telecharger
     *
     * @return EchangeItem
     */
    public function setTelecharger($telecharger)
    {
        $this->telecharger = $telecharger;

        return $this;
    }

    /**
     * Get telecharger
     *
     * @return boolean
     */
    public function getTelecharger()
    {
        return $this->telecharger;
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
     * Set echange
     *
     * @param \AppBundle\Entity\Echange $echange
     *
     * @return EchangeItem
     */
    public function setEchange(\AppBundle\Entity\Echange $echange = null)
    {
        $this->echange = $echange;

        return $this;
    }

    /**
     * Get echange
     *
     * @return \AppBundle\Entity\Echange
     */
    public function getEchange()
    {
        return $this->echange;
    }

    /**
     * Set echangeItem
     *
     * @param \AppBundle\Entity\EchangeItem $echangeItem
     *
     * @return EchangeItem
     */
    public function setEchangeItem(\AppBundle\Entity\EchangeItem $echangeItem = null)
    {
        $this->echangeItem = $echangeItem;

        return $this;
    }

    /**
     * Get echangeItem
     *
     * @return \AppBundle\Entity\EchangeItem
     */
    public function getEchangeItem()
    {
        return $this->echangeItem;
    }
}


