<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtapeTraitement
 *
 * @ORM\Table(name="etape_traitement", uniqueConstraints={@ORM\UniqueConstraint(name="code_UNIQUE", columns={"code"})}, indexes={@ORM\Index(name="fk_etape_traitement_application_version1_idx", columns={"application_version_id"})})
 * @ORM\Entity
 */
class EtapeTraitement
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=15, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=false)
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
     * @var \AppBundle\Entity\ApplicationVersion
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApplicationVersion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="application_version_id", referencedColumnName="id")
     * })
     */
    private $applicationVersion;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return EtapeTraitement
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
     * @return EtapeTraitement
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
     * Set applicationVersion
     *
     * @param \AppBundle\Entity\ApplicationVersion $applicationVersion
     *
     * @return EtapeTraitement
     */
    public function setApplicationVersion(\AppBundle\Entity\ApplicationVersion $applicationVersion = null)
    {
        $this->applicationVersion = $applicationVersion;

        return $this;
    }

    /**
     * Get applicationVersion
     *
     * @return \AppBundle\Entity\ApplicationVersion
     */
    public function getApplicationVersion()
    {
        return $this->applicationVersion;
    }
}
