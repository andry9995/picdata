<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstructionTexte
 *
 * @ORM\Table(name="instruction_texte", indexes={@ORM\Index(name="fk_instruction_texte_instruction_type1_idx", columns={"type_instruction_id"}), @ORM\Index(name="fk_instruction_texte_client1_idx", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InstructionTexteRepository")
 */
class InstructionTexte
{
    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="valide", type="integer", nullable=true)
     */
    private $valide;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\InstructionType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\InstructionType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_instruction_id", referencedColumnName="id")
     * })
     */
    private $typeInstruction;

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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return InstructionTexte
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set valide
     *
     * @param integer $valide
     *
     * @return InstructionTexte
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return integer
     */
    public function getValide()
    {
        return $this->valide;
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
     * Set typeInstruction
     *
     * @param \AppBundle\Entity\InstructionType $typeInstruction
     *
     * @return InstructionTexte
     */
    public function setTypeInstruction(\AppBundle\Entity\InstructionType $typeInstruction = null)
    {
        $this->typeInstruction = $typeInstruction;

        return $this;
    }

    /**
     * Get typeInstruction
     *
     * @return \AppBundle\Entity\InstructionType
     */
    public function getTypeInstruction()
    {
        return $this->typeInstruction;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return InstructionTexte
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
