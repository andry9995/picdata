<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banque
 *
 * @ORM\Table(name="banque", indexes={@ORM\Index(name="fk_banque_banque_idx", columns={"original"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanqueRepository")
 */
class Banque
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=150, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="codebanque", type="string", length=10, nullable=false)
     */
    private $codebanque;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="frais_bancaire", type="integer", nullable=true)
     */
    private $fraisBancaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="carte_releve", type="integer", nullable=true)
     */
    private $carteReleve;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Banque
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Banque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="original", referencedColumnName="id")
     * })
     */
    private $original;



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Banque
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set codebanque
     *
     * @param string $codebanque
     *
     * @return Banque
     */
    public function setCodebanque($codebanque)
    {
        $this->codebanque = $codebanque;

        return $this;
    }

    /**
     * Get codebanque
     *
     * @return string
     */
    public function getCodebanque()
    {
        return $this->codebanque;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Banque
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set fraisBancaire
     *
     * @param integer $fraisBancaire
     *
     * @return Banque
     */
    public function setFraisBancaire($fraisBancaire)
    {
        $this->fraisBancaire = $fraisBancaire;

        return $this;
    }

    /**
     * Get fraisBancaire
     *
     * @return integer
     */
    public function getFraisBancaire()
    {
        return $this->fraisBancaire;
    }

    /**
     * Set carteReleve
     *
     * @param integer $carteReleve
     *
     * @return Banque
     */
    public function setCarteReleve($carteReleve)
    {
        $this->carteReleve = $carteReleve;

        return $this;
    }

    /**
     * Get carteReleve
     *
     * @return integer
     */
    public function getCarteReleve()
    {
        return $this->carteReleve;
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
     * Set original
     *
     * @param \AppBundle\Entity\Banque $original
     *
     * @return Banque
     */
    public function setOriginal(\AppBundle\Entity\Banque $original = null)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return \AppBundle\Entity\Banque
     */
    public function getOriginal()
    {
        return $this->original;
    }
}
