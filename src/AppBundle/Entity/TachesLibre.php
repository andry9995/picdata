<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TachesLibre
 *
 * @ORM\Table(name="taches_libre", indexes={@ORM\Index(name="fk_taches_libre_tache_idx", columns={"tache_id"}), @ORM\Index(name="fk_taches_libre_client_idx", columns={"client_id"}), @ORM\Index(name="fk_taches_libre_dossier_idx", columns={"dossier_id"}), @ORM\Index(name="fk_taches_libre_taches_libre_idx", columns={"taches_libre_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TachesLibreRepository")
 */
class TachesLibre
{
    /**
     * @var integer
     *
     * @ORM\Column(name="responsable", type="integer", nullable=false)
     */
    private $responsable = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\TachesLibre
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TachesLibre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="taches_libre_id", referencedColumnName="id")
     * })
     */
    private $tachesLibre;

    /**
     * @var \AppBundle\Entity\Tache
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tache")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tache_id", referencedColumnName="id")
     * })
     */
    private $tache;

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
     * Set responsable
     *
     * @param integer $responsable
     *
     * @return TachesLibre
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return integer
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return TachesLibre
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
     * Set tachesLibre
     *
     * @param \AppBundle\Entity\TachesLibre $tachesLibre
     *
     * @return TachesLibre
     */
    public function setTachesLibre(\AppBundle\Entity\TachesLibre $tachesLibre = null)
    {
        $this->tachesLibre = $tachesLibre;

        return $this;
    }

    /**
     * Get tachesLibre
     *
     * @return \AppBundle\Entity\TachesLibre
     */
    public function getTachesLibre()
    {
        return $this->tachesLibre;
    }

    /**
     * Set tache
     *
     * @param \AppBundle\Entity\Tache $tache
     *
     * @return TachesLibre
     */
    public function setTache(\AppBundle\Entity\Tache $tache = null)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return \AppBundle\Entity\Tache
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return TachesLibre
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
     * @return TachesLibre
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
