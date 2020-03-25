<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveComplementaire
 *
 * @ORM\Table(name="releve_complementaire", indexes={@ORM\Index(name="fk_releve_complementaire_releve_idx", columns={"releve_id"}), @ORM\Index(name="fl_releve_complementaire_cfonb_code_idx", columns={"cfonb_code_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveComplementaireRepository")
 */
class ReleveComplementaire
{
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=150, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\CfonbCode
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CfonbCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cfonb_code_id", referencedColumnName="id")
     * })
     */
    private $cfonbCode;

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
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ReleveComplementaire
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
     * Set cfonbCode
     *
     * @param \AppBundle\Entity\CfonbCode $cfonbCode
     *
     * @return ReleveComplementaire
     */
    public function setCfonbCode(\AppBundle\Entity\CfonbCode $cfonbCode = null)
    {
        $this->cfonbCode = $cfonbCode;

        return $this;
    }

    /**
     * Get cfonbCode
     *
     * @return \AppBundle\Entity\CfonbCode
     */
    public function getCfonbCode()
    {
        return $this->cfonbCode;
    }

    /**
     * Set releve
     *
     * @param \AppBundle\Entity\Releve $releve
     *
     * @return ReleveComplementaire
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
