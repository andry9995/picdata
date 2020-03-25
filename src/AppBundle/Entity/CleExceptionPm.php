<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CleExceptionPm
 *
 * @ORM\Table(name="cle_exception_pm", indexes={@ORM\Index(name="fk_cle_exception_pm_cle_dossier_idx", columns={"cle_dossier_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CleExceptionPmRepository")
 */
class CleExceptionPm
{
    /**
     * @var integer
     *
     * @ORM\Column(name="sens", type="integer", nullable=false)
     */
    private $sens = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="formule", type="string", length=45, nullable=true)
     */
    private $formule;

    /**
     * @var integer
     *
     * @ORM\Column(name="sens_2", type="integer", nullable=false)
     */
    private $sens2 = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="formule_2", type="string", length=45, nullable=true)
     */
    private $formule2;

    /**
     * @var string
     *
     * @ORM\Column(name="mot_cle", type="string", length=10, nullable=false)
     */
    private $motCle = 'TST';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\CleDossier
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CleDossier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cle_dossier_id", referencedColumnName="id")
     * })
     */
    private $cleDossier;



    /**
     * Set sens
     *
     * @param integer $sens
     *
     * @return CleExceptionPm
     */
    public function setSens($sens)
    {
        $this->sens = $sens;

        return $this;
    }

    /**
     * Get sens
     *
     * @return integer
     */
    public function getSens()
    {
        return $this->sens;
    }

    /**
     * Set formule
     *
     * @param string $formule
     *
     * @return CleExceptionPm
     */
    public function setFormule($formule)
    {
        $this->formule = $formule;

        return $this;
    }

    /**
     * Get formule
     *
     * @return string
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * Set sens2
     *
     * @param integer $sens2
     *
     * @return CleExceptionPm
     */
    public function setSens2($sens2)
    {
        $this->sens2 = $sens2;

        return $this;
    }

    /**
     * Get sens2
     *
     * @return integer
     */
    public function getSens2()
    {
        return $this->sens2;
    }

    /**
     * Set formule2
     *
     * @param string $formule2
     *
     * @return CleExceptionPm
     */
    public function setFormule2($formule2)
    {
        $this->formule2 = $formule2;

        return $this;
    }

    /**
     * Get formule2
     *
     * @return string
     */
    public function getFormule2()
    {
        return $this->formule2;
    }

    /**
     * Set motCle
     *
     * @param string $motCle
     *
     * @return CleExceptionPm
     */
    public function setMotCle($motCle)
    {
        $this->motCle = $motCle;

        return $this;
    }

    /**
     * Get motCle
     *
     * @return string
     */
    public function getMotCle()
    {
        return $this->motCle;
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
     * Set cleDossier
     *
     * @param \AppBundle\Entity\CleDossier $cleDossier
     *
     * @return CleExceptionPm
     */
    public function setCleDossier(\AppBundle\Entity\CleDossier $cleDossier = null)
    {
        $this->cleDossier = $cleDossier;

        return $this;
    }

    /**
     * Get cleDossier
     *
     * @return \AppBundle\Entity\CleDossier
     */
    public function getCleDossier()
    {
        return $this->cleDossier;
    }
}
