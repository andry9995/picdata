<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveJson
 *
 * @ORM\Table(name="releve_json", indexes={@ORM\Index(name="fk_releve_json_releve_idx", columns={"releve_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveJsonRepository")
 */
class ReleveJson
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_derniere_modif", type="date", nullable=false)
     */
    private $dateDerniereModif;

    /**
     * @var integer
     *
     * @ORM\Column(name="a_modifier", type="integer", nullable=false)
     */
    private $aModifier;

    /**
     * @var string
     *
     * @ORM\Column(name="json", type="text", length=65535, nullable=false)
     */
    private $json;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Releve
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Releve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="releve_id", referencedColumnName="id")
     * })
     */
    private $releve;



    /**
     * Set dateDerniereModif
     *
     * @param \DateTime $dateDerniereModif
     *
     * @return ReleveJson
     */
    public function setDateDerniereModif($dateDerniereModif)
    {
        $this->dateDerniereModif = $dateDerniereModif;

        return $this;
    }

    /**
     * Get dateDerniereModif
     *
     * @return \DateTime
     */
    public function getDateDerniereModif()
    {
        return $this->dateDerniereModif;
    }

    /**
     * Set aModifier
     *
     * @param integer $aModifier
     *
     * @return ReleveJson
     */
    public function setAModifier($aModifier)
    {
        $this->aModifier = $aModifier;

        return $this;
    }

    /**
     * Get aModifier
     *
     * @return integer
     */
    public function getAModifier()
    {
        return $this->aModifier;
    }

    /**
     * Set json
     *
     * @param string $json
     *
     * @return ReleveJson
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string
     */
    public function getJson()
    {
        return $this->json;
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
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return ReleveJson
     */
    public function setReleve(\AppBundle\Entity\Releve $releve = null)
    {
        $this->releve = $releve;

        return $this;
    }

    /**
     * Get releve
     *
     * @return \AppBundle\Entity\Releve
     */
    public function getReleve()
    {
        return $this->releve;
    }
}
