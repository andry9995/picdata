<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstructionSaisie
 *
 * @ORM\Table(name="instruction_saisie", uniqueConstraints={@ORM\UniqueConstraint(name="dossier_id_UNIQUE", columns={"dossier_id"})})
 * @ORM\Entity
 */
class InstructionSaisie
{

    /**
     * @var string
     *
     * @ORM\Column(name="piece_jointe", type="text")
     */
    private $pieceJointe;


    /**
     * @var string
     *
     * @ORM\Column(name="instruction", type="text", length=65535, nullable=true)
     */
    private $instruction;

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
     * Set instruction
     *
     * @param string $instruction
     *
     * @return InstructionSaisie
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;

        return $this;
    }

    /**
     * Get instruction
     *
     * @return string
     */
    public function getInstruction()
    {
        return $this->instruction;
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
     * @return InstructionSaisie
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
     * @return mixed
     */
    public function getPieceJointe(){
        return $this->pieceJointe;
    }

    /**
     * @param $pieceJointe
     * @return $this
     */
    public function setPieceJointe($pieceJointe){
        $this->pieceJointe = $pieceJointe;
        return $this;
    }
}
