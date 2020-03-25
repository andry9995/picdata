<?php

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use Doctrine\ORM\Mapping as ORM;

/**
 * OneStatusOpp
 *
 * @ORM\Table(name="one_status_opp")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OneStatusOppRepository")
 */
class OneStatusOpp
{

    /**
     * @var \DateTime
     * @ORM\Column(name="cree_le", type="datetime", nullable=true)
     */
    private $creeLe;


    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer",  nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=false)
     */
    private $nom;

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
     *     @ORM\JoinColumn(name="dossier_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $dossier;


    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return OneStatusOpp
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return \DateTime
     */
    public function getCreeLe()
    {
        return $this->creeLe;
    }

    /**
     * @param $creeLe
     */
    public function setCreeLe($creeLe)
    {
        $this->creeLe = $creeLe;
    }

    /**
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * @param int $ordre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    /**
     * @return Dossier
     */
    public function getDossier(){
        return $this->dossier;
    }

    /**
     * @param $dossier
     * @return $this
     */
    public function setDossier($dossier){
        $this->dossier = $dossier;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status = 0){
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(){
        return $this->status;
    }
}
