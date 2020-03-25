<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactPrestation
 *
 * @ORM\Table(name="fact_prestation", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE_code_libelle", columns={"code", "libelle"})}, indexes={@ORM\Index(name="fk_fact_prestation_fact_domaine1_idx", columns={"fact_domaine_id"}), @ORM\Index(name="fk_fact_prestaton_fact_unite1_idx", columns={"fact_unite_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactPrestationRepository")
 */
class FactPrestation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="indice", type="boolean", nullable=false)
     */
    private $indice = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="remise", type="boolean", nullable=false)
     */
    private $remise = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\FactDomaine
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactDomaine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_domaine_id", referencedColumnName="id")
     * })
     */
    private $factDomaine;

    /**
     * @var \AppBundle\Entity\FactUnite
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FactUnite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fact_unite_id", referencedColumnName="id")
     * })
     */
    private $factUnite;



    /**
     * Set code
     *
     * @param integer $code
     *
     * @return FactPrestation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
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
     * @return FactPrestation
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
     * Set indice
     *
     * @param boolean $indice
     *
     * @return FactPrestation
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return boolean
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set remise
     *
     * @param boolean $remise
     *
     * @return FactPrestation
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return boolean
     */
    public function getRemise()
    {
        return $this->remise;
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
     * Set factDomaine
     *
     * @param \AppBundle\Entity\FactDomaine $factDomaine
     *
     * @return FactPrestation
     */
    public function setFactDomaine(\AppBundle\Entity\FactDomaine $factDomaine = null)
    {
        $this->factDomaine = $factDomaine;

        return $this;
    }

    /**
     * Get factDomaine
     *
     * @return \AppBundle\Entity\FactDomaine
     */
    public function getFactDomaine()
    {
        return $this->factDomaine;
    }

    /**
     * Set factUnite
     *
     * @param \AppBundle\Entity\FactUnite $factUnite
     *
     * @return FactPrestation
     */
    public function setFactUnite(\AppBundle\Entity\FactUnite $factUnite = null)
    {
        $this->factUnite = $factUnite;

        return $this;
    }

    /**
     * Get factUnite
     *
     * @return \AppBundle\Entity\FactUnite
     */
    public function getFactUnite()
    {
        return $this->factUnite;
    }
}
