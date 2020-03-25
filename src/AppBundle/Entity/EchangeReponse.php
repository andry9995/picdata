<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EchangeReponse
 *
 * @ORM\Table(name="echange_reponse", indexes={@ORM\Index(name="fk_echange_reponse_echange_item_idx", columns={"echange_item_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EchangeReponseRepository")
 */
class EchangeReponse
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom_fichier", type="string", length=100, nullable=false)
     */
    private $nomFichier;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="date", nullable=false)
     */
    private $dateEnvoi;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var boolean
     *
     * @ORM\Column(name="supprimer", type="boolean", nullable=false)
     */
    private $supprimer = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Set nomFichier
     *
     * @param string $nomFichier
     *
     * @return EchangeReponse
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
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     *
     * @return EchangeReponse
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
     * Set numero
     *
     * @param integer $numero
     *
     * @return EchangeReponse
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
     * Set supprimer
     *
     * @param boolean $supprimer
     *
     * @return EchangeReponse
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
     * Set message
     *
     * @param string $message
     *
     * @return EchangeReponse
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set echangeItem
     *
     * @param \AppBundle\Entity\EchangeItem $echangeItem
     *
     * @return EchangeReponse
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