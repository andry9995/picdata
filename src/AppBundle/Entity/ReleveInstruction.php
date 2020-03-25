<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleveInstruction
 *
 * @ORM\Table(name="releve_instruction", indexes={@ORM\Index(name="fk_releve_instruction_releve_idx", columns={"releve_id"}), @ORM\Index(name="fk_releve_instruction_banque_type1_idx", columns={"banque_type_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReleveInstructionRepository")
 */
class ReleveInstruction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="instruction", type="integer", nullable=false)
     */
    private $instruction = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="observation", type="string", length=100, nullable=false)
     */
    private $observation = '';

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
     * @var \AppBundle\Entity\BanqueType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_type_id", referencedColumnName="id")
     * })
     */
    private $banqueType;



    /**
     * Set instruction
     *
     * @param integer $instruction
     *
     * @return ReleveInstruction
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;

        return $this;
    }

    /**
     * Get instruction
     *
     * @return integer
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * Set observation
     *
     * @param string $observation
     *
     * @return ReleveInstruction
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * Get observation
     *
     * @return string
     */
    public function getObservation()
    {
        return $this->observation;
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
     * @return ReleveInstruction
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

    /**
     * Set banqueType
     *
     * @param \AppBundle\Entity\BanqueType $banqueType
     *
     * @return ReleveInstruction
     */
    public function setBanqueType(\AppBundle\Entity\BanqueType $banqueType = null)
    {
        $this->banqueType = $banqueType;

        return $this;
    }

    /**
     * Get banqueType
     *
     * @return \AppBundle\Entity\BanqueType
     */
    public function getBanqueType()
    {
        return $this->banqueType;
    }
}
