<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinxoDossier
 *
 * @ORM\Table(name="linxo_dossier", uniqueConstraints={@ORM\UniqueConstraint(name="id_linxo_UNIQUE", columns={"id_linxo"})}, indexes={@ORM\Index(name="fk_linxo_compte_banque_compte_idx", columns={"banque_compte_id"}), @ORM\Index(name="fk_linxo_dossier_linxo_idx", columns={"linxo_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinxoDossierRepository")
 */
class LinxoDossier
{
    /**
     * @var string
     *
     * @ORM\Column(name="id_linxo", type="string", length=45, nullable=false)
     */
    private $idLinxo;

    /**
     * @var string
     *
     * @ORM\Column(name="id_connection", type="string", length=45, nullable=true)
     */
    private $idConnection;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=45, nullable=true)
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_linxo", type="string", length=45, nullable=true)
     */
    private $compteLinxo;

    /**
     * @var string
     *
     * @ORM\Column(name="classification", type="string", length=45, nullable=true)
     */
    private $classification;

    /**
     * @var integer
     *
     * @ORM\Column(name="recuperation", type="integer", nullable=true)
     */
    private $recuperation = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Linxo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Linxo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="linxo_id", referencedColumnName="id")
     * })
     */
    private $linxo;

    /**
     * @var \AppBundle\Entity\BanqueCompte
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BanqueCompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_compte_id", referencedColumnName="id")
     * })
     */
    private $banqueCompte;



    /**
     * Set idLinxo
     *
     * @param string $idLinxo
     *
     * @return LinxoDossier
     */
    public function setIdLinxo($idLinxo)
    {
        $this->idLinxo = $idLinxo;

        return $this;
    }

    /**
     * Get idLinxo
     *
     * @return string
     */
    public function getIdLinxo()
    {
        return $this->idLinxo;
    }

    /**
     * Set idConnection
     *
     * @param string $idConnection
     *
     * @return LinxoDossier
     */
    public function setIdConnection($idConnection)
    {
        $this->idConnection = $idConnection;

        return $this;
    }

    /**
     * Get idConnection
     *
     * @return string
     */
    public function getIdConnection()
    {
        return $this->idConnection;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return LinxoDossier
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return LinxoDossier
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return LinxoDossier
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set compteLinxo
     *
     * @param string $compteLinxo
     *
     * @return LinxoDossier
     */
    public function setCompteLinxo($compteLinxo)
    {
        $this->compteLinxo = $compteLinxo;

        return $this;
    }

    /**
     * Get compteLinxo
     *
     * @return string
     */
    public function getCompteLinxo()
    {
        return $this->compteLinxo;
    }

    /**
     * Set classification
     *
     * @param string $classification
     *
     * @return LinxoDossier
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;

        return $this;
    }

    /**
     * Get classification
     *
     * @return string
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set recuperation
     *
     * @param integer $recuperation
     *
     * @return LinxoDossier
     */
    public function setRecuperation($recuperation)
    {
        $this->recuperation = $recuperation;

        return $this;
    }

    /**
     * Get recuperation
     *
     * @return integer
     */
    public function getRecuperation()
    {
        return $this->recuperation;
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
     * Set linxo
     *
     * @param \AppBundle\Entity\Linxo $linxo
     *
     * @return LinxoDossier
     */
    public function setLinxo(\AppBundle\Entity\Linxo $linxo = null)
    {
        $this->linxo = $linxo;

        return $this;
    }

    /**
     * Get linxo
     *
     * @return \AppBundle\Entity\Linxo
     */
    public function getLinxo()
    {
        return $this->linxo;
    }

    /**
     * Set banqueCompte
     *
     * @param \AppBundle\Entity\BanqueCompte $banqueCompte
     *
     * @return LinxoDossier
     */
    public function setBanqueCompte(\AppBundle\Entity\BanqueCompte $banqueCompte = null)
    {
        $this->banqueCompte = $banqueCompte;

        return $this;
    }

    /**
     * Get banqueCompte
     *
     * @return \AppBundle\Entity\BanqueCompte
     */
    public function getBanqueCompte()
    {
        return $this->banqueCompte;
    }
}
