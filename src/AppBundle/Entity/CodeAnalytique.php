<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CodeAnalytique
 *
 * @ORM\Table(name="code_analytique", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_code_analytique_dossier", columns={"code", "dossier_id", "code_analytique_section_id"})}, indexes={@ORM\Index(name="fk_code_analytique_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_code_analytique_cas_id_idx", columns={"code_analytique_section_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CodeAnalytiqueRepository")
 */
class CodeAnalytique
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
     */
    private $libelle;

    /**
     * @var integer
     *
     * @ORM\Column(name="supprimer", type="integer", nullable=false)
     */
    private $supprimer = 0;

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
     *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     * })
     */
    private $dossier;

    /**
     * @var \AppBundle\Entity\CodeAnalytiqueSection
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CodeAnalytiqueSection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="code_analytique_section_id", referencedColumnName="id")
     * })
     */
    private $codeAnalytiqueSection;



    /**
     * Set code
     *
     * @param string $code
     *
     * @return CodeAnalytique
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
     * @return CodeAnalytique
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
     * Set supprimer
     *
     * @param integer $supprimer
     *
     * @return CodeAnalytique
     */
    public function setSupprimer($supprimer)
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    /**
     * Get supprimer
     *
     * @return integer
     */
    public function getSupprimer()
    {
        return $this->supprimer;
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
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return CodeAnalytique
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
     * Set codeAnalytiqueSection
     *
     * @param \AppBundle\Entity\CodeAnalytiqueSection $codeAnalytiqueSection
     *
     * @return CodeAnalytique
     */
    public function setCodeAnalytiqueSection(\AppBundle\Entity\CodeAnalytiqueSection $codeAnalytiqueSection = null)
    {
        $this->codeAnalytiqueSection = $codeAnalytiqueSection;

        return $this;
    }

    /**
     * Get codeAnalytiqueSection
     *
     * @return \AppBundle\Entity\CodeAnalytiqueSection
     */
    public function getCodeAnalytiqueSection()
    {
        return $this->codeAnalytiqueSection;
    }
}
