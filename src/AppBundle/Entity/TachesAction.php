<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesAction
 *
 * @ORM\Table(name="taches_action", indexes={@ORM\Index(name="fk_taches_action_taches_item_idx", columns={"taches_item_id"}), @ORM\Index(name="fk_taches_action_tache_liste_action_idx", columns={"tache_liste_action_id"}), @ORM\Index(name="fk_taches_action_client_idx", columns={"client_id"}), @ORM\Index(name="fk_taches_action_dossier_idx", columns={"dossier_id"})})
 * @ORM\Entity
 */
class TachesAction
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=25, nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     */
    private $libelle = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TacheListeAction
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TacheListeAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_liste_action_id", referencedColumnName="id")
     * })
     */
    private $tacheListeAction;

    /**
     * @var \AppBundle\Entity\TachesItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_item_id", referencedColumnName="id")
     * })
     */
    private $tachesItem;

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
     * @var \AppBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return TachesAction
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return TachesAction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
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
     * Set tacheListeAction
     *
     * @param \AppBundle\Entity\TacheListeAction $tacheListeAction
     *
     * @return TachesAction
     */
    public function setTacheListeAction(\AppBundle\Entity\TacheListeAction $tacheListeAction = null)
    {
        $this->tacheListeAction = $tacheListeAction;

        return $this;
    }

    /**
     * Get tacheListeAction
     *
     * @return \AppBundle\Entity\TacheListeAction
     */
    public function getTacheListeAction()
    {
        return $this->tacheListeAction;
    }

    /**
     * Set tachesItem
     *
     * @param \AppBundle\Entity\TachesItem $tachesItem
     *
     * @return TachesAction
     */
    public function setTachesItem(\AppBundle\Entity\TachesItem $tachesItem = null)
    {
        $this->tachesItem = $tachesItem;

        return $this;
    }

    /**
     * Get tachesItem
     *
     * @return \AppBundle\Entity\TachesItem
     */
    public function getTachesItem()
    {
        return $this->tachesItem;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TachesAction
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return TachesAction
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
